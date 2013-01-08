dayPlan = {
    receivedData:false,
    timer: 0,
    varianceToUpdate: (60*1),
    status:0,
    issetDiv: false,
    issetDivToDo: false,
    issetDivTasks: false,
    divTop: 50,
    divLeft: 50,
    divRight:50,
    heightTaskSlot: 22,
    widthTask: 200,
    tasksdayPlan:{},
    tasksdayPlanNotMoveable:{},
    taskdayPlanSelected: false,
    taskdayPlanToSelect: false,
    tasksToDo:{},
    tasksToDoState:'middle',
    
    divZindex:0,

    setDivTop: function(val)
    {
        this.divTop = val;
    },
    setDivLeft: function(val)
    {
        this.divLeft = val;
    },
    setDivRight: function(val)
    {
        this.divRight = val;
    },
    createDiv: function()
    {
        var topZindex = php.getTopZindexOf();
        this.divZindex = topZindex;
            
        var div = document.createElement('div');
          div.setAttribute('id', 'dayPlanMainDiv');
          div.setAttribute('class', 'dayPlanMainDiv');
          div.style.position = "absolute";
          div.style.zIndex = this.divZindex;
          document.body.appendChild(div);
          $('#dayPlanMainDiv').css('top', this.divTop+'px');
          $('#dayPlanMainDiv').css('left',  this.divLeft+'px');
          $('#dayPlanMainDiv').css('right',  this.divRight+'px');
          
          //close
          var div = document.createElement('div');
          div.setAttribute('id', 'dayPlanMainDivClose');
          div.setAttribute('class', 'dayPlanMainDivClose');
          div.style.position = "absolute";
          div.style.zIndex = this.divZindex+1;
          document.body.appendChild(div);
          $('#dayPlanMainDivClose').css('top', (this.divTop-15)+'px');
          $('#dayPlanMainDivClose').css('right',  (this.divRight+45)+'px');
          //$('#dayPlanMainDivClose').html(this.closeHtml);
          
          //timeline
          var div = document.createElement('div');
          div.setAttribute('id', 'dayPlanMainDivTimeline');
          div.setAttribute('class', 'day-plan-main-div-timeline');
          div.style.position = "absolute";
          div.style.zIndex = this.divZindex+1;
          document.body.appendChild(div);
          $('#dayPlanMainDivTimeline').css('top', this.divTop+'px');
          $('#dayPlanMainDivTimeline').css('left',  (this.divLeft+73)+'px');
          $('#dayPlanMainDivTimeline').html(this.timelineHtml);
          $('#dayPlanMainDivTimeline').hide();
          
          this.issetDiv = true;
    },
    createDivToDo: function()
    {
          var bounds = objects.getBounds(document.getElementById('dayPlanMainDiv'));
          var boundsAV = objects.getBounds(document.getElementById('plannerBookAfterVacation'));
          var div = document.createElement('div');
          div.setAttribute('id', 'dayPlanToDoDiv');
          div.setAttribute('class', 'dayPlanToDoDiv');
          div.style.position = "absolute";
          div.style.zIndex = this.divZindex+5;
          document.body.appendChild(div);
          $('#dayPlanToDoDiv').css('top', (bounds.y+bounds.height-300)+'px');
          $('#dayPlanToDoDiv').css('left', (bounds.x+571)+'px');
          
          //делаем туду драгабл)
          var y1 = (bounds.y+bounds.height-215);
          var y2 = (bounds.y+bounds.height-215);
          var x1 = bounds.x;
          var x2 = bounds.x+bounds.width-305;
        $("#dayPlanToDoDiv").draggable({ 
            handle: "p",
            axis: "x", 
            containment: [x1, y1, x2, y2] 
        });
          
          this.issetDivToDo = true;
    },
    createDivTasks: function()
    {
          var bounds = objects.getBounds(document.getElementById('dayPlanMainDiv'));
          var div = document.createElement('div');
          div.setAttribute('id', 'dayPlanTasksMainDiv');
          div.setAttribute('class', 'dayPlanTasksMainDiv');
          div.style.position = "absolute";
          div.style.zIndex = this.divZindex+1;
          document.body.appendChild(div);
          $('#dayPlanTasksMainDiv').css('top', (bounds.y+51)+'px');
          $('#dayPlanTasksMainDiv').css('left', (bounds.x+100)+'px');
          
          this.issetDivTasks = true;
    },
    draw: function()
    {
        if(this.status == 0){
            sender.dayPlanTodoGet();
            sender.dayPlanDayPlanGet();
            this.status = 1;
            //логируем событие
            this.plan_window = new SKWindow('plan', 'plan');
            this.plan_window.open();
        }else{
            
                //update counter
            var counter = php.count(this.tasksToDo);
            if(counter == 0){
                icons.removeIconCounter('todo');
            }else{
                icons.setIconCounter('todo', counter);
            }
        
        
            // закрываем окна
            $('#dayPlanMainDiv').remove();
            $('#dayPlanMainDivClose').remove();
            $('#dayPlanToDoDiv').remove();
            $('#dayPlanTasksMainDiv').remove();
            $('#dayPlanMainDivTimeline').remove();
            
            //scrolls
            $('#plannerBookTomorrowScrollbar').remove();
            $('#plannerBookTodayScrollbar').remove();
            
            this.issetDiv= false;
            this.issetDivToDo= false;
            this.issetDivTasks= false;
            this.tasksToDoState = 'middle';
            dayPlan.taskdayPlanSelected = false;
            this.timer = 0;
            
            this.status = 0;
            
            //логируем событие
            this.plan_window.close();
        }
    },
    updateTimeLine: function(execFlag){
        if(typeof(execFlag)=='undefined'){execFlag=0;}
        if(!this.issetDiv){return;}
        
        var foo = new Date; // Generic JS date object
        var unixtime_ms = foo.getTime(); // Returns milliseconds since the epoch
        var unixtime = parseInt(unixtime_ms / (1000/SKConfig.skiliksSpeedFactor) );
        
        var variance = unixtime-this.timer;
        if(execFlag==0 && variance<this.varianceToUpdate){
            return;
        }
        this.timer = unixtime;
        
        var curTime = SKApp.user.simulation.getGameTime();
        //var curTimeString = curTime.charAt(0)+curTime.charAt(1)+curTime.charAt(3)+curTime.charAt(4);
        var curTdId = 't1_'+curTime.charAt(0)+curTime.charAt(1)+'00';
        var curMins = parseFloat(curTime.charAt(3)+curTime.charAt(4));
        var k = (curMins/60);
        
        var offset = $('#'+curTdId).offset();
        var bounds = objects.getBounds(document.getElementById(curTdId));
        var Ctop = offset.top+Math.ceil(2*k*bounds.height)-6;
        
        //а не вылазим ли мы ? 
        CMainDiv = $("#dayPlanTasksMainDiv").offset();
        var CtopDiff = Ctop-CMainDiv.top;
        if( CtopDiff<25 ){
            $('#dayPlanMainDivTimeline').hide();
        }else{
            $('#dayPlanMainDivTimeline').show();
        }
        
        $('#dayPlanMainDivTimeline').css('top', (Ctop)+'px');
    },
    receiveTodo: function(data)
    {
        this.tasksToDo = {};
        
        for (var key in data)
        {
            var value = data[key];
            var Cid = php.LdgZero(value['id'], 5);
            var Cdur = Math.ceil(value['duration']/30);
            
            var newKey = 'task_'+Cid+'_'+Cdur+'_0_0000';
            var newValue = value['title'];
            
            this.tasksToDo[newKey] = newValue;
        }
        
        //update counter
        var counter = php.count(this.tasksToDo);
        if(counter == 0){
            icons.removeIconCounter('todo');
        }else{
            icons.setIconCounter('todo', counter);
        }
        
        this.checkDrawInterface();
    },
    receiveDayPlan: function(data)
    {
        this.tasksdayPlan = {};
        
        for (var key in data)
        {
            var value = data[key];
            var Cid = php.LdgZero(value['task_id'], 5);
            var Cdur = Math.ceil(value['duration']/30);
            var Cday = value['day'];
            var Cdate = php.str_replace(':', '', value['date']);
            
            var newKey = 'task_'+Cid+'_'+Cdur+'_'+Cday+'_'+Cdate;
            var newValue = value['title'];
            
            this.tasksdayPlan[newKey] = newValue;
            
            //а вдруг неперемещаемая хрень?)
            if(value['type']==2){
                this.tasksdayPlanNotMoveable[newKey] = newValue;
            }
        }
        
        this.checkDrawInterface();
    },
    checkDrawInterface: function()
    {
        if(!this.receivedData){
            this.receivedData = true;
        }else{
            this.receivedData = false;
            this.drawInterface();
        }
    },
    selectById:function()
    {
        if(!this.taskdayPlanToSelect){
            return;
        }
        //'task_00020_1_1_0930':'Потупить'
        
        var id = this.taskdayPlanToSelect;
        this.taskdayPlanToSelect = false;

        var Cid = false;
        //находим нужный ид
        for (var key in this.tasksdayPlan)
        {
            var value = this.tasksdayPlan[key];
            var keyArr = key.split('_');
            if(parseFloat(keyArr[1])==id){
                Cid = key;
            }
        }
        
        if(!Cid){
            for (var key in this.tasksToDo)
            {
                var value = this.tasksdayPlan[key];
                var keyArr = key.split('_');
                if(parseFloat(keyArr[1])==id){
                    Cid = key;
                }
            }
        }

        if(Cid){
            $('.day-plan-task-active').removeClass('day-plan-task-active');
            dayPlan.taskdayPlanSelected = Cid;
            $('#'+Cid).addClass('day-plan-task-active');
        }
    },
    drawInterface: function()
    {
        if(!this.issetDiv){
            this.createDiv();
        }

        var html = this.html;
        
        var planHTML = this.planHTML;
        
        html = php.str_replace('{html}', planHTML, html);
        $('#dayPlanMainDiv').html(html);
        
        this.drawInterfaceScripts();
    },
    drawInterfaceScripts: function()
    {
        //todo
        this.drawTasks();
        this.drawToDoList();
        
        //content
        this.drawTasksContent();
        
        //select
        this.selectById();
        
        //timeline
        this.updateTimeLine();
        
        //scrolls
        this.doScrollable();
    },
    drawToDoList: function()
    {
        if(!this.issetDivToDo){
            this.createDivToDo();
        }

        var todoHtml = this.todoHtml;
        
        //html = php.str_replace('{html}', planHTML, html);
        var upK = 210;
        var downK = 250;
        Ctop = parseFloat($('#dayPlanToDoDiv').css('top'));
        
        
        if(this.tasksToDoState == 'up'){
            $('#dayPlanToDoDiv').css('top', (Ctop-upK)+'px');
            $('#dayPlanToDoDiv').html(this.todoHtmlMax);
        }else if(this.tasksToDoState == 'down'){
            $('#dayPlanToDoDiv').css('top', (Ctop+downK)+'px');
            $('#dayPlanToDoDiv').html(this.todoHtmlMin);
        }else{
            $('#dayPlanToDoDiv').html(this.todoHtml);
        }
    },
    drawTasks: function()
    {
        if(!this.issetDivTasks){
            this.createDivTasks();
        }
        
        var tasksHtml = this.tasksHtml;
        
        $('#dayPlanTasksMainDiv').html(tasksHtml);
    },

    formattingTaskTextLen:function(Cname, duration, addClass){
        if(typeof(duration) == 'undefined'){duration=1;}
        if(typeof(addClass) == 'undefined'){addClass='';}
        
        var heightDiv = (1+dayPlan.heightTaskSlot)*duration-2;
        
        var div = document.createElement('div');
        div.setAttribute('class', 'dayPlanTestLenDiv');
        div.style.position = "absolute";
        div.style.zIndex = this.divZindex;
        document.body.appendChild(div);
        $('.dayPlanTestLenDiv').css('top', 20+'px');
        $('.dayPlanTestLenDiv').css('left',  20+'px');
        $('.dayPlanTestLenDiv').html('loading...');
        
        var CnameArr = Cname.split(' ');
          
        var CnameTemp = '';
        var CnameTempLast = '';
        
        // проверяем по словам, сколько у нас влазит
        for (var key in CnameArr)
        {
            var value = CnameArr[key];
            CnameTemp += ' '+value;
            var textTemp = '<div class="planner-task regular h2'+addClass+'" style="width:185px; height:'+(heightDiv)+'px; top:11px;left:12px; position:absolute;" id="test">'+
                        '<div><p>'+CnameTemp+'</p></div>'+
                        '</div>'
            $('.dayPlanTestLenDiv').html(textTemp);
            
            var pH =  $('.dayPlanTestLenDiv div div p').height();
            if(pH > heightDiv){
                 
                //теперь проверяем побуквенно все слово
                CnameTemp = CnameTempLast+' ';
                for (var i = 0; i < value.length; i++) {
                    CnameTemp += value.charAt(i);
                    textTemp = '<div class="planner-task regular h2" style="width:185px; height:'+(heightDiv)+'px; top:11px;left:12px; position:absolute;" id="test">'+
                                '<div><p>'+CnameTemp+'...</p></div>'+
                                '</div>'
                    $('.dayPlanTestLenDiv').html(textTemp);

                    var pHl =  $('.dayPlanTestLenDiv div div p').height();
                    if(pHl > heightDiv){
                        $('.dayPlanTestLenDiv').remove();
                        return CnameTempLast+'..';
                    }else{
                        CnameTempLast = CnameTemp;
                    }
                }
            }else{
                CnameTempLast = CnameTemp;
            }
        }
        
        $('.dayPlanTestLenDiv').remove();
        return Cname;
    },
    drawTasksContent: function()
    {
        //заплняем туду лист
        var todoInHtml='';
        for (var key in this.tasksToDo)
        {
            var value = this.tasksToDo[key];
            var todoInHtmlOne=this.todoTaskHtml;
            
            var idParams = key.split('_');
            var CMins = idParams[2]*30;
            
            todoInHtmlOne = php.str_replace('{id}', key, todoInHtmlOne);
            todoInHtmlOne = php.str_replace('{name}', value, todoInHtmlOne);
            todoInHtmlOne = php.str_replace('{time}', CMins, todoInHtmlOne);
            todoInHtml += todoInHtmlOne;
        }
        $('#dayPlanToDoDivScroll').html(todoInHtml);
        $('#dayPlanTodoNum').html('('+php.count(this.tasksToDo)+')');
        //scroll
        var Cheight = php.count(this.tasksToDo) * 100
        dayPlan.addDayPlanScroll(Cheight);
        
        
        //заполняем план дневной
        //'task_00022_4_2_2000':'Заказать видосы'
        var todoInHtml='';
        var namesFull = {};
        for (var key in this.tasksdayPlan)
        {
            var value = this.tasksdayPlan[key];
            var todoInHtmlOne=this.dayPlanTaskHtml;
            var keyArr = key.split('_');
            
            
            //расчет высоты задачи для отображения
            var Cheight = ((dayPlan.heightTaskSlot)*keyArr[2])+ (2*Math.ceil(keyArr[2]/2))-3;
            //(2*Math.ceil(keyArr[2]/2))-3 - погрешность бордеров
            
            //определяем отступы, пример ид ячейки = id="t1_09_1"
            //формируем сперва ид нужной ячейки
            CMainDiv = $("#dayPlanTasksMainDiv").offset();
            var CslotId = 't'+keyArr[3]+'_'+keyArr[4];
            var Cslot = $("#"+CslotId).offset();
            
            //фикс на случай, если случайно дропнулось непонятное значение нам (глюк дроппабл)
            if(typeof(value)=='undefined'){
                delete this.tasksdayPlan[key];
            }else if(typeof($("#"+CslotId).get(0)) != 'undefined')
            {
            
                var Ctop = Cslot.top-CMainDiv.top;
                var Cleft = Cslot.left-CMainDiv.left;
                var Cname = value;
                var addClass = '';
                var addSpan = '';
                var cWidth = 195;
                if(typeof(this.tasksdayPlanNotMoveable[key]) != 'undefined'){
                    addClass = ' locked';
                    addSpan = '<span></span>';
                    cWidth = 183;
                }
                if(keyArr[3]== 3){
                    cWidth = 257;
                }
                
                namesFull[key] = php.str_replace('"', '\'', Cname);
                Cname = dayPlan.formattingTaskTextLen(Cname,keyArr[2], addClass);

                todoInHtmlOne = php.str_replace('{id}', key, todoInHtmlOne);
                todoInHtmlOne = php.str_replace('{name}', Cname+addSpan, todoInHtmlOne);
                todoInHtmlOne = php.str_replace('{height}', Cheight, todoInHtmlOne);
                todoInHtmlOne = php.str_replace('{top}', Ctop, todoInHtmlOne);
                todoInHtmlOne = php.str_replace('{left}', Cleft, todoInHtmlOne);
                todoInHtmlOne = php.str_replace('{addClass}', addClass, todoInHtmlOne);
                todoInHtmlOne = php.str_replace('{width}', cWidth, todoInHtmlOne);
                todoInHtmlOne = php.str_replace('{title}', namesFull[key], todoInHtmlOne);
                todoInHtml += todoInHtmlOne;
            }
        }
        $('#dayPlanTasksDiv').html(todoInHtml);
        
        for (var key in namesFull)
        {
            //bootstrap
            $("#"+key).tooltip();
        }
        
        dayPlan.launchCustomSettings();
        dayPlan.checkDisplayTasks();
    },
    taskTimeCheck: function(newId, noMessageFlag){
        // caution !!!
        // newId is not id! This is data array in string!
        // newId = task_X1_X2_X3_X4
        // X1 - task id
        // X2 - ?
        // X3 - ?
        // X4 - Time im game minutes from 00:00 1st game day
        if(typeof(noMessageFlag)=='undefined'){noMessageFlag=0;}

        var newIdArray = newId.split('_');
        var Cduration = newIdArray[2];
        var Cday = newIdArray[3];
        var CstartTime = parseFloat(newIdArray[4]);
        var CendTime = CstartTime + Cduration*30;
        
        var curTime = SKApp.user.simulation.getGameTime();
        //проверка текущего времени+максимального
        var curTimeString = curTime.charAt(0)+curTime.charAt(1)+curTime.charAt(3)+curTime.charAt(4);
        var curTimeInt = parseFloat(curTimeString);
        if(Cday==1 && CstartTime<=curTimeInt){
            if(noMessageFlag==0){
                CstartTime = php.LdgZero(CstartTime, 4);
                var CstartTimeString = CstartTime.charAt(0)+CstartTime.charAt(1)+':'+CstartTime.charAt(2)+CstartTime.charAt(3);
                curTime = php.str_replace(' ', ':', curTime);
                var message = 'Вы пытаетесь запланировать задачу на '+CstartTimeString+', но уже '+curTime+'.<br>Нельзя ставить задачу раньше текущего времени';
                var lang_alert_title = 'План Дневной';
                var lang_confirmed = 'Ок';
                messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
            }

            return false;
        }
        if(CendTime>2160){
            if(noMessageFlag==0){
                var message = 'Нельзя ставить здачу на это время, это повредит вашему сну';
                var lang_alert_title = 'План Дневной';
                var lang_confirmed = 'Ок';
                messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
            }

            return false;
        }
        
        //бежим по листу тасков плана дневного
        for (var key in this.tasksdayPlan)
        {
            var value = this.tasksdayPlan[key];
            var keyArray = key.split('_');
            if(!this.taskTimeCheckTwo(newIdArray, keyArray)){
                if(noMessageFlag==0){
                    var message = 'Вы не сможете выполнить две задачи одновременно';
                    var lang_alert_title = 'План Дневной';
                    var lang_confirmed = 'Ок';
                    messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
                }
                
                return false;
            }
        }
        return true;
    },
    taskTimeCheckTwo: function(newIdArray, keyArray){
        //а вдрг ид совпадают?
        if( newIdArray[1] == keyArray[1]){return true;}
        var Cduration = newIdArray[2];
        var Cday = newIdArray[3];
        var CstartTime = parseFloat(newIdArray[4]);
        var CaddTime = 100*Math.floor(Cduration*30/60) + Cduration*30 - 60*Math.floor(Cduration*30/60);
        var CendTime = CstartTime + CaddTime;
        
        var Tduration = keyArray[2];
        var Tday = keyArray[3];
        var TstartTime = parseFloat(keyArray[4]);
        var TaddTime = 100*Math.floor(Tduration*30/60) + Tduration*30 - 60*Math.floor(Tduration*30/60);
        var TendTime = TstartTime + TaddTime;
        
        if( Cday != Tday){return true;}
        if( (TstartTime-CendTime)*(TendTime-CstartTime) >=0 ){return true;}
        return false;
    },
    fastSwitchDayplanToTodo: function()
    {
        if(this.issetDiv == false){return;}
        if(dayPlan.taskdayPlanSelected == false){return;}
        this.saveDropDetails(dayPlan.taskdayPlanSelected, 'dayPlanToDoDivScroll');
         
    },
    fastSwitchTodoToDayplan: function(taskId)
    {
        var taskIdArray = taskId.split('_');
        var Cduration = taskIdArray[2];
        taskIdArray[3] = 1;
        var i = 900;
        var j = 0;
        while(i < 2200) {
            var Ctime = php.LdgZero(i, 4);
            taskIdArray[4] = Ctime;
            tempTaskId = taskIdArray.join('_');
            //проверяем
            var chkRes = this.taskTimeCheck(tempTaskId, 1);
            if(chkRes){
                //все хорошо, перемещаем
                this.saveDropDetails(taskId, 't1_'+Ctime);
                return;
            }
            
            //инкрементим время
            if(j==0){
                i = i+30;
                j = 1;
            }else{
                i = i+70;
                j = 0;
            }
            
        }
        
        //task_00012_2_0_0000
        //если все занято - перемещаем на после отпуска
        taskIdArray[3] = 2;
        i = 900;
        j = 0;
        while(i < 2200) {
            var Ctime = php.LdgZero(i, 4);
            taskIdArray[4] = Ctime;
            tempTaskId = taskIdArray.join('_');
            //проверяем
            var chkRes = this.taskTimeCheck(tempTaskId, 1);
            if(chkRes){
                //все хорошо, перемещаем
                this.saveDropDetails(taskId, 't2_'+Ctime);
                return;
            }
            
            //инкрементим время
            if(j==0){
                i = i+30;
                j = 1;
            }else{
                i = i+70;
                j = 0;
            }
            
        }
        
        //task_00012_2_0_0000
        //если все занято - перемещаем на после отпуска
        taskIdArray[3] = 3;
        i = 900;
        j = 0;
        while(i < 2200) {
            var Ctime = php.LdgZero(i, 4);
            taskIdArray[4] = Ctime;
            tempTaskId = taskIdArray.join('_');
            //проверяем
            var chkRes = this.taskTimeCheck(tempTaskId, 1);
            if(chkRes){
                //все хорошо, перемещаем
                this.saveDropDetails(taskId, 't3_'+Ctime);
                return;
            }
            
            //инкрементим время
            if(j==0){
                i = i+30;
                j = 1;
            }else{
                i = i+70;
                j = 0;
            }
            
        }
        
    },
    saveDropDetails: function (dragId, dropId) {
        //перемещаемо ли вообще наше задание?
        if(typeof(this.tasksdayPlanNotMoveable[dragId]) != 'undefined'){
            dayPlan.showSystemMessage();
            return;
        }
        
        //можем ли мы делать расчеты
        var canU = this.canUpdate();
        if(canU==0){return;}
        //имеет ли смысл обновление?
        if ( $("#"+dropId).is(':has(#'+dragId+')') ){
                //нас перетащили самих в себя, ретурн)
                return;
            }
        var dragIdArray = dragId.split('_');
        var dropIdArray = dropId.split('_');
        
        if(php.count(dragIdArray)!=5){
            //не наш формат
            return;
        }
        
        if(dropId == 'dayPlanToDoDivScroll'){
            //в туду лист
            var newId = 'task_'+dragIdArray[1]+'_'+dragIdArray[2]+'_0_0000';
            var item = this.tasksdayPlan[dragId];
            
            delete this.tasksdayPlan[dragId];
            this.tasksToDo[newId] = item;
            //отправляем данные на сервер
            var taskId = parseFloat(dragIdArray[1]);
            sender.dayPlanTodoAdd(taskId);
        }else if(dragIdArray[5]='0000' && dragIdArray[4]==0){
            //из туду листа
            var stackCode = php.str_replace('t', '', dropIdArray[0]);
            var newId = 'task_'+dragIdArray[1]+'_'+dragIdArray[2]+'_'+stackCode+'_'+dropIdArray[1];
            var item = this.tasksToDo[dragId];
            
            // проверяем - можно ли помещать именно туда
            if(this.taskTimeCheck(newId)==0){return false;}
            
            delete this.tasksToDo[dragId];
            this.tasksdayPlan[newId] = item;
            //отправляем данные на сервер
            var taskId = parseFloat(dragIdArray[1]);
            var time = dropIdArray[1].charAt(0)+dropIdArray[1].charAt(1)+':'+dropIdArray[1].charAt(2)+dropIdArray[1].charAt(3);
            var day = stackCode;
            sender.dayPlanDayPlanAdd(taskId, time, day);
        }else{
            //внутри плана дневного
            
            var stackCode = php.str_replace('t', '', dropIdArray[0]);
            var newId = 'task_'+dragIdArray[1]+'_'+dragIdArray[2]+'_'+stackCode+'_'+dropIdArray[1];
            var item = this.tasksdayPlan[dragId];
            
            // проверяем - можно ли помещать именно туда
            if(this.taskTimeCheck(newId)==0){return false;}
            
            delete this.tasksdayPlan[dragId];
            this.tasksdayPlan[newId] = item;
            //отправляем данные на сервер
            var taskId = parseFloat(dragIdArray[1]);
            var time = dropIdArray[1].charAt(0)+dropIdArray[1].charAt(1)+':'+dropIdArray[1].charAt(2)+dropIdArray[1].charAt(3);
            var day = stackCode;
            sender.dayPlanDayPlanAdd(taskId, time, day);
        }
        //update counter
        var counter = php.count(this.tasksToDo);
        if(counter == 0){
            icons.removeIconCounter('todo');
        }else{
            icons.setIconCounter('todo', counter);
        }
        
        //update content
        this.drawTasksContent();
    },
    canUpdate:function()
    {
        var foo = new Date; // Generic JS date object
        var unixtime_ms = foo.getTime(); // Returns milliseconds since the epoch
        var unixtime = parseInt(unixtime_ms / (1000/SKConfig.skiliksSpeedFactor) );
        
        var variance = unixtime-dayPlan.timer;
        if(variance>3){
            dayPlan.timer = unixtime;
            return 1;
        }
        return 0;
    },
    todoViewChange: function(direction)
    {
        /*
        //заглушка, пока не удалось победить джкверри
        if(direction=='down'){
            if(this.tasksToDoState=='down'){
                return;
            }else if(this.tasksToDoState=='middle'){
                //опускаем вниз
                this.tasksToDoState = 'down';
            }else{
                this.tasksToDoState = 'middle';
            }
        }
        if(direction=='up'){
            if(this.tasksToDoState=='up'){
                return;
            }else if(this.tasksToDoState=='middle'){
                this.tasksToDoState = 'up';
            }else{
                //поднимаем вверх
                this.tasksToDoState = 'middle';
            }
        }
        
        var tasksToDoStateTemp = this.tasksToDoState;
        
        //убираем
        this.draw();
        this.tasksToDoState = tasksToDoStateTemp;
        // отрисовываем заново
        this.draw();
        return;*/
        
/**********************************************************END*/
        var upK = 210;
        var downK = 250;
        if(direction=='down'){
            if(this.tasksToDoState=='down'){
                return;
            }else if(this.tasksToDoState=='middle'){
                //опускаем вниз
                Ctop = parseFloat($('#dayPlanToDoDiv').css('top'));
                this.redrawTodoDiv();
                $('#dayPlanToDoDiv').css('top', (Ctop+downK)+'px');
                $('#dayPlanToDoDiv').html(this.todoHtmlMin);
                
                this.tasksToDoState = 'down';
            }else{
                Ctop = parseFloat($('#dayPlanToDoDiv').css('top'));
                this.redrawTodoDiv();
                $('#dayPlanToDoDiv').css('top', (Ctop+upK)+'px');
                $('#dayPlanToDoDiv').html(this.todoHtml);
                this.tasksToDoState = 'middle';
            }
        }
        if(direction=='up'){
            if(this.tasksToDoState=='up'){
                return;
            }else if(this.tasksToDoState=='middle'){
                Ctop = parseFloat($('#dayPlanToDoDiv').css('top'));
                this.redrawTodoDiv();
                $('#dayPlanToDoDiv').css('top', (Ctop-upK)+'px');
                $('#dayPlanToDoDiv').html(this.todoHtmlMax);
                this.tasksToDoState = 'up';
            }else{
                //поднимаем вверх

                Ctop = parseFloat($('#dayPlanToDoDiv').css('top'));
                this.redrawTodoDiv();
                $('#dayPlanToDoDiv').css('top', (Ctop-downK)+'px');
                $('#dayPlanToDoDiv').html(this.todoHtml);
                this.tasksToDoState = 'middle';
            }
        }
        
        
        $('#dayPlanTasksMainDiv').html($('#dayPlanTasksMainDiv').html());
        this.drawTasksContent();
        this.launchCustomSettings();
    },
    redrawTodoDiv:function()
    {
        /*$('#dayPlanMainDiv').remove();
        $('#dayPlanMainDivClose').remove();
        $('#dayPlanMainDivTimeline').remove();
        $('#dayPlanToDoDiv').remove();
        $('#dayPlanTasksMainDiv').remove();
        
        this.issetDiv = false;
        this.issetDivTasks = false;
        this.issetDivToDo = false;*/
        
        this.drawInterface();
    },
    showSystemMessage: function()
    {
          var message = this.systemMessageHtml;
          messages.showCustomSystemMessage(message);
    },
    systemMessageHtml:'<div class="planner-popup">'+
        	'<div class="planner-popup-tit"><img alt="" src="'+SKConfig.assetsUrl+'/img/mail/type-system-message.png"></div>'+
        	
        	'<p class="planner-popup-text">'+
        		'Вы не можете перенести эту встречу, так как не являетесь ее организатором.'+
        	'</p>'+
        	
        	'<table class="planner-popup-btn">'+
        		'<tbody><tr>'+
        			'<td>'+
        				'<div>'+
        					'<div onclick="messages.hideCustomSystemMessage();">Жаль</div>'+
        				'</div>'+
        			'</td>'+
        		'</tr>'+
        	'</tbody></table>'+
        '</div>',
    html:'{html}',
    timelineHtml:'<div class="planner-tine-line"></div>',
    closeHtml: '<img src="/img/interface/close.png" onclick="dayPlan.draw();" style="cursor:pointer;">',
    todoTaskHtml1: '<div class="day-plan-todo-task" id="{id}">{name}</div>',
    todoTaskHtml: '<div class="planner-task day-plan-todo-task" id="{id}">'+
        		'{name}'+
        		'<div><p><span>{time}</span><br />мин</p></div>'+
        	'</div>',
    dayPlanTaskHtml1: '<div class="label" style="width:200px; height:{height}px; top:{top}px;left:{left}px; position:absolute;" id="{id}">{name}</div>',
    dayPlanTaskHtml:'<div class="planner-task regular h2{addClass}" style="width:{width}px; height:{height}px; top:{top}px;left:{left}px; position:absolute;" id="{id}" title="{title}">'+
                    '<div><p>{name}</p></div>'+
                    '</div>',
    tasksHtml:'<div class="dayPlanTasksDiv" id="dayPlanTasksDiv">'+
            '</div>',
    todoHtml1:'<div class="day-plan-todo-div">'+
        '<div id="dayPlanTodoHat" class="day-plan-todo-hat">'+
                '<div id="dayPlanTodoHatInside" class="day-plan-todo-hat-inside"><p style="cursor:pointer;">Сделать</p></div>'+
                '<div id="dayPlanTodoNum" class="day-plan-todo-num">(0)</div>'+
                '<div id="dayPlanTodoArrowUp" class="day-plan-todo-arrow-up"><img src="/img/interface/up.png" onclick="dayPlan.todoViewChange(\'up\');" style="cursor:pointer;"></div>'+
                '<div id="dayPlanTodoArrowDown" class="day-plan-todo-arrow-down"><img src="/img/interface/down.png" onclick="dayPlan.todoViewChange(\'down\');" style="cursor:pointer;"></div>'+
        '</div>'+
        '<div id="dayPlanToDoDivScroll">'+
            
        '</div>'+
        '</div>',
    todoHtmlMin: '<div class="plan-todo closed">'+
        	'<ul class="plan-todo-btn">'+
        		'<li><button class="max" onclick="dayPlan.todoViewChange(\'up\');"></button></li>'+
        	'</ul>'+
        	
        	'<p class="plan-todo-tit"><span id="dayPlanTodoNum">(19)</span></p>'+
        	
        '</div>',
    todoHtmlMax: '<div class="plan-todo open">'+
        	'<ul class="plan-todo-btn">'+
        		'<li><button class="min" onclick="dayPlan.todoViewChange(\'down\');"></button></li>'+
        	'</ul>'+
        	
        	'<p class="plan-todo-tit">Сделать <span id="dayPlanTodoNum">(19)</span></p>'+
        	
        	'<div class="plan-todo-wrap" id="dayPlanToDoDivScroll" style="float:left;"></div>'+
                '<div id="plannerDayPlanScrollbar" class ="planner-dayplan-scrollbar" style="float:left;margin-top:50px;"></div>'+
        '</div>',
    todoHtml: undefined,

        doScrollable: function()
        {
            var div = document.createElement('div');
              div.setAttribute('id', 'plannerBookTodayScrollbar');
              div.setAttribute('class', 'planner-book-scrollbar');
              div.style.position = "absolute";
              div.style.zIndex = this.divZindex+4;
              document.body.appendChild(div);
              $('#plannerBookTodayScrollbar').css('top', (this.divTop+115)+'px');
              $('#plannerBookTodayScrollbar').css('left',  (this.divLeft+312)+'px');
              $('#plannerBookTodayScrollbar').css('height',  '350px');
            
            $("#plannerBookTodayScrollbar").slider({
                    orientation: "vertical",
                    min: 0,
                    max: 192,
                    value: 192,
                    slide: function(event, ui)
                    {
                        dayPlan.scrollTodayTimetable(ui.value);
                    }

                });

                var div = document.createElement('div');
              div.setAttribute('id', 'plannerBookTomorrowScrollbar');
              div.setAttribute('class', 'planner-book-scrollbar');
              div.style.position = "absolute";
              div.style.zIndex = this.divZindex+4;
              document.body.appendChild(div);
              $('#plannerBookTomorrowScrollbar').css('top', (this.divTop+115)+'px');
              $('#plannerBookTomorrowScrollbar').css('left',  (this.divLeft+583)+'px');
              $('#plannerBookTomorrowScrollbar').css('height',  '350px');
              
                $("#plannerBookTomorrowScrollbar").slider({
                    orientation: "vertical",
                    min: 0,
                    max: 192,
                    value: 192,
                    slide: function(event, ui)
                    {
                        dayPlan.scrollTomorrowTimetable(ui.value);
                    }

                });
                
                $("#plannerBookAfterVacationScrollbar").slider({
                    orientation: "vertical",
                    min: 0,
                    max: 192,
                    value: 192,
                    slide: function(event, ui)
                    {
                        dayPlan.scrollAfterVacationTable(ui.value);
                    }

                });
                $('#plannerBookAfterVacationScrollbar').css('height',  '350px');
                
                this.checkDisplayTasks();
        },
                
        scrollTodayTimetable: function (value)
        {
            var scrollValue = 192-value;
            $("#plannerBookTodayTimeTable").scrollTop(scrollValue);
        },      
        scrollTomorrowTimetable: function (value)
        {
            var scrollValue = 192-value;
            $("#plannerBookTomorrowTimeTable").scrollTop(scrollValue);
        },
                
        scrollAfterVacationTable: function (value)
        {
            var scrollValue = 192-value;
            $("#plannerBookAfterVacationTable").scrollTop(scrollValue);
        },
        addDayPlanScroll: function (Cheight)
        {
            if(this.tasksToDoState=='down'){return;}
            var upK = 0;
            if(this.tasksToDoState=='up'){upK = 210;}
            
            $("#plannerDayPlanScrollbar").slider({
                    orientation: "vertical",
                    min: 0,
                    max: Cheight,
                    value: Cheight,
                    slide: function(event, ui)
                    {
                        dayPlan.scrollDayPlanScroll(ui.value, Cheight);
                    }

                });
            $('#dayPlanToDoDivScroll').css('height',  (upK+250)+'px');
            $('#plannerDayPlanScrollbar').css('height',  (upK+150)+'px');
        },
        scrollDayPlanScroll: function (value, Cheight)
        {
            var scrollValue = Cheight-value;
            $("#dayPlanToDoDivScroll").scrollTop(scrollValue);
        },
        checkDisplayTasks: function()
        {
             for (var key in dayPlan.tasksdayPlan)
                {
                    var value = dayPlan.tasksdayPlan[key];
                    var keyArr = key.split('_');
                        //определяем отступы, пример ид ячейки = id="t1_09_1"
                        //формируем сперва ид нужной ячейки
                        CMainDiv = $("#dayPlanTasksMainDiv").offset();
                        var CslotId = 't'+keyArr[3]+'_'+keyArr[4];
                        var Cslot = $("#"+CslotId).offset();

                        var Ctop = Cslot.top-CMainDiv.top;
                        $('#'+key).css('top', Ctop);

                        if( Ctop<25 || Ctop>473){
                            $('#'+key).hide();
                        }else{
                            $('#'+key).show();
                        }
                }
        },
        launchCustomSettings:function()
        {
            //теперь делаем драгбл/дропабл
        $(function() {
            $( "#dayPlanToDoDiv" ).droppable({
                drop: function( event, ui ) {
                    dayPlan.saveDropDetails(ui.draggable.attr('id'), 'dayPlanToDoDivScroll');
                }
            });
            /*todo new drop logic*/
            $( "#plannerBook" ).droppable({
                    drop: function( event, ui ) {
                            
                            var newPosX = ui.offset.left;
                            var newPosY = ui.offset.top;
                            
                            var x = newPosX+(dayPlan.widthTask/2);
                            var y = newPosY;
                            
                            //+4 сделано для изменение центра диапазона установки задачи
                            y = y + 4;
                            
                            //t1_start
                            //t2_start
                            //t3_0900
                            
                            //определяем отступы по трем таблицам
                            var fstT1_startBounds = {
                                x: $('#t1_start').offset().left,
                                y: $('#t1_start').offset().top
                            };
                            
                            var fstT2_startBounds = {
                                x: $('#t2_start').offset().left,
                                y: $('#t2_start').offset().top
                            };
                            
                            var fstT3_0900Bounds = {
                                x: $('#t3_0900').offset().left,
                                y: $('#t3_0900').offset().top
                            };
                            
                            //определение дня
                            var yDiff = null;
                            var toDay = 1;
                            if(x > fstT3_0900Bounds.x){
                                toDay = 3;
                                yDiff = y - fstT3_0900Bounds.y;
                            }else if(x > fstT2_startBounds.x){
                                toDay = 2;
                                yDiff = y - fstT2_startBounds.y;
                            }else{
                                toDay = 1;
                                yDiff = y- fstT1_startBounds.y;
                            }
                            
                            //вычисляем дни и минуты
                            var yDiffPoints = Math.floor( (yDiff/(0.5+dayPlan.heightTaskSlot/2)) );
                            
                            var yH = Math.floor(yDiffPoints/4);
                            var yM = (yDiffPoints-(yH*4))*15;
                            yH = yH+9;
                            
                            //определение ИД
                            var yMs = php.LdgZero(yM, 2);
                            var yHs = php.LdgZero(yH, 2);
                            var toId = 't'+toDay+'_'+yHs+yMs;
                            
                            if(typeof($('#'+toId).get(0)) == 'undefined'){
                                return;
                            }
                            dayPlan.saveDropDetails(ui.draggable.attr('id'), toId);

                    }
            });
            
            //из дневного плана перетаскиваемость задач
            $( ".planner-task" ).each(function (i) {
                $(this).click(function (i) {
                    $('.day-plan-task-active').removeClass('day-plan-task-active');
                    dayPlan.taskdayPlanSelected = $(this).get(0).id;
                    $(this).addClass('day-plan-task-active');
                });
                
                var dayPlanDivBounds = objects.getBounds($('#dayPlanMainDiv').get(0));
                
                $(this).draggable({
                  containment:[dayPlanDivBounds.x, dayPlanDivBounds.y, (dayPlanDivBounds.x+dayPlanDivBounds.width), (dayPlanDivBounds.y+dayPlanDivBounds.height)],
                  appendTo: 'body',
                  helper: function (event) {
                    var id = $(this).attr('id');
                    var ret = $(this).clone();
                    ret.attr('dragId', id);
                    
                    remember = ret.html();
                        var idDiv = id;
                        var idParams = idDiv.split('_');
                        var heightDiv = (1+dayPlan.heightTaskSlot)*idParams[2]-2;
                        
                        var textTemp = dayPlan.formattingTaskTextLen(remember, idParams[2]);
                        var htmlNew = '<div class="planner-task regular h2" style="width:195px; height:'+heightDiv+'px;">'+textTemp+'</div>';
                    
                    var div = document.createElement('div');
                    div.setAttribute('id', id);
                    div.innerHTML = htmlNew;
                    
                    return div;
                  },

                  zIndex: 300,
                    start: function(e, ui) {
                        $('.ui-draggable-dragging').removeClass('day-plan-task-active');
                        
                        /*$(this).css('position', 'absolute');*/
                        /*remember = $(this).html();
                        var idDiv = $(this).get(0).id;
                        var idParams = idDiv.split('_');
                        var heightDiv = dayPlan.heightTaskSlot*idParams[2];

                        var textTemp = dayPlan.formattingTaskTextLen(remember, idParams[2]);
                        $(ui.helper).html('<div class="planner-task regular h2" style="width:195px; height:'+heightDiv+'px;">'+textTemp+'</div>');
                        $(ui.helper).css('height',(heightDiv+3)+'px');*/
                        
                        /*$(ui.helper).css('position', 'static');*/
                    },
                    stop: function(e, ui) {
                        /*$(this).html(remember);*/
                        $('.importantDragBGRule').removeClass('importantDragBGRule');
                    },
                    drag: function( event, ui )
                    {
                        var newPosX = ui.offset.left;
                        var x = newPosX+(dayPlan.widthTask/2);
                            
                        //t1_start
                        //t2_start
                        //t3_0900
                            
                        //определяем отступы по трем таблицам
                        var fstT1_startBounds = {
                            x: $('#t1_start').offset().left
                        };

                        var fstT2_startBounds = {
                            x: $('#t2_start').offset().left
                        };

                        var fstT3_0900Bounds = {
                            x: $('#t3_0900').offset().left
                        };
                           
                        $('.importantDragBGRule').removeClass('importantDragBGRule');
                        
                        var dragId = $('.ui-draggable-dragging').get(0).id;
                        var dragIdArr = dragId.split('_');
                        var dragIdDay = dragIdArr[3];
                        
                        //определение дня
                        var toDay = 1;
                        if(x > fstT3_0900Bounds.x){
                            toDay = 3;
                            if(dragIdDay != toDay){
                                $('#plannerBookAfterVacationTable').addClass('importantDragBGRule');
                            }
                        }else if(x > fstT2_startBounds.x){
                            toDay = 2;
                            if(dragIdDay != toDay){
                                $('#plannerBookTomorrowTimeTable').addClass('importantDragBGRule');
                            }
                        }else{
                            toDay = 1;
                            if(dragIdDay != toDay){
                                $('#plannerBookTodayTimeTable').addClass('importantDragBGRule');
                            }
                        }
                    }
                });
            });
            //из тду листа перетаскиваемость
            $( ".day-plan-todo-task" ).each(function (i) {
                $(this).click(function (i) {

                    $('.day-plan-task-active').removeClass('day-plan-task-active');
                    dayPlan.taskdayPlanSelected = false;
                    $(this).addClass('day-plan-task-active');

                });
                
                $(this).dblclick( function () {
                    var idDiv = $(this).get(0).id;
                    dayPlan.fastSwitchTodoToDayplan(idDiv);
                });
                
                var dayPlanDivBounds = objects.getBounds($('#dayPlanMainDiv').get(0));
                
                $(this).draggable({
                  containment:[dayPlanDivBounds.x, dayPlanDivBounds.y, (dayPlanDivBounds.x+dayPlanDivBounds.width), (dayPlanDivBounds.y+dayPlanDivBounds.height)],
                  appendTo: 'body',
                  helper: 'clone',
                  zIndex: 300,
                    start: function(e, ui) {
                        $('.ui-draggable-dragging').removeClass('day-plan-task-active');

                        remember = $(this).html();
                        var idDiv = $(this).get(0).id;
                        var idParams = idDiv.split('_');
                        var heightDiv = (1+dayPlan.heightTaskSlot)*idParams[2]-2;
                        
                        var textTemp = dayPlan.formattingTaskTextLen(remember, idParams[2]);
                        //исключаем теги
                        var textTempArr = textTemp.split('<');
                        textTemp = textTempArr[0];

                        $(ui.helper).html('<div class="planner-task regular h2" style="width:195px; height:'+heightDiv+'px;"><div><p>'+textTemp+'</p></div></div>');

                    },
                    stop: function(e, ui) {
                        $(this).html(remember);
                        
                        $('.importantDragBGRule').removeClass('importantDragBGRule');
                    },
                    drag: function( event, ui )
                    {
                        var newPosX = ui.offset.left;
                        var x = newPosX+(dayPlan.widthTask/2);
                            
                        //t1_start
                        //t2_start
                        //t3_0900
                            
                        //определяем отступы по трем таблицам
                        var fstT1_startBounds = {
                            x: $('#t1_start').offset().left
                        };

                        var fstT2_startBounds = {
                            x: $('#t2_start').offset().left
                        };

                        var fstT3_0900Bounds = {
                            x: $('#t3_0900').offset().left
                        };
                           
                        $('.importantDragBGRule').removeClass('importantDragBGRule');
                        
                        var dragId = $('.ui-draggable-dragging').get(0).id;
                        var dragIdArr = dragId.split('_');
                        var dragIdDay = dragIdArr[3];
                        
                        //определение дня
                        var toDay = 1;
                        if(x > fstT3_0900Bounds.x){
                            toDay = 3;
                            if(dragIdDay != toDay){
                                $('#plannerBookAfterVacationTable').addClass('importantDragBGRule');
                            }
                        }else if(x > fstT2_startBounds.x){
                            toDay = 2;
                            if(dragIdDay != toDay){
                                $('#plannerBookTomorrowTimeTable').addClass('importantDragBGRule');
                            }
                        }else{
                            toDay = 1;
                            if(dragIdDay != toDay){
                                $('#plannerBookTodayTimeTable').addClass('importantDragBGRule');
                            }
                        }
                    }
                });
            });
            
            //скролл Сегодня
             $('#plannerBookTodayTimeTable').scroll(function(){
                    var addTop = $(this).scrollTop();
                    dayPlan.updateTimeLine(1);
                    //перепозиционируем то, что после отпуска
                     for (var key in dayPlan.tasksdayPlan)
                        {
                            var value = dayPlan.tasksdayPlan[key];
                            var keyArr = key.split('_');
                            if(keyArr[3]=='1'){
                                //определяем отступы, пример ид ячейки = id="t1_09_1"
                                //формируем сперва ид нужной ячейки
                                CMainDiv = $("#dayPlanTasksMainDiv").offset();
                                var CslotId = 't'+keyArr[3]+'_'+keyArr[4];
                                var Cslot = $("#"+CslotId).offset();

                                var Ctop = Cslot.top-CMainDiv.top;
                                $('#'+key).css('top', Ctop);
                                
                                //а не вылазим ли мы ? 

                                if( Ctop<25 || Ctop>473){
                                    $('#'+key).hide();
                                }else{
                                    $('#'+key).show();
                                }
                            }
                        }
                });
            //скролл Завтра
             $('#plannerBookTomorrowTimeTable').scroll(function(){
                    var addTop = $(this).scrollTop();
                    //перепозиционируем то, что после отпуска
                     for (var key in dayPlan.tasksdayPlan)
                        {
                            var value = dayPlan.tasksdayPlan[key];
                            var keyArr = key.split('_');
                            if(keyArr[3]=='2'){
                                //определяем отступы, пример ид ячейки = id="t1_09_1"
                                //формируем сперва ид нужной ячейки
                                CMainDiv = $("#dayPlanTasksMainDiv").offset();
                                var CslotId = 't'+keyArr[3]+'_'+keyArr[4];
                                var Cslot = $("#"+CslotId).offset();

                                var Ctop = Cslot.top-CMainDiv.top;
                                $('#'+key).css('top', Ctop);
                                
                                //а не вылазим ли мы ? 
                                var headOffset = $(".planner-book-after-vacation-head").offset();
                                if( Ctop<25 || Ctop>473){
                                    $('#'+key).hide();
                                }else{
                                    $('#'+key).show();
                                }
                            }
                        }
                });
            //скролл после отпуска
             $('#plannerBookAfterVacationTable').scroll(function(){
                    var addTop = $(this).scrollTop();
                    //перепозиционируем то, что после отпуска
                     for (var key in dayPlan.tasksdayPlan)
                        {
                            var value = dayPlan.tasksdayPlan[key];
                            var keyArr = key.split('_');
                            if(keyArr[3]=='3'){
                                //определяем отступы, пример ид ячейки = id="t1_09_1"
                                //формируем сперва ид нужной ячейки
                                CMainDiv = $("#dayPlanTasksMainDiv").offset();
                                var CslotId = 't'+keyArr[3]+'_'+keyArr[4];
                                var Cslot = $("#"+CslotId).offset();

                                var Ctop = Cslot.top-CMainDiv.top;
                                $('#'+key).css('top', Ctop);
                                
                                //а не вылазим ли мы ? 
                                var headOffset = $(".planner-book-after-vacation-head").offset();
                                if( Ctop<25 || Ctop>473){
                                    $('#'+key).hide();
                                }else{
                                    $('#'+key).show();
                                }
                            }
                        }
                });
            });
        }

}