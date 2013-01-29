viewer = {
    status:0,
    issetDiv: false,
    divTop: 50,
    divLeft: 50,
    
    fileId:0,
    filename: '',
    images:{},
    
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
        var topZindex = php.getTopZindexOf();
        
        var div = document.createElement('div');
          div.setAttribute('id', 'viewerMainDiv');
          div.setAttribute('class', 'mail-emulator-main-div');
          div.style.position = "absolute";
          div.style.zIndex = parseInt(topZindex) + 1;
          document.body.appendChild(div);
          $('#viewerMainDiv').css('top', this.divTop+'px');
          $('#viewerMainDiv').css('left',  this.divLeft+'px');
          
          //close
          var div = document.createElement('div');
          div.setAttribute('id', 'viewerMainDivClose');
          div.setAttribute('class', 'viewerMainDivClose');
          div.style.position = "absolute";
          div.style.zIndex = parseInt(topZindex) + 1;
          document.body.appendChild(div);
          $('#viewerMainDivClose').css('top', (this.divTop + 5)+'px');
          $('#viewerMainDivClose').css('left',  (this.divLeft + 765)+'px');
          $('#viewerMainDivClose').html(this.closeHtml);
          
          this.issetDiv = true;
    },

    draw: function(fileId, filename)
    {
        if(this.status == 0){
            this.filename = filename;
            sender.viewerGet(fileId);
            this.status = 1;
            this.fileId = fileId;
            
            //логируем событие
            this.viewer_window = new window.SKDocumentsWindow('documentsFiles', fileId);
            this.viewer_window.open();
        }else{
            //логируем событие
            this.viewer_window.close();

            
            $('#viewerMainDiv').remove();
            $('#viewerMainDivClose').remove();
            this.issetDiv= false;
            this.status = 0;
            this.fileId = 0;
        }
        
    },
    
    receive: function(data)
    {
        this.drawInterface(data);
    },    
    
    drawInterface: function(data)
    {
        if(!this.issetDiv){
            this.createDiv();
        }
        
        this.images = data['data'];
        
        var html = this.html;
        html = php.str_replace('{filename}', this.filename, html);
        $('#viewerMainDiv').html(html);
        
        var imagesHTML = '';
        for (var key in this.images)
        {
            var value = this.images[key];
            /*imagesHTML += php.str_replace('{path}', this.filename+'/'+value, this.imgHTML);*/
            imagesHTML += php.str_replace('{path}', value, this.imgHTML);
        }
        $('#viewerContentDiv').html(imagesHTML);
    },
    
    closeHtml: '<img src="/img/interface/close.png" onclick="viewer.draw();" style="cursor:pointer;">',
    imgHTML: '<div class="viewer-img"><img src="/media/documents/{path}"></div>',
    html:'<form class="well">'+
            '<div class="viewer-main-div">'+
                '<div class="viewer-filename" id="viewerFilename">'+
                '<span class="badge badge-inverse">{filename}</span>'+
                '</div>'+
                '<div id="viewerContentDiv" class="viewer-content-div">'+
                '</div>'+
            '</div>'+ 
         '</form>'
}