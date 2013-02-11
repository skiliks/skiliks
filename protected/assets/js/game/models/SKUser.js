/*global Backbone:false, console, SKApp, SKSimulation */

define(['game/models/SKSimulation'],function (SKSimulation) {
    "use strict";
    /**
     * @class User object
     */
    var SKUser = Backbone.Model.extend(
        /** @lends SKUser */
        {
            /**
             * @param {Array} simulations available simulation types
             */
            'initialize':function (simulations) {
                this.simulations = simulations;
            },
            /**
             * Creates new simulation
             *
             * @param stype
             * @return {SKSimulation}
             */
            'startSimulation':function (stype) {
                if (this.simulation !== undefined) {
                    throw 'Simulation already started';
                }
                this.simulation = new SKSimulation({'stype':stype});
                this.simulation.start();
                return this.simulation;
            },
            'stopSimulation':function () {
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
    return SKUser;
});