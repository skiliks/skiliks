<script type="text/template" id="icon_panel">
    <ul class="icons-panel">
        <li class="messenger only-active"><a href="#"></a></li>
        <li class="plan"><a href="" id="icons_todo"></a></li>
        <li class="phone"><a href="" id="icons_phone"></a></li>
        <li class="mail"><a href="" id="icons_email"></a></li>
        <li class="door only-active"><a href="" id="icons_visit"></a></li>
        <li class="documents"><a href="" id="icons_documents"></a></li>
    </ul>
</script>

<script type="text/template" id="simulation_template">
    <div style="width: 100%;height: 100%;">
        <div id="canvas" class="canvas">
            <ul class="main-screen-stat">
                <li class="time"><span class="hour"></span><span class="delimiter">:</span><span
                        class="minute"></span></li>
                <li><img src="{$assetsUrl}/img/main-screen/icon-bat-full.png" alt=""/></li>
                <li><a><img alt="" src="{$assetsUrl}/img/main-screen/icon-help.png"></a></li>
            </ul>
            <div class="phone-dialog-div"></div>
            <div class="visitor-container"></div>
            <div class="plan-container"></div>
            <nav class="main-screen-icons"></nav>
        </div>
        <div class="simulation-controls">
            <div class="debug-panel"></div>
        </div>

    </div>
    <div id="location" class="location"></div>
    </div>
    <div id="message" class="message"></div>
</script>
