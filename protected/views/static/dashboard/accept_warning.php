<div id="invite-accept-form" style="display: none;">
    <h2 class="title"><?= Yii::t('site', 'Предупреждение') ?></h2>
    <p>Для комфортного проходждения деловой симуляции вам понадобиться:</p>
    <ul>
        <li>Стабильный интернет со скоростью передачи данных от 1 Мб/с</li>
        <li>ПК с двухядерным процессором и 3 Гб оперативно памяти</li>
    </ul>
    <p>Если вы не уверены в скорости своего интернета или параметрах компьютера – мы рекомендуем вам закрыть, на время прохождения симуляции, не нужные открытые программы.</p>
    <p>Симуляция ориентирована на прохождение в браузере Firefox 18+ или Chrome 21+. Если у вас не установлен один из этих браузеров – то вам понадобиться загрузить и установить его.</p>
    <p>В данный момент вы пользуетесь <span class="browser-name"></span> версии <span class="browser-version"></span></p>
    <p><a class='blue-btn accept-requirements' href='#'><?= Yii::t('site', 'Продолжить') ?></a></p>
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