<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 24.02.13
 * Time: 13:16
 * To change this template use File | Settings | File Templates.
 */
$cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-1.8.3.min.js');
$cs->registerCssFile($assetsUrl . "/js/bootstrap/css/bootstrap.css");
$cs->registerScriptFile($assetsUrl . "/js/bootstrap/js/bootstrap.js");
$cs->registerScriptFile($assetsUrl . "/js/jquery/portamento.js");
?>
<!DOCTYPE html>
<html>
<head><title></title></head>
<body style="padding-top: 40px;" data-spy="scroll" data-target=".navbar">
<div class="container">
    <?php echo $content; ?>
</div>
</body>
</html>