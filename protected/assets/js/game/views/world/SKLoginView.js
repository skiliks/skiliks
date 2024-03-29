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

        /**
         * Базовый HTML DOM контейнер, должен быть уникальным
         * @var jQuery el
         */
        'el': 'body',

        /**
         * События DOM на которые должна реагировать данная view
         * @var Array events
         */
        'events': {
            'submit form.login-form': 'doSubmit'
        },

        /**
         * Стандартный родительский метод
         */
        'render': function () {
            try {
                var login_html = _.template(login_template, {});
                this.$el.html(login_html);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param event
         */
        'doSubmit': function (event) {
            try {
                event.preventDefault();
                SKApp.session.login($('#email').val(), $('#pass').val());
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });

    return SKLoginView;
});