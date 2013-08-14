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
            height: 40px;
        }
        .razdelitel+h3 {
            margin-bottom: 15px;
        }
    </style>

</head>
<body style="padding: 20px 100px 100px;">
<div class="grid-container">
    <div class="razdelitel"></div>
    <h3>Одна колонка с бордером</h3>
    <div class="container-border-1 block-border bg-yellow border-primary">
        <div class="pad-large">AAA</div>
    </div>

    <div class="razdelitel"></div>
    <h3>Две колонки с бордерами</h3>

    <div class="container-borders-2 thetable">
        <div class="block-border bg-yellow grid2 border-primary grid-cell">
            <div class="pad-large">AAA<br>AAA</div>
        </div>
        <div class="grid-cell grid-space"></div>
        <div class="block-border bg-yellow grid2 border-primary grid-cell">
            <div class="pad-large">BBB</div>
        </div>
    </div>
    <div class="razdelitel"></div>

    <h3>Три колонки с бордерами (1+2)</h3>
    <div class="container-borders-3">
        <div class="grid1">
            <div class="block-border bg-rich-blue border-primary">
                <div class="pad-large font-white">AAA</div>
            </div>
        </div>
        <div class="block-border bg-yellow grid2 border-primary">
            <div class="pad-large">BBB</div>
        </div>
    </div>

    <div class="razdelitel"></div>
    <h3>Три колонки с общим бордером (1+2)</h3>
    <div class="container-3 block-border border-primary bg-transparnt">
        <div class="border-primary bg-light-blue standard-left-box">
            <div class="pad-large">AAA</div>
        </div>
        <div class="border-primary bg-light-blue standard-right-box">
            <div class="pad-large">BBB</div>
        </div>
    </div>

    <div class="razdelitel"></div>
    <h3>Четыре колонки с бордерами</h3>
    <div class="container-borders-4">
        <div class="block-border bg-yellow grid1 border-primary"><div class="pad-large">AAA</div></div>
        <div class="block-border bg-yellow grid1 border-primary"><div class="pad-large">BBB</div></div>
        <div class="block-border bg-yellow grid1 border-primary"><div class="pad-large">CCC</div></div>
        <div class="block-border bg-yellow grid1 border-primary"><div class="pad-large">DDD</div></div>
    </div>

    <div class="razdelitel"></div>
    <h3>Две колонки</h3>

    <div class="container-2 font-xlarge">
        <div class="grid1 text-center font-white">
            Вам потребуется не более пяти минут.
        </div>
        <div class="grid1 text-center font-white">
            Кандидату потребуется 2-3 часа.
        </div>
    </div>

    <div class="razdelitel"></div>
    <h3>Три колонки</h3>

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
    <h3>Четыре колонки</h3>

    <div class="container-4">
        <div class="grid1">
            <h2 class="font-xlarge text-center">Уровень владения навыками</h2>
        </div>
        <div class="grid1">
            <h2 class="font-xlarge text-center">Уровень достижения результатов: количество и значимость выполненных задач</h2>
        </div>
        <div class="grid1">
            <h2 class="font-xlarge text-center">Скорость достижения результатов</h2>
        </div>
        <div class="grid1">
            <h2 class="font-xlarge text-center">Личностные качества, проявленные в симуляции</h2>
        </div>
    </div>

    <div class="razdelitel"></div>
    <div class="razdelitel"></div>

</div>

</body>
</html>
