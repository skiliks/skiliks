/*global _, Backbone, SKApp, phone*/
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
                    events.getUnreadMailCount(function (count) {
                        me.setCounter('.mail', count);
                    });
                } else if (event.getTypeSlug() === 'document') {
                    me.startAnimation('.' + event.getTypeSlug());
                } else if (event.getTypeSlug() === 'phone') {
                    me.startAnimation('.' + event.getTypeSlug());
                }

            });
            this.render();
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
                            el.effect("bounce", {times:3, direction:'left'}, 400, bounce_cb);
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
            'click .icons-panel .phone.icon-active a': 'doPhoneTalkStart',

            'click .icons-panel .plan a': 'doPlanToggle',
            'click .icons-panel .phone:not(.icon-active) a': 'doPhoneToggle',
            'click .icons-panel .mail a': 'doMailToggle',
            'click .icons-panel .door a': 'doDoorToggle',
            'click .icons-panel .documents a': 'doDocumentsToggle'
        },
        'render':function () {
            this.$el.html(_.template($('#icon_panel').html(), {}));
        },
        'doPhoneTalkStart': function (e) {
            e.preventDefault();
            e.stopPropagation();
            console.log(this.sim_events.getByTypeSlug('phone')[0].get('data'));
            phone.draw('income', this.sim_events.getByTypeSlug('phone')[0].get('data'));
        },
        'doPlanToggle': function (e) {
            e.preventDefault();
        },
        'doPhoneToggle': function (e) {
            e.preventDefault();
            phone.draw();
        },
        'doDoorToggle': function(e) {
            e.preventDefault();
        },
        'doDocumentsToggle':function(e) {
            e.preventDefault();
        },
        'doMailToggle': function (e) {
            e.preventDefault();
            mailEmulator.draw();
        }
    });
})();