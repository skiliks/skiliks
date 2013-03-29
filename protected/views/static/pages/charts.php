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
    }
</style>

<div class="page nice-border backgroud-light-blue">

</div>

<script type="text/javascript">
$(function() {
    new charts.Gauge('.page', 75, {class: 'inline'});
    new charts.Gauge('.page', 50, {class: 'inline'});
    new charts.Gauge('.page', 45, {class: 'inline'});

    new charts.Bar('.page', 60);
    new charts.Bar('.page', 45);
    new charts.Bar('.page', 50);
    new charts.Bar('.page', 85);

    new charts.Bullet('.page', 50, {class: 'small'});
    new charts.Bullet('.page', 70, {class: 'small'});
    new charts.Bullet('.page', 40, {class: 'small'});
});
</script>