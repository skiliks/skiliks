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
        initialize:function () {
            var me = this;
            this.$el.html(_.template(template_intro));
            this.$el.find('#skiliks_intro').bind('ended', function () {
                this.pause();
                this.src = '';
                me.$(this).remove();
                me.$el.unbind("mousemove");
                me.trigger('simulationStart');
            });

            this.$el.mousemove( function(e) {
                if(me.$el.outerHeight() / 3 >= e.pageY){
                    me.$el.find('.intro-top-icons').css('display', 'block');
                }else{
                    me.$el.find('.intro-top-icons').css('display', 'none');
                }
            });
        },
        eventHandler: function() {
            window.SKApp = new SKApplication(window.gameConfig);
            window.AppView = new SKApplicationView();

            this.tutorial = new SKDialogView({
                message: 'Это туториал',
                content: _.template(tutorial_template, {}),
                buttons: [{
                    id: 'ok',
                    value: 'Понял!',
                    onclick: function() {
                        window.SKApp.run();
                    }
                }]
            });
        },
        handleClick: function(){
            this.$el.find('#skiliks_intro').trigger('ended');
        }
    });
    return SKIntroView;
});