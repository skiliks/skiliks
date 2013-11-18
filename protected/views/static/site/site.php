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
        window.inviteId = <?= $inviteId ?>;
        window.httpUserAgent = '<?= $httpUserAgent ?>';

        window.siteHeartAuth = "<?= StaticSiteTools::getSiteHeartAuth(Yii::app()->user->data()); ?>";
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
        $cs->registerLessFile($assetsUrl . '/less/manual.less',     $assetsUrl . '/compiled_css/manual.css');
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
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery.mousewheel.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery.tablesorter.js"></script>

    <!-- We need both!!! -->
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery-ui-1.8.21.custom.min.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery/jquery-ui-1.10.3.custom.min.js"></script>

    <script>

        function preload(arrayOfImages) {
            $(arrayOfImages).each(function(){
                $('<img/>')[0].src = this;
                console.log(this);
            });
        }

        $(document).ready(function() {
            win = $(window);
            cupdiv = $("#loading-cup");
            topMargin = (win.height() - cupdiv.outerHeight()) / 2 + 'px';
            leftMargin = (win.width() - cupdiv.outerWidth()) / 2 + 'px';
            $("#loading-cup").css("margin-top",topMargin);
            $("#loading-cup").css("margin-left",leftMargin);
        });
    </script>

    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/socialcalc/socialcalcconstants_ru.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/socialcalc/socialcalc-3.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/socialcalc/socialcalctableeditor.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/socialcalc/formatnumber2.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/socialcalc/formula1.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/socialcalc/socialcalcpopup.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/socialcalc/socialcalcspreadsheetcontrol.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/game/util/socialcalc.js"></script>

    <!-- system processor speed test -->
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/game/util/jsBogoMips.js"></script>

    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/game/lib/hyphenate.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/underscore.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/prefixfree.min.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/jquery.ddslick.min.js"></script>
    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/elfinder-2.0-rc1/js/elfinder.min.js"></script>

    <? $this->renderPartial("/static/applicationcache/preload_images", ['assetsUrl' => $assetsUrl]); ?>

    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/tag-handler/jquery.taghandler.min.js"></script>

    <?php // track JS in sentry { ?>
        <script type="text/javascript" src="<?= $assetsUrl; ?>/js/sentry/tracekit.js"></script>
        <?php if (Yii::app()->params['public']['useSentryForJsLog']) : ?>
            <script type="text/javascript" src="<?= $assetsUrl; ?>/js/sentry/raven.js"></script>

            <script type="text/javascript">
                $(document).ready(function(){
                    window.Raven.config('<?= Yii::app()->params['sentry']['dsn'] ?>').install();
                });
            </script>
        <?php endif; ?>
    <?php // track JS in sentry { ?>

    <script type="text/javascript" src="<?= $assetsUrl; ?>/js/backbone.js"></script>

    <?php /*if (!YII_DEBUG): ?>
        <script type="text/javascript" src="<?= $assetsUrl; ?>/js/require.js" data-main="skiliks.min.js"></script>
    <?php endif;*/ ?>

    <?php //if (YII_DEBUG): ?>
        <script type="text/javascript" src="<?= $assetsUrl; ?>/js/require.js" data-main="game/application.js"></script>
    <?php //endif ?>
</head>

<body class="body loading" style="background-color: #2e2e2e;">
    <div id="loading-cup">
        <img src="<?= $assetsUrl; ?>/img/loading-cup.jpg" alt="Loading..." /><br/>
        <h2 class="white-color" style="color: #ffffff;">Загружается <?=$scenarioLabel?></h2><br/>
        <img src="<?= $assetsUrl; ?>/img/design/ajax-loader.gif" alt="Loader..." />
    </div>
    <div id="excel-cache" style="display: none; visibility: hidden;"></div>
    <iframe style="display: none" src="/page_for_cache"></iframe>
</body>
</html>
