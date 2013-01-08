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
    <li onclick="<@= action @>" id="FOLDER_<@= alias @>" class="<@= isActiveCssClass @> ui-droppable">
        <label class="icon_<@= alias @>"><@= label @>
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

<!-- MailClient_ActionIcon: -->
<script type="text/template" id="MailClient_ActionIcon">
    <li id="mailEmulatorReceivedButton">
        <a onclick="<@= action @>" class="<@= iconCssClass @>"><@= label @></a>
    </li>
</script>

<script type="text/template" id="mail_fixed_text_template">
    <div class="message-container">
        <div class="message-predefined"><@= mail_text.replace(new RegExp('\r?\n', 'g'), '<br/>') @></div>
    </div>
</script>

<!-- MailClient } -->
