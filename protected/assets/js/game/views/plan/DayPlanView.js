/*global Backbone, _, SKApp, SKConfig, SKWindowView*/
(function () {
    "use strict";
    window.SKDayPlanView = SKWindowView.extend({
        'el':'body .plan-container',
        'initialize': function () {
            this.render();
        },
        'renderWindow': function (window_el) {
            window_el.html(_.template($('#plan_template').html()));
        }
    });
})();
