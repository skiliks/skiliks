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
        'events': {
            'submit form' : 'doSubmit'
        },
        'render': function () {
            var code = _.template(settings_template, {});
            this.$el.html(code);
        },
        'doSubmit': function (event) {
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
        }
    });

    return SKSettingsView;
});