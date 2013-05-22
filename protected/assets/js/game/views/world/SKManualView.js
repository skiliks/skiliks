/*global define, _, $*/
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
        'text!game/jst/manual/page6.jst'
    ],
    function (
        SKWindowView,
        frame, contents, page2, page4, page6
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

            dimensions: {
                width: 1000,
                height: 605
            },

            renderTitle: function (title) {
                $(title).hide();
            },

            renderContent: function (content) {
                content.html(_.template(frame));

                [contents, page2, page4, page6].forEach(function(tpl) {
                    content.find('.flyleaf').append(_.template(tpl));
                });

                this.pages = content.find('.page');
            },

            doOpenPage: function(e) {
                e.preventDefault();

                var page = $(e.currentTarget).attr('data-refer-page');
                this.pages.addClass('hidden').filter('[data-page=' + page + ']').removeClass('hidden');
            }
        });

        return SKManualView;
    }
);
