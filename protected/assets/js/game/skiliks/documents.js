documents = {
    status:0,
    issetDiv: false,
    divTop: 50,
    divLeft: 50,
    
    fileToSelect:0,
    
    folders:{
        1:'Czech', 
        2:'asdasd', 
        3:'dddd'
    },
    
    files:{},
    
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
          div.setAttribute('id', 'documentsMainDiv');
          div.setAttribute('class', 'mail-emulator-main-div');
          div.style.position = "absolute";
          div.style.zIndex = (topZindex+1);
          document.body.appendChild(div);
          $('#documentsMainDiv').css('top', this.divTop+'px');
          $('#documentsMainDiv').css('left',  this.divLeft+'px');
          
          //close
          var div = document.createElement('div');
          div.setAttribute('id', 'documentsMainDivClose');
          div.setAttribute('class', 'documentsMainDivClose');
          div.style.position = "absolute";
          div.style.zIndex = "50";
          document.body.appendChild(div);
          $('#documentsMainDivClose').css('top', (this.divTop-15)+'px');
          $('#documentsMainDivClose').css('right',  (this.divLeft-15)+'px');
          //$('#documentsMainDivClose').html(this.closeHtml);
          
          
          this.issetDiv = true;
    },

    draw: function()
    {
        if(this.status == 0){
            sender.documentsGetList();
            this.status = 1;
            allowScroll = false;
            
            //логируем событие
            this.list_window = new window.SKDocumentsWindow('documents');
            this.list_window.open();
        }else{
            $('#documentsScrollbar').remove();
            $('#documentsMainDiv').remove();
            $('#documentsMainDivClose').remove();
            this.issetDiv= false;
            this.status = 0;
            allowScroll = true;
            
            //логируем событие
            this.list_window.close();
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
        
        for (var key in data)
        {
            var value = data[key];
            this.files[value['id']] = data[key];
        }
        
        var html = this.html;
        $('#documentsMainDiv').html(html);
        
        //this.drawFolders();
        this.drawFiles();
    },
    drawFolders: function()
    {
        var html = '';
        for (var key in this.folders)
        {
            var value = this.folders[key];
            var htmlTemp = php.str_replace('{name}', value, this.folderHTML);
            htmlTemp = php.str_replace('{id}', key, htmlTemp);
            html += htmlTemp;
        }
        
        $('#documentsFolders').html(html);
        this.setActiveFolder(1);
    },
    setActiveFolder: function(id)
    {
        $('.documents-folder-active').removeClass('documents-folder-active');
        $('#documentsFolder_'+id).addClass('documents-folder-active');
    },
    drawFiles: function()
    {
        var html = '';
        var num = 1;
        for (var key in this.files)
        {
            var value = this.files[key];
            var htmlTemp = php.str_replace('{name}', value['name'], this.fileHTML);
            htmlTemp = php.str_replace('{id}', value['id'], htmlTemp);
            htmlTemp = php.str_replace('{num}', num, htmlTemp);
            
            var valueArr = value['name'].split('.');
            var type = valueArr[1];
            htmlTemp = php.str_replace('{type}', type, htmlTemp);
            
            html += htmlTemp;
            num++;
        }
        
        $('#documentsContentDiv').html(html);
        
        $('.documents-file').dblclick(function(i){
            var idArr = $(this).get(0).id.split('_');
            documents.openFile(idArr[1]);
        });
        
        if(this.fileToSelect != 0){
            this.setActiveFile(this.fileToSelect);
            this.fileToSelect = 0;
        }
        
        var Cheight = ((php.count(this.files)/5).toFixed(0))*150
        documents.addDocumentsScroll(Cheight);
    },
    setActiveFile: function(id)
    {
        if(0 < $('#excelMainDiv').length) {
            $('#excelMainDiv').css('z-index',2 + parseInt($('#excelMainDiv').css('z-index')));
            $('#excelMainDivClose').css('z-index',2 + parseInt($('#excelMainDiv').css('z-index')));
            excel.status = 1;
            excel.draw(id);
        } else {
            $('.documents-file-active a').removeClass('active');
            $('.documents-file-active').removeClass('documents-file-active');

            $('#documentsFile_'+id).addClass('documents-file-active');
            $('#documentsFile_'+id+' a').addClass('active');
        }
    },
    keyboardUp: function()
    {
        if(typeof($(".documents-file-active").get(0)) == 'undefined'){return;}
        var curId = $(".documents-file-active").get(0).id;
        var curIdArr = curId.split('_');
        var id = parseInt(curIdArr[1]);
        
        if(typeof($('#documentsFile_'+(id-1)).get(0)) != 'undefined'){
            this.setActiveFile(id-1);
        }
    },
    keyboardDown: function()
    {
        if(typeof($(".documents-file-active").get(0)) == 'undefined'){return;}
        var curId = $(".documents-file-active").get(0).id;
        var curIdArr = curId.split('_');
        var id = parseInt(curIdArr[1]);
        
        if(typeof($('#documentsFile_'+(id+1)).get(0)) != 'undefined'){
            this.setActiveFile(id+1);
        }
    },
    keyboardEnter:function()
    {
        if(typeof($(".documents-file-active").get(0)) == 'undefined'){return;}
        var curId = $(".documents-file-active").get(0).id;
        var curIdArr = curId.split('_');
        var id = parseInt(curIdArr[1]);
        this.openFile(id);
    },
    openFile: function(id)
    {
        var filename = this.files[id]['name'];
        var valueArr = filename.split('.');
        var type = valueArr[1];
        this.draw();
        
        if(type == 'xls'){
            //запуск екселя
            excel.draw(id);
        }else{
            //viewer
            viewer.draw(id, filename);
        }
    },
    
    closeHtml: '<img src="/img/interface/close.png" onclick="documents.draw();" style="cursor:pointer;">',
    folderHTML: '<div class="documents-folder" id="documentsFolder_{id}"><img src="' + SKConfig.assetsUrl +'/img/documents/folder.png"> {name}</div>',
    fileHTML1: '<div class="documents-file" id="documentsFile_{id}" onclick="documents.setActiveFile({id})"><img src="/img/documents/{type}.png"> {name}</div>',
    fileHTML: '<li id="documentsFile_{id}" onclick="documents.setActiveFile({id});" class="documents-file">'+
                    '<a href="#"><span class="files-img"><img src="' + SKConfig.assetsUrl +'/img/files/icon-{type}.jpg" alt="" /></span><span class="files-title">{name}</span></a>'+
            '</li>',
    html1:'<form class="well">'+
            '<div class="documents-main-div">'+
                '<div class="documents-folders" id="documentsFolders">'+
                '</div>'+
                '<div id="documentsContentDiv" class="documents-content-div">'+
                '</div>'+
            '</div>'+ 
         '</form>',
     html:'<section class="files">'+
			'<header>'+
				'<h1>Мои документы</h1>'+
				
				'<div class="btn-close" onclick="documents.draw();"><button></button></div>'+
				
				'<nav><a href="#" class="back"></a><a href="#" class="forward"></a></nav>'+
				
				'<p>Мои компьютер<span>Мои документы</span></p>'+
			'</header>'+
			
			'<ul class="nav">'+
				'<li><span>Мои документы</span></li>'+
			'</ul>'+
			
			'<ul class="files" id="documentsContentDiv">'+
                        '</ul>'+
                        '<div class="files-scroll"></div>'+
		'</section>',
    addDocumentsScroll: function (Cheight)
    {
        var topZindex = php.getTopZindexOf();
            
            var div = document.createElement('div');
              div.setAttribute('id', 'documentsScrollbar');
              div.setAttribute('class', 'planner-dayplan-scrollbar');
              div.style.position = "absolute";
              div.style.zIndex = (topZindex+1);
              document.body.appendChild(div);
              $('#documentsScrollbar').css('top', (this.divTop+135)+'px');
              $('#documentsScrollbar').css('left',  (this.divLeft+835)+'px');
              $('#documentsScrollbar').css('height',  '375px');
              $('#documentsScrollbar').css('width',  '1px');
              $('#documentsScrollbar').css('border',  '0px');
              
              
        $("#documentsScrollbar").slider({
                orientation: "vertical",
                min: 0,
                max: Cheight,
                value: Cheight,
                slide: function(event, ui)
                {
                    documents.scrollDocumentsScroll(ui.value, Cheight);
                }

            });
    },
    scrollDocumentsScroll: function (value, Cheight)
    {
        var scrollValue = Cheight-value;
        $("#documentsContentDiv").scrollTop(scrollValue);
    }
}