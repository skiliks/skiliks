/*global Backbone, _ */
(function () {
    "use strict";
    /**
     * List of user's phrases added to letter
     * @type {*}
     */
    window.SKDialogView = Backbone.View.extend({
        
        // dialog`s root DOM element
        $el: undefined,
        
        preventOtherClicksElement: undefined,
        
        'initialize': function () {
            this.options.buttons.forEach(function (button) {
                button.id = _.uniqueId('button_');
            });
            this.render();
        },
        
        renderPreventClickElement: function() {
            this.preventOtherClicksElement = 
                $('<div class="preventOtherClicks" style="position: absolute; background: none repeat scroll 0 0 transparent; height: 100%;;width:100%;"></div>');
            
            this.preventOtherClicksElement.topZIndex();
            
            $('#canvas').prepend(this.preventOtherClicksElement);
            
            $('.preventOtherClicks').click(function(){
                me.close();
            });    
        },
        
        'render': function () {
            
            // must be first to get Z-index under dialog HTML block
            this.renderPreventClickElement();
            
            this.$el = $(_.template($('#dialog_template').html(), {
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
            
            $('body').prepend(this.$el);
        },
        
        'events': {
            'click .mail-popup-button': 'handleClick'
        },
        
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
        
        cleanUpDOM: function(){
            this.$el.remove();
            this.preventOtherClicksElement.remove();  
        }
    });
})();