<div class="textcener"><h2 class="total totalwithresult">Эффективность использования времени <span class="value blockvalue">0%</span></h2></div>
<div class="extrasidepads">
    <div class="timeblock">
        <h2 class="resulttitele"><a href="#">Распределение времени <span class="signmore"></span></a></h2>
        <div class="timediagr">
            <div class="timevalue criticalvalue">60%</div>
            <div class="timevalue inactvalue">10%</div>
            <div class="timevalue actvalue">30%</div>
            <span class="helpbuble">Продуктивное время. Время, потраченное на задачи первого приоритета</span>
        </div>

        <div class="pie-chart">
        </div>

        <div class="legendwrap">
            <div class="legend">
                <p class="barstitle">Обозначения</p>
                <div class="legendvalue"><span class="legendcolor colormax"></span><span class="legendtitle">Действия, относящиеся к задачам 1 приоритета</span></div>
                <div class="legendvalue"><span class="legendcolor colorwarn"></span><span class="legendtitle">Действия, не относящиеся к задачам 1 приоритета</span></div>
                <div class="legendvalue"><span class="legendcolor colordone"></span><span class="legendtitle">Время ожидания</span></div>
            </div>
        </div>
    </div>
    <div class="blockseprt"></div>
    <div class="timeblock">
        <h2 class="resulttitele">Сверхурочное время</h2>
        <div class="extrahourswrap"><div class="extrahours">45</div></div>
    </div>
</div>

<script>
$(function() {
    var data = [
        40, 50, 10
    ];

    /*var data = [
        {name:'40%',value:40, color:'#00FF00'},
        {name:'50%',value:50, color:'#FF0000'},
        {name:'10%',value:10, color:'#E8E8E8'}
    ];*/

    new charts.Pie('.pie-chart', data, {
        colors: ['#146672', '#e11a1a', '#66a3ab']
    });
});
</script>