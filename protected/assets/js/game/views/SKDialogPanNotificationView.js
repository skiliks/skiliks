/*global Backbone, _, define */

var SKDialogPanNotificationView;
define([
    "jquery/jquery.topzindex.min",
    "text!game/jst/plan/dialog_paln_notification.jst",
    "game/views/SKDialogView"
], function (
        topzindex,
        dialog_paln_notification
    ) {
    "use strict";
    /**
     * List of user's phrases added to letter
     * @class SKDialogView
     * @augments Backbone.View
     */
    SKDialogPanNotificationView = SKDialogView.extend({
        /**
         * @method
         */
        'render': function () {
            try {
                var me = this;

                if (this.options.modal !== false) {
                    // must be first to get Z-index under dialog HTML block
                    this.renderPreventClickElement();
                }

                this.$el = $(_.template(dialog_paln_notification, {
                    cls: this.options.class,
                    title: this.options.message,
                    content: this.options.content,
                    buttons: this.options.buttons,
                    addCloseButton: me.addCloseButton
                }));

                this.$el.css({
                    //'zIndex': 60000, // topZIndex wokrs well
                    'top': '70px',
                    'position': 'absolute',
                    'width': '100%',
                    'margin': 'auto'
                });
                me.$el.topZIndex();

                if ($('.windows-container').length) {
                    $('.windows-container').prepend(this.$el);
                } else {
                    $('body').append(this.$el);
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });
    return SKDialogPanNotificationView;
});