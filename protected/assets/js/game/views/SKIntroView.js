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
                this.$el.html(_.template(template_intro, {intro_path:SKApp.simulation.getMediaFile('skiliks_intro_1280', 'webm')}));
                this.$el.find('#skiliks_intro').bind('ended', function () {
                    this.pause();
                    this.src = '';
                    me.$el.empty().removeClass('loading').unbind("mousemove");
                    me.appLaunch();
                });
                this.resize();
                if(window.gameConfig.canIntroPassed){
                    this.$el.mousemove( function(e) {
                        if(me.$el.outerHeight() / 3 >= e.pageY){
                            me.$el.find('.intro-top-icons').css('display', 'block');
                        }else{
                            me.$el.find('.intro-top-icons').css('display', 'none');
                        }
                    });
                }
                $(window).on('resize', this.resize);
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
        },
        resize: function() {
            var intro = $('#skiliks_intro');
            console.log('$(window).width()', $(window).width());
            console.log('$(window).height()', $(window).height());
            var scale_height = $(window).height() / 800;
            var scale_width = $(window).width() / 1422;
            //intro.css("margin-left", 0);
            //intro.css("margin-top", 0);
            intro.css('height', '');
            intro.css('width', '');
            if(scale_height * 1422 >= $(window).width()) {
                console.log('yes height');
                intro.height($(window).height());

                /*if($(window).width() < 1280) {
                    intro.css('margin-left', ($(window).width()-1280)/2);
                }
                if($(window).height() < 800){
                    intro.css('margin-top', ($(window).height() - 800)/2);
                }*/
                //intro.css('margin-left', $(window).width() - intro.width() );
                //intro.css('margin-top', 0);
                return;
            }

            if(scale_width*800 >= $(window).height()) {

                /*if($(window).width() < 1280) {
                    intro.css('padding-left', (1280 - $(window).width())/2);
                }
                if($(window).height() < 800){
                    intro.css('padding-top', (800 - $(window).height())/2);
                }*/
                intro.width($(window).width());

                console.log('yes width');
                return;
            }
            /*if($(window).width() / $(window).height() < 1280/800) { //1280x800video size
                console.log("margin-left", ($(window).width() - intro.width()) / 2);
                intro.css("margin-left", ($(window).width() - intro.width()) / 2);

            }else if($(window).width() / $(window).height() > 1280/800){
                console.log("margin-top", ($(window).height() - intro.height()) / 2);
                intro.css("margin-top", ($(window).height() - intro.height()) / 2);
            }*/
            console.log('$(window).height()', $(window).height());
        }
    });
    return SKIntroView;
});