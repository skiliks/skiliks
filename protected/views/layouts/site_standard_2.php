<!-- standard -->
<?php

$cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();

$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-1.10.2.js');
$cs->registerScriptFile($assetsUrl . "/js/jquery/jquery.browser.js");
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-migrate-1.1.1.min.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery.selectbox-0.2.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.jeditable.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.cookies.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-ui-1.8.24.custom.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.tablesorter.js', CClientScript::POS_BEGIN);

$cs->registerCoreScript('jquery.yiiactiveform.js');

$cs->registerScriptFile($assetsUrl . '/js/niceCheckbox.js');
$cs->registerScriptFile($assetsUrl . '/js/d3-master/d3.v3.js');
$cs->registerScriptFile($assetsUrl . '/js/charts.js');

$cs->registerScriptFile($assetsUrl . '/js/main.js');

$cs->registerCssFile($assetsUrl . '/js/jquery/jquery-ui.css');

$cs->registerCssFile($assetsUrl . "/css/site/reset.css");
$cs->registerCssFile($assetsUrl . "/css/site/grid.css");
$cs->registerCssFile($assetsUrl . "/css/site/styles_size_dependent.css");
$cs->registerCssFile($assetsUrl . "/css/site/styles_size_independent.css");

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

<body class="<?php echo StaticSiteTools::getBodyClass(Yii::app()->request) ?>">

<!-- HEADER { -->

<header class="main-content">
    <!-- ACCOUNTS LINKS -->
    <nav class="full-column inline-list pull-content-right account-links">
        <?php $this->renderPartial('//global_partials/_account_links', [
            'isDisplayAccountLinks' => true
        ]) ?>
    </nav>

    <!-- SITE PAGES NAVIGATION -->
    <nav class="full-column inline-list pull-content-right static-page-links">
        <a href="/" class="inline-block pull-left">
            <img src="<?php echo $assetsUrl?>/img/site/logotypes/logo-head.png" alt="Skiliks"/>
        </a>
        <?php $this->renderPartial('//global_partials/_static_pages_links') ?>
    </nav>
</header>

<!-- HEADER } -->

<section class="main-content full-column">
    <?php echo $content; ?>
</section>

<!-- FOOTER { -->

<footer class="main-content">
    <div class="footer-clear-fix full-column"></div>

    <!-- SOCIAL SHARE -->
    <div class="social-networks-share-links full-column pull-content-right">
        <?php $this->renderPartial('//global_partials/social_networks_share_links', ['force' => true]) ?>
        <a href="#top" class="inline-block text-smallest link-to-top"><?php echo Yii::t('site', 'Back to top') ?></a>
    </div>

    <!-- SITE PAGES NAVIGATION -->
    <nav class="full-column inline-list pull-content-right static-page-links">
        <a href="/" class="inline-block pull-left logo-footer-link footer-logo">
            <img src="<?php echo $assetsUrl?>/img/site/logotypes/logo-footer.png" alt="Skiliks"/>
        </a>
        <?php $this->renderPartial('//global_partials/_static_pages_links', [
            'isDisplayAccountLinks' => false,
            'disableDemo' => true
        ]) ?>
    </nav>

    <!-- COPYRIGHT -->
    <div class="copyright-box full-column pull-content-center">
        <span class="copyright">Copyright - Skiliks  - 2013</span>
        <?php if ('ru' == Yii::app()->getlanguage()): ?>
            <span  class="help-email-link">
                Свяжитесь с нами: <a href="mailto:help@skiliks.com">help@skiliks.com</a>
            </span>
        <?php endif; ?>
    </div>
</footer>

<script type="text/javascript">
    alert('111');
</script>

<!-- FOOTER } -->

<?php // $this->renderPartial('//global_partials/_feedback', []) ?>
<?php // $this->renderPartial('//global_partials/_google_analytics') ?>

</body>
</html>
