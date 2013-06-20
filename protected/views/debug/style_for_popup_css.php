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
    <div class="razdelitel"></div>
    <div class="container-borders-2">
        <div class="block-border bg-yellow grid2 border-primary">
            <div class="pad20">AAA</div>
        </div>
        <div class="block-border bg-yellow grid2 border-primary">
            <div class="pad20">BBB</div>
        </div>
    </div>
    <div class="razdelitel"></div>

    <div class="container-borders-3">
        <div class="block-border bg-rich-blue grid1 border-primary">
            <div class="pad20 font-white">AAA</div>
        </div>
        <div class="block-border bg-yellow grid2 border-primary">
            <div class="pad20">BBB</div>
        </div>
    </div>

    <div class="razdelitel"></div>

    <div class="container-3 block-border border-primary bg-transparnt">
        <div class="border-primary bg-light-blue standard-left-box">
            <div class="pad20">AAA</div>
        </div>
        <div class="border-primary bg-light-blue standard-right-box">
            <div class="pad20">BBB</div>
        </div>
    </div>

    <div class="razdelitel"></div>

    <div class="container-borders-4">
        <div class="block-border bg-yellow grid1 border-primary"><div class="pad20">AAA</div></div>
        <div class="block-border bg-yellow grid1 border-primary"><div class="pad20">BBB</div></div>
        <div class="block-border bg-yellow grid1 border-primary"><div class="pad20">CCC</div></div>
        <div class="block-border bg-yellow grid1 border-primary"><div class="pad20">DDD</div></div>
    </div>

    <div class="razdelitel"></div>

    <div class="container-3 font-xlarge">
        <div class="grid1 text-center font-white">
            Вам потребуется не более пяти минут.
        </div>
        <div class="grid1 text-center font-white">
            Кандидату потребуется 2-3 часа.
        </div>
        <div class="grid1 text-center font-white">
            Вы сможете сравнить между собой кандидатов.
        </div>
    </div>

    <div class="razdelitel"></div>

</div>
</body>

<script type="text/javascript">
    jQuery(document).ready(function(){
        Cufon.replace('.btn', {fontFamily:"ProximaNova-Bold", hover: true});
    });
</script>

</html>
