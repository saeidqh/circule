


$(document).ready(function () {
    initPopover();
    
});

function initPopover(){
    $(".actionorder").click(function () {
            var td = $($(this).parent('td')); 
       $.ajax({
            url: $(this).attr('actionurl'),
        }).done(function (data) {

            $(td.find('.popover.in')).html(data);  

        }).error(function () {

            return false;
        });
        return false;
    });
    showPopover();
}
function showPopover() {
 $(".actionorder").each(function () {
        $(this).popover({html: true, placement: 'right', toggle: 'popover', title: '', content: '&nbsp;'});
    });
}
