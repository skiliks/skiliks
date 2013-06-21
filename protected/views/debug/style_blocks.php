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
    <div class="container-border-1 block-border bg-yellow border-primary">
        <div class="pad30">
           <h2 class="font-brown">Заголовок</h2>
           <ul class="unstyled font-brown font-xlarge">
               <li>Content</li>
               <li>Content</li>
           </ul>
        </div>
    </div>
</div>
</body>
</html>
