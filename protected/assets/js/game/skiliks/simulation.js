simulation= {
    timer: 0,
    varianceToUpdate: (60*1),
    bounds: {},
    divZindex:1,
    displayMode: 'normal',
    
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
        'activated': 'activated',
        'deactivated': 'deactivated'
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
    
    windowsArr       : [],
    documentFilesArr : [],

    subWindowsArr    : [],
    windowActive     : '',
    subwindowActive  : '', // for sim-close action log
    parametersActive : {}, // for sim-close action log
    
    isRecentlyIgnoredPhone: false,
    
    frontEventLog: function(screen, screenSub, action,  params)
    {
        var timeString = timer.getCurTimeFormatted('timeStamp');
        this.frontEventsLog.push([
                this.screens[screen],
                this.screensSub[screenSub],
                this.screensActions[action],
                timeString,
                params
            ]);

    },
    getDisplayMode: function()
    {
        return this.displayMode;
    },
    setDisplayMode: function(mode)
    {
        this.displayMode = mode;
        
        //теперь обновляем режимы окон
        simulation.switchDisplayMode(mode);
        icons.switchDisplayMode(mode);
        timer.switchDisplayMode(mode);
        
    },
    switchDisplayMode: function(mode){
        $('#canvas').css('position',  "absolute");
        $('#canvas').css('top', this.bounds.y+'px');
        $('#canvas').css('left',  (this.bounds.x-8)+'px');
        
        if(mode == 'normal'){
            $('#canvas').css('z-index',  this.divZindex);
        }else if(mode == 'dialog'){
            var zIndex = dialogController.getZindex();
            $('#canvas').css('z-index',  (zIndex-3));
        }
    },
    start: function(stype)
    {
        //логируем событие
        this.window_set = new SKWindowSet();
        this.window = new SKWindow('mainScreen', 'mainScreen');
        var startTime = timer.getSimulationStartTime();
        window.timer.setSaveExcelTime(startTime.hours, startTime.minutes);
        simulation.window.open();
        session.setStype(stype);
        sender.simulationStart(stype);
    },
    stop: function()
    {
        // если открыт mailEmulator - его надо закрыть
        if(mailEmulator.status == 1){
            mailEmulator.activeSubScreen='';
            mailEmulator.curMesage = 0;
        }
        
        // если открыт FileViewer - его надо закрыть
        if(viewer.status == 1){
            viewer.fileId = 0;
        }
        
        // если открыт Excel - его надо закрыть
        if(excel.status == 1){
            excel.fileId = 0;
        }
     
        var timeString = timer.getCurTimeFormatted('timeStamp');
        this.window_set.closeAll();
        simulation.window.close();
        this.applyStop();
        sender.simulationStop(this.frontEventsLog, 0, timeString); //Пакет - это массив логов
        delete this.window_set;
        //и чистим логи сразу
        this.frontEventsLog = [];
        // and activeWindows array
        this.windowsArr = [];
    },
    applyStop: function()
    {
        //стоп движка
        stopGame();
        
        timer.timer = 0;
        drawGame.drawController = function(){};
        world.drawWorld();
        
        //обновляем статусы доп окон
        
        //Диалоги
        dialogController.issetDiv = false;
        
        //events
        events.issetDiv = false;
        
        //clock
        timer.issetDiv = false;
        
        //icons
        icons.issetDiv = false;
        
        //addTrigger
        addTrigger.issetDiv = false;
        
        //addAssessment
        addAssessment.issetDiv = false;
        
        //addAnimation
        addAnimation.issetDiv = false;
        
        //addDocuments
        addDocuments.issetDiv = false;
        
        //dayPlan
        dayPlan.issetDiv = false;
        
        //excel
        excel.issetDiv = false;
        
        //email
        mailEmulator.issetDiv = false;
        
        //documents
        documents.issetDiv = false;
        
        //viewer
        viewer.issetDiv = false;
        
        //phone
        phone.issetDiv = false;
        
        //videos
        videos.isset = 0;
        
        //sounds
        sounds.isset = false;
    },
    drawInterface: function()
    {
        if(!session.getSid()){
            return;
        }
        var activeFrame = frame_switcher.setToCanvas();
        var bounds = objects.getBounds(activeFrame);
        this.bounds = bounds;
        
        drawGame.drawController = function(){
            //загружаем картинки
            simulation.updateInterface();
        };
        
        //запуск движка
        runGame();
        
        this.drawDefaultLocation();
        
        
        
        //Диалоги
        /*var dialogDivTop = bounds.y+bounds.height-200;
        dialogController.setDivTop(dialogDivTop);
        dialogController.setDivLeft(bounds.x+150);*/
        dialogController.setDivTop(bounds.y+20);
        dialogController.setDivLeft(bounds.x+20);
        dialogController.setDivCheight(bounds.height);
        
        //events
        var dialogDivTop = bounds.y+bounds.height-200;
        events.setDivTop(dialogDivTop);
        events.setDivLeft(bounds.x+150);
        
        //clock
        timer.setDivTop(bounds.y+10);
        //timer.setDivLeft(bounds.x+bounds.width-60);
        timer.setDivLeft(bounds.x+0);
        timer.draw(false);
        
        //icons
        var dialogDivTop = bounds.y+bounds.height-60;
        icons.setDivTop(bounds.y);
        icons.setDivLeft(bounds.x+bounds.width-160);
        icons.draw();
        
        //addTrigger
        var dialogDivTop = bounds.y+bounds.height;
        addTrigger.setDivTop(dialogDivTop);
        addTrigger.setDivLeft(bounds.x+50);
        addTrigger.draw();
        
        //addAssessment
        var dialogDivTop = bounds.y+bounds.height;
        addAssessment.setDivTop(dialogDivTop+250);
        addAssessment.setDivLeft(bounds.x+50);
        addAssessment.draw();
        
        //addAnimation
        var dialogDivTop = bounds.y+bounds.height;
        addAnimation.setDivTop(bounds.y);
        addAnimation.setDivLeft(bounds.x-100);
        addAnimation.draw();
        
        //addDocuments
        var dialogDivTop = bounds.y+bounds.height;
        addDocuments.setDivTop(bounds.y+30);
        addDocuments.setDivLeft(bounds.x-100);
        addDocuments.draw();
        
        //dayPlan
        dayPlan.setDivTop(bounds.y+35);
        dayPlan.setDivLeft(bounds.x);
        dayPlan.setDivRight(bounds.x+120);
        
        //excel
        excel.setDivTop(bounds.y+30);
        excel.setDivLeft(bounds.x+25);
        
        //email
        mailEmulator.setDivTop(bounds.y+35);
        mailEmulator.setDivLeft(bounds.x+15);
        mailEmulator.setDivRight(bounds.x+120);
        
        //documents
        documents.setDivTop(bounds.y+30);
        documents.setDivLeft(bounds.x+20);
        
        //viewer
        viewer.setDivTop(bounds.y+50);
        viewer.setDivLeft(bounds.x+25);
        
        //phone
        phone.setDivTop(bounds.y+30);
        phone.setDivLeft(bounds.x+300);
        
        phone.setBoundsParams(bounds);
        
        //videos
        videos.setDivTop(bounds.y);
        videos.setDivLeft(bounds.x-8);
        videos.setDivWidth(bounds.width+0);
        videos.setDivHeight(bounds.height+0);
    },
    drawDefaultLocation: function()
    {
        
        this.drawLocation('bgMain');
        //добавляем картинку
        var object = mapObjects['maps']['bgMain'];
    },
    drawLocation: function(name)
    {
        //добавляем картинку
        var object = mapObjects['maps'][name];
        
        drawGame.drawObjects = [];
        object.sx = 0;
        object.sy = 0;
        object.centerX = 1000/2;
        object.cenerY = 600/2;
        object.dWidth = 1000;
        object.dHeight = 600;
        object.angle = 0;
        object.scaleX = 1;
        object.alpha = 1;
        var key = drawGame.drawObjects.length;
        drawGame.drawObjects[key] = object;
    },
    updateInterface: function ()
    {
        this.getNewEvents();
        timer.update();
        icons.update();
        dayPlan.updateTimeLine();
        addAnimation.update();
        phone.update();
        dialogController.update();
    },
    getNewEvents:function(force)
    {
        var foo = new Date; // Generic JS date object
        var unixtime_ms = foo.getTime(); // Returns milliseconds since the epoch
        var unixtime = parseInt(unixtime_ms / (1000/timer.speedFactor) );
        
        var variance = unixtime-this.timer;
        if(force || variance>this.varianceToUpdate){
            
            var windowActiveNum = this.screens[this.windowActive];
            var timeString = timer.getCurTimeFormatted('timeStamp');
            sender.simulationGetNewEvents(this.frontEventsLog, windowActiveNum, timeString);
            //и чистим логи сразу
            this.frontEventsLog = [];
            
            addAssessment.draw();
            
            this.timer = unixtime;
        }
    },
    parseNewEvents: function(data, flag)
    {
        if(typeof(flag)=="undefined"){
            flag = 'regular';
        }
            
        var issetDialog = 0;
        for (var key in data['events'])
        {
            var event = data['events'][key];
            this.parseNewEvent(event);
            
            if(event['eventType']=='1' && typeof(event['data'])!='undefined' && php.count(event['data'])!=0){
                var newEvent = event['data'];
                if(newEvent[0]['dialog_subtype']!=1 && newEvent[0]['dialog_subtype']!=5){
                    //мы считаем что диалог есть, если это не звонок телефона, и не попытка визита
                    issetDialog=1;
                }
            }
        }
        
        if(issetDialog == 0 && flag == 'regular'){
            // fix to keep open dialog (phon talk or visit) alive when 
            // Main hero miss phone call and it ignored automatically
            if((1 == dialogController.status || 1 == phone.status) 
                && 'undefined' == typeof data.length 
                && true == simulation.isRecentlyIgnoredPhone) {
                simulation.isRecentlyIgnoredPhone = false;
            } else {
                dialogController.draw('close');  
                phone.draw('close');
            }            
        }
    },
    parseNewEvent: function(data)
    {
        if(data['eventType']=='1'){
            // диалог  звонок/встреча
            var newEvent = data['data'];
            if(php.count(newEvent)>0){
                icons.addNewEvent(newEvent);
            }
        }else if(data['eventType']=='2'){
            //пришло время выполнить задачу
            var newEvent = data['data'];
            if(php.count(newEvent)>0){
                events.draw(newEvent);
            }
//custom
        }else if(data['eventType']=='D'){
            icons.addCustomEvent('documents', 'D', data['id']);
            /*documents.fileToSelect = data['id'];
            documents.draw();*/
        }else if(data['eventType']=='MS'){
            icons.addCustomEvent('email', 'MS', 0);
            //обновляем по мылу счетчик
        sender.mailGetInboxUnreadedCount();
            /*
            mailEmulator.status = 0;
            mailEmulator.draw();
            mailEmulator.drawNewLetter();
            */
        }else if(data['eventType']=='M'){
            icons.addCustomEvent('email', 'M', data['id']);
            //обновляем по мылу счетчик
        sender.mailGetInboxUnreadedCount();
            /*
            mailEmulator.status = 0;
            mailEmulator.draw();
            mailEmulator.curMesageToSelect = data['id'];*/
        }else if(data['eventType']=='P'){
            icons.addCustomEvent('todo', 'P', data['id']);
            //обновляем по todo счетчик
        sender.dayPlanTodoGetCount();
            /*
            dayPlan.draw();
            dayPlan.taskdayPlanToSelect = data['id'];*/
        }else{
//custom
            var newEvent = data['data'];
            if(php.count(newEvent)>0){
                icons.addNewEvent(newEvent);
            }
        }
        
    }
};