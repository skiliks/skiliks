<div class="textcener"><h2 class="total">{Yii::t('site', 'Overall manager’s rating')}</h2></div>
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
        <div class="widthblock"><h2><a href="#managerial-skills">{Yii::t('site', 'Managerial skills')} <span class="signmore"></span></a></h2></div>
        <div class="widthblock"><h2><a href="#productivity">{Yii::t('site', 'Productivity')} <span class="signmore"></span></h2></a></div>
        <div class="widthblock"><h2><a href="#time-management">{Yii::t('site', 'Time management effectiveness')} <span class="signmore"></span></a></h2></div>
        <div class="widthblock"><h2><a href="#personal-qualities">{Yii::t('site', 'Personal skills')} <span class="signmore"></span></a></h2></div>
    </div><!-- /estmtileswrap -->
</div>
<div class="clearfix maincharts">
    <div class="gauge-charts">

    </div>
    <div class="bullet-charts">

    </div>
</div>

<div class="levellabels">
    <div class="widthblock"><h3>{Yii::t('site', 'Level of skills maturity')}</h3></div>
    <div class="widthblock"><h3>{Yii::t('site', 'Achievement of results: number and value of tasks completed')}</h3></div>
    <div class="widthblock"><h3>{Yii::t('site', 'Speed of getting results')}</h3></div>
    <div class="widthblock"><h3>{Yii::t('site', 'Личностные качества, проявленные в симуляции')}</h3></div>
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
            <li><a href="#time-management">Сверхурочное время </a></li>
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
        var r = Math.round;

        new charts.Gauge('.gauge-charts', r(AR.management.total), {class: 'inline'});
        new charts.Gauge('.gauge-charts', r(AR.performance.total), {class: 'inline'});
        new charts.Gauge('.gauge-charts', r(AR.time.total), {class: 'inline'});

        new charts.Bullet('.bullet-charts', 50, {class: 'small'});
        new charts.Bullet('.bullet-charts', 70, {class: 'small'});
        new charts.Bullet('.bullet-charts', 40, {class: 'small'});

        //new charts.Bullet('.bullet-charts', 50, {class: 'small'});
        //new charts.Bullet('.bullet-charts', 70, {class: 'small'});
       // new charts.Bullet('.bullet-charts', 40, {class: 'small'});
        $('.overall .value').html(r(AR.overall));
        $('.allrating').css('width', AR.overall + '%');
    });


</script>