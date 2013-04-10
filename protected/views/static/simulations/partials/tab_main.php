<style>
    /*.gauge-charts, */ .bar-charts, .bullet-charts {
        margin: 40px 0;
        padding: 10px;
    }
    .chart-gauge.inline {
        margin: 30px 0;
    }
    .chart-bar {
        margin: 20px 0;
    }
    .chart-bullet.small {
        width: 300px;
        margin: 20px 0;
    }
</style>
<div class="textcener"><h2 class="total">Итоговый рейтинг менеджера</h2></div>
<div class="allsummry">
    <div class="estmtresults">
        <div class="overall">
            <span class="allratebg"><span class="allrating" style="width:30%"></span></span> <span class="blockvalue"><span class="value"></span>%</span>
            <div class="allseprtwrap">
                <div class="ratepercnt uprnavprcnt">50%</div>
                <div class="ratepercnt resultprcnt">30%</div>
                <div class="ratepercnt timeprcnt">20%</div>
            </div>
        </div>
    </div><!-- /estmtresults -->
    <div class="estmtileswrap">
        <div class="widthblock"><h2><a href="#">Управленческие навыки <span class="signmore"></span></a></h2></div>
        <div class="widthblock"><h2><a href="#">Результативность <span class="signmore"></span></h2></a></div>
        <div class="widthblock"><h2><a href="#">Эффективность использования времени <span class="signmore"></span></a></h2></div>
        <div class="widthblock"><h2><a href="#">Личностные характеристики <span class="signmore"></span></a></h2></div>
    </div><!-- /estmtileswrap -->
</div>
<div class="clearfix maincharts">
    <div class="gauge-charts">

    </div>
    <div class="bullet-charts">

    </div>
</div>

<div class="levellabels">
    <div class="widthblock"><h3>Уровень владения навыками</h3></div>
    <div class="widthblock"><h3>Уровень достижения результатов: количество и значимость выполненных задач</h3></div>
    <div class="widthblock"><h3>Скорость достижения результатов</h3></div>
    <div class="widthblock"><h3>Личностные качества, проявленные в симуляции</h3></div>
</div>
<div class="rateslist">
    <div class="widthblock"><h3>ОЦЕНИВАЕМЫЕ НАВЫКИ</h3>
        <ol class="bluelist numlist">
            <li><a href="#managerial-skills-1-2" data-parent="managerial-skills">Следование приоритетам</a></li>
            <li><a href="#managerial-skills-1-2" data-parent="managerial-skills">Управление задачами</a></li>
            <li><a href="#managerial-skills-3-4" data-parent="managerial-skills">Управление людьми</a></li>
            <li><a href="#managerial-skills-3-4" data-parent="managerial-skills">Оптимальный выбор каналов коммуникации</a></li>
            <li><a href="#managerial-skills-5-6" data-parent="managerial-skills">Эффективная работа с почтой</a></li>
            <li><a href="#managerial-skills-5-6" data-parent="managerial-skills">Эффективное управление звонками</a></li>
            <li><a href="#managerial-skills-7" data-parent="managerial-skills">Эффективное управление встречами</a></li>
        </ol>
    </div>
    <div class="widthblock"></div>
    <div class="widthblock"><h3>ПОКАЗАТЕЛИ</h3>
        <ul class="bluelist nobultslist">
            <li><a href="#time-management-detail" data-parent="time-management">Распределение времени</a></li>
            <li><a href="#">Сверхурочное время </a></li>
        </ul>
    </div>
    <div class="widthblock"><h3>ИЗМЕРЯЕМЫЕ ХАРАКТЕРИСТИКИ</h3>
        <ul class="bluelist nobultslist">
            <li><a href="#personal-qualities">Ориентация на результат</a></li>
            <li><a href="#personal-qualities">Внимательность</a></li>
            <li><a href="#personal-qualities">Ответственность</a></li>
            <li><a href="#personal-qualities">Устойчивость к манипуляциям и давлению</a></li>
            <li><a href="#personal-qualities">Конструктивность</a></li>
            <li><a href="#personal-qualities">Гибкость</a></li>
            <li><a href="#personal-qualities">Принятие решений</a></li>
            <li><a href="#personal-qualities">Стрессоустойчивость</a></li>
        </ul>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        new charts.Gauge('.gauge-charts', parseInt(assessmentResult['management']['total']), {class: 'inline'});
        new charts.Gauge('.gauge-charts', parseInt(assessmentResult['performance']['total']), {class: 'inline'});
        new charts.Gauge('.gauge-charts', parseInt(assessmentResult['time']['total']), {class: 'inline'});

        new charts.Bullet('.bullet-charts', 50, {class: 'small'});
        new charts.Bullet('.bullet-charts', 70, {class: 'small'});
        new charts.Bullet('.bullet-charts', 40, {class: 'small'});

        //new charts.Bullet('.bullet-charts', 50, {class: 'small'});
        //new charts.Bullet('.bullet-charts', 70, {class: 'small'});
       // new charts.Bullet('.bullet-charts', 40, {class: 'small'});
        $('.overall .value').html(assessmentResult['overall']);
    });


</script>