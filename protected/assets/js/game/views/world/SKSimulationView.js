/*global Backbone, _, $, SKApp, SKDebugView, SKIconPanelView, SKPhoneDialogView, SKVisitView, SKImmediateVisitView, SKPhoneView, SKMailClientView
 SKPhoneCallView, SKDocumentsListView, SKXLSDisplayView, SKPDFDisplayView, SKDayPlanView, SKMailClientView, SKPhoneCallView */

var SKSimulationView;


define([
    "text!game/jst/world/simulation_template.jst",

    "game/views/mail/SKMailClientView",
    "game/views/documents/SKDocumentListView",
    "game/views/plan/SKDayPlanView",
    "game/views/documents/SKPDFDisplayView",
    "game/views/documents/SKXLSDisplayView",
    "game/views/phone/SKPhoneView",
    "game/views/phone/SKPhoneCallView",
    "game/views/phone/SKPhoneDialogView",
    "game/views/dialogs/SKVisitView",
    "game/views/dialogs/SKImmediateVisitView",
    "game/views/world/SKDebugView",
    "game/views/world/SKIconPanelView"
], function (simulation_template) {
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
                'click .pause-control, .overlay .resume': 'doTogglePause'
            },
            'window_views':    {
                'plan/plan':               SKDayPlanView,
                'phone/phoneMain':         SKPhoneView,
                'mailEmulator/mailMain':   SKMailClientView,
                'phone/phoneCall':         SKPhoneCallView,
                'phone/phoneTalk':         SKPhoneDialogView,
                'documents/documents':     SKDocumentsListView,
                'visitor/visitorEntrance': SKVisitView,
                'visitor/visitorTalk':     SKImmediateVisitView


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
                this.listenTo(simulation.window_set, 'add', this.setupWindowEvents);
                this.listenTo(simulation, 'input-lock:start', this.doStartInputLock);
                this.listenTo(simulation, 'input-lock:stop', this.doStopInputLock);

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
                var paused = this.$('.time').hasClass('paused');

                this.$('.time').toggleClass('paused');
                this.$('.canvas .overlay').toggleClass('hidden');
                if (paused) {
                    SKApp.simulation.stopPause();
                } else {
                    SKApp.simulation.startPause();
                }
            },
            doStartInputLock: function () {
                this.locking_element = this.make('div',{'class': 'display-lock'});
                this.$el.append(this.locking_element);
            },
            doStopInputLock: function () {
                $(this.locking_element).remove();
            }
        });

    return SKSimulationView;
});