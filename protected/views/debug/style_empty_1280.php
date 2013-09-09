<?php
$cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();

$cs->scriptMap=array(
    'jquery.js'        => $assetsUrl . '/js/jquery/jquery-1.9.1.min.js',
    'jquery.min.js'    => $assetsUrl . '/js/jquery/jquery-1.9.1.min.js',
    //'jquery.ba-bbq.js' => $assetsUrl . '/js/jquery/jquery.ba-bbq.js',
);

$cs->registerCoreScript('jquery');
$cs->registerScriptFile($assetsUrl . '/js/cufon-yui.js');
$cs->registerScriptFile($assetsUrl . '/js/ProximaNova.font.js');

$cs->registerCssFile($assetsUrl . "/css/static.css");
?>

<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
    <div class="site-wrap" id="top">
        <header class="site-header grid-container">
            <h1><a href="./"><img src="<?php echo $assetsUrl?>/img/logo-head.png" alt="Skiliks"/></a></h1>
            <nav class="menu-site menu-top" id="static-page-links">
                <ul>
                    <li><a href="#">English</a></li><li><a href="#">Вход</a></li>
                </ul>
            </nav>
            <nav class="menu-site menu-main" id="account-links">
                <ul>
                    <li class="menu-link-active"><a href="/">Главная</a></li><li class="menu-link-regular"><a href="/static/team">О нас</a></li><li class="menu-link-regular"><a href="/static/team">О продукте</a></li><li class="menu-link-regular"><a href="#">Цены</a></li><li class="menu-link-regular"><a href="#">Цены и тарифы</a></li>
                </ul>
            </nav>
        </header>
        <div class="site-main grid-container">
            <h1 class="page-header">Самый простой и надежный способ проверить навыки менеджеров!</h1>
        </div>
        <div class="empty-footer"></div>
        <footer class="site-footer">
            <div class="grid-container">
                <div class="container-3 container">
                    <div class="grid1">
                        <a href="/" class="brand-footer"><img src="<?php echo $assetsUrl?>/img/skiliks-footer.png" alt="Skiliks" title="Skiliks"/></a>
                    </div>
                    <div class="grid2">
                        <nav class="menu-site menu-botm">
                            <ul>
                                <li class="menu-link-active"><a href="/">Главная</a></li><li class="menu-link-regular"><a href="/static/team">О нас</a></li><li class="menu-link-regular"><a href="/static/team">О продукте</a></li><li class="menu-link-regular"><a href="#">Цены и тарифы</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="container-3 container proxima-reg font-small">
                    <div class="grid1 empty-block">.</div><div class="grid1 text-center">Copyright - Skiliks  - 2012</div><div class="grid1 text-right">Свяжитесь с нами: <a href="mailto:help@skiliks.com">help@skiliks.com</a></div>
                </div>
                <a href="#top" class="to-top font-small">Наверх</a>
                <a href="#" class="btn btn-white btn-arrow-small access-footer">Получить бесплатный доступ</a>
                <div class="social_networks">
                    <span class="proxima-bold font-dark">Рекомендовать:</span><div class="addthis_toolbox addthis_default_style addthis_32x32_style" addthis:url="http://player.vimeo.com/video/61279471" addthis:title="Skiliks - game the skills" addthis:description="Самый простой и надежный способ проверить навыки менеджеров: деловая онлайн симуляция, имитирующая реальный рабочий день с типичными управленческими задачами и ситуациями принятия решений">
                        <a class="addthis_button_vk at300b" target="_blank" title="Vk" href="#"><span class=" at300bs at15nc at15t_vk"><span class="at_a11y"></span></span></a>
                        <a class="addthis_button_facebook at300b" title="Facebook" href="#"><span class=" at300bs at15nc at15t_facebook"><span class="at_a11y"></span></span></a>
                        <a class="addthis_button_twitter at300b" title="Tweet" href="#"><span class=" at300bs at15nc at15t_twitter"><span class="at_a11y"></span></span></a>
                        <a class="addthis_button_google_plusone_share at300b" g:plusone:count="false" href="http://www.addthis.com/bookmark.php?v=300&amp;winname=addthis&amp;pub=ra-5158c9c22198d938&amp;source=tbx32-300&amp;lng=en-US&amp;s=google_plusone_share&amp;url=http%3A%2F%2Fplayer.vimeo.com%2Fvideo%2F61279471&amp;title=Skiliks%20-%20game%20the%20skills&amp;ate=AT-ra-5158c9c22198d938/-/-/51c092cc6369ed49/2&amp;frommenu=1&amp;uid=51c092cc3f68d12d&amp;description=%D0%A1%D0%B0%D0%BC%D1%8B%D0%B9%20%D0%BF%D1%80%D0%BE%D1%81%D1%82%D0%BE%D0%B9%20%D0%B8%20%D0%BD%D0%B0%D0%B4%D0%B5%D0%B6%D0%BD%D1%8B%D0%B9%20%D1%81%D0%BF%D0%BE%D1%81%D0%BE%D0%B1%20%D0%BF%D1%80%D0%BE%D0%B2%D0%B5%D1%80%D0%B8%D1%82%D1%8C%20%D0%BD%D0%B0%D0%B2%D1%8B%D0%BA%D0%B8%20%D0%BC%D0%B5%D0%BD%D0%B5%D0%B4%D0%B6%D0%B5%D1%80%D0%BE%D0%B2%3A%20%D0%B4%D0%B5%D0%BB%D0%BE%D0%B2%D0%B0%D1%8F%20%D0%BE%D0%BD%D0%BB%D0%B0%D0%B9%D0%BD%20%D1%81%D0%B8%D0%BC%D1%83%D0%BB%D1%8F%D1%86%D0%B8%D1%8F%2C%20%D0%B8%D0%BC%D0%B8%D1%82%D0%B8%D1%80%D1%83%D1%8E%D1%89%D0%B0%D1%8F%20%D1%80%D0%B5%D0%B0%D0%BB%D1%8C%D0%BD%D1%8B%D0%B9%20%D1%80%D0%B0%D0%B1%D0%BE%D1%87%D0%B8%D0%B9%20%D0%B4%D0%B5%D0%BD%D1%8C%20%D1%81%20%D1%82%D0%B8%D0%BF%D0%B8%D1%87%D0%BD%D1%8B%D0%BC%D0%B8%20%D1%83%D0%BF%D1%80%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%BC%D0%B8%20%D0%B7%D0%B0%D0%B4%D0%B0%D1%87%D0%B0%D0%BC%D0%B8%20%D0%B8%20%D1%81%D0%B8%D1%82%D1%83%D0%B0%D1%86%D0%B8%D1%8F%D0%BC%D0%B8%20%D0%BF%D1%80%D0%B8%D0%BD%D1%8F%D1%82%D0%B8%D1%8F%20%D1%80%D0%B5%D1%88%D0%B5%D0%BD%D0%B8%D0%B9&amp;ct=1&amp;pre=http%3A%2F%2Flive.skiliks.com%2F&amp;tt=0&amp;captcha_provider=nucaptcha" target="_blank" title="Google+"><span class=" at300bs at15nc at15t_google_plusone_share"><span class="at_a11y"></span></span></a>
                        <a class="addthis_button_linkedin at300b" href="http://www.addthis.com/bookmark.php?v=300&amp;winname=addthis&amp;pub=ra-5158c9c22198d938&amp;source=tbx32-300&amp;lng=en-US&amp;s=linkedin&amp;url=http%3A%2F%2Fplayer.vimeo.com%2Fvideo%2F61279471&amp;title=Skiliks%20-%20game%20the%20skills&amp;ate=AT-ra-5158c9c22198d938/-/-/51c092cc6369ed49/3&amp;frommenu=1&amp;uid=51c092ccbe7e2162&amp;description=%D0%A1%D0%B0%D0%BC%D1%8B%D0%B9%20%D0%BF%D1%80%D0%BE%D1%81%D1%82%D0%BE%D0%B9%20%D0%B8%20%D0%BD%D0%B0%D0%B4%D0%B5%D0%B6%D0%BD%D1%8B%D0%B9%20%D1%81%D0%BF%D0%BE%D1%81%D0%BE%D0%B1%20%D0%BF%D1%80%D0%BE%D0%B2%D0%B5%D1%80%D0%B8%D1%82%D1%8C%20%D0%BD%D0%B0%D0%B2%D1%8B%D0%BA%D0%B8%20%D0%BC%D0%B5%D0%BD%D0%B5%D0%B4%D0%B6%D0%B5%D1%80%D0%BE%D0%B2%3A%20%D0%B4%D0%B5%D0%BB%D0%BE%D0%B2%D0%B0%D1%8F%20%D0%BE%D0%BD%D0%BB%D0%B0%D0%B9%D0%BD%20%D1%81%D0%B8%D0%BC%D1%83%D0%BB%D1%8F%D1%86%D0%B8%D1%8F%2C%20%D0%B8%D0%BC%D0%B8%D1%82%D0%B8%D1%80%D1%83%D1%8E%D1%89%D0%B0%D1%8F%20%D1%80%D0%B5%D0%B0%D0%BB%D1%8C%D0%BD%D1%8B%D0%B9%20%D1%80%D0%B0%D0%B1%D0%BE%D1%87%D0%B8%D0%B9%20%D0%B4%D0%B5%D0%BD%D1%8C%20%D1%81%20%D1%82%D0%B8%D0%BF%D0%B8%D1%87%D0%BD%D1%8B%D0%BC%D0%B8%20%D1%83%D0%BF%D1%80%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%BC%D0%B8%20%D0%B7%D0%B0%D0%B4%D0%B0%D1%87%D0%B0%D0%BC%D0%B8%20%D0%B8%20%D1%81%D0%B8%D1%82%D1%83%D0%B0%D1%86%D0%B8%D1%8F%D0%BC%D0%B8%20%D0%BF%D1%80%D0%B8%D0%BD%D1%8F%D1%82%D0%B8%D1%8F%20%D1%80%D0%B5%D1%88%D0%B5%D0%BD%D0%B8%D0%B9&amp;ct=1&amp;pre=http%3A%2F%2Flive.skiliks.com%2F&amp;tt=0&amp;captcha_provider=nucaptcha" target="_blank" title="Linkedin"><span class=" at300bs at15nc at15t_linkedin"><span class="at_a11y"></span></span></a>
                    </div>
                </div><!-- /social_networks -->
            </div><!-- /grid-container -->
        </footer>
    </div><!-- /site-wrap -->
</body>
<script type="text/javascript">
    jQuery(document).ready(function(){
        Cufon.replace('li, p, label, input, select, .proxima-reg, .to-top', {fontFamily:"ProximaNova-Regular", hover: true});
        Cufon.replace('.btn, .proxima-bold, h1, h2, h3, h4, h5, .dark-labels label, .list-dark li', {fontFamily:"ProximaNova-Bold", hover: true});
        Cufon.replace('.semi', {fontFamily:"Conv_ProximaNova-Semibold", hover: true});
    });
</script>
</html>
