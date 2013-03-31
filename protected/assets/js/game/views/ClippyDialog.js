/*global Backbone, _, SKDialogView, SKApp, SKMailTask */

var ClippyDialog;

define([
    "text!game/jst/world/clippy.jst",
    "game/views/SKDialogView"
], function (
    clippy_template
    ) {
    "use strict";

    /**
     * @class SKMailAddToPlanDialog
     * @augments Backbone.View
     */
    ClippyDialog = SKDialogView.extend({

        options: {
            buttons: []
        },


        /**
         * Constructor
         *
         * @method initialize
         */
        'initialize': function (text) {
            this.render(text);
        },

        /**
         * @method
         */
        render:function (text) {
            if (undefined == text) {
                text = '';
            }

            var html = _.template(clippy_template, {
                text: text
            });
            var me = this;

            this.$el = $(html);
            this.$el.topZIndex();
            $('.windows-container').prepend(this.$el);

            $('.clippy').dblclick(function(){
                $(this).remove();
            });
        }

    });

    return BlueScreenDialog;
});
