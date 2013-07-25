<div class="extrasidepads">
    <div class="textcener"><h2 class="total">Управленческие навыки</h2></div>
    <h3 class="resulttitele resulttitelevalue"><span class="valuetitle followPriorities"></span>1. Управление задачами с учётом приоритетов</h3>

    <div class="twobarstitles resultlabeltitle">
        <span class="barstitle">Уровень владения навыком</span>
        <span class="barstitle">Уровень проблем</span>
    </div>

    <div class="clearfix mangrlresults">
        <div class="labels">
            <p class="labelwrap"><span class="thelabel"><a href="#">1.1 Определение приоритетов</a></span></p>
            <p class="labelwrap"><span class="thelabel"><a href="#">1.2 Использование планирования в течение дня</a></span></p>
            <p class="labelwrap"><span class="thelabel"><a href="#">1.3 Правильное определение приоритетов задач при планировании</a></span></p>
            <p class="labelwrap"><span class="thelabel"><a href="#">1.4 Выполнение задач в соответствии с приоритетами</a></span></p>
            <p class="labelwrap"><span class="thelabel"><a href="#">1.5 Завершение начатых задач</a></span></p>
        </div>
        <div class="barswrap">
            <div class="twocharts followPriorities-1">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
            <div class="twocharts followPriorities-2">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
            <div class="twocharts">
                <div class="chartbar"><div class="chart-bar" style="width: 100%;"><span class="chart-value">30%</span></div></div>
                <div class="chartproblem"><div class="chart-bar redbar" style="width: 100%;"><span class="chart-value" style="width: 10%;">10%</span></div></div>
            </div>
        </div>
    </div>

    <div class="legendwrap legendmargin lessmargintop">
        <div class="legend">
            <p class="barstitle">Обозначения</p>
            <div class="legendvalue"><span class="legendcolor colormax"></span><span class="legendtitle">Максимальный уровень владения навыком</span></div>
            <div class="legendvalue"><span class="legendcolor colordone"></span><span class="legendtitle">Проявленный уровень владения навыком</span></div>
            <div class="legendvalue"><span class="legendcolor colorwarn"></span><span class="legendtitle">Уровень продемонстрированного ошибочного поведения</span></div>
        </div>
    </div>

</div>
<script type="text/javascript">
    $(function() {
        var v1 = 15, /* AR.management[1] */
            v2 = 17; /* AR.management[2] */

        drawChartBlock('followPriorities', v1, ['123', '112']);
        drawChartBlock('taskManagement', v2, ['214a', '214b', '214d', '214g']);

        $('.valuetitle.followPriorities').html(Math.round(v1 && v1.total || 0) + '%');
        $('.valuetitle.taskManagement').html(  Math.round(v2 && v2.total || 0) + '%');
    });
</script>