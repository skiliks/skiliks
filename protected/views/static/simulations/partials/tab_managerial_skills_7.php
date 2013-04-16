<div class="extrasidepads">
    <div class="textcener"><h2 class="total">Управленческие навыки</h2></div>
    <h3 class="resulttitele resulttitelevalue"><span class="valuetitle meetManagement"></span>7. Эффективная работа со встречами</h3>

    <div class="twobarstitles resultlabeltitle">
        <span class="barstitle">Уровень владения навыком</span>
        <span class="barstitle">Уровень проблем</span>
    </div>

    <div class="clearfix mangrlresults">
        <div class="labels">
            <p class="labelwrap"><span class="thelabel">7.1 Управление временем, затрачиваемым на встречи</span></p>
            <p class="labelwrap"><span class="thelabel">7.2 Целесообразный прием посетителей</span></p>
            <p class="labelwrap"><span class="thelabel">7.3 Эффективная обработка результатов встречи</span></p>
        </div>
        <div class="barswrap">
            <div class="twocharts meetManagement-1">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
            <div class="twocharts meetManagement-2">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
            <div class="twocharts meetManagement-3">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
        </div>
    </div>


    <div class="legendwrap legendmargin">
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
        var v7 = AR.management[7];

        drawChartBlock('meetManagement', v7, ['351a', '351b', '351c']);
        $('.valuetitle.meetManagement').html(Math.round(v7 && v7.total || 0) + '%');
    });
</script>