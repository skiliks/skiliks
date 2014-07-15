/*global define, _, SKApp, $*/
/**
 * @class SKManualView
 *
 * @augments SKWindowView
 */
var SKManualView;

define(
    [
        'game/views/SKWindowView',

        'text!game/jst/manual/frame.jst',
        'text!game/jst/manual/contents.jst',
        'text!game/jst/manual/page2.jst',
        'text!game/jst/manual/page4.jst',
        'text!game/jst/manual/page6.jst',
        'text!game/jst/manual/page8.jst',
        'text!game/jst/manual/page10.jst',
        'text!game/jst/manual/page12.jst',
        'text!game/jst/manual/page14.jst'
    ],
    function (
        SKWindowView,
        frame, contents, page2, page4, page6, page8, page10, page12, page14
    ) {
        "use strict";

        SKManualView = SKWindowView.extend({

            addClass: 'manual-window',

            /**
             * События DOM на которые должна реагировать данная view
             * @var Array events
             */
            'events': _.defaults(
                {
                    'click a[data-refer-page]': 'doOpenPage'
                },
                SKWindowView.prototype.events
            ),

            is_first_closed : false,

            /**
             * Стандартный родительский метод
             * @param {jQuery} el
             */
            renderTitle: function (title) {
                $(title).hide();
            },

            /**
             * Стандартный родительский метод
             * @param {jQuery} el
             */
            renderContent: function (el) {
                try {
                    // не показывать крестик закрытия в справке,
                    // пока пользователь не долистает до последней страницы.
                    var required = this.options.model_instance.get('required');

                    el.html(_.template(frame, {
                        'required': required
                    }));

                    [contents, page2, page4, page6, page8].forEach(function(tpl) {
                        el.find('.flyleaf').append(_.template(tpl));
                    });

                    this.pages = el.find('.page');
                    this.closeBtn = el.find('.close-window-manual');
                    el.find('.pages .total').html(this.pages.length).prev('.current').html(1);

                    if (required) {
                        this.closeBtn.hide();
                    }

                    if ($.fn.tooltip.noConflict) {
                        $.fn.tooltip.noConflict();
                    }

                    this.$el.tooltip({
                        tooltipClass: 'person-info-tooltip',
                        position: {
                            my: 'left+10 top-50',
                            at: 'right center',
                            collision: 'flip',
                            within: this.$el
                        },
                        show: {
                            effect: 'fade',
                            delay: 500
                        },
                        hide: false,
                        items: '[data-refer-tooltip]',
                        content: function() {
                            var tooltipId = $(this).attr('data-refer-tooltip');

                            return el.find('.tooltip[data-tooltip="' + tooltipId + '"]').html();
                        }
                    });
                    if(SKApp.isTutorial() && !SKApp.simulation.manual_is_first_closed) {
                        this.$el.css('zIndex', 1001); // у DIV-затемнения индекс 1000
                    }

                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doOpenPage: function(e) {
                try {
                    e.preventDefault();

                    var page = $(e.currentTarget).attr('data-refer-page');
                    var index = this.pages.addClass('hidden').filter('[data-page=' + page + ']').removeClass('hidden').index();

                    this.$el.find('.pages .current').html(index);
                    if (index === this.pages.length) {
                        this.closeBtn.show();
                    }
                    if (index > 0) {
                        this.$el.find('.warn').hide();
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            resize: function() {
                try {
                    var dimensions = [1060, 640];
                    if (window.innerWidth <= 1280 || window.innerHeight <= 750) {
                        dimensions = [845, 510];
                    }

                    SKWindowView.prototype.resize.apply(this, dimensions);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Стандартный родительский метод
             */
            onWindowClose: function() {
                try {
                    var me = this;

                    SKApp.simulation.manual_is_first_closed = true;
                    setTimeout(me.checkTutorialCrashBag, 3000);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            checkTutorialCrashBag: function() {
                try {
                    var isTutorialWindowPresent = (0 < $('.manual-window').length);
                    var isMeetingWindowPresent = (0 < $('#choose-meeting-box').length);
                    var isOverlayVisible = (false == $('.paused-screen').hasClass('hidden'));

                    if ( true == isOverlayVisible
                        && (false == isTutorialWindowPresent && false == isMeetingWindowPresent)) {

                        // для екстренной ситуации - экстренные методы
                        $('.sim-window').remove();

                        var message = new SKDialogView({
                            'message': 'Приносим извинения,<br/>'
                                + 'из-за разрыва интернет соединения данные для игры НЕ были полностью загружены.<br/>'
                                + 'Пожалуйста, начните игру заново.',
                            'buttons': [
                                {
                                    'value': 'Начать заново',
                                    'onclick': function () {
                                        try {
                                            $(window).off('beforeunload');
                                            location.href = location.href;
                                        } catch(exception) {
                                            if (window.Raven) {
                                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                            }
                                        }
                                    }
                                }
                            ]
                        });
                    } else {
                        console.log('all right!');
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            }
        });

        return SKManualView;
    }
);
