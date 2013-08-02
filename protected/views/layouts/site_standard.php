<!-- standard -->
<?php

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
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.cookies.js');
$cs->registerScriptFile($assetsUrl . '/js/d3-master/d3.v3.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-ui-1.8.24.custom.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile($assetsUrl . '/js/cufon-yui.js');
$cs->registerScriptFile($assetsUrl . '/js/ProximaNova.font.js');
$cs->registerScriptFile($assetsUrl . '/js/for_standard_site.js');
$cs->registerScriptFile($assetsUrl . '/js/charts.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.tablesorter.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-ui-1.9.2.custom.js', CClientScript::POS_BEGIN);
$cs->registerCssFile($assetsUrl . '/js/jquery/jquery-ui.css');

$cs->registerCssFile($assetsUrl . "/css/static.css");
?>

<!DOCTYPE html>
<html lang="<?php echo Yii::t('site', 'en') ?>">
	<head>
        <meta property="og:image" content="<?php echo $assetsUrl?>/img/skiliks-fb.png"/>
        <meta charset="utf-8" />
        <meta name="description" content="Самый простой и надежный способ проверить навыки менеджеров:
        деловая онлайн симуляция, имитирующая реальный рабочий день с типичными управленческими задачами
        и ситуациями принятия решений">
        <meta property="og:description" content="Самый простой и надежный способ проверить навыки менеджеров:
        деловая онлайн симуляция, имитирующая реальный рабочий день с типичными управленческими задачами
        и ситуациями принятия решений">
        <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
        <title>Skiliks - game the skills</title>

		<!--[if IE]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
    </head>
    
    <body class="<?php echo StaticSiteTools::getBodyClass(Yii::app()->request) ?>">

    <div class="<?php echo StaticSiteTools::getContainerClass(Yii::app()->request) ?> site-wrap" id="top">
        <header class="site-header grid-container">
            <h1><a href="./"><img id="header-main-logo"
                         src="<?php echo $assetsUrl?>/img/logo/logo-header-1280.png"
                         data-src-big="<?php echo $assetsUrl?>/img/logo/logo-header-1024.png"
                         data-src-small="<?php echo $assetsUrl?>/img/logo/logo-header-1280.png"
                         alt="Skiliks"/></a></h1>
            <nav class="menu-site menu-top" id="static-page-links">
                <?php $this->renderPartial('//global_partials/_static_pages_links') ?>
            </nav>

            <nav class="menu-site menu-main" id="account-links">
                <?php $this->renderPartial('//global_partials/_account_links', [
                    'isDisplayAccountLinks' => true
                ]) ?>
            </nav>
            <div class="betaflag"></div>
        </header>
        <?php $this->renderPartial('//global_partials/_sing_in') ?>
        <?php if (Yii::app()->getController()->getId() == 'static/pages' &&
            in_array(Yii::app()->getController()->getAction()->getId(), ['index', 'comingSoonSuccess'])): ?>
            <p class="heroes-comment right"><?php echo Yii::t('site', '&quot;Remarkably comprehensive<br />&nbsp;and deep assessment of<br />&nbsp;&nbsp;&nbsp;skills - now I know what<br />&nbsp;&nbsp;I can expect from<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;newcomers&quot;') ?></p>
            <p class="heroes-comment left"><?php echo Yii::t('site', '&quot;It&lsquo;s a real game with<br />&nbsp;great challenge and high<br />&nbsp;&nbsp;&nbsp;&nbsp;immersion - I haven&lsquo;t even<br />&nbsp;&nbsp;noticed how the time<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;passed by&quot;') ?></p>
        <?php endif; ?>

        <div class="site-main grid-container">
            <!-- flash-messages { -->
            <?php $flashes = Yii::app()->user->getFlashes(); ?>
            <?php if (0 < count($flashes)): ?>
                <div class="flash">
                    <a class="popupclose"></a>
                    <?php foreach($flashes as $key => $message) : ?>
                        <div class="flash-data flash-<?php echo $key ?>" data-key="<?php echo $key ?>""><?php echo $message ?></div>
                    <?php endforeach ?>
                </div>
            <?php endif; ?>

            <?php if (0 < count($flashes)): ?>
                <script type="text/javascript">
                    $('.flash').each(function() {
                        var key = $(this).find('.flash-data').attr('data-key');
                        console.log('key: ', key, $(this).find('.flash-data'));

                        var positionData = {
                            my: "center top",
                            at: "center top",
                            of: $('#top header')
                        };

                        // fix pop-up position for PasswordRecoveryMessage {
                        var isPasswordRecoveryMessagePresent = false;

                        <?php foreach($flashes as $key => $message) : ?>
                        <?php if ('popup-recovery-view' == $key): ?>
                        isPasswordRecoveryMessagePresent = true;
                        <?php endif ?>
                        <?php endforeach ?>

                        if (isPasswordRecoveryMessagePresent) {
                            positionData = {
                                my: "right top",
                                at: "right top",
                                of: $('#top header #static-page-links')
                            };
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
                            width: 275,
                            open: function( event, ui ) { Cufon.refresh(); }
                        });
                    });
                    $('.flash-message-popup .ui-dialog-titlebar').remove();
                    //$('.flash-pop-up').addClass('transparent-boder errorblock');
                    //$('.flash-pop-up div.flash').addClass('radiusthree backgroud-light-blue');
                    $('.sing-in-pop-up').dialog('open');

                    $('.flash-message-popup .popupclose').click(function() {
                        console.log('click');
                        $('.flash').dialog('close');
                    });
                </script>
            <?php endif; ?>

            <!-- flash-messages } -->
            <?php echo $content; ?>
        </div>
        <div class="empty-footer"></div>
        <footer class="site-footer">
            <div class="grid-container">
                <div class="container-3 container">
                    <div class="grid1">
                        <a href="/" class="brand-footer">
                            <img id="footer-main-logo"
                                 src="<?php echo $assetsUrl?>/img/logo/logo-footer.png"
                                 data-src-big="<?php echo $assetsUrl?>/img/logo/logo-footer-1280.png"
                                 data-src-small="<?php echo $assetsUrl?>/img/logo/logo-footer-1024.png"
                                 alt="Skiliks" title="Skiliks"/>
                        </a>
                    </div>
                    <div class="grid2">
                        <nav class="menu-site menu-botm" id="footer-menu">
                            <?php $this->renderPartial('//global_partials/_account_links', [
                                'isDisplayAccountLinks' => false
                            ]) ?>
                        </nav>
                    </div>
                </div>
                <div class="container-3 container proxima-reg font-small">
                    <div class="grid1 empty-block">.</div><div class="grid1 text-center">Copyright - Skiliks  - 2012</div><div class="grid1 text-right"><?php if ('ru' == Yii::app()->getlanguage()): ?><span class="help-contact-us">Свяжитесь с нами: <a href="mailto:help@skiliks.com">help@skiliks.com</a></span><?php endif; ?></div>
                </div>
                <a href="#top" class="to-top font-small"><?php echo Yii::t('site', 'Back to top') ?></a>
                <?php $route = Yii::app()->getController()->getRoute(); ?>
                <?php if (($route == 'static/pages/index' || $route == 'static/pages/homeNew') && 'ru' == Yii::app()->getlanguage()): ?>
                    <a href="/registration" class="btn btn-white btn-arrow-small access-footer"><?php echo Yii::t('site', 'Start using it now for free') ?></a>
                <?php endif ?>
                <div class="social_networks">
                    <div class="addthis_toolbox addthis_default_style addthis_32x32_style" addthis:url="http://player.vimeo.com/video/61279471" addthis:title="Skiliks - game the skills" addthis:description="Самый простой и надежный способ проверить навыки менеджеров: деловая онлайн симуляция, имитирующая реальный рабочий день с типичными управленческими задачами и ситуациями принятия решений">
                        <a class="addthis_button_vk at300b" target="_blank" title="Vk" href="#"><span class=" at300bs at15nc at15t_vk"><span class="at_a11y"></span></span></a>
                        <a class="addthis_button_facebook at300b" title="Facebook" href="#"><span class=" at300bs at15nc at15t_facebook"><span class="at_a11y"></span></span></a>
                        <a class="addthis_button_twitter at300b" title="Tweet" href="#"><span class=" at300bs at15nc at15t_twitter"><span class="at_a11y"></span></span></a>
                        <a class="addthis_button_google_plusone_share at300b" g:plusone:count="false" href="http://www.addthis.com/bookmark.php?v=300&amp;winname=addthis&amp;pub=ra-5158c9c22198d938&amp;source=tbx32-300&amp;lng=en-US&amp;s=google_plusone_share&amp;url=http%3A%2F%2Fplayer.vimeo.com%2Fvideo%2F61279471&amp;title=Skiliks%20-%20game%20the%20skills&amp;ate=AT-ra-5158c9c22198d938/-/-/51c092cc6369ed49/2&amp;frommenu=1&amp;uid=51c092cc3f68d12d&amp;description=%D0%A1%D0%B0%D0%BC%D1%8B%D0%B9%20%D0%BF%D1%80%D0%BE%D1%81%D1%82%D0%BE%D0%B9%20%D0%B8%20%D0%BD%D0%B0%D0%B4%D0%B5%D0%B6%D0%BD%D1%8B%D0%B9%20%D1%81%D0%BF%D0%BE%D1%81%D0%BE%D0%B1%20%D0%BF%D1%80%D0%BE%D0%B2%D0%B5%D1%80%D0%B8%D1%82%D1%8C%20%D0%BD%D0%B0%D0%B2%D1%8B%D0%BA%D0%B8%20%D0%BC%D0%B5%D0%BD%D0%B5%D0%B4%D0%B6%D0%B5%D1%80%D0%BE%D0%B2%3A%20%D0%B4%D0%B5%D0%BB%D0%BE%D0%B2%D0%B0%D1%8F%20%D0%BE%D0%BD%D0%BB%D0%B0%D0%B9%D0%BD%20%D1%81%D0%B8%D0%BC%D1%83%D0%BB%D1%8F%D1%86%D0%B8%D1%8F%2C%20%D0%B8%D0%BC%D0%B8%D1%82%D0%B8%D1%80%D1%83%D1%8E%D1%89%D0%B0%D1%8F%20%D1%80%D0%B5%D0%B0%D0%BB%D1%8C%D0%BD%D1%8B%D0%B9%20%D1%80%D0%B0%D0%B1%D0%BE%D1%87%D0%B8%D0%B9%20%D0%B4%D0%B5%D0%BD%D1%8C%20%D1%81%20%D1%82%D0%B8%D0%BF%D0%B8%D1%87%D0%BD%D1%8B%D0%BC%D0%B8%20%D1%83%D0%BF%D1%80%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%BC%D0%B8%20%D0%B7%D0%B0%D0%B4%D0%B0%D1%87%D0%B0%D0%BC%D0%B8%20%D0%B8%20%D1%81%D0%B8%D1%82%D1%83%D0%B0%D1%86%D0%B8%D1%8F%D0%BC%D0%B8%20%D0%BF%D1%80%D0%B8%D0%BD%D1%8F%D1%82%D0%B8%D1%8F%20%D1%80%D0%B5%D1%88%D0%B5%D0%BD%D0%B8%D0%B9&amp;ct=1&amp;pre=http%3A%2F%2Flive.skiliks.com%2F&amp;tt=0&amp;captcha_provider=nucaptcha" target="_blank" title="Google+"><span class=" at300bs at15nc at15t_google_plusone_share"><span class="at_a11y"></span></span></a>
                        <a class="addthis_button_linkedin at300b" href="http://www.addthis.com/bookmark.php?v=300&amp;winname=addthis&amp;pub=ra-5158c9c22198d938&amp;source=tbx32-300&amp;lng=en-US&amp;s=linkedin&amp;url=http%3A%2F%2Fplayer.vimeo.com%2Fvideo%2F61279471&amp;title=Skiliks%20-%20game%20the%20skills&amp;ate=AT-ra-5158c9c22198d938/-/-/51c092cc6369ed49/3&amp;frommenu=1&amp;uid=51c092ccbe7e2162&amp;description=%D0%A1%D0%B0%D0%BC%D1%8B%D0%B9%20%D0%BF%D1%80%D0%BE%D1%81%D1%82%D0%BE%D0%B9%20%D0%B8%20%D0%BD%D0%B0%D0%B4%D0%B5%D0%B6%D0%BD%D1%8B%D0%B9%20%D1%81%D0%BF%D0%BE%D1%81%D0%BE%D0%B1%20%D0%BF%D1%80%D0%BE%D0%B2%D0%B5%D1%80%D0%B8%D1%82%D1%8C%20%D0%BD%D0%B0%D0%B2%D1%8B%D0%BA%D0%B8%20%D0%BC%D0%B5%D0%BD%D0%B5%D0%B4%D0%B6%D0%B5%D1%80%D0%BE%D0%B2%3A%20%D0%B4%D0%B5%D0%BB%D0%BE%D0%B2%D0%B0%D1%8F%20%D0%BE%D0%BD%D0%BB%D0%B0%D0%B9%D0%BD%20%D1%81%D0%B8%D0%BC%D1%83%D0%BB%D1%8F%D1%86%D0%B8%D1%8F%2C%20%D0%B8%D0%BC%D0%B8%D1%82%D0%B8%D1%80%D1%83%D1%8E%D1%89%D0%B0%D1%8F%20%D1%80%D0%B5%D0%B0%D0%BB%D1%8C%D0%BD%D1%8B%D0%B9%20%D1%80%D0%B0%D0%B1%D0%BE%D1%87%D0%B8%D0%B9%20%D0%B4%D0%B5%D0%BD%D1%8C%20%D1%81%20%D1%82%D0%B8%D0%BF%D0%B8%D1%87%D0%BD%D1%8B%D0%BC%D0%B8%20%D1%83%D0%BF%D1%80%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%BC%D0%B8%20%D0%B7%D0%B0%D0%B4%D0%B0%D1%87%D0%B0%D0%BC%D0%B8%20%D0%B8%20%D1%81%D0%B8%D1%82%D1%83%D0%B0%D1%86%D0%B8%D1%8F%D0%BC%D0%B8%20%D0%BF%D1%80%D0%B8%D0%BD%D1%8F%D1%82%D0%B8%D1%8F%20%D1%80%D0%B5%D1%88%D0%B5%D0%BD%D0%B8%D0%B9&amp;ct=1&amp;pre=http%3A%2F%2Flive.skiliks.com%2F&amp;tt=0&amp;captcha_provider=nucaptcha" target="_blank" title="Linkedin"><span class=" at300bs at15nc at15t_linkedin"><span class="at_a11y"></span></span></a>
                    </div><span class="proxima-bold font-dark"><?php echo Yii::t('site', 'Share') ?></span>
                </div><!-- /social_networks -->
            </div><!-- /grid-container -->
        </footer>
    </div><!-- /site-wrap -->

    <?php $this->renderPartial('//global_partials/_feedback', []) ?>
    <script type="text/javascript">
        Cufon.replace('.menu-site, .unstyled, .inner .site-main p, span, label, input, select, sup, .proxima-reg, .sbHolder a, .errorMessage, .feedback-dialog-title, .to-top, small, .team-values, ' +
            '.action-productNew p, .action-productNew div, .tariff-header p, .method-description, .order-page header, .method-description' +
            '.action-teamNew p', {fontFamily:"ProximaNova-Regular", hover: true});
        Cufon.replace('.btn, .proxima-bold, h1, h2, h3, h4, h5, h6, strong, .dark-labels label, .list-dark li, .items th, .items td, .add-vacancy-popup h1, .ui-dialog-title, ' +
            '.side-menu .active a, .action-tariffsNew .proxima-bold', {fontFamily:"ProximaNova-Bold", hover: true});
        Cufon.replace('.semi, .yiiPager', {fontFamily:"ProximaNova-Semibold", hover: true});
        Cufon.replace('.feedback-dialog-title, .vacancy-list, .font-normal', {fontFamily:"ProximaNova-Regular", hover: true});
        Cufon.now();
    </script>
    </body>
</html>
