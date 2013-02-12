function initSwfUpload(params) {
    
    function uploadSuccess(file, serverData) {
        $('#images').append($(serverData));
    }
    
    function uploadComplete(file) {
        $('#status').append($('<p>Загрузка ' + file.name + ' завершена</p>'));
    }
    
    function uploadStart(file) {
        $('#status').append($('<p>Начата загрузка файла ' + file.name + '</p>'));
        return true;
    }
    
    function uploadProgress(file, bytesLoaded, bytesTotal) {
        $('#status').append($('<p>Загружено ' + Math.round(bytesLoaded/bytesTotal*100) + '% файла ' + file.name + '</p>'));
    }

    function fileDialogComplete(numFilesSelected, numFilesQueued) {
        $('#status').html($('<p>Выбрано ' + numFilesSelected + ' файл(ов), начинаем загрузку</p>'));
        this.startUpload();
    }

    var swfParams = {
            //upload_url : "upload.php",
            upload_url : params.uploadUrl,
            flash_url : "js/swfupload/swfupload.swf",
            button_placeholder_id : "uploadButton",
            
            file_size_limit : "2 MB",
            //file_types : "*.jpg; *.png; *.jpeg; *.gif",
            file_types : "*.csv",
            file_types_description : "Images",
            file_upload_limit : "0",
            debug: false,

            button_image_url: params.uploadButton,
            button_width : 100,
            button_height : 30,
            button_text_left_padding: 15,
            button_text_top_padding: 2, 
            button_text : "<span class=\"uploadBtn\">Обзор...</span>",
            button_text_style : ".uploadBtn { font-size: 18px; font-family: Arial; background-color: #FF0000; }",
            
            file_dialog_complete_handler : fileDialogComplete,

            //upload_complete_handler : uploadComplete,
            upload_start_handler : uploadStart,
            upload_progress_handler : uploadProgress
        }
    if (params.uploadSuccess) {
        swfParams.upload_success_handler = params.uploadSuccess;
    }
    else {
        swfParams.upload_success_handler = uploadSuccess;
    }
    if (params.uploadComplete) {
        swfParams.upload_success_handler = params.uploadComplete;
    }
    else {
        swfParams.upload_complete_handler = uploadComplete;
    }
    /*if (params.uploadStart) {
        swfParams.upload_start_handler = params.uploadStart;
    }
    else {
        swfParams.upload_start_handler = uploadStart;
    }*/
    
    swfu = new SWFUpload(swfParams);
};
