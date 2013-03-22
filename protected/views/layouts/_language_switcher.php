<div class="language">
    <?php if (in_array(Yii::app()->request->getPathInfo(), ['', 'team', 'product'])): ?>
        <a href="?_lang=<?php echo Yii::t('site', 'ru')?>">
            <?php echo Yii::t('site', 'Русский') ?>
        </a>
    <?php endif ?>
</div>