<div id="referral-popup" style="min-height: 17px !important;">
    <div style="clear: both;"></div>
    <div class="ProximaNova-Bold">
        Теперь вы можете получить дополнительные симуляции, пригласив друзей
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.content').css('margin-bottom', '600px');

        $('#referral-popup').dialog({
            dialogClass: 'accept-invite-warning-popup full-simulation-info-popup show-popup-top referral-popup',
            modal:       false,
            autoOpen:    true,
            resizable:   false,
            closeOnEscape: false,
            draggable:   false,
            width:       935,
            height:   20,
            position: {
                my: "left top",
                at: "left bottom",
                of: $("header h1")
            },
            open: function( event, ui ) {
                $(".referral-popup").css({"z-index" : "1000", "margin-left" : "19px"});
                $(".ui-dialog-content").css("height", "20px");
                $("span#ui-dialog-title-referral-popup").remove();
            },
            close: function() {
                $.post( "/dashboard/dontShowPopup", { dontShowPopup : 1}).done(function() {
                    $(".tariff-expired-popup").css("margin-top", "0");
                });
            }
        });

        $(".referral-popup").click(function() {
           window.location.href = "/invite/referrals";
        });
    })
</script>