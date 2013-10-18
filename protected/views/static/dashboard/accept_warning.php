<div id="invite-accept-form" style="display: none;">
    <h2 class="title"><?= Yii::t('site', 'Simulation rules') ?></h2>

    <ul class="list-ordered">
        <li><strong class="grid-cell">1.</strong><p class="grid-cell">Прохождение симуляции занимает 2 часа.  Всего с учётом подготовки и ознакомления с документами вам может потребоваться до 3 часов.</p></li>
        <li><strong class="grid-cell">2.</strong><p class="grid-cell">Cимуляцию остановить нельзя, пауз и перерывов не предусмотрено. Выберите удобное время для прохождения симуляции, чтобы вас никто не отвлекал.</p></li>
        <li><strong class="grid-cell">3.</strong><p class="grid-cell">До начала симуляции вам будет дополнительно предоставлено 30 минут для ознакомления с командой, коллегами, поставленными задачами, имеющимися документами, а также интерфейсом игры.</p></li>
        <li><strong class="grid-cell">4.</strong><p class="grid-cell">Не прибегайте к чужой помощи для прохождения симуляции. Это не поможет и сформирует вам соответствующую репутацию у работодателя. </p></li>
    </ul>
    <h2 class="title"><?= Yii::t('site', 'Preparation for the simulation') ?> </h2>

    <div class="container-4">
        <div class="grid1"><div class="popup-icons icon-inet"></div><h4>Интернет</h4><p class="line-xheight font-large">Обеспечьте хорошее Интернет соединение. Симуляция не запустится при скорости менее 1Мб в секунду.</p></div>
        <div class="grid1"><div class="popup-icons icon-browser"></div><h4>Браузер</h4><p class="line-xheight font-large">Убедитесь, что у вас установлен браузер Firefox (от 18 версии) или Chrome (от 22 версии). При необходимости установите последние версии данных браузеров.</p></div>
        <div class="grid1"><div class="popup-icons icon-progs"></div><h4>Лишние программы</h4><p class="line-xheight font-large">Перед прохождением симуляции мы рекомендуем закрыть все приложения, кроме браузера.</p></div>
        <div class="grid1"><div class="popup-icons icon-demo"></div><h4>Демо</h4><p class="line-xheight font-large">Пройдите демо-версию симуляции. Это позволит вам легче ориентироваться в новом интерфейсе.</p></div>
    </div>

    <div class="text-center accept-requirements-div">
        <a class='bigbtnsubmt accept-requirements' href='#'><?= Yii::t('site', 'Начать позже') ?></a>
        <a class='bigbtnsubmt start-full-simulation start-simulation-from-popup' href='#'><?= Yii::t('site', 'Начать сейчас') ?></a>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        var browserNames = {
            'chrome':  'Google Chrome',
            'mozilla': 'Mozilla Firefox',
            'safari':  'Apple Safari',
            'opera':   'Opera',
            'msie':    'Internet Explorer'
        };

        for (var code in browserNames) {
            if ($.browser[code]) {
                $('.browser-name').html(browserNames[code]);
                $('.browser-version').html($.browser.version);
                break;
            }
        }
    });
</script>