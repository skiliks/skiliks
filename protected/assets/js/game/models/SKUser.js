/*global Backbone:false, console, SKApp, SKSimulation */
var SKUser;
define(['game/models/SKSimulation'],function (SKSimulation) {
    "use strict";
    /**
     * Пользователь. Может логиниться и разлогиниваться пока
     *
     * @class SKUser
     * @augments Backbone.Model
     */
    SKUser = Backbone.Model.extend(
        /** @lends SKUser */
        {
            /**
             * Constructor
             * @method initialize
             * @param {Array} simulations available simulation types
             */
            'initialize':function (simulations) {
                this.simulations = simulations;
            },

            /**
             * Creates new simulation
             *
             * @method startSimulation
             * @param stype
             * @return {SKSimulation} created simulation
             */
            'startSimulation':function (stype) {
                if (this.simulation !== undefined) {
                    throw 'Simulation already started';
                }
                this.simulation = new SKSimulation({'stype':stype});
                this.simulation.start();
                return this.simulation;
            },

            /**
             * Stops simulation
             *
             * @method stopSimulation
             * @async
             */
            'stopSimulation':function () {
                if (this.simulation === undefined) {
                    throw 'Simulation already stopped';
                }
                this.simulation.on('stop', function () {
                    delete this.simulation;
                }, this);
                this.simulation.stop();
            },

            /**
             * Завершение работы пользователя
             *
             * @method logout
             */
            'logout':function () {
                var me = this;
                SKApp.server.api('auth/logout', undefined, function () {
                    /**
                     * Пользователь успешно разлогинен
                     * @event logout
                     */
                    me.trigger('logout');
                    SKApp.session.trigger('logout');
                });
            }
        });

    return SKUser;
});