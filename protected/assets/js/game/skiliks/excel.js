excel = {
    status:0,
    fileId:0,
    issetDiv: false,
    divTop: 50,
    divLeft: 50,
    
    worksheets:{},
    currentWorksheet: false,
    currentWorksheetNext: false,
    worksheetData:{},
    strings:false,
    columns: false,
    
    tdHeight:18,
    tdWidth:28,
    tdWidthArr:{},
    tdWidthSumm : 0,
    
    tdNavHeight:18,
    tdNavWidth:28,
    
    stringMaxLen:10,
    
    cellsSelected:[],   
    cellsSelectedCopy:[],
    cellEdited: false,
    drawingCS:false,
    worksheetCopy:false,
    cellEditLastValue:[],
    ctrlDown : false,
    keyboardKeyPressValue : false,
    ExcelID : null,
    formulaFocused:false,
    curExcelID : null,
    allowToClose: true,
    curExcelTime: null,
    /**
     * Indicate is document in cache or not
     * @var array of boolean, indexed by document ID
     */
    isDocInCache: new Array(),
    zohoIframeBaseName: 'exelDocumentIframe',
    
    areYouSaveDocumentMessage: 'Вы сохранили документ Excel?',

    setDivTop: function(val)
    {
        this.divTop = val;
    },
    setDivLeft: function(val)
    {
        this.divLeft = val;
    },
    createDiv: function(fileId)
    {
        var topZindex = php.getTopZindexOf();
        
        var div = document.createElement('div');
          div.setAttribute('id', 'excelMainDiv');
          div.setAttribute('class', 'excelMainDiv');
          div.style.position = "absolute";
          div.style.zIndex = parseInt(topZindex) + 1;
          document.body.appendChild(div);
          $('#excelMainDiv').css('top', this.divTop+'px');
          $('#excelMainDiv').css('left',  this.divLeft+'px');
          
          //close
          var div = document.createElement('div');
          div.setAttribute('id', 'excelMainDivClose');
          div.setAttribute('class', 'excelMainDivClose');
          div.style.position = "absolute";
          div.style.zIndex = parseInt(topZindex) + 1;
          document.body.appendChild(div);
          $('#excelMainDivClose').css('top', (this.divTop + 15)+'px');
          $('#excelMainDivClose').css('left',  (this.divLeft + 800)+'px');
          $('#excelMainDivClose').html(this.getCloseHtml(fileId));
          
          //drawing
          var div = document.createElement('div');
          div.setAttribute('id', 'excelMainDivDrawing');
          div.setAttribute('class', 'excelMainDivDrawing');
          div.style.position = "absolute";
          div.style.zIndex = (topZindex+2);
          document.body.appendChild(div);
          $('#excelMainDivDrawing').css('cursor', 'pointer');
          $('#excelMainDivDrawing').css('height',  '6px');
          $('#excelMainDivDrawing').css('width',  '6px');
          $('#excelMainDivDrawing').html('<span class="excel-copy-icon"></span>');
          $('#excelMainDivDrawing').hide();
          
          this.issetDiv = true;
    },

    draw: function(fileId)
    {
        if(typeof(fileId) == 'undefined'){
            fileId = 0;
        }
            
        if(this.status == 0){
            if (true !== this.isDocInCache[fileId.toString()]) {
                // prevent uploading file if it in cache
                sender.excelGet(fileId);
                this.isDocInCache[fileId.toString()] = true; // mark cached
            }
            this.status = 1;
            allowScroll = false;
            this.fileId = fileId;
            
            //логируем OPEN
            this.excel_window = new window.SKDocumentsWindow('documentsFiles', fileId);
            this.excel_window.open();
            
            if (true == this.isDocInCache[fileId.toString()]) {
                // init build interface
                this.initFromCache(fileId);
            }

        }else{
            //Close

                // закрываем окна
                $('#excelMainDiv').remove();
                $('#excelMainDivClose').remove();
                $('#excelMainDivDrawing').remove();
                $("#excelContentScrollbar").remove();
                $("#excelContentScrollbarH").remove();
                
                // I have no ability to find what zoho-iframe is active at this moment, 
                // when user try to open another excel-document
                // @solution: hide all zoho-iframes
                $('iframe.excel-mother-content').hide(); // <- sorry, this is dirty trick
                // $('#' + this.zohoIframeBaseName + '-' + fileId).hide(); // close active excel-doc
                this.excel_window.close();

                this.issetDiv= false;
                this.cellsSelected=[];
                this.status = 0;
                this.fileId = 0;
                allowScroll = true;
        
        }
        
    },
    initFromCache: function(fileId)
    {
         if(!this.issetDiv){
            this.createDiv(fileId);
        }
        
        // check with getBaseHtml() HTML
        $('#excelMainDiv').append('<section class="excel"></section>');
        $('#excelMainDiv section').append('<header></header>');        

        this.placeZohoIframe(fileId);
    },
    receive: function(data)
    {
        //worksheets
        this.worksheets = {};
        for (var key in data["worksheets"])
        {
            var value = data["worksheets"][key];
            this.worksheets['w_'+value["id"]]=value["title"];
        }
        
        //сортируем массив воркшитов под текущую верстку(вывод в обратном порядке)
        var worksheetsTemp = this.worksheets;
        worksheetsTemp = php.objReverseSort(worksheetsTemp);
        this.worksheets = worksheetsTemp;

        
        this.currentWorksheet = data["currentWorksheet"];
        
        //worksheetData
        this.worksheetData = {};
        for (var key in data["worksheetData"])
        {
            var value = data["worksheetData"][key];
            this.worksheetData[value["column"]+'_'+value["string"]]=value;
        }
        this.strings = data["strings"];
        this.columns = data["columns"];
        
        if( php.is_int(data["cellHeight"]) ){this.tdHeight = parseInt(data["cellHeight"]);}
        if( php.is_int(data["cellWidth"]) ){this.tdWidth = parseInt(data["cellWidth"]);}
        this.tdWidthArr = {};
        this.tdWidthSumm = 0;
        
        if ('undefined' == typeof data || 'undefined' == typeof data.excelDocumentUrl) {
            data.excelDocumentUrl = '/pages/excel/fileNotFound.html';
        }
        
        this.drawInterface(data.excelDocumentUrl, data.fileId);
    },
     formattingStringValue: function(string)
    {
        string = string + '';     // конвертируем в строку
        
        // число ?
        if(string.length>3){
            var stringTemp = parseFloat( php.str_replace(',', '.', string) );
            if( string - (string%1) == string){
                string = accounting.formatNumber(stringTemp, 0, " ", ",");
            }else if(stringTemp == parseFloat(stringTemp)){
                string = accounting.formatNumber(stringTemp, 2, " ", ",");
            }
        }

    return string;
    },
    saveDocument: function(id)
    {
        /*sender.excelSaveDocument();*/
    },
    placeZohoIframe: function(fileId) {
        $('#' + this.zohoIframeBaseName + '-' + fileId).css('width', '845px');
        $('#' + this.zohoIframeBaseName + '-' + fileId).css('top', '82px');
        $('#' + this.zohoIframeBaseName + '-' + fileId).css('height', '510px');
        $('#' + this.zohoIframeBaseName + '-' + fileId).css('position', 'absolute');
        $('#' + this.zohoIframeBaseName + '-' + fileId).css('left', ($('#excelMainDiv').offset().left + 2) + 'px');
        $('#' + this.zohoIframeBaseName + '-' + fileId).css('z-index', $('#excelMainDiv').css('z-index') + 1);
        $('#' + this.zohoIframeBaseName + '-' + fileId).show();
    },
    drawInterface: function(excelDocumentUrl, documentId)
    {
        if(!this.issetDiv){
            this.createDiv(documentId);
        }
        
        // build window
        $('#excelMainDiv').html(this.getBaseHtml(excelDocumentUrl, documentId));
        
        // add zoho iframe
        $('#excelMainDiv').after(
            '<iframe id="' + this.zohoIframeBaseName + '-' + documentId + '" src="' + 
            excelDocumentUrl + 
            '" class="excel-mother-content" scrolling="no" style="background-color: #fff;"></iframe>'
        );
        this.placeZohoIframe(documentId);
    },
    addHint: function(cellId)
    {
        //id="excelString_'+alphaColumn+'_'+string+'"
        var cellIdArr = cellId.split('_');
        var dataKey = cellIdArr[1]+'_'+cellIdArr[2];
        if(typeof( this.worksheetData[dataKey]) != 'undefined' && this.worksheetData[dataKey].comment!=''){
            //добавляем уголок
            //$('#'+cellId).css('background', 'url(img/excel/hint.png) no-repeat top right');
            $('#'+cellId).css('background', 'url("../img/excel/icon-oa.png") no-repeat scroll 0 0 transparent');
            //вставляем хинт
            $('#'+cellId).balloon({
                contents: this.worksheetData[dataKey].comment,
                position: "right"
            });
        }
    },
    excelScrollToMe: function (newCellId, columns, strings, counter) {
        if( typeof(counter) == 'undefined'){
            counter = 0;
        }
        counter++;
        
        if(counter>10){
            return;
        }
        
        var newCellIdArr = newCellId.split('_');
        var dataKey = newCellIdArr[1]+'_'+newCellIdArr[2];
        if(typeof( this.worksheetData[dataKey]) == 'undefined' || this.worksheetData[dataKey]['colspan']>1){
            return;
        }
        
        var x = columns*this.tdWidth-100;
        var y = strings*this.tdHeight-100;
        
        if(x<0){x=0;}
        if(y<0){y=0;}
        
        /*$("#excelContent").scrollLeft(x);
$("#excelContent").scrollTop(y);*/
        
        var parentOffset = $('#excelContent').offset();
        var parentWidth = $('#excelContent').width();
        var parentHeight = $('#excelContent').height();
        
        var childOffset = $('#'+newCellId).offset();
        var childWidth = $('#'+newCellId).width();
        var childHeight = $('#'+newCellId).height();
        
        if(childOffset.left < parentOffset.left)
        {
            $('#excelContent').scrollLeft( ($('#excelContent').scrollLeft()-25) );
            this. excelScrollToMe(newCellId, columns, strings, counter);
            return;
        }
        if(childOffset.top < parentOffset.top)
        {
            $('#excelContent').scrollTop( ($('#excelContent').scrollTop()-25) );
            this. excelScrollToMe(newCellId, columns, strings, counter);
            return;
        }
        
        if( (childOffset.left+childWidth) > (parentOffset.left+parentWidth-10) )
        {
            $('#excelContent').scrollLeft( ($('#excelContent').scrollLeft()+25) );
            this. excelScrollToMe(newCellId, columns, strings, counter);
            return;
        }
        if( (childOffset.top+childHeight) > (parentOffset.top+parentHeight-10) )
        {
            $('#excelContent').scrollTop( ($('#excelContent').scrollTop()+25) );
            this. excelScrollToMe(newCellId, columns, strings, counter);
            return;
        }
        
    },
    collspanValidator: function(cellNameArr, counter){
        if( typeof(counter) == 'undefined'){
            counter = 0;
        }
        counter++;
        
        if(counter>25){
            return false;
        }
        
        var cellIdArr = new Array();
        cellIdArr[0] = cellNameArr[0];
        cellIdArr[1] = php.num2alpha( (parseFloat(cellNameArr[1])-counter) ) ;
        cellIdArr[2] = cellNameArr[2];
    
        var newCellId = cellIdArr.join('_');
        
        if(typeof($("#"+newCellId).get(0)) !='undefined'){
            return cellIdArr;
        }else{
            return this.collspanValidator(cellNameArr, counter);
        }
    },
    keyboardUp: function()
    {
        if(this.issetDiv == false){return;}
        var inputVal = $('.excel-nav-menu-text-formula').val();
        if(inputVal.charAt(0) == '=' && typeof($(".excel-cell-edit-text").parent().get(0)) !='undefined' ){return;}
        if(inputVal.charAt(0) == '=' && this.formulaFocused==true ){return;}
        
        
        for (var key in excel.cellsSelected) {
            var cellId = excel.cellsSelected[key];
            var cellIdArr = cellId.split('_');
            
            var cellName = $("#"+cellId).attr('name');
            var cellNameArr = cellName.split('_');
            
            cellIdArr[2] = parseFloat(cellIdArr[2])-1;
            
            //а вдруг коллспан?
            var cellNameArrTemp = cellNameArr;
            cellNameArrTemp[2] = parseFloat(cellNameArrTemp[2])-1;
            var validArr = this.collspanValidator(cellNameArrTemp);
            if(validArr){cellIdArr = validArr;}
            
            var newCellId = cellIdArr.join('_');
            
            
            if(typeof($("#"+newCellId).get(0)) !='undefined' && cellIdArr[2]>0){
                this.cellSelect(newCellId);
                excel.excelScrollToMe(newCellId, cellNameArr[1]-1, cellIdArr[2]);
            }
            return;
        }
    },
    keyboardLeft: function()
    {
        if(this.issetDiv == false){return;}
        var inputVal = $('.excel-nav-menu-text-formula').val();
        if(inputVal.charAt(0) == '=' && typeof($(".excel-cell-edit-text").parent().get(0)) !='undefined' ){return;}
        if(inputVal.charAt(0) == '=' && this.formulaFocused==true ){return;}
        
        for (var key in excel.cellsSelected) {
            var cellId = excel.cellsSelected[key];
            var cellIdArr = cellId.split('_');
            
            var cellName = $("#"+cellId).attr('name');
            var cellNameArr = cellName.split('_');
            var colspan = $("#"+cellId).attr('colspan');
            
            cellIdArr[1] = php.num2alpha( (parseFloat(cellNameArr[1])-2) ) ;
            /*cellIdArr[1] = php.num2alpha( (parseFloat(cellNameArr[1])-1-colspan) ) ;*/
            
            //а вдруг коллспан?
            var cellNameArrTemp = cellNameArr;
            cellNameArrTemp[1] = parseFloat(cellNameArrTemp[1])-1;
            var validArr = this.collspanValidator(cellNameArrTemp);
            if(validArr){cellIdArr = validArr;}
            
            var newCellId = cellIdArr.join('_');
            
            if(typeof($("#"+newCellId).get(0)) !='undefined' && (parseFloat(cellNameArr[1])-1)>=0 ){
                this.cellSelect(newCellId);
                excel.excelScrollToMe(newCellId, cellNameArr[1]-2, cellIdArr[2]);
            }
            return;
        }
    },
    keyboardDown: function()
    {
        if(this.issetDiv == false){return;}
        var inputVal = $('.excel-nav-menu-text-formula').val();
        if(inputVal.charAt(0) == '=' && typeof($(".excel-cell-edit-text").parent().get(0)) !='undefined' ){return;}
        if(inputVal.charAt(0) == '=' && this.formulaFocused==true ){return;}
        
        for (var key in excel.cellsSelected) {
            var cellId = excel.cellsSelected[key];
            var cellIdArr = cellId.split('_');
            
            var cellName = $("#"+cellId).attr('name');
            var cellNameArr = cellName.split('_');
            
            cellIdArr[2] = parseFloat(cellIdArr[2])+1;
            
            //а вдруг коллспан?
            var cellNameArrTemp = cellNameArr;
            cellNameArrTemp[2] = parseFloat(cellNameArrTemp[2])+1;
            var validArr = this.collspanValidator(cellNameArrTemp);
            if(validArr){cellIdArr = validArr;}
            
            var newCellId = cellIdArr.join('_');
            
            if(typeof($("#"+newCellId).get(0)) !='undefined' && cellIdArr[2]<=excel.strings){
                this.cellSelect(newCellId);
                excel.excelScrollToMe(newCellId, cellNameArr[1]-1, cellIdArr[2]);
            }
            return;
        }
    },
    keyboardCtrlDown: function()
    {
        if(this.issetDiv == false){return;}
        if(this.ctrlDown == true){return;}
        this.ctrlDown = true;
    },
    keyboardCtrlUp: function()
    {
        if(this.issetDiv == false){return;}
        this.ctrlDown = false;
    },
    keyboardRight: function()
    {
        if(this.issetDiv == false){return;}
        var inputVal = $('.excel-nav-menu-text-formula').val();
        if(inputVal.charAt(0) == '=' && typeof($(".excel-cell-edit-text").parent().get(0)) !='undefined' ){return;}
        if(inputVal.charAt(0) == '=' && this.formulaFocused==true ){return;}
        
        for (var key in excel.cellsSelected) {
            var cellId = excel.cellsSelected[key];
            var cellIdArr = cellId.split('_');
            
            var cellName = $("#"+cellId).attr('name');
            var cellNameArr = cellName.split('_');
            var colspan = $("#"+cellId).attr('colspan');
            
            /*cellIdArr[1] = php.num2alpha( (parseFloat(cellNameArr[1])-1+colspan) ) ;*/
            cellIdArr[1] = php.num2alpha( (parseFloat(cellNameArr[1])) ) ;
            
            //а вдруг коллспан?
            var cellNameArrTemp = cellNameArr;
            cellNameArrTemp[1] = parseFloat(cellNameArrTemp[1])+1;
            var validArr = this.collspanValidator(cellNameArrTemp);
            if(validArr){cellIdArr = validArr;}
            
            var newCellId = cellIdArr.join('_');
            
            if(typeof($("#"+newCellId).get(0)) !='undefined' && (parseFloat(cellNameArr[1])-1)<=excel.columns ){
                this.cellSelect(newCellId);
                excel.excelScrollToMe(newCellId, cellNameArr[1], cellIdArr[2]);
            }
            return;
        }
        
    },
    uiSelectedAddBorders:function(color, customClass)
    {
        if(typeof(customClass) == 'undefined')
        {
            customClass = 'ui-selected';
        }
        
        $( "."+customClass ).each(function (i) {
                var curId = $(this).get(0).id;
                var cellId = curId;
              $('#'+cellId).css('background','');
                var id = cellId;
                var idArr = id.split('_');
                
                excel.restoreBorders(idArr[1], idArr[2]);
        });
            
        var i = 0;
            var fStr = 0;
            var fCol= '';
            var lStr = 0;
            var lCol= '';
            var strTemp = 0;
            var colArr = new Array();
            $( "."+customClass ).each(function (i) {
                var curId = $(this).get(0).id;
                
                $('#'+curId).css('background','#ffffff');
                
                var idArr = curId.split('_');
                var key = idArr[1]+'_'+idArr[2];
                
                if(i==0){
                    fCol = idArr[1];
                    fStr = idArr[2];
                };
                
                if(!php.arraySearch(colArr, idArr[1])){
                    colArr.push(idArr[1]);
                }
                
                if(lCol=='' && (strTemp == 0 || strTemp==(parseFloat(idArr[2])-1))){
                    strTemp = idArr[2];
                }else{
                    lCol = idArr[1];
                }
                    
                if(lStr < parseFloat(idArr[2])){
                    lStr = parseFloat(idArr[2]);
                }
                
                i++;
            });
            
            i=0;
            for (var key in colArr) {
                var value = colArr[key];
                $('#excelString_'+value+'_'+fStr).css('border-top', '2px solid '+color);
                $('#excelString_'+value+'_'+lStr).css('border-bottom', '2px solid '+color);
            }
            for (var i = fStr; i <= lStr; i++) {
               $('#excelString_'+fCol+'_'+i).css('border-left', '2px solid '+color);
                $('#excelString_'+lCol+'_'+i).css('border-right', '2px solid '+color);
            }
    },
    cellSelect: function(cellId, type)
    {
        if(typeof(cellId)=='undefined'){return;}
        if( typeof(type) =='undefined' ){
            type = 'text';
        }

        this.ctrlDown = false;
        
        
        //надо добавить значение при нажатии кнопки
        var inputVal = $('.excel-nav-menu-text-formula').val();
        if(inputVal.charAt(0) == '=' && typeof($(".excel-cell-edit-text").parent().get(0)) !='undefined' ){
            for (var key in excel.cellsSelected) {
                var value = excel.cellsSelected[key];
                
                //перевыделяем нас, а вдруг выделение слетело
                $('.ui-selected').each(function(){
                    var id = $(this).get(0).id;
                    var idArr = id.split('_');
                    var key = idArr[1]+'_'+idArr[2];
                    var value = excel.worksheetData[key];
                    var color = value["color"];
                    if(!color || color == 'null' || color == '000000')
                    {
                        color = '';
                    }else{
                        color = '#'+color;
                    }
                    $('#excelString_'+value["column"]+'_'+value["string"]).css('background-color', color);
                    excel.restoreBorders(idArr[1], idArr[2]);
                });
                $('.ui-selected').removeClass('ui-selected');
                $('#'+value).addClass('ui-selected');
                $('#'+value).css('background','#ffffff');
                $('#'+value).css('border','2px solid #c2d9f5');
                
                //а вдруг выделяют область, не разрешаем
                if(cellId== -1){return;}
                var selectedArr = cellId.split('_');
                
                //начало, а вдруг нам ндо не добавить, а заменить
                var formula = $('.excel-nav-menu-text-formula').val();
            
                var formulaArr = formula.match(/(\w*\!*\w+\d+)/g);
                var lastElemnt = '';
                for (var i in formulaArr) {
                  lastElemnt = formulaArr[i];
                }
                
                if(formula.length == (formula.lastIndexOf(lastElemnt)+lastElemnt.length) )
                {
                    inputVal = formula.slice(0,formula.lastIndexOf(lastElemnt));

                }
                //конец
                
                var addVal = selectedArr[1]+selectedArr[2];
                
                $('.excel-nav-menu-text-formula').val(inputVal+addVal);
                this.changeInputFormula();
                $('.excel-cell-edit-text').focus();
                return;
            }
        }
        
        
        //предыдущее значение не было сохранено
        if( typeof($(".excel-cell-edit-text").parent().get(0)) !='undefined' ){
            var lastCellId = $('.excel-cell-edit-text').parent().get(0).id;
            this.saveEdit();
        }
        
        
        if(cellId == -1){
            excel.cellsSelected = [];
            $( ".ui-selected" ).each(function (i) {
                var curId = $(this).get(0).id;
                excel.cellsSelected.push(curId);
                $('#excelMainDivDrawing').hide();
                $(this).css('background','');
            });
            
            this.uiSelectedAddBorders('#c2d9f5');
            
            $('.excel-nav-menu-edit-image').hide();
        }else{
            $('.ui-selected').each(function(){
                    var id = $(this).get(0).id;
                    var idArr = id.split('_');
                    var key = idArr[1]+'_'+idArr[2];
                    var value = excel.worksheetData[key];
                    var color = value["color"];
                    if(!color || color == 'null' || color == '000000')
                    {
                        color = '';
                    }else{
                        color = '#'+color;
                    }
                    $('#excelString_'+value["column"]+'_'+value["string"]).css('background-color', color);
                    excel.restoreBorders(idArr[1], idArr[2]);
                });
            $('.ui-selected').removeClass('ui-selected');
            excel.cellsSelected = [];
            excel.cellsSelected.push(cellId);
            $('#'+cellId).addClass('ui-selected');
            $('#'+cellId).css('background','#ffffff');
            $('#'+cellId).css('border','2px solid #c2d9f5');
            
            var drawingOffsets = $('#'+cellId).offset();
            var cellIdArr = cellId.split('_');
            
            var dataKey = cellIdArr[1]+'_'+cellIdArr[2];
            var colspanTemp = 1;
            if(typeof( this.worksheetData[dataKey]) != 'undefined' && this.worksheetData[dataKey]['colspan']>1){
                colspanTemp = this.worksheetData[dataKey]['colspan']
            }
            
            $('#excelMainDivDrawing').css('left', (drawingOffsets.left+colspanTemp*(excel.tdWidthArr[cellIdArr[1]]+1)-5)+'px');
            $('#excelMainDivDrawing').css('top', (drawingOffsets.top+this.tdHeight-4)+'px');
            $('#excelMainDivDrawing').show();
            $( "#excelMainDivDrawing" ).draggable({
                helper: "clone"/*,
drag: function(event, ui) {
var dragOffset = $(this).offset();
excel.drawingProgress(dragOffset);
}*/
            });
            $( "#excelMainDivDrawing" ).bind( "drag", function(event, ui) {
                var left=ui.offset.left;
                var top=ui.offset.top;

                excel.drawingProgress(left, top);
            });
            
            $( "#excelMainDivDrawing" ).bind( "dragstop", function(event, ui) {
                excel.drawingStop();
            });
            
            $('.excel-nav-menu-edit-image').show();
        }
        
        //обновляем значение формулы, если редактирование произошло не через нее
        if(type=='text'){
            for (var key in excel.cellsSelected) {
                var value = excel.cellsSelected[key];
                var valueArr = value.split('_');
                var excelStringValue = '';
                var formula = '';
                var propDisabled = false;
                var dataKey = valueArr[1]+'_'+valueArr[2];
                if(typeof( this.worksheetData[dataKey]) != 'undefined'){
                    formula = excel.worksheetData[dataKey].formula;
                    excelStringValue = excel.worksheetData[dataKey].value;

                    if(formula==''){
                        formula = excel.worksheetData[dataKey].value;
                    }
                    if(excel.worksheetData[dataKey].read_only==1){
                        propDisabled = true;
                    }
                }

                $('.excel-nav-menu-text-label').val(valueArr[1]+valueArr[2]);
                $('.excel-nav-menu-text-formula').val(formula);
                excel.cellEditLastValue = formula;

                //обновляем значение формулы из строки редактирования
                if(typeof($(".excel-cell-edit-text").parent().get(0)) !='undefined' ){
                    this.changeInputInside();
                }

                excelStringValue = this.formattingStringValue(excelStringValue);
                if(excel.worksheetData[dataKey]["bold"] == '1')
                {
                    excelStringValue = '<b>'+excelStringValue+'</b>';
                }

                $('#'+cellId).html(excelStringValue);

                $(".excel-nav-menu-text-formula").prop('disabled', propDisabled);
                return;
            }
        }
        
    },
    drawingProgress: function(left, top)
    {
        $('.ui-drawing-progress').css('background','');
        $('.ui-drawing-progress').each(function(){
            var id = $(this).get(0).id;
            var idArr = id.split('_');
            var key = idArr[1]+'_'+idArr[2];
            var value = excel.worksheetData[key];
            var color = value["color"];
            if(!color || color == 'null' || color == '000000')
            {
                color = '';
            }else{
                color = '#'+color;
            }
            $('#excelString_'+value["column"]+'_'+value["string"]).css('background-color', color);
            excel.restoreBorders(idArr[1], idArr[2]);
        });
        $('.ui-drawing-progress').removeClass('ui-drawing-progress');
        var curC = 0;
        var curS = 0;
        var selectedC = '';
        var selectedS = '';
        
        while(curC<this.columns){
            curC++;
            var alphaColumn = php.num2alpha(curC-1);
            var curCellId = 'excelString_'+alphaColumn+'_'+(this.strings-1);
            var drawingOffsetsC = $('#'+curCellId).offset();
            if(drawingOffsetsC!='null' && selectedC == '' && drawingOffsetsC.left > left){
                selectedC = php.num2alpha(curC-2);
            }
            if(selectedC != ''){break;}
        }
        
        while(curS<this.strings){
            curS++;
            var curCellId = 'excelString_A_'+curS;
            var drawingOffsetsS = $('#'+curCellId).offset();
            if(drawingOffsetsS!=null && selectedS == '' && drawingOffsetsS.top > top){
                selectedS = curS-1;
            }
            if(selectedS != ''){break;}
        }
        var alphaColumn = php.num2alpha(curC-1);
        if(selectedC == ''){selectedC = alphaColumn;}
        if(selectedS == ''){selectedS = curS;}
        
        //возвращаемся к актуальной ячейке
        curS = curS-1;
        var curCellId = 'excelString_A_'+curS;
        var drawingOffsetsS = $('#'+curCellId).offset();
        curC = curC-1;
        var alphaColumn = php.num2alpha(curC-1);
        var curCellId = 'excelString_'+alphaColumn+'_1';
        var drawingOffsetsC = $('#'+curCellId).offset();

        //определяем, по строке или по столбцу надо протягивать
        for (var key in excel.cellsSelected) {
            var parentCellId = excel.cellsSelected[key];
            var parentCellIdArr = parentCellId.split('_');
            var parentOffsets = $('#'+parentCellId).offset();
        }

        var parentCellIdSimple = php.str_replace('excelString_', '', parentCellId);
        var parentCellIdSimpleColumnIndex = this.worksheetData[parentCellIdSimple]['columnIndex'];

        
        
        /*var diffC = (drawingOffsetsC.left-parentOffsets.left)/this.tdWidth;
var diffS = (drawingOffsetsS.top-parentOffsets.top)/this.tdHeight;*/
        var diffC = curC-parentCellIdSimpleColumnIndex;
        var diffS = curS-parentCellIdArr[2];
        
        if(diffC !=0 && Math.abs(diffC) > Math.abs(diffS)){
            //по строке
            var i = 0;
            while(curSelectedId != parentCellId)
            {
                var kC = diffC/Math.abs(diffC);
                var alphaColumn = php.num2alpha(curC-1);
                var curSelectedId = 'excelString_'+alphaColumn+'_'+parentCellIdArr[2];
                if(i==0){
                    var hLastOffset = -(120 - this.tdWidthArr[alphaColumn]);
                   $('#'+curSelectedId).css('background', '#ffffff');
                   $('#'+curSelectedId).css('border-top', '2px solid #c2d9f5');
                    $('#'+curSelectedId).css('border-left', '2px solid #ffffff');
                    $('#'+curSelectedId).css('border-right', '2px solid #c2d9f5');
                    $('#'+curSelectedId).css('border-bottom', '2px solid #c2d9f5');
                }else{
                    $('#'+curSelectedId).css('background', '#ffffff');
                    $('#'+curSelectedId).css('border-top', '2px solid #c2d9f5');
                    $('#'+curSelectedId).css('border-left', '2px solid #ffffff');
                    $('#'+curSelectedId).css('border-right', '2px solid #ffffff');
                    $('#'+curSelectedId).css('border-bottom', '2px solid #c2d9f5');
                }
                $('#'+curSelectedId).addClass('ui-drawing-progress');
                curC = curC-kC;
                i++;
            }
            $('#'+curSelectedId).css('background', '#ffffff');
            $('#'+curSelectedId).css('border-top', '2px solid #c2d9f5');
            $('#'+curSelectedId).css('border-left', '2px solid #c2d9f5');
            $('#'+curSelectedId).css('border-right', '2px solid #ffffff');
            $('#'+curSelectedId).css('border-bottom', '2px solid #c2d9f5');
            selectedS = parentCellIdArr[2];
        }else if(diffS != 0){
            //по столбцу
            var i = 0;
            while(curSelectedId != parentCellId)
            {
                var kS = diffS/Math.abs(diffS);
                var curSelectedId = 'excelString_'+parentCellIdArr[1]+'_'+curS;
                if(i==0){
                   var hLastOffset = -(36 - 18);
                   $('#'+curSelectedId).css('background', '#ffffff');
                   $('#'+curSelectedId).css('border-top', '2px solid #ffffff');
                    $('#'+curSelectedId).css('border-left', '2px solid #c2d9f5');
                    $('#'+curSelectedId).css('border-right', '2px solid #c2d9f5');
                    $('#'+curSelectedId).css('border-bottom', '2px solid #c2d9f5');
                }else{
                    $('#'+curSelectedId).css('background', '#ffffff');
                    $('#'+curSelectedId).css('border-top', '2px solid #ffffff');
                    $('#'+curSelectedId).css('border-left', '2px solid #c2d9f5');
                    $('#'+curSelectedId).css('border-right', '2px solid #c2d9f5');
                    $('#'+curSelectedId).css('border-bottom', '2px solid #ffffff');
                }
                $('#'+curSelectedId).addClass('ui-drawing-progress');
                curS = curS-kS;
                i++;
            }
            
            $('#'+curSelectedId).css('background', '#ffffff');
            $('#'+curSelectedId).css('border-top', '2px solid #c2d9f5');
            $('#'+curSelectedId).css('border-left', '2px solid #c2d9f5');
            $('#'+curSelectedId).css('border-right', '2px solid #c2d9f5');
            $('#'+curSelectedId).css('border-bottom', '2px solid #ffffff');
            selectedC = parentCellIdArr[1];
        }

        this.drawingCS = selectedC+selectedS;
    },
    restoreBorders:function(alphaColumn,string)
    {
        var dataKey = alphaColumn+'_'+string;
        //colspan
                            var colspan = 0;
                            if(typeof( this.worksheetData[dataKey]) != 'undefined'){
                                colspan = this.worksheetData[dataKey]['colspan'];
                            }
                            if(colspan<1){colspan=1;}
        //бордеры начало
                            var borderTop = this.worksheetData[dataKey]['borderTop'];
                            if(borderTop && borderTop != 'null' && borderTop>0)
                            {
                                if( borderTop==2){
                                    $('#excelString_'+dataKey).css('border-top', '1px solid #9FA5A7');
                                }
                                if( borderTop==1){
                                    $('#excelString_'+dataKey).css('border-top', '1px dashed #6E6D6D');
                                }
                            }else{
                                $('#excelString_'+dataKey).css('border-top','1px solid #CFCECE');
                            }

                            var borderBottom = this.worksheetData[dataKey]['borderBottom'];
                            if(borderBottom && borderBottom != 'null' && borderBottom>0)
                            {
                                if( borderBottom==2){
                                    $('#excelString_'+dataKey).css('border-bottom', '1px solid #9FA5A7');
                                }
                                if( borderBottom==1){
                                    $('#excelString_'+dataKey).css('border-bottom', '1px dashed #6E6D6D');
                                }
                            }else{
                                $('#excelString_'+dataKey).css('border-bottom','1px solid #CFCECE');
                            }

                            var borderLeft = this.worksheetData[dataKey]['borderLeft'];
                            if(borderLeft && borderLeft != 'null' && borderLeft>0)
                            {
                                if( borderLeft==2){
                                    $('#excelString_'+dataKey).css('border-left', '1px solid #9FA5A7');
                                }
                                if( borderLeft==1){
                                    $('#excelString_'+dataKey).css('border-left', '1px dashed #6E6D6D');
                                }
                            }else{
                                $('#excelString_'+dataKey).css('border-left','1px solid #CFCECE');
                            }
                            
                            
                            var borderRight = this.worksheetData[dataKey]['borderRight'];
                            
                            if(borderRight && borderRight != 'null' && borderRight>0)
                            {
                                if( borderRight==2){
                                    $('#excelString_'+dataKey).css('border-right', '1px solid #9FA5A7');
                                }
                                if( borderRight==1){
                                    $('#excelString_'+dataKey).css('border-right', '1px dashed #6E6D6D');
                                }
                            }else{
                                $('#excelString_'+dataKey).css('border-right','1px solid #CFCECE');
                            }
                            //бордеры конец
    },
    drawingStop: function()
    {
        $('.ui-drawing-progress').css('background','');
        $('.ui-drawing-progress').each(function(){
            var id = $(this).get(0).id;
            var idArr = id.split('_');
            var key = idArr[1]+'_'+idArr[2];
            var value = excel.worksheetData[key];
            var color = value["color"];
            if(!color || color == 'null' || color == '000000')
            {
                color = '';
            }else{
                color = '#'+color;
            }
            $('#excelString_'+value["column"]+'_'+value["string"]).css('background-color', color);
            excel.restoreBorders(idArr[1], idArr[2]);
        });
        $('.ui-drawing-progress').removeClass('ui-drawing-progress');
        //определяем, по строке или по столбцу надо протягивать
        for (var key in excel.cellsSelected) {
            var parentCellId = excel.cellsSelected[key];
            var parentCellIdArr = parentCellId.split('_');
            var column = parentCellIdArr[1];
            var string = parentCellIdArr[2];
        }
        
        var id = this.currentWorksheet;
        var target = this.drawingCS;
        
        if(this.drawingCS!=false){
            sender.excelApplyDrawing(id, string, column, target);
        }
        this.drawingCS = false;
    },
    keyboardKeyPress: function(e)
    {
        if(this.issetDiv == false){return;}
        if(this.cellEdited != false){return;}
        if(this.ctrlDown != false){return;}
        if (e.which == 32 || (65 <= e.which && e.which <= 65 + 25)
            || (97 <= e.which && e.which <= 97 + 25)
            || (e.which == 61) || (48 <= e.which && e.which <= 48 + 9)
            || (1073 <= e.which && e.which <= 1103)) {
            var c = String.fromCharCode(e.which);
            
            if(!this.keyboardKeyPressValue){
                this.keyboardKeyPressValue = c;
            }else{
                this.keyboardKeyPressValue += c;
            }
        }
    },
    keyboardKeyUp: function(e)
    {
        if(this.issetDiv == false){return;}
        if(this.cellEdited != false){return;}
        if(this.ctrlDown != false){return;}
        if(!this.keyboardKeyPressValue){return;}
        
        for (var key in excel.cellsSelected) {
            var value = excel.cellsSelected[key];

            this.cellEdit(value);
            $('.excel-cell-edit-text').val(this.keyboardKeyPressValue);
            this.keyboardKeyPressValue = false;
            this.changeInputInside();
            return;
        }
    },
    keyboardApplyCopy: function(){
        if(this.issetDiv == false){return;}
        this.applyCopy();
    },
    applyCopy: function(){
        this.worksheetCopy = this.currentWorksheet;
        this.cellsSelectedCopy = this.cellsSelected;
        this.uiSelectedAddBorders('#e1a148');
    },
    keyboardApplyPaste: function()
    {
        if(this.issetDiv == false){return;}
        this.applyPaste();
    },
    applyPaste: function(){
        //excelString_D_12
        var firstElementKey = '';
        var lastElementKey = '';
        
        
        for (var key in excel.cellsSelectedCopy) {
            var value = excel.cellsSelectedCopy[key];
            if(firstElementKey==''){
                firstElementKey = value;
            }
            lastElementKey = value;
        }
        
        var dataKeyFirstArr = firstElementKey.split('_');
        var dataKeyLastArr = lastElementKey.split('_');
        
        
        var id = excel.currentWorksheet;
        var fromId = excel.worksheetCopy;
        
        for (var key in excel.cellsSelected) {
            var value = excel.cellsSelected[key];
            var valueArr = value.split('_');
            
            var column = valueArr[1];
            var string = valueArr[2];
            
            sender.excelApplyPaste(id, fromId, string, column, dataKeyFirstArr[1]+dataKeyFirstArr[2]+':'+dataKeyLastArr[1]+dataKeyLastArr[2]);
            return;
        }
    },
    doScrollable: function()
        {
            $("#excelContentScrollbar").remove();
            $("#excelContentScrollbarH").remove();
            
            var topZindex = php.getTopZindexOf();
            
            var div = document.createElement('div');
              div.setAttribute('id', 'excelContentScrollbar');
              div.setAttribute('class', 'planner-book-scrollbar');
              div.style.position = "absolute";
              div.style.zIndex = (topZindex+1);
              document.body.appendChild(div);
              $('#excelContentScrollbar').css('top', (this.divTop+135)+'px');
              $('#excelContentScrollbar').css('left', (this.divLeft+839)+'px');
              $('#excelContentScrollbar').css('height', '410px');
              $('#excelContentScrollbar').css('width', '1px');
              $('#excelContentScrollbar').css('border', '0px');
            
            $("#excelContentScrollbar").slider({
                    orientation: "vertical",
                    min: 0,
                    max: (this.strings*this.tdHeight),
                    value: (this.strings*this.tdHeight),
                    slide: function(event, ui)
                    {
                        excel.scrollExcelContent(ui.value);
                    }

                });
                
               var div = document.createElement('div');
              div.setAttribute('id', 'excelContentScrollbarH');
              div.setAttribute('class', 'planner-book-scrollbar');
              div.style.position = "absolute";
              div.style.zIndex = (topZindex+1);
              document.body.appendChild(div);
              $('#excelContentScrollbarH').css('top', (this.divTop+580)+'px');
              $('#excelContentScrollbarH').css('left', (this.divLeft+570)+'px');
              $('#excelContentScrollbarH').css('height', '1px');
              $('#excelContentScrollbarH').css('width', '200px');
              $('#excelContentScrollbarH').css('border', '0px');
            
            $("#excelContentScrollbarH").slider({
                    orientation: "horizontal",
                    min: 0,
                    max: (this.tdWidthSumm),
                    value: 0,
                    slide: function(event, ui)
                    {
                        excel.scrollHExcelContent(ui.value);
                    }

                });
                
            $('#excelContentScrollbarH a').css('height', '20px');
            $('#excelContentScrollbarH a').css('width', '80px');
            $('#excelContentScrollbarH a').css('top', '-10px');
            $('#excelContentScrollbarH a').css('background', 'url("/img/planner/scroll-h.png") repeat scroll 0 0 transparent');
        },
        scrollExcelContent: function (value)
        {
            var scrollValue = (this.strings*this.tdHeight)-value;
            $("#excelContent").scrollTop(scrollValue);
        },
        scrollHExcelContent: function (value)
        {
            var scrollValue = value;
            $("#excelContent").scrollLeft(scrollValue);
        },
    /**
     * @param string exelDocumentUrl, 'html://zoho.com/something
     * check with initFronCache() HTML
     */
    getBaseHtml: function(excelDocumentUrl, documentId) {
       return '<section class="excel">' +
           '<header></header>' + 
           '</section>';           
    },
    getCloseHtml: function(documentId) {

        return '<div class="btn-close" style="right: -40px;"  onclick="excel.draw(' + documentId + ', \'close\');"><button></button></div>'

    },
    cancelEdit: function()
    {

        if(this.issetDiv == false){return;}
        this.cancelDblClick();
        $('.excel-nav-menu-text-formula').val(excel.cellEditLastValue);
    },
    keyboardSaveEdit: function()
    {
        if(this.issetDiv == false){return;}
        this.saveEdit();
        this.keyboardDown();
    },
    saveEdit: function() {},
    windowsSaveExcel : function() {

        $.ajax({
        data:{sid:session.getSid(), fileId:excel.curExcelID},   
        url: config.host.name+'index.php/ExcelDocument/GetExcelID',
        dataType:"json",
        success: function(data) {
            excel.curExcelID = (excel.curExcelID == null)?data.id:excel.curExcelID;
            //excel.curExcelTime = data.time;
            var iframe = document.getElementById("exelDocumentIframe-"+excel.curExcelID);
            if( iframe == undefined ){
               simulation.stop();  
            } else {
               //excel.draw(fileId, "save");
               if( excel.curExcelTime == data.time ) {
                   excel.showExcelMessage(); 
               } else {
                   
                   if( excel.curExcelTime == null ) {
                       excel.curExcelTime = data.time;
                       excel.showExcelMessage();
                   } else {
                       window.clearInterval(window.timer.saveExcelTimeout);
                       window.timer.saveExcelTimeout = null;
                       simulation.stop();
                   }
               }
            }
        }
        });
        
    },
    showExcelMessage:function(){
        var dialog = new SKDialogView({
                    'message':
                    'Пожалуйста, сохраните файл Сводный бюджет_02_v23.xls.<br/><br/>'+
                    'Для этого в окне Excel нажмите File->Save.<br/><br/>'+
                    'Спасибо!',
                    'buttons': [
                        {'value': 'Хорошо!', onclick: function() {window.timer.setSaveExcelTimeout();}}
                    ] });
         return dialog;       
    }
}