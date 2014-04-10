<?php

$cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();

$cs->scriptMap=array(
    'jquery.js'        => $assetsUrl . '/js/site/jquery/jquery-1.10.2.js',
    'jquery-min.js'    => false,
    'jquery.ba-bbq.js' => false, /* не обновляется с 2010 года! @link http://benalman.com/code/projects/jquery-bbq/docs/files/jquery-ba-bbq-js.html */
    'jquery.yiilistview.js' => false
);

$cs->registerCoreScript('jquery');
$cs->registerScriptFile($assetsUrl . "/js/jquery/jquery.browser.js");
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-migrate-1.1.1.min.js');
$cs->registerCoreScript('jquery.yiiactiveform.js');
$cs->registerScriptFile($assetsUrl . '/js/niceCheckbox.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery.selectbox-0.2.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.jeditable.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.cookies.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.textchange.js');
$cs->registerScriptFile($assetsUrl . '/js/d3-master/d3.v3.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-ui-1.8.24.custom.js', CClientScript::POS_BEGIN);
//$cs->registerScriptFile($assetsUrl . '/js/cufon-yui.js');
//$cs->registerScriptFile($assetsUrl . '/js/ProximaNova_old.font.js');
$cs->registerScriptFile($assetsUrl . '/js/main.js');
$cs->registerScriptFile($assetsUrl . '/js/charts.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.tablesorter.js', CClientScript::POS_BEGIN);
$cs->registerCssFile($assetsUrl . '/js/jquery/jquery-ui.css');
$cs->registerCssFile($assetsUrl . "/css/style.css");
$cs->registerCssFile($assetsUrl . "/css/popover.css");

/**
 * Подключаем ie10.css для специфичной IE10 вёрстки.
 * Все прочие (общие) CSS должны быть подключены ниже.
 * @link:http://stackoverflow.com/questions/16474948/detect-ie10-ie10-and-other-browsers-in-php
 */
if(preg_match('/(?i)msie [10]/',$_SERVER['HTTP_USER_AGENT']))
{
    $cs->registerCssFile($assetsUrl . "/css/site_ie10.css");
}

if(preg_match('/(?i)Chrome/',$_SERVER['HTTP_USER_AGENT']))
{
    $cs->registerCssFile($assetsUrl . "/css/site_chrome.css");
}

if(preg_match('/(?i)Firefox/',$_SERVER['HTTP_USER_AGENT']))
{
    $cs->registerCssFile($assetsUrl . "/css/site_firefox.css");
}

