<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <script type="text/javascript">
        window.gameVersion = '?v=1';
        window.gameConfig = <?= $config; ?>;

        var require = {
            baseUrl: "<?= $assetsUrl; ?>/js",
            waitSeconds: 15
        };
    </script>

    <title>Skiliks</title>

    <?php
        $cs = Yii::app()->clientScript;

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
        $cs->registerLessFile($assetsUrl . '/less/plan.less',       $assetsUrl . '/compiled_css/plan.css');
        $cs->registerLessFile($assetsUrl . '/less/mail.less',       $assetsUrl . '/compiled_css/mail.css');
        $cs->registerLessFile($assetsUrl . '/less/documents.less',  $assetsUrl . '/compiled_css/documents.css');
    ?>

    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery.hotkeys.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery.balloon.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery.topzindex.min.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery.cookies.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery-skiliks.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery.mCustomScrollbar.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery.mousewheel.min.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery.tablesorter.js"></script>

    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/game/lib/hyphenate.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/underscore.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/prefixfree.min.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery.ddslick.min.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/game/lib/pdf.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/elfinder-2.0-rc1/js/elfinder.min.js"></script>

    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/tag-handler/jquery.taghandler.min.js"></script>

    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/raven-0.7.1.js"></script>


    <?php if (!YII_DEBUG): ?>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/require.js" data-main="skiliks.min.js"></script>
    <script type="text/javascript">
        Raven.config('https://bfd7395024f24728afdf79e9034bca04:2f8bec2e2c40493dbf7b07db88afc94f@app.getsentry.com/4572');
        window.onerror = Raven.process;
    </script>
    <?php endif; ?>
    <?php if (YII_DEBUG): ?>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/require.js" data-main="game/application.js"></script>
    <?php endif ?>
</head>
<body class="body loading">
    <div id="excel-cache" style="display: none; visibility: hidden;"></div>
</body>
</html>