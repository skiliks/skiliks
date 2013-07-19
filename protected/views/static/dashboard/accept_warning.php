<div id="invite-accept-form" style="display: none;">
    <h2 class="title"><?= Yii::t('site', 'Simulation rules') ?></h2>

    <ul>
        <li>Прохождение симуляции занимает 2 часа.  Всего с учётом подготовки и ознакомления с документами вам может потребоваться до 3 часов.</li>
        <li>Прохождение симуляции занимает 2 часа.  Всего с учётом подготовки и ознакомления с документами вам может потребоваться до 3 часов.</li>
        <li>До начала симуляции вам будет дополнительно предоставлено 30 минут для ознакомления с командой, коллегами, поставленными задачами, имеющимися документами, а также интерфейсом игры.</li>
        <li>Не прибегайте к чужой помощи для прохождения симуляции. Это не поможет и сформирует вам соответствующую репутацию у работодателя. </li>
    </ul>


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