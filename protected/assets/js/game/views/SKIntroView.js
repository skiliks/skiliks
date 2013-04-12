/*global Backbone, _ */

var SKIntroView;
define([
    'game/views/world/SKApplicationView',
    'game/models/SKApplication'
], function (SKApplicationView, SKApplication) {
    "use strict";
    /**
     * Загрузка Интромуви
     * @class IntroView
     * @augments Backbone.View
     */
    SKIntroView = Backbone.View.extend({
        initialize:function () {

            $('body').html('<video id="skiliks_intro" src="http://storage.skiliks.com/v1/videos/skiliks_intro_1280.webm" autoplay="autoplay"></video>');
            $('body').append('<div class="intro-top-icons">Пропустить видео <button class="pass-video"/></div>');
            $('#skiliks_intro').bind('ended', function () {
                $(this).remove();
                $(window).unbind("mousemove");
                window.SKIntroView.trigger('simulationStart');
            });
            $('.pass-video').bind('click', function () {
                $('#skiliks_intro').trigger('ended');
            });
            $(window).mousemove( function(e) {
                //console.log("window.height / 3 >= e.pageX : "+parseInt(window.outerHeight) / 3);
                if(window.outerHeight / 3 >= e.pageY){
                    $('.intro-top-icons').css('display', 'block');
                }else{
                    $('.intro-top-icons').css('display', 'none');
                }
                //console.log("window.height: " +window.outerHeight);
                //console.log("x: " + e.pageX);
            });
            $.cookie('intro_is_watched', 'yes');
        },
        eventHandler: function() {
            window.SKApp = new SKApplication(window.gameConfig);
            window.AppView = new SKApplicationView();
        }
    });
    return SKIntroView;
});