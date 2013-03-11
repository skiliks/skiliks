/*global Backbone, _, SKApp */

var SKLoginView;

define([
    "text!game/jst/world/login_template.jst"
], function(
    login_template
) {
    "use strict";
    /**
     * Форма ввода логина и пароля
     *
     * @module skiliks.world
     * @class SKLoginView
     * @augments Backbone.View
     */
    SKLoginView = Backbone.View.extend({
        'el': 'body',
        'events': {
            'submit form.login-form': 'doSubmit'
        },
        'render': function () {
            var login_html = _.template(login_template, {});
            this.$el.html(login_html);
        },
        'doSubmit': function (event) {
            event.preventDefault();
            SKApp.session.login($('#email').val(), $('#pass').val());
        }
    });

    return SKLoginView;
});