
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

<h2>Skiliks: release 1.4</h2>

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
<?php if (Yii::app()->user->data()->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)): ?>
    Вы имеет доступ к опциям режима разработчика:

    <br><br>

    <nav>

    <a href="/simulation/<?php echo Simulation::MODE_DEVELOPER_LABEL ?>/<?php echo Scenario::TYPE_LITE ?>">Developer (lite)</a>
    <a style="background-color: #2d7b91" href="/simulation/<?php echo Simulation::MODE_DEVELOPER_LABEL ?>/<?php echo Scenario::TYPE_FULL ?>">Developer (full)</a>
    <?php if(Yii::app()->user->data()->isAdmin()) : ?>
        <a href="/admin_area/dashboard">Admin Area</a>
    <?php endif ?>
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

<a href="/invite/add-10">Добавить себе 10 приглашений в корп. аккаунт</a>

        <br>
        <br>
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
        <br>

    <a href="/static/cheats/listOfsubscriptions" style="background-color: #ffa73d">Список подписавшихся на рассылку</a>
    <a href="/cheat/dialogsAnalyzer">Открыть анализатор диалогов БД</a>

        <br>
        <br>
        <br>

    <a href="/cheat/uploadDialogsToAnalyzer">Открыть анализатор диалогов произвольного ексел-файла</a>

        <br/>
        <br/>
        <br/>

    <a href="/debug/styleCss">style css</a>
    <a href="/debug/styleForPopupCss">style popup css</a>
    <a href="/debug/styleBlocks">style blocks</a>
    <a href="/debug/styleGrid">style grid</a>
    <a href="/debug/styleGridResults">style grid results</a>
    <a href="/debug/styleEmpty1280">style empty 1280</a>
    <a href="/debug/styleEmpty1024">style empty 1024</a>

        <br/>
        <br/>
        <br/>

    <a href="/dashboard-new" style="background-color: #1D7885">dashboard Corporate new</a>
    <a href="/simulations-new" style="background-color: #1D7885">simulations Corporate new</a>
    <a href="/profile-corporate-tariff-new" style="background-color: #1D7885">tariff Corporate new</a>
    <a href="/profile-corporate-company-info-new" style="background-color: #1D7885">company info Corporate new</a>

        <br/>
        <br/>
        <br/>

    <a href="/profile-corporate-user-info-new" style="background-color: #1D7885">user info Corporate new</a>
    <a href="/profile-corporate-password-new" style="background-color: #1D7885">Corporate password new</a>
    <a href="/profile-corporate-vacancies-new" style="background-color: #1D7885">Corporate vacavcies new</a>

        <br/>
        <br/>
        <br/>

    <a href="/product-new" style="background-color: #1D7885">Product new</a>
    <a href="/team-new" style="background-color: #1D7885">Team new</a>
    <a href="/form-errors-standard" style="background-color: #1D7885">form errors standard</a>
    <a href="/home-new" style="background-color: #1D7885">home standard</a>
    <a href="/old-browser-new" style="background-color: #1D7885">old browser standard</a>

        <br/>
        <br/>
        <br/>

    <a href="/static/tariffs-new" style="background-color: #1D7885">static tariffs-new</a>
    <a href="/order-new/starter" style="background-color: #1D7885">order "starter" tariff</a>

        <br/>
        <br/>
        <br/>

    <a href="/static/drag-and-drop">Drag & Drop prototype</a>

        <br/>
        <br/>
        <br/>

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

Yii version: <?= Yii::getVersion(); ?>

</header>
</div>

<br/>
<br/>
<br/>

<div style="float: none; clear: both; height: 100px;"></div>

<br/>
<br/>
<br/>
<br/>
<br/>
<br/>


