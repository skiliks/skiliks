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

    <div class="razdelitel"></div>

    <div class="container-border-1 block-border bg-yellow border-primary">
        <div class="pad20">AAA</div>
    </div>

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

<div class="results-width-block block-border bg-lblue-primary border-primary">
    <div class="pad20">
        <h2 class="font-dark">Две колонки</h2>
        <div class="container-results-2 thetable">
            <div class="grid1 grid-cell block-border-dark border-large"><div class="pad20">AAA</div></div>
            <div class="grid-cell grid-space"></div>
            <div class="grid1 grid-cell block-border-dark border-large"><div class="pad20">AAA</div></div>
        </div>
        <div class="razdelitel"></div>

        <h2 class="font-dark">Три колонки (1 +2 )</h2>

        <div class="container-results-3">
            <div class="grid1">Следование приоритетам </div>
            <div class="grid2 bg-blue-block border-primary"><div class="pad16">BBB</div></div>
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
