<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Skiliks - Админка</title>

    <script type="text/javascript">
        var SKConfig = {$config};
        window.gameVersion = '?v=1';

        //_.templateSettings.interpolate = /<@=(.+?)@>/g;
        //_.templateSettings.evaluate = /<@(.+?)@>/g;

        //Raven.config('https://bfd7395024f24728afdf79e9034bca04:2f8bec2e2c40493dbf7b07db88afc94f@app.getsentry.com/4572');
        //window.onerror = Raven.process;
    </script>
</head>
<body>
    <canvas height="0" width="0" id="canvas" style="cursor: crosshair; position: absolute; left:300; top:50;"></canvas>
    <center>
        <div id="location" style="width: 0px; height: 0px; margin-top: 50px;"></div>
    </center>
        
    <div id="message" class="message"></div>
    {$jsScripts}
</body>