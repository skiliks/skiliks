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
    <div class="ratepercnt"><?php echo $simulation->getCategoryAssessment(AssessmentCategory::MANAGEMENT_SKILLS); ?></div>
    <div class="ratepercnt"><?php echo $simulation->getCategoryAssessment(AssessmentCategory::PRODUCTIVITY); ?></div>
    <div class="ratepercnt"><?php echo $simulation->getCategoryAssessment(AssessmentCategory::TIME_EFFECTIVENESS); ?></div>
    <div class="ratepercnt"><?php echo $simulation->getCategoryAssessment(AssessmentCategory::OVERALL); ?></div>
</div><!-- /estmtresults -->
<div class="estmtileswrap">
<h2>Managerial Skills</h2>
<h2>Productivity</h2>
<h2>Time Management Effectiveness</h2>
<h2>Overall Manager's Rating</h2>
</div><!-- /estmtileswrap -->
<div class="gauge-charts">

</div>

<div class="bar-charts">

</div>

<div class="bullet-charts">

</div>


<script type="text/javascript">
    $(function() {
        new charts.Gauge('.gauge-charts', 75, {class: 'inline'});
        new charts.Gauge('.gauge-charts', 50, {class: 'inline'});
        new charts.Gauge('.gauge-charts', 45, {class: 'inline'});

        new charts.Bar('.bar-charts', 60);
        new charts.Bar('.bar-charts', 45);
        new charts.Bar('.bar-charts', 50);
        new charts.Bar('.bar-charts', 85);
        new charts.Bar('.bar-charts', 100);
        new charts.Bar('.bar-charts', 5);
        new charts.Bar('.bar-charts', 15);

        new charts.Bullet('.bullet-charts', 50, {class: 'small'});
        new charts.Bullet('.bullet-charts', 70, {class: 'small'});
        new charts.Bullet('.bullet-charts', 40, {class: 'small'});
    });
</script>