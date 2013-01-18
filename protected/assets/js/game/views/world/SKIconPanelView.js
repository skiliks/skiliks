/*global _, Backbone, SKApp, SKVisitView, phone, mailEmulator, documents, dayPlan, SKPhoneView, SKPhoneDialogView,
glabal SKDayPlanView, SKPhoneHistoryCollection, SKPhoneCallView*/
(function () {
    "use strict";
    window.SKIconPanelView = Backbone.View.extend({
        initialize:function () {
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
                    me.startAnimation('.' + event.getTypeSlug(), function () {
                        var dialogId = event.get('data')[2].id;
                        SKApp.server.api('dialog/get', {'dialogId':dialogId, 'time':SKApp.user.simulation.getGameTime()}, function (data) {
                            SKApp.user.simulation.parseNewEvents(data.events);
                            var history = SKApp.user.simulation.phone_history;
                                history.fetch();
                                // event will be linked later, if link event here - it will be handled twise
                                me.setCounter('.phone', phone_history.where({'is_read': false}).length);
                        });
                    });
                    
                } else if (event.getTypeSlug() === 'visit') {
                    me.$('.door').attr('data-event-id', event.cid);
                    me.startAnimation('.door');
                }
            });
            var todo_tasks = SKApp.user.simulation.todo_tasks;
            todo_tasks.on('add remove reset', function () {
                me.updatePlanCounter();
            });
            var phone_history = SKApp.user.simulation.phone_history;
            
            // update counter on any change in calls collection
            phone_history.on('add change remove reset', function () {
                me.setCounter(
                    '.phone',
                    phone_history.where({'is_read': false}).length);
            });
            this.render();
        },
        updateMailCounter: function () {
            var me = this;
            this.sim_events.getUnreadMailCount(function (count) {
                me.setCounter('.mail', count);
            });
        },
        updatePlanCounter: function () {
            var me = this;
            me.setCounter('.plan', SKApp.user.simulation.todo_tasks.length);

        },
        setCounter:function (selector, count) {
            if (0 === this.$(selector + ' a span').length) {
                this.$(selector + ' a').html('<span></span>');
            }
            
            if (0 === count) {
                this.$(selector + ' a span').remove();
            }
            
            this.$(selector + ' a span').html(count);
        },
        startAnimation:function (selector, end_cb) {
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
                        if (end_cb !== undefined) {
                            end_cb();
                        }
                    }
                };
                bounce_cb();

            }
        },
        events:{
            'click .icons-panel .phone.icon-active a':'doPhoneTalkStart',
            'click .icons-panel .door.icon-active a':'doDialogStart',

            'click .icons-panel .plan a':'doPlanToggle',
            'click .icons-panel .phone:not(.icon-active) a':'doPhoneToggle',
            'click .icons-panel .mail a':'doMailToggle',
            'click .icons-panel .door a':'doDoorToggle',
            'click .icons-panel .documents a':'doDocumentsToggle'
        },
        render:function () {
            var me = this;
            this.$el.html(_.template($('#icon_panel').html(), {}));
            me.updateMailCounter();
            me.updatePlanCounter();
        },
        doPhoneTalkStart:function (e) {
            e.preventDefault();
            e.stopPropagation();
            var sim_event = this.sim_events.getByTypeSlug('phone', false)[0];
            sim_event.complete();
            this.$('.phone').removeClass('icon-active');
            SKApp.user.simulation.window_set.toggle('phone','phoneCall', {sim_event:sim_event});
        },
        doDialogStart:function (e) {
            e.preventDefault();
            e.stopPropagation();
            var sim_event = this.sim_events.get($(e.currentTarget).parents('.door').attr('data-event-id'));
            sim_event.complete();
            var visit_view = new SKVisitView({event:sim_event});
            this.$('.door').removeClass('icon-active');
        },
        doPlanToggle:function (e) {
            e.preventDefault();
            SKApp.user.simulation.window_set.toggle('plan','plan');
        },
        doPhoneToggle:function (e) {
            e.preventDefault();
            SKApp.user.simulation.window_set.toggle('phone','phoneMain');
        },
        doDoorToggle:function (e) {
            e.preventDefault();
        },
        doDocumentsToggle:function (e) {
            e.preventDefault();
            SKApp.user.simulation.window_set.toggle('documents','documents');

            this.$('.documents').removeClass('icon-active');
        },
        doMailToggle:function (e) {
            e.preventDefault();
            this.$('.mail').removeClass('icon-active');

            // we need getActiveSubscreenName() because mailClient window subname changed dinamically
            SKApp.user.simulation.window_set.toggle(
                'mailEmulator', 
                SKApp.user.simulation.mailClient.getActiveSubscreenName()
            );

        }
    });
})();