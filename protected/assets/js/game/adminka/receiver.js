receiver = {
    commands : {
        //login
        auth: 1,
        login: 2,
        logout: 3,
        register: 4,
        lostpass: 5,
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
        phoneGetThemes: 56,
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
    parseData: function (data, commandId){
        var json_data_object = eval(data);

        //2 playerLogin
        if(commandId == this.commands.login){
            this.playerLogin(json_data_object);
            return;
        }
        //4 register
        if(commandId == this.commands.register){
            this.registerAction(json_data_object);
            return;
        }
        //5 lostpass
        if(commandId == this.commands.lostpass){
            this.lostpass(json_data_object);
            return;
        }
        //6 userAccountChangeEmail
        if(commandId == this.commands.userAccountChangeEmail){
            this.userAccountChangeEmail(json_data_object);
            return;
        }
        //7 userAccountChangePassword
        if(commandId == this.commands.userAccountChangePassword){
            this.userAccountChangePassword(json_data_object);
            return;
        }
        //8 simulationStart
        if(commandId == this.commands.simulationStart){
            this.simulationStart(json_data_object);
            return;
        }
        //200 checkSession
        if (commandId == this.commands.checkSession) {
            this.checkSession(json_data_object);
            return;
        }
        //9 simulationStart
        if(commandId == this.commands.simulationGetNewEvents){
            this.simulationGetNewEvents(json_data_object);
            return;
        }
        //10 dialogsGetSelect
        if(commandId == this.commands.dialogsGetSelect){
            this.dialogsGetSelect(json_data_object);
            return;
        }
        //11
        if(commandId == this.commands.addTriggerGetList){
            this.addTriggerGetList(json_data_object);
            return;
        }
        //12
        if(commandId == this.commands.addTriggerAdd){
            this.addTriggerAdd(json_data_object);
            return;
        }
        //13 addAssessmentGetList
        if(commandId == this.commands.addAssessmentGetList){
            this.addAssessmentGetList(json_data_object);
            return;
        }
        //14 dayPlanTodoGet
        if(commandId == this.commands.dayPlanTodoGet){
            this.dayPlanTodoGet(json_data_object);
            return;
        }
        //15 dayPlanDayPlanGet
        if(commandId == this.commands.dayPlanDayPlanGet){
            this.dayPlanDayPlanGet(json_data_object);
            return;
        }
        //16 dayPlanTodoAdd
        if(commandId == this.commands.dayPlanTodoAdd){
            this.dayPlanTodoAdd(json_data_object);
            return;
        }
        //17 dayPlanDayPlanAdd
        if(commandId == this.commands.dayPlanDayPlanAdd){
            this.dayPlanDayPlanAdd(json_data_object);
            return;
        }
        //18 excelGet
        if(commandId == this.commands.excelGet){
            this.excelGet(json_data_object);
            return;
        }
        //19 excelGetWorksheet
        if(commandId == this.commands.excelGetWorksheet){
            this.excelGetWorksheet(json_data_object);
            return;
        }
        //20 excelSaveDocument
        if(commandId == this.commands.excelSaveDocument){
            this.excelSaveDocument(json_data_object);
            return;
        }
        //21 excelSaveEdit
        if(commandId == this.commands.excelSaveEdit){
            this.excelSaveEdit(json_data_object);
            return;
        }
        //22 excelApplyPaste
        if(commandId == this.commands.excelApplyPaste){
            this.excelApplyPaste(json_data_object);
            return;
        }
        //23 excelApplySumm
        if(commandId == this.commands.excelApplySumm){
            this.excelApplySumm(json_data_object);
            return;
        }
        //24 excelApplyAverage
        if(commandId == this.commands.excelApplyAverage){
            this.excelApplyAverage(json_data_object);
            return;
        }
        //25 excelApplyDrawing
        if(commandId == this.commands.excelApplyDrawing){
            this.excelApplyDrawing(json_data_object);
            return;
        }
        //26 simulationStop
        if(commandId == this.commands.simulationStop){
            this.simulationStop(json_data_object);
            return;
        }
        //27 mailGetFolders
        if(commandId == this.commands.mailGetFolders){
            this.mailGetFolders(json_data_object);
            return;
        }
        //28 mailGetMessages
        if(commandId == this.commands.mailGetMessages){
            this.mailGetMessages(json_data_object);
            return;
        }
        //29 mailGetMessage
        if(commandId == this.commands.mailGetMessage){
            this.mailGetMessage(json_data_object);
            return;
        }
        //30 mailGetSettings
        if(commandId == this.commands.mailGetSettings){
            this.mailGetSettings(json_data_object);
            return;
        }
        //31 mailSaveSettings
        if(commandId == this.commands.mailSaveSettings){
            this.mailSaveSettings(json_data_object);
            return;
        }
        //32 mailGetReceivers
        if(commandId == this.commands.mailGetReceivers){
            this.mailGetReceivers(json_data_object);
            return;
        }
        //33 mailGetThemes
        if(commandId == this.commands.mailGetThemes){
            this.mailGetThemes(json_data_object);
            return;
        }
        //34 mailGetPhrases
        if(commandId == this.commands.mailGetPhrases){
            this.mailGetPhrases(json_data_object);
            return;
        }
        //35 mailMessageDelete
        if(commandId == this.commands.mailMessageDelete){
            this.mailMessageDelete(json_data_object);
            return;
        }
        //36 mailGetMessageFull
        if(commandId == this.commands.mailGetMessageFull){
            this.mailGetMessageFull(json_data_object);
            return;
        }
        //37 mailMarkRead
        if(commandId == this.commands.mailMarkRead){
            this.mailMarkRead(json_data_object);
            return;
        }
        //38 mailSendMessage
        if(commandId == this.commands.mailSendMessage){
            this.mailSendMessage(json_data_object);
            return;
        }
        //39 mailMessageTransfer
        if(commandId == this.commands.mailMessageTransfer){
            this.mailMessageTransfer(json_data_object);
            return;
        }
        //40 mailSaveDraft
        if(commandId == this.commands.mailSaveDraft){
            this.mailSaveDraft(json_data_object);
            return;
        }
        //41 mailMessageReply
        if(commandId == this.commands.mailMessageReply){
            this.mailMessageReply(json_data_object);
            return;
        }
        //42 mailMessageReplyAll
        if(commandId == this.commands.mailMessageReplyAll){
            this.mailMessageReplyAll(json_data_object);
            return;
        }
        //43 mailMessageToPlan
        if(commandId == this.commands.mailMessageToPlan){
            this.mailMessageToPlan(json_data_object);
            return;
        }
        //44 mailMessageForward
        if(commandId == this.commands.mailMessageForward){
            this.mailMessageForward(json_data_object);
            return;
        }
        //45 mailSendDraftLetter
        if(commandId == this.commands.mailSendDraftLetter){
            this.mailSendDraftLetter(json_data_object);
            return;
        }
        //46 mailAddTask
        if(commandId == this.commands.mailAddTask){
            this.mailAddTask(json_data_object);
            return;
        }
        //47 documentsGetList
        if(commandId == this.commands.documentsGetList){
            this.documentsGetList(json_data_object);
            return;
        }
        //48 mailGetDocumentsList
        if(commandId == this.commands.mailGetDocumentsList){
            this.mailGetDocumentsList(json_data_object);
            return;
        }
        //49 mailSaveDocument
        if(commandId == this.commands.mailSaveDocument){
            this.mailSaveDocument(json_data_object);
            return;
        }
        //50 viewerGet
        if(commandId == this.commands.viewerGet){
            this.viewerGet(json_data_object);
            return;
        }

        //51 phoneGetContacts
        if(commandId == this.commands.phoneGetContacts){
            this.phoneGetContacts(json_data_object);
            return;
        }

        //52 phoneGetHistory
        if(commandId == this.commands.phoneGetHistory){
            this.phoneGetHistory(json_data_object);
            return;
        }
        //53 phoneCallTo
        if(commandId == this.commands.phoneCallTo){
            this.phoneCallTo(json_data_object);
            return;
        }
        //54 timerSetNewTime
        if(commandId == this.commands.timerSetNewTime){
            this.timerSetNewTime(json_data_object);
            return;
        }
        //55 phoneGetSelect
        if(commandId == this.commands.phoneGetSelect){
            this.phoneGetSelect(json_data_object);
            return;
        }
        //56 phoneGetThemes
        if(commandId == this.commands.phoneGetThemes){
            this.phoneGetThemes(json_data_object);
            return;
        }
        //57 phoneCallIgnore
        if(commandId == this.commands.phoneCallIgnore){
            this.phoneCallIgnore(json_data_object);
            return;
        }
        //58 mailGetInboxUnreadedCount
        if(commandId == this.commands.mailGetInboxUnreadedCount){
            this.mailGetInboxUnreadedCount(json_data_object);
            return;
        }
        //59 dayPlanTodoGetCount
        if(commandId == this.commands.dayPlanTodoGetCount){
            this.dayPlanTodoGetCount(json_data_object);
            return;
        }
        //60 excelPointsDraw
        if(commandId == this.commands.excelPointsDraw){
            this.excelPointsDraw(json_data_object);
            return;
        }
        //61 excelPointsReload
        if(commandId == this.commands.excelPointsReload){
            this.excelPointsReload(json_data_object);
            return;
        }

//админка *****************************************************

        //100 dialogsSelectsRequest
        if(commandId == this.commands.dialogsSelectsRequest){
            this.dialogsSelectsRequest(json_data_object);
            return;
        }
        //101 dialogsSelectsRequest
        if(commandId == this.commands.dialogsGetPointsRequest){
            this.dialogsGetPointsRequest(json_data_object);
            return;
        }
        //102 saveCharactersPoints
        if(commandId == this.commands.saveCharactersPoints){
            this.saveCharactersPoints(json_data_object);
            return;
        }
        //103 eventsSamplesSelectsRequest
        if(commandId == this.commands.eventsSamplesSelectsRequest){
            this.eventsSamplesSelectsRequest(json_data_object);
            return;
        }
        //104 eventsChoicesSelectsRequest
        if(commandId == this.commands.eventsChoicesSelectsRequest){
            this.eventsChoicesSelectsRequest(json_data_object);
            return;
        }

        //105 charactersPointsTitlesSelectsRequest
        if(commandId == this.commands.charactersPointsTitlesSelectsRequest){
            this.charactersPointsTitlesSelectsRequest(json_data_object);
            return;
        }
    },
    checkSession: function (data) {

        if(data['result']==1){
            session.setSid(data['sid']);
            world.drawWorld(data['simulations']);
        }
    },
    playerLogin: function(data)
    {
        if(data['result']==1){
            session.setSid(data['sid']);
            // cookies to save logined user {
            $.cookie('user-email', data['user-email'], { expires: 1 });
            // cookies }
            world.drawWorld(data['simulations']);
        }else{
            var message = data['message'];
            var lang_alert_title = 'Авторизация';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }
    },
    registerAction: function (data){
        if(data['result']==1){
            var message = 'Поздравляем, вы успешно зарегистрировались под email-ом <b>'+data['email']+'</b>';
            var lang_alert_title = 'Регистрация';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-success');
            world.drawDefault();
        }else{
            var message = data['message'];
            var lang_alert_title = 'Регистрация';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }
    },
    lostpass: function(data)
    {
        if(data['result']==1){
            var message = 'Данные по восстановлению пароля были отправлены вам на почту';
            var lang_alert_title = 'Восстановление пароля';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-success');
        }else{
            var message = data['message'];
            var lang_alert_title = 'Восстановление пароля';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }
    },
    userAccountChangeEmail: function (data){
        if(data['result']==1){
            var message = 'Ваша почта была изменена';
            var lang_alert_title = 'Личный кабинет';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-success');
        }else{
            var message = data['message'];
            var lang_alert_title = 'Личный кабинет';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }
    },
    userAccountChangePassword: function (data){
        if(data['result']==1){
            var message = 'Ваш пароль был изменен';
            var lang_alert_title = 'Личный кабинет';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-success');
        }else{
            var message = data['message'];
            var lang_alert_title = 'Личный кабинет';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }
    },
    simulationStart: function(data)
    {
        if(data['result']==1){
            timer.setSpeedFactor(data['speedFactor']);
            simulation.drawInterface();
        }
    },
    simulationGetNewEvents: function(data)
    {
        if(data['result']==1){
            simulation.parseNewEvents(data,'new');
            simulation.getNewEvents(true);
        }
        //принудительная синхронизация времени сервера с фронтом, комментирую во избежание прыжков времени
        /*
         if(parseFloat(data['serverTime'])>0){
         var unixtimeMins = parseFloat(data['serverTime']);
         var clockH = Math.floor(unixtimeMins/60);
         var clockM = unixtimeMins-(clockH*60);
         timer.setTimeTo(clockH, clockM);
         }*/
    },
    dialogsGetSelect: function(data)
    {
        if(data['result']==1){
            /*dialogController.draw(data['data']);*/
            simulation.parseNewEvents(data);
        }
        /*if(typeof(data['data'])=='undefined' || php.count(data['data'])==0){
         dialogController.draw('close');
         }*/
    },
    addTriggerGetList: function(data)
    {
        if(data['result']==1){
            addTrigger.drawInterface(data['data']);
        }
    },
    addTriggerAdd: function(data)
    {
        if(data['result']==1){
            var message = 'Событие было успешно добавлено';
            var lang_alert_title = 'Триггер';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-success');
        }else{
            var message = data['message'];
            var lang_alert_title = 'Триггер';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }
    },
    addAssessmentGetList: function(data)
    {
        if(data['result']==1){
            addAssessment.drawInterface(data);
        }
    },
    dayPlanTodoGet: function(data)
    {
        if(data['result']==1){
            dayPlan.receiveTodo(data['data']);
        }
    },
    dayPlanDayPlanGet: function(data)
    {
        if(data['result']==1){
            dayPlan.receiveDayPlan(data['data']);
        }
    },
    dayPlanTodoAdd: function(data)
    {
        // no code?
    },
    dayPlanDayPlanAdd: function(data)
    {
        // no code?
    },
    excelGet: function(data)
    {
        if(data['result']==1){
            excel.receive(data);
        }
        if( typeof(data['message']) != 'undefined' ){
            var message = data['message'];
            var lang_alert_title = 'Excel';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
            excel.status = 1;
            excel.allowToClose = true;
            excel.draw();
        }
    },
    excelGetWorksheet: function(data)
    {
        if(data['result']==1){
            excel.receiveWorksheet(data);
        }
        if( typeof(data['message']) != 'undefined' ){
            var message = data['message'];
            var lang_alert_title = 'Excel';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }
    },
    excelSaveDocument: function(data)
    {
        if( typeof(data['message']) != 'undefined' ){
            var message = data['message'];
            var lang_alert_title = 'Excel';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }
    },
    excelSaveEdit: function(data)
    {
        if( typeof(data['message']) != 'undefined' ){
            var message = data['message'];
            var lang_alert_title = 'Excel';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }

        if( typeof(data['worksheetData']) != 'undefined' )
        {
            excel.receiveChanges(data);
        }
    },
    excelApplyPaste: function(data)
    {
        if( typeof(data['message']) != 'undefined' ){
            var message = data['message'];
            var lang_alert_title = 'Excel';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }

        if( typeof(data['worksheetData']) != 'undefined' )
        {
            excel.receiveChanges(data);
        }
    },
    excelApplySumm: function(data)
    {
        if( typeof(data['message']) != 'undefined' ){
            var message = data['message'];
            var lang_alert_title = 'Excel';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }

        if( typeof(data['worksheetData']) != 'undefined' )
        {
            excel.receiveChanges(data);
        }
    },
    excelApplyAverage: function(data)
    {
        if( typeof(data['message']) != 'undefined' ){
            var message = data['message'];
            var lang_alert_title = 'Excel';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }

        if( typeof(data['worksheetData']) != 'undefined' )
        {
            excel.receiveChanges(data);
        }
    },
    excelApplyDrawing: function(data)
    {
        if( typeof(data['message']) != 'undefined' ){
            var message = data['message'];
            var lang_alert_title = 'Excel';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }

        if( typeof(data['worksheetData']) != 'undefined' )
        {
            excel.receiveChanges(data);
        }
    },
    simulationStop: function(data)
    {
        if(data['result']==1){
            //принудительно закрываю симуляцию на этапе отправки
            //simulation.applyStop();
        }
    },
    mailGetFolders: function(data)
    {
        if(data['result']==1){
            mailEmulator.receive(data);
        }
    },
    mailGetMessages: function(data)
    {
        if(data['result']==1){
            mailEmulator.receiveMessages(data);
        }
    },
    mailGetMessage: function(data)
    {
        if(data['result']==1){
            mailEmulator.receiveMessage(data['data']);
        }
    },
    mailGetSettings: function(data)
    {
        if(data['result']==1){
            mailEmulator.receiveSettings(data['data']['messageArriveSound']);
        }
    },
    mailSaveSettings: function(data)
    {
        if(data['result']==1){

        }
    },
    mailGetReceivers: function(data)
    {
        if(data['result']==1){
            mailEmulator.receiveReceivers(data['data']);
        }
    },
    mailGetThemes: function(data)
    {
        // characterThemeId - store characterThemeId for Fwd: letter returned by mail/getThemes
        // based on first recipient and Fwd:subject id

        if(data['result']==1){
            mailEmulator.receiveThemes(data['data'], data['characterThemeId']);
        }
    },
    mailGetPhrases: function(data)
    {
        if(data['result']==1){
            mailEmulator.receivePhrases(data);
        }
    },
    mailMessageDelete: function(data)
    {
        if(data['result']==1){
            mailEmulator.folderUpdate();
            mailEmulator.receiveFolder(data);
        }
    },
    mailGetMessageFull: function(data)
    {
        if(data['result']==1){
            mailEmulator.receiveMessageFull(data['data']);
        }
    },
    mailMarkRead: function(data)
    {
        if(data['result']==1){
            mailEmulator.receiveFolder(data);
            sender.mailGetInboxUnreadedCount();
        }
    },
    mailSendMessage: function(data)
    {
        if(data['result']==1){
            mailEmulator.backAction({'mailId': data['messageId']});
        }
    },
    mailMessageTransfer: function(data)
    {
        if(data['result']==1){
            mailEmulator.receiveFolder(data);
            mailEmulator.backAction({isMailTransfer: true});
            sender.mailGetInboxUnreadedCount();
        }
    },
    mailSaveDraft: function(data)
    {
        if(data['result']==1){

        }
    },
    mailMessageReply: function(data)
    {
        if(data['result']==1){
            mailEmulator.messageDrawReply(data);
            mailEmulator.preventChangeRecipient();
            mailEmulator.letterType = 'reply';
            mailEmulator.drawNewLetterCheckType();
        }
    },
    mailMessageReplyAll: function(data)
    {
        if(data['result']==1){
            mailEmulator.messageDrawReply(data);
            mailEmulator.preventChangeRecipient()
            mailEmulator.letterType = 'replyAll';
            mailEmulator.drawNewLetterCheckType();
        }
    },
    mailMessageToPlan: function(data)
    {
        if(data['result']==1){
            mailEmulator.showTasks(data['data']);
        }
    },
    mailMessageForward: function(data)
    {
        if(data['result']==1){
            mailEmulator.messageDrawReply(data);
            mailEmulator.letterType = 'forward';
            mailEmulator.drawNewLetterCheckType();
        }
    },
    mailSendDraftLetter: function(data)
    {
        if(data['result']==1){
            mailEmulator.backAction({'after_send_draft' : true});
        }
    },
    mailAddTask: function(data)
    {
        if(data['result']==1){
            mailEmulator.gotoTask(data['taskId']);
        }
    },
    documentsGetList: function(data)
    {
        if(data['result']==1){
            documents.receive(data['data']);
        }
    },
    mailGetDocumentsList: function(data)
    {
        if(data['result']==1){
            mailEmulator.drawAttachForm(data['data']);
        }
    },
    mailSaveDocument: function(data)
    {
        if(data['result']==1){
            var message = 'Вложенный файл был успешно сохранен';
            var lang_alert_title = 'e-mail';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-success');
        }else{
            var message = 'Не могу сохранить файл - системная ошибка';
            var lang_alert_title = 'e-mail';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }
    },
    viewerGet: function(data)
    {
        if(data['result']==1){
            viewer.receive(data);
        }
    },

    phoneGetContacts: function(data)
    {
        if(data['result']==1){
            phone.receiveContacts(data['data']);
        }
    },

    phoneGetHistory: function(data)
    {
        if(data['result']==1){
            phone.receiveHistory(data['data']);
        }
    },
    phoneCallTo: function(data)
    {
        if(data['result']==1){
            simulation.parseNewEvents(data);
        }
        /*if(data['result']==1 && php.count(data['data'])>0){
         simulation.parseNewEvents(data);
         }else{
         simulation.drawDefaultLocation();
         }*/
    },
    timerSetNewTime: function(data)
    {
        if(data['result']==1){
            addTrigger.applyNewTime();
        }
    },
    phoneGetSelect: function(data)
    {
        if(data['result']==1){
            simulation.parseNewEvents(data);
        }
    },
    phoneGetThemes: function(data)
    {
        if(data['result']==1){
            phone.receiveThemes(data['data']);
        }
    },
    phoneCallIgnore: function(data)
    {
        if(data['result']==1){
            //none
        }
    },
    mailGetInboxUnreadedCount: function(data)
    {
        if(data['result']==1){
            var counter = data['unreaded'];
            if(counter == 0){
                icons.removeIconCounter('email');
            }else{
                icons.setIconCounter('email', counter);
            }
        }else{
            icons.removeIconCounter('email');
        }
    },
    dayPlanTodoGetCount: function(data)
    {
        if(data['result']==1){
            var counter = data['data'];
            if(counter == 0){
                icons.removeIconCounter('todo');
            }else{
                icons.setIconCounter('todo', counter);
            }
        }
    },
    excelPointsDraw: function(data)
    {
        if(data['result']==1){
            addAssessment.drawExcelPoints(data['data']);
        }
    },
    excelPointsReload: function(data)
    {
        if(data['result']==1){
            var message = 'Пересчет прошел успешно';
            var lang_alert_title = 'e-mail';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-success');
        }else{
            var message = 'Пересчет не прошел успешно';
            var lang_alert_title = 'e-mail';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }
    },

//админка *****************************************************
    dialogsSelectsRequest: function(data)
    {
        if(data['result']==1){
            dialogs.drawGrid(data['data']);
        }
    },
    dialogsGetPointsRequest: function(data)
    {
        if(data['result']==1){
            dialogs.drawCharactersPoints(data['data']);
        }
    },
    saveCharactersPoints: function(data)
    {
        if(data['result']==1){
            var message = 'Влияния на характеристики были успешно сохранены';
            var lang_alert_title = 'Диалоги';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed);

            dialogs.hideCharactersPoints();
        }
    },
    eventsSamplesSelectsRequest: function(data)
    {
        if(data['result']==1){
            eventsSamples.drawGrid(data['data']);
        }
    },
    eventsChoicesSelectsRequest: function(data)
    {
        if(data['result']==1){
            eventsChoiсes.drawGrid(data['data']);
        }
    },
    charactersPointsTitlesSelectsRequest: function(data)
    {
        if(data['result']==1){
            charactersPointsTitles.drawGrid(data['data']);
        }
    }
}