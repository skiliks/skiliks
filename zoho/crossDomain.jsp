<script>
//console.log('Skiliks');
function autoSave(){
    $('#filesave').click();
    console.log('excel save');
    }
var timeout_id = setTimeout(autoSave, 300);
var userAgent = navigator.userAgent;
var isOperaBrowser = (userAgent.indexOf("Opera")!=-1) ? true : false;
var isIEBrowser = (userAgent.toUpperCase().indexOf("IE") >= 0) ? true : false;
if(!isOperaBrowser && !isIEBrowser) {
    document.domain = "skiliks.com";
    }
function _writeDynamicIframe(content, windowArgsInJson, documentArgsInJson){
    document.open();
    if(!isOperaBrowser && !isIEBrowser) {
    document.domain = "skiliks.com";
    }
if(windowArgsInJson)
		{
            for (var i in windowArgsInJson)
            {
            window[i] = windowArgsInJson[i];
            }
}
if(documentArgsInJson)
		{

            for (var i in documentArgsInJson)
            {
            document[i] = documentArgsInJson[i];
            }
}
document.write(content);
document.close();
}
</script>