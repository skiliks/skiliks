<div id="refferal-popup" style="display: none;">
    <div class="more-side-pads">
        <h2 class="title">Пригласить друга</h2>
        <input type="checkbox" id="dontShowPopupCheckbox"> Не показывать снова
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.content').css('margin-bottom', '600px');

        $('#refferal-popup').dialog({
            dialogClass: 'accept-invite-warning-popup full-simulation-info-popup margin-top-popup',
            modal:       true,
            autoOpen:    true,
            resizable:   false,
            draggable:   false,
            width:       881,
            maxHeight:   600,
            position: {
                my: "left top",
                at: "left bottom",
                of: $("header h1")
            },
            open: function( event, ui ) {
            },
            close: function() {
                var showPopup = 0;
                if($("#dontShowPopupCheckbox").is(":checked")) {
                    showPopup = 1;
                }
                $.post( "/dashboard/dontShowPopup", { dontShowPopup : showPopup}).done(function() {
                });
            }
        });

        // hack {
        $('.accept-invite-warning-popup full-simulation-info-popup').css('top', '50px');
        $(window).scrollTop('.narrow-contnt');
    })
</script>