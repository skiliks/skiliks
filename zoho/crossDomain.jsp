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
    console.log('errorSK 1 up, ', errorAlert);
    function errorAlert(_1225,_1226,_1227,_1228){
    console.log('we hack it 1!');
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
    console.log('errorSK 2 up, ', errorAlert);

    console.log('errorSK 1-1 up, ', showBannerMessage);
    function showBannerMessage(_1416,msg,_1418,_1419,_141a){
    console.log('we hack it 2!');
    if(typeof _1418!="undefined"){
    if(_1418){
    getObj("bannerCloseBtn").style.display="";
    }else{
    getObj("bannerCloseBtn").style.display="none";
    }
    }else{
    if(_1416=="WARNING"||_1416=="INFO"){
    getObj("bannerCloseBtn").style.display="";
    }else{
    getObj("bannerCloseBtn").style.display="none";
    }
    }
    var _141b=getObj("msgBannerPanel");
    _141b.style.height="100%";
    getObj("bannerMessage").innerHTML=msg;
    var _141c=getObj("msgBanner");
    var _141d=getObj("bannerErrorIcon");
    var _141e=getObj("bannerWarningIcon");
    var _141f=getObj("bannerInfoIcon");
    _141d.style.display="none";
    _141e.style.display="none";
    _141f.style.display="none";
    if(_1416=="ERROR"){
    _141d.style.display="";
    _141c.className="error";
    }else{
    if(_1416=="WARNING"){
    _141e.style.display="";
    _141c.className="warning";
    }else{
    if(_1416=="INFO"){
    _141f.style.display="";
    _141c.className="info";
    }
    }
    }
    if(_141b.style.display=="none"){
    if(typeof _1419=="undefined"||_1419>0){
    blindDown("msgBannerPanel",openErrorPanelCallBack,_1419);
    }else{
    _141b.style.display="";
    }
    }else{
    fadeIn(_141c.id,50);
    }
    if(typeof _141a!="undefined"){
    _141a();
    }
    }
    console.log('errorSK 1-2 up, ', showBannerMessage);
// new code }
</script>