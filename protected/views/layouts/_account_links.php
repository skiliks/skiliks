
<?php if ('ru' != Yii::app()->language): ?>
    <?php return; ?>
<?php endif ?>

<!-- Corporate: -->
<?php if (null !== Yii::app()->user && Yii::app()->user->data()->isCorporate()) : ?>

    <a href="/dashboard"  class="<?php if (Yii::app()->request->getPathInfo() == 'dashboard') { echo "active"; } ?>">
        <?php echo Yii::t('site', 'Dashboard') ?>
    </a>

    <a href="/profile" class="<?php if (Yii::app()->request->getPathInfo() == 'profile') { echo "active"; } ?>">
        <?php echo Yii::t('site', 'Profile') ?>
    </a>

    <?php /*
    <a href="/statistic" class="<?php if (Yii::app()->request->getPathInfo() == 'statistic') { echo "active"; } ?>">
        <?php echo Yii::t('site', 'Statistic') ?>
    </a>

    <a href="/notifications" class="<?php if (Yii::app()->request->getPathInfo() == 'notifications') { echo "active"; } ?>">
        <?php echo Yii::t('site', 'Notifications') ?>
    </a>
    */ ?>

    <a href="/simulations" class="<?php if (Yii::app()->request->getPathInfo() == 'simulations') { echo "active"; } ?>">
        <?php echo Yii::t('site', 'Simulations'); ?>
    </a>

<?php endif ?>

<!-- Personal: -->
<?php if (null !== Yii::app()->user && Yii::app()->user->data()->isPersonal()) : ?>

    <a href="/dashboard"  class="<?php if (Yii::app()->request->getPathInfo() == 'dashboard') { echo "active"; } ?>">
        <?php echo Yii::t('site', 'Dashboard') ?>
    </a>

    <a href="/profile" class="<?php if (Yii::app()->request->getPathInfo() == 'profile') { echo "active"; } ?>">
        <?php echo Yii::t('site', 'Profile') ?>
    </a>

    <?php /*
    <a href="/statistic" class="<?php if (Yii::app()->request->getPathInfo() == 'statistic') { echo "active"; } ?>">
        <?php echo Yii::t('site', 'Statistic') ?>
    </a>

    <a href="/notifications" class="<?php if (Yii::app()->request->getPathInfo() == 'notifications') { echo "active"; } ?>">
        <?php echo Yii::t('site', 'Notifications') ?>
    </a>
    */?>

    <a href="/simulations" class="<?php if (Yii::app()->request->getPathInfo() == 'simulations') { echo "active"; } ?>">
        <?php echo Yii::t('site', 'Simulations'); ?>
    </a>

<?php endif ?>

<?php // Our DEV cheats ?>
<?php if (null !== Yii::app()->user && Yii::app()->user->data()->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)) : ?>

    <a href="/cheats">
        <?php echo Yii::t('site', 'Cheats') ?>*
    </a>

<?php endif ?>