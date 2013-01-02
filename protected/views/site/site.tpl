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
            <input type="button" value="Начать симуляцию <@= simulations[simulation] @>" data-sim-id="<@= simulation @>"
                   class="btn simulation-start">
            <br/>
            <br/>
            <@ } @>
            <input type="button" value="Изменить личные данные" class="btn settings">
            <br>
            <br>
            <input type="button" value="Выход" class="btn logout">
        </div>
    </script>
    <script type="text/template" id="settings_template">
        <div class="world-sett-mainDiv">
            <form action="">
                <div><label for="pass1" class="def-label-200">Пароль<span style="color: red; ">*</span></label><input
                        id="pass1" type="password" class="span3"></div>
                <br>

                <div><label for="pass2" class="def-label-200">Подтверждение пароля<span
                        style="color: red; ">*</span></label><input id="pass2" type="password" class="span3"></div>
                <br>

                <div class="world-sett-b2Div"><input type="submit" value="Изменить пароль" class="btn"></div>
                <br><br>

                <div class="world-sett-b3Div"><input type="button" onclick="world.drawWorld();" value="Вернуться"
                                                     class="btn"></div>
            </form>
        </div>
    </script>
    <script type="text/template" id="mail_fixed_text_template">
        <div class="message-container">
            <div class="message-predefined"><@= mail_text.replace(new RegExp('\r?\n', 'g'), '<br/>') @></div>
        </div>
    </script>
    <script type="text/template" id="icon_panel">
        <ul class="icons-panel">
            <li class="messenger"><a href="#"></a></li>
            <li class="plan"><a href="" id="icons_todo"></a></li>
            <li class="phone"><a href="" id="icons_phone"></a></li>
            <li class="mail"><a href="" id="icons_email"></a></li>
            <li class="door"><a href="" id="icons_visit"></a></li>
            <li class="documents"><a href="" id="icons_documents"></a></li>
        </ul>
    </script>
    <script type="text/template" id="debug_panel">
        <form class="form-horizontal span8 offset2">
            <fieldset>
                <legend>Запуск события</legend>
                <div class="control-group">
                    <label for="addTriggerSelect" class="control-label">Код события:</label>

                    <div class="controls">
                        <input id="addTriggerSelect" type="text" class="span2">
                    </div>
                </div>
                <div class="control-group">
                    <label for="addTriggerDelay" class="control-label">Задержка(игровые
                        минуты):</label>

                    <div class="controls">
                        <input id="addTriggerDelay" type="text" class="span2" value="0">
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <label for="addTriggerClearEvents" class="checkbox">
                            <input id="addTriggerClearEvents" type="checkbox"/>
                            Очистить очередь событий
                        </label>
                        <label for="addTriggerClearAssessment" class="checkbox">
                            <input id="addTriggerClearAssessment" type="checkbox"/>
                            Очистить очередь оценки
                        </label>
                    </div>
                    <input type="button"
                           onclick="addTrigger.add(document.getElementById('addTriggerSelect').value,document.getElementById('addTriggerDelay').value,document.getElementById('addTriggerClearEvents').checked,document.getElementById('addTriggerClearAssessment').checked);"
                           value="Создать" class="btn" style="margin-top:0px; margin-left:25px;">
                </div>
            </fieldset>
        </form>

        <form class="form-horizontal form-set-time span8 offset2">
            <fieldset>
                <legend>Установка времени</legend>
                <div class="control-group">
                    <label for="setTimeHours" class="control-label">Новое время:</label>

                    <div class="controls">
                        <input name="hours" id="setTimeHours" type="number" class="span1" maxlength="2"/>
                        <span> : </span>
                        <input name="minutes" type="number" class="span1" maxlength="2">
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <input type="submit" value="Задать" class="btn btn-primary">
                    </div>
                </div>
                <div class="btn-group">
                    <button class="btn set-time" data-hour="0" data-minute="0">0:00</button>
                    <button class="btn set-time" data-hour="10" data-minute="0">10:00</button>
                    <button class="btn set-time" data-hour="11" data-minute="0">11:00</button>
                    <button class="btn set-time" data-hour="12" data-minute="0">12:00</button>
                    <button class="btn set-time" data-hour="13" data-minute="0">13:00</button>
                    <button class="btn set-time" data-hour="14" data-minute="0">14:00</button>
                    <button class="btn set-time" data-hour="15" data-minute="0">15:00</button>
                    <button class="btn set-time" data-hour="16" data-minute="0">16:00</button>
                    <button class="btn set-time" data-hour="17" data-minute="0">17:00</button>
                    <button class="btn set-time" data-hour="17" data-minute="50">17:50</button>
                </div>
            </fieldset>
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
    <script type="text/template" id="simulation_template">
        <div style="width: 100%;">
            <div id="canvas" class="canvas">
                <ul class="main-screen-stat">
                    <li class="time"><span class="hour"></span><span class="delimiter">:</span><span
                            class="minute"></span></li>
                    <li><img src="{$assetsUrl}/img/main-screen/icon-bat-full.png" alt=""/></li>
                    <li><a><img alt="" src="{$assetsUrl}/img/main-screen/icon-help.png"></a></li>
                </ul>
                <nav class="main-screen-icons"></nav>
            </div>
            <div class="simulation-controls">
                <form class="simulation-buttons span8 offset2">
                    <fieldset>
                        <legend>Действия</legend>
                        <input class="btn btn-simulation-stop" type="button" value="SIM стоп">
                    </fieldset>
                </form>
            <div class="debug-panel"></div>
            </div>

        </div>
        <div id="location" class="location"></div>
        </div>
        <div id="message" class="message"></div>
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