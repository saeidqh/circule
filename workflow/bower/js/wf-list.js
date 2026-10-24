$(document).ready(function () {
//   $(".load-log-popup").click(function () {
//        LoadLogPopup($(this));
//    });  
    
    $('#btn-new-cashrequest').click(function(){
        showNewCashrequestForm();
    });
    
    $('#btn-sign-alert').click(function(){
        showMsgBoxInBottomRight('Contact admin for saving your <b style=\'color:yellow\'>Signature</b> befor using workflow.','error');
    });
    
    $('#w0').on("pjax:send", function(dt){
        ajaxProgressLoader('show', '#w0');
    });
    //بجایafterAjaxupdate
    $('#w0').on("pjax:complete", function(dt){
         ajaxProgressLoader('hide', '#w0');
    });
    
});	

function showRowNextActionForm(data,key){
    removeAllChilds();
    var rowId = 'row-'+key;
    var htm=  "<tr class='child-tr' id='tr-child-"+rowId+"'><td colspan='11' ><div class='tr-child-content'>{DATA}</div></td></tr>";
    
    
    htm = htm.replace('{DATA}',data);
    
    
    $('#'+rowId).after(htm);
        if(!($('#ActionEntity_ActionDateHijri').attr('readonly') == 'readonly')){
        $('#ActionEntity_ActionDateHijri').datepicker({dateFormat:'yy/mm/dd'});
    }
    $('#ActionEntity_ActionDateHijri').change(function(){
        $("#ActionEntity_ActionDate").val(ShamsiToGregorian($("#ActionEntity_ActionDateHijri").val()));
    })
    
//    initInputSteps();
}

function removeAllChilds(){
    $('#next-action-check').html('');
    $('.child-tr').each(function(){
        
        $(this).remove();
    });
}




function updateFormState(ddl){
    ajaxProgressLoader('show','.ajax-loading-next-action');
    var ajaxUrl = $(ddl).attr('ajax-url');
    var data= $('#next-action-form').serialize();
    var key= $(ddl).attr('peid');
    key = "#tr-child-row-"+key
    $.ajax({
	url:ajaxUrl+"&"+data+"&dropDown=true", 
        methode:'post',
        
    }).done(function(data){
        $('td .tr-child-content' ).html(data);    
        ajaxProgressLoader('hide','.ajax-loading-next-action');
    }).error(function(){
        showMsgBoxInBottomRight('error!','erroe');
        ajaxProgressLoader('hide','.ajax-loading-next-action');
    });
}


//*
function checkActionDetailResult(data,type){
    if(!data){
        showMsgBoxInBottomRight('Error!! ','error');
        return ;
    }
    var gridId  = 'grid-cash-request-todo'
    if(type == 'invoice')
        gridId  ='grid-workflow-invoice';
    
        
    
    if(data['Resault'] == 'false'){
        // error message
        showMsgBoxInBottomRight(data['Message'],'error');
    }else{
        // update todo total counter
        loadTodoCount();
        // close modal 
        $('#check-modal').modal('hide');
        //refresh gridview
        $.fn.yiiGridView.update( gridId );
        
        
        // succss message
        //showMsgBoxInBottomRight('Next action.....','success');
    }
}


function showNewCashrequestForm(){
    $('#new-cashrequest-modal').modal('show');  
}


