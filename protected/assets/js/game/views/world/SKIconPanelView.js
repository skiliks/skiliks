/*global _, Backbone, SKApp, SKVisitView, SKImmediateVisitView, phone, mailEmulator, documents, dayPlan, SKPhoneView, SKPhoneDialogView,
 glabal SKDayPlanView, SKPhoneHistoryCollection, SKPhoneCallView*/
var SKIconPanelView;

define([
    "text!game/jst/world/icon_panel.jst"
],
    function (icon_panel) {
        "use strict";
        /**
         * Панель с иконками. Слушает коллекцию events, обновляет счетчики и прыгает иконки
         *
         * @class SKIconPanelView
         * @augments Backbone.View
         */
        SKIconPanelView = Backbone.View.extend({
            /** @lends SKIconPanelView.prototype */

            /**
             * Количество прыганий иконки
             * @property defaultBouncesNo
             */
            defaultBouncesNo: 10, // icon animated 10 times

            events: {
                'click .icons-panel .phone.icon-active a': 'doPhoneTalkStart',
                'click .icons-panel .door.icon-active a': 'doDialogStart',
                'click .icons-panel .mail.create-mail a': 'doNewMailStart',
                'click .icons-panel .plan a': 'doPlanToggle',
                'click .icons-panel .phone:not(.icon-active):not(.icon-button-disabled) a': 'doPhoneToggle',
                'click .icons-panel .mail:not(.create-mail) a': 'doMailToggle',
                'click .icons-panel .documents a': 'doDocumentsToggle',
                'click .icons-panel .icon-button-disabled a': 'doNothing',
                'click .icons-panel .only-active:not(.icon-active) a': 'doNothing'
            },

            /**
             * Constructor
             * @method initialize
             */
            initialize: function () {
                var me = this;
                me.icon_lock = {};
                var events = this.sim_events = SKApp.simulation.events;

                this.listenTo(events, 'event:mail', this.onMailEvent);
                this.listenTo(events, 'event:mail-send', this.onMailSendEvent);
                this.listenTo(events, 'event:document', this.onDocumentEvent);
                this.listenTo(events, 'event:visit', this.onVisitEvent);

                this.listenTo(events, 'event:phone', this.onPhoneEvent);
                this.listenTo(events, 'blocking:start', this.doBlockingPhoneIcon);
                this.listenTo(events, 'blocking:end', this.doDeblockingPhoneIcon);

                var todo_tasks = SKApp.simulation.todo_tasks;
                this.listenTo(todo_tasks, 'add remove reset', this.updatePlanCounter);

                var phone_history = SKApp.simulation.phone_history;

                // update counter on any change in calls collection
                phone_history.on('add change remove reset', function () {
                    me.setCounter(
                        '.phone',
                        phone_history.where({'is_read': false}).length);
                });
                this.render();
            },

            /**
             * Пришло почтовое событие. Начинает прыгать иконка и обновляется счетчик
             *
             * @method
             * @param event
             * @method onMailEvent
             */
            onMailEvent: function (event) {
                this.updateMailCounter();
                this.startAnimation('.mail');
            },

            /**
             * Пришло событие "отправь почту"
             *
             * @method
             * @param event
             * @method onMailEvent
             */
            onMailSendEvent: function (event) {
                if (!event.get('fantastic')) {
                    this.$('.mail').addClass('create-mail');
                    this.startAnimation('.mail');
                }
            },

            /**
             * Пришло событие документа
             * Прыгает иконка и открывается соответствующий событию документ
             *
             * @method
             * @param event
             * @method onDocumentEvent
             */
            onDocumentEvent: function (event) {
                this.startAnimation('.documents');
                this.documentId = event.get('data').id;
            },

            /**
             * Пришел телефонный звонок. Прыгает иконка. Сюда же засунута логика по обработке звонков, на которые
             * нельзя не ответить. Нужно это перенести в модель
             *
             * @method
             * @param event
             * @method onPhoneEvent
             */
            onPhoneEvent: function (event) {
                var me = this;
                this.$('.phone').attr('data-event-id', event.cid);

                var data = event.get('data');
                var callbackFunction;
                if (undefined == data[2]) {
                    // user can`t ignore call
                    callbackFunction = function () {
                        event.setStatus('in progress');
                    };
                } else {
                    // user can ignore call
                    callbackFunction = function () {
                        if (event.getStatus() === 'waiting' && undefined !== data[2]) {
                            event.setStatus('completed');
                            event.ignore(function () {
                                var history = SKApp.simulation.phone_history;
                                history.fetch();
                            });
                        }
                    };
                }
                this.startAnimation('.' + event.getTypeSlug(), callbackFunction, me.getPhoneBounces(data));
            },

            /**
             * Пришел посетитель. Прыгает иконка
             *
             * @method
             * @param event
             * @method onVisitEvent
             */
            onVisitEvent: function (event) {
                var me = this;

                me.$('.door').attr('data-event-id', event.cid);
                me.doBlockingPhoneIcon();
                me.startAnimation('.door', function() {
                    if (event.getStatus() === 'waiting') {
                        event.setStatus('completed');
                    }
                });
            },

            /**
             * @method
             * @param data
             * @returns {number}
             */
            getPhoneBounces: function (data) {
                if (undefined === data[2]) {
                    return 2;
                } else {
                    return this.defaultBouncesNo;
                }
            },

            /**
             * Обновляет счетчик почтовых сообщений
             *
             * @method
             * @method updateMailCounter
             */
            updateMailCounter: function () {
                var me = this;
                this.sim_events.getUnreadMailCount(function (count) {
                    me.setCounter('.mail', count);
                });
            },

            /**
             * Обновляет список задач в плане
             *
             * @method
             * @method updatePlanCounter
             */
            updatePlanCounter: function () {
                var me = this;
                me.setCounter('.plan', SKApp.simulation.todo_tasks.length);

            },

            /**
             * Changes counter value
             *
             * @method
             * @param selector
             * @param count
             * @method setCounter
             */
            setCounter: function (selector, count) {
                if (0 === this.$(selector + ' a span').length) {
                    this.$(selector + ' a').html('<span></span>');
                }

                if (0 === count) {
                    this.$(selector + ' a span').remove();
                }

                this.$(selector + ' a span').html(count);
            },

            /**
             * Starts icon animation
             *
             * @method startAnimation
             * @param {string} selector CSS selector of jQuery li element
             * @optional @param {Function} end_cb called when animation ends
             * @optional @param bounces
             * @async
             */
            startAnimation: function (selector, end_cb, bounces) {
                // define bounce_counter
                var bounce_counter;
                if (undefined === bounces) {
                    bounce_counter = this.defaultBouncesNo;
                } else {
                    bounce_counter = bounces;
                }

                var me = this;
                if (!(me.icon_lock[selector])) {
                    me.icon_lock[selector] = true;
                    var el = me.$(selector);
                    el.addClass('icon-active');

                    // define callback {
                    var bounce_cb = function () {
                        if (bounce_counter > 0) {
                            bounce_counter--;
                            setTimeout(function () {
                                if (el.hasClass('icon-active')) {
                                    el.effect("bounce", {times: 3, direction: 'left'}, 400, bounce_cb);
                                } else {
                                    me.icon_lock[selector] = false;
                                    if (end_cb !== undefined) {
                                        end_cb();
                                    }
                                }
                            }, 1000);
                        } else {
                            me.icon_lock[selector] = false;
                            el.removeClass('icon-active');
                            if (end_cb !== undefined) {
                                end_cb();
                            }
                        }
                    };
                    // define callback }

                    // run callback
                    bounce_cb();
                }
            },

            /**
             * @method
             */
            render: function () {
                var me = this;
                this.$el.html(_.template(icon_panel, {}));
                me.updateMailCounter();
                this.listenTo(SKApp.simulation, 'start', this.updateMailCounter);
            },

            /**
             * @method
             * @param e
             */
            doPhoneTalkStart: function (e) {
                e.preventDefault();
                e.stopPropagation();
                this.runPhoneTalkStart($(e.currentTarget).parents('.phone').attr('data-event-id'));
            },

            /**
             * @method
             * @param sim_event_id
             */
            runPhoneTalkStart: function (sim_event_id) {
                var sim_event = this.sim_events.get(sim_event_id);
                sim_event.setStatus('in progress');
                this.$('.phone').removeClass('icon-active');
            },

            /**
             * Запускает событие
             *
             * @method
             * @param e
             */
            doDialogStart: function (e) {
                e.preventDefault();
                e.stopPropagation();
                var sim_event = this.sim_events.get($(e.currentTarget).parents('.door').attr('data-event-id'));
                sim_event.setStatus('in progress');
                this.$('.door').removeClass('icon-active');
            },

            /**
             * @method
             * @param e
             */
            doPlanToggle: function (e) {
                e.preventDefault();
                SKApp.simulation.window_set.toggle('plan', 'plan');
            },

            /**
             * @method
             * @param e
             */
            doPhoneToggle: function (e) {
                e.preventDefault();
                SKApp.simulation.window_set.toggle('phone', 'phoneMain');
            },

            /**
             * @method
             * @param e
             */
            doDocumentsToggle: function (e) {
                e.preventDefault();

                if (this.documentId) {
                    this.doDocumentViewShow(this.documentId);
                    this.documentId = null;
                } else {
                    SKApp.simulation.window_set.toggle('documents', 'documents');
                }

                this.$('.documents').removeClass('icon-active');
            },

            /**
             * @method
             * @param e
             */
            doNothing: function (e) {
                e.preventDefault();
            },

            /**
             * @method
             * @param e
             */
            doNewMailStart: function (e) {
                this.$('.mail').removeClass('create-mail');
                SKApp.simulation.mailClient.once('init_completed', function () {
                    this.view.once('render_finished', function () {
                        this.renderWriteCustomNewEmailScreen();
                    });
                });

                this.doMailToggle(e);
            },

            /**
             * @method
             * @param e
             */
            doMailToggle: function (e) {
                e.preventDefault();
                this.$('.mail').removeClass('icon-active');

                // we need getActiveSubscreenName() because mailClient window subname changed dinamically
                SKApp.simulation.window_set.toggle(
                    'mailEmulator',
                    SKApp.simulation.mailClient.getActiveSubscreenName()
                );
            },

            /**
             * @method
             * @param docId
             */
            doDocumentViewShow: function (docId) {
                var document = SKApp.simulation.documents.where({id: docId})[0];
                var window = new SKDocumentsWindow({
                    subname: 'documentsFiles',
                    document: document,
                    fileId: document.get('id')
                });
                window.open();
            },

            /**
             * Blocking phone icon when HERO talk by phone or speak with visitor
             *
             * @method doBlockingPhoneIcon
             */
            doBlockingPhoneIcon: function () {
                this.$('.phone').addClass('icon-button-disabled');
            },

            /**
             * Deblocking phone icon when HERO finished talk by phone or speak with visitor
             *
             * @method doDeblockingPhoneIcon
             */
            doDeblockingPhoneIcon: function () {
                this.$('.phone').removeClass('icon-button-disabled');
            }
        });

        return SKIconPanelView;
    });
