<?php /** @var Simulation $simulation */ ?>

<div class="section">
    <div class="textcener"><h2 class="total totalwithresult">Результативность <span class="value blockvalue productivity-total"></span></h2></div>


    <p class="barstitle resultlabeltitle">Уровень выполнения задач</p>
    <div class="clearfix">
        <div class="labels">
            <div class="row"><div class="popover" style="display: block;"><div class="popover-triangle"></div><div class="popover-content"><div class="popup-content ProximaNova">Выполнение срочных задач, которые должны быть сделаны прямо сейчас (отложить не возможно).</div></div></div><h3 class="resulttitele smallerfont">Срочно</h3><a href="#" class="questn show-popover" style="margin:0;"></a></div>
            <div class="row"><div class="popover" style="display: block;"><div class="popover-triangle"></div><div class="popover-content"><div class="popup-content ProximaNova">Выполнение задач, значимых для компании и/или для подразделения, которые должны быть сделаны сегодня.</div></div></div><h3 class="resulttitele smallerfont">Высокий приоритет</h3><a href="#" class="questn show-popover" style="margin:0;"></a></div>
            <div class="row"><div class="popover" style="display: block;"><div class="popover-triangle"></div><div class="popover-content"><div class="popup-content ProximaNova">Выполнение задач, важных для компании и/или для подразделения, со сроком исполнения в ближайшие дни, но не сегодня.</div></div></div><h3 class="resulttitele smallerfont">Средний приоритет</h3><a href="#" class="questn show-popover" style="margin:0;"></a></div>
            <div class="row"><div class="popover" style="display: block;"><div class="popover-triangle"></div><div class="popover-content"><div class="popup-content ProximaNova">Задачи без определённого срока исполнения, а также задачи, не относящиеся к основному бизнес-процессу.</div></div></div><h3 class="resulttitele smallerfont">Прочее</h3><a href="#" class="questn show-popover" style="margin:0;"></a></div>
        </div>

        <div class="bars barswrap">

        </div>
    </div>
    <div class="legendwrap resultslegend">
        <div class="legend">
            <p class="barstitle">Обозначения</p>
            <div class="legendvalue shortlegend"><span class="legendcolor colormax"></span><span class="legendtitle">Максимальный уровень выполнения задач</span></div>
            <div class="legendvalue"><span class="legendcolor colordone"></span><span class="legendtitle">Проявленный уровень выполнения задач</span></div>
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