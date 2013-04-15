<div class="textcener"><h2 class="total totalwithresult">Эффективность использования времени <span class="value blockvalue time-total">0%</span></h2></div>
<div class="extrasidepads">
    <div class="timeblock">
        <h2 class="resulttitele"><a href="#time-management-detail">Распределение времени <span class="signmore"></span></a></h2>

        <div class="time-distribution"></div>

        <div class="legendwrap">
            <div class="legend">
                <p class="barstitle">Обозначения</p>
                <div class="legendvalue"><span class="legendcolor colormax"></span><span class="legendtitle">Действия, относящиеся к задачам 1 приоритета</span></div>
                <div class="legendvalue"><span class="legendcolor colorwarn"></span><span class="legendtitle">Время ожидания</span></div>
                <div class="legendvalue"><span class="legendcolor colordone"></span><span class="legendtitle">Действия, не относящиеся к задачам 1 приоритета</span></div>
            </div>
        </div>
    </div>
    <div class="blockseprt"></div>
    <div class="timeblock">
        <h2 class="resulttitele">Сверхурочное время</h2>
        <div class="over-time"></div>
    </div>
</div>

<script>
$(function() {
    var time = AR.time,
        overtime = time['<?= TimeManagementAggregated::SLUG_WORKDAY_OVERHEAD_DURATION?>'],
        r = Math.round;

    new charts.Pie('.time-distribution',
        [
            time['<?= TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_1ST_PRIORITY_ACTIVITIES ?>'],
            time['<?= TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_NON_PRIORITY_ACTIVITIES ?>'],
            time['<?= TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_INACTIVITY ?>']
        ],
        {
            colors: ['#146672', '#e11a1a', '#66a3ab']
        }
    );

    new charts.Pie('.over-time',
        [
            Math.min(30, overtime),
            Math.max(0, Math.min(30, overtime - 30)),
            Math.max(0, overtime - 60)
        ],
        {
            total: 120,
            donut: true,
            hideLabels: true,
            scaled: true,
            colors: ['#46b14a', '#f9e819', '#e11a1a'],
            bgColor: '#3d4041'
        }
    );

    $('.time-total').html(r(time.total) + '%');
});
</script>