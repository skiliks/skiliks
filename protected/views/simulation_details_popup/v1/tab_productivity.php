<?php /** @var Simulation $simulation */ ?>

<div class="section">
    <div class="pull-content-center"><h2 class="total totalwithresult">Результативность <span class="value blockvalue productivity-total"></span></h2></div>

    <p class="barstitle resultlabeltitle">Уровень выполнения задач</p>
    <div class="clearfix-simulation-results">
        <div class="labels">
            <div class="row">

                <div class="locator-learning-goal-description-hint hide
                    inner-popover background-yellow us-hint-assessment-type">
                    <div class="popover-triangle-upper"></div>
                    <div class="popover-wrapper">
                        <div class="popover-content">
                            Выполнение форс-мажорных задач, отложить которые невозможно.
                        </div>
                    </div>
                </div>

                <h4 class="resulttitele smallerfont">Срочно</h4>
                <a href="#" class="questn action-toggle-learning-goal-description-hint"></a>
            </div>

            <div class="row">

                <div class="locator-learning-goal-description-hint hide
                    inner-popover background-yellow us-hint-assessment-type">
                    <div class="popover-triangle-upper"></div>
                    <div class="popover-wrapper">
                        <div class="popover-content">
                            Выполнение задач, значимых для компании в целом или для подразделения, которые должны быть
                            обязательно сделаны сегодня.
                        </div>
                    </div>
                </div>

                <h4 class="resulttitele smallerfont">Высокий приоритет</h4>
                <a href="#" class="questn action-toggle-learning-goal-description-hint"></a>
            </div>

            <div class="row">
                <div class="locator-learning-goal-description-hint hide
                    inner-popover background-yellow us-hint-assessment-type">
                    <div class="popover-triangle-upper"></div>
                    <div class="popover-wrapper">
                        <div class="popover-content">
                            Выполнение задач, значимых для компании в целом или для подразделения, со сроком исполнения
                            в ближайшие дни.
                        </div>
                    </div>
                </div>

                <h4 class="resulttitele smallerfont">Средний приоритет</h4>
                <a href="#" class="questn action-toggle-learning-goal-description-hint"></a>
            </div>

            <div class="row">
                <div class="locator-learning-goal-description-hint hide
                    inner-popover background-yellow us-hint-assessment-type">
                    <div class="popover-triangle-upper"></div>
                    <div class="popover-wrapper">
                        <div class="popover-content">
                            Задачи, выполнение которых занимает не более 2 минут.
                        </div>
                    </div>
                </div>

                <h4 class="resulttitele smallerfont">Двухминутные задачи</h4>
                <a href="#" class="questn action-toggle-learning-goal-description-hint"></a>
            </div>
        </div>

        <!-- полосы прогресса с оценками за резкльтативность -->
        <div class="bars barswrap"></div>

    </div>
    <div class="legendwrap resultslegend">
        <div class="legend">
            <p class="barstitle">Обозначения</p>
            <div class="legendvalue shortlegend">
                <span class="legendcolor colormax"></span>
                <span class="legendtitle">Максимальный уровень выполнения задач</span>
            </div>
            <div class="legendvalue">
                <span class="legendcolor colordone"></span>
                <span class="legendtitle">Проявленный уровень выполнения задач</span>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        var result = AR.performance,
            r = Math.round,
            renderer = function(v) { return v + '%';},
            categories = ['0', '1', '2', '2_min'];

        for (var i = 0; i < categories.length; i++) {
            new charts.Bar('.bars', r(result[categories[i]] || 0), { valueRenderer: renderer });
        }

        $('.productivity-total').html(Math.round(result.total || 0) + '%');
    });
</script>