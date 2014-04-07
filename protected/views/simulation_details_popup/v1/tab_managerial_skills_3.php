<div class="extrasidepads">
    <div class="pull-content-center"><h2 class="total">Управленческие навыки</h2></div>

    <h3 class="resulttitele resulttitelevalue"><span class="valuetitle peopleManagement"></span>3. Управление коммуникациями</h3>

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
                            Выбор оптимального канала коммуникации для постановки задач,
                            вопросов и обсуждений, принятия решений, обмена информацией, в зависимости от срочности,
                            сложности, количества участников и т.д.
                        </div>
                    </div>
                </div>

                <span class="thelabel">
                    <div>
                        <span class="list-counter">3.1</span>
                        <span class="list-text">Оптимальное использование каналов коммуникации</span>
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
                            Использование минимально необходимого времени на работу с почтой и
                            получение всей важной информации, выполнение поступивших из почты задач.
                            Корректное написание писем
                        </div>
                    </div>
                </div>

                <span class="thelabel">
                    <div>
                        <span class="list-counter">3.2</span>
                        <span class="list-text">Эффективная работа с почтой</span>
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
                            Стабильный приём звонков по важным вопросам и отсутствие траты
                            времени на разговоры по неприоритетным вопросам
                        </div>
                    </div>
                </div>

                <span class="thelabel">
                    <div>
                        <span class="list-counter">3.3</span>
                        <span class="list-text">Эффективная работа со звонками</span>
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
                            Участие только в приоритетных встречах, планирование встреч заранее.
                            Выполнение поступивших на встрече задач в соответствии с их приоритетами, фиксирование
                            результатов встреч
                        </div>
                    </div>
                </div>

                <span class="thelabel">
                    <div>
                        <span class="list-counter">3.4</span>
                        <span class="list-text">Эффективное управление встречами</span>
                    </div>
                </span>
                <a class="questn action-toggle-learning-goal-description-hint" href="#"></a>
            </div>
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
        var v3 = AR.management[3];

        drawChartBlock('peopleManagement', v3, ['3_1', '3_2', '3_3', '3_4']);

        $('.valuetitle.peopleManagement').html(   Math.round(v3 && v3.total || 0) + '%');
    });
</script>