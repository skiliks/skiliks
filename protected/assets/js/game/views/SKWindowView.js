/*global Backbone, _, SKConfig*/
/**
 * @class SKWindowView
 *
 * @augments Backbone.View
 */
var SKWindowView;
define(["text!game/jst/window.jst"], function (window_template) {
    "use strict";

    SKWindowView = Backbone.View.extend(
        /** @lends SKWindowView.prototype */
        {
            Windows:{},
            
            container:'.windows-container',
            
            'events':{
                'click .win-close':'doWindowClose',
                'mousedown':'doActivate'
            },
            
            isDisplaySettingsButton: true,
            
            isDisplayCloseWindowsButton: true,

            dimensions: {},

            initialize:function () {
                //this.preLoadWindow();
                if (this.options.model_instance === undefined) {
                    throw 'You need to pass model_instance';
                }
                var sim_window = this.make('div', {"class":'sim-window' + (this.addClass ? ' ' + this.addClass : '')});
                this.$container = $(this.container);
                this.$container.append(sim_window);
                this.setElement(sim_window);
            },


            renderWindow:function () {
                var me = this;
                this.$el.html(_.template(window_template, {
                    title:                       this.title,
                    isDisplaySettingsButton:     this.isDisplaySettingsButton,
                    isDisplayCloseWindowsButton: this.isDisplayCloseWindowsButton
                }));
                this.renderTitle(this.$('header'));
                this.$el.draggable({
                    handle:"header",
                    containment: this.$container,
                    scroll: false,
                    start:function () {
                        if (typeof(me.doStartDrag) !== "undefined") {
                            me.doStartDrag();
                        }
                    },
                    stop:function () {
                        if (typeof(me.doEndDrag) !== "undefined") {
                            me.doEndDrag($(this));
                        }
                    }
                });
                this.renderContent(this.$('.sim-window-content'));
            },
            /**
             * @abstract
             * @param {jQuery} el
             */
            renderTitle:function (el) {
                // Do nothing
            },
            renderContent:function (el) {
                throw 'You need to override it';
            },
            remove:function () {
                this.trigger('close');
                this.stopListening();
                $(window).off('resize', this.$resize);
                Backbone.View.prototype.remove.call(this);
            },

            renderTPL:function (element, templateHtml, userData) {
                var systemData = {assetsUrl:SKConfig.assetsUrl};
                var data = _.defaults(systemData, userData);
                var html = _.template(templateHtml, data);
                $(element).html(html);
            },
            /**
             Creates window

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

                this.resize();
                this.$resize = function() { me.resize(); };
                $(window).on('resize', this.$resize);

                this.center();
            },

            /**
             *
             * @param width (optional)
             * @param height (optional)
             */
            resize: function(width, height) {
                var dimensions = this._calculateDimensions(width, height);
                this.$el.css({
                    width: dimensions.width + 'px',
                    height: dimensions.height + 'px'
                });
            },

            center: function() {
                this.$el.css({
                    top: Math.max(0, ((this.$container.height() - this.$el.outerHeight()) / 2) + this.$container.scrollTop()) + 'px',
                    left: Math.max(0, ((this.$container.width() - this.$el.outerWidth()) / 2) + this.$container.scrollLeft()) + 'px',
                });
            },

            doWindowClose:function () {
                this.options.model_instance.close();
            },
            doActivate:function () {
                this.options.model_instance.setOnTop();
            },
            preLoadWindow:function () {
                var windows = $('.windows-container');
                var stat = $('.main-screen-stat');
                var icons = $('.main-screen-icons');
                var canvas = $('.canvas');
                var margin_top = stat.outerHeight(true);
                var margin_right = icons.outerWidth(true);
                windows.css('margin-top', margin_top+'px');
                windows.height(canvas.height() - margin_top);
                windows.css('margin-right', margin_right+'px');
                windows.width(canvas.width() - margin_right);
            },

            _calculateDimensions: function(width, height) {
                var sd = this.dimensions,
                    rd = {},
                    containerWidth = this.$container.width(),
                    containerHeight = this.$container.height(),
                    specifiedWidth = width || sd.width || sd.maxWidth || '100%',
                    specifiedHeight = height || sd.height || sd.minHeight || '100%',
                    maxWidth, maxHeight, minWidth, minHeight;

                function percent2px(relation, value) {
                    if (typeof value === 'string' && value.charAt(value.length - 1) === '%') {
                        return relation / 100 * value.slice(0, -1);
                    }
                    return +value;
                }

                rd.width = percent2px(containerWidth, specifiedWidth);
                rd.height = percent2px(containerHeight, specifiedHeight);

                rd.width = containerWidth < rd.width ? containerWidth : rd.width;
                rd.height = containerHeight < rd.height ? containerHeight : rd.height;

                if (sd.maxWidth && sd.maxWidth !== specifiedWidth) {
                    maxWidth = percent2px(containerWidth, sd.maxWidth);
                    rd.width = rd.width > maxWidth ? maxWidth : rd.width;
                }

                if (sd.maxHeight && sd.maxHeight !== specifiedHeight) {
                    maxHeight = percent2px(containerHeight, sd.maxHeight);
                    rd.height = rd.height > maxHeight ? maxHeight : rd.height;
                }

                if (sd.minWidth) {
                    minWidth = percent2px(containerWidth, sd.minWidth);
                    rd.width = rd.width < minWidth ? minWidth : rd.width;
                }

                if (sd.minHeight) {
                    minHeight = percent2px(containerHeight, sd.minHeight);
                    rd.height = rd.height < minHeight ? minHeight : rd.height;
                }

                return rd;
            }
        });
    return SKWindowView;
});
