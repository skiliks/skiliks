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
<body>
<header class="site-header">
    <h1><a href="./"><img src="<?php echo $assetsUrl?>/img/logo-head.png" alt="Skiliks"/></a></h1>
    <nav class="menu-site menu-top" id="static-page-links">
        <ul>
            <li><a href="#">English</a></li>
            <li><a href="#">Вход</a></li>
        </ul>
    </nav>
    <nav class="menu-site menu-main">
        <ul>
            <li class="menu-link-active"><a href="/">Главная</a></li>
            <li class="menu-link-regular"><a href="/static/team">О нас</a></li>
            <li class="menu-link-regular"><a href="#">Цены</a></li>
        </ul>
    </nav>
</header>

<div style="padding: 0px 100px 100px;">
<div>
    <a href="#" class="btn btn-large">Get access</a>
</div>

<div class="razdelitel"></div>

<div>
    <a href="#" class="btn btn-primary">Get access</a>
</div>

<div class="razdelitel"></div>

<div>
    <a href="#" class="btn btn-site">Get access</a>
</div>

<div class="razdelitel"></div>

<ul id="yw1" class="yiiPager"><li class="first hidden"><a href="/dashboard/corporate">&lt;&lt; начало</a></li>
    <li class="previous hidden"><a href="/dashboard/corporate">Назад</a></li>
    <li class="page selected"><a href="/dashboard/corporate">1</a></li>
    <li class="page"><a href="/dashboard/corporate?page=2">2</a></li>
    <li class="next"><a href="/dashboard/corporate?page=2">Вперед</a></li>
    <li class="last"><a href="/dashboard/corporate?page=2">конец &gt;&gt;</a></li>
</ul>

<div class="razdelitel"></div>

    <h1 style="width:780px">Самый простой и надёжный способ проверить навыки менеджера</h1>
    <h1 class="font-blue-dark">Самый простой</h1>
    <h2 class="font-dark">Результативность</h2>
    <h3 class="font-brown">Индивидуальный профиль</h3>


<div class="razdelitel"></div>
    <div class="testblocks">
        <div class="block-border border-primary"></div>
        <div class="bg-rich-blue block-border"></div>
        <div class="bg-light-blue border-primary""></div>
        <div class="bg-blue border-primary""></div>
        <div class="bg-yellow border-primary""></div>
        <div class="bg-yellow-light border-large""></div>
    </div>

<div class="razdelitel"></div>

<div class="razdelitel"></div><div class="razdelitel"></div>
<a href="#">Link</a>&nbsp;&nbsp;&nbsp;<a href="#" class="link-dark">Link Dark</a>
<div class="razdelitel"></div>

<div id="invite-people-box" class="block-border bg-rich-blue border-large pad30 pull-left">
    <h3>Отправить приглашение</h3>
    <form id="invite-form" class="form-simple form-small placehldrs-dark">
        <div class="row "><label for="Invite_full_name">Имя</label><input placeholder="Имя" name="Invite[firstname]" id="Invite_firstname" type="text" maxlength="100"><input placeholder="Фамилия" name="Invite[lastname]" id="Invite_lastname" type="text" maxlength="100"></div>
        <div class="row "><label for="Invite_email" class="required">Email</label><input placeholder="Введите e-mail" name="Invite[email]" id="Invite_email" type="text" maxlength="255"></div>

        <div class="row wide postn-reltv">
            <label for="Invite_vacancy_id" class="required">Вакансия</span></label><div id="sbHolder_26848379" class="sbHolder">
                <a id="sbToggle_26848379" href="#" class="sbToggle"></a>
                <a id="sbSelector_26848379" href="#" class="sbSelector">aaa</a>
                <ul id="sbOptions_26848379" class="sbOptions" style="display: none;">
                    <li><a href="#" rel="4" class="sbFocus">Текст 1</a></li>
                    <li><a href="#" rel="4" class="sbFocus">Текст 2</a></li>
                    <li><a href="#" rel="4" class="sbFocus">Текст 3</a></li>
                    <li><a href="#" rel="4" class="sbFocus">Текст 4</a></li>
                </ul>
            </div>
        </div>
        <div class="row wide postn-reltv">
            <label for="Invite_vacancy_id" class="required">Вакансия</span></label><div id="sbHolder_26848379" class="sbHolder">
                <a id="sbToggle_26848379" href="#" class="sbToggle"></a>
                <a id="sbSelector_26848379" href="#" class="sbSelector">selector</a>
                <ul id="sbOptions_26848379" class="sbOptions" style="display: none;">
                    <li><a href="#" rel="4" class="sbFocus">Текст 1</a></li>
                    <li><a href="#" rel="4" class="sbFocus">Текст 2</a></li>
                    <li><a href="#" rel="4" class="sbFocus">Текст 3</a></li>
                    <li><a href="#" rel="4" class="sbFocus">Текст 4</a></li>
                </ul>
            </div>
        </div>
        <div class="row buttons"><input name="prevalidate" type="submit" class="btn btn-primary" value="Отправить"></div>

    </form>
</div>

<div class="razdelitel"></div>

    <div class="registrationform" style="float: left">
        <form id="yum-user-registration-form" class="form-simple form-large">
            <div class="block-border bg-transparnt rows-inline">
                <div class="row"><input placeholder="Email" name="YumProfile[email]" id="YumProfile_email" type="text" value=""></div><div class="row"><input placeholder="Введите пароль" name="YumUser[password]" id="YumUser_password" type="password"></div><div class="row"><input placeholder="Подтвердите пароль" name="YumUser[password_again]" id="YumUser_password_again" type="password"></div><div class="row"><input type="submit" name="yt0" value="Начать" class="btn-large font-xxlarge"></div>
            </div>
        </form>
    </div>

</div>
</body>

<script type="text/javascript">
    jQuery(document).ready(function(){
        Cufon.replace('.btn', {fontFamily:"ProximaNova-Bold", hover: true});
    });
</script>

</html>
