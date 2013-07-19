<div id="invite-accept-form" style="display: none;">
    <h2 class="title"><?= Yii::t('site', 'Simulation rules') ?></h2>

    <ul class="list-ordered">
        <li><strong class="grid-cell">1</strong><p class="grid-cell">Прохождение симуляции занимает 2 часа.  Всего с учётом подготовки и ознакомления с документами вам может потребоваться до 3 часов.</p></li>
        <li><strong class="grid-cell">2</strong><p class="grid-cell">Прохождение симуляции занимает 2 часа.  Всего с учётом подготовки и ознакомления с документами вам может потребоваться до 3 часов.</p></li>
        <li><strong class="grid-cell">3</strong><p class="grid-cell">До начала симуляции вам будет дополнительно предоставлено 30 минут для ознакомления с командой, коллегами, поставленными задачами, имеющимися документами, а также интерфейсом игры.</p></li>
        <li><strong class="grid-cell">4</strong><p class="grid-cell">Не прибегайте к чужой помощи для прохождения симуляции. Это не поможет и сформирует вам соответствующую репутацию у работодателя. </p></li>
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