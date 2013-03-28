$(document).ready(function(){

    $('a.feedback').click(function(){
        $('.feedback_dialog').dialog({ width: 600});
        return false;
    });
        $('.dashboard').append($('<div id="simulation-details-pop-up"></div>'));
        $('#simulation-details-pop-up').dialog({
            modal: true,
            width: 940,
            minHeight: 600
        });

        $('#simulation-details-pop-up').dialog('close');

        $(".view-simulation-details-pop-up").click(function(event){
            event.preventDefault();
            $.ajax({
                url: $(this).attr('data-simulation'),
                success: function(data) {

                    $('#simulation-details-pop-up').html(data);
                    $('#simulation-details-pop-up').dialog('open');
                }
            });
        });
});