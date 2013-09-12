<?php

$cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();
$cs->scriptMap=array(
    'jquery.js'=>$assetsUrl . '/js/jquery/jquery-1.9.1.min.js',
    'jquery.min.js'=>$assetsUrl . '/js/jquery/jquery-1.9.1.min.js',
    'jquery.ba-bbq.js'=>$assetsUrl . '/js/jquery/jquery.ba-bbq.js',
);
$cs->registerCoreScript('jquery');
$cs->registerScriptFile($assetsUrl . "/js/jquery/jquery.browser.js");
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-migrate-1.1.1.min.js');
$cs->registerCoreScript('jquery.yiiactiveform.js');
$cs->registerScriptFile($assetsUrl . '/js/niceCheckbox.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery.selectbox-0.2.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.jeditable.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.cookies.js');
$cs->registerScriptFile($assetsUrl . '/js/d3-master/d3.v3.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-ui-1.8.24.custom.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile($assetsUrl . '/js/cufon-yui.js');
$cs->registerScriptFile($assetsUrl . '/js/ProximaNova_old.font.js');
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

		<div class="<?php echo StaticSiteTools::getContainerClass(Yii::app()->request) ?>" id="top">
			
			<!--header SC -->
			<header>
				<h1>
                    <a href="/">
                        <img src="<?php echo $assetsUrl?>/img/logo-head.png" alt="Skiliks"/>
                    </a>
                </h1>

                <nav id="account-links">
                    <?php $this->renderPartial('//global_partials/_account_links', [
                        'isDisplayAccountLinks' => true
                    ]) ?>
                </nav>
				<nav id="static-page-links">
                    <?php $this->renderPartial('//global_partials/_static_pages_links') ?>
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
                                open: function( event, ui ) { Cufon.refresh(); }
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
                    <p class="copyright">Copyright - Skiliks  - 2012</p>
                    <?php if ('ru' == Yii::app()->getlanguage()): ?>
                        <span class="help-contact-us">
                            Свяжитесь с нами: <a href="mailto:help@skiliks.com">help@skiliks.com</a>
                        </span>
                    <?php endif; ?>
                    <?php $route = Yii::app()->getController()->getRoute(); ?>
                    <?php if (($route == 'static/pages/index' || $route == 'static/pages/homeNew') && 'ru' == Yii::app()->getlanguage()): ?>
                        <a href="/registration" class="bigbtnsubmt freeacess"><?php echo Yii::t('site', 'Start using it now for free') ?></a>
                    <?php endif ?>
                <nav id="footer-menu">
                    <?php $this->renderPartial('//global_partials/_account_links', [
                        'isDisplayAccountLinks' => false
                    ]) ?>
                </nav>
            </div>
            <?php $this->renderPartial('//global_partials/_feedback', []) ?>
            <script type="text/javascript">
                Cufon.replace('.invite-people-form input[type="submit"], .brightblock, .lightblock, .benefits, .tarifname, ' +
                    '.clients h3, .main-article article h3, #simulation-details label, .features h2, .thetitle, .tarifswrap .text16, .sing-in-pop-up .ui-dialog-title, ' +
                    '.form-submit-button, .midtitle, .social_networks span, .main-article h3, .registration input[type=submit], ' +
                    '.registration .form h1, .registration .form li, .note, .product h2, .product section h3, .product section table td h6, .team article h2, ' +
                    '.team .team-list li h4, .team .team-values h3, .registration h2, .registrationform h3, .registration .form h1, .widthblock h3, .ratepercnt, .testtime strong, ' +
                    '.registration .form .row label, .register-by-link .row label, .regicon span, .register-by-link .row input[type=submit], ' +
                    '.login-form h6, .login-form div input[type=submit], .dashboard aside h2, .blue-btn, .vacancy-add-form-switcher, .items th, .items td, .pager ul.yiiPager .page a, ' +
                    '.vacancy-list .grid-view tr td:first-child, .features form div input[type=submit], .registrationform h3, ' +
                    '.icon-choose, .testtime, .testtime strong, .benefits, .tarifswrap .text16, .value, .tarifform .value, #simulations-counter-box strong, ' +
                    '.greenbtn, .cabmessage input[type="submit"], .cabmessage .ui-dialog-title, #send-invite-message-form label, .action-controller-login-auth #usercontent h2, ' +
                    '.action-controller-registerByLink-static-userAuth h2.title, #invite-decline-form #form-decline-explanation input[type="submit"], section.registration-by-link .form .row input[type="submit"],' +
                    '.action-controller-personal-static-simulations h1.title, .action-controller-corporate-static-simulations h1.title, .action-controller-corporate-static-simulations .grid-view table.items th,' +
                    '#password-recovery-form input[type="submit"], #simulation-details-pop-up h1, .estmtileswrap h2, .estmtileswrap h2 a, .product .estmtileswrap h2, .simulation-result-popup h3,' +
                    '.levellabels h3, .resulttitele, .resulttitele a, .barstitle, .total, .labeltitles h3, .labeltitles h4, .valuetitle, .resulttitele  small, .timedetail .thelabel,' +
                    '.feedback #input_2, .profileform input[type="submit"], .pager ul.yiiPager .next a, .pager ul.yiiPager .previous a, .product .ratepercnt, .light-btn' +
                    '.value, .tarifform .value, .light-btn, .terms-page h1, .terms-page h3, #error404-message, .browsers h2, .browsers span a, .btn-large, .accept-invite-warning-popup h2, ' +
                    '.list-ordered strong, .accept-invite-warning-popup h4',
                    {hover: true}
                );
                Cufon.replace('.proxima-regular, .main-article article ul li, .container>header nav a, .features ul li, .sbHolder a, #simulation-details label, .container>header nav a, .features .error span, ' +
                    '.features p.success, .flash-data, .flash-success, .flash-error, .product hgroup h6, .productfeatrs td, .product table p, .product section table th, .product section h3, ' +
                    '.product section table th, .product section th h5, .product .sub-menu-switcher, .productsubmenu a, .team .team-list li p, .team .team-values ul li, .team article p, ' +
                    '.footer nav a, .backtotop a, .price p, .registrationform li, .registrationform input, .register-by-link-desc, .register-by-link .row input[type=text], ' +
                    '.register-by-link .row input[type=password], .register-by-link .row .cancel, .login-form label, .login-form div input[type=text],' +
                    '.login-form div input[type=password], .login-form a, .invites-smallmenu-item a, .tarifform .expire-date, .tarifform small, .errorblock p, ' +
                    '.chart-gauge .chart-value, .chart-bar .chart-value, .features form div input[type=text], .registrationform input[type=text], ' +
                    '.registrationform input[type=password], .registrationform input[type=submit], .registrationform .errorMessageWrap .errorMessage, .cabmessage input, .cabmessage select, ' +
                    '.cabmessage textarea, .cabmessage button, .feedbackwrap .ui-dialog-title, .feedback input[type="email"], .action-controller-login-auth #usercontent input[type="submit"], ' +
                    '#invite-decline-form #form-decline-explanation h2, #invite-decline-form #form-decline-explanation #DeclineExplanation_reason_id' +
                    'section.registration-by-link h1, section.registration-by-link .form, section.registration-by-link .form .row a.decline-link, #password-recovery-form #YumPasswordRecoveryForm_email,' +
                    '.errorMessage, .simulation-details .ratepercnt, .simulation-details .navigation a, .labels a, .labels li, .labels p, .labels div, .blockvalue, .blockvalue .value, .legendtitle, .smalltitle, .smalltitle a,' +
                    '.extrahours, .timevalue, .helpbuble, .feedback .form-all textarea, .feedbackwrap .ui-dialog-title, .feedback .sbHolder a, .skillstitle, .productlink,' +
                    '.profileform label, .profileform  div, .form p, .form label, .items td .invites-smallmenu-item a, .estmfooter a, .sbSelector, .flash-pop-up p, .flash-pop-up a, ' +
                    '.action-registration .registrationform .row input[type=submit], .thintitle, .order-status label, .order-method label, ' +
                    '.method-description small, .terms-confirm, .period, .order-item h3, .feedback-dialog-title, .terms-page h2,' +
                    '.terms-page p, .browsers a, .browsers span, .copyright, .help-contact-us, .help-contact-us a, .list-ordered p, .grid1 p',
                    {fontFamily:"ProximaNova-Regular", hover:true});
                Cufon.replace('.profile-menu a', {fontFamily:"ProximaNova-Regular"});
                Cufon.replace('.profile-menu .active a, .action-corporateTariff .tarifform .value, .tarifform .light-btn, #account-corporate-personal-form .row .value,' +
                    '#account-personal-personal-form .row .value, .profileform input[type=submit], .inviteaction, .password-recovery-step-4, .order-methods input[type=submit], ' +
                    '.tariff-name, .video-caption, .popup-before-start-sim h3, .popup-before-start-sim .bigbtnsubm, .popup-before-start-sim h2',
                    {fontFamily:"ProximaNova-Bold", hover:true}
                );
                Cufon.replace('.freeacess', {hover:true});
                Cufon.replace('.browsers span a, .proxima-bold', {fontFamily:"ProximaNova-Bold", hover: true});
                Cufon.replace('.ProximaNova-Bold-22px', {fontFamily:"ProximaNova-Bold", fontSize:"19px", color: "#555545", hover: true});
            </script>
        <?php $this->renderPartial('//global_partials/_google_analytics') ?>
    </body>
</html>
