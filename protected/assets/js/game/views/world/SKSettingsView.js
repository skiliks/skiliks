/*global _, Backbone, simulation, SKSettingsView:true, world, messages*/
(function () {
    "use strict";
    window.SKSettingsView = Backbone.View.extend({
        'initialize': function () {
            this.render();
        },
        'events': {
            'submit form' : 'doSubmit'
        },
        'render': function () {
            var code = _.template($('#settings_template').html(), {});
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
})();