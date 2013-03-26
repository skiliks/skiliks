$(document).ready(function(){

    /*$('a.feedback').click(function(){

        if($('#JotFormIFrame').length === 0) {
            $('#top.container').append('<iframe id="JotFormIFrame" onload="window.parent.scrollTo(0,0)" allowtransparency="true" src="http://form.jotformeu.com/form/30835043655352" frameborder="0" style="width:100%; height:513px; border:none;" scrolling="no"></iframe>');
            $('#top.container').append('<script type="text/javascript">window.handleIFrameMessage = function(e) {var args = e.data.split(":");var iframe = document.getElementById("JotFormIFrame");if (!iframe)return;switch (args[0]) {case "scrollIntoView":iframe.scrollIntoView();break;case "setHeight":iframe.style.height = args[1] + "px";break;}};if (window.addEventListener) {window.addEventListener("message", handleIFrameMessage, false);} else if (window.attachEvent) {window.attachEvent("onmessage", handleIFrameMessage);}</script>');
        }else{
            $("#JotFormIFrame").show("slow");
        }

        return false;
    });

    $(document).click(function(event) {
        if ($(event.target).closest("#JotFormIFrame").length) return;
        $("#JotFormIFrame").hide("slow");
        event.stopPropagation();
    });*/
    $('a.feedback').click(function(){
        $('#JotFormIFrame').dialog({ width: 750});
        return false;
    });

});