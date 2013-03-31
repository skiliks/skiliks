/*global Backbone, _, SKDialogView, SKApp, SKMailTask */

var BlueScreenDialog;

define([
    "text!game/jst/world/blue_screen.jst",
    "game/views/SKDialogView"
], function (
    blue_screen_template
    ) {
    "use strict";

    /**
     * @class SKMailAddToPlanDialog
     * @augments Backbone.View
     */
    BlueScreenDialog = SKDialogView.extend({

        options: {
            buttons: []
        },

        /**
         * @method
         */
        render:function () {
            var html = _.template(blue_screen_template, {});
            var me = this;

            this.$el = $(html);
            this.$el.topZIndex();
            $('.canvas').prepend(this.$el);

            var cssClass = 'windows-die-blue-screen';

            if (Math.random() < 0.5) {
                // cssClass = 'matrix-has-you';
            }

            $('#windows-die-blue-screen').addClass(cssClass);

            $('#windows-die-blue-screen').dblclick(function() {
                me.cleanUpDOM();
            })
        }

    });

    return BlueScreenDialog;
});
