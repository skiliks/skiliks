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

    function errorAlert(_1225,_1226,_1227,_1228){
    alert('1');
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

    function showBannerMessage(_1416,msg,_1418,_1419,_141a){
    alert('2');
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

    function showConnectionLostError(_1420){
    alert('3');
    if(COLLAB_ID&&COLLAB_ID!=""){
    var msg;
    if((jsNEW_DOC_VIEW||jsSHARE_VIEW||jsOPEN_DOC_VIEW)&&(DOCUMENT_ID&&DOCUMENT_ID!=""&&DOCUMENT_ID!="doc_id")){
    msg=replaceParams(jsMsg["ErrorPanel.ConnectionLost"],new Array("<a onclick='javascript:loadDocument(\""+DOCUMENT_ID+"\", \"\", \"\", \"fsm\");' href='#'>","</a>"));
    }else{
    msg=replaceParams(jsMsg["ErrorPanel.ConnectionLost"],new Array("",""));
    }
    isRegistered=false;
    if(_1420){
    handleServerError("ERROR",msg,true,false);
    }else{
    handleServerError("ERROR","ConnectionLost",true,false);
    }
    }
    }

    //console.log("bannerMessage: ", document.getElementById("msgBannerPanel"));

    function handleServerError(_1409,msg,_140b,_140c){
    //console.log('handleServerError!!!');
    var _140d="";
    var _140e="";
    var _140f="";
    if(typeof msg=="undefined"){
    msg="";
    }
    message("","none");
    if(msg==showErrorPanelMsg){
    msg=jsMsg["ErrorPanel.MainContent"]+" ";
    if(lastSavedTime!=null&&DOCUMENT_ID!="doc_id"&&ALLOW_TO_WRITE){
    var _1410=returnTimeElaspsedinString(new Date(),lastSavedTime);
    if(_1410.unit.indexOf("second")!=-1){
    _140d=replaceParams(jsMsg["ErrorPanel.LastSavedInfo.LastSavedTimeinSeconds"],new Array(_1410.count));
    }else{
    if(_1410.unit=="minutes"){
    _140d=replaceParams(jsMsg["ErrorPanel.LastSavedInfo.LastSavedTimeinMinutes"],new Array(_1410.count));
    }else{
    if(_1410.unit=="hours"){
    _140d=replaceParams(jsMsg["ErrorPanel.LastSavedInfo.LastSavedTimeinHours"],new Array(_1410.count));
    }else{
    if(_1410.unit=="days"){
    _140d=replaceParams(jsMsg["ErrorPanel.LastSavedInfo.LastSavedTimeinDays"],new Array(_1410.count));
    }
    }
    }
    }
    _140d="<b>"+_140d+"</b>";
    }else{
    }
    if(DOCUMENT_ID!="doc_id"){
    if(jsREMOTE_SAVE_VIEW){
    _140e="<br>"+replaceParams(jsMsg["ErrorPanel.AdviseToBackupandReloadwithLink"],new Array("<a onclick='javascript:loadRemoteDocument();' href='#'>","</a>"));
    }else{
    if(INTERNAL_VIEW){
    _140e="<br>"+jsMsg["ErrorPanel.AdviseToBackupandReloadwithoutLink"];
    }else{
    if(jsPUBLIC_VIEW||jsGUEST_VIEW||jsTHROWAWAY_VIEW){
    _140e="<br>"+jsMsg["ErrorPanel.AdviseToBackupandReloadwithoutLink"];
    }else{
    if(ALLOW_TO_WRITE){
    _140e="<br>"+replaceParams(jsMsg["ErrorPanel.AdviseToBackupandReloadwithLink"],new Array("<a onclick='javascript:loadDocument(\""+DOCUMENT_ID+"\");' href='#'>","</a>"));
    }else{
    _140e="<br>"+replaceParams(jsMsg["ErrorPanel.AdviseToBackupandReloadwithLink"],new Array("<a onclick='javascript:loadDocument(\""+DOCUMENT_ID+"\");' href='#'>","</a>"));
    }
    }
    }
    }
    }else{
    if(!jsNEW_DOC_VIEW){
    _140e="<br>"+jsMsg["ErrorPanel.AdviseToBackupandReloadwithoutLink"];
    }else{
    _140e="<br>"+jsMsg["ErrorPanel.CreateNewDocumentandTryAgain"];
    }
    }
    }else{
    if(msg=="ConnectionLost"){
    msg=replaceParams(jsMsg["ErrorPanel.ServerConnectionLost"],"","");
    if(lastSavedTime!=null&&DOCUMENT_ID!="doc_id"&&ALLOW_TO_WRITE){
    var _1410=returnTimeElaspsedinString(new Date(),lastSavedTime);
    if(_1410.unit.indexOf("second")!=-1){
    _140d=replaceParams(jsMsg["ErrorPanel.LastSavedInfo.LastSavedTimeinSeconds"],new Array(_1410.count));
    }else{
    if(_1410.unit=="minutes"){
    _140d=replaceParams(jsMsg["ErrorPanel.LastSavedInfo.LastSavedTimeinMinutes"],new Array(_1410.count));
    }else{
    if(_1410.unit=="hours"){
    _140d=replaceParams(jsMsg["ErrorPanel.LastSavedInfo.LastSavedTimeinHours"],new Array(_1410.count));
    }else{
    if(_1410.unit=="days"){
    _140d=replaceParams(jsMsg["ErrorPanel.LastSavedInfo.LastSavedTimeinDays"],new Array(_1410.count));
    }
    }
    }
    }
    if((jsNEW_DOC_VIEW||jsSHARE_VIEW||jsOPEN_DOC_VIEW)&&(DOCUMENT_ID&&DOCUMENT_ID!=""&&DOCUMENT_ID!="doc_id")){
    _140f=replaceParams(jsMsg["ErrorPanel.reopen"],new Array("<a onclick='javascript:loadDocument(\""+DOCUMENT_ID+"\", \"\", \"\", \"fcl\");' href='#'>","</a>"));
    }else{
    _140f=replaceParams(jsMsg["ErrorPanel.reopen"],new Array("",""));
    }
    _140d=_140d+" "+_140f;
    }else{
    if((jsNEW_DOC_VIEW||jsSHARE_VIEW||jsOPEN_DOC_VIEW)&&(DOCUMENT_ID&&DOCUMENT_ID!=""&&DOCUMENT_ID!="doc_id")){
    msg=replaceParams(jsMsg["ErrorPanel.ConnectionLost"],new Array("<a onclick='javascript:loadDocument(\""+DOCUMENT_ID+"\", \"\", \"\", \"fcl\");' href='#'>","</a>"));
    }else{
    msg=replaceParams(jsMsg["ErrorPanel.ConnectionLost"],new Array("",""));
    }
    }
    }
    }
    msg=msg+_140d+_140e;
    if(msg!=""){
    btn_disabled();
    if(typeof _1409=="undefined"){
    _1409="ERROR";
    }
    showBannerMessage(_1409,msg);
    }
    if(_140b){
    READ_WRITE=false;
    }
    ALLOW_TO_WRITE=false;
    if(_1409=="ERROR"){
    isErrorDoc=true;
    if(document.getElementById("menutabs")){
    errorDocMenutab();
    }
    var _1411=getObj("addSheetBtn");
    if(_1411){
    _1411.style.visibility="hidden";
    }
    if(ole){
    ole.setObjectProperties("button",{permissions:{isDraggable:false,isResizable:false,isEditable:false}});
    }
    if(COLLAB_ID!=""&&COLLAB_ID!="null"){
    Collaboration.quit(COLLAB_ID);
    var _1412=COLLAB_ID;
    resetRTCvariables();
    if(jsSHARE_VIEW){
    COLLAB_ID=_1412;
    }
    }
    }
    if(typeof _140c!="undefined"){
    allowSheetSwitch=_140c;
    }else{
    allowSheetSwitch=false;
    }
    }

    function handleRequest1(rurl,_17e4,_17e5){
    console.log('handleRequest1!!!');
    var _17e6=this;
    var _17e7=new getXmlhttp();
    _17e7.onreadystatechange=function(){
    if(_17e7.readyState==4&&_17e7.status<300){
    var _17e8=_17e7.responseText;
    try{
    _17e6.callback(_17e8);
    }
    catch(e){
    openSheet(_17e8);
    }
    }
    };
    if(_17e5){
    handleForm(_17e7,rurl,_17e5);
    }else{
    _17e7.open("GET",rurl,true);
    _17e7.send(null);
    }
    return true;
    }

    $(window).load(function() {
        //console.log('body: ', $('body').html()); //(an empty string)
        //console.log('hid_imp_doc: ', $('#hid_imp_doc'));
        //console.log($('#main'));

        console.log('1: ', $('#msgBannerPanel'));

        $('body').unbind();
        $('body').bind(function(event){
        if (event.type == 'DOMNodeInserted') {
        //alert('Content added! Current content:' + '\n\n' + this.innerHTML);
            console.log($('#msgBannerPanel'));
        } else {
        //alert('Content removed! Current content:' + '\n\n' + this.innerHTML);
            console.log($('#msgBannerPanel'));
        }
        });
    });
// new code }
</script>