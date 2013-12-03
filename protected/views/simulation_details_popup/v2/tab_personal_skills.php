<div class="page">
    <div class="textcener"><h2 class="total">Личностные характеристики</h2></div>

    <div class="personallabels">
        <div class="smalltitle">Ориентация на результат<a href="#" class="questn"></a></div>
        <div class="smalltitle">Внимательность<a href="#" class="questn"></a></div>
        <div class="smalltitle">Ответственность<a href="#" class="questn"></a></div>
        <div class="smalltitle">Устойчивость к манипуляциям и давлению<a href="#" class="questn"></a></div>
        <div class="smalltitle">Конструктивность<a href="#" class="questn"></a></div>
        <div class="smalltitle">Гибкость<a href="#" class="questn"></a></div>
        <div class="smalltitle">Принятие решений<a href="#" class="questn"></a> <!-- <a href="#">Принятие решений <span class="signmore"></span></a> --></div>
        <div class="smalltitle">Стрессоустойчивость<a href="#" class="questn"></a></div>
    </div>

    <div class="barswrap personalbars">
        <!--<div class="shortIndicator">
            <div class="shortindvalue">
                <div class="bullet highlevel"></div><
                <div class="bar"></div>
            </div>
            <div class="shortchartvalue">продемонстрирован высокий уровень</div>
        </div><!-- stressResistance -->
    </div>
</div>
<script type="text/javascript">
    $(function() {
        var result = AR.personal,
            r = Math.round,
            val,
            codes = [14, 11, 12, 10, 15, 16, 13, 9];

        for (var i = 0; i < codes.length; i++) {
            val = result[codes[i]] || 0;
            if (codes[i] == 9) {
                val = val > 50 ? 100 : 0;
            }

            new charts.Bullet(
                '.personalbars',
                r(val),
                codes[i] == 9 ? {class: 'short'} : {class: 'small', displayValue: true}
            );

            if (codes[i] == 9) {
                $('.personalbars').append('<div class="shortchartdesc">продемонстрирован ' + (val ? 'высокий' : 'низкий') + ' уровень</div>');
            }
        }
    });
</script>
