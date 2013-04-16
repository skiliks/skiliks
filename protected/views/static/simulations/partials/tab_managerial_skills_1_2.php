<div class="extrasidepads">
    <div class="textcener"><h2 class="total">Управленческие навыки</h2></div>
    <h3 class="resulttitele resulttitelevalue"><span class="valuetitle followPriorities"></span>1. Следование приоритетам</h3>

    <div class="twobarstitles resultlabeltitle">
        <span class="barstitle">Уровень владения навыком</span>
        <span class="barstitle">Уровень проблем</span>
    </div>

    <div class="clearfix mangrlresults">
        <div class="labels">
            <p class="labelwrap"><span class="thelabel">1.1 Следование целям подразделения</span></p>
            <p class="labelwrap"><span class="thelabel"><a href="#">1.2 Следование личным приоритетам</a></span></p>
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

    <h3 class="resulttitele resulttitelevalue"><span class="valuetitle taskManagement"></span>2. Управление задачами</h3>

    <div class="twobarstitles resultlabeltitle">
        <span class="barstitle">Уровень владения навыком</span>
        <span class="barstitle">Уровень проблем</span>
    </div>

    <div class="clearfix mangrlresults">
        <div class="labels">
            <p class="labelwrap"><span class="thelabel">2.1 Использование планирования в течение дня</span></p>
            <p class="labelwrap"><span class="thelabel">2.2 Правильное определение приоритетов задач при планировании</span></p>
            <p class="labelwrap"><span class="thelabel">2.3 Выполнение задач в соответствии с приоритетами</span></p>
            <p class="labelwrap"><span class="thelabel">2.4 Завершение начатых задач <span class="helpbuble">Описание навыка</span></span></p>
        </div>
        <div class="barswrap">
            <div class="twocharts taskManagement-1">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
            <div class="twocharts taskManagement-2">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
            <div class="twocharts taskManagement-3">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
            <div class="twocharts taskManagement-4">
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
        var v1 = AR.management[1],
            v2 = AR.management[2];

        drawChartBlock('followPriorities', v1, ['123', '112']);
        drawChartBlock('taskManagement', v2, ['214a', '214b', '214d', '214g']);

        $('.valuetitle.followPriorities').html(Math.round(v1 && v1.total || 0) + '%');
        $('.valuetitle.taskManagement').html(  Math.round(v2 && v2.total || 0) + '%');
    });
</script>