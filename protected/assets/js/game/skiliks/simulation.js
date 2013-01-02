simulation = {
    timer:0,
    varianceToUpdate:(60 * 1),
    bounds:{},
    divZindex:1,
    displayMode:'normal',

    screens:{
        'mainScreen':1,
        'plan':3,
        'mailEmulator':10,
        'phone':20,
        'visitor':30,
        'documents':40
    },
    screensSub:{
        'mainScreen':1,
        'plan':3,
        'mailMain':11,
        'mailPreview':12,
        'mailNew':13,
        'mailPlan':14,
        'phoneMain':21,
        'phoneTalk':23,
        'phoneCall':24,
        'visitorEntrance':31,
        'visitorTalk':32,
        'documents':41,
        'documentsFiles':42
    },
    screensActions:{
        'close':0,
        'open':1,
        'custom':2,
        'activated':'activated',
        'deactivated':'deactivated'
    },
    frontEventsLog:[],

    // it stores subscreen NAME for previosly opened screens
    // system can display only one subscreen for each scree, so I use screen name us array index 
    // used to find what sudscreen will be activated/deactivated
    subscreenTypesLog:[],

    // it stores subscreen PARAMETERS for previosly opened screens
    // system can display only one subscreen for each scree, so I use sereen name us array index
    // used to find parameters for sudscreen that must be activated/deactivated
    subscreenParametersLog:[],

    windowsArr:[],
    documentFilesArr:[],

    subWindowsArr:[],
    windowActive:'',
    subwindowActive:'', // for sim-close action log
    parametersActive:{}, // for sim-close action log

    isRecentlyIgnoredPhone:false,

    frontEventLog:function (screen, screenSub, action, params) {
        var timeString = SKApp.user.simulation.getGameMinutes();
        this.frontEventsLog.push([
            this.screens[screen],
            this.screensSub[screenSub],
            this.screensActions[action],
            timeString,
            params
        ]);

    },
    //ГВОНО!
    getNewEvents:function (force) {
        var foo = new Date; // Generic JS date object
        var unixtime_ms = foo.getTime(); // Returns milliseconds since the epoch
        var unixtime = parseInt(unixtime_ms / (1000 / timer.speedFactor));

        var variance = unixtime - this.timer;
        if (force || variance > this.varianceToUpdate) {

            var windowActiveNum = this.screens[this.windowActive];
            var timeString = timer.getCurTimeFormatted('timeStamp');
            sender.simulationGetNewEvents(this.frontEventsLog, windowActiveNum, timeString);
            //и чистим логи сразу
            this.frontEventsLog = [];

            addAssessment.draw();

            this.timer = unixtime;
        }
    },
    parseNewEvents:function (data, flag) {
        if (typeof(flag) == "undefined") {
            flag = 'regular';
        }

        var issetDialog = 0;
        for (var key in data['events']) {
            var event = data['events'][key];
            this.parseNewEvent(event);

            if (event['eventType'] == '1' && typeof(event['data']) != 'undefined' && php.count(event['data']) != 0) {
                var newEvent = event['data'];
                if (newEvent[0]['dialog_subtype'] != 1 && newEvent[0]['dialog_subtype'] != 5) {
                    //мы считаем что диалог есть, если это не звонок телефона, и не попытка визита
                    issetDialog = 1;
                }
            }
        }

        if (issetDialog == 0 && flag == 'regular') {
            // fix to keep open dialog (phon talk or visit) alive when 
            // Main hero miss phone call and it ignored automatically
            if ((1 == dialogController.status || 1 == phone.status)
                && 'undefined' == typeof data.length
                && true == simulation.isRecentlyIgnoredPhone) {
                simulation.isRecentlyIgnoredPhone = false;
            } else {
                dialogController.draw('close');
                phone.draw('close');
            }
        }
    },
    parseNewEvent:function (data) {
        if (data['eventType'] == '1') {
            // диалог  звонок/встреча
            var newEvent = data['data'];
            if (php.count(newEvent) > 0) {
                icons.addNewEvent(newEvent);
            }
        } else if (data['eventType'] == '2') {
            //пришло время выполнить задачу
            var newEvent = data['data'];
            if (php.count(newEvent) > 0) {
                events.draw(newEvent);
            }
//custom
        } else if (data['eventType'] == 'D') {
            icons.addCustomEvent('documents', 'D', data['id']);
            /*documents.fileToSelect = data['id'];
             documents.draw();*/
        } else if (data['eventType'] == 'MS') {
            icons.addCustomEvent('email', 'MS', 0);
            //обновляем по мылу счетчик
            sender.mailGetInboxUnreadedCount();
            /*
             mailEmulator.status = 0;
             mailEmulator.draw();
             mailEmulator.drawNewLetter();
             */
        } else if (data['eventType'] == 'M') {
            icons.addCustomEvent('email', 'M', data['id']);
            //обновляем по мылу счетчик
            sender.mailGetInboxUnreadedCount();
            /*
             mailEmulator.status = 0;
             mailEmulator.draw();
             mailEmulator.curMesageToSelect = data['id'];*/
        } else if (data['eventType'] == 'P') {
            icons.addCustomEvent('todo', 'P', data['id']);
            //обновляем по todo счетчик
            sender.dayPlanTodoGetCount();
            /*
             dayPlan.draw();
             dayPlan.taskdayPlanToSelect = data['id'];*/
        } else {
//custom
            var newEvent = data['data'];
            if (php.count(newEvent) > 0) {
                icons.addNewEvent(newEvent);
            }
        }

    }
};