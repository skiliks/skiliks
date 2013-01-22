/*global Backbone, _, $, SKApp, SKDebugView, SKIconPanelView, SKPhoneDialogView, SKVisitView, SKPhoneView, SKMailClientView
 SKPhoneCallView, SKDocumentsListView, SKXLSDisplayView, SKPDFDisplayView */
(function () {
    "use strict";
    window.SKSimulationView = Backbone.View.extend({
        'el':'body',
        'events':{
            'click .btn-simulation-stop': 'doSimulationStop'
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
            if (window.get('name') === 'documents' && window.get('subname') === 'documentsFiles') {
                var file = window.get('filename');
                var document_view;
                if (file.match(/\.xlsx$/) || file.match(/\.xls$/)) {
                    document_view = new SKXLSDisplayView({model_instance:window});
                } else {
                    document_view = new SKPDFDisplayView({model_instance:window});
                }
                document_view.render();
            }
        }, 'initialize':function () {
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
                simulation.documents.each(function (doc) {
                    me.listenTo(doc, 'change:excel_url', function () {
                        me.preloadZoho(doc);
                    });
                });
            });
            this.listenTo(simulation.documents, 'add', function (doc) {
                me.listenTo(doc, 'change:excel_url', function () {
                    me.preloadZoho(doc);  
                });
            });
        },
        preloadZoho: function (doc) {
            this.$('.canvas').append($('<iframe />', {
                src: doc.get('excel_url'),
                id: 'excel-preload-' + doc.id
            }).css('display', 'none'));    
        },
        'render':function () {
            var login_html = _.template($('#simulation_template').html(), {});
            this.$el.html(login_html);
            this.icon_view = new SKIconPanelView({'el':this.$('.main-screen-icons')});
            if (this.simulation.isDebug()) {
                this.debug_view = new SKDebugView({'el':this.$('.debug-panel')});
            }
            var canvas = this.$('.canvas');
            this.updateTime();
        },
        'updateTime':function () {
            var parts = this.simulation.getGameTime().split(':');
            this.$('.time .hour').text(parts[0]);
            this.$('.time .minute').text(parts[1]);
        },
        'addSimulationEvents':function () {
            SKApp.user.simulation.events.on('add', function (event) {
                if (event.getTypeSlug() === 'immediate-visit') {
                    var visit_view = new SKVisitView({'event':event});
                    event.complete();
                } else if (event.getTypeSlug() === 'immediate-phone') {

                    SKApp.user.simulation.window_set.toggle('phone', 'phoneTalk', {sim_event:event});
                    event.complete();
                }
            });

        },

        'doSimulationStop':function () {
            SKApp.user.stopSimulation();
        }
    });
})();