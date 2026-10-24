
var margin;
var zoom;
var width;
var height;
var drag;
var svg;
var container;

$(document).ready(function () {

    var processTitle = $(this).attr('ProcessTitle');
    var workFlowCode = '';
    var updateto = '';
    var rev = '';
    switch (parseInt($(this).attr('processId'), 10)) {
        case 1:
            workFlowCode = 'wf-invoice-01';
            rev = '04';
            updateto = '1394/05/03';
            break;
        case 2:
            workFlowCode = 'wf-invoice-02';
            rev = '04';
            updateto = '1394/05/03';
            break;
        case 3:
            workFlowCode = 'wf-invoice-03';
            rev = '04';
            updateto = '1394/05/03';
            break;
        case 4:
            workFlowCode = 'wf-invoice-OP-04';
            rev = '04';
            updateto = '1394/05/03';
            break;
        case 5:
            workFlowCode = 'wf-invoice-SUR-05';
            rev = '07';
            updateto = '1394/05/03';
            break;
        case 6:
            workFlowCode = 'wf-invoice-SUR-06';
            rev = '05';
            updateto = '1394/05/03';
            break;
        case 7:
            workFlowCode = 'wf-invoice-07';
            rev = '04';
            updateto = '1394/05/03';
            break;
        case 8:
            workFlowCode = 'wf-invoice-08';
            rev = '05';
            updateto = '1394/05/03';
            break;
        case 9:
            workFlowCode = 'wf-invoice-09';
            rev = '05';
            updateto = '1394/04/04';
            break;
        case 10:
            workFlowCode = 'wf-invoice-10';
            rev = '04';
            updateto = '1394/05/03';
            break;
        case 11:
            workFlowCode = 'wf-invoice-11';
            rev = '04';
            updateto = '1394/05/03';
            break;
        case 12:
            workFlowCode = 'wf-invoice-12';
            rev = '04';
            updateto = '1394/05/03';
            break;
        case 13:
            workFlowCode = 'wf-invoice-13';
            rev = '07';
            updateto = '1394/07/08';
            break;
        case 14:
            workFlowCode = 'wf-cash-14';
            rev = '07';
            updateto = '1394/05/03';
            break;
        case 15:
            workFlowCode = 'wf-cash-15';
            rev = '06';
            updateto = '1394/05/03';
            break;
        case 16:
            workFlowCode = '';
            rev = '';
            updateto = '';
            break;
        case 17:
            workFlowCode = '';
            rev = '';
            updateto = '';
            break;
        case 18:
            workFlowCode = '';
            rev = '';
            updateto = '';
            break;
        case 19:
            workFlowCode = '';
            rev = '';
            updateto = '';
            break;
        case 20:
            workFlowCode = 'wf-cash-20';
            rev = '05';
            updateto = '1394/05/03';
            break;
        case 21:
            workFlowCode = 'wf-invoice-21';
            rev = '04';
            updateto = '1394/05/03';
            break;
        case 22:
            workFlowCode = 'wf-budget-22';
            rev = '04';
            updateto = '1394/05/03';
            break;
        case 23:
            workFlowCode = 'wf-contract-23';
            rev = '08';
            updateto = '1394/07/30';
            break;
        case 24:
            workFlowCode = 'wf-contract-24';
            rev = '03';
            updateto = '1394/05/03';
            break;
        case 25:
            workFlowCode = 'wf-invoice-25';
            rev = '03';
            updateto = '1394/05/03';
            break;
        case 26:
            workFlowCode = '';
            rev = '';
            updateto = '';
            break;
        case 27:
            workFlowCode = '';
            rev = '';
            updateto = '';
            break;
    }
        
    inisiateGraph();

            
//    var html = '<table  class="table"><tr><td colspan="3" style="text-align:center;padding: 0px;line-height: 2.42857143;vertical-align: top;border-top: 0px;font-size:15px;"><b>' + processTitle + '</b></td></tr>';
//    html += '<tr><td style="text-align:left;padding:0px;">Work Flow Code : ' + workFlowCode + '</td><td style="text-align:left;padding:0px;">Rev :' + rev + '</td><td style="text-align:left;padding:0px;text-align:right;">Up date to : ' + updateto + '</td></table>';



    
//    $('.pb-title-text').attr('style', 'width: 97%;height: 0px;');
//    $('.pb-title').attr('style', 'min-height: 65px;'); 
    
//    $('.pb-content').css('overflow', 'hidden');
            
        
    
});

function inisiateGraph() {

    width = parseInt($('.graph-list').css('width'), 10);
    width += 30;
    var sHeight = screen.height;
    height = 450;

    //var mt = (1716/width);
    //height=height/mt;

    margin = {top: 0, right: 0, bottom: 0, left: 0};
    width = width - margin.left - margin.right;
    height = height - margin.top - margin.bottom;

    zoom = d3.behavior.zoom()
            .scaleExtent([1, 10])
            .on("zoom", zoomed);

    drag = d3.behavior.drag()
            .origin(function (d) {
                return d;
            })
            .on("dragstart", dragstarted)
            .on("drag", dragged)
            .on("dragend", dragended);

    svg = d3.select("body svg")
//        .attr("width", width + margin.left + margin.right)
//        .attr("height", height + margin.top + margin.bottom)
            .attr("width", '100%')
            .attr("height", '100%')

            .attr("transform", "translate(" + margin.left + "," + margin.right + ")")
//        .attr("transform", "translate(0,0)")
//        .attr("transform", "scale(.8)translate(0,0)")
            .call(zoom);



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


