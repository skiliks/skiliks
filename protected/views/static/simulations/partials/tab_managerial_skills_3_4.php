<div class="extrasidepads">
    <div class="textcener"><h2 class="total">Управленческие навыки</h2></div>
    <h3 class="resulttitele resulttitelevalue"><span class="valuetitle peopleManagement"></span>2. Управление людьми</h3>

    <div class="twobarstitles resultlabeltitle">
        <span class="barstitle">Уровень владения навыком</span>
        <span class="barstitle">Уровень проблем</span>
    </div>

    <div class="clearfix mangrlresults">
        <div class="labels">
            <div class="labelwrap"><span class="thelabel"><div><span class="list-counter">2.1</span><span class="list-text">Использование делегирования для управления объемом задач</span></div></span></div>
            <div class="labelwrap"><span class="thelabel"><div><span class="list-counter">2.2</span><span class="list-text">Управление ресурсами различной квалификации</span></div></span></div>
            <div class="labelwrap"><span class="thelabel"><div><span class="list-counter">2.3</span><span class="list-text">Использование обратной связи</span></div></span></div>
        </div>
        <div class="barswrap">
            <div class="twocharts peopleManagement-1">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
            <div class="twocharts peopleManagement-2">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
            <div class="twocharts peopleManagement-3">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
        </div>
    </div>

    <h3 class="resulttitele resulttitelevalue"><span class="valuetitle communication"></span>3. Управление коммуникациями</h3>

    <div class="twobarstitles resultlabeltitle">
        <span class="barstitle">Уровень владения навыком</span>
        <span class="barstitle">Уровень проблем</span>
    </div>

    <div class="clearfix mangrlresults">
        <div class="labels">
            <div class="labelwrap"><span class="thelabel"><div><span class="list-counter">3.1</span><span class="list-text">Оптимальное использование каналов коммуникации</span></div></span></div>
            <div class="labelwrap"><span class="thelabel"><div><span class="list-counter">3.2</span><span class="list-text">Эффективная работа с почтой</span></div></span></div>
            <div class="labelwrap"><span class="thelabel"><div><span class="list-counter">3.3</span><span class="list-text">Эффективная работа со звонками</span></div></span></div>
            <div class="labelwrap"><span class="thelabel"><div><span class="list-counter">3.4</span><span class="list-text">Эффективное управление встречами</span></div></span></div>
        </div>
        <div class="barswrap">
            <div class="twocharts communication-1">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
            <div class="twocharts communication-2">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
            <div class="twocharts communication-3">
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
        var v3 = AR.management[3],
            v4 = AR.management[4];

        drawChartBlock('peopleManagement', v3, ['412', '414', '415', '413']);
        drawChartBlock('communication', v4, ['3214', '3216', '3218']);

        $('.valuetitle.peopleManagement').html(Math.round(v3 && v3.total || 0) + '%');
        $('.valuetitle.communication').html(   Math.round(v4 && v4.total || 0) + '%');
    });
</script>