<div id="tariff-expired-popup">
    <div style="clear: both;"></div>
    <div class="message-text" style="overflow-y:hidden;">
        Ваш тарифный план истёк. Вы можете его <a href="<?= MailHelper::createUrlWithHostname('profile/corporate/tariff') ?>">продлить</a> или <a href="<?= MailHelper::createUrlWithHostname('static/tariffs') ?>">оформить новый</a>.
    </div>
</div>
<script>
    $(document).ready(function() {
        /*$('#tariff-expired-popup').dialog({
            dialogClass: 'accept-invite-warning-popup full-simulation-info-popup show-popup-top tariff-expired-popup',
            modal:       false,
            autoOpen:    true,
            resizable:   false,
            closeOnEscape: false,
            draggable:   false,
            width:       934,
            height:   20,
            position: {
                my: "left top",
                at: "left bottom",
                of: $("header h1")
            },
            open: function( event, ui ) {
                $(".ui-dialog-content").css("height", "20px");
                $(".tariff-expired-popup").css({"z-index" : "1000", "margin-left" : "7px"});
                $("span#ui-dialog-title-tariff-expired-popup").remove();
                <? if($hasOtherPopup) : ?>
                    $(".tariff-expired-popup").css("margin-top", "70px");
                <? endif; ?>
            },
            close: function() {
                $.post('/dashboard/dontShowTariffExpirePopup');
            }
        });*/
    })
</script>

