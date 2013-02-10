
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
    <div class="mail-view new">
        <div class="mail-view-header">
            <table>
              <tbody>
                <tr>
                    <th>Кому:</th>
                    <td>
                        <ul id="MailClient_RecipientsList" class="tagHandlerContainer">
                            <li class="tagInput"
                                <input type="text" id="MailClient_NewLetterReceiverBox" readonly="readonly">
                            </li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th>Копия:</th>
                    <td>
                        <ul id="MailClient_CopiesList" class="tagHandlerContainer">
                            <li class="tagInput"
                                <input type="text" id="MailClient_NewLetterThemeBox" readonly="readonly">
                            </li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th>Тема:</th>
                    <td id="MailClient_NewLetterSubject">
                        <select class="origin">
                            <option value="0"></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>
                        <div id="MailClient_NewLetterAddAttachmentIcon" class="attach-icon-medium"></div>
                    </th>
                    <td id="MailClient_NewLetterAttachment">
                        <div class="list"></div>
                    </td>
                </tr>
              </tbody>
            </table>
            <button data-type="mail-header" class="switch-size mail-header-btn min"></button>
        </div>
        <div id="mailEmulatorNewLetterDiv" class="mail-new-text mCustomScrollbar _mCS_25">
            <div style="width:100%;" id="mCSB_25" class="mCustomScrollBox">
                <div style="position:relative; top:0;" class="mCSB_container mCS_no_scrollbar">
                    <div class="message-container">
                        <ul class="ui-sortable" id="mailEmulatorNewLetterText"></ul>
                    </div>
                    <div class="previouse-message-text"></div>
                </div>
                <div style="position: absolute; display: none;" class="mCSB_scrollTools">
                    <div style="position:relative;" class="mCSB_draggerContainer">
                        <div style="position: absolute; top: 0px;" class="mCSB_dragger">
                            <div style="position:relative;" class="mCSB_dragger_bar"></div>
                        </div>
                        <div class="mCSB_draggerRail"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mail-tags-bl" style="overflow-y: scroll;">
        <ul id="mailEmulatorNewLetterTextVariantsAdd" class="mail-tags-signs">
        </ul>
        <ul id="mailEmulatorNewLetterTextVariants" class="mail-tags-words">
        </ul>
    </div>
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
    <option data-fileId="<@= fileId @>" value="<@= fileId @>">
        <div style="background: url('/img/documents/<@= iconFile @>'); heigth:25px; width:25px;"></div>
        <@= label @>
    </option>
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
