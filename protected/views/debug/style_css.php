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
<div>
    <a href="#" class="btn btn-primary">Get access</a>
</div>
</body>
</html>
