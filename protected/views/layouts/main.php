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
				
				<p class="coming-soon"><?php echo Yii::t('site', 'Coming soon') ?></p>

				<div class="language"><a href="?_lang=<?php echo Yii::t('site', 'ru')?>"><?php echo Yii::t('site', 'Русский') ?></a></div>

				<nav>
					<a href="/"  <?php if ($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/?_lang=en' || $_SERVER['REQUEST_URI'] == '/?_lang=ru') {?>class="active"<?php } ?>><?php echo Yii::t('site', 'Home') ?></a>
					<a href="/team" <?php if ($_SERVER['REQUEST_URI'] == '/team' || $_SERVER['REQUEST_URI'] == '/team?_lang=en' || $_SERVER['REQUEST_URI'] == '/team?_lang=ru') {?>class="active"<?php } ?>><?php echo Yii::t('site', 'About Us') ?></a>
					<a href="/product" <?php if ($_SERVER['REQUEST_URI'] == '/product' || $_SERVER['REQUEST_URI'] == '/product?_lang=en' || $_SERVER['REQUEST_URI'] == '/product?_lang=ru') {?>class="active"<?php } ?>><?php echo Yii::t('site', 'Product') ?></a>
                    <?php if (null === $this->user || 0 != count($this->signInErrors)) : ?>
                        <a href="" class="sign-in-link"><?php echo Yii::t('site', 'Sign in') ?></a>
                    <?php else: ?>
                        <a href="/site/logout">Log out</a>
                    <?php endif; ?>
				</nav>
			</header>
			<!--header end-->

            <?php if (null === $this->user || 0 != count($this->signInErrors)) : ?>
                <div class="sing-in-box" style="display: <?php echo (0 == count($this->signInErrors)) ? 'none' : 'block'; ?>;">
                    <form class="login-form" action="/" method="post">

                        <div>
                            <label><?php echo Yii::t('site', 'E-mail') ?></label>
                            <input type="text" name="email">
                        </div>
                        <br/>
                        <div>
                            <label><?php echo Yii::t('site', 'Password') ?></label>
                            <input type="password" name="password">
                        </div>
                        <br/>
                        <div>
                            <label><?php echo Yii::t('site', 'Remember me') ?></label>
                            <input type="checkbox" checked="checked" name="remember_me">
                        </div>
                        <br/>
                        <div class="errors">
                            <?php foreach ($this->signInErrors as $error) : ?>
                                <?php echo $error ?><br/><br/>
                            <?php endforeach ?>
                        </div>
                        <div>
                            <input type="submit" value="<?php echo Yii::t('site', 'Sign in') ?>">
                        </div>
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

        <?php if (null === $this->user || 0 != count($this->signInErrors)) : ?>
            <script type="text/javascript">
                // show/hide sign-in box
                $('.sign-in-link').click(function(event){
                    event.preventDefault();
                    $('.sing-in-box').toggle();
                });
            </script>
        <?php endif; ?>
	</body>
</html>