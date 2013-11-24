<!doctype html>
<html lang="ru" manifest="/cache.manifest">
<head>
    <meta charset="utf-8" />
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <title>Skiliks</title>

    <script type="text/javascript">
        window.gameVersion = '?v=1';
        window.gameConfig = <?= $config; ?>;

        var require = {
            baseUrl: "<?= $assetsUrl; ?>/js",
            waitSeconds: 15
        };
        window.inviteId = <?= $inviteId ?>;
        window.httpUserAgent = '<?= $httpUserAgent ?>';

        window.siteHeartAuth = "<?= StaticSiteTools::getSiteHeartAuth(Yii::app()->user->data()); ?>";
    </script>



<?php

 $cs = Yii::app()->clientScript;

 /*
 * @var YiiLessCClientScript $cs
 * потомок CClientScript
 */

// ### CSS files:

 $cs->registerCssFile($assetsUrl . '/js/jquery/jquery-ui.css');
 $cs->registerCssFile($assetsUrl . '/js/bootstrap/css/bootstrap.css');
 $cs->registerCssFile($assetsUrl . '/js/jquery/jquery-ui-1.8.23.slider.css');
 $cs->registerCssFile($assetsUrl . '/js/jquery/jquery.mCustomScrollbar.css');
 $cs->registerCssFile($assetsUrl . '/js/elfinder-2.0-rc1/css/elfinder.min.css');
 $cs->registerCssFile($assetsUrl . '/js/elfinder-2.0-rc1/css/theme.css');
 $cs->registerCssFile($assetsUrl . '/css/tag-handler.css');
 $cs->registerCssFile($assetsUrl . '/css/ddSlick.css');
 $cs->registerCssFile($assetsUrl . '/css/main.css');

 $cs->registerLessFile($assetsUrl . '/less/simulation.less', $assetsUrl . '/compiled_css/simulation.css');
 $cs->registerLessFile($assetsUrl . '/less/manual.less',     $assetsUrl . '/compiled_css/manual.css');
 $cs->registerLessFile($assetsUrl . '/less/plan.less',       $assetsUrl . '/compiled_css/plan.css');
 $cs->registerLessFile($assetsUrl . '/less/mail.less',       $assetsUrl . '/compiled_css/mail.css');
 $cs->registerLessFile($assetsUrl . '/less/documents.less',  $assetsUrl . '/compiled_css/documents.css');
/**
 * Подключаем ie10.css для специфичной IE10 вёрстки.
 * Все прочие (общие) CSS должны быть подключены ниже.
 * @link:http://stackoverflow.com/questions/16474948/detect-ie10-ie10-and-other-browsers-in-php
 */
if(preg_match('/(?i)msie [10]/',$_SERVER['HTTP_USER_AGENT']))
{
     $cs->registerCssFile($assetsUrl . '/css/ie10.css');
}

// ### Java scripts :

// jQuery нужен первым, чтобы "$" была обьявлена
$this->renderPartial("//global_partials/jquery-1.7.2.min");

$cs->registerScriptFile($assetsUrl . '/js/underscore.js', CClientScript::POS_END);

$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.hotkeys.js', CClientScript::POS_END);
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.balloon.js', CClientScript::POS_END);
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.topzindex.min.js', CClientScript::POS_END);
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.cookies.js', CClientScript::POS_END);
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-skiliks.js', CClientScript::POS_END);
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.mCustomScrollbar.js', CClientScript::POS_END);
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.mousewheel.js', CClientScript::POS_END);
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.tablesorter.js', CClientScript::POS_END);

// 10 000 раз WTF!
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-ui-1.8.21.custom.min.js', CClientScript::POS_END);
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-ui-1.10.3.custom.min.js', CClientScript::POS_END);

$cs->registerScriptFile($assetsUrl . '/js/socialcalc/socialcalcconstants_ru.js', CClientScript::POS_END);

$cs->registerScriptFile($assetsUrl . '/js/socialcalc/socialcalc-3.js', CClientScript::POS_END);
$cs->registerScriptFile($assetsUrl . '/js/socialcalc/socialcalctableeditor.js', CClientScript::POS_END);
$cs->registerScriptFile($assetsUrl . '/js/socialcalc/formatnumber2.js', CClientScript::POS_END);
$cs->registerScriptFile($assetsUrl . '/js/socialcalc/formula1.js', CClientScript::POS_END);
$cs->registerScriptFile($assetsUrl . '/js/socialcalc/socialcalcpopup.js', CClientScript::POS_END);
$cs->registerScriptFile($assetsUrl . '/js/socialcalc/socialcalcspreadsheetcontrol.js', CClientScript::POS_END);
$cs->registerScriptFile($assetsUrl . '/js/game/util/socialcalc.js', CClientScript::POS_END);

// system processor speed test
$cs->registerScriptFile($assetsUrl . '/js/game/util/jsBogoMips.js', CClientScript::POS_END);

$cs->registerScriptFile($assetsUrl . '/js/bootstrap/js/bootstrap.js', CClientScript::POS_END);
$cs->registerScriptFile($assetsUrl . '/js/game/lib/hyphenate.js', CClientScript::POS_END);
$cs->registerScriptFile($assetsUrl . '/js/prefixfree.min.js', CClientScript::POS_END);
$cs->registerScriptFile($assetsUrl . '/js/jquery.ddslick.min.js', CClientScript::POS_END);

// MyDocument folder emulator
$cs->registerScriptFile($assetsUrl . '/js/elfinder-2.0-rc1/js/elfinder.min.js', CClientScript::POS_END);

$cs->registerScriptFile($assetsUrl . '/js/tag-handler/jquery.taghandler.min.js', CClientScript::POS_END);

// track JS in sentry {
if (Yii::app()->params['public']['useSentryForJsLog']) {
    $cs->registerScriptFile($assetsUrl . '/js/sentry/tracekit.js', CClientScript::POS_END);
    $cs->registerScriptFile($assetsUrl .'/js/sentry/raven.js', CClientScript::POS_END);
    ?>
        <script type="text/javascript">
            $(document).ready(function(){
                window.Raven.config('<?= Yii::app()->params['sentry']['dsn'] ?>').install();
            });
        </script>
    <?php
// track JS in sentry }
}

// картинки интерфейсов, которые надо будет предзагрузить
$this->renderPartial("/static/applicationcache/preload_images", ['assetsUrl' => $assetsUrl]);

$cs->registerScriptFile($assetsUrl . '/js/backbone.js', CClientScript::POS_END);

?>
</head>

<body class="body" style="background-color: #2e2e2e; text-align: center;">
    <div id="loading-cup">
        <img src="<?= $assetsUrl; ?>/img/loading-cup.jpg" alt="Loading..." style="margin: 0 auto; clear: both; display: block;" />
        <h2 class="white-color" style="color: #ffffff; margin: 0 auto;">Загружается <?=$scenarioLabel?></h2>
        <div id="images-loader-text"
             style="margin: 0 auto; height: 60px; width: 400px;
             text-align: center; color: #ffffff"></div>

        <div id="images-loader"
             style="margin: 0 auto; width: 400px; border: 1px solid #000; height: 30px;">

            <div id="images-loader-bar" style="background-color: grey; height: 30px; width: 0px;"></div>
        </div>
    </div>
    <div id="excel-cache" style="display: none; visibility: hidden;"></div>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/require.js" data-main="game/application.js"></script>
</body>
</html>