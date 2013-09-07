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

            'events': _.defaults(
                {
                    'click a[data-refer-page]': 'doOpenPage'
                },
                SKWindowView.prototype.events
            ),

            is_first_closed : false,

            renderTitle: function (title) {
                $(title).hide();
            },

            renderContent: function (content) {
                try {
                    var required = this.options.model_instance.get('required');

                    content.html(_.template(frame, {
                        'required': required
                    }));

                    [contents, page2, page4, page6, page8].forEach(function(tpl) {
                        content.find('.flyleaf').append(_.template(tpl));
                    });

                    this.pages = content.find('.page');
                    this.closeBtn = content.find('.close-window');
                    content.find('.pages .total').html(this.pages.length).prev('.current').html(1);

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

                            return content.find('.tooltip[data-tooltip="' + tooltipId + '"]').html();
                        }
                    });
                    if(SKApp.isTutorial() && !SKApp.simulation.manual_is_first_closed){
                        this.$el.css('zIndex', 50000);
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

                    this.$el.find('.pages .current').html(index + 1);
                    if (index + 1 === this.pages.length) {
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
            onWindowClose: function() {
                SKApp.simulation.manual_is_first_closed = true;
            }
        });

        return SKManualView;
    }
);
