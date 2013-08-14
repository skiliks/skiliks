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
<div class="grid-container">
    <div class="container-border-1 block-border bg-yellow border-primary">
        <div class="pad-xsize">
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
        <div class="grid1">
            <div class="block-border bg-rich-blue border-primary">
                <div class="pad-large font-white"><h3>Заголовок</h3></div>
            </div>
        </div>
        <div class="block-border grid2 border-primary dashboard">
            <table class="table-primary">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="razdelitel"></div><div class="razdelitel"></div>

    <div class="container-borders-2 thetable">
        <div class="block-border bg-yellow grid2 border-primary grid-cell">
            <div class="pad-large">
                <h2 class="font-brown">Заголовок</h2>
                <div>Content</div>
            </div>
        </div>
        <div class="grid-cell grid-space"></div>
        <div class="block-border bg-yellow grid2 border-primary grid-cell">
            <div class="pad-large">
                <h2 class="font-brown">Заголовок</h2>
                <div>Content</div>
            </div>
        </div>
    </div>

    <div class="razdelitel"></div><div class="razdelitel"></div>

    <div class="container-borders-2 thetable">
        <div class="block-border bg-yellow grid2 border-primary grid-cell">
            <div class="highlight-title font-white">Заголовок</div>
            <div class="pad-xxsize">Content</div>
        </div>
        <div class="grid-cell grid-space"></div>
        <div class="block-border bg-yellow grid2 border-primary grid-cell">
            <div class="highlight-title font-white">Заголовок</div>
            <div class="pad-xxsize">Content</div>
        </div>
    </div>

    <div class="razdelitel"></div><div class="razdelitel"></div>

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
