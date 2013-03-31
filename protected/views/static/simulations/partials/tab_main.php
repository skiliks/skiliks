<style>
    .gauge-charts, .bar-charts, .bullet-charts {
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
<div class="gauge-charts nice-border backgroud-light-blue">

</div>

<div class="bar-charts nice-border backgroud-light-blue">

</div>

<div class="bullet-charts nice-border backgroud-light-blue">

</div>

<p>
    <h2>Managerial Skills <?php echo $simulation->managerial_skills; ?></h2>
</p>
<p>
    <h2>Productivity <?php echo $simulation->managerial_productivity; ?></h2>
</p>
<p>
    <h2>Time Management Effectiveness <?php echo $simulation->time_management_effectiveness; ?></h2>
</p>
<p>
    <h2>Overall Manager's Rating <?php echo $simulation->overall_manager_rating; ?></h2>
</p>
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