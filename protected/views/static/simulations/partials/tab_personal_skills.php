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
    <h2>Личностные характеристики</h2>
</div>

<script type="text/javascript">
    $(function() {
        new charts.Bullet('.page', 70, {class: 'small', displayValue: true});
    });
</script>