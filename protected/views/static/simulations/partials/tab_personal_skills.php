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
    <div>Ориентация на результат<div class="resultOrientation"></div></div>
    <div>Внимательность<div class="attentiveness"></div></div>
    <div>Ответственность<div class="responsibility"></div></div>
    <div>Устойчивость к манипуляциям и давлению<div class="stability"></div></div>
    <div>Конструктивность<div class="constructibility"></div></div>
    <div>Гибкость<div class="flexibility"></div></div>
    <div>Принятие решений<div class="adoptionOfDecisions"></div></div>
    <div>Стрессоустойчивость<div class="stressResistance"></div></div>
</div><script type="text/javascript">
    $(function() {
        var val = assessmentResult.personal;
        new charts.Bullet('.resultOrientation', Math.round(val.resultOrientation), {class: 'small', displayValue: true});
        new charts.Bullet('.attentiveness', Math.round(val.attentiveness), {class: 'small', displayValue: true});
        new charts.Bullet('.responsibility', Math.round(val.attentiveness), {class: 'small', displayValue: true});
        new charts.Bullet('.stability', Math.round(val.stability), {class: 'small', displayValue: true});
        new charts.Bullet('.constructibility', Math.round(val.constructibility), {class: 'small', displayValue: true});
        new charts.Bullet('.flexibility', Math.round(val.flexibility), {class: 'small', displayValue: true});
        new charts.Bullet('.adoptionOfDecisions', Math.round(val.adoptionOfDecisions), {class: 'small', displayValue: true});
        new charts.Bullet('.stressResistance', Math.round(val.stressResistance), {class: 'small', displayValue: true});
    });
</script>
