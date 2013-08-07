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


<div class="results-width-block bg-lblue-primary simulation-result-popup">
    <div class="ui-widget-content">
        <h2 class="font-dark">Две колонки</h2>
        <div class="container-results-2 thetable">
            <div class="grid1 grid-cell block-border-dark border-large"><div class="pad-large">AAA</div></div>
            <div class="grid-cell grid-space"></div>
            <div class="grid1 grid-cell block-border-dark border-large"><div class="pad-large">AAA</div></div>
        </div>
        <div class="razdelitel"></div>

        <h2 class="font-dark">Три колонки (1 +2 )</h2>

        <div class="container-results-3">
            <div class="grid1">Следование приоритетам </div>
            <div class="grid2 bg-blue-block border-primary"><div class="pad-midle">BBB</div></div>
        </div>
        <div class="razdelitel"></div>

        <h2 class="font-dark">Четыре колонки</h2>

        <div class="container-results-4">
            <div class="grid1">
                <h2 class="font-xlarge text-center font-dark">Уровень владения навыками</h2>
            </div>
            <div class="grid1">
                <h2 class="font-xlarge text-center font-dark">Уровень достижения результатов: количество и значимость выполненных задач</h2>
            </div>
            <div class="grid1">
                <h2 class="font-xlarge text-center font-dark">Скорость достижения результатов</h2>
            </div>
            <div class="grid1">
                <h2 class="font-xlarge text-center font-dark">Личностные качества, проявленные в симуляции</h2>
            </div>
        </div>
        <div class="razdelitel"></div>
    </div>
</div>

</body>
</html>
