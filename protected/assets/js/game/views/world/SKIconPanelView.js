/*global _, Backbone, SKApp, SKVisitView, SKImmediateVisitView, phone, mailEmulator, documents, dayPlan, SKPhoneView, SKPhoneDialogView,
glabal SKDayPlanView, SKPhoneHistoryCollection, SKPhoneCallView*/
var SKIconPanelView;

define([
    "text!game/jst/world/icon_panel.jst"
    ],
    function (icon_panel)
    {
    "use strict";
    /**
     * @class
     * @type {*}
     */
    SKIconPanelView = Backbone.View.extend(
        {
        /** @lends SKIconPanelView.prototype */

        isPhoneAvailable: true,

        defaultBouncesNo: 10, // icon animated 10 times

        events:{
            'click .icons-panel .phone.icon-active a'       :'doPhoneTalkStart',
            'click .icons-panel .door.icon-active a'        :'doDialogStart',
            'click .icons-panel .plan a'                    :'doPlanToggle',
            'click .icons-panel .phone:not(.icon-active) a' :'doPhoneToggle',
            'click .icons-panel .mail a'                    :'doMailToggle',
            'click .icons-panel .door a'                    :'doDoorToggle',
            'click .icons-panel .documents a'               :'doDocumentsToggle'
        },

        initialize:function () {
            var me = this;
            me.icon_lock = {};
            var events = this.sim_events = SKApp.user.simulation.events;
            events.on('add', function (event) {
                if (event.getTypeSlug() === 'mail') {
                    me.updateMailCounter();
                    me.startAnimation('.' + event.getTypeSlug());
                    if (event.get('type') === 'MS') {
                        SKApp.user.simulation.mailClient.once('init_completed', function() {
                            this.view.once('render_finished', function() {
                                this.renderWriteCustomNewEmailScreen();
                            });
                        });
                    }
                } else if (event.getTypeSlug() === 'document') {
                    me.startAnimation('.documents');

                    if (event.get('type') === 'D') {
                        me.documentId = event.get('data')['id'];
                    }
                } else if (event.getTypeSlug() === 'phone') {
                    me.$('.phone').attr('data-event-id', event.cid);

                    var data = event.get('data');

                    if (undefined == data[2] && this.isPhoneAvailable == true) {
                        // user can`t ignore call
                        var callbackFunction = function () {
                            me.runPhoneTalkStart(me.$('.phone').attr('data-event-id'));
                        };
                    } else {
                        // user can ignore call
                        var callbackFunction = function () {
                            if (event.getStatus() === 'waiting' && undefined !== data[2]) {
                                event.setStatus('completed');
                                event.ignore(function () {
                                    var history = SKApp.user.simulation.phone_history;
                                    history.fetch();
                                    // event will be linked later, if link event here - it will be handled twice
                                    me.setCounter('.phone', phone_history.where({'is_read': false}).length);
                                });
                            } else {
                                //me.doDeblockingPhoneIcon();
                                if(me.isPhoneAvailable == true){
                                    me.runPhoneTalkStart(me.$('.phone').attr('data-event-id'));
                                }

                            }
                        };
                    }

                    me.startAnimation('.' + event.getTypeSlug(), callbackFunction,  me.getPhoneBounces(data));

                } else if (event.getTypeSlug() === 'visit') {
                    me.$('.door').attr('data-event-id', event.cid);
                    me.startAnimation('.door');
                }
            });

            // Block phone when visit/call going {
            events.on('add', function (event) {
                if (event.getTypeSlug().match(/(phone|visit)$/))  {
                    if ('in progress' === event.getStatus()) {
                        me.doBlockingPhoneIcon();
                    } else {
                        event.on('in progress', function() {
                            me.doBlockingPhoneIcon();
                        });
                    }

                    event.on('complete', function() {
                        me.doDeblockingPhoneIcon();
                    });
                }
            });
            // Block phone when visit/call going }

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

        getPhoneBounces: function(data){
            if (undefined == data[2]) {
                return 2;
            } else {
                return this.defaultBouncesNo;
            }
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

        /**
         * Changes counter value
         * @param selector
         * @param count
         */
        setCounter:function (selector, count) {
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
         * @param {string} selector CSS selector of jQuery li element
         * @param {Function} end_cb called when animation ends
         */
        startAnimation:function (selector, end_cb, bounces) {
            // define bounce_counter
            if (undefined == bounces) {
                var bounce_counter = this.defaultBouncesNo;
            } else {
                var bounce_counter = bounces;
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
                                el.effect("bounce", {times:3, direction:'left'}, 400, bounce_cb);
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

        render:function () {
            var me = this;
            this.$el.html(_.template(icon_panel, {}));
            me.updateMailCounter();
            me.updatePlanCounter();
        },

        doPhoneTalkStart:function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.runPhoneTalkStart($(e.currentTarget).parents('.phone').attr('data-event-id'));
        },

        runPhoneTalkStart: function(sim_event_id) {
            console.log('runPhoneTalkStart!');
            var sim_event = this.sim_events.get(sim_event_id);
            sim_event.setStatus('in progress');
            this.$('.phone').removeClass('icon-active');
            SKApp.user.simulation.window_set.toggle('phone','phoneCall', {sim_event:sim_event});
        },

        doDialogStart:function (e) {
            e.preventDefault();
            e.stopPropagation();
            var sim_event = this.sim_events.get($(e.currentTarget).parents('.door').attr('data-event-id'));
            sim_event.setStatus('in progress');
            SKApp.user.simulation.window_set.toggle('visitor','visitorEntrance', {sim_event:sim_event});
            this.$('.door').removeClass('icon-active');
        },
        doPlanToggle:function (e) {
            e.preventDefault();
            SKApp.user.simulation.window_set.toggle('plan','plan');
        },

        doPhoneToggle:function (e) {
            e.preventDefault();

            if (this.isPhoneAvailable) {
                SKApp.user.simulation.window_set.toggle('phone','phoneMain');
            }
        },
        doDoorToggle:function (e) {
            e.preventDefault();
        },

        doDocumentsToggle:function (e) {
            e.preventDefault();

            if (this.documentId) {
                this.doDocumentViewShow(this.documentId);
                this.documentId = null;
            } else {
                SKApp.user.simulation.window_set.toggle('documents','documents');
            }

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

        },

        doDocumentViewShow: function(docId) {
            var document = SKApp.user.simulation.documents.where({id: docId})[0];
            var window = new SKDocumentsWindow({
                subname: 'documentsFiles',
                document: document,
                fileId: document.get('id')
            });
            window.open();
        },

        /**
         * Blocking phone icon when HERO talk by phone or speak with visitor
         */
        doBlockingPhoneIcon: function() {
            this.$('.phone').addClass('only-active');
            this.isPhoneAvailable = false;
        },

        /**
         * Deblocking phone icon when HERO finished talk by phone or speak with visitor
         */
        doDeblockingPhoneIcon: function() {
            this.$('.phone').removeClass('only-active');
            this.isPhoneAvailable = true;
        }
    });

    return SKIconPanelView;
});
