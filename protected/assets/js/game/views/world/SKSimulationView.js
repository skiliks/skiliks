/*global Backbone, _, $, SKApp, SKDebugView, SKIconPanelView, SKPhoneDialogView, SKVisitView, SKImmediateVisitView, SKPhoneView, SKMailClientView
 SKPhoneCallView, SKDocumentsListView, SKXLSDisplayView, SKPDFDisplayView, SKDayPlanView, SKMailClientView, SKPhoneCallView */

var SKSimulationView;


define([
    "text!game/jst/world/simulation_template.jst",

    "game/views/mail/SKMailClientView",
    "game/views/documents/SKXLSDisplayView",
    "game/views/documents/SKDocumentListView",
    "game/views/plan/SKDayPlanView",
    "game/views/documents/SKPDFDisplayView",
    "game/views/phone/SKPhoneView",
    "game/views/phone/SKPhoneCallView",
    "game/views/phone/SKPhoneDialogView",
    "game/views/dialogs/SKVisitView",
    "game/views/dialogs/SKImmediateVisitView",
    "game/views/world/SKDebugView",
    "game/views/world/SKIconPanelView",
    "game/views/SKDialogView"
], function (simulation_template, SKMailClientView, SKXLSDisplayView) {
    "use strict";
    /**
     * @class SKSimulationView
     * @augments Backbone.View
     */
    SKSimulationView = Backbone.View.extend(
        /**
         * @lends SKSimulationView
         */
        {
            'el':              'body',
            'events':          {
                'click .btn-simulation-stop':      'doSimulationStop',
                // TODO: move to SKDebugView
                'click .btn-toggle-dialods-sound': 'doToggleDialogSound',
                'click .pause-control, .paused-screen .resume, .finish > a': 'doTogglePause'
            },
            'window_views':    {
                'plan/plan':               SKDayPlanView,
                'phone/phoneMain':         SKPhoneView,
                'mailEmulator/mailMain':   SKMailClientView,
                'phone/phoneCall':         SKPhoneCallView,
                'phone/phoneTalk':         SKPhoneDialogView,
                'documents/documents':     SKDocumentsListView,
                'visitor/visitorEntrance': SKVisitView,
                'visitor/visitorTalk':     SKImmediateVisitView,
            },
            /**
             * Массив окон, которые открыты в симуляции
             * @property windows
             */
            'windows':  [],

            /**
             * Constructor
             *
             * @method initialize
             */
            'initialize':      function () {
                var me = this;
                var simulation = this.simulation = SKApp.simulation;
                this.listenTo(simulation, 'tick', this.updateTime);
                this.listenTo(simulation, 'end', this.endWorkday);
                this.listenTo(simulation.window_set, 'add', this.setupWindowEvents);
                this.listenTo(simulation, 'input-lock:start', this.doStartInputLock);
                this.listenTo(simulation, 'input-lock:stop', this.doStopInputLock);
                this.listenTo(simulation, 'start', this.startExitProtection);
                this.listenTo(simulation, 'before-stop', this.stopExitProtection);
                this.listenTo(simulation, 'stop-time', this.stopSimulation);

                this.listenTo(simulation.documents, 'reset', function () {
                    simulation.documents.each(function (doc) {
                        me.listenTo(doc, 'change:excel_url', function () {
                            me.preloadZoho(doc);
                        });
                    });
                });

                this.listenTo(simulation.documents, 'add', function (doc) {
                    me.listenTo(doc, 'document:excel_uploaded', function () {
                        me.preloadZoho(doc);
                    });
                });

                this.listenTo(simulation, 'start', function () {
                    $('#sim-id').text(simulation.id);
                });
            },

            startExitProtection: function () {
                $(window).on('beforeunload', function () {
                    return "Вы действительно хотите прервать симуляцию? Вы не получите оценку и запуск симуляции будет израсходован";
                });
            },

            stopExitProtection: function () {
                $(window).off('beforeunload');
            },

            /**
             * @method
             * @param window
             */
            setupWindowEvents: function (window) {
                var window_full_name = (window.get('name') + '/' + window.get('subname'));
                if (this.window_views[window_full_name]) {
                    var WindowClass = this.window_views[window_full_name];
                    var view = new WindowClass({model_instance: window, event: window.get('sim_event')});
                    view.render();
                    this.windows.push(view);
                }
                if (window.get('name') === 'documents' && window.get('subname') === 'documentsFiles') {
                    var file = window.get('document').get('name');
                    var document_view;
                    if (file.match(/\.xlsx$/) || file.match(/\.xls$/)) {
                        document_view = new SKXLSDisplayView({model_instance: window});
                    } else {
                        document_view = new SKPDFDisplayView({model_instance: window});
                    }

                    if (false === document_view.isRender) {
                        SKApp.simulation.window_set.remove(
                            SKApp.simulation.window_set.where({subname: 'documentsFiles'})
                        );
                    } else {
                        document_view.render();
                    }
                }
            },

            /**
             * Preloads excel with Zoho on simulation start
             *
             * @method
             * @param doc
             */
            preloadZoho:       function (doc) {
                this.$('.windows-container').append($('<iframe />', {
                    src: doc.get('excel_url'),
                    id:  'excel-preload-' + doc.id
                }).css({
                        'left':     '-1000px',
                        'position': 'absolute'
                    }));
            },

            /**
             * @method
             */
            'render':          function () {
                var login_html = _.template(simulation_template, {});
                this.$el.html(login_html);
                this.icon_view = new SKIconPanelView({'el': this.$('.main-screen-icons')});
                if (this.simulation.isDebug()) {
                    this.debug_view = new SKDebugView({'el': this.$('.debug-panel')});
                    this.doToggleDialogSound();
                }
                var canvas = this.$('.canvas');
                this.updateTime();
                this.undelegateEvents();
                this.delegateEvents();

                if (undefined !== SKApp.simulation) {
                    this.$('#sim-id').text(SKApp.simulation.get('id'));
                }

                if (undefined !== SKApp.get('skiliksSpeedFactor')) {
                    this.$('#speed-factor').text(SKApp.get('skiliksSpeedFactor'));
                }
                this.renderSupportBlock();
            },

            /**
             * Renders siteheart block
             */
            renderSupportBlock: function renderSupportBlock() {
                window._shcp = [];
                window._shcp.push({link_wrap_off: true, widget_id : 582287, widget : "Chat", side : "bottom", position : "left", template : "orange", title : "Поддержка Skiliks", title_offline : "Оставьте сообщение" });
                (function() {
                    var hcc = document.createElement("script");
                    hcc.type = "text/javascript";
                    hcc.async = true;
                    hcc.src = ("https:" === document.location.protocol ? "https" : "http")+"://widget.siteheart.com/apps/js/sh.js";
                    var s = document.head;
                    s.parentNode.insertBefore(hcc, null); }
                )();
            },

            /**
             * @method
             */
            'updateTime':      function () {
                var parts = this.simulation.getGameTime().split(':');
                this.$('.time .hour').text(parts[0]);
                this.$('.time .minute').text(parts[1]);
            },

            /**
             * @method
             */
            endWorkday: function() {
                var me = this;

                me.simulation.startPause();
                me._showPausedScreen();

                new SKDialogView({
                    message: 'Рабочий день закончен',
                    buttons: [
                        {
                            value: 'Завершить работу',
                            onclick: function() {
                                // me._hidePausedScreen();
                                me.stopExitProtection();
                                SKApp.simulation.stopPause(
                                    me.simulation.stop()
                                );

                                new SKDialogView({
                                    message: 'Данные симуляции сохраняются. <br/> Это займёт 10-15 секунд.',
                                    buttons: []
                                });
                            }
                        },
                        {
                            value: 'Продолжить работу',
                            onclick: function() {
                                me._hidePausedScreen();
                                me.$('.canvas .finish').removeClass('hidden');

                                SKApp.simulation.stopPause();
                            }
                        }
                    ]
                });
            },

            stopSimulation: function() {
                var me = this;
                me.simulation.stop();

                me._showPausedScreen();
                new SKDialogView({
                    message: 'Спасибо, симуляция завершена. <br/> Сейчас сохраняются результаты. <br/> Это займёт 5-10 сек.',
                    buttons: [
                        {
                            value: 'Перейти к результатам',
                            onclick: function() {
                                me.simulation.trigger('user-agree-with-sim-stop');
                            }
                        }
                    ]
                });
                $('.mail-popup-button').hide();
            },

            /**
             * @method
             */
            'doSimulationStop':  function () {
                SKApp.simulation.stop();
            },

            /**
             * @method
             */
            doToggleDialogSound: function () {
                if (SKApp.simulation.config.isMuteVideo === false) {
                    SKApp.simulation.config.isMuteVideo = true;
                    this.$('.btn-toggle-dialods-sound i').removeClass('icon-volume-up');
                    this.$('.btn-toggle-dialods-sound i').addClass('icon-volume-off');
                } else {
                    SKApp.simulation.config.isMuteVideo = false;
                    this.$('.btn-toggle-dialods-sound i').addClass('icon-volume-up');
                    this.$('.btn-toggle-dialods-sound i').removeClass('icon-volume-off');
                }
            },

            doTogglePause: function() {
                var me = this,
                    paused = me.$('.time').hasClass('paused');

                if (paused) {
                    me._hidePausedScreen();
                    me.pausedDialog.remove();
                    SKApp.simulation.stopPause();
                } else {
                    me._showPausedScreen(true);
                    me.pausedDialog = new SKDialogView({
                        modal: false,
                        message: 'Симуляция остановлена',
                        buttons: [
                            {
                                value: 'Вернуться к симуляции',
                                onclick: function() {
                                    me._hidePausedScreen();
                                    me.simulation.stopPause();
                                }
                            },
                            {
                                value: 'Перейти к результатам',
                                onclick: function() {
                                    me._hidePausedScreen();
                                    me.stopExitProtection();
                                    me.simulation.stop();
                                }
                            }
                        ]
                    });

                    SKApp.simulation.startPause();
                }

                return false;
            },

            doStartInputLock: function () {
                this.locking_element = this.make('div',{'class': 'display-lock'});
                this.$el.append(this.locking_element);
            },
            doStopInputLock: function () {
                $(this.locking_element).remove();
            },

            _showPausedScreen: function(showTopIcons) {
                this.$('.time').addClass('paused');
                this.$('.canvas .paused-screen')
                    .removeClass('hidden')
                    .find('.top-icons')
                    .toggleClass('hidden', !showTopIcons);
            },

            _hidePausedScreen: function() {
                this.$('.time').removeClass('paused');
                this.$('.canvas .paused-screen')
                    .addClass('hidden');
            }
        });

    return SKSimulationView;
});