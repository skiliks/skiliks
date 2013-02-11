
<!-- MailClient { -->


{*
<!-- MailClient_TrashFolderSceleton: -->
<script type="text/template" id="MailClient_TrashFolderSceleton">
    <div id="<@= listId @>" style="height: 250px; overflow: hidden; overflow-y: scroll;">
        <table id="mlTitle" class="ml ml-title">
            <colgroup>
                <col class="col0">
                <col class="col1">
                <col class="col2">
                <col class="col3">
            </colgroup>
            <thead>
                <tr>
                    <th>
                        <span id="mailEmulatorReceivedListSortSender">От кого</span>
                    </th>
                    <th>
                        <span>Тема</span>
                    </th>
                    <th>
                        <span id="mailEmulatorReceivedListSortTime">Дата получения</span>
                    </th>
                    <th>
                        <div class="attachmentIcon"></div>
                    </th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div id="<@= emailPreviewId @>" class="pre"></div>
</script>

<!-- MailClient_SendedFolderSceleton: -->
<script type="text/template" id="MailClient_SendedFolderSceleton">
    <div id="<@= listId @>" style="height: 250px; overflow: hidden; overflow-y: scroll;">
        <table id="mlTitle" class="ml ml-title">
            <colgroup>
                <col class="col0">
                <col class="col1">
                <col class="col2">
                <col class="col3">
            </colgroup>
            <thead>
                <tr>
                    <th>
                        <span id="mailEmulatorReceivedListSortSender">Кому</span>
                    </th>
                    <th>
                        <span>Тема</span>
                    </th>
                    <th>
                        <span id="mailEmulatorReceivedListSortTime">Дата отправки</span>
                    </th>
                    <th>
                        <div class="attachmentIcon"></div>
                    </th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div id="<@= emailPreviewId @>" class="pre"></div>
</script>

<!-- MailClient_TrashEmailLine: -->
<script type="text/template" id="MailClient_TrashEmailLine">
    <tr data-email-id="<@= emailMySqlId @>"
        class="email-list-line <@= isReadedCssClass @> mail-emulator-received-list-string
          mail-emulator-received-list-string-selected <@= isActiveCssClass @> ui-draggable">
        <td class="col0 mail-emulator-received-list-cell-sender"><@= senderName @></td>
        <td class="col1 mail-emulator-received-list-cell-theme"><@= subject @></td>
        <td class="col2 mail-emulator-received-list-cell-time"><@= sendedAt @></td>
        <td class="col3 mail-emulator-received-list-cell-attach">
            <span style="display: none;"><@= isHasAttachment @></span> <!-- for sorting purposes -->
            <div class="attachmentIcon" style="<@= isHasAttachmentCss @>"></div>
        </td>
    </tr>
</script>

<!-- MailClient_SendedEmailLine: -->
<script type="text/template" id="MailClient_SendedEmailLine">
    <tr data-email-id="<@= emailMySqlId @>"
        class="email-list-line <@= isReadedCssClass @> mail-emulator-received-list-string
          mail-emulator-received-list-string-selected <@= isActiveCssClass @> ui-draggable">
        <td class="col0 mail-emulator-received-list-cell-sender"><@= recipientName @></td>
        <td class="col1 mail-emulator-received-list-cell-theme"><@= subject @></td>
        <td class="col2 mail-emulator-received-list-cell-time"><@= sendedAt @></td>
        <td class="col3 mail-emulator-received-list-cell-attach">
            <span style="display: none;"><@= isHasAttachment @></span> <!-- for sorting purposes -->
            <div class="attachmentIcon" style="<@= isHasAttachmentCss @>"></div>
        </td>
    </tr>
</script>

<!-- MailClient_EmailPreview: -->
<script type="text/template" id="MailClient_EmailPreview">
</script>

<!-- MailClient_ReadEmailSceleton: -->
<script type="text/template" id="MailClient_ReadEmailSceleton">
    <div id="<@= emailPreviewId @>" class="pre" style="padding-top: 25px;"></div>
</script>

<!-- MailClient_ActionIcon: -->
<script type="text/template" id="MailClient_ActionIcon">

</script>

<!-- MailClient_NewEmailScreen_Sceleton: -->
<script type="text/template" id="MailClient_NewEmailScreen_Sceleton">
</script>

<!-- MailClient_PhraseItem -->
<script type="text/template" id="MailClient_PhraseItem">
    <li data-uid="<@= phraseUid @>" data-id="<@= phraseId @>">
        <a href="#">
            <span><@= text @></span>
        </a>
    </li>
</script>

<!-- MailClient_PhraseItem -->
<script type="text/template" id="MailClient_AttachmentOptionItem">

</script>

<!-- MailClient_AddToPlanPopUp -->
<script type="text/template" id="MailClient_AddToPlanPopUp">
    <div id="MailClient_AddToPlanPopUp">
        <div class="mail-plan">
            <@= list @>
            <div class="mail-plan-btn">
                <span>
                    <label><@= buttonLabel @></label>
                </span>
            </div>
        </div>
    </div>
</script>

<!-- MailClient_AddToPlanItem -->
<script type="text/template" id="MailClient_AddToPlanItem">
    <div class="mail-plan-item mail-task-<@= id @>" onclick="SKApp.user.simulation.mailClient.addToPlanDialogObject.selectItem(<@= id @>);">
        <label><@= text @></label>
        <span><@= duration @></span>
    </div>
</script>

<!-- @todo: is it used? -->
<script type="text/template" id="mail_fixed_text_template">
    <div class="message-container">
        <div class="message-predefined"><@= mail_text.replace(new RegExp('\r?\n', 'g'), '<br/>') @></div>
    </div>
</script>

<!-- MailClient } -->        *}
