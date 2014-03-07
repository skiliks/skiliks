<div class="pull-content-center">
    <h1 class="total">Итоговый рейтинг менеджера</h1>
</div>
<div class="allsummry">
    <div class="overall percentil_overall_container">
        <span class="percentil_base">
            <span class="percentil_overall tab_main_popup" style="width:0;"></span>
        </span>
        <div class="percentil_text">P0</div>
    </div>
    <div class="clear: both"></div>
    <div class="estmtresults">
        <div class="overall">
            <span class="allratebg">
                <span class="allrating" style="width:30%"></span>
            </span>
            <span class="blockvalue">
                <span class="value"></span>
                %
            </span>
        </div>
    </div>
    <!-- /estmtresults -->

    <div class="estmtileswrap">
        <div class="widthblock">
            <h4 class="pull-content-center"><a href="#time-management">
                    Эффективность использования времени
                    <span class="signmore"></span>
                </a></h4>
        </div>

        <div class="widthblock widthshort">
            <h4 class="pull-content-center"><a href="#productivity">
                    Результативность
                    <span class="signmore"></span>
            </h4></a>
        </div>

        <div class="widthblock">
            <h4 class="pull-content-center"><a href="#managerial-skills">
                    Управленческие навыки
                    <span class="signmore"></span>
                </a></h4>
        </div>

    </div><!-- /estmtileswrap -->
</div>
<div class="clearfix-simulation-results maincharts">
    <div class="gauge-charts">

    </div>
</div>

<div class="levellabels">
    <h6 class="pull-content-center">Время, затраченное на выполнение задач</h6>
    <h6 class="pull-content-center">Уровень достижения результатов: количество и значимость выполненных задач</h6>
    <h6 class="pull-content-center">Уровень владения навыками </h6>
</div>
<div class="rateslist">
    <div class="widthblock"><h3 class="in-mark">ПОКАЗАТЕЛИ</h3>
        <ul class="bluelist nobultslist in-mark">
            <li><a href="#time-management-detail" data-parent="time-management">Распределение времени </a></li>
            <li><a href="#time-management">Сверхурочное время </a></li>
        </ul>
    </div>
    <div class="widthblock widthshort"><h3 class="in-mark">ПОКАЗАТЕЛИ</h3>
        <ul class="bluelist nobultslist in-mark">
            <li><a href="#productivity" data-parent="time-management">Результативность</a></li>
        </ul>
    </div>
    <div class="widthblock in-mark"><h3>ОЦЕНИВАЕМЫЕ НАВЫКИ</h3>
        <ul class="bluelist nobultslist in-mark">
            <li><a href="#managerial-skills-1" data-parent="managerial-skills">Управление задачами с учётом приоритетов</a></li>
            <li><a href="#managerial-skills-2" data-parent="managerial-skills">Управление людьми</a></li>
            <li><a href="#managerial-skills-3" data-parent="managerial-skills">Управление коммуникациями</a></li>
        </ul>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        setTimeout(function() {
            $(".gauge-charts").html('');
            var r = Math.round;

            new charts.Gauge('.gauge-charts', r(AR.management.total || 0), {class: 'inline'});
            new charts.Gauge('.gauge-charts', r(AR.performance.total || 0), {class: 'inline'});
            new charts.Gauge('.gauge-charts', r(AR.time.total || 0), {class: 'inline'});

            new charts.Bullet('.bullet-charts', 50, {class: 'small'});
            new charts.Bullet('.bullet-charts', 70, {class: 'small'});
            new charts.Bullet('.bullet-charts', 40, {class: 'small'});

            $('.overall .value').html(r(AR.overall || 0));
            $('.allrating').css('width', (AR.overall || 0) + '%');
            $('.tab_main_popup.percentil_overall').css('width', (AR.percentile.total || 0) + '%');
            $('.percentil_text').html("P"+r(AR.percentile.total));
        }, 500);
    });
</script>