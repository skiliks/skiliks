/*global Backbone*/
(function () {
    "use strict";

    window.SKWindowView = Backbone.View.extend({
        Windows:{},
        container:'.canvas',
        'events': {
            'click .win-close': 'doWindowClose'
        },

        renderWindow: function (el) {
            throw 'You need to override it';
        },

        renderTPL: function (element, template, userData) {
            var data = {};
            var systemData = {assetsUrl:SKConfig.assetsUrl};
            for(var i in systemData){ data[i] = systemData[i]; }
            for(var i in userData){ 
                if(data[i] === undefined){
                    data[i] = userData[i]}
                else{
                    throw new Error("Переменная "+i+" используеться системой!");
                }}
            //console.log(data)
            var html = _.template($(template).html(), data);
            //console.log(html)
            $(element).html(html);
        },
        /*
        Creates window

        @return window jquery element
         */
        render:function () {
            var me = this;
            me.$el.html($('#window_template').html(), {});
            me.renderWindow(me.$('.sim-window'));
            function alignWindow() {
                me.$('.sim-window').center();
            }
            $(window).on('resize', alignWindow);
            this.on('close', function() {
                $(window).off('resize', alignWindow);
            });
        },

        doWindowClose:function () {
            this.$el.html('');
            this.trigger('close');
        }
    });
})();