?>
<!DOCTYPE html>
<html lang="<?php echo Yii::t('site', 'en') ?>">
	<head>
        <?php if(Yii::app()->controller->action->id == "watchVideo") : ?>
            <meta property="og:image" content="http://<?=$_SERVER['HTTP_HOST']; ?>/<?=$assetsUrl?>/img/videoscreen.jpg"/>
        <?php else : ?>
            <meta property="og:image" content="http://<?=$_SERVER['HTTP_HOST']; ?>/<?=$assetsUrl?>/img/square-logo.jpg"/>
        <?php endif ?>

        <meta property="og:title" content="Skiliks – game the skills"/>
        <meta property="og:url" content="http://<?=$_SERVER['HTTP_HOST']; ?>"/>
        <meta charset="utf-8" />
        <meta name="description" content="<?= Yii::t('site', 'www.skiliks.com - online simulation aimed at testing management skills') ?>">
        <meta property="og:description" content="<?= Yii::t('site', 'www.skiliks.com - online simulation aimed at testing management skills') ?>">
        <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
        <title>Skiliks - game the skills</title>

		<!--[if IE]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
    </head>

    <body class="<?php echo StaticSiteTools::getBodyClass(Yii::app()->request) ?>">

    <script type="text/javascript">
        var assetsUrl = '<?= $assetsUrl ?>';
    </script>

        <div class="<?php echo StaticSiteTools::getContainerClass(Yii::app()->request) ?>" id="top">
			
			<!--header SC -->
			<header>
				<h1>
                    <a href="/">
                        <img src="<?php echo $assetsUrl?>/img/logo-head.png" alt="Skiliks"/>
                    </a>
                </h1>

                <nav id="account-links">
                    <?php $this->renderPartial('//global_partials/_static_pages_links',[
                        'isDisplayAccountLinks' => true
                    ]) ?>
                </nav>
				<nav id="static-page-links">
                    <?php $this->renderPartial('//global_partials/_account_links') ?>
				</nav>

                <br/>
                <br/>

			</header>
			<!--header end-->

            <!-- sing in { -->
            <?php $this->renderPartial('//global_partials/_sing_in') ?>

			<?php if (Yii::app()->getController()->getId() == 'static/pages' &&
                in_array(Yii::app()->getController()->getAction()->getId(), ['index', 'comingSoonSuccess'])): ?>
                <p class="heroes-comment right"><?php echo Yii::t('site', '&quot;Remarkably comprehensive<br />&nbsp;and deep assessment of<br />&nbsp;&nbsp;&nbsp;skills - now I know what<br />&nbsp;&nbsp;I can expect from<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;newcomers&quot;') ?></p>
                <p class="heroes-comment left"><?php echo Yii::t('site', '&quot;It&lsquo;s a real game with<br />&nbsp;great challenge and high<br />&nbsp;&nbsp;&nbsp;&nbsp;immersion - I haven&lsquo;t even<br />&nbsp;&nbsp;noticed how the time<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;passed by&quot;') ?></p>
            <?php endif; ?>

			<!--content-->
			<div class="content">
                <!-- flash-messages { -->
                <?php $flashes = Yii::app()->user->getFlashes(); ?>
                <?php if (0 < count($flashes)): ?>
                    <div class="flash">
                        <a class="popupclose"></a>
                        <?php foreach($flashes as $key => $message) : ?>
                             <div class="flash-data flash-<?php echo $key ?>" data-key="<?php echo $key ?>"">
                                 <?php echo $message ?>
                             </div>
                        <?php endforeach ?>
                    </div>
                <?php endif; ?>

                <?php if (0 < count($flashes)): ?>
                    <script type="text/javascript">
                        $('.flash').each(function() {
                            var key = $(this).find('.flash-data').attr('data-key');

                            var positionData = {
                                my: "center top",
                                at: "center top",
                                of: $('#top header')
                            };

                            var  widthData = 550;

                            if ($(window).width() < 1281) {
                                var  widthData = 450
                            }

                            // fix pop-up position for PasswordRecoveryMessage {
                            var isPasswordRecoveryMessagePresent = false;

                            <?php foreach($flashes as $key => $message) : ?>
                                <?php if ('recovery-popup' == $key): ?>
                            isPasswordRecoveryMessagePresent = true;
                                <?php endif ?>
                            <?php endforeach ?>

                            if (isPasswordRecoveryMessagePresent) {
                                positionData = {
                                    my: "right top",
                                    at: "right top",
                                    of: $('#top header #static-page-links')
                                };
                                widthData = 274;
                            }

                            // fix pop-up position for PasswordRecoveryMessage }

                            $(this).dialog({
                                closeOnEscape: true,
                                dialogClass: "flash-message-popup " + "flash-message-popup-" + key+" popup-center",
                                minHeight: 220,
                                modal: true,
                                resizable: false,
                                position: positionData,
                                //title: '',
                                width: widthData,
                                open: function( event, ui ) { /*Cufon.refresh();*/ }
                            });
                        });
                        $('.flash-message-popup .ui-dialog-titlebar').remove();
                        $('.sing-in-pop-up').dialog('open');

                        $('.flash-message-popup .popupclose').click(function() {
                            $('.flash').dialog('close');
                        });
                    </script>
                <?php endif; ?>

                <!-- flash-messages } -->

			    <?php echo $content; ?>
            </div>
			<!--content end-->
            <div class="empty-footer"></div>
            <!--footer-->
            <div class="footer">
                <footer>
                    <?php $this->renderPartial('//global_partials/addthis', ['force' => false]) ?>
                    <div class="backtotop"><a href="#top"><?php echo Yii::t('site', 'Back to top') ?></a></div>
                    <div class="logo"><a href="/">Skiliks</a></div>
                    <p class="copyright">Copyright - Skiliks  - 2014</p>
                    <?php if ('ru' == Yii::app()->getlanguage()): ?>
                        <span class="help-contact-us">
                            Свяжитесь с нами: <a href="mailto:support@skiliks.com">support@skiliks.com</a>
                        </span>
                    <?php endif; ?>
                    <?php $route = Yii::app()->getController()->getRoute(); ?>
                    <?php if (($route == 'static/pages/index' || $route == 'static/pages/homeNew') && 'ru' == Yii::app()->getlanguage()): ?>
                        <a href="/registration" class="bigbtnsubmt freeacess"><?php echo Yii::t('site', 'Start using it now for free') ?></a>
                    <?php endif ?>
                <nav id="footer-menu">
                    <?php $this->renderPartial('//global_partials/_static_pages_links', [
                        'isDisplayAccountLinks' => false,
                        'disableDemo' => true
                    ]) ?>
                </nav>
            </div>

        <?php if (Yii::app()->params['public']['isDisplaySupportChat']) : ?>
            <script type="text/javascript">
                window._shcp = [];
                window._shcp.push({
                    link_wrap_off: true, widget_id :<?= Yii::app()->params['public']['SiteHeartWidgetCode'] ?>,
                    widget : "Chat",
                    side : "right",
                    position : "top",
                    template : "blue",
                    title : "<?= Yii::app()->params['public']['SiteHeartWidgetTitle'] ?>",
                    title_offline : "Оставьте сообщение",
                     auth : "<?= StaticSiteTools::getSiteHeartAuth(Yii::app()->user->data()); ?>"
                });
                $(document).ready(function() {
                    var hcc = document.createElement("script");
                    hcc.type = "text/javascript";
                    hcc.async = true;
                    hcc.src = ("https:" === document.location.protocol ? "https" : "http")+"://widget.siteheart.com/apps/js/sh.js?v=2";
                    var s = document.head;
                    s.parentNode.insertBefore(hcc, null);
                });
            </script>
        <?php endif; ?>

        <?php $this->renderPartial('//global_partials/_feedback', []) ?>
        <?php $this->renderPartial('//global_partials/_before_start_lite_simulation_popup', []) ?>
        <?php $this->renderPartial('//global_partials/_google_analytics') ?>
    </body>
</html>
