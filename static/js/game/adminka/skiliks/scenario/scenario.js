scenario = {
    draw: function (){
        var html = '';
        html += this.defaultHtml;
        
        var uploadUrl = config.host.name+'index.php/scenario/upload';
        html = php.str_replace('{url}', uploadUrl, html);
        
        world.draw(html);
        
        menuMain.setActive('scenario');
        
    },
    startUpload: function()
    {
        document.getElementById('scenarioForm').style.display = 'none';
        document.getElementById('f1_upload_process').style.display = 'block';
        return true;
    },
    stopUpload: function(success, rowCount)
    {
        alert('called');
        var result = '';
        if (success == 1){
        document.getElementById('result').innerHTML =
        '<span class="msg">The file was uploaded successfully!<\/span><br/><br/>';
        }
        else {
        document.getElementById('result').innerHTML =
        '<span class="emsg">There was an error during file upload!<\/span><br/><br/>';
        }
        document.getElementById('f1_upload_process').style.display = 'none';
        document.getElementById('scenarioForm').style.display = 'block';
        
        if(success){
            var message = 'Загрузка прошла успешно, было добавлено '+rowCount+' записей';
            var lang_alert_title = 'Личный кабинет';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-success');
        }else{
            var message = 'Загрузка была прервана.<br>Если вы видите это сообщение впервые - попробуйте еще раз.<br>При повторении этой ошибки обратитесь к вашему системному администратору.';
            var lang_alert_title = 'Личный кабинет';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }
        
        return true; 
    },
    defaultHtml:'<div id="scenarioUploader">'+
                '</div>'+
                '<div id="scenarioUploaderSwf">'+

                '<p id="f1_upload_process" style="display:none;">Подождите пожалуйста, идет загрузка и обработка данных...<br/><img src="img/design/ajax-loader(3).gif" /></p>'+
                '<p id="result"></p>'+
                '<form action="{url}" method="post" enctype="multipart/form-data" target="upload_target" onsubmit="scenario.startUpload();" id="scenarioForm">'+
                'File: <input name="myfile" type="file" />'+
                '<input type="submit" name="submitBtn" value="Upload" style="margin-left: 100px;" class="btn btn-success" />'+
                '</form>'+
                '<iframe id="upload_target" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>'+
                
                '<input id="scenarioSuccess" type="hidden" />'+
                '<input id="scenarioRowCount" type="hidden" />'+
                '<input id="scenarioStopUpload" type="button" onclick="scenario.stopUpload(document.getElementById(\'scenarioSuccess\').value, document.getElementById(\'scenarioRowCount\').value);" style="display:none;">'+
                
                '</div>'
}