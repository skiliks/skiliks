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
    SKApplication = Backbone.Model.extend({
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

                /** Объект для отправки AJAX запросов к API игры */
                this.server = new SKServer();

                /**
                 * Объект хранения очереди запросов к API игры.
                 * Используется для организации автоматического переспроса сервера, в случае разрыва соединения.
                 */
                this.server.requests_queue = new SKRequestsQueueCollection();

                /**
                 * При симстарте и симстопе таймаут увеличен относительно стандартного,
                 * так как это "тяжелые" запросы к API.
                 */
                this.server.requests_timeout = this.get("simStartTimeout");

                /**
                 * В туториале используется подставной класс для симуляции.
                 * Чтобы заставить часы идти вспять: от 30:00 к 0:00.
                 */
                var SimClass = this.isTutorial() ? SKTutorial : SKSimulation;
                this.simulation = new SimClass({
                    'app' : this,
                    'mode': this.get('mode'),
                    'type': this.get('type')
                });

                this.isInternetConnectionBreakHappent = false;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Проверка типа симуляции
         * @returns {boolean}
         */
        isLite: function() {
            try {
                return this.get('type') === 'lite';
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Проверка типа симуляции
         * @returns {boolean}
         */
        isFull: function() {
            try {
                return this.get('type') === 'full';
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Проверка типа симуляции
         * @returns {boolean}
         */
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