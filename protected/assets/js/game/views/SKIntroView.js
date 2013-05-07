/*global Backbone, _ */

var SKIntroView;
define([
    'game/views/world/SKApplicationView',
    'game/models/SKApplication',
    'game/views/SKDialogView',
    'text!game/jst/intro.jst',
    'text!game/jst/world/tutorial.jst'
], function (SKApplicationView, SKApplication, SKDialogView, template_intro, tutorial_template) {
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
        },

        appLaunch: function() {
            var me = this;

            window.SKApp = new SKApplication(window.gameConfig);
            window.AppView = new SKApplicationView();

            window.SKApp.simulation.start(function() {
                window.AppView.drawDesktop();
                me.tutorial = new SKDialogView({
                    class: 'tutorial-popup',
                    content: _.template(tutorial_template, {}),
                    buttons: [{
                        id: 'ok',
                        value: 'Начать',
                        onclick: function() {
                            $('.time').removeClass('paused');
                            window.SKApp.simulation.run();
                        }
                    }]
                });
            });
        },
        handleClick: function(){
            this.$el.find('#skiliks_intro').trigger('ended');
        }
    });
    return SKIntroView;
});