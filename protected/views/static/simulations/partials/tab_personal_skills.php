<div class="page">
    <div class="textcener"><h2 class="total">Личностные характеристики</h2></div>

    <div class="personallabels">
        <div class="smalltitle">Ориентация на результат<a href="#" class="questn">?</a></div>
        <div class="smalltitle">Внимательность<a href="#" class="questn">?</a></div>
        <div class="smalltitle">Ответственность<a href="#" class="questn">?</a></div>
        <div class="smalltitle">Устойчивость к манипуляциям и давлению<a href="#" class="questn">?</a></div>
        <div class="smalltitle">Конструктивность<a href="#" class="questn">?</a></div>
        <div class="smalltitle">Гибкость<a href="#" class="questn">?</a></div>
        <div class="smalltitle"><a href="#">Принятие решений <span class="signmore"></span></a></div>
        <div class="smalltitle">Стрессоустойчивость<a href="#" class="questn">?</a></div>
    </div>

    <div class="barswrap personalbars">
        <div class="resultOrientation"></div>
        <div class="attentiveness"></div>
        <div class="responsibility"></div>
        <div class="stability"></div>
        <div class="constructibility"></div>
        <div class="flexibility"></div>
        <div class="adoptionOfDecisions"></div>
        <div class="stressResistance"></div>
    </div>
</div>
<script type="text/javascript">
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
