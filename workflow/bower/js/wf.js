/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var margin ;
var zoom;
var width;
var height;
var drag;
var svg ;
var container;
var lastNodeId = "";

$(document).ready(function(){
 
});

function inisiateGraph(){
    width  =  parseInt($('.graph-box-main').css('width'),10) ;
    width+=30;
    var sHeight = screen.height;
    height = sHeight -parseInt($('.graph-box-main').css('top'),10) ;
    
    
    
    //var mt = (1716/width);
    //height=height/mt;
    
    margin = {top: 0, right: 0, bottom: 0, left: 0};
    width = width - margin.left - margin.right;
    height = height - margin.top - margin.bottom;

    zoom = d3.behavior.zoom()
        .scaleExtent([1, 10])
        .on("zoom", zoomed);

    drag = d3.behavior.drag()
        .origin(function(d) { return d; })
        .on("dragstart", dragstarted)
        .on("drag", dragged)
        .on("dragend", dragended);

    svg = d3.select("body svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        //.attr("width", '100%')
        //.attr("height", '100%')
            //.append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.right + ")")
        //.attr("transform", "scale(.8)translate(0,0)")
        .call(zoom);
    addBalloonPoints();
    svg.selectAll(".node")
        .on("mouseover", function(){
            lastNodeId = d3.select(this).attr('id');
            if(baloonState(d3.select(this).attr('id')))
                showStepBalloonBox(d3.select(this));
        })
        .on("mouseout", function(){
                //lastNodeId = "";
                hideBaloonBox();
        })
        .on("mousemove", function(){

        });
    svg.selectAll("text")
        .on("mouseover", function(){
            if(inRange(this)){
                if(baloonState(lastNodeId))
                    showStepBalloonBox(d3.select('#'+lastNodeId));
            }
        });
    container = svg.selectAll('#main-group').call(drag);
    
    
}



    function dottype(d) {
      d.x = +d.x;
      d.y = +d.y;
      return d;
    }

    function zoomed() {
      container.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
    }

    function dragstarted(d) {
      d3.event.sourceEvent.stopPropagation();
      d3.select(this).classed("dragging", true);
    }

    function dragged(d) {
        
        g.attr("cx", d.x = d3.event.x).attr("cy", d.y = d3.event.y);
    }

    function dragended(d) {
      d3.select(this).classed("dragging", false);
    }




    function activeNodes(strNodes){
        if(!activeNodes) return ;
        var nodes = strNodes.split(',');
        for(var i=0; i<nodes.length; i++)
        {
            if (i != (nodes.length-1) )
                svg.select('#node-'+nodes[i]).classed('active-node',true);
            else
                svg.select('#node-'+nodes[i]).classed('current-node',true);
        }
        
        
        d3.selectAll('.node').style('fill','#f8f8f8');
        d3.selectAll('.active-node').style('fill','#ccff00');
        startCurrentNodeColorChange()

    }
    function startCurrentNodeColorChange(){
        var fill = d3.select('.current-node').style('fill');
        if (fill == 'rgb(255, 162, 24)'){
            fill = 'rgb(255, 247, 232)';
        }else{
            fill = 'rgb(255, 162, 24)';
        }
        d3.select('.current-node').transition().duration(750).style('fill',fill);
        
        setTimeout(startCurrentNodeColorChange,800);
    }
    
//balloon box

function showStepBalloonBox(obj){
    var bcr=obj.node().getBoundingClientRect();   
    
    var px = parseInt(bcr.left+bcr.width,10) ;
    var py = parseInt(bcr.top +(bcr.height/2)+30,10);
    
    if($('balloon').attr('state') != 'true'){
        var htm = "<div class='balloon' state='true' style='position:fixed; z-index:10; min-height:100px; width: 100px;margin:-32px 0 0 4px;border:solid 0px purple' ></div>"        
        $('body').append(htm);
    }
    
    
    var id = obj.attr('id');
    var acId = id.split('-')[1]; 
    var ballonText = getBalloonData(acId);
    if(ballonText !="")
    {
            var tpc = d3.select('#bp-'+id);
            var bcr=d3.select('#bp-'+id).node().getBoundingClientRect();


            var px = (bcr.left)+(bcr.width/2);// -10 ;
            var py = (bcr.top)+(bcr.height/2);//  - 80;

            $('.balloon').css('left',px+"px");
            $('.balloon').css('top',py+"px");
            
    }
    
//------------------< generate balloon >--------------------
    
    var tip = "";
    tip += "<div class='balloon-box' style=''>";
    tip += "<div class='balloon-title'>Actions";//+"<a href='#' onclick='hideBalloonBox()' >x</a>";
    tip += "<div class='tooltip-triangle' ></div></div>";

    tip += "<div class='balloon-content'>";
    //title
    
    tip += "<p>";

    //text (description)
    tip += ballonText;

    tip += "</p>";
    tip += "</div>";

    tip += "</div>";
    tip += "<div class='clear'></div> ";

    $('.balloon').html(tip) ;

    
//----------------------------------------------------------
    
    
    
    $('.balloon').css('display','');
    
    
    
}

    
function addBalloonPoints()
{

        svg.selectAll('.node').each(function(){
                var bbox = d3.select(this).node().getBBox();
                var ballonText = "test"
                var bx = bbox.x+bbox.width;
                var by = bbox.y+(bbox.height/2);
                if (bbox.width<bbox.height) {

                }
                var id = d3.select(this).attr('id');

               

                d3.selectAll("#main-group").append('circle')
                    .attr("cx", bx)
                    .attr("cy", by)
                    .attr("r",1)
                    .attr('id','bp-'+id)
                    .attr('forId',id)
                    .attr('fill','red')
                    .attr("visibility", "hidden");

         

        });
}


function hideBaloonBox(){
    $('.balloon').css('display','none');
}


function getBalloonData(actionId){
    var c = balloonData.length;
    var rows = [];
    for (var i=0; i<c; i++ ){
        if(balloonData[i]["ActionIDRef"] == actionId)
        {
            rows[rows.length] = "<tr><td>"+
                                    balloonData[i]["UserName"]
                                +"</td><td>"+
                                    balloonData[i]["OBSCategory3Title"]
                                +"</td><td>"+
                                    balloonData[i]["ActionDateHijri"]
                                +"</td><td>"+
                                    ((balloonData[i]["ActionMessage"]!=null)?balloonData[i]["ActionMessage"]:'--')
                                +"</td><td>"+
                                    ((balloonData[i]["ActionAutoMessage"]!=null)?balloonData[i]["ActionAutoMessage"]:'--')
                                +"</td><td>"+
                                    ((balloonData[i]["LetterNumber"]!=null)?balloonData[i]["LetterNumber"]:'--')
                                +"</td></tr>";
        }
    }
    
    if (rows.length>0)
    {
        var html ="";
        html +="<div style='margin:-5px;' ><table class='table table-striped table-hover psa-grid table-bordered ' >";
            html +="<thead>";
                html +="<tr><th>User</th><th>OBS</th><th>Date</th><th>Message</th><th>Auto Message</th><th>Letter Number</th></tr>";
            html +="</thead>";
            html +="<tbody>";
                for(var i = 0; i<rows.length;i++)
                    html+= rows[i];
            html +="</tbody>";


        html +="</table></div>";
        return     html;
    }
    return "";
    
}





function baloonState(id){
    if((id+"").indexOf('-')== -1) return;
    
    var c = balloonData.length;
    var acId = id.split('-')[1]; 
    for (var i=0; i<c; i++ )
        if(balloonData[i]["ActionIDRef"] == acId)
            return true;
    return false;
        
}


function inRange(obj){
    
    var bbox = d3.select(obj).node().getBBox();
    return false;
//    var ballonText = "test"
//    var bx = bbox.x+bbox.width;
//    var by = bbox.y+(bbox.height/2);
//    if (bbox.width<bbox.height) {
//
//    }
}