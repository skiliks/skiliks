<!doctype html>
<html lang="ru">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
    <script type="text/javascript">
        var SKConfig = {$config};
        window.gameVersion = '?v=1';
    </script>
    <title>Skiliks</title>


    <script type="text/javascript">
        _.templateSettings.interpolate = /<@=(.+?)@>/g;
        _.templateSettings.evaluate = /<@(.+?)@>/g;
        {if !$smarty.const.YII_DEBUG}
        Raven.config('https://bfd7395024f24728afdf79e9034bca04:2f8bec2e2c40493dbf7b07db88afc94f@app.getsentry.com/4572');
        window.onerror = Raven.process;
        {/if}
    </script>

{include "site/jst/world.tpl"}
{include "site/jst/window.tpl"}
{include "site/jst/visit.tpl"}
{include "site/jst/simulation.tpl"}
{include "site/jst/plan.tpl"}
{include "site/jst/mail.tpl"}
{include "site/jst/documents.tpl"}
{include "site/jst/phone.tpl"}
</head>
<body class="body">
<div id="excel-cache" style="display: none; visibility: hidden;"></div>
</body>
</html>