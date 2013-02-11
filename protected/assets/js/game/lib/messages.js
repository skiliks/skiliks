/*
 * пример использования
 * messages.dw_confirm('Если нажать ОК то будет просто алерт', function(){
 *      messages.dw_alert('Просто алерт'); 
 *   });
 */

messages = {
    messageFrameName: 'message',
    lang_alert_title_default: 'Сообщение',
    lang_confirmed_default: 'Ознакомлен',
    lang_cancel_default: 'Отмена',
    lang_ok_default: 'OK',
    add_style: '', /* alert-error alert-success*/
    
    draw: function(message,lang_alert_title,lang_confirmed)
    {
        var messageFrame = document.getElementById(this.messageFrameName);
        //alert(message);
        this.dw_alert(message);
    },
    dw_alert: function (message,lang_alert_title,lang_confirmed, add_style)
    {
        if(!lang_alert_title){lang_alert_title=this.lang_alert_title_default;}
        if(!lang_confirmed){lang_confirmed=this.lang_confirmed_default;}
        if(!add_style){add_style=this.add_style;}
        
        var messageFrame = document.getElementById(this.messageFrameName);
        
        message = message.replace(/\[img([^\]]+)\]/ig, '<img$1>');
        
        var innerText = this.dw_alertHTML;
        innerText = php.str_replace('{message}', message, innerText);
        innerText = php.str_replace('{lang_alert_title}', lang_alert_title, innerText);
        innerText = php.str_replace('{lang_confirmed}', lang_confirmed, innerText);
        innerText = php.str_replace('{add_style}', add_style, innerText);
        
        messageFrame.innerHTML = innerText;
        messageFrame.style.display='block';
    },
    closeHint: function ()
    {
        var messageFrame = document.getElementById(this.messageFrameName);
        messageFrame.style.display = 'none';
    },
    dw_confirm: function (message, callback, lang_alert_title,lang_ok, lang_cancel)
    {
        if(!lang_alert_title){lang_alert_title=this.lang_alert_title_default;}
        if(!lang_ok){lang_ok=this.lang_ok_default;}
        if(!lang_cancel){lang_cancel=this.lang_cancel_default;}
        
        var messageFrame = document.getElementById(this.messageFrameName);
        messageFrame.style.top=Math.abs((config.activeFrame.height)/2);
        messageFrame.style.left = Math.abs((config.activeFrame.width)/2);
        
        message = message.replace(/\[img([^\]]+)\]/ig, '<img$1>');
        innerText = '<TABLE cellSpacing=0 cellPadding=0 border=0 style="background-image:url(img/messages/background.gif);">\n<tr><td>\n <TABLE cellSpacing=0 cellPadding=0 border=0 style="text-align: left;background-image:url(img/messages/bg_title.gif);FONT-WEIGHT: bold;FONT-SIZE: 10pt;COLOR: #222222;FONT-FAMILY: Verdana, Arial, Helvetica, Tahoma, sans-serif;" width=100%>\n  <tr><td align=left nowrap>'+lang_alert_title+'</td><td width="10" nowrap></td><td align=right><B onClick="messages.closeHint();" style="cursor:pointer;FONT-WEIGHT: bold;FONT-SIZE: 10.5pt; color: red">X&nbsp;</B></td></tr>\n </TABLE>\n</td></tr>\n<tr><td>\n <TABLE cellSpacing=0 cellPadding=2 border=0 width=100%>\n  <tr><td align=left nowrap style="FONT-SIZE: 8.5pt;color: black;FONT-FAMILY: Verdana, Arial, Helvetica, Tahoma, sans-serif;" width=300 nowrap>'+message+'</td></tr>\n </TABLE>\n</td></tr>\n<tr><td height=5></td></tr>\n<tr><td align=right bgcolor="#F2D1B0">\n <TABLE cellSpacing=0 cellPadding=0 border=0>\n  <tr><td style="text-align: center;background-image:url(img/messages/background_2.gif);cursor:pointer;FONT-WEIGHT: bold;FONT-SIZE: 8pt;COLOR: #333333;FONT-FAMILY: Verdana, Arial, Helvetica, Tahoma, sans-serif;" onmouseover="this.style.background=\'#ddd url(img/messages/background.gif)\'" onmouseout="this.style.background=\'#ddd url(img/messages/background_2.gif)\'" id="pm_ok">&nbsp;&nbsp;'+lang_ok+'&nbsp;&nbsp;</td>\n';
        innerText += '<td style="text-align: center;background-image:url(img/messages/background_2.gif);cursor:pointer;FONT-WEIGHT: bold;FONT-SIZE: 8pt;COLOR: #333333;FONT-FAMILY: Verdana, Arial, Helvetica, Tahoma, sans-serif;" onmouseover="this.style.background=\'#ddd url(img/messages/background.gif)\'" onmouseout="this.style.background=\'#ddd url(img/messages/background_2.gif)\'" onclick="messages.closeHint();">&nbsp;&nbsp;'+lang_cancel+'&nbsp;&nbsp;</td></tr>\n<tr><td height=3> </TABLE>\n</td></tr>\n</TABLE>\n';
        innerText += '';
        messageFrame.innerHTML = innerText;
        messageFrame.style.display='block';
        document.getElementById('pm_ok').onclick = function() {messages.closeHint();callback();}
    },
    dw_alertHTML:   '<div class="alert alert-block {add_style} fade in">'+
                    '<button class="close" data-dismiss="alert" type="button" onclick="messages.closeHint();">×</button>'+
                    '<h4 class="alert-heading">{lang_alert_title}</h4>'+
                    '<p>{message}</p>'+
                    '<p>'+
                    '<a class="btn" href="#" onclick="messages.closeHint();">{lang_confirmed}</a>'+
                    '</p>'+
                    '</div>',
    showCustomSystemMessage: function(message)
    {
        if(typeof(message)=='undefined'){
            return;
        }
        
        var topZindex = php.getTopZindexOf();
        
        var div = document.createElement('div');
          div.setAttribute('id', 'messageSystemMessageDiv');
          div.style.position = "absolute";
          div.style.zIndex = topZindex+1;
          document.body.appendChild(div);
          
          var offsets = {
              top:50,
              left:150+75
          };
          
          $('#messageSystemMessageDiv').css('top', (offsets.top+20)+'px');
          $('#messageSystemMessageDiv').css('left',  offsets.left+'px');
          
          /////////////////////////////////////////////
         $('#messageSystemMessageDiv').html(message);
         
         //закрывалка
         var div = document.createElement('div');
          div.setAttribute('id', 'messageSystemMessageDivClose');
          div.style.position = "absolute";
          div.style.zIndex = topZindex;
          document.body.appendChild(div);
          
          $('#messageSystemMessageDivClose').css('top', '0px');
          $('#messageSystemMessageDivClose').css('left',  '0px');
          $('#messageSystemMessageDivClose').css('width',  '100%');
          $('#messageSystemMessageDivClose').css('height',  '100%');
         
         $('#messageSystemMessageDivClose').click(function() {
            messages.hideCustomSystemMessage();
        });
        
    },
    disableCloseByClick: function() {
        $('#messageSystemMessageDivClose').unbind();
    },
    hideCustomSystemMessage: function()
    {
        $('#messageSystemMessageDivClose').remove();
        $('#messageSystemMessageDiv').remove();
    }
}