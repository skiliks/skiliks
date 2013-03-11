/*global SKEventCollection:true, SKEvent, Backbone, _, SKApp*/
var SKEventCollection;

define(["game/models/SKEvent"], function () {
    "use strict";
    /**
     * Список событий данной симуляции.
     *
     * События бывают в трех статусах:
     *
     * 1. waiting — иконка прыгает и ждет клика пользователя
     * 2. in progress — событие идет пряи сейчас
     * 3. completed — событие завершилось
     *
     * @class SKEventCollection
     * @constructor initialize
     */
    SKEventCollection = Backbone.Collection.extend(
        /**
         * @lends SKEventCollection.prototype
         */
        {
            /**
             * Телефонный звонок (на который можно не ответить)
             * @event event:phone
             *
             * Телефонный диалог
             * @event event:immediate-phone
             *
             * Стук в дверь
             * @event event:visit
             *
             * Общение с человеком
             * @event event:immediate-visit
             *
             * Входящее письмо
             * @event event:mail
             */

            /**
             * @property model
             * @type SKEvent
             * @default empty SKEvent
             */
            'model': SKEvent,

            /**
             * constructor
             * @method initialize
             */
            'initialize': function () {
                var me = this;
                // Block phone when visit/call going
                this.on('event:phone event:immediate-phone event:visit event:immediate-visit', this.handleBlocking);
            },

            /**
             * Отправляет события начала и конца блокировки
             * @param event
             * @method handleBlocking
             */
            handleBlocking: function (event) {
                /**
                 * Начало блокировки новых событий
                 * @event blocking:start
                 */
                if ('in progress' === event.getStatus()) {
                    this.trigger('blocking:start');
                } else {
                    event.on('in progress', function () {
                        this.trigger('blocking:start');
                    }, this);
                }

                event.on('complete', function () {
                    /**
                     * Конец блокировки новых событий
                     * @event blocking:end
                     */
                    this.trigger('blocking:end');
                }, this);
            },

            /**
             *
             * @method getUnreadMailCount
             * @param cb
             */
            'getUnreadMailCount': function (cb) {
                SKApp.server.api('mail/getInboxUnreadCount', {}, function (data) {
                    if (parseInt(data.result) === 1) {
                        var counter = data.unreaded;
                        cb(counter);
                    }
                });
            },

            /**
             * @method getPlanTodoCount
             * @param cb
             */
            'getPlanTodoCount': function (cb) {
                SKApp.server.api('todo/getCount', {}, function (data) {
                    if (data.result === 1) {
                        var counter = data.data;
                        cb(counter);
                    }
                });
            },

            /**
             * Returns true if simulation can accept event
             *
             * @param {SKEvent} event
             * @returns {Boolean}
             * @method canAddEvent
             */
            canAddEvent: function (event) {
                if (!event.getTypeSlug().match(/(phone|visit)$/)) {
                    return true;
                }
                var res = true;
                this.each(function (ev) {
                    if (ev.getTypeSlug().match(/(phone|visit)$/) &&
                        (ev.getStatus() === 'in progress' || ev.getStatus() === 'waiting') &&
                        ev.get('data')[0].code !== event.get('data')[0].code) {
                        res = false;
                    }
                });
                return res;
            },

            /**
             * Костыльный метод отправки события на сервер
             *
             * @param {string} code
             * @param {int} delay in minutes
             * @param clear_events
             * @param clear_assessment
             * @method triggerEvent
             */
            'triggerEvent': function (code, delay, clear_events, clear_assessment) {
                var callback;
                if (arguments.length > 4) {
                    callback = arguments[4];
                } else {
                    callback = undefined;
                }
                SKApp.server.api('events/start', {
                    eventCode: code,
                    delay: delay,
                    clearEvents: clear_events,
                    clearAssessment: clear_assessment
                }, callback);
            },

            /**
             * Чего-то ждет o_0
             *
             * @param {string} code
             * @param originalTime
             * @method wait
             */
            'wait': function (code, originalTime) {
                SKApp.server.api('events/wait', {
                    eventCode: code,
                    eventTime: originalTime
                });
            }
        });

    return SKEventCollection;
});