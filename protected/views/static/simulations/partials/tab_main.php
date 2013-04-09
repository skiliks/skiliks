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
<div class="estmtresults">
    <div class="ratepercnt overall">Overall: <span class="value"></span></div>
</div><!-- /estmtresults -->
<div class="estmtileswrap">
    <div class="widthblock"><h2>Managerial Skills</h2></div>
    <div class="widthblock"><h2>Productivity</h2></div>
    <div class="widthblock"><h2>Time Management Effectiveness</h2></div>
    <div class="widthblock"><h2>Overall Manager's Rating</h2></div>
</div><!-- /estmtileswrap -->
<div class="gauge-charts">

</div>
<div class="bullet-charts"></div>
<script type="text/javascript">
    $(function() {
        new charts.Gauge('.gauge-charts', parseInt(assessmentResult['management']['total']), {class: 'inline'});
        new charts.Gauge('.gauge-charts', parseInt(assessmentResult['performance']['total']), {class: 'inline'});
        new charts.Gauge('.gauge-charts', parseInt(assessmentResult['time']['total']), {class: 'inline'});

        new charts.Bullet('.bullet-charts', 50, {class: 'small'});
        new charts.Bullet('.bullet-charts', 70, {class: 'small'});
        new charts.Bullet('.bullet-charts', 40, {class: 'small'});

        new charts.Bullet('.bullet-charts', 50, {class: 'small'});
        new charts.Bullet('.bullet-charts', 70, {class: 'small'});
        new charts.Bullet('.bullet-charts', 40, {class: 'small'});
        $('.overall .value').html(assessmentResult['overall']);
    });


</script>