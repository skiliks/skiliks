<style>
    .page {
        margin: 100px 0 0;
        padding: 20px;
        min-height: 400px;
    }
    .page > * {
        margin: 20px 0;
    }

    .chart-gauge.inline {
        float: left;
        margin: 10px -7px;
    }

    .chart-bullet.small {
        width: 300px;
        margin: 40px 50px;
    }

    .bar {
        clear: both;
    }
</style>

<div class="page nice-border backgroud-light-blue">

</div>

<script type="text/javascript">
$(function() {
    new charts.Gauge('.page', 45, {class: 'inline'});

    new charts.Bar('.page', 60, {class: 'bar'});
    new charts.Bar('.page', 5, {class: 'bar'});
    new charts.Bar('.page', 95, {class: 'bar'});

    new charts.Bullet('.page', 50, {class: 'small'});
    new charts.Bullet('.page', 70, {class: 'small', displayValue: true});
});
</script>