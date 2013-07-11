/*global define, Backbone, _, $ */

var SKIntroView;
define([
    'game/views/world/SKApplicationView',
    'game/models/SKApplication',
    'game/models/window/SKWindow',
    'game/views/SKDialogView',
    'text!game/jst/intro.jst',
    'text!game/jst/world/intro_warning.jst'
], function (SKApplicationView, SKApplication, SKWindow, SKDialogView, template_intro, intro_warning) {
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
            window.SKApp.simulation.on('start', function() {
                this.startPause(function(){});
            });

            window.SKApp.simulation.start(function() {
                var me = this,
                    wnd = new SKWindow({
                        name: 'mainScreen',
                        subname: 'manual',
                        required: true
                    });

                window.AppView.drawDesktop();

                if (SKApp.isTutorial()) {
                    wnd.on('close', function() {
                        window.AppView.frame._hidePausedScreen();
                        window.AppView.frame._toggleClockFreeze(false);
                        me.stopPause();
                    });
                    window.AppView.frame._showPausedScreen();
                    wnd.open();
                } else {
                    me.stopPause();
                }
            });
        },

        handleClick: function(){
            this.$el.find('#skiliks_intro').trigger('ended');
        }
    });
    return SKIntroView;
});