/*global Backbone, _, SKConfig, $, define, SKApp, console*/
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

        container: '.windows-container',

        windowName:null,

        'events': {
            'click .win-close': 'doWindowClose',
            'mousedown': 'doActivate',
            'click .btn-set':'doSettingsMenu',
            'click .sim-window-settings .volume-control':'doVolumeChange'
        },

        isDisplaySettingsButton: true,

        isDisplayCloseWindowsButton: true,

        dimensions: {},

        /**
         * Constructor
         * @method initialize
         */
        initialize: function () {
            //this.preLoadWindow();
            if (this.options.model_instance === undefined) {
                throw 'You need to pass model_instance';
            }
            var sim_window = this.make('div', {"class": 'sim-window' + (this.addClass ? ' ' + this.addClass : '')});
            this.$container = $(this.container);
            this.$container.append(sim_window);
            this.setElement(sim_window);
        },


        renderWindow: function () {
            var me = this;
            this.$el.html(_.template(window_template, {
                title: this.title,
                isDisplaySettingsButton: this.isDisplaySettingsButton,
                isDisplayCloseWindowsButton: this.isDisplayCloseWindowsButton,
                windowName:this.windowName
            }));
            this.renderTitle(this.$('header'));
            this.$el.draggable({
                handle: "header",
                containment: this.$container,
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
         * @abstract
         * @param {jQuery} el
         */
        renderTitle: function (el) {
            // Do nothing
        },
        renderContent: function (el) {
            throw 'You need to override it';
        },
        remove: function () {
            this.trigger('close');
            this.stopListening();
            $(window).off('resize', this.onResize);
            Backbone.View.prototype.remove.call(this);
        },

        renderTPL: function (element, templateHtml, userData) {
            var systemData = {assetsUrl: SKApp.get('assetsUrl')};
            var data = _.defaults(systemData, userData);
            var html = _.template(templateHtml, data);
            $(element).html(html);
        },
        /**
         Creates window

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

            this.resize();
            this.onResize = function() {
                me.resize();
                me.constrain();
            };
            $(window).on('resize', this.onResize);

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
                left: Math.max(0, ((this.$container.width() - this.$el.outerWidth()) / 2) + this.$container.scrollLeft()) + 'px'
            });
        },

        constrain: function() {
            var position = this.$el.position(),
                dimensions = {
                    width: this.$el.width(),
                    height: this.$el.height()
                },
                bounds = {
                    width: this.$container.width(),
                    height: this.$container.height()
                };

            if (position.left < 0) {
                this.$el.css('left', 0);
            } else if (position.left + dimensions.width > bounds.width) {
                this.$el.css('left', Math.max(bounds.width - dimensions.width, 0));
            }

            if (position.top < 0) {
                this.$el.css('top', 0);
            } else if (position.top + dimensions.height > bounds.height) {
                this.$el.css('top', Math.max(bounds.height - dimensions.height, 0));
            }
        },

        doWindowClose: function () {
            this.options.model_instance.close();
        },
        doActivate: function () {
            this.options.model_instance.setOnTop();
        },

        block: function() {
            if (!this.$('.overlay').length) {
                this.$el.append(this.make('div', {'class': 'overlay hidden'}));
            }

            this.$('.overlay').removeClass('hidden');
        },

        unBlock: function() {
            this.$('.overlay').addClass('hidden');
        },

        _calculateDimensions: function(width, height) {
            var sd = this.dimensions,
                rd = {},
                containerWidth = this.$container.width(),
                containerHeight = this.$container.height(),
                specifiedWidth = width || sd.width || sd.maxWidth || '100%',
                specifiedHeight = height || sd.height || sd.maxHeight || '100%',
                maxWidth, maxHeight, minWidth, minHeight;

            function percent2px(relation, value) {
                if (typeof value === 'string' && value.charAt(value.length - 1) === '%') {
                    return relation / 100 * value.slice(0, -1);
                }
                return +value;
            }

            if (sd.width) {
                sd.minWidth = sd.maxWidth = sd.width;
            } else if (width) {
                sd.minWidth = sd.maxWidth = width;
            }

            if (sd.height) {
                sd.minHeight = sd.maxHeight = sd.height;
            } else if (height) {
                sd.minHeight = sd.maxHeight = height;
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
        },
        doSettingsMenu:function(event) {
            var me = this;
            if(me.$('.sim-window-settings').css('display') === 'none') {
                me.$('.sim-window-settings').css('display', 'block');
            }else{
                me.$('.sim-window-settings').css('display', 'none');
            }
            console.log("Click YES");
        },
        doVolumeChange:function(event) {
            if($(event.currentTarget).hasClass('volume-on')){
                $(event.currentTarget).text("Выкл.");
                if($(event.currentTarget).hasClass('control-mail')) {
                    $(event.currentTarget).removeClass('volume-on');
                    $(event.currentTarget).addClass('volume-off');
                    SKApp.simulation.isPlayIncomingMailSound = false;
                }else if($(event.currentTarget).hasClass('control-phone')){
                    $(event.currentTarget).removeClass('volume-on');
                    $(event.currentTarget).addClass('volume-off');
                    SKApp.simulation.isPlayIncomingCallSound = false;
                }else{
                    throw new Error("Must be has class control-mail or control-phone");
                }
            }else if($(event.currentTarget).hasClass('volume-off')) {
                $(event.currentTarget).text("Вкл.");
                if($(event.currentTarget).hasClass('control-mail')) {
                    $(event.currentTarget).removeClass('volume-off');
                    $(event.currentTarget).addClass('volume-on');
                    SKApp.simulation.isPlayIncomingMailSound = true;
                }else if($(event.currentTarget).hasClass('control-phone')){
                    $(event.currentTarget).removeClass('volume-off');
                    $(event.currentTarget).addClass('volume-on');
                    SKApp.simulation.isPlayIncomingCallSound = true;
                }else{
                    throw new Error("Must be has class control-mail or control-phone");
                }
            }else{
                throw new Error("Must be has class volume-off or volume-on");
            }
            return false;
        }
    });
    return SKWindowView;
});
