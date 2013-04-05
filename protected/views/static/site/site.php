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

    <link  type="text/css" rel="stylesheet" href="<?= $assetsUrl; ?>/js/jquery/jquery-ui.css" />
    <link  type="text/css" rel="stylesheet" href="<?= $assetsUrl; ?>/js/bootstrap/css/bootstrap.css" />
    <link  type="text/css" rel="stylesheet" href="<?= $assetsUrl; ?>/js/jquery/jquery-ui-1.8.23.slider.css" />
    <link  type="text/css" rel="stylesheet" href="<?= $assetsUrl; ?>/js/jquery/jquery.mCustomScrollbar.css" />
    <link  type="text/css" rel="stylesheet" href="<?= $assetsUrl; ?>/js/elfinder-2.0-rc1/css/elfinder.min.css" />
    <link  type="text/css" rel="stylesheet" href="<?= $assetsUrl; ?>/js/elfinder-2.0-rc1/css/theme.css" />
    <link  type="text/css" rel="stylesheet" href="<?= $assetsUrl; ?>/css/tag-handler.css" />
    <link  type="text/css" rel="stylesheet" href="<?= $assetsUrl; ?>/css/ddSlick.css" />
    <link  type="text/css" rel="stylesheet" href="<?= $assetsUrl; ?>/css/main.css" />

    <?php
        $cs = Yii::app()->clientScript;
        $cs->registerLessFile($assetsUrl . '/less/simulation.less', $assetsUrl . '/css');
        $cs->registerLessFile($assetsUrl . '/less/plan.less', $assetsUrl . '/css');
        $cs->registerLessFile($assetsUrl . '/less/mail.less', $assetsUrl . '/css');
        $cs->registerLessFile($assetsUrl . '/less/documents.less', $assetsUrl . '/css');
    ?>

    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery-ui-1.8.24.custom.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery.hotkeys.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery.balloon.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery.topzindex.min.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery.cookies.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery-skiliks.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery.mCustomScrollbar.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery.mousewheel.min.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery.tablesorter.js"></script>

    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/game/lib/pdf.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/game/lib/hyphenate.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/underscore.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/prefixfree.min.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery.ddslick.min.js"></script>

    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/backbone.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/less-1.3.3.min.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/elfinder-2.0-rc1/js/elfinder.min.js"></script>

    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/tag-handler/jquery.taghandler.min.js"></script>

    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/raven-0.7.1.js"></script>

    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/require.js" data-main="game/application.js"></script>

    <?php if (!YII_DEBUG): ?>
    <script type="text/javascript">
        Raven.config('https://bfd7395024f24728afdf79e9034bca04:2f8bec2e2c40493dbf7b07db88afc94f@app.getsentry.com/4572');
        window.onerror = Raven.process;
    </script>
    <?php endif; ?>

</head>
<body class="body loading">
    <div id="excel-cache" style="display: none; visibility: hidden;"></div>
</body>
</html>