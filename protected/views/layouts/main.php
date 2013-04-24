<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 05.02.13
 * Time: 12:14
 * To change this template use File | Settings | File Templates.
 */
$cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();
$cs->scriptMap=array(
    'jquery.js'=>$assetsUrl . '/js/jquery/jquery-1.9.1.min.js',
    'jquery.min.js'=>$assetsUrl . '/js/jquery/jquery-1.9.1.min.js',
    'jquery.ba-bbq.js'=>$assetsUrl . '/js/jquery/jquery.ba-bbq.js',
);
$cs->registerCoreScript('jquery');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-migrate-1.1.1.min.js');
$cs->registerCoreScript('jquery.yiiactiveform.js');
$cs->registerScriptFile($assetsUrl . '/js/niceCheckbox.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery.selectbox-0.2.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.jeditable.js');
$cs->registerScriptFile($assetsUrl . '/js/d3-master/d3.v3.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-ui-1.8.24.custom.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile($assetsUrl . '/js/cufon-yui.js');
$cs->registerScriptFile($assetsUrl . '/js/ProximaNova.font.js');
$cs->registerScriptFile($assetsUrl . '/js/main.js');
$cs->registerScriptFile($assetsUrl . '/js/charts.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.tablesorter.js', CClientScript::POS_BEGIN);
$cs->registerCssFile($assetsUrl . '/js/jquery/jquery-ui.css');
$cs->registerCssFile($assetsUrl . "/css/style.css");
?>

<!DOCTYPE html>
<html lang="<?php echo Yii::t('site', 'en') ?>">
	<head>
        <meta property="og:image" content="<?php echo $assetsUrl?>/img/skiliks-fb.png"/>
		<meta charset="utf-8" />
        <meta name="description" content="Самый простой и надежный способ проверить навыки менеджеров:
        деловая онлайн симуляция, имитирующая реальный рабочий день с типичными управленческими задачами
        и ситуациями принятия решений">
        <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
        <title>Skiliks - game the skills</title>

		<!--[if IE]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
    </head>
    
    <body class="<?php echo StaticSiteTools::getBodyClass(Yii::app()->request) ?>">

		<div class="<?php echo StaticSiteTools::getContainerClass(Yii::app()->request) ?>" id="top">
			
			<!--header-->
			<header>
				<h1>
                    <a href="/">
                        <img src="<?php echo $assetsUrl?>/img/logo-header.png" alt="Skiliks"/>
                    </a>
                </h1>


                <nav id="account-links">
                    <?php $this->renderPartial('//layouts/_account_links') ?>
                </nav>
				<nav id="static-page-links">
                    <?php $this->renderPartial('//layouts/_static_pages_links') ?>
				</nav>

                <br/>
                <br/>

			</header>
			<!--header end-->

            <!-- sing in { -->
            <?php $this->renderPartial('//layouts/_sing_in') ?>

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
                             <div class="flash-<?php echo $key ?>">
                                 <?php echo $message ?>
                             </div>
                        <?php endforeach ?>
                    </div>
                <?php endif; ?>

                <?php if (0 < count($flashes)): ?>
                    <script type="text/javascript">
                        $('.flash').dialog({
                            closeOnEscape: true,
                            dialogClass: 'flash-pop-up',
                            modal: true,
                            height: 280,
                            position: {
                                my: "right top",
                                at: "middle bottom",
                                of: $('#top').find('header')
                            },
                            resizable: false,
                            title: '',
                            width: 560,
                            open: function( event, ui ) { Cufon.refresh(); }
                        });
                        $('.flash-pop-up .ui-dialog-titlebar').remove();
                        $('.flash-pop-up').addClass('transparent-boder errorblock');
                        $('.flash-pop-up div.flash').addClass('radiusthree backgroud-light-blue');
                        // $('.flash-pop-up').dialog('open');

                        $('.flash .popupclose').click(function() {
                            console.log('click');
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
                    <?php $this->renderPartial('//layouts/addthis', ['force' => false]) ?>
                    <div class="backtotop"><a href="#top"><?php echo Yii::t('site', 'Back to top') ?></a></div>
                    <div class="logo"><a href="/">Skiliks</a></div>


                    <?php if (Yii::app()->getController()->getRoute() == 'static/pages/index' && 'ru' == Yii::app()->getlanguage()): ?>
                        <a href="/registration" class="bigbtnsubmt freeacess"><?php echo Yii::t('site', 'Start using it now for free') ?></a>
                    <?php endif ?>
                <nav>
                    <a href="/"><?php echo Yii::t('site', 'Home') ?></a>
                    <a href="/static/team/"><?php echo Yii::t('site', 'About Us') ?></a>
                    <a href="/static/product/"><?php echo Yii::t('site', 'Product') ?></a>
                </nav>
            </div>
            <?php $this->renderPartial('//global_partials/_feedback', []) ?>
            <script type="text/javascript"> Cufon.now(); </script>
    </body>
</html>
