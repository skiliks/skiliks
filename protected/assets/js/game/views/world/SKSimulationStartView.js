/*global _, Backbone, simulation, SKSettingsView, SKApp, session, world*/
(function () {
    "use strict";
    window.SKSimulationStartView = Backbone.View.extend({
        'initialize': function () {
            this.render();
        },
        'events': {
            'click .btn.simulation-start': 'doSimulationStart',
            'click .settings': 'doSettings',
            'click .logout': 'doLogout'
        },
        'render': function () {
            var code = _.template($('#start_simulation_menu').html(), {'simulations': this.options.simulations});
            this.$el.html(code);
        },
        'doSimulationStart': function (event) {
            simulation.start($(event.target).attr('data-sim-id'));
        },
        'doSettings': function () {
            var view = new SKSettingsView({'el': this.$el});
        },
        'doLogout': function () {
            SKApp.server.api('auth/logout', {}, function() {
                session.clearSid();
                world.drawDefault();
            });
        }
    });
})();