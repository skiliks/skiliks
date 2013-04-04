<a href="/"  class="<?php if (in_array(Yii::app()->request->getPathInfo(), ['', 'static/'])) { echo "active"; } ?>">
    <?php echo Yii::t('site', 'Home') ?>
</a>

<a href="/static/team" class="<?php if (Yii::app()->request->getPathInfo() == 'static/team') { echo "active"; } ?>">
    <?php echo Yii::t('site', 'About Us') ?>
</a>

<a href="/static/product" class="<?php if (Yii::app()->request->getPathInfo() == 'static/product') { echo "active"; } ?>">
    <?php echo Yii::t('site', 'Product') ?>
</a>

<?php if ('ru' === Yii::app()->getLanguage()): ?>
    <!-- RU only -->
    <a href="/static/tariffs" class="<?php if (Yii::app()->request->getPathInfo() == 'static/tariffs') { echo "active"; } ?>">
        <?php echo Yii::t('site', 'Tariffs') ?>
    </a>
<?php endif ?>

<?php if (Yii::app()->user->isGuest) : ?>
    <!-- RU only -->
    <?php if ('ru' == Yii::app()->getLanguage()): ?>
        <a href="" class="sign-in-link">
            <?php echo Yii::t('site', 'Sign in') ?>
        </a>
    <?php endif ?>
<?php else: ?>
    <!-- RU only -->
    <?php if ('ru' == Yii::app()->getLanguage()): ?>
        <a href="/logout">
            <?php echo Yii::t('site', 'Log out') ?> <?php echo Yii::app()->user->data()->profile->email ?>
        </a>
    <?php endif ?>
<?php endif; ?>