<!doctype html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
    <script type="text/javascript">
        var SKConfig = {$config};
        window.gameVersion = '?v=<?php echo $version ?>';
    </script>
    <title>Skiliks</title>


    <script type="text/javascript">
        _.templateSettings.interpolate = /<@=(.+?)@>/g;
        _.templateSettings.evaluate = /<@(.+?)@>/g;
    </script>


{include "site/jst/world.tpl"}
{include "site/jst/window.tpl"}
{include "site/jst/visit.tpl"}
{include "site/jst/simulation.tpl"}
{include "site/jst/plan.tpl"}
{include "site/jst/mail.tpl"}
{include "site/jst/phone.tpl"}
</head>
<body class="body">
<div id="excel-cache" style="display: none; visibility: hidden;"></div>
</body>
</html>