/*global define, Backbone, _, $, SKApp */

var SKIntroView;
define([
    'game/views/world/SKApplicationView',
    'game/models/SKApplication',
    'game/models/window/SKWindow',
    'game/views/SKDialogView',
    'text!game/jst/intro.jst',
    'text!game/jst/world/simulation_warning.jst'
], function (SKApplicationView, SKApplication, SKWindow, SKDialogView, template_intro, simulation_warning) {
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
            try{
                var me = this;
                this.$el.html(_.template(template_intro));
                this.$el.find('#skiliks_intro').bind('ended', function () {
                    this.pause();
                    this.src = '';
                    me.$el.empty().removeClass('loading').unbind("mousemove");
                    me.appLaunch();
                });
                this.$el.mousemove( function(e) {
                    if(me.$el.outerHeight() / 3 >= e.pageY){
                        me.$el.find('.intro-top-icons').css('display', 'block');
                    }else{
                        me.$el.find('.intro-top-icons').css('display', 'none');
                    }
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        appLaunch: function() {
            try {
                var app = window.SKApp,
                    appView = window.AppView,
                    content = _.template(simulation_warning),
                    warning, onStart;

                if(false === SKApp.simulation.isDebug()){
                    app.simulation.on('start', function() {
                        this.startPause(function(){});
                    });
                }

                onStart = function() {
                    var me = this,
                        wnd = new SKWindow({
                            name: 'mainScreen',
                            subname: 'manual',
                            required: true
                        });
                    var warning;

                    appView.drawDesktop();

                    if (app.isTutorial()) {
                        wnd.on('close', function() {
                            appView.frame._hidePausedScreen();
                            appView.frame._toggleClockFreeze(false);
                            me.stopPause();
                        });
                        appView.frame._showPausedScreen();
                        wnd.open();
                    } else if(!app.isLite()) {
                        if(false === SKApp.simulation.isDebug()) {
                            $('.time').addClass("paused");
                            warning = new SKDialogView({
                                class: 'before-video-warning',
                                content: content(),
                                isPutCenter: true,
                                buttons: [{
                                    id: 'ok',
                                    value: 'НАЧАТЬ',
                                    onclick: function() {
                                        warning.remove();
                                        me.stopPause();
                                        $('.time').removeClass("paused");
                                    }
                                }]
                            });
                        }
                    }else{
                        me.stopPause();
                    }
                };

                app.simulation.start(onStart);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        handleClick: function(){
            this.$el.find('#skiliks_intro').trigger('ended');
        }
    });
    return SKIntroView;
});