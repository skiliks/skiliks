addAnimation = {
    issetDiv: false,
    divTop: 50,
    divLeft: 50,
    
    play:0,
    counter:0,
    key:0,

    setDivTop: function(val)
    {
        this.divTop = val;
    },
    setDivLeft: function(val)
    {
        this.divLeft = val;
    },
    createDiv: function()
    {
        var div = document.createElement('div');
          div.setAttribute('id', 'addAnimationMainDiv');
          div.setAttribute('class', 'addAnimationMainDiv');
          div.style.position = "absolute";
          div.style.zIndex = "50";
          document.body.appendChild(div);
          $('#addAnimationMainDiv').css('top', this.divTop+'px');
          $('#addAnimationMainDiv').css('left',  this.divLeft+'px');
          $('#addAnimationMainDiv').css('right',  this.divLeft+'px');
          
          this.issetDiv = true;
    },
    draw: function()
    {
        if(session.getSimulationType()!='dev'){return;}
        
        this.drawInterface();
    },
    drawInterface: function()
    {
        if(!this.issetDiv){
            this.createDiv();
        }
        
        var html = this.html;
        
        $('#addAnimationMainDiv').html(html);
    },
    animate:function()
    {
        //добавляем картинку
        var object = mapObjects['animations']['animation']['1']['frame0'];
        
        object.sx = 0;
        object.sy = 0;
        object.centerX = 600;
        object.cenerY = 325;
        object.dWidth = object.sWidth;
        object.dHeight = object.sHeight;
        object.angle = 0;
        object.scaleX = 1;
        object.alpha = 1;
        var key = drawGame.drawObjects.length;
        drawGame.drawObjects[key] = object;
        this.key = key;
        this.play = 1;
    },
    update:function()
    {
        if(this.play == 0){return;}
        this.counter = this.counter+1;
        if( typeof(mapObjects['animations']['animation']['1']['frame'+this.counter]) == 'undefined' )
        {
            delete drawGame.drawObjects[this.key];
            this.play = 0;
            this.key = 0;
            this.counter = 0;
            return;
        }
        
        var object = mapObjects['animations']['animation']['1']['frame'+this.counter];
        
        object.sx = 0;
        object.sy = 0;
        object.centerX = 600;
        object.cenerY = 325;
        object.dWidth = object.sWidth;
        object.dHeight = object.sHeight;
        object.angle = 0;
        object.scaleX = 1;
        object.alpha = 1;

        drawGame.drawObjects[this.key] = object;
    },
    html:'<input class="btn" type="button" onclick="addAnimation.animate()" value="animate">'
}