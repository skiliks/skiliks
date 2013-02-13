<script type="text/template" id="visit_template">
    <div class="visit-background-container">
        <@ if (video_src) { @>
        <video class="visit-background" src="<@= video_src @>" autoplay="autoplay"></video>
        <@ } @>
        <@ if (img_src) { @>
        <img class="visit-background" src="<@= img_src @>" />
        <@ } @>
        <@ if (remote_replica) { @>
        <div class="visitor-reply"><p class="visitor-replica"><@=remote_replica.text@></p>
            <@ } @>

            <ul class="char-reply" id="dialogControllerAnswers">
                <@ my_replicas.forEach(function (replica) { @>
                <li><p>
                    <a href="" class="replica-select" data-id="<@= replica.id @>" <@if (replica.is_final_replica === "1") { @>
                    data-is-final="true"
                    <@ } @>>
                        <@= replica.text.replace(/^\s*-\s*/, ' — ')@>
                    </a>
                    <span></span></p></li>
                <@ }) @>
            </ul>
        </div>
    </div>
</script>
<script type="text/template" id="visit_door">
    <div id="dialogControllerMainDiv" class="mail-emulator-main-div">
        <section class="visitor-income">
            <div class="visitor-img">
                <img alt="" src="<@=SKConfig.assetsUrl@>/img/visitor/visitor-ch<@=visit[0].ch_from@>.png">
            </div>
            <div class="visitor-rbl">
                <@ if(isDisplayCloseWindowsButton){ @>
                    <button class="btn-close"></button>
                <@ } @>
                <p class="visitor-name"><@=visit[0].title@></p>
                <div class="visitor-btn">
                    <a class="visitor-allow" data-dialog-id="<@=visit[1].id@>"><span><@=visit[1].text@></span></a><br>

                    <a class="visitor-deny" data-dialog-id="<@=visit[2].id@>"><span><@=visit[2].text@></span></a><br>
                </div>
            </div>
        </section>
    </div>
</script>
