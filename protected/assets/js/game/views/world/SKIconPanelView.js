/*global _, Backbone, SKApp, SKVisitView, SKImmediateVisitView, phone, mailEmulator,
documents, dayPlan, SKPhoneView, SKPhoneDialogView,
 glabal SKDayPlanView, SKPhoneHistoryCollection, SKPhoneCallView, $, console, define, SKDocumentsWindow */
var SKIconPanelView;

define([
        "text!game/jst/world/icon_panel.jst",
        "text!game/jst/world/audio.jst"
    ],
    function (
        icon_panel,
        audio
    ) {
        "use strict";
        /**
         * Панель с иконками. Слушает коллекцию events, обновляет счетчики и прыгает иконки
         *
         * @class SKIconPanelView
         * @augments Backbone.View
         */
        SKIconPanelView = Backbone.View.extend({
            /** @lends SKIconPanelView.prototype */

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
                this.listenTo(events, 'event:plan', this.onPlanEvent);

                this.listenTo(events, 'event:phone', this.onPhoneEvent);
                this.listenTo(events, 'blocking:start', this.doBlockingPhoneIcon);
                this.listenTo(events, 'blocking:end', this.doDeblockingPhoneIcon);
                this.listenTo(events, 'mail:counter:update', function (count) {
                    me.setCounter('.mail', count);
                });

                this.listenTo(SKApp.simulation, 'audio-phone-end-start', function() {
                    me.doSoundPhoneCallShortZoomerStart();
                    setTimeout(me.doSoundPhoneCallShortZoomerStop, SKApp.get('afterCallZoomerDuration'));
                });

                this.listenTo(
                    SKApp.simulation,
                    'audio-phone-small-zoom-stop',
                    _.bind(me.doSoundPhoneCallShortZoomerStop, me)
                );

                var todo_tasks = SKApp.simulation.todo_tasks;
                this.listenTo(todo_tasks, 'add remove reset', this.updatePlanCounter);
                this.listenTo(todo_tasks, 'onNewTask', this.doSoundNewTodo);

                var phone_history = SKApp.simulation.phone_history;

                // update counter on any change in calls collection
                phone_history.on('add change remove reset', function () {
                    me.setCounter(
                        '.phone',
                        phone_history.where({'is_displayed': false}).length);
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
                this.startAnimation('.mail');
                if(SKApp.simulation.isPlayIncomingMailSound){
                    this.doSoundIncomeMail();
                }
            },

            /**
             * Пришло событие "отправь почту"
             *
             * @method
             * @param event
             * @method onMailEvent
             */
            onMailSendEvent: function (event) {
                var me = this,
                    mailClientView = SKApp.simulation.mailClient.view;

                if (!event.get('fantastic')) {
                    if (mailClientView) {
                        me.doNewMailStart();
                    } else {
                        me.$('.mail').addClass('create-mail');
                        me.startAnimation('.mail');
                    }
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
                var me = this;

                me.documentId = event.get('data').id;
                me.startAnimation('.documents', function() {
                    me.documentId = null;
                });
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
                var phones = SKApp.simulation.window_set.where({subname: "phoneMain"});
                if(phones.length !== 0){
                    phones[0].setOnTop();
                    phones[0].close();
                    this.runPhoneTalkStart(event.cid);
                    return;
                }

                var me = this;
                this.$('.phone').attr('data-event-id', event.cid);

                var data = event.get('data');
                var callbackFunction;
                if (undefined === data[2]) {
                    // user can`t ignore call
                    callbackFunction = function () {
                        if (event.getStatus() !== 'in progress') {
                            event.setStatus('in progress');
                        }
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
                this.startAnimation('.' + event.getTypeSlug(), callbackFunction, me.isShortDuration(data));

                if(SKApp.simulation.isPlayIncomingCallSound){
                    me.doSoundPhoneCallInStart();
                }
                event.on('complete', function() {
                    me.doSoundPhoneCallInStop();
                });
            },

            /**
             * Пришел посетитель. Прыгает иконка
             *
             * @method
             * @param event
             * @method onVisitEvent
             */
            onVisitEvent: function (event) {
                var phones = SKApp.simulation.window_set.where({subname: "phoneMain"});
                if(phones.length !== 0) {
                    phones[0].setOnTop();
                    phones[0].close();
                    //this.runPhoneTalkStart(event.cid);
                }
                var me = this;

                me.$('.door').attr('data-event-id', event.cid);
                me.doBlockingPhoneIcon();

                var data = event.get('data');
                var callbackFunction = function() {
                    if (undefined === data[2]) {
                        // user can`t ignore visit
                        if (event.getStatus() !== 'in progress') {
                            event.setStatus('in progress');
                        }
                    } else {
                        // user can ignore visit
                        if (event.getStatus() === 'waiting') {
                            event.setStatus('completed');
                        }
                    }
                };

                me.startAnimation('.door', callbackFunction, me.isShortDuration(data));

                me.doSoundKnockStart();
                event.on('complete', function() {
                    me.doSoundKnockStop();
                });
            },

            /**
             * Пришла новая задача. Прыгает иконка
             *
             * @method
             * @param event
             * @method onPlanEvent
             */
            onPlanEvent: function(event) {
                this.startAnimation('.plan');
            },

            /**
             * @method
             * @param data
             * @returns {number}
             */
            isShortDuration: function (data) {
                return undefined === data[2];
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
             * @optional @param shortDuration
             * @async
             */
            startAnimation: function (selector, end_cb, shortDuration) {
                var me = this;

                if (!(me.icon_lock[selector])) {
                    me.icon_lock[selector] = true;
                    var el = me.$(selector);
                    el.addClass('icon-active');

                    if (shortDuration) {
                        el.addClass('icon-active-short');
                    }

                    setTimeout(function() {
                        me.stopAnimation(selector);

                        if (end_cb !== undefined) {
                            end_cb();
                        }
                    }, shortDuration ? 4000 : 20000);
                }
            },

            stopAnimation: function(selector) {
                this.icon_lock[selector] = false;
                this.$(selector).removeClass('icon-active icon-active-short');
            },

            /**
             * @method
             */
            render: function () {
                var me = this;
                this.$el.html(_.template(icon_panel, {}));
                this.updatePlanCounter();
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
                this.stopAnimation('.phone');
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
                this.stopAnimation('.door');
            },

            /**
             * @method
             * @param e
             */
            doPlanToggle: function (e) {
                e.preventDefault();
                this.stopAnimation('.plan');
                SKApp.simulation.window_set.toggle('plan', 'plan');
            },

            doSoundPhoneCallInStop: function() {
                window.AppView.frame.icon_view._stopSound('audio-phone-call');
            },

            doSoundPhoneCallInStart: function() {
                window.AppView.frame.icon_view._playSound('phone/S1.4.1.ogg', true, true, 'audio-phone-call');
            },

            doSoundPhoneCallLongZoomerStop: function() {
                window.AppView.frame.icon_view._stopSound('audio-phone-long-zoom');
            },

            doSoundPhoneCallLongZoomerStart: function() {
                window.AppView.frame.icon_view._playSound('phone/S1.4.2.ogg', true, true, 'audio-phone-long-zoom');
            },

            doSoundPhoneCallShortZoomerStop: function() {
                window.AppView.frame.icon_view._stopSound('audio-phone-short-zoom');
            },

            doSoundPhoneCallShortZoomerStart: function() {
                window.AppView.frame.icon_view._playSound('phone/S1.4.3.ogg', true, true, 'audio-phone-short-zoom');
            },

            doSoundKnockStart: function() {
                window.AppView.frame.icon_view._playSound('visit/S1.5.1.ogg', true, true, 'audio-door-knock');
            },

            doSoundKnockStop: function() {
                window.AppView.frame.icon_view._stopSound('audio-door-knock');
            },

            doSoundIncomeMail: function() {
                window.AppView.frame.icon_view._playSound('mail/S1.1.1.ogg');
            },

            doSoundMailSent: function() {
                this._playSound('mail/S1.1.2.ogg');
            },

            doSoundSaveAttachment: function() {
                window.AppView.frame.icon_view._playSound('mail/S1.1.3.ogg');
            },

            doSoundNewTodo: function() {
                window.AppView.frame.icon_view._playSound('plan/S1.2.1.ogg');
            },

            _playSound: function(filename, repeat, replay, id) {
                var me = this,
                    el;

                id = id || 'sound' + Math.floor(Math.random() * 10000);

                if (me.$el.find('#' + id).length && !replay) {
                    return false;
                }

                me._stopSound(id);

                me.$el.append(_.template(audio, {
                    id        : id,
                    repeat    : !!repeat,
                    audio_src : SKApp.get('storageURL') + '/sounds/' + filename
                }));

                el = me.$el.find('#' + id)[0];
                if ('function' === typeof el.play) {
                    el.play();
                    if (!repeat) {
                        $(el).on('ended', function() {
                            if (this.pause !== undefined) {
                                this.pause();
                            }
                            this.src = '';
                            $(this).remove();
                        });
                    }
                }

                return id;
            },

            _stopSound: function(id) {
                this.$el.find('#' + id).each(function() {
                    if (this.pause !== undefined) {
                        this.pause();
                    }
                    this.src = '';
                }).remove();
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

                this.stopAnimation('.documents');
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
                var me = this,
                    mailClient = SKApp.simulation.mailClient,
                    mailClientView = mailClient.view,
                    windowSet = SKApp.simulation.window_set;

                if (e) {
                    e.preventDefault();
                }

                this.$('.mail').removeClass('create-mail');
                this.stopAnimation('.mail');
                if (mailClientView && mailClientView.render_finished) {
                    windowSet.open('mailEmulator', mailClient.getActiveSubscreenName());
                    mailClientView.renderWriteCustomNewEmailScreen();
                } else if (mailClientView) {
                    windowSet.open('mailEmulator', mailClient.getActiveSubscreenName());
                    mailClientView.once('render_folder_finished', function () {
                        mailClientView.renderWriteCustomNewEmailScreen();
                    });
                } else {
                    mailClient.once('init_completed', function () {
                        this.view.once('render_finished', function () {
                            this.renderWriteCustomNewEmailScreen();
                        });
                    });
                    windowSet.open('mailEmulator', mailClient.getActiveSubscreenName());
                }
            },

            /**
             * @method
             * @param event
             */
            doMailToggle: function (event) {
                //console.log('doMailToggle');
                if (event) {
                    event.preventDefault();
                }

                this.stopAnimation('.mail');

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
                var window = SKApp.simulation.window_set.where({subname: 'documentsFiles', fileId: document.get('id')})[0];
                if (window !== undefined) {
                    window.setOnTop();
                } else {
                    window = new SKDocumentsWindow({
                        subname: 'documentsFiles',
                        document: document,
                        fileId: document.get('id')
                    });
                    window.open();
                }
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
