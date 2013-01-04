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
        <li class="messenger only-active"><a href="#"></a></li>
        <li class="plan"><a href="" id="icons_todo"></a></li>
        <li class="phone"><a href="" id="icons_phone"></a></li>
        <li class="mail"><a href="" id="icons_email"></a></li>
        <li class="door only-active"><a href="" id="icons_visit"></a></li>
        <li class="documents"><a href="" id="icons_documents"></a></li>
    </ul>
</script>

<script type="text/template" id="debug_panel">
    <form class="form-horizontal span8 offset2 trigger-event">
        <fieldset>
            <legend>Запуск события</legend>
            <div class="control-group">
                <label for="addTriggerSelect" class="control-label">Код события:</label>

                <div class="controls">
                    <input name="code" id="addTriggerSelect" required="required" type="text" class="span2">
                </div>
            </div>
            <div class="control-group">
                <label for="addTriggerDelay" class="control-label">Задержка(игровые
                    минуты):</label>

                <div class="controls">
                    <input name="delay" type="number" id="addTriggerDelay"  required="required" class="span2" value="0">
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <label for="addTriggerClearEvents" class="checkbox">
                        <input name="clear_events" id="addTriggerClearEvents" type="checkbox"/>
                        Очистить очередь событий
                    </label>
                    <label for="addTriggerClearAssessment" class="checkbox">
                        <input name="clear_assessment" id="addTriggerClearAssessment" type="checkbox"/>
                        Очистить очередь оценки
                    </label>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <input type="submit"
                           value="Создать" class="btn btn-primary">

                </div>
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

<script type="text/template" id="visit_template">
    <div>
        <video class="video_background" src="<@= video_src @>" autoplay="autoplay"></video>
        <@ if (remote_replica) { @>
        <div class="visitor-reply"><p class="visitor-replica"><@=remote_replica.text@></p>
            <@ } @>

            <ul class="char-reply" id="dialogControllerAnswers">
                <@ my_replicas.forEach(function (replica) { @>
                <li><p>
                    <a href="" class="replica-select" data-id="<@= replica.id @>">
                        <@= replica.text.replace(/^\s*-/, '—')@>
                    </a>
                    <span></span></p></li>
                <@ }) @>
            </ul>
        </div>
    </div>
</script>

<script type="text/template" id="simulation_template">
    <div style="width: 100%;height: 100%;">
        <div id="canvas" class="canvas">
            <ul class="main-screen-stat">
                <li class="time"><span class="hour"></span><span class="delimiter">:</span><span
                        class="minute"></span></li>
                <li><img src="{$assetsUrl}/img/main-screen/icon-bat-full.png" alt=""/></li>
                <li><a><img alt="" src="{$assetsUrl}/img/main-screen/icon-help.png"></a></li>
            </ul>
            <div class="phone-dialog-div"></div>
            <div class="visitor-container"></div>
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
        <form action="" class="login-form">

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

   <!-- MailClient { -->
    
    <!-- MailClient_BasicHtml: -->
    <script type="text/template" id="MailClient_BasicHtml">
        <div id="<@= id @>" class="mail-emulator-main-div" style="position: absolute; z-index: 50; top: 35px; left: 283px; right: 388px;">
            <section class="mail">
                <header>
                    <h1>Почта</h1>
                    <nav>
                        <ul id="MailClient_FolderLabels"></ul>
                    </nav>
                </header>
                
                <div id="mailEmulatorContentDiv" class="r">
                    <ul class="actions"></ul>
                    <ul class="btn-window">
                        <li>                 
                            <button onclick="SKApp.user.simulation.mailClient.closeWindow();" class="btn-cl"></button>
                        </li>
                    </ul>
                    <div id="<@= contentBlockId @>"></div>
                </div>
            </section>
        </div>
    </script>
    
    <!-- MailClient_Folderlabel: -->
    <script type="text/template" id="MailClient_FolderLabel">
        <li onclick="" id="FOLDER_<@= alias @>" class="<@= isActiveCssClass @> ui-droppable">
            <label class="icon_<@= alias @>" href="#"><@= label @> 
                (<span class="counter"><@= counter @></span>)
            </label>
        </li>
    </script>
    
    <!-- MailClient_IncomeFolderSceleton: -->
    <script type="text/template" id="MailClient_IncomeFolderSceleton">
        <table id="mlTitle" class="ml-title">
            <colgroup>
                <col class="col0">
                <col class="col1">
                <col class="col2">
                <col class="col3">
            </colgroup>
            <tbody>
                <tr>
                    <td onclick="mailEmulator.folderSort('sender')">
                        <span id="mailEmulatorReceivedListSortSender">От кого</span>
                    </td>
                    <td onclick="mailEmulator.folderSort('subject')">
                        <span>Тема</span>
                    </td>
                    <td onclick="mailEmulator.folderSort('time')">
                        <span id="mailEmulatorReceivedListSortTime">Дата получения</span>
                    </td>
                    <td>
                        <div class="attachmentIcon"></div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="<@= listId @>" style="height: 250px; overflow: hidden; overflow-y: scroll;">
            <table class="ml"></table>
        </div>
        <div id="<@= emailPreviewId @>" class="pre"></div>
    </script>
    
    <!-- MailClient_IncomeEmailLine: -->
    <script type="text/template" id="MailClient_IncomeEmailLine">
        <tr data-email-id="<@= emailMySqlId @>"
          class="email-list-line <@= isReadedCssClass @> mail-emulator-received-list-string
          mail-emulator-received-list-string-selected <@= isActiveCssClass @> ui-draggable">
            <td class="col0 mail-emulator-received-list-cell-sender"><@= senderName @></td>
            <td class="col1 mail-emulator-received-list-cell-theme"><@= subject @></td>
            <td class="col2 mail-emulator-received-list-cell-time"><@= sendedAt @></td>
            <td class="col3 mail-emulator-received-list-cell-attach">
                <div class="attachmentIcon" style="<@= isHasAttachmentCss @>"></div>
            </td>
        </tr>
    </script>
    
    <!-- MailClient_EmailPreview: -->
    <script type="text/template" id="MailClient_EmailPreview">
        <div class="mail-view-header">
            <table>
                <tbody>
                    <tr>
                        <th>От кого:</th>
                        <td><strong><@= senderName @></strong></td>
                    </tr>
                    <tr>
                        <th>Кому:</th>
                        <td><@= recipientName @></td>
                   </tr>
                   <tr>
                       <th>Копия:</th>
                       <td><@= copyNamesLine @></td>
                   </tr>
                   <tr>
                       <th>Тема:</th>
                       <td><@= subject @></td>
                   </tr>
                   <tr>
                       <th>Вложение:</th>
                       <td>
                           <@= attachmentFileName @>
                           <span class="save-attachment-icon" 
                               onclick="SKApp.user.simulation.mailClient.saveAttachmentToMyDocuments(<@= attachmentId @>)">
                           </span>
                       </td>
                   </tr>
                </tbody>
            </table>
        </div>
        <div style="overflow-y: scroll; height: 120px; padding: 7px 15px 15px 15px">
            <@= text @>
        </div>
    </script>
    
    <!-- MailClient_ReadEmailSceleton: -->
    <script type="text/template" id="MailClient_ReadEmailSceleton">
        <div id="<@= emailPreviewId @>" class="pre" style="padding-top: 25px;"></div>
    </script>
        
    <!-- MailClient } -->

</head>
<body class="body">
<?php // need to storage already opened Zoho docs ?>
<div id="excel-cache" style="display: none; visibility: hidden;"></div>
</body>
</html>