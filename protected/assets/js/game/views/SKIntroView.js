/*global define, Backbone, _, $ */

var SKIntroView;
define([
    'game/views/world/SKApplicationView',
    'game/models/SKApplication',
    'game/models/window/SKWindow',
    'game/views/SKDialogView',
    'text!game/jst/intro.jst',
    'text!game/jst/world/intro_warning.jst',
    'text!game/jst/world/simulation_warning.jst'
], function (SKApplicationView, SKApplication, SKWindow, SKDialogView, template_intro, intro_warning, simulation_warning) {
    "use strict";
    /**
     * Загрузка Интромуви
     * @class IntroView
     * @augments Backbone.View
     */
    SKIntroView = Backbone.View.extend({
        el:"body",
        'events': {
            'click .pass-video': 'handleClick'
        },
        show: function() {
            var me = this,
                content = _.template(intro_warning);

            var warning = new SKDialogView({
                class: 'before-video-warning',
                content: content(),
                buttons: [{
                    id: 'ok',
                    value: 'OK',
                    onclick: function() {
                        me.$el.html(_.template(template_intro));
                        me.$el.find('#skiliks_intro').bind('ended', function () {
                            this.pause();
                            this.src = '';
                            me.$el.empty().removeClass('loading').unbind("mousemove");
                            me.appLaunch();
                        });

                        me.$el.mousemove( function(e) {
                            me.$el.find('.intro-top-icons').toggle(me.$el.outerHeight() / 3 >= e.pageY);
                        });
                    }
                }]
            });
        },

        appLaunch: function() {
            var app = window.SKApp,
                appView = window.AppView,
                content = _.template(simulation_warning),
                warning, onStart;

            app.simulation.on('start', function() {
                this.startPause(function(){});
            });

            onStart = function() {
                var me = this,
                    wnd = new SKWindow({
                        name: 'mainScreen',
                        subname: 'manual',
                        required: true
                    });

                appView.drawDesktop();

                if (app.isTutorial()) {
                    wnd.on('close', function() {
                        appView.frame._hidePausedScreen();
                        appView.frame._toggleClockFreeze(false);
                        me.stopPause();
                    });
                    appView.frame._showPausedScreen();
                    wnd.open();
                } else {
                    me.stopPause();
                }
            };

            if (!app.isLite() && !app.isTutorial()) {
                warning = new SKDialogView({
                    class: 'before-video-warning',
                    content: content(),
                    buttons: [{
                        id: 'ok',
                        value: 'OK',
                        onclick: function() {
                            app.simulation.start(onStart);
                        }
                    }]
                });
            } else {
                app.simulation.start(onStart);
            }
        },

        handleClick: function(){
            this.$el.find('#skiliks_intro').trigger('ended');
        }
    });
    return SKIntroView;
});