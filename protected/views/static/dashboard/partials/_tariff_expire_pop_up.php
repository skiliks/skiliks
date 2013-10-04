<div id="tariff-expired-popup">
    <div style="clear: both;"></div>
    <div class="ProximaNova-Bold">
        Ваш тарифный план истек.
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#tariff-expired-popup').dialog({
            dialogClass: 'accept-invite-warning-popup full-simulation-info-popup show-popup-top',
            modal:       false,
            autoOpen:    true,
            resizable:   false,
            closeOnEscape: false,
            draggable:   false,
            width:       940,
            height:   15,
            position: {
                my: "left top",
                at: "left bottom",
                of: $("header h1")
            },
            open: function( event, ui ) {
                $(".ui-dialog-content").css("height", "10px");
                $(".show-popup-top .ui-dialog-titlebar-close").show();
            },
            close: function() {
                $.post('/dashboard/dontShowTariffEndPopup', {is_display_tariff_expire_pop_up : "1"});
            }
        });

        // hack {
        $('.accept-invite-warning-popup full-simulation-info-popup').css('top', '50px');
        $(window).scrollTop('.narrow-contnt');
    })
</script>