<!-- standard -->
<?php

$cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();

//$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-1.10.2.js');
//$cs->registerScriptFile($assetsUrl . "/js/jquery/jquery.browser.js");
//$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-migrate-1.1.1.min.js');
//$cs->registerScriptFile($assetsUrl . '/js/jquery.selectbox-0.2.js');
//$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.jeditable.js');
//$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.cookies.js');
//$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-ui-1.8.24.custom.js', CClientScript::POS_BEGIN);
//$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.tablesorter.js', CClientScript::POS_BEGIN);

//$cs->registerScriptFile($assetsUrl . '/js/site/jquery/jquery.tablesorter.js', CClientScript::POS_BEGIN);

//$cs->registerScriptFile($assetsUrl . '/js/niceCheckbox.js');
//$cs->registerScriptFile($assetsUrl . '/js/d3-master/d3.v3.js');
//$cs->registerScriptFile($assetsUrl . '/js/charts.js');

$cs->scriptMap=array(
    'jquery.js'        => $assetsUrl . '/js/site/jquery/jquery-1.10.2.js',
    'jquery-min.js'    => false,
    'jquery.ba-bbq.js' => false, /* не обновляется с 2010 года! @link http://benalman.com/code/projects/jquery-bbq/docs/files/jquery-ba-bbq-js.html */
    'jquery.yiilistview.js' => false
);

// если не регистрировать jQuery как CoreScript - то Yii AJAX формы не работают
// @link: http://stackoverflow.com/questions/14502018/using-yiis-ajax-validation-without-autoload-jquery
$cs->registerCoreScript('jquery');
$cs->registerCoreScript('jquery.yiiactiveform.js');
//$cs->registerScriptFile($assetsUrl . '/js/site/jquery/jquery-1.10.2.js');

/* fix:
 1. jquery .live() issue, @link: http://stackoverflow.com/questions/15573645/typeerror-live-is-not-a-function */
$cs->registerScriptFile($assetsUrl . '/js/site/jquery/plugins/jquery-migrate-1.1.1.min.js');

/* .dialog() */
$cs->registerScriptFile($assetsUrl . '/js/site/jquery/plugins/jquery-ui-1.8.24.custom.js', CClientScript::POS_BEGIN);

$cs->registerScriptFile($assetsUrl . "/js/site/jquery/plugins/jquery.browser.js");


$cs->registerScriptFile($assetsUrl . '/js/site/jquery/plugins/jquery.selectbox-0.2.js');

$cs->registerScriptFile($assetsUrl . '/js/site/common.js');

/* .dialog() */
$cs->registerCssFile($assetsUrl . '/js/jquery/jquery-ui.css');

$cs->registerCssFile($assetsUrl . "/css/site/reset.css");
$cs->registerCssFile($assetsUrl . "/css/site/reset-1024.css");
$cs->registerCssFile($assetsUrl . "/css/site/grid.css");
$cs->registerCssFile($assetsUrl . "/css/site/grid-1024.css");
$cs->registerCssFile($assetsUrl . "/css/site/sb-holder.css");
$cs->registerCssFile($assetsUrl . "/css/site/social-networks.css");
$cs->registerCssFile($assetsUrl . "/css/site/styles_size_independent.css");
$cs->registerCssFile($assetsUrl . "/css/site/styles-1280.css");
$cs->registerCssFile($assetsUrl . "/css/site/styles-1024.css");


?>

<!DOCTYPE html>
<html lang="<?php echo Yii::t('site', 'en') ?>">
<head>
    <meta property="og:image" content="<?php echo $assetsUrl?>/img/skiliks-fb.png"/>
    <meta charset="utf-8" />
    <meta name="description" content="Простой и надежный способ проверить навыки менеджеров:
        деловая онлайн симуляция, имитирующая реальный рабочий день с типичными управленческими задачами
        и ситуациями принятия решений">
    <meta property="og:description" content="Простой и надежный способ проверить навыки менеджеров:
        деловая онлайн симуляция, имитирующая реальный рабочий день с типичными управленческими задачами
        и ситуациями принятия решений">
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <title>Skiliks - game the skills</title>

    <!--[if IE]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>

<body class="skiliks <?php echo StaticSiteTools::getBodyClass(Yii::app()->request) ?>">
<section class="partial">
    <label class="partial-label"><?= __FILE__ ?></label>

    <!-- HEADER { -->

    <header class="main-content">
        <!-- ACCOUNTS LINKS -->
        <nav class="column-full inline-list pull-content-right account-links">
            <?php $this->renderPartial('//global_partials/_account_links') ?>
        </nav>

        <!-- SITE PAGES NAVIGATION -->
        <nav class="column-full inline-list pull-content-right static-page-links">
            <a href="/" class="inline-block pull-left">
                <img class="locator-logo-head"
                     src="<?php echo $assetsUrl?>/img/site/1280/logotypes/logo-head.png" alt="Skiliks"/>
            </a>
            <?php $this->renderPartial('//global_partials/_static_pages_links', [
                'isDisplayAccountLinks' => true
            ]) ?>
        </nav>
    </header>

    <!-- HEADER } -->

    <section class="main-content column-full mark-up-block">
        <label class="mark-up-label">#Content</label>
        <?php echo $content; ?>
    </section>

    <!-- FOOTER { -->

    <footer class="main-content mark-up-block">
        <label class="mark-up-label">#Footer</label>
        <div class="footer-clear-fix column-full"></div>

        <!-- SOCIAL SHARE -->
            <?php $this->renderPartial('//global_partials/_social_networks_share_links', ['force' => true]) ?>

        <!-- SITE PAGES NAVIGATION -->
        <nav class="column-full inline-list pull-content-right static-page-links">
            <a href="/" class="inline-block pull-left logo-footer-link footer-logo">
                <img src="<?php echo $assetsUrl?>/img/site/1280/logotypes/logo-footer.png" alt="Skiliks"/>
            </a>
            <?php $this->renderPartial('//global_partials/_static_pages_links', [
                'isDisplayAccountLinks' => false,
                'disableDemo'           => true
            ]) ?>
        </nav>

        <!-- COPYRIGHT -->
        <div class="copyright-box column-full pull-content-center mark-up-block">
            <span class="copyright">Copyright - Skiliks  - 2013</span>
            <?php if ('ru' == Yii::app()->getlanguage()): ?>
                <span  class="help-email-link">
                    Свяжитесь с нами: <a href="mailto:help@skiliks.com">help@skiliks.com</a>
                </span>
            <?php endif; ?>
        </div>
    </footer>

    <!-- FOOTER } -->

    <?php $this->renderPartial('//global_partials/_feedback', []) ?>
    <?php // $this->renderPartial('//global_partials/_google_analytics') ?>
    <?php $this->renderPartial('//global_partials/_before_start_lite_simulation_popup', []) ?>
</section>

<?php $this->renderPartial('//global_partials/_flash_messages', [
    'isDisplayAccountLinks' => true
]) ?>

</body>
</html>
