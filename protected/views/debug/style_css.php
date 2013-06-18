<?php

$cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();
$cs->registerCoreScript('jquery');
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
    height: 30px;
}
</style>
</head>
<body style="padding: 100px;">
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

    <h2 style="width:780px">Самый простой и надёжный способ проверить навыки менеджера</h2>

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
<nav class="menu-main">
    <ul>
        <li class="active"><a href="/">Главная</a></li>
        <li><a href="/static/team">О нас</a></li>
        <li><a href="#">Цены</a></li>
    </ul>
</nav>
<div class="razdelitel"></div><div class="razdelitel"></div>
<div class="block-border bg-rich-blue border-large pad30">
<form id="invite-form" class="form-simple">
    <div class="row ">
        <label for="Invite_full_name">Имя</label>
        <input placeholder="Имя" name="Invite[firstname]" id="Invite_firstname" type="text" maxlength="100"><input placeholder="Фамилия" name="Invite[lastname]" id="Invite_lastname" type="text" maxlength="100"></div>

    <div class="row ">
        <label for="Invite_email" class="required">Email</label>
        <input placeholder="Введите e-mail" name="Invite[email]" id="Invite_email" type="text" maxlength="255">
    </div>

    <div class="row wide">
        <label for="Invite_vacancy_id" class="required">Вакансия</span></label>
        <div id="sbHolder_26848379" class="sbHolder">
            <a id="sbToggle_26848379" href="#" class="sbToggle"></a>
            <a id="sbSelector_26848379" href="#" class="sbSelector" style="border:1px solid red">aaa</a>
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
</body>
</html>
