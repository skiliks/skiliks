/*global Backbone, _, SKConfig, $, define, SKApp, console*/
/**
 * @class SKWindowView
 *
 * @augments Backbone.View
 */
var SKWindowView;
define(["text!game/jst/window.jst"],
    function (window_template) {
    "use strict";

    /**
     * @class SKWindowView
     * @augments Backbone.View
     */
    SKWindowView = Backbone.View.extend({

        /**
         * Базовый HTML DOM контейнер, должен быть уникальным
         * @var String container
         */
        container: '.windows-container',

        windowName:null,

        /**
         * События DOM на которые должна реагировать данная view
         * @var Array events
         */
        'events': {
            'click .win-close': 'doWindowClose',
            'mousedown':        'doActivate',
            'click .btn-set':   'doSettingsMenu',
            'click .sim-window-settings .volume-control':'doVolumeChange'
        },

        isDisplaySettingsButton: true,

        isDisplayCloseWindowsButton: true,

        isDragable: false,

        dimensions: {},

        /**
         * Constructor
         * @method initialize
         */
        initialize: function () {
            try {
                if (this.options.model_instance === undefined) {
                    window.Raven.captureMessage('You need to pass model_instance');
                }
                var sim_window = this.make('div', {"class": 'sim-window' + (this.addClass ? ' ' + this.addClass : ''), "id":(this.addId ? this.addId : '')});
                this.$container = $(this.container);
                this.$container.append(sim_window);
                this.setElement(sim_window);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         *
         */
        renderWindow: function () {
            try {
                var me = this;
                this.$el.html(_.template(window_template, {
                    window_uid:                  this.options.model_instance.window_uid,
                    title:                       this.title,
                    isDisplaySettingsButton:     this.isDisplaySettingsButton,
                    isDisplayCloseWindowsButton: this.isDisplayCloseWindowsButton,
                    windowName:                  this.windowName
                }));
                this.renderTitle(this.$('header'));

                if (me.isDragable) {
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
                }

                this.renderContent(me.$('.sim-window-content'), me);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Должен задавать текстовый заголовок в окнах "программ" (почта, ексель, ворд...) игры
         * @abstract
         * @param {jQuery} el
         */
        renderTitle: function (el) {
            // Do nothing
        },

        /**
         * Должен наполнять содержимое окна "программ" (почта, ексель, ворд...) игры
         * @abstract
         * @param {jQuery} el, DOM-node которая должна будет содержать контент окна
         */
        renderContent: function (el) {
            throw new Error ('You need to override it');
        },

        /**
         * Удаление окна, при закрытии его.
         *
         * Также выполняются сопутстующие действия для освобождения памяти.
         */
        remove: function () {
            var me = this;
            try {
                this.trigger('close');
                this.stopListening();
                $(window).off('resize', this.onResize);
                Backbone.View.prototype.remove.call(this);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Отображение HTML кода "окна" по шаблону templateHtml,
         * на основе данных userData,
         * в DOM-node element
         *
         * @param jQuery element
         * @param String templateHtml
         * @param Array userData
         */
        renderTPL: function (element, templateHtml, userData) {
            try {
                var systemData = {assetsUrl: SKApp.get('assetsUrl')};
                var data = _.defaults(systemData, userData);
                var html = _.template(templateHtml, data);
                $(element).html(html);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Стандартный родительский метод
         */
        render: function () {
            try {
                var me = this;
                this.listenTo(this.options.model_instance, 'close', function () {
                    me.remove();
                });
                this.listenTo(this.options.model_instance, 'change:zindex', function () {
                    me.$el.css('zIndex', me.options.model_instance.get('zindex') * 10);
                });

                me.resize();
                me.$el.css('zIndex', me.options.model_instance.get('zindex') * 10);
                me.renderWindow(me.$el);

                this.resize();

                this.onResize = _.bind(this.onResize, me);
                $(window).on('resize', this.onResize);

                this.center();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Мастабирует окно
         * (этот метод должен вызываться для каждого игрового "окна", при изменении размеров окна браузера)
         *
         *
         * @param Number width, in pixels
         * @param Number height, in pixels
         */
        resize: function(width, height) {
            try {
                var dimensions = this._calculateDimensions(width, height);
                this.$el.css({
                    width: dimensions.width + 'px',
                    height: dimensions.height + 'px'
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Центрирование "окна" SKWindowView
         */
        center: function() {
            try {
                this.$el.css({
                    top: Math.max(0, ((this.$container.height() - this.$el.outerHeight()) / 2) + this.$container.scrollTop()) + 'px',
                    left: Math.max(0, ((this.$container.width() - this.$el.outerWidth()) / 2) + this.$container.scrollLeft()) + 'px'
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Ограничения, которые не позволяют "окну" быть
         *  - меньще допустимого размера
         *  - больще области отведённой под окна в интерфейсе игры
         */
        constrain: function() {
            try {
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
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Вызывается при нажатии на крестик закрытия "окна"
         * @param OnClickEvent event
         */
        doWindowClose: function (event) {
            try {
                this.onWindowClose();
                this.options.model_instance.close();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Вызывается при нажатии элемент, который открывает(иконка) "окно"
         * или активирует (любая часть окна) "окно"
         */
        doActivate: function () {
            try {
                this.$('.menu_bar').show();
                this.options.model_instance.setOnTop();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Блокирует окно затемнением.
         * Нужно для блокирования окна почты, при отправке почты, к примеру
         */
        block: function() {
            try {
                if (!this.$('.overlay').length) {
                    this.$el.append(this.make('div', {'class': 'overlay hidden'}));
                }

                this.$('.overlay').removeClass('hidden');
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Разблокирует затемнение окна.
         * Нужно для блокирования окна почты, при отправке почты, к примеру
         */
        unBlock: function() {
            try {
                this.$('.menu_bar').show();
                this.$('.overlay').addClass('hidden');
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Служебрый метод конвертации процентов (ширины/высоты) и пиксели
         * Если в строке valueнет символа "%",
         * то строка value интепритируется как значения в пикселях, а не в "value".
         *
         * @param number relation, 100% в пикселях
         * @param String value, '43%' - размер в %
         *
         * @returns {number}
         */
        percent2px: function (relation, value) {
            if (typeof value === 'string' && value.charAt(value.length - 1) === '%') {
                return relation / 100 * value.slice(0, -1);
            }

            return parseFloat(value);
        },

        /**
         * Расчёт размеров окна, в зависимости парамертов высоты и ширины по умолчанию
         * и текущего размера окна браузера.
         *
         * @param Number width
         * @param Number height
         * @returns {height: Number, width: Number }
         * @private
         */
        _calculateDimensions: function(width, height) {
            try {
                var standardDimensions = this.dimensions,
                    realDimensions = {},
                    containerWidth = this.$container.width(),
                    containerHeight = this.$container.height(),
                    specifiedWidth = width || standardDimensions.width || standardDimensions.maxWidth || '100%',
                    specifiedHeight = height || standardDimensions.height || standardDimensions.maxHeight || '100%',
                    maxWidth, maxHeight, minWidth, minHeight;


                if (standardDimensions.width) {
                    standardDimensions.minWidth = standardDimensions.maxWidth = standardDimensions.width;
                } else if (width) {
                    standardDimensions.minWidth = standardDimensions.maxWidth = width;
                }

                if (standardDimensions.height) {
                    standardDimensions.minHeight = standardDimensions.maxHeight = standardDimensions.height;
                } else if (height) {
                    standardDimensions.minHeight = standardDimensions.maxHeight = height;
                }

                realDimensions.width = this.percent2px(containerWidth, specifiedWidth);
                realDimensions.height = this.percent2px(containerHeight, specifiedHeight);

                realDimensions.width = containerWidth < realDimensions.width ? containerWidth : realDimensions.width;
                realDimensions.height = containerHeight < realDimensions.height ? containerHeight : realDimensions.height;

                if (standardDimensions.maxWidth && standardDimensions.maxWidth !== specifiedWidth) {
                    maxWidth = this.percent2px(containerWidth, standardDimensions.maxWidth);
                    realDimensions.width = realDimensions.width > maxWidth ? maxWidth : realDimensions.width;
                }

                if (standardDimensions.maxHeight && standardDimensions.maxHeight !== specifiedHeight) {
                    maxHeight = this.percent2px(containerHeight, standardDimensions.maxHeight);
                    realDimensions.height = realDimensions.height > maxHeight ? maxHeight : realDimensions.height;
                }

                if (standardDimensions.minWidth) {
                    minWidth = this.percent2px(containerWidth, standardDimensions.minWidth);
                    realDimensions.width = realDimensions.width < minWidth ? minWidth : realDimensions.width;
                }

                if (standardDimensions.minHeight) {
                    minHeight = this.percent2px(containerHeight, standardDimensions.minHeight);
                    realDimensions.height = realDimensions.height < minHeight ? minHeight : realDimensions.height;
                }

                return realDimensions;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Отображает меню при нажатии на шестерёнку.
         * @param OnClickEvent event
         */
        doSettingsMenu:function(event) {
            try {
                var me = this;
                if (me.$('.sim-window-settings').css('display') === 'none') {
                    me.$('.sim-window-settings').css('display', 'block');
                } else{
                    me.$('.sim-window-settings').css('display', 'none');
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Переключает звук в меню окна
         * @param event
         */
        doVolumeChange: function(event) {
            try {
                event.preventDefault();
                event.stopPropagation();
                if ($(event.currentTarget).hasClass('volume-on')) {
                    $(event.currentTarget).text("Выкл.");
                    if ($(event.currentTarget).hasClass('control-mail')) {
                        $(event.currentTarget).removeClass('volume-on');
                        $(event.currentTarget).addClass('volume-off');
                        SKApp.simulation.isPlayIncomingMailSound = false;
                        SKApp.server.api('LogService/SoundSwitcher', {sound_alias:'incoming_mail', is_play:0}, function() {});
                    } else if ($(event.currentTarget).hasClass('control-phone')) {
                        $(event.currentTarget).removeClass('volume-on');
                        $(event.currentTarget).addClass('volume-off');
                        SKApp.simulation.isPlayIncomingCallSound = false;
                        SKApp.server.api('LogService/SoundSwitcher', {sound_alias:'incoming_call', is_play:0}, function() {});
                    } else{
                        throw new Error("Must be has class control-mail or control-phone");
                    }
                } else if ($(event.currentTarget).hasClass('volume-off')) {
                    $(event.currentTarget).text("Вкл.");
                    if ($(event.currentTarget).hasClass('control-mail')) {
                        $(event.currentTarget).removeClass('volume-off');
                        $(event.currentTarget).addClass('volume-on');
                        SKApp.simulation.isPlayIncomingMailSound = true;
                        SKApp.server.api('LogService/SoundSwitcher', {sound_alias:'incoming_mail', is_play:1}, function() {});
                    } else if ($(event.currentTarget).hasClass('control-phone')) {
                        $(event.currentTarget).removeClass('volume-off');
                        $(event.currentTarget).addClass('volume-on');
                        SKApp.simulation.isPlayIncomingCallSound = true;
                        SKApp.server.api('LogService/SoundSwitcher', {sound_alias:'incoming_call', is_play:1}, function() {});
                    } else{
                        throw new Error("Must has class 'control-mail' or 'control-phone'");
                    }
                } else{
                    throw new Error("Must has class 'volume-off' or 'volume-on'");
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Изменяет размеры окна и проверяет их на соответствие
         * ограничениям минимальных и максимальных габаритов окон
         */
        onResize: function() {
            try {
                this.resize();
                this.constrain();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Должен вызываться при нажатии на крестик закрытия окна
         * @abstract
         */
        onWindowClose: function() {        }

    });
    return SKWindowView;
});
