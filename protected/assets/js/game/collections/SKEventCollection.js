/*global SKEventCollection:true, SKEvent, Backbone, _, SKApp, define*/
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
     * @augments Backbone.Collection
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

            'isLock':false,

            'unLockUrl':'',

            /**
             * constructor
             * @method initialize
             */
            'initialize': function () {
                try {
                    var me = this;
                    // Block phone when visit/call going
                    this.on('event:phone event:immediate-phone event:visit event:immediate-visit', this.handleBlocking);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Отправляет события начала и конца блокировки
             * @param event
             * @method handleBlocking
             */
            handleBlocking: function (event) {
                try {
                    /**
                     * Начало блокировки новых событий
                     * @event blocking:start
                     */
                    if ('in progress' === event.getStatus()) {
                        this.trigger('blocking:start');
                    } else {
                        event.on('in progress', function () {
                            try {
                                this.trigger('blocking:start');
                            } catch(exception) {
                                if (window.Raven) {
                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                }
                            }
                        }, this);
                    }

                    event.on('complete', function () {
                        /**
                         * Конец блокировки новых событий
                         * @event blocking:end
                         */
                        try {
                            this.trigger('blocking:end');
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    }, this);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             *
             * @method getUnreadMailCount
             * @param cb
             */
            'getUnreadMailCount': function (cb) {
                try {
                    var me = this;
                    SKApp.server.api('mail/getInboxUnreadCount', {}, function (data) {
                        try {
                            if (parseInt(data.result, 10) === 1) {
                                var counter = parseInt(data.unreaded, 10);
                                me.unread_mail_count = counter;
                                me.trigger('mail:counter:update', me.unread_mail_count);
                                if (cb !== undefined) {
                                    cb(counter);
                                }
                            }
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Returns true if simulation can accept event
             *
             * @param {SKEvent} event
             * @returns {Boolean}
             * @method canAddEvent
             */
            canAddEvent: function (event, url) {
                try {
                    if(this.isLock && this.unLockUrl !== url) {
                        return false;
                    }

                    if (!event.getTypeSlug().match(/(phone|visit)$/)) {
                        return true;
                    }
                    var me = this;

                    return !(me.isNowDialogInProgress(event));
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @param event SKevent, это событие исключается из поиска
             * @returns {boolean}
             */
            isNowDialogInProgress: function(event) {
                var me = this;
                var result = false;
                me.each(function (ev) {
                    try {
                        if (ev.getTypeSlug().match(/(phone|visit)$/) &&
                            (ev.getStatus() === 'in progress' || ev.getStatus() === 'waiting')
                            ) {
                            if ((null != event && ev.get('data')[0].code !== event.get('data')[0].code) ||
                                null == event ) {
                                result = true;
                            }
                        }
                    } catch(exception) {
                        if (window.Raven) {
                            window.Raven.captureMessage(exception.message + ',' + exception.stack);
                        }
                    }
                });

                return result;
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
                try {
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
                        clearAssessment: clear_assessment,
                        gameTime: SKApp.simulation.getGameSeconds()
                    }, callback);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Чего-то ждет o_0
             *
             * @param {string} code
             * @param originalTime
             * @method wait
             */
            'wait': function (code, originalTime) {
                try {
                    SKApp.server.api('events/wait', {
                        eventCode: code,
                        eventTime: originalTime
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },
            'unlockEvents' : function() {
                try {
                    this.isLock = false;
                    this.unLockUrl = '';
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },
            'lockEvents' : function(url) {
                try {
                    this.unLockUrl = url;
                    this.isLock = true;
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            }
        });

    return SKEventCollection;
});