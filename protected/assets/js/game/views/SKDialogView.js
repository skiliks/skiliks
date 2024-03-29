/*global Backbone, _, define */

var SKDialogView;
define([
    "jquery/jquery.topzindex.min",
    "text!game/jst/world/dialog.jst"
], function (
        topzindex,
        dialog_template
    ) {
    "use strict";
    /**
     * List of user's phrases added to letter
     * @class SKDialogView
     * @augments Backbone.View
     */
    SKDialogView = Backbone.View.extend({

        /**
         * Базовый HTML DOM контейнер, должен быть уникальным
         * @var jQuery $el
         */
        $el: undefined,

        /**
         * @var String container, стандартный метод определения $el
         */
        container: '.windows-container',

        /**
         * @var Query preventOtherClicksElement
         */
        preventOtherClicksElement: undefined,

        /**
         * Окно закрывается кликом по любому элементу или только по кнопке закрыть
         * true - закрывать кликом по любому элементу
         */
        isCloseWhenClickNotOnDialog: false,

        /**
         * События DOM на которые должна реагировать данная view
         * @var Array events
         */
        'events': {
            'click .mail-popup-button': 'handleClick',
            'click .dialog-close': 'doDialogClose'
        },

        /**
         * Отрисовывать ли кнопку закрытия
         */
        addCloseButton: false,

        /**
         * Constructor
         *
         * @method initialize
         */
        'initialize': function () {
            try {
                this.options.buttons.forEach(function (button) {
                    button.id = _.uniqueId('button_');
                });

                if (undefined !== this.options.addCloseButton) {
                    this.addCloseButton = this.options.addCloseButton;
                }

                if (undefined !== this.options.isPutCenter) {
                    this.isPutCenter = this.options.isPutCenter;
                }

                this.$container = $(this.container);

                this.render();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Системные сообщения могут быть модальными
         */
        renderPreventClickElement: function() {
            try {
                this.preventOtherClicksElement =
                    $('<div class="preventOtherClicks" style="position: absolute; background: none repeat scroll 0 0 transparent; z-index: 1200; height: 100%;;width:100%;"></div>');

                if ($.browser['msie']) {
                    this.preventOtherClicksElement =
                        $('<div class="preventOtherClicks" style="position: absolute; background-color: #000000; opacity: 0; z-index: 1200; height: 100%;;width:100%;"></div>');
                }

                $('.canvas').prepend(this.preventOtherClicksElement);

                var me = this;
                $('.preventOtherClicks').click(function(){
                    if (me.isCloseWhenClickNotOnDialog) {
                        me.remove();
                        me.trigger('click-prevent-click-element');
                    }
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Стандартный родительский метод
         */
        'render': function () {
            try {
                var me = this;

                if (this.options.modal !== false) {
                    // must be first to get Z-index under dialog HTML block
                    this.renderPreventClickElement();
                }

                this.$el = $(_.template(dialog_template, {
                    cls: this.options.class,
                    title: this.options.message,
                    content: this.options.content,
                    buttons: this.options.buttons,
                    addCloseButton: me.addCloseButton
                }));

                this.$el.css({
                    'top': '70px',
                    'position': 'absolute',
                    'width': '100%',
                    'margin': 'auto'
                });
                me.$el.topZIndex();

                $('#messageSystemMessageDiv').css('z-index', 1210);
                me.$el.css('z-index', 1210);

                if ($('.windows-container').length) {
                    $('.windows-container').prepend(this.$el);
                } else {
                    $('body').append(this.$el);
                }

                if (me.isPutCenter) {
                    me.center();
                }

            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param OnClickEvent event
         */
        'handleClick': function (event) {
            try {
                var target = $(event.target).parents('*').andSelf().filter('.mail-popup-button');
                this.options.buttons.forEach(function(button) {
                    if (button.id === target.attr('data-button-id')) {
                        if ((typeof button.onclick) === 'function' ) {
                            button.onclick();
                        }
                    }
                });

                this.cleanUpDOM();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Полная очистка DOM от диалога
         */
        cleanUpDOM: function() {
            try {
                this.$el.remove();
                if (undefined !== this.preventOtherClicksElement) {
                    this.preventOtherClicksElement.remove();
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Стандартный родительский метод
         */
        remove: function() {
            try {
                this.cleanUpDOM();
                this.trigger('close');
                return Backbone.View.prototype.remove.call(this);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Обёртка для закрытия окна
         */
        doDialogClose: function() {
            this.remove();
        },

        /**
         * Стандартный родительский метод
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
        }
    });
    return SKDialogView;
});