<script type="text/javascript">
    // 1) проверка ОС и браузера
    window.httpUserAgent = '<?= $httpUserAgent ?>';
    window.isSkipBrowserCheck = '<?= $isSkipBrowserCheck ?>'
</script>

<!--features-->

    <section class="pull-center us-column-full us-homepage-content">
        <section class="features">
            <h1><?= Yii::t('site', 'Easy &amp; reliable way to discover your people management skills!') ?></h1>

            <div class="column-2-3-fixed">
                <span class="us-column-2-3">
                    <ul class="list-white">
                        <li class="icon-tick"><?= Yii::t('site', 'Simulation aimed at testing basic manager’s skills')?></li>
                        <li class="icon-tick"><?= Yii::t('site', '2-hours game')?></li>
                        <li class="icon-tick"><?= Yii::t('site', 'Live tasks and decision-making situations')?></li>
                        <li class="icon-tick"><?= Yii::t('site', 'A tool to assess candidates and employees')?></li>
                    </ul>

                    <!-- -->
                    <?php if ('ru' == Yii::app()->getlanguage()): ?>

                        <a href="/registration/single-account"
                           class="background-dark-blue icon-circle-with-blue-arrow-big
                               button-standard button-standard-BD2929 icon-padding-standard">
                            <?= Yii::t('site', 'Register now') ?>
                        </a>

                    <?php elseif ('en' == Yii::app()->getlanguage()): ?>
                        <br/>
                        <br/>
                        <!-- FORM { -->
                        <div class="locator-subscribe-form">
                            <form action="static/pages/addUserSubscription" id="action-subscribe-form">
                                <div>
                                    <span class="error-place">
                                        <span class="errorMessage locator-errorMessage"></span>
                                    </span>
                                    <input type="text" style="width: 182px;"
                                           class="inputs-wide-height locator-user-email-value"
                                           placeholder="<?= Yii::t('site', 'Enter your email address') ?>"
                                        />
                                    <input type="submit" value="<?= Yii::t('site', 'Notify me') ?>"
                                        class="background-dark-blue icon-circle-with-blue-arrow-big
                                        button-standard icon-padding-standard margin-left-8" />
                                </div>
                            </form>
                        </div>
                        <!-- FORM } -->
                    <?php endif ?>

                </span>
                <span class="us-column-1-3 margin-left-18 vertical-align-top">
                    <?php if ('ru' == Yii::app()->getlanguage()): ?>
                        <?php // RU: ?>
                        <br/>
                        <div class="pull-content-center">
                            <span class="action-open-lite-simulation-popup inter-active us-start-demo-box"
                                  <?php if (0 == count($notUsedLiteSimulations)): ?>
                                    data-href="/simulation/demo"
                                  <?php else: ?>
                                    data-href="/simulation/promo/lite/<?= $notUsedLiteSimulations[0]->id ?>"
                                  <?php endif ?>
                                >
                                <img class="us-start-demo-image"
                                     src="<?= $this->assetsUrl ?>/img/site/1280/homepage/demo.png" />
                                <span class="label no-hover background-dark-blue color-ffffff us-start-demo">
                                    Начать демо
                                </span>
                            </span>
                        </div>
                    <?php else: ?>
                    <?php // EN: ?>
                        <br/>
                        <div class="pull-content-center">
                            <span class="inter-active us-start-demo-box us-video-height"
                                  data-href="/simulation/demo">
                                <span class="action-view-video">
                                    <img class="us-start-demo-image"
                                         src="<?= $this->assetsUrl ?>/img/site/1280/homepage/demo.png" />
                                    <span class="label no-hover background-dark-blue color-ffffff us-start-demo">
                                        Watch the video
                                    </span>
                                </span>

                                <?php // Social networks share(video) links { ?>
                                <span class="social-networks-share-link pull-content-right">
                                    <label class="inline-block">Share video:</label>

                                    <span class="share-buttons-box inline-block">
                                        <ul class="inline-list inline-block">
                                            <li class="share-button vk-share-button">
                                                <a target="_blank" href="#" onclick="
                                                    window.open(
                                                    'http://vk.com/share.php?url='
                                                    + encodeURIComponent('http://loc.skiliks.com/watchVideo/en'),
                                                    'vk-share-dialog',
                                                    'width=626,height=436');
                                                    return false;" title="ВКонтакте">
                                                </a>
                                            </li>

                                            <li class="share-button facebook-share-button">
                                                <a target="_blank" href="#" onclick="
                                                    window.open(
                                                      'https://www.facebook.com/sharer/sharer.php?u='
                                                      + encodeURIComponent('http://loc.skiliks.com/watchVideo/en'),
                                                      'fb-share-dialog',
                                                      'width=626,height=436');
                                                    return false;" title="Facebook">
                                                </a>
                                            </li>

                                            <li class="share-button twitter-share-button">
                                                <a target="_blank" href="#" onclick="
                                                    window.open(
                                                    'https://twitter.com/share?url='
                                                    + encodeURIComponent('http://loc.skiliks.com/watchVideo/en'),
                                                    'twitter-share-dialog',
                                                    'width=626,height=436');
                                                    return false;" title="Twitter">
                                                </a>
                                            </li>

                                            <li class="share-button google-share-button">
                                                <a target="_blank" href="#" onclick="
                                                    window.open(
                                                    'https://plus.google.com/share?url='
                                                    + encodeURIComponent('http://loc.skiliks.com/watchVideo/en'),
                                                    'google-share-dialog',
                                                    'width=626,height=436');
                                                    return false;" title="Google">
                                                </a>
                                            </li>

                                            <li class="share-button linkedIn-share-button">
                                                <a target="_blank" href="#" onclick="
                                                    window.open(
                                                    'https://www.linkedin.com/cws/share?url='
                                                    + encodeURIComponent('http://loc.skiliks.com/watchVideo/en'),
                                                    'linkedin-share-dialog',
                                                    'width=626,height=436');
                                                    return false;" title="Linkedin">
                                                </a>
                                            </li>
                                        </ul>
                                    </span>
                                </span>
                                <?php // Social networks share(video) links } ?>

                            </span>
                        </div>
                    <?php endif ?>
                </span>
            </div>

            <section class="">
                <p class="us-comment us-angela-comment">
                    <?php echo Yii::t('site', '&quot;Remarkably comprehensive<br />&nbsp;and deep assessment of<br />&nbsp;&nbsp;&nbsp;skills - now I know what<br />&nbsp;&nbsp;I can expect from<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;newcomers&quot;') ?>
                </p>
            </section>

            <img class="us-homepage-angela" src="<?= $this->assetsUrl ?>/img/site/1280/homepage/angela.png" />

        </section>
        <!--features end-->

        <!--main article-->
        <section class="pull-content-left column-2-3-fixed us-bottom-content">
            <img class="us-homepage-trudyakin" src="<?= $this->assetsUrl ?>/img/site/1280/homepage/trudyakin.png" />

            <section class="">
                <p class="us-comment us-trudyakin-comment">
                    <?php echo Yii::t('site', '&quot;It&lsquo;s a real game with<br />&nbsp;great challenge and high<br />&nbsp;&nbsp;&nbsp;&nbsp;immersion - I haven&lsquo;t even<br />&nbsp;&nbsp;noticed how the time<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;passed by&quot;') ?>
                </p>
            </section>
            <article class="us-middle-content color-29313D">
                <h2><?= Yii::t('site', 'Easy')?></h2>
                <ul>
                    <li><?= Yii::t('site', 'Saves your time')?></li>
                    <li><?= Yii::t('site', 'Can be used by an unlimited number of applicants in any part of the world')?></li>
                    <li><?= Yii::t('site', 'No hard-, soft- or any-ware required! ! Just make sure you are online')?></li>
                    <li><?= Yii::t('site', 'Results can be obtained and used immediately')?></li>
                </ul>

                <h2><?= Yii::t('site', 'Reliable')?></h2>
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
            <article class="us-our-client color-29313D">
            <h2><?=Yii::t('site', 'Our Clients')?></h2>

            <ul>
                <li><?= CHtml::image("$assetsUrl/img/site/1280/homepage/hipway.png")?></a></li>
                <li><?= CHtml::image("$assetsUrl/img/site/1280/homepage/mif.png")?></li>
                <li><?= CHtml::image("$assetsUrl/img/site/1280/homepage/wikimart.png")?></li>
                <li><?= CHtml::image("$assetsUrl/img/site/1280/homepage/mcg.png")?></li>
                <li><?= CHtml::image("$assetsUrl/img/site/1280/homepage/fabrica-ocon.png")?></li>
                <li><?= CHtml::image("$assetsUrl/img/site/1280/homepage/nettrader.png")?></li>
                <li><?= CHtml::image("$assetsUrl/img/site/1280/homepage/eksmo.png")?></li>
                <li><?= CHtml::image("$assetsUrl/img/site/1280/homepage/exiclub.png")?></li>
            </ul>
            </article>
        </section>
        <!--clients end-->
    </section>

<img class="us-homepage-heroes-group" src="<?= $this->assetsUrl ?>/img/site/1280/homepage/heroes-group.png" />

<div class="iframe-video-wrap hide">
    <div class="iframe-video">
        <iframe src="http://player.vimeo.com/video/<?= Yii::t('site', '61258856') ?>"
            frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
    </div>
</div>

<?=$this->renderPartial('//global_partials/_system_mismatch_popup')?>

