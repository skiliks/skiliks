/*global Backbone, _, SKApp, SKConfig, SKDialogWindow*/
(function () {
    "use strict";
    window.SKDayPlanView = Backbone.View.extend({
        'el':'body .plan-container',
        'initialize': function () {
            this.render();
        },
        'render': function () {
            this.$el.html(_.template($('#plan_template').html()));
        }
    });
})();
