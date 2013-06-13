<?php

$cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();
$cs->registerCssFile($assetsUrl . "/css/styles_new.css");
?>

<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body style="padding: 100px;background:#7cb8c2">
<div>
    <a href="#" class="btn btn-large">Get access</a>
</div>
<p>&nbsp;</p><p>&nbsp;</p>
<div>
    <a href="#" class="btn btn-primary">Get access</a>
</div>
<p>&nbsp;</p><p>&nbsp;</p>
<div>
    <a href="#" class="btn btn-site">Get access</a>
</div>
<p>&nbsp;</p><p>&nbsp;</p>
<ul id="yw1" class="yiiPager"><li class="first hidden"><a href="/dashboard/corporate">&lt;&lt; начало</a></li>
    <li class="previous hidden"><a href="/dashboard/corporate">Назад</a></li>
    <li class="page selected"><a href="/dashboard/corporate">1</a></li>
    <li class="page"><a href="/dashboard/corporate?page=2">1</a></li>
    <li class="next"><a href="/dashboard/corporate?page=2">Вперед</a></li>
    <li class="last"><a href="/dashboard/corporate?page=2">конец &gt;&gt;</a></li></ul>
</body>
</html>
