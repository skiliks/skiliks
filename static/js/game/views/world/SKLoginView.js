/*global Backbone, _, sender */
$(function() {
    "use strict";
    window.SKLoginView = Backbone.View.extend({
        'initialize': function () {
            this.render();
        },
        'events': {
            'submit form': 'doSubmit'
        },
        'render': function () {
            var login_html = _.template($('#login_template').html(), {});
            this.$el.html(login_html);
        },
        'doSubmit': function (event) {
            event.preventDefault();
            sender.playerLogin($('#login').val(), $('#pass').val(), false);
        }
    });
});