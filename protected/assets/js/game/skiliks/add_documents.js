addDocuments = {
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
          div.setAttribute('id', 'addDocumentsMainDiv');
          div.setAttribute('class', 'addDocumentsMainDiv');
          div.style.position = "absolute";
          div.style.zIndex = "50";
          document.body.appendChild(div);
          $('#addDocumentsMainDiv').css('top', this.divTop+'px');
          $('#addDocumentsMainDiv').css('left',  this.divLeft+'px');
          $('#addDocumentsMainDiv').css('width',  '250px');
          
          this.issetDiv = true;
    },
    draw: function()
    {
        //todo временно коментируем, делаем 1 кнопку
        /*if(session.getSimulationType()!='dev'){return;}*/
        
        this.drawInterface();
    },
    drawInterface: function()
    {
        if(!this.issetDiv){
            this.createDiv();
        }
        
        var html = this.html;
        
        
        //todo временный кусок кода
        if(session.getSimulationType()!='dev'){
            html = this.htmlToAll;
        }else{
            html = this.html+this.htmlToAll;
        }
        //end
        
        $('#addDocumentsMainDiv').html(html);
    },
    html:'<input type="button" class="btn" onclick="documents.draw()" value="documents"><br>'+
        '<input type="button" class="btn" onclick="videos.start()" value="video"><br>'+
        '<input type="button" class="btn" onclick="sender.excelPointsReload()" value="excelReload"><br>',
    htmlToAll:'<input class="btn"  type="button" onclick="simulation.stop()" value="SIM стоп"><br>'
}