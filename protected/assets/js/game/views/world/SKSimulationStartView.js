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
            try {
                var code = _.template(start_simulation_menu);

                this.$el.html(code);

                if (undefined !== window.mode) {
                    this.doSimulationStart({});
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param event
         */
        'doSimulationStart': function (event) {
            try {
                var me = this;

                var mode = $(event.target).attr('data-sim-id');
                if (undefined !== window.mode) {
                    if ('developer' == window.mode) {
                        mode = 2;
                    }
                    if ('promo' == window.mode) {
                        mode = 1;
                    }
                }

                var simulation = SKApp.user.startSimulation(mode);
                var simulation_view = this.simulation_view = new SKSimulationView();
                simulation.on('start', function () {
                    simulation_view.render();
                });
                this.listenTo(simulation, 'stop', function () {

                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         */
        'doSettings': function () {
            try {
                var view = new SKSettingsView({'el': this.$el});
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         */
        'doLogout': function () {
            try {
                SKApp.user.logout();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });

    return SKSimulationStartView;
});