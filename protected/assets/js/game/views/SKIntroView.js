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
//            console.log("Test cache");
//            var intro = $('#skiliks_intro');
//            var height = 800; //высота видео
//            var width = 1280; //ширина видео
//            var scale_height = $(window).height() / height; //коефициент маштабирования по высоте
//            var scale_width = $(window).width() / width; //коефициент маштабирования по ширине
//
//            //очищаем маштабирование
//            intro.css("margin-left", '');
//            intro.css('height', '');
//            intro.css('width', '');
//
//            //проверяем как маштабируеться высота по коефициенту ширины
//            if(scale_height * width >= $(window).width()) {
//                console.log('yes height');
//
//                //если высота больше высоты видео то меняем
//                if($(window).height() > height){
//                    intro.height($(window).height());
//                }
//
//                //если ширина меньше ширины видео то маштабируем по ширине
//                if($(window).width() < width) {
//                    intro.css('margin-left', ($(window).width()-width)/2);
//                }
//                console.log('$(window).width()',$(window).width());
//                return true;
//            }
//
//            //проверяем как маштабируеться ширина по коефициенту высоты
//            if(scale_width*height >= $(window).height()) {
//
//
//                //если ширина больше ширины видео то меняем
//                if($(window).width() > width) {
//                    intro.width($(window).width());
//                }
//
//                //если ширина меньше ширины видео то маштабируем по ширине
//                if($(window).width() < width) {
//                    intro.css('margin-left', ($(window).width()-width)/2);
//                }
//                console.log('yes width');
//                return true;
//            }

            var intro = $('#skiliks_intro');
            var minimalHeight = 800; // минимальная высота видео

            // 1442 - мистика! - надо переделать видео
            var minimalWidth = 1422; // минимальная ширина видео

            // определяем соотношение окна браузера и минимального размера видео
            var widthScale = $(window).width() / minimalWidth;
            var heightScale = $(window).height() / minimalHeight;

            // размер видео должен быть не меньше минимальой длинны и высоты
            // так что округляем мелкие значения до единицы
            if (heightScale < 1) { heightScale = 1;}
            if (widthScale < 1) { widthScale = 1;}

            // выбираем сторону по которой надо масштабировать видео максимально
            var scale = Math.max(
                heightScale,
                widthScale
            );

            // применяем мастабирование к видео-объекту
            intro.width(scale*minimalWidth);
            intro.height(scale*minimalHeight);

            // если ширина видео-объекта больше ширины окна браузера
            // надо оцентрировать видео-объект по ширине в окне браузера
            var marginLeft = 0;
            if ($(window).width() < intro.width()) {
                marginLeft = ($(window).width() - intro.width()) / 2;
            }

            intro.css('margin-left', marginLeft);
        }
    });
    return SKIntroView;
});