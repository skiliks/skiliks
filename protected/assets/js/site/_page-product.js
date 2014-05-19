
$(document).ready(function(){

    new charts.Gauge(
        '.product-gauge-charts',
        Math.round(80),
        { class: 'inline', isDisplayValue: false }
    );

    new charts.Gauge(
        '.product-gauge-charts',
        Math.round(80),
        { class: 'inline', isDisplayValue: false }
    );

    new charts.Gauge(
        '.product-gauge-charts',
        Math.round(80),
        { class: 'inline', isDisplayValue: false }
    );

    //    new charts.Bullet('.product-bullet-charts', 50, { class: 'inline' });
    //    new charts.Bullet('.product-bullet-charts', 70, { class: 'inline' });
    //    new charts.Bullet('.product-bullet-charts', 40, { class: 'inline' });

    // 2) product page, test results - sub list hide/show switcher
    $('.hassubmenu a.sub-menu-switcher').click(function () {
        if ($(this).parent().hasClass('subisopen')) {
            $(this).parent().removeClass('subisopen');
        } else {
            $(this).parent().addClass('subisopen');
        }
    });

});
