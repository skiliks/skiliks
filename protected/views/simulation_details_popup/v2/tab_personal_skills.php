<div class="page">
    <div class="pull-content-center"><h2 class="total">Личностные характеристики</h2></div>

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

    <div class="barswrap personalbars"></div>
</div>
<script type="text/javascript">
    $(function() {
        var val,
            codes = [14, 11, 12, 10, 15, 16, 13, 9];

        for (var i = 0; i < codes.length; i++) {
            val = AR.personal[codes[i]] || 0;
            if (codes[i] == 9) {
                val = val > 50 ? 100 : 0;
            }

            new charts.Bullet(
                '.personalbars',
                Math.round(val),
                codes[i] == 9 ? {class: 'short'} : {class: 'small', displayValue: true}
            );

            if (codes[i] == 9) {
                $('.personalbars').append('<div class="shortchartdesc">продемонстрирован ' + (val ? 'высокий' : 'низкий') + ' уровень</div>');
            }
        }
    });
</script>
