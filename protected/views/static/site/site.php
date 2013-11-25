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

// они уже в авто-подгрузке: CreateListOfPreloadedFilesCommand

// $cs->registerCssFile($assetsUrl . '/js/jquery/jquery-ui.css');
// $cs->registerCssFile($assetsUrl . '/js/bootstrap/css/bootstrap.css');
// $cs->registerCssFile($assetsUrl . '/js/jquery/jquery-ui-1.8.23.slider.css');
// $cs->registerCssFile($assetsUrl . '/js/jquery/jquery.mCustomScrollbar.css');
// $cs->registerCssFile($assetsUrl . '/js/elfinder-2.0-rc1/css/elfinder.min.css');
// $cs->registerCssFile($assetsUrl . '/js/elfinder-2.0-rc1/css/theme.css');
// $cs->registerCssFile($assetsUrl . '/css/tag-handler.css');
// $cs->registerCssFile($assetsUrl . '/css/ddSlick.css');
// $cs->registerCssFile($assetsUrl . '/css/main.css');
//

$cs->compileLess($assetsUrl . '/less/simulation.less', $assetsUrl . '/compiled_css/simulation.css');
$cs->compileLess($assetsUrl . '/less/manual.less',     $assetsUrl . '/compiled_css/manual.css');
$cs->compileLess($assetsUrl . '/less/plan.less',       $assetsUrl . '/compiled_css/plan.css');
$cs->compileLess($assetsUrl . '/less/mail.less',       $assetsUrl . '/compiled_css/mail.css');
$cs->compileLess($assetsUrl . '/less/documents.less',  $assetsUrl . '/compiled_css/documents.css');

//$cs->registerLessFile($assetsUrl . '/less/simulation.less', $assetsUrl . '/compiled_css/simulation.css');
// $cs->registerLessFile($assetsUrl . '/less/manual.less',     $assetsUrl . '/compiled_css/manual.css');
// $cs->registerLessFile($assetsUrl . '/less/plan.less',       $assetsUrl . '/compiled_css/plan.css');
// $cs->registerLessFile($assetsUrl . '/less/mail.less',       $assetsUrl . '/compiled_css/mail.css');
// $cs->registerLessFile($assetsUrl . '/less/documents.less',  $assetsUrl . '/compiled_css/documents.css');
/**
 * Подключаем ie10.css для специфичной IE10 вёрстки.
 * Все прочие (общие) CSS должны быть подключены ниже.
 * @link:http://stackoverflow.com/questions/16474948/detect-ie10-ie10-and-other-browsers-in-php
 */
//if(preg_match('/(?i)msie [10]/',$_SERVER['HTTP_USER_AGENT']))
//{
//     $cs->registerCssFile($assetsUrl . '/css/ie10.css');
//}

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

<?php if(preg_match('/(?i)msie [10]/',$_SERVER['HTTP_USER_AGENT'])): ?>
    <script type="text/javascript">
        preLoadImages.push("<?= $assetsUrl . '/css/ie10.css'; ?>");
    </script>
<?php endif ?>

    <style>
        #loading-cup {
            color: #ffffff !important;
            height: 500px !important;
            padding-top: 5% !important;
            margin: 0 auto !important;
            width: 759px !important;
        }

        #loading-cup-img {
            clear: both;
            display: block;
            heigth: 338px;
            margin: 0 auto !important;
        }

        #loading-cup h2 {
            font-size: 24px !important;
            font-family: sans-serif !important;
        }

        #images-loader-text {
            height: 60px !important;
            font-size: 14px !important;
            text-align: center !important;
            margin: 0 auto !important;
            font-family: sans-serif !important;
            width: 400px !important;
        }

        #images-loader {
            margin: 0 auto !important;
            width: 400px !important;
            border: 1px solid #000 !important;
            height: 30px !important;
        }

        #images-loader-bar {
            background-color: grey !important;
            height: 30px !important;
            width: 0px;
        }
    </style>

</head>

<body class="body" style="background-color: #2e2e2e; text-align: center;">
    <div id="loading-cup">
        <img id="loading-cup-img" src="<?= $assetsUrl; ?>/img/loading-cup.jpg" alt="" />
        <h2 class="white-color">Загружается <?=$scenarioLabel?></h2>
        <div id="images-loader-text"></div>

        <div id="images-loader">
            <div id="images-loader-bar"></div>
        </div>
    </div>
    <div id="excel-cache" style="display: none; visibility: hidden;"></div>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/require.js" data-main="game/application.js"></script>
</body>
</html>