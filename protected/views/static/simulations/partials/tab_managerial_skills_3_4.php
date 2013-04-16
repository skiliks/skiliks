<div class="extrasidepads">
    <div class="textcener"><h2 class="total">Управленческие навыки</h2></div>
    <h3 class="resulttitele resulttitelevalue"><span class="valuetitle peopleManagement"></span>3. Управление людьми</h3>

    <div class="twobarstitles resultlabeltitle">
        <span class="barstitle">Уровень владения навыком</span>
        <span class="barstitle">Уровень проблем</span>
    </div>

    <div class="clearfix mangrlresults">
        <div class="labels">
            <p class="labelwrap"><span class="thelabel">3.1 Использование делегирования для управления объемом задач</span></p>
            <p class="labelwrap"><span class="thelabel">3.2 Управление ресурсами различной квалификации</span></p>
            <p class="labelwrap"><span class="thelabel">3.3 Использование обратной связи</span></p>
            <p class="labelwrap"><span class="thelabel">3.4 Делегирование задачи оптимальному сотруднику</span></p>
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
            <div class="twocharts peopleManagement-4">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
        </div>
    </div>

    <h3 class="resulttitele resulttitelevalue"><span class="valuetitle communication"></span>4. Оптимальный выбор каналов коммуникации</h3>

    <div class="twobarstitles resultlabeltitle">
        <span class="barstitle">Уровень владения навыком</span>
        <span class="barstitle">Уровень проблем</span>
    </div>

    <div class="clearfix mangrlresults">
        <div class="labels">
            <p class="labelwrap"><span class="thelabel">4.1  Оптимальное использование почты</span></p>
            <p class="labelwrap"><span class="thelabel">4.2 Оптимальное использование звонков</span></p>
            <p class="labelwrap"><span class="thelabel">4.3 Оптимальное использование встреч</span></p>
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