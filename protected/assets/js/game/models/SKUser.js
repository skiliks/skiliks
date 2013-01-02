/*global Backbone:false, console, SKApp, SKSimulation */

(function () {
    "use strict";
    window.SKUser = Backbone.Model.extend({
        'initialize':function (simulations) {
            this.simulations = simulations;
        },
        'startSimulation':function (stype) {
            if (this.simulation !== undefined) {
                throw 'Simulation already started';
            }
            this.simulation = new SKSimulation({'stype':stype});
            this.simulation.start();
            return this.simulation;
        },
        'stopSimulation':function (stype) {
            if (this.simulation === undefined) {
                throw 'Simulation already stopped';
            }
            this.simulation.stop();
            delete this.simulation;
        },
        'logout':function () {
            var me = this;
            SKApp.server.api('auth/logout', undefined, function () {
                me.trigger('logout');
            });
        }
    });
})();