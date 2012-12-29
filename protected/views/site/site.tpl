<!doctype html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
    <script type="text/javascript">
        var SKConfig = {$config};
        window.gameVersion = '?v=<?php echo $version ?>';
    </script>
    <title>Skiliks</title>


    <script type="text/javascript">
        _.templateSettings.interpolate = /<@=(.+?)@>/g;
        _.templateSettings.evaluate = /<@(.+?)@>/g;
    </script>

    <script type="text/template" id="start_simulation_menu">
        <div class="world-index-mainDiv">
        <@ for (var simulation in simulations) { @>
            <input type="button" value="Начать симуляцию <@= simulations[simulation] @>" data-sim-id="<@= simulation @>" class="btn simulation-start">
            <br />
            <br />
            <@ } @>
            <input type="button" value="Изменить личные данные" class="btn settings">
            <br>
            <br>
           <input type="button" value="Выход" class="btn logout">
        </div>
    </script>
    <script type="text/template" id="settings_template">
        <div class="world-sett-mainDiv"><form action="">
        <div><label for="pass1" class="def-label-200">Пароль<span style="color: red; ">*</span></label><input id="pass1" type="password" class="span3"></div><br>
        <div><label for="pass2" class="def-label-200">Подтверждение пароля<span style="color: red; ">*</span></label><input id="pass2" type="password" class="span3"></div><br>
        <div class="world-sett-b2Div"><input type="submit" value="Изменить пароль" class="btn"></div>
        <br><br>
        <div class="world-sett-b3Div"><input type="button" onclick="world.drawWorld();" value="Вернуться" class="btn"></div>
        </form></div>
    </script>
    <script type="text/template" id="mail_fixed_text_template">
        <div class="message-container">
            <div class="message-predefined"><@= mail_text.replace(new RegExp('\r?\n', 'g'), '<br />') @></div>
        </div>
    </script>
    <script type="text/template" id="dialog_template">
        <!--suppress HtmlUnknownTag -->
        <div id="messageSystemMessageDiv">
            <div class="mail-popup">
                <div class="mail-popup-tit"><img src="img/mail/type-system-message.png" alt=""></div>
                <p class="mail-popup-text"><@= title @></p>
                <table class="mail-popup-btn">
                    <tbody>
                    <tr>
                        <@ buttons.forEach(function (button) { @>
                        <td>
                            <div class="mail-popup-button" data-button-id="<@= button.id @>">
                                <div><@= button.value @></div>
                            </div>
                        </td>
                        <@ }) @>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </script>

    <script type="text/template" id="login_template">
        <div class="world-index-mainDiv" style="width: 400px; padding-top: 50px">
            <form action="">

                <div><label for="login">E-mail</label><input id="login" type="text" class="input-large"></div>

                <div><label for="pass">Пароль</label><input id="pass" type="password" class="input-large"></div>

                <div class="form-actions"><input type="submit"
                                                 value="Вход" class="btn btn-primary">&nbsp;
                    <input type="button" onclick="register.drawDefault();" value="Регистрация" class="btn">
                    <input type="button" onclick="register.lostPass();" value="Забыли пароль?"
                           class="btn">
                </div>
            </form>
        </div>
    </script>
</head>
<body class="body">
    <?php // need to storage already opened Zoho docs ?>
    <div id="excel-cache" style="display: none; visibility: hidden;"></div>
</body>
</html>