<script type="text/template" id="visit_template">
    <div>
        <video class="video_background" src="<@= video_src @>" autoplay="autoplay"></video>
        <@ if (remote_replica) { @>
        <div class="visitor-reply"><p class="visitor-replica"><@=remote_replica.text@></p>
            <@ } @>

            <ul class="char-reply" id="dialogControllerAnswers">
                <@ my_replicas.forEach(function (replica) { @>
                <li><p>
                    <a href="" class="replica-select" data-id="<@= replica.id @>">
                        <@= replica.text.replace(/^\s*-/, '—')@>
                    </a>
                    <span></span></p></li>
                <@ }) @>
            </ul>
        </div>
    </div>
</script>
