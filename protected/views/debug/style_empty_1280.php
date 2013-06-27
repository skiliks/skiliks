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
<head>
<style>
body {
    background:#7cb8c2;
}
</style>

</head>
<body>
    <div class="site-wrap">
        <header class="site-header grid-container">
            <h1><a href="./"><img src="<?php echo $assetsUrl?>/img/logo-head.png" alt="Skiliks"/></a></h1>
            <nav class="menu-site menu-top" id="static-page-links">
                <ul>
                    <li><a href="#">English</a></li><li><a href="#">Вход</a></li>
                </ul>
            </nav>
            <nav class="menu-site menu-main" id="account-links">
                <ul>
                    <li class="menu-link-active"><a href="/">Главная</a></li><li class="menu-link-regular"><a href="/static/team">О нас</a></li><li class="menu-link-regular"><a href="#">Цены</a></li>
                </ul>
            </nav>
        </header>
        <div class="site-main grid-container">
            <p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text1</p>
            <p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text2</p>
            <p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text3</p>
            <p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text4</p>
            <p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text</p><p>Text5</p>
        </div>
        <div class="empty-footer"></div>
        <footer class="site-footer">
            <div class="grid-container">
                <div class="container-3">
                    <div class="grid1">
                        <a href="/" class="brand-footer"><img src="<?php echo $assetsUrl?>/img/skiliks-footer.png" alt="Skiliks" title="Skiliks"/></a>
                    </div>
                    <div class="grid2">!!!</div>
                </div>
                <div class="container-3">
                    <div class="grid1"></div>
                    <div class="grid1 text-center">Copyright - Skiliks - 2012</div>
                    <div class="grid1">Свяжитесь </div>
            </div>
        </footer>
    </div><!-- /site-wrap -->
</body>
<script type="text/javascript">
    jQuery(document).ready(function(){
        Cufon.replace('li, p, label, input, select', {fontFamily:"ProximaNova-Regular", hover: true});
        Cufon.replace('.btn, .proxima-bold, h1, h2, h3, h4, h5, .dark-labels label, .list-dark li', {fontFamily:"ProximaNova-Bold", hover: true});
        Cufon.replace('.semi', {fontFamily:"Conv_ProximaNova-Semibold", hover: true});
    });
</script>
</html>
