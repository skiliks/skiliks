<?php

$cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();
$cs->registerCssFile($assetsUrl . "/css/styles_new.css");
?>

<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body style="padding: 100px">
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
    <li class="previous hidden"><a href="/dashboard/corporate"><cufon class="cufon cufon-canvas" alt="Назад" style="width: 42px; height: 14px;"><canvas width="51" height="15" style="width: 51px; height: 15px; top: -1px; left: -1px;"></canvas><cufontext>Назад</cufontext></cufon></a></li>
    <li class="page selected"><a href="/dashboard/corporate"><cufon class="cufon cufon-canvas" alt="1" style="width: 6px; height: 14px;"><canvas width="18" height="15" style="width: 18px; height: 15px; top: -1px; left: -1px;"></canvas><cufontext>1</cufontext></cufon></a></li>
    <li class="page"><a href="/dashboard/corporate?page=2"><cufon class="cufon cufon-canvas" alt="2" style="width: 9px; height: 14px;"><canvas width="18" height="15" style="width: 18px; height: 15px; top: -1px; left: -1px;"></canvas><cufontext>2</cufontext></cufon></a></li>
    <li class="next"><a href="/dashboard/corporate?page=2"><cufon class="cufon cufon-canvas" alt="Вперед" style="width: 51px; height: 14px;"><canvas width="60" height="15" style="width: 60px; height: 15px; top: -1px; left: -1px;"></canvas><cufontext>Вперед</cufontext></cufon></a></li>
    <li class="last"><a href="/dashboard/corporate?page=2">конец &gt;&gt;</a></li></ul>
</body>
</html>
