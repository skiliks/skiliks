<?php

$cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();

$cs->scriptMap=array(
    'jquery.js'=>$assetsUrl . '/js/jquery/jquery-1.9.1.min.js',
    'jquery.min.js'=>$assetsUrl . '/js/jquery/jquery-1.9.1.min.js',
    //'jquery.ba-bbq.js'=>$assetsUrl . '/js/jquery/jquery.ba-bbq.js',
);

$cs->registerCoreScript('jquery');
$cs->registerScriptFile($assetsUrl . '/js/cufon-yui.js');
$cs->registerScriptFile($assetsUrl . '/js/ProximaNova.font.js');

$cs->registerCssFile($assetsUrl . "/css/static.css");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<style>

body {
    background:#7cb8c2;
}
.blockfortest, .testblocks div {
    display: inline-block;
    height:20px;
    margin: 0px 20px;
    width:30px;
}
.razdelitel {
    clear:both;
    height: 30px;
}
</style>

</head>
<body style="padding: 100px;">
<style>


</style>
<div class="grid-container">
    <div class="sign-block popup-site ui-dialog">
        <div class="ui-dialog-titlebar">
            <span class="ui-dialog-title">Заголовок</span>
            <a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button"><span class="ui-icon ui-icon-closethick">close</span></a>
        </div>
        <div class="ui-dialog-content ui-widget-content">
            Content
        </div>
    </div>


<div class="razdelitel"></div>

    <div class="popup-primary popup-site popup-center ui-dialog" style="top:380px;width:600px;margin-left: -300px;">
        <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
            <span class="ui-dialog-title" id="ui-dialog-title-simulation-details-pop-up">&nbsp;</span>
            <a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button"><span class="ui-icon ui-icon-closethick">close</span></a>
        </div>
        <div class="ui-dialog-content ui-widget-content">
            <div class="popup-primary-title">Заголовок</div>
            <p>Content</p>
        </div>
    </div>

<div class="razdelitel"></div>

    <div class="popup-simple popup-site popup-center ui-dialog"  style="top:560px;width:600px;margin-left: -300px;">
        <div class="ui-dialog-content ui-widget-content">
            <a class="popupclose"></a>
            <h3 class="font-dark text-center">Заголовок</h3>
            <div class="font-large">Content <a href="#" class="link-dark">link</a></div>
        </div>
    </div>

</div>

<div class="results-width-block bg-lblue-primary popup-center simulation-result-popup ui-dialog ui-widget" style="margin-left:-500px;top:720px;">
    <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
            <span class="ui-dialog-title" id="ui-dialog-title-simulation-details-pop-up">&nbsp;</span>
            <a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button"><span class="ui-icon ui-icon-closethick">close</span></a>
    </div>
    <div class="ui-dialog-content ui-widget-content">
        <h1 class="font-dark">Заголовок</h1>
        <p>Content</p>
    </div>
</div>

<div class="ui-widget-overlay"></div>
</body>

<script type="text/javascript">
    jQuery(document).ready(function(){
        Cufon.replace('.btn', {fontFamily:"ProximaNova-Bold", hover: true});
    });
</script>

</html>
