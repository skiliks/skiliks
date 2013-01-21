/*global Backbone, _, SKConfig*/
(function () {
    "use strict";

    window.SKWindowView = Backbone.View.extend({
        Windows:{},
        container:'.canvas',
        'events':{
            'click .win-close':'doWindowClose',
            'mousedown':'doActivate'
        },

        initialize:function () {
            var sim_window = this.make('div', {"class":'sim-window' + (this.addClass ? ' ' + this.addClass : '')});
            $('.canvas').append(sim_window);
            this.setElement(sim_window);
        },


        renderWindow:function () {
            var me = this;
            this.$el.html(_.template($('#window_template').html(), {title:this.title}));
            this.renderTitle(this.$('header'));
            this.$el.draggable({
                handle:"header",
                start:function () {
                    if (me.doStartDrag !== undefined) {
                        me.doStartDrag();
                    }
                },
                end:function () {
                    if (me.doEndDrag !== undefined) {
                        me.doEndDrag();
                    }
                }
            });
            this.renderContent(this.$('.sim-window-content'));
        },
        renderTitle:function (el) {
            // Do nothing
        },
        renderContent:function (el) {
            throw 'You need to override it';
        },
        remove:function () {
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
            this.listenTo(this.options.model_instance, 'change:zindex', function () {
                me.$el.css('zIndex', me.options.model_instance.get('zindex') * 20);
            });
            me.renderWindow(me.$el);
            me.$el.css('zIndex', me.options.model_instance.get('zindex') * 20);
            function alignWindow() {
                me.$el.center();
            }

            alignWindow();

        },

        doWindowClose:function () {
            this.options.model_instance.close();
        },
        doActivate:function () {
            this.options.model_instance.setOnTop();
        }
    });
})();
