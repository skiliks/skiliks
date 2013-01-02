dialogController = {
    status:0,
    activeSubScreen:'',
    issetDiv: false,
    divTop: 50,
    divLeft: 50,
    divCheight:0,
    topZindex: 50,
    
    answersShowFlag:1,
    timeToShow:0,
    
    setDivTop: function(val)
    {
        this.divTop = val;
    },
    
    setDivLeft: function(val)
    {
        this.divLeft = val;
    },
    
    setDivCheight: function(val)
    {
        this.divCheight = val;
    },
    getZindex: function()
    {
        return this.topZindex;
    },
    createDiv: function()
    {
        var topZindex = php.getTopZindexOf();
        this.topZindex = topZindex+4;
        videos.divZindex = this.topZindex-2;
        
        var div = document.createElement('div');
          div.setAttribute('id', 'dialogControllerMainDiv');
          div.setAttribute('class', 'mail-emulator-main-div');
          div.style.position = "absolute";
          div.style.zIndex = this.topZindex;
          document.body.appendChild(div);
          $('#dialogControllerMainDiv').css('top', this.divTop+'px');
          $('#dialogControllerMainDiv').css('left',  this.divLeft+'px');
          
          this.issetDiv = true;
    },

    draw: function(action, dialog)
    {
        $('#dialogControllerMainDiv').css('top', this.divTop+'px');
        $('#dialogControllerMainDiv').css('left',  this.divLeft+'px');
        
        if(this.status == 0 || typeof(action) != 'undefined'){
            if(action=='close'){
                this.closedialogController();
                //логируем событие
                SKApp.user.simulation.window_set.closeAll('visitor');
                this.activeSubScreen = '';
                return;
            }
            this.status = 1;
            
            this.drawInterface(action, dialog);
            
        }else{
            this.closedialogController();
            simulation.drawDefaultLocation();
            //логируем событие
            simulation.window_set.closeAll('visitor');
            this.activeSubScreen = '';
            return;
        }
        
    },
    closedialogController: function(){
        $('#dialogControllerMainDiv').remove();
            this.issetDiv= false;
            this.status = 0;
    },
    drawInterface: function(action, dialog)
    {
        if(!this.issetDiv){
            this.createDiv();
        }
        
        $('#dialogControllerMainDiv').css('top',  this.divTop+'px');
        $('#dialogControllerMainDiv').css('left',  this.divLeft+'px');
        $('#dialogControllerMainDiv').css('height', '');

        if(typeof(action) == 'undefined'){
            return;
        }else if(action=='income'){
            this.drawIncome(dialog);
        }else if(action='dialog'){
            this.dialogDisplay(dialog);
        }
        
    },
    drawIncome:function(dialog)
    {
        //логируем режим
        if(this.activeSubScreen !== 'visitorEntrance'){
            simulation.window_set.closeAll('visitor');
            this.activeSubScreen = 'visitorEntrance';
            this.visitor_entrance_window = new SKDialogWindow('visitor', 'visitorEntrance', dialog[0].id);
            this.visitor_entrance_window.open();
        }
        
        $('#dialogControllerMainDiv').css('left',  (this.divLeft+200)+'px');
        
        var callInHtml = this.incomeHTML;
        var callInHtmlAns = '';
        
        
        var fromUsFlag = 0;
        var toUsLastId = 0;
        
        var sound = '';
        var i=0;
        for (var key in dialog)
        {
            var value = dialog[key];
            if(value['ch_to']==1){
                sound = value['sound']
                toUsLastId = value['id'];
                callInHtml = php.str_replace('{id}', value['ch_from'], callInHtml);
                callInHtml = php.str_replace('{name}', value['name'], callInHtml);
                
            }else{
                //<a href="#"><span>войдите</span></a><br>
                callInHtmlAns+='<a onclick="dialogController.getSelect(\''+value['id']+'\','+i+')"><span>'+value['text']+'</span></a><br>';
                fromUsFlag = 1;
                i++;
            }
        }
        
        callInHtml = php.str_replace('{dialog_answers}', callInHtmlAns, callInHtml);
        
        $('#dialogControllerMainDiv').html(callInHtml);
        
        if(sound!='' && sound != '#'){
            if(sound.indexOf('.webm') > 0){
                videos.start(sound);
            }else if(sound.indexOf('.wav') > 0){
                sounds.start(sound);
            }
        }
        
        //а вдруг вариантов ответа нет
        if(fromUsFlag == 0)
        {
            setTimeout(function(){dialogController.getSelectByTimeout(toUsLastId);}, 5000);
        }
    },
    getSelectByTimeout:function(dialogId, flag)
    {
        if(!sounds.isset /*|| session.getSimulationType()=='dev'*/){
            this.visitor_entrance_window.setLastDialog(dialogId);
            sender.dialogsGetSelect(dialogId);
        }else{
             setTimeout(function(){dialogController.getSelectByTimeout(dialogId);}, 1000);
        }
        
        
    },
    getSelect:function(dialogId, flag)
    {
        var me = this;
        SKApp.server.api('dialog/get', {'dialogId': dialogId}, function (data) {
            if(data.result===1){
                me.visitor_entrance_window.setLastDialog(dialogId);
                if (data.events && data.events[0] && data.events[0].data && data.events[0].data[0] && data.events[0].data[0].step_number === '1' &&
                    data.events[0].data[0].dialog_subtype === '4') {
                        me.visitor_entrance_window.switchDialog(data.events[0].data[0].id);
                }
                simulation.parseNewEvents(data);
                if(flag===1){
                    me.closedialogController();
                }
            }
        });

    },
    dialogDisplay:function(dialog)
    {
        $('#dialogControllerMainDiv').css('top',  (this.divTop-20)+'px');
        $('#dialogControllerMainDiv').css('left',  (this.divLeft-20)+'px');
        $('#dialogControllerMainDiv').css('height', this.divCheight);
        

        //логируем режим
        if(this.activeSubScreen !== 'visitorTalk'){
            simulation.window_set.closeAll('visitor');
            this.activeSubScreen = 'visitorTalk';
            this.visitor_entrance_window = new SKDialogWindow('visitor', 'visitorTalk', dialog[0].id);
            this.visitor_entrance_window.open();
        }
        
        var callInHtml = this.dialogHTML;
        var callInHtmlAns = '';
        
        
        var fromUsFlag = 0;
        var toUsLastId = 0;
        
        var soundDuration = 0;
        var sound = '';
        var image = '';
        var i=0;
        for (var key in dialog)
        {
            var value = dialog[key];
            
            if (null == value.sound) {
                value.sound = '';
            }
            
            if(value['ch_to']==1){
                sound = value['sound'];
                toUsLastId = value['id'];
                soundDuration = value['duration'];
                
                callInHtml = php.str_replace('{id}', value['ch_from'], callInHtml);
                callInHtml = php.str_replace('{dialog_text}', value['text'], callInHtml);
                
                if(value['sound'].indexOf('.png') > 0){
                    image = value['sound'];
                }
                
            }else{
                if(value['sound'].indexOf('.png') > 0){
                    image = value['sound'];
                }
                
                //<li><p>Потрясающая безответственность! Вот уж от тебя никак не ожидал!</p><span></span></li>
                callInHtmlAns += '<li><p onclick="dialogController.getSelect(\''+value['id']+'\')">'+value['text']+'</p><span></span></li>';
                fromUsFlag = 1;
                i++;
            }
        }
        
        callInHtml = php.str_replace('{id}', '', callInHtml);
        callInHtml = php.str_replace('{dialog_text}', '', callInHtml);
        callInHtml = php.str_replace('{dialog_answers}', callInHtmlAns, callInHtml);
        
        $('#dialogControllerMainDiv').html(callInHtml);
        
        if(toUsLastId == 0){
            $('.visitor-reply').hide();
        }
        
        if(sound!='' && sound != '#'){
            if(sound.indexOf('.webm') > 0){
                videos.start(sound);
            }else if(sound.indexOf('.wav') > 0){
                sounds.start(sound);
            }
        }
        
        if(image!='' && image != '#'){
            this.loadCustomDialogMap(image);
        }else{
            simulation.drawDefaultLocation();
        }
        
        soundDuration = 5;
        //а вдруг нам надо послушать звук?
        if(soundDuration > 0 ){
            $('#dialogControllerAnswers').hide();
            this.answersShowFlag = 0;
            this.timeToShow = timer.getCurUnixtime() + (parseFloat(soundDuration));
        }
        
        //а вдруг вариантов ответа нет
        if(fromUsFlag == 0)
        {
            setTimeout(function(){dialogController.getSelectByTimeout(toUsLastId);}, 5000);
        }
        
        // fix to keep open dialog (phon talk or visit) alive when 
        // Main hero miss phone call and it ignored automatically
        simulation.isRecentlyIgnoredPhone = false;
    },
    update:function()
    {
        if(this.answersShowFlag == 1){return;}
        
        if(!videos.isset || session.getSimulationType()=='dev'){
            $('#dialogControllerAnswers').show();
            this.answersShowFlag = 1;
        }
    },
    loadCustomDialogMap: function (image)
    {

        var object = {
            image: 'media/dialog_images/'+image,
            sWidth: 1000,
            sHeight: 600
        };

        var img = object.image;
        object.imageSrc = new Image();
        object.imageSrc.src = img;
        object.imageSrc.onload = function(){
            simulation.drawLocation(image);
        }


        mapObjects['maps'][image] = object;
        
        return true;
    },
     incomeHTML:'<section class="visitor-income">'+
			'<div class="visitor-img"><img alt="" src="img/visitor/visitor-ch{id}.png"></div>'+
			
			'<div class="visitor-rbl">'+
				'<button class="btn-close"></button>'+
				
				'<p class="visitor-name">'+
					'{name}'+
				'</p>'+
				
				'<div class="visitor-btn">'+
					/*'<a href="#"><span>войдите</span></a><br>'+
					'<a href="#"><span>прошу войти позже</span></a>'+*/
                                        '{dialog_answers}'+
				'</div>'+
			'</div>'+
		'</section>',
    dialogHTML:'<div class="visitor-reply"><p>{dialog_text}</p><div></div></div>'+
        '<ul class="char-reply" id="dialogControllerAnswers">{dialog_answers}</ul>',
    dialogHTML1:'<section class="visitor-talk">'+
			'<div class="visitor-img"><img alt="" src="img/visitor/visitor-ch{id}.png"></div>'+
			
			'<div class="visitor-rbl">'+
				'<button class="btn-close" onclick="dialogController.draw(\'close\')"></button>'+
				
				'<p class="visitor-reply-ch min">{dialog_text}</p>'+
				
				'<ul class="visitor-reply-h" id="dialogControllerAnswers">'+
                                    '{dialog_answers}'+
				'</ul>'+
			'</div>'+
		'</section>',
    //todo временная хрень с переключением видимости/невидимости
    showFlag:1,
    showSwitcher:function()
    {
        if(this.showFlag == 1){
            $('#dialogControllerMainDiv').hide();
            this.showFlag = 0;
        }else{
            $('#dialogControllerMainDiv').show();
            this.showFlag = 1;
        }
    }
}