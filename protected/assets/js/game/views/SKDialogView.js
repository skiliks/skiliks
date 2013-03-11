/*global Backbone, _ */

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
        
        // dialog`s root DOM element
        $el: undefined,
        
        preventOtherClicksElement: undefined,
        
        isCloseWhenClickNotOnDialog: false,

        'events': {
            'click .mail-popup-button': 'handleClick'
        },

        /**
         * Constructor
         *
         * @method initialize
         */
        'initialize': function () {
            this.options.buttons.forEach(function (button) {
                button.id = _.uniqueId('button_');
            });
            this.render();
        },

        /**
         * @method
         */
        renderPreventClickElement: function() {
            this.preventOtherClicksElement = 
                $('<div class="preventOtherClicks" style="position: absolute; background: none repeat scroll 0 0 transparent; height: 100%;;width:100%;"></div>');
            
            this.preventOtherClicksElement.topZIndex();
            
            $('.windows-container').prepend(this.preventOtherClicksElement);
            
            var me = this;
            $('.preventOtherClicks').click(function(){
                if (me.isCloseWhenClickNotOnDialog) {
                    me.cleanUpDOM();
                    me.trigger('click-prevent-click-element');
                }
            });    
        },

        /**
         * @method
         */
        'render': function () {
            
            // must be first to get Z-index under dialog HTML block
            this.renderPreventClickElement();
            
            this.$el = $(_.template(dialog_template, {
                title: this.options.message,
                buttons: this.options.buttons
            }));
 
            this.$el.css({
                //'zIndex': 60000, // topZIndex wokrs well
                'top': '70px',
                'position': 'absolute',
                'width': '100%',
                'margin': 'auto'
            });
            
            this.$el.topZIndex();
            
            $('.windows-container').prepend(this.$el);
        },

        /**
         * @method
         * @param event
         */
        'handleClick': function (event) {
            var target = $(event.target).parents('*').andSelf().filter('.mail-popup-button');
            this.options.buttons.forEach(function(button) {
                if (button.id === target.attr('data-button-id')) {
                    if ((typeof button.onclick) === 'function' ) {
                        button.onclick();
                    }
                }
            });
            
            this.cleanUpDOM();
        },

        /**
         * @method
         */
        cleanUpDOM: function(){
            this.$el.remove();
            if (undefined !== this.preventOtherClicksElement) {
                this.preventOtherClicksElement.remove();
            }
        }
    });
    return SKDialogView;
});