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

    <div class="razdelitel"></div>

    <div class="container-border-1 block-border bg-yellow border-primary block-order">
        <div class="order-header"></div>
        <div class="order-content"><h3 class="font-brown">Content</h3></div>
    </div>

    <div class="razdelitel"></div>

    <div class="container-border-1 popup-simple popup-site popup-center ui-dialog ui-widget">
        <div class="ui-dialog-content ui-widget-content">
            Content for Change Password
        </div>
    </div>

    <div class="razdelitel"></div><div class="razdelitel" style="height:80px;"></div>

    <div class="container-borders-3">
        <div class="block-border bg-rich-blue grid1 border-primary">
            <div class="pad20 font-white"><h3>Заголовок</h3></div>
        </div>
        <div class="block-border grid2 border-primary dashboard">
            <table class="table-primary">
                <thead>
                    <tr>
                        <th>Title 1</th>
                        <th>Title 2</th>
                        <th>Title 3</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Content 1</td>
                        <td>Content 2</td>
                        <td>Content 3</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="razdelitel"></div>

    <div class="container-borders-2">
        <div class="block-border bg-yellow grid2 border-primary">
            <div class="pad20">
                <h2 class="font-brown">Заголовок</h2>
                <div>Content</div>
            </div>
        </div>
        <div class="block-border bg-yellow grid2 border-primary">
            <div class="pad20">
                <h2 class="font-brown">Заголовок</h2>
                <div>Content</div>
            </div>
        </div>
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

</div>
</body>
</html>
