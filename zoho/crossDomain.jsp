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
// can`t replicate 500

    window.newLoad = function() {
    console.log('errorSK 1 up, ', errorAlert);
    errorAlert = function(_1225, _1226, _1227, _1228) {
    console.log('errorSK 1');
    if("ShowErrorPanel" == _1226 || _1228 == "error") {
    if( _1225 != null && _1225 != "") {
    _1225 = window.jsTitleMsg.Error+" "+_1225+": ";
    }

    window.handleServerError("ERROR", _1225 + _1226, true);

    } else {
    if(_1227 == "true") {
    window.showBannerMessage(_1228.toUpperCase(), _1226);
    } else {
    if(cookieEnabled) {
    window.handleServerError("ERROR", window.jsMsg["ErrorPanel.MainContent"]
    + " " + window.jsTitleMsg.KindlyReopenFileAndTryAgain, true);
    }
    }
    }
    };

if(window.attachEvent) {
    window.attachEvent('onload', yourFunctionName);
} else {
    if(window.onload) {
        var currentOnLoad = window.onload;
        var newOnLoad = function() {
            currentOnLoad();
            setTimeout('window.newLoad();', 15*1000);
        };
        window.onload = newOnLoad;
    } else {
        window.onload = function() {
            setTimeout('window.newLoad();', 15*1000);
        };
    }
}

// new code }
</script>