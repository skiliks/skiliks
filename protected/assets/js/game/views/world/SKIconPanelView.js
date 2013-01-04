/*global _, Backbone, SKApp, SKVisitView, phone, dialogController, mailEmulator, documents, dayPlan, SKPhoneView, SKPhoneDialogView*/
(function () {
    "use strict";
    window.SKIconPanelView = Backbone.View.extend({
        'initialize':function () {
            var me = this;
            me.icon_lock = {};
            var events = this.sim_events = SKApp.user.simulation.events;
            events.on('add', function (event) {
                if (event.getTypeSlug() === 'mail') {
                    me.startAnimation('.' + event.getTypeSlug());
                    me.updateMailCounter();
                } else if (event.getTypeSlug() === 'document') {
                    me.startAnimation('.documents');
                } else if (event.getTypeSlug() === 'phone') {
                    me.startAnimation('.' + event.getTypeSlug());
                } else if (event.getTypeSlug() === 'visit') {
                    me.$('.door').attr('data-event-id', event.id);
                    me.startAnimation('.door');
                } else if (event.getTypeSlug() === 'immediate-visit') {
                    // TODO: incorrect location
                    var visit_view = new SKVisitView({'event': event});
                    event.complete();
                } else if (event.getTypeSlug() === 'immediate-phone') {
                    // TODO: incorrect location
                    var view = new SKPhoneDialogView({'event' : event.get('data')});
                    event.complete();
                }
            });
            this.render();
        },
        'updateMailCounter': function () {
            var me = this;
            this.sim_events.getUnreadMailCount(function (count) {
                me.setCounter('.mail', count);
            });
        },
        'updatePlanCounter': function () {
            var me = this;
            this.sim_events.getPlanTodoCount(function (count) {
                me.setCounter('.plan', count);
            });
        },
        'setCounter':function (selector, count) {
            if (!this.$(selector + ' a span').length) {
                this.$(selector + ' a').html('<span></span>');
            }
            this.$(selector + ' a span').html(count);
        },
        'startAnimation':function (selector) {
            var me = this;
            if (!(me.icon_lock[selector])) {
                me.icon_lock[selector] = true;
                var el = me.$(selector);
                el.addClass('icon-active');
                var bounce_counter = 10;
                var bounce_cb = function () {
                    if (bounce_counter > 0) {
                        bounce_counter--;
                        setTimeout(function () {
                            if (el.hasClass('icon-active')) {
                                el.effect("bounce", {times:3, direction:'left'}, 400, bounce_cb);
                            }
                        }, 1000);
                    } else {
                        me.icon_lock[selector] = false;
                        el.removeClass('icon-active');
                    }
                };
                bounce_cb();

            }
        },
        'events':{
            'click .icons-panel .phone.icon-active a':'doPhoneTalkStart',
            'click .icons-panel .door.icon-active a':'doDialogStart',

            'click .icons-panel .plan a':'doPlanToggle',
            'click .icons-panel .phone:not(.icon-active) a':'doPhoneToggle',
            'click .icons-panel .mail a':'doMailToggle',
            'click .icons-panel .door a':'doDoorToggle',
            'click .icons-panel .documents a':'doDocumentsToggle'
        },
        'render':function () {
            var me = this;
            this.$el.html(_.template($('#icon_panel').html(), {}));
            me.updateMailCounter();
            me.updatePlanCounter();
        },
        'doPhoneTalkStart':function (e) {
            e.preventDefault();
            e.stopPropagation();
            var sim_event = this.sim_events.getByTypeSlug('phone', false)[0];
            sim_event.complete();
            this.$('.phone').removeClass('icon-active');
            phone.draw('income', sim_event.get('data'));
        },
        'doDialogStart':function (e) {
            e.preventDefault();
            e.stopPropagation();
            var sim_event = this.sim_events.get($(e.currentTarget).attr('data-event-id'));
            sim_event.complete();
            var visit_view = new SKVisitView(sim_event);
            this.$('.door').removeClass('icon-active');
            dialogController.draw('income', sim_event.get('data'));
        },
        'doPlanToggle':function (e) {
            dayPlan.draw();
            e.preventDefault();
        },
        'doPhoneToggle':function (e) {
            e.preventDefault();
            phone.draw();
        },
        'doDoorToggle':function (e) {
            e.preventDefault();
        },
        'doDocumentsToggle':function (e) {
            e.preventDefault();
            documents.draw();
            this.$('.documents').removeClass('icon-active');
        },
        'doMailToggle':function (e) {
            e.preventDefault();
            this.$('.mail').removeClass('icon-active');
            
            SKApp.user.simulation.mailClient.openWindow();
        }
    });
})();