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
   window.parent.parent.postMessage({type: 'DocumentLoaded', url:window.parent.location.href} , '*');

   window.parent.isExportEvent = true;

    console.log('new code 1');
   //send postMessage
   window.parent.showBannerMessage = function(_1416,msg,_1418,_1419,_141a) {
       console.log('showBannerMessage');

       window.parent.parent.postMessage({type:'Zoho_500', url:window.parent.location.href}, '*');
       console.log('message Zoho_500');

       if(typeof _141a!="undefined") {
           _141a();
       }
    }

//   Code to emulate Zoho 500 error in future
    /*setTimeout(function() {
        window.parent.showBannerMessage(
            'ERROR',
            'The server has encountered a problem. We are sorry! Kindly reopen the file and try again.');
            },
        25*1000);*/

});
// new code }
</script>