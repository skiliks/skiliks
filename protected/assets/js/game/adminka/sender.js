sender = {
    commands : {
        //login
        /*auth: 1,
        login: 2,
        logout: 3,
        register: 4,
        lostpass: 5,*/
        checkSession: 200,
        userAccountChangeEmail: 6,
        userAccountChangePassword: 7,
        simulationStart:8,
        simulationGetNewEvents:9,
        dialogsGetSelect:10,
        addTriggerGetList: 11,
        addTriggerAdd: 12,
        addAssessmentGetList:13,
        dayPlanTodoGet:14,
        dayPlanDayPlanGet:15,
        dayPlanTodoAdd:16,
        dayPlanDayPlanAdd:17,
        excelGet:18,
        excelGetWorksheet:19,
        excelSaveDocument:20,
        excelSaveEdit:21,
        excelApplyPaste:22,
        excelApplySumm:23,
        excelApplyAverage:24,
        excelApplyDrawing:25,
        simulationStop:26,
        mailGetFolders:27,
        mailGetMessages: 28,
        mailGetMessage: 29,
        mailGetSettings:30,
        mailSaveSettings:31,
        mailGetReceivers:32,
        mailGetThemes:33,
        mailGetPhrases:34,
        mailMessageDelete:35,
        mailGetMessageFull:36,
        mailMarkRead: 37,
        mailSendMessage: 38,
        mailMessageTransfer: 39,
        mailSaveDraft:40,
        mailMessageReply: 41,
        mailMessageReplyAll: 42,
        mailMessageToPlan: 43,
        mailMessageForward: 44,
        mailSendDraftLetter: 45,
        mailAddTask: 46,
        documentsGetList: 47,
        mailGetDocumentsList: 48,
        mailSaveDocument:49,
        viewerGet:50,
        phoneGetContacts: 51,
        phoneGetHistory:52,
        phoneCallTo:53,
        timerSetNewTime:54,
        phoneGetSelect: 55,
        phoneGetThemes:56,
        phoneCallIgnore:57,
        mailGetInboxUnreadedCount:58,
        dayPlanTodoGetCount:59,
        excelPointsDraw:60,
        excelPointsReload:61,

//админка *****************************************************
        dialogsSelectsRequest: 100,
        dialogsGetPointsRequest: 101,
        saveCharactersPoints: 102,
        eventsSamplesSelectsRequest: 103,
        eventsChoicesSelectsRequest: 104,
        charactersPointsTitlesSelectsRequest:105
    },
    urls : {
        //login
        auth: 1,
        login: config.host.name+'index.php/auth/auth',
        logout: config.host.name+'index.php/auth/logout',
        register: config.host.name+'index.php/registration/save',
        lostpass: config.host.name+'index.php/password/remember',
        checkSession: config.host.name+'index.php/auth/checkSession',
        userAccountChangeEmail: config.host.name+'index.php/userAccount/changeEmail',
        userAccountChangePassword: config.host.name+'index.php/userAccount/changePassword',
        simulationStart: config.host.name+'index.php/simulation/start',
        simulationGetNewEvents: config.host.name+'index.php/events/getState',
        dialogsGetSelect: config.host.name+'index.php/dialog/get',
        addTriggerGetList: config.host.name+'index.php/events/getList',
        addTriggerAdd: config.host.name+'index.php/events/start',
        addAssessmentGetList: config.host.name+'index.php/simulation/getPoint',
        dayPlanTodoGet: config.host.name+'index.php/todo/get',
        dayPlanDayPlanGet: config.host.name+'index.php/dayPlan/get',
        dayPlanTodoAdd: config.host.name+'index.php/todo/add',
        dayPlanDayPlanAdd: config.host.name+'index.php/dayPlan/add',
        excelGet: config.host.name+'index.php/excelDocument/get',
        excelGetWorksheet: config.host.name+'index.php/excelDocument/getWorksheet',
        excelSaveDocument: config.host.name+'index.php/excelDocument/saveDocument',
        excelSaveEdit: config.host.name+'index.php/excelDocument/save',
        excelApplyPaste: config.host.name+'index.php/excelDocument/paste',
        excelApplySumm: config.host.name+'index.php/excelDocument/sum',
        excelApplyAverage: config.host.name+'index.php/excelDocument/avg',
        excelApplyDrawing: config.host.name+'index.php/excelDocument/drawing',
        simulationStop: config.host.name+'index.php/simulation/stop',
        mailGetFolders: config.host.name+'index.php/mail/getFolders',
        mailGetMessages: config.host.name+'index.php/mail/getMessages',
        mailGetMessage: config.host.name+'index.php/mail/getMessage',
        mailGetSettings: config.host.name+'index.php/mail/getSettings',
        mailSaveSettings: config.host.name+'index.php/mail/saveSettings',
        mailGetReceivers: config.host.name+'index.php/mail/getReceivers',
        mailGetThemes: config.host.name+'index.php/mail/getThemes',
        mailGetPhrases: config.host.name+'index.php/mail/getPhrases',
        mailMessageDelete: config.host.name+'index.php/mail/delete',
        mailGetMessageFull: config.host.name+'index.php/mail/getMessage',
        mailMarkRead: config.host.name+'index.php/mail/markRead',
        mailSendMessage: config.host.name+'index.php/mail/sendMessage',
        mailMessageTransfer: config.host.name+'index.php/mail/move',
        mailSaveDraft: config.host.name+'index.php/mail/saveDraft',
        mailMessageReply: config.host.name+'index.php/mail/reply',
        mailMessageReplyAll: config.host.name+'index.php/mail/replyAll',
        mailMessageToPlan: config.host.name+'index.php/mail/toPlan',
        mailMessageForward: config.host.name+'index.php/mail/forward',
        mailSendDraftLetter: config.host.name+'index.php/mail/sendDraft',
        mailAddTask: config.host.name+'index.php/mail/addToPlan',
        documentsGetList: config.host.name+'index.php/myDocuments/getList',
        mailGetDocumentsList: config.host.name+'index.php/myDocuments/getList',
        mailSaveDocument: config.host.name+'index.php/myDocuments/add',
        viewerGet: config.host.name+'index.php/viewer/get',
        phoneGetContacts: config.host.name+'index.php/phone/getContacts',
        phoneGetHistory: config.host.name+'index.php/phone/getList',
        phoneCallTo: config.host.name+'index.php/phone/call',
        timerSetNewTime: config.host.name+'index.php/simulation/changeTime',
        phoneGetSelect: config.host.name+'index.php/dialog/get',
        phoneGetThemes: config.host.name+'index.php/phone/getThemes',
        phoneCallIgnore: config.host.name+'index.php/phone/ignore',
        mailGetInboxUnreadedCount: config.host.name+'index.php/mail/getInboxUnreadedCount',
        dayPlanTodoGetCount: config.host.name+'index.php/todo/getCount',
        excelPointsDraw: config.host.name+'index.php/excelPoints/draw',
        excelPointsReload: config.host.name+'index.php/debug/calcExcel',

        //админка *****************************************************
        dialogsSelectsRequest: config.host.name+'index.php/dialogs/getSelect',
        dialogsGetPointsRequest: config.host.name+'index.php/dialogs/getPoints',
        saveCharactersPoints: config.host.name+'index.php/dialogs/savePoints',
        eventsSamplesSelectsRequest: config.host.name+'index.php/eventsSamples/getSelect',
        eventsChoicesSelectsRequest: config.host.name+'index.php/eventsChoices/getSelect',
        charactersPointsTitlesSelectsRequest: config.host.name+'index.php/heroBehaviour/getSelect'

    },
    sendCommand: function (dataArray, url, commandId, sys, callback){
        var runBlock = 1;
        if(typeof(sys) != 'undefined' && sys==1){
            runBlock = 0;
        }
        var debug_match = location.search.match(/XDEBUG_SESSION_START=(\d+)/);
        if (debug_match !== null) {
            url += '?XDEBUG_SESSION_START=' + debug_match[1];
        }
        if(runBlock==1){loading.waitingDialog({});}

        var result;
        $.ajax({
            data:     dataArray,
            url:      url,
            type:     "POST",
            dataType: "json",
            success: function (data){
                result = data;
                if(runBlock===1){loading.closeWaitingDialog();}
                receiver.parseData(data, commandId, runBlock);
                if (typeof callback !== 'undefined') {
                    callback(data);
                }
            },
            error: function () {
                "use strict";
                if(runBlock===1){loading.closeWaitingDialog();}
                alert("Увы, произошла ошибка! Нам очень жаль и мы постараемся исправить ее как можно скорее");
            }
        });
        return result;
    },
    playerLogin: function(curUserLogin, curUserPass, isByCookie)
    {
        var command = {
            commandId:    this.commands.login,
            email:        curUserLogin,
            pass:         curUserPass,
            is_by_cookie: isByCookie
        };
        this.sendCommand(command, this.urls.login, this.commands.login);
    },
    playerCheckSession: function () {
        this.sendCommand({}, this.urls.checkSession, this.commands.checkSession);
    },
    playerLogout: function()
    {
        var command = {
            commandId: this.commands.logout,
            sid:session.getSid()
        };
        this.sendCommand(command, this.urls.logout, this.commands.logout);
    },
    playerRegister: function(curUserEmail, curUserPass1, curUserPass2)
    {
        var command = {
            commandId:this.commands.register,
            email:curUserEmail,
            pass1:curUserPass1,
            pass2:curUserPass2
        };
        this.sendCommand(command, this.urls.register, this.commands.register);
    },
    playerLostPass: function(curUserEmail)
    {
        var command = {
            commandId:this.commands.lostpass,
            email:curUserEmail
        };

        this.sendCommand(command, this.urls.lostpass, this.commands.lostpass);
    },
    userAccountChangeEmail: function(curUserEmail1, curUserEmail2)
    {
        var command = {
            commandId:this.commands.register,
            sid:session.getSid(),
            email1:curUserEmail1,
            email2:curUserEmail2
        };
        this.sendCommand(command, this.urls.userAccountChangeEmail, this.commands.userAccountChangeEmail);
    },
    userAccountChangePassword: function(curUserPass1, curUserPass2)
    {
        var command = {
            commandId:this.commands.register,
            sid:session.getSid(),
            pass1:curUserPass1,
            pass2:curUserPass2
        };
        this.sendCommand(command, this.urls.userAccountChangePassword, this.commands.userAccountChangePassword);
    },
    simulationStart: function(stype)
    {
        var command = {
            commandId: this.commands.simulationStart,
            sid:session.getSid(),
            stype:stype
        };
        this.sendCommand(command, this.urls.simulationStart, this.commands.simulationStart);
    },
    simulationGetNewEvents: function(logs, windowActive,timeString)
    {
        var command = {
            commandId: this.commands.simulationGetNewEvents,
            sid:session.getSid(),
            logs:logs,
            windowActive:windowActive,
            timeString:timeString
        };
        this.sendCommand(command, this.urls.simulationGetNewEvents, this.commands.simulationGetNewEvents, 1);
    },
    dialogsGetSelect: function(dialogId)
    {
        var command = {
            commandId: this.commands.dialogsGetSelect,
            sid:session.getSid(),
            dialogId:dialogId
        };

        this.sendCommand(command, this.urls.dialogsGetSelect, this.commands.dialogsGetSelect);
    },
    addTriggerGetList: function()
    {
        var command = {
            commandId: this.commands.addTriggerGetList,
            sid:session.getSid()
        };
        this.sendCommand(command, this.urls.addTriggerGetList, this.commands.addTriggerGetList);
    },
    addTriggerAdd: function(id, delay, clearEvents, clearAssessment)
    {
        var command = {
            commandId: this.commands.addTriggerAdd,
            sid:session.getSid(),
            eventCode:id,
            delay:delay,
            clearEvents:clearEvents,
            clearAssessment:clearAssessment
        };
        this.sendCommand(command, this.urls.addTriggerAdd, this.commands.addTriggerAdd);
    },
    addAssessmentGetList: function()
    {
        var command = {
            commandId: this.commands.addAssessmentGetList,
            sid:session.getSid()
        };
        this.sendCommand(command, this.urls.addAssessmentGetList, this.commands.addAssessmentGetList, 1);
    },
    dayPlanTodoGet: function()
    {
        var command = {
            commandId: this.commands.dayPlanTodoGet,
            sid:session.getSid()
        };
        this.sendCommand(command, this.urls.dayPlanTodoGet, this.commands.dayPlanTodoGet);
    },
    dayPlanDayPlanGet: function()
    {
        var command = {
            commandId: this.commands.dayPlanDayPlanGet,
            sid:session.getSid()
        };
        this.sendCommand(command, this.urls.dayPlanDayPlanGet, this.commands.dayPlanDayPlanGet);
    },
    dayPlanTodoAdd: function(taskId)
    {
        var command = {
            commandId: this.commands.dayPlanTodoAdd,
            sid:session.getSid(),
            taskId:taskId
        };
        this.sendCommand(command, this.urls.dayPlanTodoAdd, this.commands.dayPlanTodoAdd);
    },
    dayPlanDayPlanAdd: function(taskId, time, day)
    {
        var command = {
            commandId: this.commands.dayPlanDayPlanAdd,
            sid:session.getSid(),
            taskId:taskId,
            time:time,
            day:day
        };
        this.sendCommand(command, this.urls.dayPlanDayPlanAdd, this.commands.dayPlanDayPlanAdd);
    },
    excelGet: function(fileId)
    {
        var command = {
            commandId:this.commands.excelGet,
            sid:session.getSid(),
            fileId:fileId
        };
        this.sendCommand(command, this.urls.excelGet, this.commands.excelGet);
    },
    excelGetWorksheet: function(id)
    {
        var command = {
            commandId:this.commands.excelGetWorksheet,
            sid:session.getSid(),
            id: id
        };
        this.sendCommand(command, this.urls.excelGetWorksheet, this.commands.excelGetWorksheet);
    },
    excelSaveDocument: function()
    {
        var command = {
            commandId:this.commands.excelSaveDocument,
            sid:session.getSid()
        };
        this.sendCommand(command, this.urls.excelSaveDocument, this.commands.excelSaveDocument);
    },
    excelSaveEdit: function(id, string, column, formula)
    {
        var command = {
            commandId:this.commands.excelSaveEdit,
            sid:session.getSid(),
            id:id,
            string: string,
            column: column,
            formula: formula
        };
        this.sendCommand(command, this.urls.excelSaveEdit, this.commands.excelSaveEdit);
    },
    excelApplyPaste: function(id, fromId, string, column, range)
    {
        var command = {
            commandId:this.commands.excelApplyPaste,
            sid:session.getSid(),
            id:id,
            fromId: fromId,
            string: string,
            column: column,
            range: range
        };
        this.sendCommand(command, this.urls.excelApplyPaste, this.commands.excelApplyPaste);
    },
    excelApplySumm: function(id, range)
    {
        var command = {
            commandId:this.commands.excelApplySumm,
            sid:session.getSid(),
            id:id,
            range: range
        };
        this.sendCommand(command, this.urls.excelApplySumm, this.commands.excelApplySumm);
    },
    excelApplyAverage: function(id, range)
    {
        var command = {
            commandId:this.commands.excelApplyAverage,
            sid:session.getSid(),
            id:id,
            range: range
        };
        this.sendCommand(command, this.urls.excelApplyAverage, this.commands.excelApplyAverage);
    },
    excelApplyDrawing: function (id, string, column, target)
    {
        var command = {
            commandId:this.commands.excelApplyDrawing,
            sid:session.getSid(),
            id:id,
            string: string,
            column:column,
            target:target
        };
        this.sendCommand(command, this.urls.excelApplyDrawing, this.commands.excelApplyDrawing);
    },
    simulationStop: function(logs, windowActive, timeString)
    {
        var command = {
            commandId: this.commands.simulationStop,
            sid:session.getSid(),
            logs:logs,
            windowActive:windowActive,
            timeString:timeString
        };
        this.sendCommand(command, this.urls.simulationStop, this.commands.simulationStop);
    },
    mailGetFolders: function()
    {
        var command = {
            commandId: this.commands.mailGetFolders,
            sid:session.getSid()
        };
        this.sendCommand(command, this.urls.mailGetFolders, this.commands.mailGetFolders);
    },
    mailGetMessages: function(folderId, order, order_type)
    {
        var command = {
            commandId: this.commands.mailGetMessages,
            sid:session.getSid(),
            folderId:folderId,
            order:order,
            order_type:order_type
        };
        this.sendCommand(command, this.urls.mailGetMessages, this.commands.mailGetMessages);
    },
    mailGetMessage: function(id)
    {
        var command = {
            commandId: this.commands.mailGetMessage,
            sid:session.getSid(),
            id:id
        };
        this.sendCommand(command, this.urls.mailGetMessage, this.commands.mailGetMessage, 1);
    },
    mailGetSettings: function()
    {
        var command = {
            commandId: this.commands.mailGetSettings,
            sid:session.getSid()
        };
        this.sendCommand(command, this.urls.mailGetSettings, this.commands.mailGetSettings);
    },
    mailSaveSettings: function(messageArriveSound)
    {
        var command = {
            commandId: this.commands.mailSaveSettings,
            sid:session.getSid(),
            messageArriveSound:messageArriveSound
        };
        this.sendCommand(command, this.urls.mailSaveSettings, this.commands.mailSaveSettings);
    },
    mailGetReceivers: function()
    {
        var command = {
            commandId: this.commands.mailGetReceivers,
            sid:session.getSid()
        };
        this.sendCommand(command, this.urls.mailGetReceivers, this.commands.mailGetReceivers);
    },
    mailGetThemes: function(receivers)
    {
        var command = {
            commandId:      this.commands.mailGetThemes,
            sid:            session.getSid(),
            receivers:      receivers,
            forwardEmailId: $('#mailEmulatorNewLetterThemeBox').attr('data-subject-id')
        };
        this.sendCommand(command, this.urls.mailGetThemes, this.commands.mailGetThemes);
    },
    mailGetPhrases: function(id, callback)
    {
        var command = {
            commandId:                      this.commands.mailGetPhrases,
            sid:                            session.getSid(),
            id:                             id,
            forwardLetterCharacterThemesId: $('#mailEmulatorNewLetterThemeBox').attr('data-character-subject-id')
        };
        this.sendCommand(command, this.urls.mailGetPhrases, this.commands.mailGetPhrases, undefined, callback);
    },
    mailMessageDelete: function(id)
    {
        var command = {
            commandId: this.commands.mailMessageDelete,
            sid:session.getSid(),
            id:id
        };
        this.sendCommand(command, this.urls.mailMessageDelete, this.commands.mailMessageDelete);
    },
    mailGetMessageFull: function(id)
    {
        var command = {
            commandId: this.commands.mailGetMessageFull,
            sid:session.getSid(),
            id:id
        };
        this.sendCommand(command, this.urls.mailGetMessageFull, this.commands.mailGetMessageFull);
    },
    mailMarkRead: function(id)
    {
        var command = {
            commandId: this.commands.mailMarkRead,
            sid:session.getSid(),
            id:id
        };
        this.sendCommand(command, this.urls.mailMarkRead, this.commands.mailMarkRead, 1);
    },
    mailSendMessage: function(curMesage, receivers, copies, subject, phrases, letterType, fileId, timeString)
    {
        var command = {
            commandId: this.commands.mailSendMessage,
            sid:session.getSid(),
            messageId:curMesage,
            receivers:receivers,
            copies:copies,
            subject:subject,
            phrases:phrases,
            letterType:letterType,
            fileId:fileId,
            timeString:timeString
        };
        return this.sendCommand(command, this.urls.mailSendMessage, this.commands.mailSendMessage);
    },
    mailMessageTransfer: function(folderId, messageId)
    {
        var command = {
            commandId: this.commands.mailMessageTransfer,
            sid:session.getSid(),
            folderId:folderId,
            messageId:messageId
        };
        this.sendCommand(command, this.urls.mailMessageTransfer, this.commands.mailMessageTransfer);
    },
    mailSaveDraft: function(curMesage, receivers, copies, subject, phrases, letterType, fileId, timeString)
    {
        var command = {
            commandId: this.commands.mailSaveDraft,
            sid:session.getSid(),
            messageId:curMesage,
            receivers:receivers,
            copies:copies,
            subject:subject,
            phrases:phrases,
            letterType:letterType,
            fileId:fileId,
            timeString:timeString
        };
        this.sendCommand(command, this.urls.mailSaveDraft, this.commands.mailSaveDraft);
    },
    mailMessageReply: function(MessageId)
    {
        var command = {
            commandId: this.commands.mailMessageReply,
            sid:session.getSid(),
            id:MessageId
        };
        this.sendCommand(command, this.urls.mailMessageReply, this.commands.mailMessageReply);
    },
    mailMessageReplyAll: function(MessageId)
    {
        var command = {
            commandId: this.commands.mailMessageReplyAll,
            sid:session.getSid(),
            id:MessageId
        };
        this.sendCommand(command, this.urls.mailMessageReplyAll, this.commands.mailMessageReplyAll);
    },
    mailMessageToPlan: function(MessageId)
    {
        var command = {
            commandId: this.commands.mailMessageToPlan,
            sid:session.getSid(),
            id:MessageId
        };
        this.sendCommand(command, this.urls.mailMessageToPlan, this.commands.mailMessageToPlan);
    },
    mailMessageForward: function(MessageId)
    {
        var command = {
            commandId: this.commands.mailMessageForward,
            sid:session.getSid(),
            id:MessageId
        };
        this.sendCommand(command, this.urls.mailMessageForward, this.commands.mailMessageForward);
    },
    mailSendDraftLetter: function(MessageId)
    {
        var command = {
            commandId: this.commands.mailSendDraftLetter,
            sid:session.getSid(),
            id:MessageId
        };
        this.sendCommand(command, this.urls.mailSendDraftLetter, this.commands.mailSendDraftLetter);
    },
    mailAddTask: function(taskId, messageId)
    {
        var command = {
            commandId: this.commands.mailAddTask,
            sid:session.getSid(),
            id:taskId,
            messageId:messageId
        };
        this.sendCommand(command, this.urls.mailAddTask, this.commands.mailAddTask);
    },
    documentsGetList: function()
    {
        var command = {
            commandId: this.commands.documentsGetList,
            sid:session.getSid()
        };
        this.sendCommand(command, this.urls.documentsGetList, this.commands.documentsGetList);
    },
    mailGetDocumentsList: function()
    {
        var command = {
            commandId: this.commands.mailGetDocumentsList,
            sid:session.getSid()
        };
        this.sendCommand(command, this.urls.mailGetDocumentsList, this.commands.mailGetDocumentsList);
    },
    mailSaveDocument: function(id)
    {
        var command = {
            commandId: this.commands.mailSaveDocument,
            sid:session.getSid(),
            fileId:id
        };
        this.sendCommand(command, this.urls.mailSaveDocument, this.commands.mailSaveDocument);
    },
    viewerGet: function(fileId)
    {
        var command = {
            commandId: this.commands.viewerGet,
            sid:session.getSid(),
            fileId:fileId
        };
        this.sendCommand(command, this.urls.viewerGet, this.commands.viewerGet);
    },
    phoneGetContacts: function()
    {
        var command = {
            commandId: this.commands.phoneGetContacts,
            sid:session.getSid()
        };
        this.sendCommand(command, this.urls.phoneGetContacts, this.commands.phoneGetContacts);
    },
    phoneGetHistory: function()
    {
        var command = {
            commandId: this.commands.phoneGetHistory,
            sid:session.getSid()
        };
        this.sendCommand(command, this.urls.phoneGetHistory, this.commands.phoneGetHistory);
    },
    phoneCallTo: function(companion, themeId)
    {
        var command = {
            commandId: this.commands.phoneCallTo,
            sid:session.getSid(),
            id:companion,
            themeId:themeId
        };
        this.sendCommand(command, this.urls.phoneCallTo, this.commands.phoneCallTo);
    },
    timerSetNewTime: function(newTimeH,newTimeM)
    {
        var command = {
            commandId: this.commands.timerSetNewTime,
            sid:session.getSid(),
            hour:newTimeH,
            min:newTimeM
        };
        this.sendCommand(command, this.urls.timerSetNewTime, this.commands.timerSetNewTime);
    },
    phoneGetSelect: function(dialogId)
    {
        var command = {
            commandId: this.commands.phoneGetSelect,
            sid:session.getSid(),
            dialogId:dialogId,
            timeString:timer.getCurTimeFormatted('timeStamp')
        };
        this.sendCommand(command, this.urls.phoneGetSelect, this.commands.phoneGetSelect);
    },
    phoneGetThemes: function(id)
    {
        var command = {
            commandId: this.commands.phoneGetThemes,
            sid:session.getSid(),
            id:id
        };
        this.sendCommand(command, this.urls.phoneGetThemes, this.commands.phoneGetThemes);
    },
    phoneCallIgnore: function(dialogId)
    {
        var command = {
            commandId: this.commands.phoneCallIgnore,
            sid:session.getSid(),
            dialogId:dialogId
        };
        this.sendCommand(command, this.urls.phoneCallIgnore, this.commands.phoneCallIgnore);
    },
    mailGetInboxUnreadedCount: function()
    {
        var command = {
            commandId: this.commands.mailGetInboxUnreadedCount,
            sid:session.getSid()
        };
        this.sendCommand(command, this.urls.mailGetInboxUnreadedCount, this.commands.mailGetInboxUnreadedCount, 1);
    },
    dayPlanTodoGetCount: function()
    {
        var command = {
            commandId: this.commands.dayPlanTodoGetCount,
            sid:session.getSid()
        };
        this.sendCommand(command, this.urls.dayPlanTodoGetCount, this.commands.dayPlanTodoGetCount, 1);
    },
    excelPointsDraw: function()
    {
        var command = {
            commandId: this.commands.excelPointsDraw,
            sid:session.getSid()
        };
        this.sendCommand(command, this.urls.excelPointsDraw, this.commands.excelPointsDraw, 1);
    },
    excelPointsReload: function()
    {
        var command = {
            commandId: this.commands.excelPointsReload,
            sid:session.getSid()
        };
        this.sendCommand(command, this.urls.excelPointsReload, this.commands.excelPointsReload);
    },

//админка *****************************************************
    dialogsSelectsRequest: function()
    {
        var command = {
            commandId:this.commands.dialogsSelectsRequest
        };
        this.sendCommand(command, this.urls.dialogsSelectsRequest, this.commands.dialogsSelectsRequest);
    },
    dialogsGetPointsRequest: function(curDialogId)
    {
        var command = {
            commandId:this.commands.dialogsGetPointsRequest,
            id:curDialogId
        };
        this.sendCommand(command, this.urls.dialogsGetPointsRequest, this.commands.dialogsGetPointsRequest);
    },
    saveCharactersPoints: function(curDialogId,points)
    {
        var command = {
            commandId:this.commands.dialogsGetPointsRequest,
            id:curDialogId,
            points:points
        };
        this.sendCommand(command, this.urls.saveCharactersPoints, this.commands.saveCharactersPoints);
    },
    eventsSamplesSelectsRequest: function()
    {
        var command = {
            commandId:this.commands.eventsSamplesSelectsRequest
        };
        this.sendCommand(command, this.urls.eventsSamplesSelectsRequest, this.commands.eventsSamplesSelectsRequest);
    },
    eventsChoicesSelectsRequest: function()
    {
        var command = {
            commandId:this.commands.eventsChoicesSelectsRequest
        };
        this.sendCommand(command, this.urls.eventsChoicesSelectsRequest, this.commands.eventsChoicesSelectsRequest);
    },
    charactersPointsTitlesSelectsRequest: function()
    {
        var command = {
            commandId:this.commands.charactersPointsTitlesSelectsRequest
        };
        this.sendCommand(command, this.urls.charactersPointsTitlesSelectsRequest, this.commands.charactersPointsTitlesSelectsRequest);
    }
}