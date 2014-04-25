<div class="extrasidepads">
    <div class="pull-content-center"><h2 class="total">Управленческие навыки</h2></div>
    <h3 class="resulttitele resulttitelevalue"><span class="valuetitle taskManagement"></span>2. Управление людьми</h3>

    <div class="twobarstitles resultlabeltitle">
        <span class="barstitle">Уровень владения навыком</span>
        <span class="barstitle">Уровень проблем</span>
    </div>

    <div class="clearfix-simulation-results mangrlresults">
        <div class="labels">
            <div class="labelwrap">
                <div class="locator-learning-goal-description-hint hide
                    inner-popover background-yellow us-hint-assessment-type">
                    <div class="popover-triangle-upper"></div>
                    <div class="popover-wrapper">
                        <div class="popover-content">
                            Постановка сотрудникам задач, которые целесообразно делегировать,
                            самостоятельное выполнение тех задач, которые нецелесообразно делегировать. Предоставление
                            сотрудникам достаточной информации при постановке задачи и контроль выполнения
                            делегированной задачи
                        </div>
                    </div>
                </div>
                <span class="thelabel">
                    <div>
                        <span class="list-counter">2.1</span>
                        <span class="list-text">Использование делегирования для управления объемом задач</span>
                    </div>
                </span>
                <a class="questn action-toggle-learning-goal-description-hint" href="#"></a>
            </div>

            <div class="labelwrap">
                <div class="locator-learning-goal-description-hint hide
                    inner-popover background-yellow us-hint-assessment-type">
                    <div class="popover-triangle-upper"></div>
                    <div class="popover-wrapper">
                        <div class="popover-content">
                            Использование промежуточного контроля делегированной задачи в
                            необходимом и достаточном объёме в зависимости от квалификации и уровня мотивации
                            исполнителя
                        </div>
                    </div>
                </div>

                <span class="thelabel">
                    <div>
                        <span class="list-counter">2.2</span>
                        <span class="list-text">Управление ресурсами различной квалификации</span>
                    </div>
                </span>
                <a class="questn action-toggle-learning-goal-description-hint" href="#"></a>
            </div>

            <div class="labelwrap">
                <div class="locator-learning-goal-description-hint hide
                    inner-popover background-yellow us-hint-assessment-type">
                    <div class="popover-triangle-upper"></div>
                    <div class="popover-wrapper">
                        <div class="popover-content">
                            Предоставление конструктивной обратной связи, нацеленной на
                            изменение поведения
                        </div>
                    </div>
                </div>

                <span class="thelabel">
                    <div>
                        <span class="list-counter">2.3</span>
                        <span class="list-text">Использование обратной связи</span>
                    </div>
                </span>
                <a class="questn action-toggle-learning-goal-description-hint" href="#"></a>
            </div>
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
        </div>
    </div>

    <div class="legendwrap legendmargin lessmargintop">
        <div class="legend">
            <p class="barstitle">Обозначения</p>

            <div class="legendvalue">
                <span class="legendcolor colormax"></span>
                <span class="legendtitle">Максимальный уровень владения навыком</span>
            </div>
            <div class="legendvalue">
                <span class="legendcolor colordone"></span>
                <span class="legendtitle">Проявленный уровень владения навыком</span>
            </div>
            <div class="legendvalue">
                <span class="legendcolor colorwarn"></span>
                <span class="legendtitle">Уровень продемонстрированного ошибочного поведения</span>
            </div>
        </div>
    </div>

</div>
<script type="text/javascript">
    $(function() {
        drawChartBlock('taskManagement', AR.management[2], ['2_1', '2_2', '2_3']);

        $('.valuetitle.taskManagement').html(Math.round(AR.management[2] && AR.management[2].total || 0) + '%');
    });
</script>