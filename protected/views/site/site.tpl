<?php
    $version = '0.015';
    ?>
<!doctype html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <link href="favicon.ico" rel="shortcut icon" type="image/x-icon"/>
    <title>Skiliks</title>

    <link href="/static/js/jquery/jquery-ui.css?v=<?php echo $version ?>" rel="stylesheet"/>
    <link href="/static/js/bootstrap/css/bootstrap.css?v=<?php echo $version ?>" rel="stylesheet"/>
    <link href="/static/js/jquery/jquery-ui-1.8.23.slider.css?v=<?php echo $version ?>" rel="stylesheet"/>
    <link href="/static/js/jquery/jquery.mCustomScrollbar.css?v=<?php echo $version ?>" rel="stylesheet"/>
    <link href="/static/css/main.css?v=<?php echo $version ?>" rel="stylesheet"/>

    <script type="text/javascript">
        window.gameVersion = '?v=<?php echo $version ?>';
    </script>

    <script type="text/javascript" src="/static/js/jquery/jquery-1.7.2.min.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/jquery/jquery-ui-1.8.21.custom.min.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/jquery/jquery.hotkeys.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/jquery/jquery.balloon.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/jquery/jquery.cookies.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/jquery/jquery.mCustomScrollbar.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/jquery/jquery.mousewheel.min.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/jquery.textchange.min.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/bootstrap/js/bootstrap.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/bootstrap/js/bootstrap-alert.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/game/lib/php.js?v=<?php echo $version ?>"></script>

    <script type="text/javascript" src="/static/js/jquery/jquery-ui-1.8.23.custom.min.js?v=<?php echo $version ?>"></script>

    <script type="text/javascript" src="/static/js/underscore.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript">
        _.templateSettings.interpolate = /<@=(.+?)@>/g;
        _.templateSettings.evaluate = /<@(.+?)@>/g;
    </script>
    <script type="text/javascript" src="/static/js/backbone.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/game/models/skwindow.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/game/models/skwindowset.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/game/models/skmailwindow.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/game/models/skdocumentswindow.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/game/models/skdialogwindow.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/game/models/SKServer.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/game/models/SKApplication.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/game/views/SKDialogView.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/game/views/world/SKLoginView.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript"
            src="/static/js/game/views/mail/SKMailLetterBaseView.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript"
            src="/static/js/game/views/mail/SKMailLetterFixedTextView.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript"
            src="/static/js/game/views/mail/SKMailLetterPhraseListView.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/game/views/world/SKSimulationStartView.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript"
            src="/static/js/game/views/world/SKSettingsView.js?v=<?php echo $version ?>"></script>
    <script type="text/javascript" src="/static/js/game/skiliks/engine_loader.js?v=<?php echo $version ?>"></script>

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