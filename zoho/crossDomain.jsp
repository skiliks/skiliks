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
    //console.log('errorSK 1 up, ', errorAlert);
    function errorAlert(_1225,_1226,_1227,_1228){
    console.log('we hack it!');
    if(showErrorPanelMsg==_1226||_1228=="error"){
    if(_1225!=null&&_1225!=""){
    _1225=jsTitleMsg.Error+" "+_1225+": ";
    }
    handleServerError("ERROR",_1225+_1226,true);
    }else{
    if(_1227=="true"){
    showBannerMessage(_1228.toUpperCase(),_1226);
    }else{
    if(cookieEnabled){
    handleServerError("ERROR",jsMsg["ErrorPanel.MainContent"]+" "+jsTitleMsg.KindlyReopenFileAndTryAgain,true);
    }
    }
    }
    }
// new code }
</script>