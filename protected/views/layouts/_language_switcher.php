<div class="language">
    <?php $path = Yii::app()->request->getPathInfo() ?>
    <?php if ($path == '' || $path == 'ru' || $path == 'en' || -1 < strpos($path, 'team') || -1 < strpos($path, 'product')): ?>
        <?php $url = str_replace(['/ru/','/en/','/ru','/en'],'',Yii::app()->request->getUrl()).'/'.Yii::t('site', 'ru'); ?>
        <?php $url = str_replace('//', '/', $url) ?>
        <a href="<?php echo $url ?>">
            <?php echo Yii::t('site', 'Русский') ?>
        </a>
    <?php endif ?>
</div>