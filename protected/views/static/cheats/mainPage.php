
<style>
    .container-2 > header nav a {
        float: left;
    }

    .container-2 > header nav {
        position: static;
    }
</style>

<div class="container container-2">
    <header>
<br>
<br>
<br>
<?php if (Yii::app()->user->data()->isHasAccount()): ?>
    Тип Вашего аккаунта "<?php echo Yii::app()->user->data()->getAccountType() ?>".
<?php else: ?>
    У Вас не выбран тип аккаунта.
<?php endif; ?>

<?php if (Yii::app()->user->data()->getAccount() instanceof UserAccountCorporate): ?>
    <br>
    <br>
    <strong>Корпоративный e-mail: </strong>
        <?php echo Yii::app()->user->data()->getAccount()->corporate_email?>
        <?php if (Yii::app()->user->data()->getAccount()->is_corporate_email_verified): ?>
            (верифицирован)
        <?php else: ?>
            (не верифицирован)
        <?php endif; ?>
        .
<?php endif; ?>

<br>
<br>
<nav>
    <?php if (false === Yii::app()->user->data()->isHasAccount()): ?>
        <a href="/registration/choose-account-type">
            <?php echo Yii::t('site', 'Choose account type') ?>
        </a>
    <?php endif; ?>

    <a href="/simulation/promo/2">Начать симуляцию в режиме promo (lite)</a>
    <a href="/simulation/promo/1">Начать симуляцию в режиме promo (full)</a>

    <?php if (Yii::app()->user->data()->getAccount() instanceof UserAccountCorporate): ?>
    <a href="/dashboard">
        <?php echo Yii::t('site', 'Dashboard') ?>
    </a>
    <?php endif; ?>
</nav>
<br>
<br>
<br>
<br>
<?php if (Yii::app()->user->data()->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)): ?>
    Вы имеет доступ к опциям режима разработчика:

    <br><br>

    <nav>

    <?php if (false === Yii::app()->user->data()->isHasAccount()): ?>
        <a href="/registration/choose-account-type">
            <?php echo Yii::t('site', 'Choose account type') ?>
        </a>
    <?php else: ?>
        <a href="/cheats/cleanUpAccount">
            <?php echo Yii::t('site', 'Reset account type') ?>
        </a>
    <?php endif; ?>

    <a href="/simulation/developer/2">Начать симуляцию (lite) в режиме developer</a>
    <a href="/simulation/developer/1">Начать симуляцию (full) в режиме developer</a>

        <br>
        <br>
        <br>

    <a>Сменить статусы приглашений:</a>
    <a href="/cheats/setinvites/<?php echo Invite:: $statusText[Invite::STATUS_PENDING]   ?>"><?php echo Yii::t('site', Invite:: $statusText[Invite::STATUS_PENDING]) ?></a>
    <a href="/cheats/setinvites/<?php echo Invite:: $statusText[Invite::STATUS_ACCEPTED]  ?>"><?php echo Yii::t('site', Invite:: $statusText[Invite::STATUS_ACCEPTED]) ?></a>
    <a href="/cheats/setinvites/<?php echo Invite:: $statusText[Invite::STATUS_COMPLETED] ?>"><?php echo Yii::t('site', Invite:: $statusText[Invite::STATUS_COMPLETED]) ?></a>
    <a href="/cheats/setinvites/<?php echo Invite:: $statusText[Invite::STATUS_DECLINED]  ?>"><?php echo Yii::t('site', Invite:: $statusText[Invite::STATUS_DECLINED]) ?></a>

        <br>
        <br>
        <br>

<a href="/cheat/dialogsAnalyzer">Открыть анализатор диалогов БД</a>
<a href="/cheat/uploadDialogsToAnalyzer">Открыть анализатор диалогов произвольного ексел-файла</a>

        <br>
        <br>
        <br>

<a href="/invite/add-10">Добавить себе 10 приглашений в корп. аккаунт</a>
<a href="/admin">Старая "админка" - отображение таблиц с логами</a>

        <br>
        <br>
        <br>
        <hr>
Свободные приглашения:
<br/><br>
<?php $invites = Invite::model()->findAllByAttributes(['status' => 0], ['limit'=>'5']); ?>
<?php foreach ($invites as $invite) : ?>
    <a href="/dashboard/accept-invite/<?php echo $invite->code ?>"?>Приглашение</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php endforeach ?>
        <br>
        <br>
        <hr>
        <br>
        <br>

<a href="/static/cheats/listOfsubscriptions">Список подписавшихся на рассылку</a>

    </nav>

<?php endif ?>


<br>
<br>

    </header>
    </div>

<div style="float: none; clear: both; height: 100px;"></div>
