<div class="pull-content-center"><h2 class="total totalwithresult">Эффективность использования времени (детализация)<span class="value blockvalue time-total">0%</span></h2></div>
<div class="extrasidepads timedetail">
    <div class="timeblock good">
        <h2 class="resulttitele">Продуктивное время <small>(выполнение приоритетных задач, минуты)</small></h2>
        <div class="testtime"><strong>20</strong>%</div>

        <div class="clearfix-simulation-results">
            <div class="labels">
                <p class="labelwrap"><span class="thelabel">Работа с документами</span></p>
                <p class="labelwrap"><span class="thelabel">Встречи</span></p>
                <p class="labelwrap"><span class="thelabel">Звонки</span></p>
                <p class="labelwrap"><span class="thelabel">Работа с почтой</span></p>
                <p class="labelwrap"><span class="thelabel">Планирование</span></p>
            </div>
            <div class="barswrap timebars"></div>
        </div>

    </div>
    <div class="blockseprt"></div>
    <div class="timeblock bad">
        <h2 class="resulttitele">Непродуктивное время <small>(иные действия, не связанные с приоритетами, минуты)</small></h2>
        <div class="testtime"><strong>20</strong>%</div>

        <div class="clearfix-simulation-results">
            <div class="labels">
                <p class="labelwrap"><span class="thelabel">Работа с документами</span></p>
                <p class="labelwrap"><span class="thelabel">Встречи</span></p>
                <p class="labelwrap"><span class="thelabel">Звонки</span></p>
                <p class="labelwrap"><span class="thelabel">Работа с почтой</span></p>
                <p class="labelwrap"><span class="thelabel">Планирование</span></p>
            </div>
            <div class="barswrap timebars"></div>
        </div>
    </div>
</div>
<script>
    var good = [
            Math.round(AR.time['<?= TimeManagementAggregated::SLUG_1ST_PRIORITY_DOCUMENTS ?>']),
            Math.round(AR.time['<?= TimeManagementAggregated::SLUG_1ST_PRIORITY_MEETINGS ?>']),
            Math.round(AR.time['<?= TimeManagementAggregated::SLUG_1ST_PRIORITY_PHONE_CALLS ?>']),
            Math.round(AR.time['<?= TimeManagementAggregated::SLUG_1ST_PRIORITY_MAIL ?>']),
            Math.round(AR.time['<?= TimeManagementAggregated::SLUG_1ST_PRIORITY_PLANING ?>'])
        ],
        bad = [
            Math.round(AR.time['<?= TimeManagementAggregated::SLUG_NON_PRIORITY_DOCUMENTS ?>']),
            Math.round(AR.time['<?= TimeManagementAggregated::SLUG_NON_PRIORITY_MEETINGS ?>']),
            Math.round(AR.time['<?= TimeManagementAggregated::SLUG_NON_PRIORITY_PHONE_CALLS ?>']),
            Math.round(AR.time['<?= TimeManagementAggregated::SLUG_NON_PRIORITY_MAIL ?>']),
            Math.round(AR.time['<?= TimeManagementAggregated::SLUG_NON_PRIORITY_PLANING ?>'])
        ],
        goodPercents = Math.round(AR.time['<?= TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_1ST_PRIORITY_ACTIVITIES ?>']),
        badPercents = Math.round(AR.time['<?= TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_NON_PRIORITY_ACTIVITIES ?>']),
        goodMax = Math.max.apply(null, good),
        badMax = Math.max.apply(null, bad),
        i;

    for (i = 0; i < good.length; i++) {
        new charts.Bar('.timeblock.good .timebars', good[i], {max: goodMax, hideMax: true});
    }

    for (i = 0; i < bad.length; i++) {
        new charts.Bar('.timeblock.bad .timebars', bad[i], {max: badMax, hideMax: true, 'class': 'redbar'});
    }

    $('.good .testtime strong').html(goodPercents);
    $('.bad .testtime strong').html(badPercents);

    $('.time-total').html(Math.round(AR.time.total) + '%');
</script>