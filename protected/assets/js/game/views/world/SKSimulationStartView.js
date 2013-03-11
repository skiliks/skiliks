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
            'click .btn.simulation-start': 'doSimulationStart',
            'click .settings': 'doSettings',
            'click .logout': 'doLogout'
        },
        'render': function () {
            var simulations = SKApp.user.simulations;
            var code = _.template(start_simulation_menu, {'simulations': simulations});
            this.$el.html(code);
        },
        'doSimulationStart': function (event) {
            var me = this;
            var simulation = SKApp.user.startSimulation($(event.target).attr('data-sim-id'));
            var simulation_view = this.simulation_view = new SKSimulationView();
            simulation.on('start', function () {
                simulation_view.render();
            });
            simulation.on('stop', function () {
                delete me.simulation_view;
                me.render();
            });
        },
        'doSettings': function () {
            var view = new SKSettingsView({'el': this.$el});
        },
        'doLogout': function () {
            SKApp.user.logout();
        }
    });

    return SKSimulationStartView;
});