function showLetterBox(obj){
    ajaxProgressLoader('show','.ajax-loading-next-action');
    obj = $(obj);
    var ajaxUrl = obj.attr('ajax-url');
    var lid  = $('#LetterID').val();
    
    $.ajax({
	url:ajaxUrl,
        data: {
            lid:lid,
        }
    }).done(function(data){
        $('#modal-side').html(data);
        ajaxProgressLoader('hide','.ajax-loading-next-action');
    }).error(function(){
        showMsgBoxInBottomRight(
            "Error on connecting to server.",
            "error"
        );
        ajaxProgressLoader('hide','.ajax-loading-next-action');
        return false;
    });



    
    
}
var k=0;
function showCheckForm(obj){

        ajaxProgressLoader('show', '.child-tr');
        // generate data
        var serData =$('#next-action-form input').serialize();
        serData += '&'+$('#next-action-form select').serialize();
        serData += '&'+$('#next-action-form textarea').serialize();
        var ajaxUrl = $('#btn-check').attr('ajax-url')+'&'+serData;
        var parentBox = $('#btn-check').attr('parent-box');
        var updateBox = $('#btn-check').attr('update-box');
 
         $.ajax({
            url:$('#next-action-form').attr('action'),
            data:serData,
        }).done(function(data){
            if(data.result == false)
            {
                if(!jQuery.isEmptyObject(data.message.ActionMessage)){
                    for(var i=0; i<Object.keys(data.message.ActionMessage).length; i++)
                    {
                        if(data.message.ActionMessage[i] != '')
                        {
                           showMsgBoxInBottomRight(data.message.ActionMessage[i]+ ' ActionMessage', 'error');
                        }
                    }
                }
                
                if(!jQuery.isEmptyObject(data.message.LetterNumber)){
                    for(var i=0; i<Object.keys(data.message.LetterNumber).length; i++)
                    {
                        if(data.message.LetterNumber[i] != '')
                        {
                           showMsgBoxInBottomRight(data.message.LetterNumber[i]+ ' LetterNumber', 'error');
                        }
                    }
                }

                if(!jQuery.isEmptyObject(data.message.ActionDateHijri)){
                    for(var i=0; i<Object.keys(data.message.ActionDateHijri).length; i++)
                    {
                        if(data.message.ActionDateHijri[i] != '')
                        {
                           showMsgBoxInBottomRight(data.message.ActionDateHijri[i]+ ' ActionDateHijri', 'error');
                        }
                    }
                }
                
               
            }
            else{
                $.ajax({
                    url:ajaxUrl,
                }).done(function(data){
                    $(updateBox).html(data);
                    $('#check-modal').modal('show');
                    ajaxProgressLoader('hide', '.child-tr');
                }).error(function(){
                    showMsgBoxInBottomRight('Error on loading data!','error');
                    ajaxProgressLoader('hide','.child-tr');
                });
            }
            ajaxProgressLoader('hide', '.child-tr');
            return false;
        }).error(function(){
            showMsgBoxInBottomRight('Error on loading data!','error');
            ajaxProgressLoader('hide','.child-tr');
        });
        ajaxProgressLoader('hide', '.child-tr');
        return false;
}



function hideNextActionBox(key){
    var row = "#tr-child-row-"+key;
    setActiveRow('#grid-cash-request-todo','#row-'+key);
    $(row).remove();
    
}
// make active and deactive rows
function setActiveRow(tableId,rowId){
    
    $(''+tableId+' table tbody tr').each(function(){
        $(this).removeClass('action-active-row');
    });
    if(rowId != null){
        $(''+rowId).addClass('action-active-row');
    }
    
}
function removeActiveRows(tableId){
    setActiveRow(tableId);
}
//----------------



function addFlasherToInput(obj,type){
    $(obj.parent()).parent().addClass('input-flasher');
    toggleBorder(obj);
    obj.attr('toggle-state','on');
}

function removeFlasherFromInput(obj,type){
    
    $(obj.parent()).parent().removeClass('input-flasher');
    $('.step-form').attr('form-status','change');
    obj.attr('toggle-state','off');
}


function toggleBorder(obj){
    
    
    var state = obj.attr('toggle-state');
    
    if (state == 'off') return;
    var div = obj.parent();
    var bc = div.css('borderColor');
     
    var border = 1;
    var color = 'rgb(255, 0, 0)';
     
    if(bc == 'rgb(255, 0, 0)'){
        border = 0;
        color = 'white';
    }
    div.stop().animate({
         //borderWidth:border,
         borderColor:color
    },200,function(){
         setTimeout('toggleBorder($("#'+obj.attr('id')+'"))',300);
    });
}