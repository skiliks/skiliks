/*global Backbone, _, SKApp */

var SKLoginView;

define([
    "text!game/jst/world/login_template.jst"
], function(
    login_template
) {
    "use strict";
    SKLoginView = Backbone.View.extend({
        'el': 'body',
        'initialize': function () {
            this.render();
        },
        'events': {
            'submit form.login-form': 'doSubmit'
        },
        'render': function () {
            var login_html = _.template(login_template, {});
            this.$el.html(login_html);
        },
        'doSubmit': function (event) {
            event.preventDefault();
            SKApp.session.login($('#login').val(), $('#pass').val());
        }
    });

    return SKLoginView;
});