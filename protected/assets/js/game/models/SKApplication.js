/*global Backbone:false, define */
var SKApplication;

define([
    "game/models/SKServer",
    "game/models/SKSimulation",
    "game/models/SKTutorial",
    "game/collections/SKRequestsQueueCollection"
], function (SKServer, SKSimulation, SKTutorial, SKRequestsQueueCollection) {
    "use strict";
    /**
     * Корневой класс нашей игры. Инстанциируется в начале игры и инстанс доступен под именем SKApp
     *
     * @class SKApplication
     * @augments Backbone.Model
     */
    SKApplication = Backbone.Model.extend(
        {
            /**
             * Constructor
             * @method initialize
             * @return void
             */
            'initialize':function () {
                try {
                    if ('developer' === this.get('mode')) {
                        this.set('skiliksSpeedFactor', this.get('skiliksDeveloperModeSpeedFactor'));
                    }
                    /**
                     * Ссылка на API-сервер
                     * @attribute server
                     * @type SKServer
                     */
                    this.server = new SKServer();
                    this.server.requests_queue = new SKRequestsQueueCollection();

                    var SimClass = this.isTutorial() ? SKTutorial : SKSimulation;
                    this.simulation = new SimClass({'app': this, 'mode': this.get('mode'), 'type': this.get('type')});

                    this.isInternetConnectionBreakHappent = false;
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            run: function() {
                try {
                    this.simulation.start();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Очищает текущего пользователя симуляции
             * @method clearUser
             * @return void
             */
            'clearUser':function () {
                try {
                    this.user.logout();
                    delete this.user;
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            isLite: function() {
                try {
                    return this.get('type') === 'lite';
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            isFull: function() {
                try {
                    return this.get('type') === 'full';
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            isTutorial: function() {
                try {
                    return this.get('type') === 'tutorial';
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            }
        });

    return SKApplication;
});