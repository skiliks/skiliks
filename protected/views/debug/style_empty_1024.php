<?php
$cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();

$cs->scriptMap=array(
    'jquery.js'        => $assetsUrl . '/js/jquery/jquery-1.9.1.min.js',
    'jquery.min.js'    => $assetsUrl . '/js/jquery/jquery-1.9.1.min.js',
    'jquery.ba-bbq.js' => $assetsUrl . '/js/jquery/jquery.ba-bbq.js',
);

$cs->registerCoreScript('jquery');
$cs->registerScriptFile($assetsUrl . '/js/cufon-yui.js');
$cs->registerScriptFile($assetsUrl . '/js/ProximaNova.font.js');

$cs->registerCssFile($assetsUrl . "/css/styles_new.css");
$cs->registerCssFile($assetsUrl . "/css/styles_site_custom.css");
?>

<!DOCTYPE html>
<html lang="en">
<body>

</body>
<script type="text/javascript">
    jQuery(document).ready(function(){
        Cufon.replace('li, p, label, input, select', {fontFamily:"ProximaNova-Regular", hover: true});
        Cufon.replace('.btn, .proxima-bold, h1, h2, h3, h4, h5, .dark-labels label, .list-dark li', {fontFamily:"ProximaNova-Bold", hover: true});
        Cufon.replace('.semi', {fontFamily:"Conv_ProximaNova-Semibold", hover: true});
    });
</script>
</html>
