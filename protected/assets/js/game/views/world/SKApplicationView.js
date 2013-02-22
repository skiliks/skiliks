/*global _, Backbone, session, SKApplicationView:true, SKApp, SKLoginView, SKSimulationStartView*/
define(["game/models/SKApplication", "game/views/world/SKSimulationStartView", "game/views/world/SKLoginView"], function () {
    "use strict";
    window.SKApplicationView = Backbone.View.extend({
        'el':'body',
        'initialize':function () {
            var me = this;
            SKApp.session.on('login:failure', function () {
                me.frame = new SKLoginView();
            });
            SKApp.session.on('login:success', function () {
                me.frame = new SKSimulationStartView({'simulations':SKApp.user.simulations});
            });
            this.render();
        },
        'render':function () {
            var code = _.template($('#simulation_template').html(), {});
            SKApp.session.check();

            this.$el.html(code);
        },
        '_drawWorld':function (simulations) {
            //нам пришли симуляции, или мы просто прервали текущую
            if (typeof(simulations) !== 'undefined') {
                this.simulations = simulations;
            } else {
                simulations = this.simulations;
            }
            var activeFrame = this.$('#location');

        }
    });

    return SKApplicationView;
});