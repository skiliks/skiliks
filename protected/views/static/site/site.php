<!doctype html>
<html lang="ru" manifest="/cache.manifest">
<head>
    <meta charset="utf-8" />
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <title>Skiliks</title>


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
?>


<?php
// ### Java scripts :
    // jQuery нужен первым, чтобы "$" была обьявлена
    $this->renderPartial("//global_partials/jquery-1.7.2.min");
?>

<script type="text/javascript">
    window.gameVersion = '?v=1';
    window.gameConfig = <?= $config; ?>;
    window.assetsUrl = '<?= $assetsUrl; ?>';

    var require = {
        baseUrl: "<?= $assetsUrl; ?>/js",
        waitSeconds: 15
    };
    window.inviteId = <?= $inviteId ?>;
    window.httpUserAgent = '<?= $httpUserAgent ?>';

    window.siteHeartAuth = "<?= StaticSiteTools::getSiteHeartAuth(Yii::app()->user->data()); ?>";
</script>

<?php
/*
  * Можно было бы использовать $cs->registerScriptFile, но тогда все JS будут над jquery-1.7.2.min
  * или под window.raven - а надо чтоб были между
  */
?>

<script type="text/javascript" src="<?= $assetsUrl . '/js/socialcalc/socialcalcconstants_ru.js' ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/socialcalc/socialcalc-3.js' ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/socialcalc/socialcalctableeditor.js' ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/socialcalc/formatnumber2.js'  ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/socialcalc/formula1.js' ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/socialcalc/socialcalcpopup.js' ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/socialcalc/socialcalcspreadsheetcontrol.js' ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/game/util/socialcalc.js' ?>"></script>

<script type="text/javascript" src="<?= $assetsUrl . '/js/jquery/jquery.hotkeys.js' ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/jquery/jquery.balloon.js' ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/jquery/jquery.topzindex.min.js' ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/jquery/jquery.cookies.js' ?>"></script>

<?php // пока тут только метод .center ?>
<script type="text/javascript" src="<?= $assetsUrl . '/js/jquery/jquery-skiliks.js' ?>"></script>

<script type="text/javascript" src="<?= $assetsUrl . '/js/jquery/jquery.mCustomScrollbar.js' ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/jquery/jquery.mousewheel.js' ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/jquery/jquery.tablesorter.js' ?>"></script>

<?php // We need both!!! // 10 000 раз WTF! ?>
<script type="text/javascript" src="<?= $assetsUrl . '/js/jquery/jquery-ui-1.8.21.custom.min.js' ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/jquery/jquery-ui-1.10.3.custom.min.js'  ?>"></script>

<?php // system processor speed test ?>
<script type="text/javascript" src="<?= $assetsUrl . '/js/game/util/jsBogoMips.js' ?>"></script>

<script type="text/javascript" src="<?= $assetsUrl . '/js/bootstrap/js/bootstrap.js' ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/game/lib/hyphenate.js' ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/underscore.js' ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/prefixfree.min.js' ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/jquery.ddslick.min.js' ?>"></script>

<?php // MyDocument folder emulator ?>
<script type="text/javascript" src="<?= $assetsUrl . '/js/elfinder-2.0-rc1/js/elfinder.min.js' ?>"></script>

<script type="text/javascript" src="<?= $assetsUrl . '/js/tag-handler/jquery.taghandler.min.js' ?>"></script>
<script type="text/javascript" src="<?= $assetsUrl . '/js/backbone.js' ?>"></script>

<?php // track JS in sentry { ?>
<?php if (Yii::app()->params['public']['useSentryForJsLog']) :  ?>
    <script type="text/javascript" src="<?= $assetsUrl . '/js/sentry/tracekit.js' ?>"></script>
    <script type="text/javascript" src="<?= $assetsUrl . '/js/sentry/raven.js' ?>"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            window.Raven.config('<?= Yii::app()->params['sentry']['dsn'] ?>').install();
        });
    </script>
<?php endif; // track JS in sentry } ?>

<?php // файлы которые надо будет предзагрузить ?>
    <?php $this->renderPartial("/static/applicationcache/preload_images", ['assetsUrl' => $assetsUrl]); ?>

    <?php if(preg_match('/(?i)msie [10]/',$_SERVER['HTTP_USER_AGENT'])): ?>
        <script type="text/javascript">
            preLoadImages.push("<?= $assetsUrl . '/css/ie10.css'; ?>");
        </script>
    <?php endif ?>

    <?php if(preg_match('/(?i)Firefox/',$_SERVER['HTTP_USER_AGENT'])): ?>
        <script type="text/javascript">
            preLoadImages.push("<?= $assetsUrl . '/css/firefox_simulation.css'; ?>");
        </script>
    <?php endif ?>
<?php // файлы которые надо будет предзагрузить } ?>

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