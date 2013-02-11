/*global Backbone, _, $, SKApp, SKDebugView, SKIconPanelView, SKPhoneDialogView, SKVisitView, SKImmediateVisitView, SKPhoneView, SKMailClientView
 SKPhoneCallView, SKDocumentsListView, SKXLSDisplayView, SKPDFDisplayView, SKDayPlanView */
define([
    "game/views/mail/SKMailClientView",
    "game/views/documents/SKDocumentListView",
    "game/views/plan/SKDayPlanView",
    "game/views/documents/SKPDFDisplayView",
    "game/views/documents/SKXLSDisplayView",
    "game/views/phone/SKPhoneView",
    "game/views/phone/SKPhoneCallView"
    ], function () {
    "use strict";
    /**
     * @class
     * @type {*}
     */
    window.SKSimulationView = Backbone.View.extend(
        /**
         * @lends SKSimulationView
         */
        {
            'el':'body',
            'events':{
                'click .btn-simulation-stop':'doSimulationStop',
                // TODO: move to SKDebugView
                'click .btn-toggle-dialods-sound':'doToggleDialogSound'
            },
            setupWindowEvents:function (window) {
                if (window.get('name') === 'plan') {
                    var plan_view = new SKDayPlanView({model_instance:window});
                    plan_view.render();
                }
                if (window.get('name') === 'phone' && window.get('subname') === 'phoneMain') {
                    var phone_view = new SKPhoneView({model_instance:window});
                    phone_view.render();
                }
                if (window.get('name') === 'mailEmulator' && window.get('subname') === 'mailMain') {
                    var mail_client_view = new SKMailClientView({model_instance:window});
                    mail_client_view.render();
                    //SKApp.user.simulation.mailClient.toggleWindow();
                }
                if (window.get('name') === 'phone' && window.get('subname') === 'phoneCall') {
                    var call_view = new SKPhoneCallView({model_instance:window, event:window.get('sim_event')});
                    call_view.render();
                }
                if (window.get('name') === 'phone' && window.get('subname') === 'phoneTalk') {
                    var view = new SKPhoneDialogView({model_instance:window, 'event':window.get('params').event});
                    view.render();
                }
                if (window.get('name') === 'documents' && window.get('subname') === 'documents') {
                    var doc_view = new SKDocumentsListView({model_instance:window});
                    doc_view.render();
                }
                if (window.get('name') === 'visitor' && window.get('subname') === 'visitorEntrance') {
                    var visitor_view = new SKVisitView({model_instance:window});
                    visitor_view.render();
                }
                if (window.get('name') === 'documents' && window.get('subname') === 'documentsFiles') {
                    var file = window.get('document').get('name');
                    var document_view;
                    if (file.match(/\.xlsx$/) || file.match(/\.xls$/)) {
                        document_view = new SKXLSDisplayView({model_instance:window});
                    } else {
                        document_view = new SKPDFDisplayView({model_instance:window});
                    }
                    document_view.render();
                }
            },
            'initialize':function () {
                var me = this;
                var simulation = this.simulation = SKApp.user.simulation;
                this.addSimulationEvents();
                this.listenTo(simulation, 'tick', function () {
                    me.updateTime();
                });
                this.listenTo(simulation.window_set, 'add', function (window) {
                    me.setupWindowEvents(window);
                });
                this.listenTo(simulation.documents, 'reset', function () {
                    var timeout = 0;
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
            },
            /**
             * Preloads excel with Zoho on simulation start
             * @param doc
             */
            preloadZoho:function (doc) {
                this.$('.canvas').append($('<iframe />', {
                    src:doc.get('excel_url'),
                    id:'excel-preload-' + doc.id
                }).css({
                        'left':'-1000px',
                        'position':'absolute'
                    }));
            },
            'render':function () {
                var login_html = _.template($('#simulation_template').html(), {});
                this.$el.html(login_html);
                this.icon_view = new SKIconPanelView({'el':this.$('.main-screen-icons')});
                if (this.simulation.isDebug()) {
                    this.debug_view = new SKDebugView({'el':this.$('.debug-panel')});
                    this.doToggleDialogSound();
                }
                var canvas = this.$('.canvas');
                this.updateTime();
                this.undelegateEvents();
                this.delegateEvents();
                
                if (undefined !== SKApp.user.simulation.id) {
                    this.$('#sim-id').text(SKApp.user.simulation.id);
                }
                if (undefined !== SKConfig.skiliksSpeedFactor) {
                    this.$('#speed-factor').text(SKConfig.skiliksSpeedFactor);
                }
            },
            'updateTime':function () {
                var parts = this.simulation.getGameTime().split(':');
                this.$('.time .hour').text(parts[0]);
                this.$('.time .minute').text(parts[1]);
            },
            'addSimulationEvents':function () {
                var me = this;
                SKApp.user.simulation.events.on('add', function (event) {
                    if (event.getTypeSlug() === 'immediate-visit') {
                        if (me.visit_view === undefined) {
                            me.visit_view = new SKImmediateVisitView({'event':event});
                            me.visit_view.visitor_entrance_window.on('close', function () {
                                delete me.visit_view;
                            });
                        } else {
                            me.visit_view.options.event = event;
                            me.visit_view.render();
                        }
                        event.setStatus('in progress');
                    } else if (event.getTypeSlug() === 'immediate-phone') {
                        var win = SKApp.user.simulation.window_set.open('phone', 'phoneTalk', {sim_event:event});
                        event.setStatus('in progress');
                    }
                });
            },


            'doSimulationStop':function () {
                SKApp.user.stopSimulation();
            },
            doToggleDialogSound:function () {
                if (SKApp.user.simulation.config.isMuteVideo === false) {
                    SKApp.user.simulation.config.isMuteVideo = true;
                    this.$('.btn-toggle-dialods-sound i').removeClass('icon-volume-up');
                    this.$('.btn-toggle-dialods-sound i').addClass('icon-volume-off');
                } else {
                    SKApp.user.simulation.config.isMuteVideo = false;
                    this.$('.btn-toggle-dialods-sound i').addClass('icon-volume-up');
                    this.$('.btn-toggle-dialods-sound i').removeClass('icon-volume-off');
                }
            }
        });
});