<div class="page">
    <div class="textcener"><h2 class="total">Личностные характеристики</h2></div>

    <div class="personallabels">
        <div class="smalltitle">Ориентация на результат<a href="#" class="questn"></a></div>
        <div class="smalltitle">Внимательность<a href="#" class="questn"></a></div>
        <div class="smalltitle">Ответственность<a href="#" class="questn"></a></div>
        <div class="smalltitle">Устойчивость к манипуляциям и давлению<a href="#" class="questn"></a></div>
        <div class="smalltitle">Конструктивность<a href="#" class="questn"></a></div>
        <div class="smalltitle">Гибкость<a href="#" class="questn"></a></div>
        <div class="smalltitle">Принятие решений <a href="#" class="questn"></a></div>
        <div class="smalltitle">Стрессоустойчивость<a href="#" class="questn"></a></div>
    </div>

    <div class="barswrap personalbars">
        <div class="resultOrientation"></div>
        <div class="attentiveness"></div>
        <div class="responsibility"></div>
        <div class="stability"></div>
        <div class="constructibility"></div>
        <div class="flexibility"></div>
        <div class="adoptionOfDecisions"></div>
        <div class="shortIndicator">
            <div class="shortindvalue">
                <div class="bullet highlevel"></div><!-- highlevel - dlya vysokogo urovnya, lowlevel - dlya nizkogo -->
                <div class="bar"></div>
            </div>
            <div class="shortchartvalue">продемонстрирован высокий уровень</div>
        </div><!-- stressResistance -->
    </div>
</div>
<script type="text/javascript">
    $(function() {
        var result = AR.personal,
            r = Math.round;

        new charts.Bullet('.resultOrientation', r(result.resultOrientation), {class: 'small', displayValue: true});
        new charts.Bullet('.attentiveness', r(result.attentiveness), {class: 'small', displayValue: true});
        new charts.Bullet('.responsibility', r(result.attentiveness), {class: 'small', displayValue: true});
        new charts.Bullet('.stability', r(result.stability), {class: 'small', displayValue: true});
        new charts.Bullet('.constructibility', r(result.constructibility), {class: 'small', displayValue: true});
        new charts.Bullet('.flexibility', r(result.flexibility), {class: 'small', displayValue: true});
        new charts.Bullet('.adoptionOfDecisions', r(result.adoptionOfDecisions), {class: 'small', displayValue: true});
        new charts.Bullet('.stressResistance', r(result.stressResistance), {class: 'small', displayValue: true});
    });
</script>
