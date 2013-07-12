<section class="home-content">
    <h1 class="page-header"><?php echo Yii::t('site', 'The easiest &amp; most reliable way to discover your people management skills!') ?></h1>
    <!--<div class="iframe-video-wrap">
        <div class="iframe-video">
            <iframe src="http://player.vimeo.com/video/{Yii::t('site', '61258856')}" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
        </div>
    </div>-->

    <div class="container-2">
        <div class="grid1">
            <ul class="list-light unstyled font-xxlarge home-list-box">
                <li><?php echo Yii::t('site', 'Simulation aimed at testing manager’s skills') ?></li>
                <li><?php echo Yii::t('site', '2-3-hours game') ?></li>
                <li><?php echo Yii::t('site', 'Live tasks and decision-making situations') ?></li>
                <li><?php echo Yii::t('site', 'A tool to assess candidates and newcomers') ?></li>
            </ul>
        </div>
        <div class="grid1 noleftmargin">
            <div class="video" style="cursor: pointer;">
                <strong class="video-caption font-white font-xlarge"><?php echo Yii::t('site', 'Watch the video to learn more') ?></strong>
            </div>
            <div class="social_networks smallicons">
                <span class="proxima-bold"><?php echo Yii::t('site', 'Share') ?>:</span>
                <div class="addthis_toolbox addthis_default_style addthis_32x32_style" addthis:url="http://player.vimeo.com/video/{Yii::t('site', '61258856')}" addthis:title="Skiliks - game the skills" addthis:description="Самый простой и надежный способ проверить навыки менеджеров: деловая онлайн симуляция, имитирующая реальный рабочий день с типичными управленческими задачами и ситуациями принятия решений">
                    <a class="addthis_button_vk"></a>
                    <a class="addthis_button_facebook"></a>
                    <a class="addthis_button_twitter"></a>
                    <a class="addthis_button_google_plusone_share"  g:plusone:count="false"></a>
                    <a class="addthis_button_linkedin"></a>
                </div>
            </div>
        </div>
    </div>


    <?php if ('ru' == Yii::app()->getlanguage()): ?>

        <a href="/registration" class="bigbtnsubmt freeacess"><?php echo Yii::t('site', 'Start using it now for free') ?></a>

    <?php elseif ('en' == Yii::app()->getlanguage()): ?>
            <!-- FORM { -->
            <div id="notify-form">
            <form action="static/pages/addUserSubscription" id="subscribe-form">
                <div>
                    <input type="text"
                           id = "user-email-value"
                           placeholder="{Yii::t('site', 'Enter your email address')}"
                           />
                    <p id="user-email-error-box" class="errorMessage" style="display: none; top:-17px; left:2px; white-space: nowrap;">
                        <?php echo Yii::t('site', 'Please enter a valid email address') ?>
                    </p>
                </div>
                <div><input type="submit" value="{Yii::t('site', 'Notify me')}" ?></div>
            </form>
            </div>
        <!-- FORM } -->
    <?PHP endif ?>

</section>
<!--features end-->

<!--main article-->
<section class="main-article">
    <article>
        <h3><?php echo Yii::t('site', 'Easiest') ?></h3>
        <ul>
            <li><?php echo Yii::t('site', 'Saves your time') ?></li>
            <li><?php echo Yii::t('site', 'Can be used by an unlimited number of applicants in any part of the world') ?></li>
            <li><?php echo Yii::t('site', 'No hard-, soft- or any-ware required! ! Just make sure you are online') ?></li>
            <li><?php echo Yii::t('site', 'Results can be obtained and used immediately') ?></li>
        </ul>
    </article>

    <article>
        <h3><?php echo Yii::t('site', 'Most Reliable') ?></h3>
        <ul>
            <li><?php echo Yii::t('site', 'Focused on key skills') ?></li>
            <li><?php echo Yii::t('site', 'Based on best working practices') ?></li>
            <li><?php echo Yii::t('site', 'Uses real work environment, tasks and decision<br />making situations') ?></li>
            <li><?php echo Yii::t('site', 'Based on mathematical methods not just feelings') ?></li>
        </ul>
    </article>
</section>
<!--main article end-->

<!--clients-->
<section class="clients">
    <h3><?php echo Yii::t('site', 'Our Clients') ?></h3>

    <ul>
        <li style="display:none;"><?php echo CHtml::image("$assetsUrl/img/skiliks-fb.png") ?></li>
        <li><?php echo CHtml::image("$assetsUrl/img/icon-hipway.png") ?></a></li>
        <li><?php echo CHtml::image("$assetsUrl/img/icon-mif.png") ?></li>
        <li><?php echo CHtml::image("$assetsUrl/img/icon-wikimart.png") ?></li>
        <li><?php echo CHtml::image("$assetsUrl/img/icon-mcg.png") ?></li>
    </ul>
</section>
<!--clients end-->

