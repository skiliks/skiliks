register = {
    drawDefault: function (){
        document.body.innerHTML = this.defaultHtml;
    },
    lostPass: function (){
        document.body.innerHTML = this.defaultLostPassHtml;
    },
    playerRegisterCheck: function(curUserEmail, curUserPass1, curUserPass2)
    {
        if(curUserEmail=='' || curUserPass1=='' || curUserPass2==''){
            var message = 'Заполните все поля';
            var lang_alert_title = 'Регистрация';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
            return;
        }
        
        if(curUserPass1 != curUserPass2){
            var message = 'Введенные пароли не совпадают';
            var lang_alert_title = 'Регистрация';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
            return;
        }
        
        sender.playerRegister(curUserEmail, curUserPass1, curUserPass2);
    },
    playerLostPassCheck: function(curUserEmail)
    {
        if(curUserEmail==''){
            var message = 'Введите email';
            var lang_alert_title = 'Напоминание пароля';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
            return;
        }
        sender.playerLostPass(curUserEmail);
    },
    defaultHtml: '<div class="reg-def-mainDiv">'+
            '<br><br>'+
            '<label for="email" class="def-label-200">Почта<span style="color: red; ">*</span></label><input id="email" type="text" class="span3"><br>'+
            '<label for="pass1" class="def-label-200">Придумайте пароль:<span style="color: red; ">*</span></label><input id="pass1" type="password" class="span3"><br>'+
            '<label for="pass2" class="def-label-200">Повторите пароль:<span style="color: red; ">*</span></label><input id="pass2" type="password" class="span3"><br>'+
            
            '<div class="reg-def-b1Div"><input type="button" onclick="register.playerRegisterCheck(document.getElementById(\'email\').value, document.getElementById(\'pass1\').value, document.getElementById(\'pass2\').value);" value="Регистрация" class="btn">&nbsp;'+
            '<input type="button" onclick="new SKLoginView()" value="Назад" class="btn"></div><br>'+
            '</div>',
    defaultLostPassHtml: '<div class="reg-def-mainDiv">'+
            '<br><br><label for="email">Почта<font color=red>*</font></label><input id="email" type="text" class="span3"><br>'+
            
            '<div class="reg-lp-b1Div"><input type="button" onclick="register.playerLostPassCheck(document.getElementById(\'email\').value);" value="Восстановить пароль" class="btn">&nbsp;'+
            '<input type="button" onclick="new SKLoginView()" value="Назад" class="btn"></div><br>'+
            '</div>'
}