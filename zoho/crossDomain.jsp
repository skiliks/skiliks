<script>
//console.log('Skiliks');
function autoSave() {
    //var btn = document.getElementById('savefile');
    //btn.click();
    console.log(document.getElementById('savefile'));
    //$('#filesave').click();
    console.log('excel save');
    }

console.log(document.getElementById('savefile'));
    console.log(document);
    console.log(window);
    console.log(window.parent);
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