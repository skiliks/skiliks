<?php if (
       (preg_match('/(?i)MSIE 6/',$_SERVER['HTTP_USER_AGENT']))
    || (preg_match('/(?i)MSIE 7/',$_SERVER['HTTP_USER_AGENT']))
    || (preg_match('/(?i)MSIE 8/',$_SERVER['HTTP_USER_AGENT']))
    || (preg_match('/(?i)MSIE 9/',$_SERVER['HTTP_USER_AGENT']))
) {
    return '';
}; ?>

<section class="social-networks-share-links column-full pull-content-right partial">
    <label class="partial-label"><?= __FILE__ ?></label>

        <?php
            $allow = ['ru', 'en', '', 'static/team',
                'static/team/ru', 'static/team/en', 'static/product',
                'static/product/ru', 'static/product/en'];
        ?>
        <?php if($force || in_array(Yii::app()->request->getPathInfo(), $allow)) : ?>

            <label class="inline-block"><?php echo Yii::t('site', "Share");?>:</label>

            <span class="share-buttons-box inline-block">
                <ul class="inline-list inline-block">
                    <li class="share-button vk-share-button">
                        <a title="ВКонтакте" onclick="
                            window.open(
                            'http://vk.com/share.php?url='
                            + encodeURIComponent('<?= Yii::app()->request->hostInfo; ?>/<?= Yii::app()->language; ?>'),
                            'vk-share-dialog',
                            'width=626,height=436');
                            return false;" href="#" target="_blank">
                        </a>
                    </li>

                    <li class="share-button facebook-share-button" >
                        <a title="Facebook" onclick="
                            window.open(
                              'https://www.facebook.com/sharer/sharer.php?u='
                              + encodeURIComponent('<?= Yii::app()->request->hostInfo; ?>/<?= Yii::app()->language; ?>'),
                              'fb-share-dialog',
                              'width=626,height=436');
                            return false;" href="#" target="_blank">
                        </a>
                    </li>

                    <li class="share-button twitter-share-button" >
                        <a title="Twitter" onclick="
                            window.open(
                            'https://twitter.com/share?url='
                            + encodeURIComponent('<?= Yii::app()->request->hostInfo; ?>/<?= Yii::app()->language; ?>'),
                            'twitter-share-dialog',
                            'width=626,height=436');
                            return false;" href="#" target="_blank">
                        </a>
                    </li>

                    <li class="share-button google-share-button" >
                        <a title="Google" onclick="
                            window.open(
                            'https://plus.google.com/share?url='
                            + encodeURIComponent('<?= Yii::app()->request->hostInfo; ?>/<?= Yii::app()->language; ?>'),
                            'google-share-dialog',
                            'width=626,height=436');
                            return false;" href="#" target="_blank">
                        </a>
                    </li>

                    <li class="share-button linkedIn-share-button" >
                        <a title="Linkedin" onclick="
                            window.open(
                            'https://www.linkedin.com/cws/share?url='
                            + encodeURIComponent('<?= Yii::app()->request->hostInfo; ?>/<?= Yii::app()->language; ?>'),
                            'linkedin-share-dialog',
                            'width=626,height=436');
                            return false;" href="#" target="_blank">
                        </a>
                    </li>
            </ul>
        </span>
        <!-- AddThis Button END -->
        <?php endif; ?>

    <a href="#top" class="inline-block link-to-top"><?php echo Yii::t('site', 'Back to top') ?></a>
</section>