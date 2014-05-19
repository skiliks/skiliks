<div class="extrasidepads">
    <div class="pull-content-center"><h2 class="total">Управленческие навыки</h2></div>
    <h3 class="resulttitele resulttitelevalue"><span class="valuetitle followPriorities"></span>1. Управление задачами с учётом приоритетов</h3>

    <div class="twobarstitles resultlabeltitle">
        <span class="barstitle">Уровень владения навыком</span>
        <span class="barstitle">Уровень проблем</span>
    </div>

    <?php $this->renderPartial($simulation->results_popup_partials_path."/_popup_js"); ?>

    <div class="clearfix-simulation-results mangrlresults">
        <div class="labels labels1">
            <div class="labelwrap">
                <div class="locator-learning-goal-description-hint hide
                    inner-popover background-yellow us-hint-assessment-type">
                    <div class="popover-triangle-upper"></div>
                    <div class="popover-wrapper">
                        <div class="popover-content">
                            Определение приоритетов между личными и рабочими задачами, учёт
                            данных приоритетов при выполнении задач. Способность влиять на постановку новых задач, в том
                            числе от руководства, основываясь на сформированных приоритетах, а также пересматривать
                            приоритеты в случае необходимости.
                        </div>
                    </div>
                </div>

                <span class="thelabel">
                    <div>
                        <span class="list-counter">1.1 </span>
                        <a href="#" class="list-text">Определение приоритетов</a>
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
                            Постановка всех текущих задач в план до начала их выполнения,
                            формирование реализуемого плана на день. Определение конкретного времени выполнения для всех
                            задач, в том числе долгосрочных
                        </div>
                    </div>
                </div>

                <span class="thelabel">
                    <div>
                        <span class="list-counter">1.2 </span>
                        <a href="#" class="list-text">Использование планирования в течение дня</a>
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
                            По всем текущим задачам постановка в план самой приоритетной задачи
                            на самое раннее время, по сравнению с менее приоритетной, при условии, что у задачи нет
                            фиксированного времени выполнения
                        </div>
                    </div>
                </div>

                <span class="thelabel">
                    <div>
                        <span class="list-counter">1.3 </span>
                        <a href="#" class="list-text">Правильное определение приоритетов задач при планировании</a>
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
                            Всегда выполняется самая приоритетная задача из всех известных на
                            данный момент
                        </div>
                    </div>
                </div>

                <span class="thelabel">
                    <div>
                        <span class="list-counter pull-left">1.4 </span>
                        <a href="#" class="list-text">
                            Выполнение задач с учётом приоритетов</a>
                    </div>
                </span>
                <a class="questn action-toggle-learning-goal-description-hint" href="#"></a>
            </div>

            <div class="labelwrap">
                <span class="thelabel">
                    <div>
                        <span class="list-counter pull-left">1.5 </span>
                        <a href="#" class="list-text">Завершение начатых задач</a>
                    </div>
                </span>
            </div>
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
            <div class="twocharts followPriorities-3">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
            <div class="twocharts followPriorities-4">
                <div class="chartbar"></div>
                <div class="chartproblem"></div>
            </div>
            <div class="twocharts followPriorities-5">
                <!--div class="chartbar"></div-->
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
        drawChartBlock('followPriorities', AR.management[1], ['1_1', '1_2', '1_3', '1_4', '1_5']);
        $('.valuetitle.followPriorities').html(Math.round(AR.management[1] && AR.management[1].total || 0) + '%');
    });
</script>