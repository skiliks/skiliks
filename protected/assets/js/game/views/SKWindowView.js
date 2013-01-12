/*global Backbone, _, SKConfig*/
(function () {
    "use strict";

    window.SKWindowView = Backbone.View.extend({
            Windows:{},
            container:'.canvas',
            'events':{
                'click .win-close':'doWindowClose'
            },

            initialize:function () {
                var sim_window = this.make('div',{"class":'sim-window'});
                $('.canvas').append(sim_window);
                this.setElement(sim_window);
            },


            renderWindow:function (el) {
                this.$el.html(_.template($('#window_template').html(), {title:this.title}));
                this.$el.draggable({handle:"header"});
                this.renderContent(this.$('.sim-window-content'));
            },
            renderContent:function (el) {
                throw 'You need to override it';
            },
            remove:function() {
                this.trigger('close');
                this.stopListening();
                Backbone.View.prototype.remove.call(this);
            },

            renderTPL:function (element, template, userData) {
                var systemData = {assetsUrl:SKConfig.assetsUrl};
                var data = _.defaults(userData, systemData);
                var html = _.template($(template).html(), data);
                $(element).html(html);
            },
            /*
             Creates window

             @return window jquery element
             */
            render:function () {
                var me = this;
                this.listenTo(this.options.model_instance, 'close', function () {
                    me.remove();
                });
                me.renderWindow(me.$el);
                function alignWindow() {
                    me.$el.center();
                }
                alignWindow();
                $(window).on('resize', alignWindow);
                this.on('close', function () {
                    $(window).off('resize', alignWindow);
                });
            },

            doWindowClose:function () {
                this.options.model_instance.close();
            }
        },

        {
            open:function () {
                var SKThisWindow = this;
                var window = new SKThisWindow();
                window.render();
            }
        });
})();
