
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

<h2>Skiliks: release 1.2</h2>

<?php if (Yii::app()->user->data()->isHasAccount()): ?>
    Тип Вашего аккаунта "<?php echo Yii::app()->user->data()->getAccountType() ?>".
<?php else: ?>
    У вас не выбран тип аккаунта.
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
    <?php /*
        <a href="/cheat/assessments/grid">Таблица оценок</a>

        <br>
        <br>
        <br>
    */ ?>

    <a href="/simulation/<?php echo Simulation::MODE_DEVELOPER_LABEL ?>/<?php echo Scenario::TYPE_LITE ?>">Developer (lite)</a>
    <a href="/simulation/<?php echo Simulation::MODE_DEVELOPER_LABEL ?>/<?php echo Scenario::TYPE_FULL ?>">Developer (full)</a>
    <a href="/simulation/<?php echo Simulation::MODE_DEVELOPER_LABEL ?>/<?php echo Scenario::TYPE_TUTORIAL ?>">Developer (tutorial)</a>

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

Смена тарифа:
        <br/><br>

        <a href="/static/cheats/set-tariff/">Очистить поле тариф</a>
        <a href="/static/cheats/set-tariff/<?= Tariff::SLUG_LITE ?>">Пробный</a>
        <a href="/static/cheats/set-tariff/<?= Tariff::SLUG_STARTER ?>">Малый</a>
        <a href="/static/cheats/set-tariff/<?= Tariff::SLUG_PROFESSIONAL ?>">Профессиональный</a>
        <a href="/static/cheats/set-tariff/<?= Tariff::SLUG_BUSINESS ?>">Бизнес</a>

        <br>
        <br>

        <hr>

        <br>
        <br>

<a href="/static/cheats/listOfsubscriptions">Список подписавшихся на рассылку</a>

<?php if (false === Yii::app()->user->data()->isHasAccount()): ?>
    <a href="/registration/choose-account-type">
        <?php echo Yii::t('site', 'Choose account type') ?>
    </a>
<?php else: ?>
    <a href="/cheats/cleanUpAccount">
        <?php echo Yii::t('site', 'Reset account type') ?>
    </a>
<?php endif; ?>

    </nav>

<?php endif ?>

<br/>
<br/>
<br/>

<h2 style="color: #3C747B;">Импортированные версии сценариев:</h2>

<?php foreach ($scenarios as $scenario): ?>
    <div><strong><?php echo $scenario->slug ?></strong>: <?php echo $scenario->filename ?></div><br/>
<?php endforeach; ?>
<br>

    </header>
    </div>

<div style="float: none; clear: both; height: 100px;"></div>
