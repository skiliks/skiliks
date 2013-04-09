<?php /** @var Simulation $simulation */ ?>

<div class="section">
    <div class="textcener"><h2 class="total totalwithresult">Результативность <span class="value blockvalue"></span></h2></div>


    <p class="barstitle resultlabeltitle">Уровень выполнения задач</p>
    <div class="labels">
        <div class="row"><h3 class="resulttitele">Срочно</h3><a href="#" class="questn">?</a></div>
        <div class="row"><h3 class="resulttitele">Высокий приоритет</h3><a href="#" class="questn">?</a></div>
        <div class="row"><h3 class="resulttitele">Средний приоритет</h3><a href="#" class="questn">?</a></div>
        <div class="row"><h3 class="resulttitele">Прочее</h3><a href="#" class="questn">?</a></div>
    </div>

    <div class="bars barswrap">

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