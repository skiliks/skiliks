<div class="language">
    <?php $path = Yii::app()->request->getPathInfo() ?>
    <?php if (in_array($path, ['', 'ru', 'en']) || -1 < strpos($path, 'static')|| -1 < strpos($path, 'team')|| -1 < strpos($path, 'product')): ?>
        <a href="<?php echo Yii::t('site', 'ru')?>">
            <?php echo Yii::t('site', 'Русский') ?>
        </a>
    <?php endif ?>
</div>