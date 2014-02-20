<script type="text/javascript">
    // 1) проверка ОС и браузера
    window.httpUserAgent = '<?= $httpUserAgent ?>';
    window.isSkipBrowserCheck = '<?= $isSkipBrowserCheck ?>'
</script>

<!--features-->
<section class="us-heroes-background">
    <section class="pull-center us-column-full us-homepage-content">
        <section class="features">
            <h1><?= Yii::t('site', 'Easy &amp; reliable way to discover your people management skills!') ?></h1>

            <div class="column-2-3-wide">
                <span class="column-1-2">
                    <ul class="list-white">
                        <li class="icon-tick"><?= Yii::t('site', 'Simulation aimed at testing basic manager’s skills')?></li>
                        <li class="icon-tick"><?= Yii::t('site', '2-hours game')?></li>
                        <li class="icon-tick"><?= Yii::t('site', 'Live tasks and decision-making situations')?></li>
                        <li class="icon-tick"><?= Yii::t('site', 'A tool to assess candidates and employees')?></li>
                    </ul>

                    <!-- -->
                    <?php if ('ru' == Yii::app()->getlanguage()): ?>

                        <a href="/registration" class="label background-dark-blue icon-circle-with-blue-arrow-big button-standard icon-padding-standard">
                            <?= Yii::t('site', 'Register now') ?>
                        </a>

                    <?php elseif ('en' == Yii::app()->getlanguage()): ?>
                        <!-- FORM { -->
                        <div id="notify-form">
                            <form action="static/pages/addUserSubscription" id="subscribe-form">
                                <div>
                                    <input type="text"
                                           id = "user-email-value"
                                           placeholder="<?= Yii::t('site', 'Enter your email address') ?>"
                                        />
                                    <p id="user-email-error-box" class="errorMessage" style="display: none; top:-17px; left:2px; white-space: nowrap;">
                                        <?php Yii::t('site', 'Please enter a valid email address') ?>
                                    </p>
                                </div>
                                <div><input type="submit" value="<?= Yii::t('site', 'Notify me') ?>" /></div>
                            </form>
                        </div>
                        <!-- FORM } -->
                    <?php endif ?>
                </span>
                <span class="column-1-2">
                    <div class="videosocwrap clearfix">
                        <div class="video" style="cursor: pointer;">
                            <span class="video-caption"><?= Yii::t('site', 'Watch the video to learn more')?></span>
                        </div>
                        <div class="social_networks smallicons">
                            <span><?= Yii::t('site', 'Share video')?>:</span>
                            <div class="addthis_toolbox addthis_default_style addthis_32x32_style"
                                 addthis:url="http://player.vimeo.com/video/<?= Yii::t('site', '61258856')?>?title=0&amp;byline=0&amp;portrait=0&amp;color=24bdd3"
                                 addthis:title="Skiliks - game the skills"
                                 addthis:description="<?= Yii::t('site', 'www.skiliks.com - online simulation aimed at testing management skills')?>">

                                <a class="new_social_buttons vk_share_button" title="VK" onclick="
                                    window.open(
                                    'http://vk.com/share.php?'
                                    + 'description=<?= Yii::t('site', 'www.skiliks.com - online simulation aimed at testing management skills')?>'
                                    + '&url=' + encodeURIComponent('<?= Yii::app()->request->hostInfo?>'
                                    +'/watchVideo/' + '<?= Yii::app()->language?>'),
                                    'vk-share-dialog',
                                    'width=626, height=436');
                                    return false;" href="#" target="_blank">
                                </a>

                                <a class="new_social_buttons facebook_share_button" title="Facebook" onclick="
                                    window.open(
                                    'https://www.facebook.com/sharer/sharer.php?u='
                                    + encodeURIComponent('<?= Yii::app()->request->hostInfo?>'
                                    +'/watchVideo/' + '<?= Yii::app()->language?>'),
                                    'facebook-share-dialog',
                                    'width=626,height=436');
                                    return false;" href="#" target="_blank">
                                </a>


                                <a class="new_social_buttons twitter_share_button" title="Twitter" onclick="
                                    window.open(
                                    'https://twitter.com/share?url='
                                    + encodeURIComponent('<?= Yii::app()->request->hostInfo?>'
                                    + '/watchVideo/' + '<?= Yii::app()->language?>'),
                                    'twitter-share-dialog',
                                    'width=626,height=436');
                                    return false;" href="#" target="_blank">
                                </a>

                                <a class="new_social_buttons google_share_button" title="Google" onclick="
                                    window.open(
                                    'https://plus.google.com/share?url='
                                    + encodeURIComponent('<?= Yii::app()->request->hostInfo?>'
                                    + '/watchVideo/' + '<?= Yii::app()->language?>'),
                                    'google-share-dialog',
                                    'width=626,height=436');
                                    return false;" href="#" target="_blank">
                                </a>

                                <a class="new_social_buttons linkedin_share_button" title="Linkedin" onclick="
                                    window.open(
                                    'https://www.linkedin.com/cws/share?url='
                                    + encodeURIComponent('<?= Yii::app()->request->hostInfo?>'
                                    + '/watchVideo/' + '<?= Yii::app()->language?>'),
                                    'linkedin-share-dialog',
                                    'width=626,height=436');
                                    return false;" href="#" target="_blank">
                                </a>
                            </div>
                        </div>
                    </div>
                </span>
            </div>

            <section class="anjela">
                <p class="heroes-comment right">
                    <?php echo Yii::t('site', '&quot;Remarkably comprehensive<br />&nbsp;and deep assessment of<br />&nbsp;&nbsp;&nbsp;skills - now I know what<br />&nbsp;&nbsp;I can expect from<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;newcomers&quot;') ?>
                </p>
            </section>

        </section>
        <!--features end-->

        <!--main article-->
        <section class="pull-content-left column-2-3-wide">
            <section class="trudyakin">
                <p class="heroes-comment left">
                <p class="heroes-comment left">
                    <?php echo Yii::t('site', '&quot;It&lsquo;s a real game with<br />&nbsp;great challenge and high<br />&nbsp;&nbsp;&nbsp;&nbsp;immersion - I haven&lsquo;t even<br />&nbsp;&nbsp;noticed how the time<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;passed by&quot;') ?>
                </p>
            </section>
            <article>
                <h3><?= Yii::t('site', 'Easy')?></h3>
                <ul>
                    <li><?= Yii::t('site', 'Saves your time')?></li>
                    <li><?= Yii::t('site', 'Can be used by an unlimited number of applicants in any part of the world')?></li>
                    <li><?= Yii::t('site', 'No hard-, soft- or any-ware required! ! Just make sure you are online')?></li>
                    <li><?= Yii::t('site', 'Results can be obtained and used immediately')?></li>
                </ul>
            </article>

            <article>
                <h3><?= Yii::t('site', 'Reliable')?></h3>
                <ul>
                    <li><?= Yii::t('site', 'Focused on key practical skills')?></li>
                    <li><?= Yii::t('site', 'Based on best working practices')?></li>
                    <li><?= Yii::t('site', 'Uses real work environment, tasks and decision<br />making situations')?></li>
                    <li><?= Yii::t('site', 'Based on mathematical methods not just feelings')?></li>
                </ul>
            </article>
        </section>
        <!--main article end-->

        <!--clients-->
        <section class="pull-content-left">
            <h3><?=Yii::t('site', 'Our Clients')?></h3>

            <ul>
                <li style="display:none;"><?=CHtml::image("$assetsUrl/img/skiliks-fb.png")?></li>
                <li><?=CHtml::image("$assetsUrl/img/icon-hipway.png")?></a></li>
                <li><?=CHtml::image("$assetsUrl/img/icon-mif.png")?></li>
                <li><?=CHtml::image("$assetsUrl/img/icon-wikimart.png")?></li>
                <li><?=CHtml::image("$assetsUrl/img/icon-mcg.png")?></li>
            </ul>
        </section>
        <!--clients end-->
    </section>
</section>
<div class="clearfix"></div>

<?=$this->renderPartial('//global_partials/_system_mismatch_popup')?>

