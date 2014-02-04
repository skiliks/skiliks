<div class="locator-invite-accept-popup hide invite-accept-popup">
    <h1 class="pull-content-center"><?= Yii::t('site', 'Simulation rules') ?></h1>

    <div class="row">
        <span class="unstandard-list-number">1.</span>
        <span class="unstandard-list-description unstandard-list-description-text">Прохождение симуляции занимает 2 часа.
            Всего с учётом подготовки и ознакомления с документами вам может
            потребоваться до 3 часов.</span>
    </div>

    <div class="row">
        <span class="unstandard-list-number pull-left">2.</span>
        <span class="unstandard-list-description unstandard-list-description-text">Cимуляцию остановить нельзя, пауз и перерывов не предусмотрено.
            Выберите удобное время для прохождения симуляции, чтобы вас никто не отвлекал.</span>
    </div>

    <div class="row">
        <span class="unstandard-list-number pull-left">3.</span>
        <span class="unstandard-list-description unstandard-list-description-text">До начала симуляции вам будет дополнительно предоставлено
            30 минут для ознакомления с командой, коллегами, поставленными задачами,
            имеющимися документами, а также интерфейсом игры.</span>
    </div>

    <div class="row margin-bottom-standard">
        <span class="unstandard-list-number pull-left">4.</span>
        <span class="unstandard-list-description unstandard-list-description-text">Не прибегайте к чужой помощи для прохождения симуляции.
            Это не поможет и сформирует вам соответствующую репутацию у работодателя.</span>
    </div>

    <br/>

    <h1 class="pull-content-center margin-bottom-standard">
        <?= Yii::t('site', 'Preparation for the simulation') ?>
    </h1>

    <div class="row unstandard-simulation-requirements unstandard-list-description-text">
        <span class="column-1-4 pull-content-center">
            <img src="<?= $this->assetsUrl.'/img/site/1280/popups/internet.png' ?>"><br/>
            <label>Интернет</label><br/>
            <span class="pull-content-left">
                Обеспечьте хорошее Интернет соединение.
                Симуляция не запустится при скорости менее 1Мб в секунду.
            </span>
        </span>

        <span class="column-1-4 pull-content-center">
            <img src="<?= $this->assetsUrl.'/img/site/1280/popups/browser.png' ?>"><br/>
            <label>Браузер</label><br/>
            <span class="pull-content-left">
                Убедитесь, что у вас установлен браузер
                Firefox (от 18 версии) или Chrome (от 22 версии).
                При необходимости установите последние версии данных браузеров.
            </span>
        </span>

        <span class="column-1-4 pull-content-center">
            <img src="<?= $this->assetsUrl.'/img/site/1280/popups/other-programs.png' ?>"><br/>
            <label>Лишние программы</label><br/>
            <span class="pull-content-left">
                Перед прохождением
                симуляции мы рекомендуем закрыть все приложения, кроме браузера.
            </span>
        </span>

        <span class="column-1-4 pull-content-center">
            <img src="<?= $this->assetsUrl.'/img/site/1280/popups/game-demo.png' ?>"><br/>
            <label>Демо</label><br/>
            <span class="pull-content-left">
                Пройдите демо-версию симуляции.
                Это позволит вам легче ориентироваться в новом интерфейсе.
            </span>
        </span>

    </div>

    <div class="row pull-content-center margin-bottom-standard">
        <!-- bigbtnsubmt accept-requirements  -->
        <a class='label background-dark-blue icon-circle-with-blue-arrow-big
               button-standard icon-padding-standard locator-start-later'>
            <?= Yii::t('site', 'Начать позже') ?>
        </a>

        <!-- bigbtnsubmt start-full-simulation start-simulation-from-popup -->
        <span class='label background-dark-blue icon-circle-with-blue-arrow-big
               button-standard icon-padding-standard inter-active
               locator-start-now action-open-full-simulation-popup'>
            <?= Yii::t('site', 'Начать сейчас') ?>
        </span>
    </div>
</div>
