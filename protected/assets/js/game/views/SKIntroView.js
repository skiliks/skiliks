/*global Backbone, _ */

var SKIntroView;
define([
    'game/views/world/SKApplicationView',
    'game/models/SKApplication',
    'text!game/jst/intro.jst'
], function (SKApplicationView, SKApplication, template_intro) {
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
        initialize:function () {
            var me = this;
            //$('body').html('<video id="skiliks_intro" src="http://storage.skiliks.com/v1/videos/skiliks_intro_1280.webm" autoplay="autoplay"></video>');
            //$('body').append('<div class="intro-top-icons">Пропустить видео <button class="pass-video"/></div>');
            this.$el.html(_.template(template_intro));
            this.$el.find('#skiliks_intro').bind('ended', function () {
                me.$(this).remove();
                me.$el.unbind("mousemove");
                me.trigger('simulationStart');
            });
            /*$('.pass-video').bind('click', function () {
                $('#skiliks_intro').trigger('ended');
            });*/
            this.$el.mousemove( function(e) {
                //console.log("window.height / 3 >= e.pageX : "+parseInt(window.outerHeight) / 3);
                //console.log(me.el);
                //console.log(me.$el.outerHeight());
                if(me.$el.outerHeight() / 3 >= e.pageY){
                    me.$el.find('.intro-top-icons').css('display', 'block');
                }else{
                    me.$el.find('.intro-top-icons').css('display', 'none');
                }
                //console.log("window.height: " +window.outerHeight);
                //console.log("x: " + e.pageX);
            });
            $.cookie('intro_is_watched', 'yes', { expires: 365, path: "/" });

        },
        eventHandler: function() {
            window.SKApp = new SKApplication(window.gameConfig);
            window.AppView = new SKApplicationView();
        },
        handleClick: function(){
            this.$el.find('#skiliks_intro').trigger('ended');
        }
    });
    return SKIntroView;
});