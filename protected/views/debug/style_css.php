<?php

$cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();
$cs->registerCoreScript('jquery');
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
    <li class="page"><a href="/dashboard/corporate?page=2">2</a></li>
    <li class="next"><a href="/dashboard/corporate?page=2">Вперед</a></li>
    <li class="last"><a href="/dashboard/corporate?page=2">конец &gt;&gt;</a></li>
</ul>
<p>&nbsp;</p><p>&nbsp;</p>
<h2 style="width:780px">Самый простой и надёжный способ проверить навыки менеджера</h2>
<p>&nbsp;</p><p>&nbsp;</p>
<nav class="menu-main">
    <ul>
        <li class="active"><a href="/">Главная</a></li>
        <li><a href="/static/team">О нас</a></li>
        <li><a href="#">Цены</a></li>
    </ul>
</nav>
<p>&nbsp;</p><p>&nbsp;</p>
<form id="invite-form" class="form-simple">
    <span class="form-global-errors">
            </span>

    <div class="row ">
        <label for="Invite_full_name"><cufon class="cufon cufon-canvas" alt="Имя" style="width: 28px; height: 14px;"><canvas width="37" height="15" style="width: 37px; height: 15px; top: -1px; left: -1px;"></canvas><cufontext>Имя</cufontext></cufon></label>        <input placeholder="Имя" name="Invite[firstname]" id="Invite_firstname" type="text" maxlength="100">                <input placeholder="Фамилия" name="Invite[lastname]" id="Invite_lastname" type="text" maxlength="100">            </div>

    <div class="row ">
        <label for="Invite_email" class="required"><cufon class="cufon cufon-canvas" alt="Email " style="width: 39px; height: 14px;"><canvas width="51" height="15" style="width: 51px; height: 15px; top: -1px; left: -1px;"></canvas><cufontext>Email </cufontext></cufon><span class="required"><cufon class="cufon cufon-canvas" alt="*" style="width: 5px; height: 14px;"><canvas width="17" height="15" style="width: 17px; height: 15px; top: -1px; left: -1px;"></canvas><cufontext>*</cufontext></cufon></span></label>        <input placeholder="Введите e-mail" name="Invite[email]" id="Invite_email" type="text" maxlength="255">            </div>

    <div class="row wide  v">
        <label for="Invite_vacancy_id" class="required"><cufon class="cufon cufon-canvas" alt="Вакансия " style="width: 68px; height: 14px;"><canvas width="80" height="15" style="width: 80px; height: 15px; top: -1px; left: -1px;"></canvas><cufontext>Вакансия </cufontext></cufon><span class="required"><cufon class="cufon cufon-canvas" alt="*" style="width: 5px; height: 14px;"><canvas width="17" height="15" style="width: 17px; height: 15px; top: -1px; left: -1px;"></canvas><cufontext>*</cufontext></cufon></span></label>        <select name="Invite[vacancy_id]" id="Invite_vacancy_id" sb="26848379" style="display: none;">
            <option value="4">ieuieu</option>
            <option value="41">Длинное ну очень длинное такое вот название вакансии</option>
            <option value="42">Analitic</option>
            <option value="44">Директор</option>
            <option value="45">Научный сотрудник</option>
            <option value="68">рои</option>
        </select><div id="sbHolder_26848379" class="sbHolder"><a id="sbToggle_26848379" href="#" class="sbToggle"></a><a id="sbSelector_26848379" href="#" class="sbSelector"><cufon class="cufon cufon-canvas" alt="ieuieu" style="width: 37px; height: 13px;"><canvas width="45" height="14" style="width: 45px; height: 14px; top: -1px; left: -1px;"></canvas><cufontext>ieuieu</cufontext></cufon></a><ul id="sbOptions_26848379" class="sbOptions" style="display: none;"><li><a href="#4" rel="4" class="sbFocus"><cufon class="cufon cufon-canvas" alt="ieuieu" style="width: 37px; height: 13px;"><canvas width="45" height="14" style="width: 45px; height: 14px; top: -1px; left: -1px;"></canvas><cufontext>ieuieu</cufontext></cufon></a></li><li><a href="#41" rel="41"><cufon class="cufon cufon-canvas" alt="Длинное " style="width: 59px; height: 13px;"><canvas width="71" height="14" style="width: 71px; height: 14px; top: -1px; left: -1px;"></canvas><cufontext>Длинное </cufontext></cufon><cufon class="cufon cufon-canvas" alt="ну " style="width: 18px; height: 13px;"><canvas width="30" height="14" style="width: 30px; height: 14px; top: -1px; left: -1px;"></canvas><cufontext>ну </cufontext></cufon><cufon class="cufon cufon-canvas" alt="очень " style="width: 41px; height: 13px;"><canvas width="53" height="14" style="width: 53px; height: 14px; top: -1px; left: -1px;"></canvas><cufontext>очень </cufontext></cufon><cufon class="cufon cufon-canvas" alt="длинное " style="width: 57px; height: 13px;"><canvas width="69" height="14" style="width: 69px; height: 14px; top: -1px; left: -1px;"></canvas><cufontext>длинное </cufontext></cufon><cufon class="cufon cufon-canvas" alt="такое " style="width: 39px; height: 13px;"><canvas width="51" height="14" style="width: 51px; height: 14px; top: -1px; left: -1px;"></canvas><cufontext>такое </cufontext></cufon><cufon class="cufon cufon-canvas" alt="вот " style="width: 25px; height: 13px;"><canvas width="36" height="14" style="width: 36px; height: 14px; top: -1px; left: -1px;"></canvas><cufontext>вот </cufontext></cufon><cufon class="cufon cufon-canvas" alt="название " style="width: 63px; height: 13px;"><canvas width="75" height="14" style="width: 75px; height: 14px; top: -1px; left: -1px;"></canvas><cufontext>название </cufontext></cufon><cufon class="cufon cufon-canvas" alt="вакансии" style="width: 59px; height: 13px;"><canvas width="66" height="14" style="width: 66px; height: 14px; top: -1px; left: -1px;"></canvas><cufontext>вакансии</cufontext></cufon></a></li><li><a href="#42" rel="42"><cufon class="cufon cufon-canvas" alt="Analitic" style="width: 44px; height: 13px;"><canvas width="53" height="14" style="width: 53px; height: 14px; top: -1px; left: -1px;"></canvas><cufontext>Analitic</cufontext></cufon></a></li><li><a href="#44" rel="44"><cufon class="cufon cufon-canvas" alt="Директор" style="width: 61px; height: 13px;"><canvas width="69" height="14" style="width: 69px; height: 14px; top: -1px; left: -1px;"></canvas><cufontext>Директор</cufontext></cufon></a></li><li><a href="#45" rel="45"><cufon class="cufon cufon-canvas" alt="Научный " style="width: 60px; height: 13px;"><canvas width="71" height="14" style="width: 71px; height: 14px; top: -1px; left: -1px;"></canvas><cufontext>Научный </cufontext></cufon><cufon class="cufon cufon-canvas" alt="сотрудник" style="width: 65px; height: 13px;"><canvas width="73" height="14" style="width: 73px; height: 14px; top: -1px; left: -1px;"></canvas><cufontext>сотрудник</cufontext></cufon></a></li><li><a href="#68" rel="68"><cufon class="cufon cufon-canvas" alt="рои" style="width: 24px; height: 13px;"><canvas width="31" height="14" style="width: 31px; height: 14px; top: -1px; left: -1px;"></canvas><cufontext>рои</cufontext></cufon></a></li></ul></div>                <span id="corporate-dashboard-add-vacancy"></span>
    </div>

    <div class="row buttons">
        <input name="prevalidate" type="submit" value="Отправить">    </div>

</form>
</body>
</html>
