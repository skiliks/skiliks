<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script>
function autoSave() {
    var button = window.parent.document.getElementById('savefile');
    button.click();
    console.log('Excel auto saved.');
}

setInterval(autoSave, 5*60*1000);

//console.log('save file element: ', window.parent.document.getElementById('savefile'));

var userAgent = navigator.userAgent;
var isOperaBrowser = (userAgent.indexOf("Opera")!=-1) ? true : false;
var isIEBrowser = (userAgent.toUpperCase().indexOf("IE") >= 0) ? true : false;

function _writeDynamicIframe(content, windowArgsInJson, documentArgsInJson) {
    document.open();
    if (windowArgsInJson) {
        for (var i in windowArgsInJson) {
            window[i] = windowArgsInJson[i];
        }
    }
    if (documentArgsInJson) {
        for (var i in documentArgsInJson) {
            document[i] = documentArgsInJson[i];
        }
    }
    document.write(content);
    document.close();
}

// new code to handle 500 Zoho {

$(window.parent.window).load(function()
{
    console.log('L');

    console.log($(window.parent.frames[0].document).find('iframe'));
    console.log($(window.parent.frames[0].attr('id')));
   // window.parent.parent.postMessage('DocumentLoaded-' + window.parent.location.href, 'http://live.skiliks.com');
    window.parent.parent.postMessage('DocumentLoaded-' + $(window.parent.frames[0]).attr('id'), 'http://live.skiliks.com');

    //send postMessage
    window.parent.showBannerMessage = function(_1416,msg,_1418,_1419,_141a){
        window.parent.parent.postMessage('Zoho_500', 'http://live.skiliks.com');

        if(typeof _1418!="undefined") {
            if(_1418) {
                window.parent.getObj("bannerCloseBtn").style.display="";
            } else {
                window.parent.getObj("bannerCloseBtn").style.display="none";
            }
        } else {
            if(_1416=="WARNING"||_1416=="INFO") {
                window.parent.getObj("bannerCloseBtn").style.display="";
            } else {
                window.parent.getObj("bannerCloseBtn").style.display="none";
            }
        }

        var _141b=window.parent.getObj("msgBannerPanel");
        _141b.style.height="100%";

        window.parent.getObj("bannerMessage").innerHTML=msg;

        var _141c=window.parent.getObj("msgBanner");
        var _141d=window.parent.getObj("bannerErrorIcon");
        var _141e=window.parent.getObj("bannerWarningIcon");
        var _141f=window.parent.getObj("bannerInfoIcon");

        _141d.style.display="none";
        _141e.style.display="none";
        _141f.style.display="none";

        if(_1416=="ERROR") {
            _141d.style.display="";
            _141c.className="error";
        } else {
            if(_1416=="WARNING") {
             _141e.style.display="";
                _141c.className="warning";
            } else {
                if(_1416=="INFO") {
                    _141f.style.display="";
                    _141c.className="info";
                }
            }
        }

        if(_141b.style.display=="none") {
            if(typeof _1419=="undefined"||_1419>0) {
                window.parent.blindDown("msgBannerPanel", window.parent.openErrorPanelCallBack,_1419);
            } else {
                _141b.style.display="";
            }
        } else {
            window.parent.fadeIn(_141c.id,50);
        }

        if(typeof _141a!="undefined") {
            _141a();
        }
    }

/*   Code to emulate Zoho 500 error in future
    setTimeout(function() {
        window.parent.showBannerMessage(
            'ERROR',
            'The server has encountered a problem. We are sorry! Kindly reopen the file and try again.');
            },
        30*1000);
    */
});
// new code }
</script>