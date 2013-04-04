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
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-1.9.1.min.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-migrate-1.1.1.min.js');
$cs->registerScriptFile($assetsUrl . '/js/niceCheckbox.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery.selectbox-0.2.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.jeditable.js');
$cs->registerScriptFile($assetsUrl . '/js/main.js');
$cs->registerScriptFile($assetsUrl . '/js/charts.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-ui-1.8.24.custom.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.tablesorter.js', CClientScript::POS_BEGIN);

$cs->registerCssFile($assetsUrl . '/js/jquery/jquery-ui.css');;
$cs->registerCssFile($assetsUrl . "/css/style.css");
?>

<!DOCTYPE html>
<html lang="<?php echo Yii::t('site', 'en') ?>">
    <script src="http://cdn.jotfor.ms/static/feedback2.js?3.1.2591" type="text/javascript">
        new JotformFeedback({
            formId:'30934004754349',
            base:'http://jotformeu.com/',
            windowTitle:'Обратная связь',
            background:'#2D8694',
            fontColor:'#ffffff',
            type:false,
            height:500,
            width:700
        });
    </script>

	<head>
		<meta charset="utf-8" />
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
				<h1><a href="/">Skiliks</a></h1>
				
				<p class="coming-soon"><?php echo Yii::t('site', 'Coming soon') ?></p>

                <?php $this->renderPartial('//layouts/_language_switcher') ?>

				<nav id="static-page-links">
                    <?php $this->renderPartial('//layouts/_static_pages_links') ?>
				</nav>

                <br/>
                <br/>

				<nav id="account-links">
                    <?php $this->renderPartial('//layouts/_account_links') ?>
				</nav>
			</header>
			<!--header end-->

            <!-- sing in { -->
            <?php $this->renderPartial('//layouts/_sing_in') ?>

			<?php if (in_array(Yii::app()->request->getPathInfo(), ['', 'static/comingSoonSuccess'])): ?>
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
                                of: $('#top header')
                            },
                            resizable: false,
                            title: '',
                            width: 560
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
		</div>

		<!--footer-->
		<div class="footer">
			<footer>
				<div class="backtotop"><a href="#top"><?php echo Yii::t('site', 'Back to top') ?></a></div>

				<div class="logo"><a href="/">Skiliks</a></div>

                <?php $this->renderPartial('//layouts/addthis') ?>

				<nav>
					<a href="../"><?php echo Yii::t('site', 'Home') ?></a>
					<a href="/static/team/"><?php echo Yii::t('site', 'About Us') ?></a>
					<a href="/static/product/"><?php echo Yii::t('site', 'Product') ?></a>
				</nav>

				<p class="copyright">Copyright - Skiliks  - 2012 <a href="#" class="lightbox-30934004754349">Feedback</a></p>
            </footer>
		</div>
		<!--footer end-->
	</body>
</html>
