<div class="extrasidepads">
    <div class="textcener"><h2 class="total">Управленческие навыки</h2></div>
    <h3 class="resulttitele resulttitelevalue"><span class="valuetitle followPriorities"></span>1. Управление задачами с учётом приоритетов</h3>

    <div class="twobarstitles resultlabeltitle">
        <span class="barstitle">Уровень владения навыком</span>
        <span class="barstitle">Уровень проблем</span>
    </div>

    <div class="clearfix mangrlresults">
        <div class="labels labels1">
            <div class="labelwrap"><span class="thelabel"><p><span class="list-counter">1.1 </span><a href="#" class="list-link">Определение приоритетов</a></p></span></div>
            <div class="labelwrap"><span class="thelabel"><p><span class="list-counter">1.2 </span><a href="#" class="list-link">Использование планирования в течение дня</a></p></span></div>
            <div class="labelwrap"><span class="thelabel"><p><span class="list-counter">1.3 </span><a href="#" class="list-link">Правильное определение приоритетов задач при планировании</a></p></span></div>
            <div class="labelwrap"><span class="thelabel"><p><span class="list-counter">1.4 </span><a href="#" class="list-link">Выполнение задач в соответствии с приоритетами</a></p></span></div>
            <div class="labelwrap"><span class="thelabel"><p><span class="list-counter">1.5 </span><a href="#" class="list-link">Завершение начатых задач</a></p></span></div>
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
        var v1 = {positive: 20, negative: 20, total: 5}, /* AR.management[1] */
            v2 = 17; /* AR.management[2] */

        drawChartBlock('followPriorities', v1, ['123', '112']);
        drawChartBlock('taskManagement', v2, ['214a', '214b', '214d', '214g']);

        $('.valuetitle.followPriorities').html(Math.round(v1 && v1.total || 0) + '%');
        $('.valuetitle.taskManagement').html(  Math.round(v2 && v2.total || 0) + '%');
    });
</script>