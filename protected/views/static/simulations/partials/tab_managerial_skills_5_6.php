<div class="extrasidepads">
    <div class="textcener"><h2 class="total">Управленческие навыки</h2></div>
    <h3 class="resulttitele resulttitelevalue"><span class="valuetitle mailManagement"></span>5. Эффективная работа с почтой</h3>

    <div class="twobarstitles resultlabeltitle">
        <span class="barstitle">Уровень владения навыком</span>
        <span class="barstitle">Уровень проблем</span>
    </div>

    <div class="clearfix mangrlresults">
        <div class="labels">
            <p class="labelwrap"><span class="thelabel">5.1 Управление временем, затрачиваемым на почту</span></p>
            <p class="labelwrap"><span class="thelabel">5.2 Эффективная обработка входящих писем</span></p>
            <p class="labelwrap"><span class="thelabel">5.3 Создание информативных и экономных сообщений </span></p>
        </div>
        <div class="barswrap">
            <div class="twocharts mailManagement-1">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
            <div class="twocharts mailManagement-2">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
            <div class="twocharts mailManagement-3">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
        </div>
    </div>

    <h3 class="resulttitele resulttitelevalue"><span class="valuetitle phoneManagement"></span>6. Эффективная работа со звонками</h3>

    <div class="twobarstitles resultlabeltitle">
        <span class="barstitle">Уровень владения навыком</span>
        <span class="barstitle">Уровень проблем</span>
    </div>

    <div class="clearfix mangrlresults">
        <div class="labels">
            <p class="labelwrap"><span class="thelabel">6.1 Управление временем, затрачиваемым на звонки</span></p>
            <p class="labelwrap"><span class="thelabel">6.2 Целесообразный прием входящих звонков</span></p>
            <p class="labelwrap"><span class="thelabel">6.3 Эффективная обработка входящих звонков</span></p>
        </div>
        <div class="barswrap">
            <div class="twocharts phoneManagement-1">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
            <div class="twocharts phoneManagement-2">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
            <div class="twocharts phoneManagement-3">
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
        var v5 = AR.management[5],
            v6 = AR.management[6];

        drawChartBlock('mailManagement', v5, ['331', '332', '333']);
        drawChartBlock('phoneManagement', v6, ['341a', '341b', '341c']);

        $('.valuetitle.mailManagement').html( Math.round(v5 && v5.total || 0) + '%');
        $('.valuetitle.phoneManagement').html(Math.round(v6 && v6.total || 0) + '%');
    });
</script>