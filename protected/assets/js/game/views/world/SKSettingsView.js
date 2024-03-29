/*global _, Backbone, simulation, SKSettingsView:true, world, messages*/

var SKSettingsView;

define([
    "text!game/jst/world/settings_template.jst"
], function(
    settings_template
    ){
    "use strict";
    /**
     * class SKSettingsView
     * @augments Backbone.View
     */
    SKSettingsView = Backbone.View.extend({
        /**
         * Constructor
         * @method initialize
         */
        'initialize': function () {
            this.render();
        },

        /**
         * События DOM на которые должна реагировать данная view
         * @var Array events
         */
        'events': {
            'submit form' : 'doSubmit'
        },

        /**
         * Стандартный родительский метод
         */
        'render': function () {
            var code = _.template(settings_template, {});
            this.$el.html(code);
        },

        /**
         * @method
         * @param event
         */
        'doSubmit': function (event) {
            try {
                event.preventDefault();
                var pass1 = event.target.elements.pass1;
                var pass2 = event.target.elements.pass2;
                if(pass1==='' || pass2===''){
                    var message = 'Заполните все поля';
                    var lang_alert_title = 'Изменение пароля';
                    var lang_confirmed = 'Ок';
                    messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
                    return;
                }
                sender.userAccountChangePassword(curUserPass1, curUserPass2);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });

    return SKSettingsView;
});