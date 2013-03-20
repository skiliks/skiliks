
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
<?php if ($this->user->isHasAccount()): ?>
    Тип Вашего аккаунта "<?php echo $this->user->getAccountType() ?>".
<?php else: ?>
    У Вас не выбран тип аккаунта.
<?php endif; ?>

<?php if ($this->user->getAccount() instanceof UserAccountCorporate): ?>
    <br>
    <br>
    <strong>Корпоративный e-mail: </strong>
        <?php echo $this->user->getAccount()->corporate_email?>
        <?php if ($this->user->getAccount()->is_corporate_email_verified): ?>
            (верифицирован)
        <?php else: ?>
            (не верифицирован)
        <?php endif; ?>
        .
<?php endif; ?>

<br>
<br>
<nav>
    <?php if (false === $this->user->isHasAccount()): ?>
        <a href="/registration/choose-account-type">
            <?php echo Yii::t('site', 'Choose account type') ?>
        </a>
    <?php endif; ?>

    <a href="/simulation/promo">Начать симуляцию в режиме promo</a>

    <?php if ($this->user->getAccount() instanceof UserAccountCorporate): ?>
    <a href="/dashboard">
        <?php echo Yii::t('site', 'Dashboard') ?>
    </a>
    <?php endif; ?>
</nav>
<br>
<br>
<br>
<br>
<?php if ($user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)): ?>
    Вы имеет доступ к опциям режима разработчика:

    <br><br>

    <nav>

    <?php if (false === $this->user->isHasAccount()): ?>
        <a href="/registration/choose-account-type">
            <?php echo Yii::t('site', 'Choose account type') ?>
        </a>
    <?php else: ?>
        <a href="/registration/cleanUpAccount">
            <?php echo Yii::t('site', 'Reset account type') ?>
        </a>
    <?php endif; ?>

    <a href="/simulation/developer">Начать симуляцию (lite) в режиме developer</a>
    <a href="/simulation/developer?type=1">Начать симуляцию (full) в режиме developer</a>

        <br>
        <br>
        <br>

<a href="/admin/dialogsAnalyzer">Открыть анализатор диалогов БД</a>

        <br>
        <br>
        <br>

<a href="/admin/uploadDialogsToAnalyzer">Открыть анализатор диалогов произвольного ексел-файла</a>

        <br>
        <br>
        <br>

<a href="/admin">Старая "админка" - отображение таблиц с логами</a>

    </nav>

<?php endif ?>


<br>
<br>

    </header>
    </div>