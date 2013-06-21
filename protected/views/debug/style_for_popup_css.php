<?php

$cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();

$cs->scriptMap=array(
    'jquery.js'=>$assetsUrl . '/js/jquery/jquery-1.9.1.min.js',
    'jquery.min.js'=>$assetsUrl . '/js/jquery/jquery-1.9.1.min.js',
    'jquery.ba-bbq.js'=>$assetsUrl . '/js/jquery/jquery.ba-bbq.js',
);

$cs->registerCoreScript('jquery');
$cs->registerScriptFile($assetsUrl . '/js/cufon-yui.js');
$cs->registerScriptFile($assetsUrl . '/js/ProximaNova.font.js');

$cs->registerCssFile($assetsUrl . "/css/styles_new.css");
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
    <div class="sign-block ui-dialog">
        <div class="ui-dialog-titlebar">
            <span class="ui-dialog-title">Заголовок</span>
            <a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button"><span class="ui-icon ui-icon-closethick">close</span></a>
        </div>
        <div class="ui-dialog-content ui-widget-content">
            Content
        </div>
    </div>


<div class="razdelitel"></div>

    <div class="ui-dialog popup-primary" style="top:400px;">
        <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
            <span class="ui-dialog-title" id="ui-dialog-title-simulation-details-pop-up">&nbsp;</span>
            <a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button"><span class="ui-icon ui-icon-closethick">close</span></a>
        </div>
        <div class="ui-dialog-content ui-widget-content">
            Content
        </div>
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
