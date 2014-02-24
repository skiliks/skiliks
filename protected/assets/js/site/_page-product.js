
$(document).ready(function(){
    var r = Math.round;

    new charts.Gauge('.product-gauge-charts', r(80), { class: 'inline' });
    new charts.Gauge('.product-gauge-charts', r(80), { class: 'inline' });
    new charts.Gauge('.product-gauge-charts', r(80), { class: 'inline' });

    new charts.Bullet('.product-bullet-charts', 50, { class: 'inline' });
    new charts.Bullet('.product-bullet-charts', 70, { class: 'inline' });
    new charts.Bullet('.product-bullet-charts', 40, { class: 'inline' });
});
