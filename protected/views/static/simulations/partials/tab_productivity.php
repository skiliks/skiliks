<?php /** @var Simulation $simulation */ ?>
<style>
    .labels {
        float: left;
        width: 300px;
        font-size: 20px;
        line-height: 55px;
        font-weight: bold;
    }
    .labels .row {

    }
    .bars {
        float: right;
        width: 650px;

    }
</style>

<div class="section">
    <h2 class="total">Результативность <span class="value"></span></h2>

    <div class="labels">
        <div class="row">Срочно</div>
        <div class="row">Высокий приоритет</div>
        <div class="row">Средний приоритет</div>
        <div class="row">Прочее</div>
    </div>

    <div class="bars">

    </div>

    <div class="legend"></div>
</div>

<script type="text/javascript">
    $(function() {
        var result = assessmentResult['performance'];

        new charts.Bar('.bars', parseInt(result['0']));
        new charts.Bar('.bars', parseInt(result['1']));
        new charts.Bar('.bars', parseInt(result['2']));
        new charts.Bar('.bars', parseInt(result['2_min']));

        $('.total .value').html(parseInt(result['total']) + '%');
    });
</script>