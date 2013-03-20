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
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-1.7.2.min.js');
$cs->registerScriptFile($assetsUrl . '/js/niceCheckbox.js');
$cs->registerScriptFile($assetsUrl . '/js/jquery.selectbox-0.2.js');
//$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-ui-1.8.24.custom.js', CClientScript::POS_BEGIN);
//
//$cs->registerCssFile($assetsUrl . '/js/jquery/jquery-ui.css');
$cs->registerCssFile($assetsUrl . "/css/style.css");
?>

<!DOCTYPE html>
<html lang="<?php echo Yii::t('site', 'en') ?>">
	<head>
		<meta charset="utf-8" />
        <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
		<title>Skiliks - game the skills</title>

		<!--[if IE]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
    </head>
    
    <?php if ($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/?_lang=en' || $_SERVER['REQUEST_URI'] == '/?_lang=ru') {?>
   	<body>
    <?php } else if ($_SERVER['REQUEST_URI'] == '/team' || $_SERVER['REQUEST_URI'] == '/team?_lang=en' || $_SERVER['REQUEST_URI'] == '/team?_lang=ru') {?>
    <body class="inner-team">
    <?php } else if ($_SERVER['REQUEST_URI'] == '/registration/choose-account-type' || $_SERVER['REQUEST_URI'] == '/registration/choose-account-type?_lang=en' || $_SERVER['REQUEST_URI'] == '/registration/choose-account-type?_lang=ru') {?>
	<body class="inner-registration">
    <?php } else {?>
    <body class="inner">
    <?php } ?>
    	
		<div class="container<?php if ($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/?_lang=en' || $_SERVER['REQUEST_URI'] == '/?_lang=ru') {?> main-page<?php } ?><?php if ($_SERVER['REQUEST_URI'] == '/team' || $_SERVER['REQUEST_URI'] == '/team?_lang=en' || $_SERVER['REQUEST_URI'] == '/team?_lang=ru') {?> team-page<?php } ?>" id="top">
			
			<!--header-->
			<header>
				<h1><a href="/">Skiliks</a></h1>
				
				<p class="coming-soon">Coming soon</p>

				<div class="language">
                    <a href="?_lang=<?php echo Yii::t('site', 'ru')?>"><?php echo Yii::t('site', 'Русский') ?></a>
                </div>

				<nav>
					<a href="/"  <?php if ($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/?_lang=en' || $_SERVER['REQUEST_URI'] == '/?_lang=ru') {?>class="active"<?php } ?>><?php echo Yii::t('site', 'Home') ?></a>
					<a href="/team" <?php if ($_SERVER['REQUEST_URI'] == '/team' || $_SERVER['REQUEST_URI'] == '/team?_lang=en' || $_SERVER['REQUEST_URI'] == '/team?_lang=ru') {?>class="active"<?php } ?>><?php echo Yii::t('site', 'About Us') ?></a>
					<a href="/product" <?php if ($_SERVER['REQUEST_URI'] == '/product' || $_SERVER['REQUEST_URI'] == '/product?_lang=en' || $_SERVER['REQUEST_URI'] == '/product?_lang=ru') {?>class="active"<?php } ?>><?php echo Yii::t('site', 'Product') ?></a>
                    <?php if (Yii::app()->user->isGuest) : ?>
                        <a href="" class="sign-in-link"><?php echo Yii::t('site', 'Sign in') ?></a>
                    <?php else: ?>
                        <a href="/office"><?php echo Yii::t('site', 'Office for') ?> <?php echo Yii::app()->user->data()->profile->email ?></a>
                        <a href="/user/user/logout"><?php echo Yii::t('site', 'Log out') ?></a>
                    <?php endif; ?>
				</nav>
			</header>
			<!--header end-->

            <?php if (!Yii::app()->user->id) : ?>
                <div class="sign-in-box message_window">
                    <form class="login-form" action="/user/auth" method="post">
                        <input type="hidden" name="returnUrl" value="/static/site/index"/>

                        <div class="login">
                            <a href="#">Forgot your password?</a>
                            <input type="text" name="YumUserLogin[username]" placeholder="Enter login" />
                        </div>
                        <div class="password">
                            <input type="password" name="YumUserLogin[password]" placeholder="Enter password" />
                        </div>
                        <div class="remember">
                            <input type="checkbox" name="remember_me" value="remeber" class="niceCheck" id="ch1" /> <label for="ch1"><?php echo Yii::t('site', 'Remember me') ?></label>
                        </div>
                        <div class="errors">
                        </div>
                        <div class="submit">
                            <input type="submit" value="<?php echo Yii::t('site', 'Sign in') ?>">
                        </div>
                        <?php if (null === $this->user || null === $this->user->id || 0 != count($this->signInErrors)) : ?>
                            <a href="/registration"><?php echo Yii::t('site', 'Registration') ?></a>
                        <?php endif; ?>
                    </form>
                </div>
            <?php endif; ?>

			<?php if ($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/?_lang=en' || $_SERVER['REQUEST_URI'] == '/?_lang=ru') {?>
			<p class="heroes-comment right"><?php echo Yii::t('site', '&quot;Remarkably comprehensive<br />&nbsp;and deep assessment of<br />&nbsp;&nbsp;&nbsp;skills - now I know what<br />&nbsp;&nbsp;I can expect from<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;newcomers&quot;') ?></p>
			<p class="heroes-comment left"><?php echo Yii::t('site', '&quot;It&lsquo;s a real game with<br />&nbsp;great challenge and high<br />&nbsp;&nbsp;&nbsp;&nbsp;immersion - I haven&lsquo;t even<br />&nbsp;&nbsp;noticed how the time<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;passed by&quot;') ?></p>
			<?php } ?>

			<!--content-->
			<div class="content">

			<?php echo $content; ?>

			</div>
			<!--content end-->
		</div>

		<!--footer-->
		<div class="footer">
			<footer>
				<div class="backtotop"><a href="#top"><?php echo Yii::t('site', 'Back to top') ?></a></div>

				<div class="logo"><a href="/">Skiliks</a></div>

				<nav>
					<a href="../"><?php echo Yii::t('site', 'Home') ?></a>
					<a href="team"><?php echo Yii::t('site', 'About Us') ?></a>
					<a href="product"><?php echo Yii::t('site', 'Product') ?></a>
				</nav>

				<p class="copyright">Copyright - Skiliks  - 2012</p>
			</footer>
		</div>
		<!--footer end-->

        <?php if (null === $this->user || null === $this->user->id || 0 != count($this->signInErrors)) : ?>
            <script type="text/javascript">
            	$(function () {
			        var h=$('.container').height();
	            	$('.sign-in-box').css('height',h+'px');

                    // @link: http://jqueryui.com/dialog/
                    $(".message_window").dialog({
                        closeOnEscape: true,
                        dialogClass: 'sing-in-pop-up',
                        minHeight: 220,
                        modal: true,
                        position: {
                            my: "right top",
                            at: "right bottom",
                            of: $('#top header')
                        },
                        resizable: false,
                        title: 'Sign in',
                        width: 275
                    });
                    $(".message_window").dialog("close");
			    });
            </script>
        <?php endif; ?>

        <script type="text/javascript">
        	$(function () {
		        $("select").selectbox();
		    });
        </script>

	</body>
</html>
