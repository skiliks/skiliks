<a href="/<?php echo Yii::app()->language ?>"  class="<?php if (in_array(Yii::app()->request->getPathInfo(), ['/', '/static/'])) { echo "active"; } ?>">
    <?php echo Yii::t('site', 'Home') ?>
</a>

<a href="/static/team/<?php echo Yii::app()->language ?>" <?php if (Yii::app()->request->getPathInfo() == '/static/team') { echo "active"; } ?>">
    <?php echo Yii::t('site', 'About Us') ?>
</a>

<a href="/static/product/<?php echo Yii::app()->language ?>" <?php if (Yii::app()->request->getPathInfo() == '/static/product') { echo "active"; } ?>">
    <?php echo Yii::t('site', 'Product') ?>
</a>

<?php if ('ru' === Yii::app()->language): ?>
    <!-- RU only -->
    <a href="/static/tariffs" <?php if (Yii::app()->request->getPathInfo() == '/static/tariffs') { echo "active"; } ?>">
        <?php echo Yii::t('site', 'Tariffs') ?>
    </a>

    <!-- RU only -->
    <a href="/static/contacts" <?php if (Yii::app()->request->getPathInfo() == '/static/contacts') { echo "active"; } ?>">
        <?php echo Yii::t('site', 'Contacts') ?>
    </a>
<?php endif ?>

<?php if (Yii::app()->user->isGuest) : ?>
    <!-- RU only -->
    <?php if ('ru' == Yii::app()->language): ?>
        <a href="" class="sign-in-link">
            <?php echo Yii::t('site', 'Sign in') ?>
        </a>
    <?php endif ?>
<?php else: ?>
    <!-- RU only -->
    <?php if ('ru' == Yii::app()->language): ?>
        <a href="/logout">
            <?php echo Yii::t('site', 'Log out') ?> <?php echo Yii::app()->user->data()->profile->email ?>
        </a>
    <?php endif ?>
<?php endif; ?>