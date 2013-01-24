<script type="text/template" id="visit_template">
    <div class="visit-background-container">
        <@ if (video_src) { @>
        <video class="visit-background" src="<@= video_src @>" autoplay="autoplay" <@= mute_attribute @>></video>
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
