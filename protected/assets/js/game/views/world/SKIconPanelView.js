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
                'click .icons-panel .door.icon-active a':  'doDialogStart',
                'click .icons-panel .mail.create-mail a':  'doNewMailStart',

                'click .icons-panel .door:not(.icon-active) a':  'doMeetingToggle',
                'click .icons-panel .phone:not(.icon-active) a': 'doPhoneToggle',

                'click .icons-panel .mail:not(.create-mail) a': 'doMailToggle',
                'click .icons-panel .documents a':              'doDocumentsToggle',
                'click .icons-panel .plan a':                   'doPlanToggle',

                'click .icons-panel .info a':                   'doToggleManual',

                'click .icons-panel .icon-button-disabled a':          'doNothing',
                'click .icons-panel .only-active:not(.icon-active) a': 'doNothing'
            },

            /**
             * Constructor
             * @method initialize
             */
            initialize: function () {
                try {
                    var me = this;
                    me.icon_lock = {};
                    var events = this.sim_events = SKApp.simulation.events;

                    this.listenTo(events, 'event:mail', this.onMailEvent);
                    this.listenTo(events, 'event:mail-send', this.onMailSendEvent);
                    this.listenTo(events, 'event:document', this.onDocumentEvent);
                    this.listenTo(events, 'event:visit', this.onVisitEvent);
                    this.listenTo(events, 'event:plan', this.onPlanEvent);

                    this.listenTo(events, 'event:phone', this.onPhoneEvent);
                    this.listenTo(events, 'blocking:start', this.doBlockingActions);
                    this.listenTo(events, 'blocking:end', this.doDeblockingActions);
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
                    this.listenTo(todo_tasks, 'add', function() {
                        this.onPlanEvent;
                        _.each(todo_tasks.models, function(model) {
                            model.isNewTask = false;
                        });
                        var last_model = todo_tasks.at(todo_tasks.length - 1);
                        last_model.isNewTask = true;
                    });
                    this.listenTo(todo_tasks, 'onNewTask', this.doSoundNewTodo);

                    var phone_history = SKApp.simulation.phone_history;

                    // update counter on any change in calls collection
                    phone_history.on('add change remove reset', function () {
                        me.setCounter(
                            '.phone',
                            phone_history.where({'is_displayed': false}).length);
                    });
                    this.render();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Пришло почтовое событие. Начинает прыгать иконка и обновляется счетчик
             *
             * @method
             * @param event
             * @method onMailEvent
             */
            onMailEvent: function (event) {
                try {
                    this.startAnimation('.mail');
                    if(SKApp.simulation.isPlayIncomingMailSound){
                        this.doSoundIncomeMail();
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
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
                try {
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
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
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
                try {
                    var me = this;

                    me.documentId = event.get('data').id;
                    me.startAnimation('.documents', function() {
                        me.documentId = null;
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
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
                try {
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
                            if (event.getStatus() !== 'in progress' && event.getStatus() !== 'completed') {
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
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Пришел посетитель. Прыгает иконка
             *
             * @method
             * @param event
             * @method onVisitEvent
             */
            onVisitEvent: function (event) {
                try {
                    var phones = SKApp.simulation.window_set.where({subname: "phoneMain"});
                    if(phones.length !== 0) {
                        phones[0].setOnTop();
                        phones[0].close();
                        //this.runPhoneTalkStart(event.cid);
                    }
                    var me = this;

                    me.$('.door').attr('data-event-id', event.cid);
                    me.doBlockingPhone();

                    var data = event.get('data');
                    var callbackFunction = function() {
                        if (undefined === data[2]) {
                            // user can`t ignore visit
                            if (event.getStatus() !== 'in progress' && event.getStatus() !== 'completed') {
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
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Пришла новая задача. Прыгает иконка
             *
             * @method
             * @param event
             * @method onPlanEvent
             */
            onPlanEvent: function(event) {
                try {
                    this.startAnimation('.plan');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param data
             * @returns {number}
             */
            isShortDuration: function (data) {
                try {
                    return undefined === data[2];
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Обновляет список задач в плане
             *
             * @method
             * @method updatePlanCounter
             */
            updatePlanCounter: function () {
                try {
                    var me = this;
                    me.setCounter('.plan', SKApp.simulation.todo_tasks.length);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
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
                try {
                    if (0 === this.$(selector + ' a span').length) {
                        this.$(selector + ' a').html('<span></span>');
                    }

                    if (0 === count) {
                        this.$(selector + ' a span').remove();
                    }

                    this.$(selector + ' a span').html(count);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
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
                try {
                    var me = this;

                    if (!(me.icon_lock[selector])) {
                        me.icon_lock[selector] = true;
                        var el = me.$(selector);
                        el.addClass('icon-active');

                        if (shortDuration) {
                            el.addClass('icon-active-short');
                        }

                        me.animationTimer = setTimeout(function() {
                            me.stopAnimation(selector);

                            if (end_cb !== undefined) {
                                end_cb();
                            }
                        }, shortDuration ? 4000 : 20000);
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            stopAnimation: function(selector) {
                try {
                    this.icon_lock[selector] = false;
                    if (undefined !== this.animationTimer && undefined !== this.animationTimer[selector]) {
                        clearTimeout(this.animationTimer[selector]);
                    }
                    this.$(selector).removeClass('icon-active icon-active-short');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            render: function () {
                try {
                    var me = this;
                    this.$el.html(_.template(icon_panel, {}));
                    this.updatePlanCounter();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param e
             */
            doPhoneTalkStart: function (e) {
                try {
                    e.preventDefault();
                    e.stopPropagation();
                    this.runPhoneTalkStart($(e.currentTarget).parents('.phone').attr('data-event-id'));
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param sim_event_id
             */
            runPhoneTalkStart: function (sim_event_id) {
                try {
                    var sim_event = this.sim_events.get(sim_event_id);
                    sim_event.setStatus('in progress');
                    this.stopAnimation('.phone');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Запускает событие
             *
             * @method
             * @param e
             */
            doDialogStart: function (e) {
                try {
                    e.preventDefault();
                    e.stopPropagation();
                    var sim_event = this.sim_events.get($(e.currentTarget).parents('.door').attr('data-event-id'));
                    sim_event.setStatus('in progress');
                    this.stopAnimation('.door');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param e
             */
            doPlanToggle: function (e) {
                try {
                    e.preventDefault();
                    this.stopAnimation('.plan');
                    if(false === SKApp.simulation.window_set.isActive('plan', 'plan')){
                        SKApp.simulation.window_set.toggle('plan', 'plan');
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doMeetingToggle: function(e) {
                try {
                    e.preventDefault();
                    if ($(e.target).parents('.icon-button-disabled').length) {
                        if (SKApp.simulation.window_set.isOpen('visitor')) {
                            SKApp.simulation.window_set.getWindow('visitor').setOnTop();
                        }
                    } else {
                        SKApp.simulation.window_set.open('visitor', 'meetingChoice');
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doToggleManual: function(e) {
                try {
                    e.preventDefault();
                    if (false === SKApp.simulation.window_set.isActive('mainScreen', 'manual')){
                        SKApp.simulation.window_set.toggle('mainScreen', 'manual');
                    }
                } catch (exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doSoundPhoneCallInStop: function() {
                try {
                    window.AppView.frame.icon_view._stopSound('audio-phone-call');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doSoundPhoneCallInStart: function() {
                try {
                    window.AppView.frame.icon_view._playSound('phone/S1.4.1.ogg', true, true, 'audio-phone-call');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doSoundPhoneCallLongZoomerStop: function() {
                try {
                    window.AppView.frame.icon_view._stopSound('audio-phone-long-zoom');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doSoundPhoneCallLongZoomerStart: function() {
                try {
                    window.AppView.frame.icon_view._playSound('phone/S1.4.2.ogg', true, true, 'audio-phone-long-zoom');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doSoundPhoneCallShortZoomerStop: function() {
                try {
                    window.AppView.frame.icon_view._stopSound('audio-phone-short-zoom');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doSoundPhoneCallShortZoomerStart: function() {
                try {
                    window.AppView.frame.icon_view._playSound('phone/S1.4.3.ogg', true, true, 'audio-phone-short-zoom');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doSoundKnockStart: function() {
                try {
                    window.AppView.frame.icon_view._playSound('visit/S1.5.1.ogg', true, true, 'audio-door-knock');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doSoundKnockStop: function() {
                try {
                    window.AppView.frame.icon_view._stopSound('audio-door-knock');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doSoundIncomeMail: function() {
                try {
                    window.AppView.frame.icon_view._playSound('mail/S1.1.1.ogg');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doSoundMailSent: function() {
                try {
                    this._playSound('mail/S1.1.2.ogg');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doSoundSaveAttachment: function() {
                try {
                    window.AppView.frame.icon_view._playSound('mail/S1.1.3.ogg');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doSoundNewTodo: function() {
                try {
                    window.AppView.frame.icon_view._playSound('plan/S1.2.1.ogg');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            _playSound: function(filename, repeat, replay, id) {
                try {
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
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            _stopSound: function(id) {
                try {
                    this.$el.find('#' + id).each(function() {
                        if (this.pause !== undefined) {
                            this.pause();
                        }
                        this.src = '';
                    }).remove();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param e
             */
            doPhoneToggle: function (e) {
                try {
                    e.preventDefault();
                    if ($(e.target).parents('.icon-button-disabled').length) {
                        if (SKApp.simulation.window_set.isOpen('phone')) {
                            SKApp.simulation.window_set.getWindow('phone').setOnTop();
                        }
                    } else {
                        SKApp.simulation.window_set.open('phone', 'phoneMain');
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param e
             */
            doDocumentsToggle: function (e) {
                try {
                    e.preventDefault();

                    if (this.documentId) {
                        this.doDocumentViewShow(this.documentId);
                        this.documentId = null;
                    } else {
                        if(false == SKApp.simulation.window_set.isActive('documents', 'documents')){
                            SKApp.simulation.window_set.toggle('documents', 'documents');
                        }
                    }

                    this.stopAnimation('.documents');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param e
             */
            doNothing: function (e) {
                try {
                    e.preventDefault();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param e
             */
            doNewMailStart: function (e) {
                try {
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
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param event
             */
            doMailToggle: function (event) {
                try {
                    if (event) {
                        event.preventDefault();
                    }

                    this.stopAnimation('.mail');

                    // we need getActiveSubscreenName() because mailClient window subname changed dinamically
                    if(false == SKApp.simulation.window_set.isActive('mailEmulator', SKApp.simulation.mailClient.getActiveSubscreenName())){
                        SKApp.simulation.window_set.toggle(
                            'mailEmulator',
                            SKApp.simulation.mailClient.getActiveSubscreenName()
                        );
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param docId
             */
            doDocumentViewShow: function (docId) {
                try {
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
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Blocking only phone icon
             *
             * @method doBlockingActions
             */
            doBlockingPhone: function() {
                try {
                    this.$('.phone').addClass('icon-button-disabled');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Blocking icons when HERO talk by phone or speak with visitor
             *
             * @method doBlockingActions
             */
            doBlockingActions: function () {
                try {
                    this.$('.phone, .door').addClass('icon-button-disabled');

                    var meetingDoor = SKApp.simulation.window_set.where({subname: "meetingChoice"});
                    if (meetingDoor.length !== 0) {
                        meetingDoor[0].close();
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Deblocking icons when HERO finished talk by phone or speak with visitor
             *
             * @method doDeblockingActions
             */
            doDeblockingActions: function () {
                try {
                    this.$('.phone, .door').removeClass('icon-button-disabled');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            }
        });

        return SKIconPanelView;
    });
