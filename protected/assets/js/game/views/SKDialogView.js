/*global Backbone, _ */
$(function () {
    "use strict";
    /**
     * List of user's phrases added to letter
     * @type {*}
     */
    window.SKDialogView = Backbone.View.extend({
        'initialize': function () {
            this.options.buttons.forEach(function (button) {
                button.id = _.uniqueId('button_');
            });
            this.render();
        },
        'render': function () {
            var template = _.template($('#dialog_template').html(), {
                title: this.options.message,
                buttons: this.options.buttons
            });
            var el = $(template);
            el.css({
                'zIndex': 60000,
                'top': '70px',
                'position': 'absolute',
                'width': '100%',
                'margin': 'auto'
            });
            el.topZIndex();
            $('body').prepend(el);
            this.$el = el;
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
            this.$el.remove();
        }
    });
});