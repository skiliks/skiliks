/*global Backbone, _, SKConfig*/
/**
 * @class SKWindowView
 *
 * @augments Backbone.View
 */
var SKWindowView;
define(["text!game/jst/window.jst"], function (window_template) {
    "use strict";

    /**
     * @class SKWindowView
     * @augments Backbone.View
     */
    SKWindowView = Backbone.View.extend({
        Windows: {},

        container: '.windows-container',

        'events': {
            'click .win-close': 'doWindowClose',
            'mousedown': 'doActivate'
        },

        isDisplaySettingsButton: true,

        isDisplayCloseWindowsButton: true,

        /**
         * Constructor
         *
         * @method initialize
         */
        initialize: function () {
            this.preLoadWindow();
            if (this.options.model_instance === undefined) {
                throw 'You need to pass model_instance';
            }
            var sim_window = this.make('div', {"class": 'sim-window' + (this.addClass ? ' ' + this.addClass : '')});
            $('.windows-container').append(sim_window);
            this.setElement(sim_window);
        },

        /**
         * @method
         */
        renderWindow: function () {
            var me = this;
            this.$el.html(_.template(window_template, {
                title: this.title,
                isDisplaySettingsButton: this.isDisplaySettingsButton,
                isDisplayCloseWindowsButton: this.isDisplayCloseWindowsButton
            }));
            this.renderTitle(this.$('header'));
            this.$el.draggable({
                handle: "header",
                containment: ".windows-container",
                scroll: false,
                start: function () {
                    if (typeof(me.doStartDrag) !== "undefined") {
                        me.doStartDrag();
                    }
                },
                stop: function () {
                    if (typeof(me.doEndDrag) !== "undefined") {
                        me.doEndDrag($(this));
                    }
                }
            });
            this.renderContent(this.$('.sim-window-content'));
        },

        /**
         * @method
         * @abstract
         * @param {jQuery} el
         */
        renderTitle: function (el) {
            // Do nothing
        },

        /**
         * @method
         * @param el
         */
        renderContent: function (el) {
            throw 'You need to override it';
        },

        /**
         * @method
         */
        remove: function () {
            this.trigger('close');
            this.stopListening();
            Backbone.View.prototype.remove.call(this);
        },

        /**
         * @method
         * @param element
         * @param templateHtml
         * @param userData
         */
        renderTPL: function (element, templateHtml, userData) {
            var systemData = {assetsUrl: SKConfig.assetsUrl};
            var data = _.defaults(systemData, userData);
            var html = _.template(templateHtml, data);
            $(element).html(html);
        },

        /**
         * Creates window
         *
         * @method
         */
        render: function () {
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

        /**
         * @method
         */
        doWindowClose: function () {
            this.options.model_instance.close();
        },

        /**
         * @method
         */
        doActivate: function () {
            this.options.model_instance.setOnTop();
        },

        /**
         * @method
         */
        preLoadWindow: function () {
            var windows = $('.windows-container');
            var stat = $('.main-screen-stat');
            var icons = $('.main-screen-icons');
            var canvas = $('.canvas');
            var margin_top = parseInt(stat.css('margin-top')) + stat.height();
            var margin_right = parseInt(icons.css('margin-right')) + icons.width();
            windows.css('margin-top', margin_top + 'px');
            windows.height(canvas.height() - margin_top);
            windows.css('margin-right', margin_right + 'px');
            windows.width(canvas.width() - margin_right);
        }
    });
    return SKWindowView;
});
