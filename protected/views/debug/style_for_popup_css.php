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
<div class="grid-container">

    <div class="block-border bg-rich-blue sign-in-pop-up-width">
        <div class="pad20">
            <div class="ui-dialog-titlebar font-white proxima-bold">Вход</div>
        </div>
    </div>


</div>

</body>

<script type="text/javascript">
    jQuery(document).ready(function(){
        Cufon.replace('.btn', {fontFamily:"ProximaNova-Bold", hover: true});
    });
</script>

</html>
