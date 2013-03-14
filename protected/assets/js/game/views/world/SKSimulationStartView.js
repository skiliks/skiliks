/*global _, Backbone, simulation, SKSettingsView, SKLoginView, SKApp, session, world, $*/

var SKSimulationStartView;

define([
    "text!game/jst/world/start_simulation_menu.jst",

    "game/views/world/SKSimulationView",
    "game/views/world/SKLoginView",
    "game/views/world/SKDebugView"
], function (
    start_simulation_menu
    ) {
    "use strict";
    /**
     * @class SKSimulationStartView
     * @augments Backbone.View
     */
    SKSimulationStartView = Backbone.View.extend({
        'el': 'body',
        'events': {
            //'click .btn.simulation-start': 'doSimulationStart',
            //'click .settings': 'doSettings',
            //'click .logout': 'doLogout'
        },

        /**
         * @method
         */
        'render': function () {
            var simulations = SKApp.user.simulations;
            var code = _.template(start_simulation_menu, {'simulations': simulations});

            this.$el.html(code);

            if (undefined != window.mode) {
                this.doSimulationStart({});
            }
        },

        /**
         * @method
         * @param event
         */
        'doSimulationStart': function (event) {
            var me = this;

            var mode = $(event.target).attr('data-sim-id');
            if (undefined != window.mode) {
                if ('developer' == window.mode) {
                    mode = 2;
                }
                if ('promo' == window.mode) {
                    mode = 1;
                }
            }
            console.log('**: ',  $(event.target).attr('data-sim-id'), window.mode, mode);

            var simulation = SKApp.user.startSimulation(mode);
            var simulation_view = this.simulation_view = new SKSimulationView();
            simulation.on('start', function () {
                simulation_view.render();
            });
            this.listenTo(simulation, 'stop', function () {
                delete me.simulation_view;
                location.href = simulation.get('result-url');
            });
        },

        /**
         * @method
         */
        'doSettings': function () {
            var view = new SKSettingsView({'el': this.$el});
        },

        /**
         * @method
         */
        'doLogout': function () {
            SKApp.user.logout();
        }
    });

    return SKSimulationStartView;
});