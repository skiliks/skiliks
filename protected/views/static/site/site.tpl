<!doctype html>
<html lang="ru">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
    <script type="text/javascript">
        window.gameVersion = '?v=1';
        var require = {
            baseUrl: "{$assetsUrl}/js",
            waitSeconds: 15
        };
    </script>
    <title>Skiliks</title>


    <script type="text/javascript">
        _.templateSettings.interpolate = /<@=(.+?)@>/g;
        _.templateSettings.evaluate = /<@(.+?)@>/g;
        //noinspection JSUnresolvedFunction
        require(["game/views/world/SKApplicationView"], function (SKApplicationView) {
            $(function () {
                window.SKApp = new window.SKApplication({$config});
                window.AppView = new window.SKApplicationView();
            });
        });

        {if !$smarty.const.YII_DEBUG}
        Raven.config('https://bfd7395024f24728afdf79e9034bca04:2f8bec2e2c40493dbf7b07db88afc94f@app.getsentry.com/4572');
        window.onerror = Raven.process;
        {/if}

    </script>

</head>
<body class="body">
<div id="excel-cache" style="display: none; visibility: hidden;"></div>
</body>
</html>