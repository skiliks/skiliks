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
<div>
    <h1><a href="./"><img src="<?php echo $assetsUrl?>/img/logo-head.png" alt="Skiliks"/></a></h1>
    <nav class="menu-site menu-top" id="static-page-links">
        <ul>
            <li><a href="#">English</a></li><li><a href="#">Вход</a></li>
        </ul>
    </nav>
    <nav class="menu-site menu-main">
        <ul>
            <li class="menu-link-active"><a href="/">Главная</a></li><li class="menu-link-regular"><a href="/static/team">О нас</a></li><li class="menu-link-regular"><a href="#">Цены</a></li>
        </ul>
    </nav>
</header>
<div class="grid-container">
    <h1 class="page-header">Простой и надёжный способ проверить навыки менеджера</h1>
    <ul class="list-light unstyled font-xxlarge home-list-box">
        <li>Деловая симуляция, позволяющая оценить управленческие навыки</li>
        <li>2-3 часовая игра</li>
        <li>Реальные задачи и ситуации, требующие принятия решений</li>
        <li>Отличный инструмент для оценки кандидатов и новых сотрудников</li>
    </ul>
</div>




<div class="grid-container">


    <div class="razdelitel"></div>


    <h2 class="font-blue-dark">Самый простой</h2>
    <h2 class="font-dark">Результативность</h2>
    <h3 class="font-brown">Индивидуальный профиль</h3>

    <div class="razdelitel"></div>

<div>
    <a href="#" class="btn btn-large btn-green">Get access</a>
</div>

<div class="razdelitel"></div>

<div>
    <a href="#" class="btn btn-primary">Get access</a>
</div>

<div class="razdelitel"></div>

<div>
    <a href="#" class="btn btn-site btn-green">Get access</a>
</div>

<div class="razdelitel"></div>

<ul id="yw1" class="yiiPager"><li class="first hidden"><a href="/dashboard/corporate">&lt;&lt; начало</a></li>
    <li class="previous hidden semi"><a href="/dashboard/corporate">Назад</a></li>
    <li class="page selected semi"><a href="/dashboard/corporate">1</a></li>
    <li class="page semi"><a href="/dashboard/corporate?page=2">2</a></li>
    <li class="page semi"><a href="/dashboard/corporate?page=2">3</a></li>
    <li class="next semi"><a href="/dashboard/corporate?page=2">Вперед</a></li>
    <li class="last semi"><a href="/dashboard/corporate?page=2">конец &gt;&gt;</a></li>
</ul>


<div class="razdelitel"></div>
    <div class="testblocks">
        <div class="block-border border-primary"></div>
        <div class="bg-rich-blue block-border"></div>
        <div class="bg-light-blue border-primary""></div>
        <div class="bg-blue border-primary""></div>
        <div class="bg-blue border-primary""></div>
        <div class="bg-yellow border-primary""></div>
    </div>

<div class="razdelitel"></div>
    <div class="testblocks">
        <div class="block-border-dark bg-lblue-primary border-large""></div><!-- bg results -->
        <div class="bg-blue-block border-large""></div><!-- bg results blocks -->
        <div class="bg-blue-bar border-primary""></div><!-- bg results bar -->
    </div>

<div class="razdelitel"></div>
<a href="#" class="proxima-reg">Link</a>&nbsp;&nbsp;&nbsp;<a href="#" class="link-dark proxima-reg">Link Dark</a>
<div class="razdelitel"></div>

<div>
    <div id="invite-people-box" class="block-border bg-rich-blue border-large pull-left">
        <div class="pad-xsize">
        <h3>Отправить приглашение</h3>
            <form id="invite-form" class="form-simple form-small">
                <div class="row "><label for="Invite_full_name">Имя</label><input placeholder="Имя" name="Invite[firstname]" id="Invite_firstname" type="text" maxlength="100"><input placeholder="Фамилия" name="Invite[lastname]" id="Invite_lastname" type="text" maxlength="100"></div>
                <div class="row "><label for="Invite_email" class="required">Email</label><input placeholder="Введите e-mail" name="Invite[email]" id="Invite_email" type="text" maxlength="255"></div>

                <div class="row wide postn-reltv">
                    <label for="Invite_vacancy_id" class="required">Позиция</span></label><div id="sbHolder_26848379" class="sbHolder">
                        <a id="sbToggle_26848379" href="#" class="sbToggle"></a>
                        <a id="sbSelector_26848379" href="#" class="sbSelector">Analytic</a>
                        <ul id="sbOptions_26848379" class="sbOptions" style="display: none;">
                            <li><a href="#" rel="4" class="sbFocus">Текст 1</a></li>
                            <li><a href="#" rel="4" class="sbFocus">Текст 2</a></li>
                            <li><a href="#" rel="4" class="sbFocus">Текст 3</a></li>
                            <li><a href="#" rel="4" class="sbFocus">Текст 4</a></li>
                        </ul>
                    </div>
                    <span class="btn-add"></span>
                </div>
                <div class="row buttons"><input name="prevalidate" type="submit" class="btn btn-primary" value="Отправить"></div>

            </form>
        </div>
    </div>
</div>
<div class="razdelitel"></div>

<div class="block-border bg-yellow border-primary pad-large pull-left">
    <ul class="list-dark unstyled font-xlarge">
        <li>Полная оценка навыков бесплатно</li>
        <li>Сравнение навыков с другими</li>
        <li>Бесплатные обновления</li>
    </ul>
    <div class="razdelitel"></div>
    <form class="form-simple form-small dark-labels form-profl-reg">
        <div class="row">
            <label>Имя</label><input placeholder="Имя" class="" name="YumProfile[firstname]" id="YumProfile_firstname" type="text"><input placeholder="Фамилия" class="" name="YumProfile[lastname]" id="YumProfile_lastname" type="text">
        </div>
        <div class="row wide">
            <label for="Invite_vacancy_id" class="required">Профессиональный статус</span></label><div id="sbHolder_26848379" class="sbHolder">
                <a id="sbToggle_26848379" href="#" class="sbToggle"></a>
                <a id="sbSelector_26848379" href="#" class="sbSelector">Собственник</a>
                <ul id="sbOptions_26848379" class="sbOptions" style="display: none;">
                    <li><a href="#" rel="4" class="sbFocus">Текст 1</a></li>
                    <li><a href="#" rel="4" class="sbFocus">Текст 2</a></li>
                    <li><a href="#" rel="4" class="sbFocus">Текст 3</a></li>
                    <li><a href="#" rel="4" class="sbFocus">Текст 4</a></li>
                </ul>
            </div>
        </div>
        <div class="row"><label>Корпоративный email</label><input placeholder="email" class="input-long" name="YumProfile[firstname]" id="YumProfile_firstname" type="text"></div>
    </form>
</div>

<div class="razdelitel"></div>

    <div class="registrationform" style="float: left">
        <form id="yum-user-registration-form" class="form-simple form-large">
            <div class="block-border bg-transparnt rows-inline">
                <div class="row"><input placeholder="Email" name="YumProfile[email]" id="YumProfile_email" type="text" value=""></div><div class="row"><input placeholder="Введите пароль" name="YumUser[password]" id="YumUser_password" type="password"></div><div class="row"><input placeholder="Подтвердите пароль" name="YumUser[password_again]" id="YumUser_password_again" type="password"></div><div class="row"><input type="submit" name="yt0" value="Сохранить изменения" class="btn-large font-xxxlarge btn-green proxima-bold"></div>
            </div>
        </form>
    </div>

<div class="razdelitel"></div>

<div class="social_networks smallicons">
    <div class="addthis_toolbox addthis_default_style addthis_32x32_style" addthis:url="http://player.vimeo.com/video/61279471" addthis:title="Skiliks - game the skills" addthis:description="Самый простой и надежный способ проверить навыки менеджеров: деловая онлайн симуляция, имитирующая реальный рабочий день с типичными управленческими задачами и ситуациями принятия решений">
        <a class="addthis_button_vk at300b" target="_blank" title="Vk" href="#"><span class=" at300bs at15nc at15t_vk"><span class="at_a11y"></span></span></a>
        <a class="addthis_button_facebook at300b" title="Facebook" href="#"><span class=" at300bs at15nc at15t_facebook"><span class="at_a11y"></span></span></a>
        <a class="addthis_button_twitter at300b" title="Tweet" href="#"><span class=" at300bs at15nc at15t_twitter"><span class="at_a11y"></span></span></a>
        <a class="addthis_button_google_plusone_share at300b" g:plusone:count="false" href="http://www.addthis.com/bookmark.php?v=300&amp;winname=addthis&amp;pub=ra-5158c9c22198d938&amp;source=tbx32-300&amp;lng=en-US&amp;s=google_plusone_share&amp;url=http%3A%2F%2Fplayer.vimeo.com%2Fvideo%2F61279471&amp;title=Skiliks%20-%20game%20the%20skills&amp;ate=AT-ra-5158c9c22198d938/-/-/51c092cc6369ed49/2&amp;frommenu=1&amp;uid=51c092cc3f68d12d&amp;description=%D0%A1%D0%B0%D0%BC%D1%8B%D0%B9%20%D0%BF%D1%80%D0%BE%D1%81%D1%82%D0%BE%D0%B9%20%D0%B8%20%D0%BD%D0%B0%D0%B4%D0%B5%D0%B6%D0%BD%D1%8B%D0%B9%20%D1%81%D0%BF%D0%BE%D1%81%D0%BE%D0%B1%20%D0%BF%D1%80%D0%BE%D0%B2%D0%B5%D1%80%D0%B8%D1%82%D1%8C%20%D0%BD%D0%B0%D0%B2%D1%8B%D0%BA%D0%B8%20%D0%BC%D0%B5%D0%BD%D0%B5%D0%B4%D0%B6%D0%B5%D1%80%D0%BE%D0%B2%3A%20%D0%B4%D0%B5%D0%BB%D0%BE%D0%B2%D0%B0%D1%8F%20%D0%BE%D0%BD%D0%BB%D0%B0%D0%B9%D0%BD%20%D1%81%D0%B8%D0%BC%D1%83%D0%BB%D1%8F%D1%86%D0%B8%D1%8F%2C%20%D0%B8%D0%BC%D0%B8%D1%82%D0%B8%D1%80%D1%83%D1%8E%D1%89%D0%B0%D1%8F%20%D1%80%D0%B5%D0%B0%D0%BB%D1%8C%D0%BD%D1%8B%D0%B9%20%D1%80%D0%B0%D0%B1%D0%BE%D1%87%D0%B8%D0%B9%20%D0%B4%D0%B5%D0%BD%D1%8C%20%D1%81%20%D1%82%D0%B8%D0%BF%D0%B8%D1%87%D0%BD%D1%8B%D0%BC%D0%B8%20%D1%83%D0%BF%D1%80%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%BC%D0%B8%20%D0%B7%D0%B0%D0%B4%D0%B0%D1%87%D0%B0%D0%BC%D0%B8%20%D0%B8%20%D1%81%D0%B8%D1%82%D1%83%D0%B0%D1%86%D0%B8%D1%8F%D0%BC%D0%B8%20%D0%BF%D1%80%D0%B8%D0%BD%D1%8F%D1%82%D0%B8%D1%8F%20%D1%80%D0%B5%D1%88%D0%B5%D0%BD%D0%B8%D0%B9&amp;ct=1&amp;pre=http%3A%2F%2Flive.skiliks.com%2F&amp;tt=0&amp;captcha_provider=nucaptcha" target="_blank" title="Google+"><span class=" at300bs at15nc at15t_google_plusone_share"><span class="at_a11y"></span></span></a>
        <a class="addthis_button_linkedin at300b" href="http://www.addthis.com/bookmark.php?v=300&amp;winname=addthis&amp;pub=ra-5158c9c22198d938&amp;source=tbx32-300&amp;lng=en-US&amp;s=linkedin&amp;url=http%3A%2F%2Fplayer.vimeo.com%2Fvideo%2F61279471&amp;title=Skiliks%20-%20game%20the%20skills&amp;ate=AT-ra-5158c9c22198d938/-/-/51c092cc6369ed49/3&amp;frommenu=1&amp;uid=51c092ccbe7e2162&amp;description=%D0%A1%D0%B0%D0%BC%D1%8B%D0%B9%20%D0%BF%D1%80%D0%BE%D1%81%D1%82%D0%BE%D0%B9%20%D0%B8%20%D0%BD%D0%B0%D0%B4%D0%B5%D0%B6%D0%BD%D1%8B%D0%B9%20%D1%81%D0%BF%D0%BE%D1%81%D0%BE%D0%B1%20%D0%BF%D1%80%D0%BE%D0%B2%D0%B5%D1%80%D0%B8%D1%82%D1%8C%20%D0%BD%D0%B0%D0%B2%D1%8B%D0%BA%D0%B8%20%D0%BC%D0%B5%D0%BD%D0%B5%D0%B4%D0%B6%D0%B5%D1%80%D0%BE%D0%B2%3A%20%D0%B4%D0%B5%D0%BB%D0%BE%D0%B2%D0%B0%D1%8F%20%D0%BE%D0%BD%D0%BB%D0%B0%D0%B9%D0%BD%20%D1%81%D0%B8%D0%BC%D1%83%D0%BB%D1%8F%D1%86%D0%B8%D1%8F%2C%20%D0%B8%D0%BC%D0%B8%D1%82%D0%B8%D1%80%D1%83%D1%8E%D1%89%D0%B0%D1%8F%20%D1%80%D0%B5%D0%B0%D0%BB%D1%8C%D0%BD%D1%8B%D0%B9%20%D1%80%D0%B0%D0%B1%D0%BE%D1%87%D0%B8%D0%B9%20%D0%B4%D0%B5%D0%BD%D1%8C%20%D1%81%20%D1%82%D0%B8%D0%BF%D0%B8%D1%87%D0%BD%D1%8B%D0%BC%D0%B8%20%D1%83%D0%BF%D1%80%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%BC%D0%B8%20%D0%B7%D0%B0%D0%B4%D0%B0%D1%87%D0%B0%D0%BC%D0%B8%20%D0%B8%20%D1%81%D0%B8%D1%82%D1%83%D0%B0%D1%86%D0%B8%D1%8F%D0%BC%D0%B8%20%D0%BF%D1%80%D0%B8%D0%BD%D1%8F%D1%82%D0%B8%D1%8F%20%D1%80%D0%B5%D1%88%D0%B5%D0%BD%D0%B8%D0%B9&amp;ct=1&amp;pre=http%3A%2F%2Flive.skiliks.com%2F&amp;tt=0&amp;captcha_provider=nucaptcha" target="_blank" title="Linkedin"><span class=" at300bs at15nc at15t_linkedin"><span class="at_a11y"></span></span></a>
        </div><span class="proxima-bold">Рекомендовать:</span>
</div>
<div class="razdelitel"></div>
<div class="social_networks">
    <div class="addthis_toolbox addthis_default_style addthis_32x32_style" addthis:url="http://player.vimeo.com/video/61279471" addthis:title="Skiliks - game the skills" addthis:description="Самый простой и надежный способ проверить навыки менеджеров: деловая онлайн симуляция, имитирующая реальный рабочий день с типичными управленческими задачами и ситуациями принятия решений">
        <a class="addthis_button_vk at300b" target="_blank" title="Vk" href="#"><span class=" at300bs at15nc at15t_vk"><span class="at_a11y"></span></span></a>
        <a class="addthis_button_facebook at300b" title="Facebook" href="#"><span class=" at300bs at15nc at15t_facebook"><span class="at_a11y"></span></span></a>
        <a class="addthis_button_twitter at300b" title="Tweet" href="#"><span class=" at300bs at15nc at15t_twitter"><span class="at_a11y"></span></span></a>
        <a class="addthis_button_google_plusone_share at300b" g:plusone:count="false" href="http://www.addthis.com/bookmark.php?v=300&amp;winname=addthis&amp;pub=ra-5158c9c22198d938&amp;source=tbx32-300&amp;lng=en-US&amp;s=google_plusone_share&amp;url=http%3A%2F%2Fplayer.vimeo.com%2Fvideo%2F61279471&amp;title=Skiliks%20-%20game%20the%20skills&amp;ate=AT-ra-5158c9c22198d938/-/-/51c092cc6369ed49/2&amp;frommenu=1&amp;uid=51c092cc3f68d12d&amp;description=%D0%A1%D0%B0%D0%BC%D1%8B%D0%B9%20%D0%BF%D1%80%D0%BE%D1%81%D1%82%D0%BE%D0%B9%20%D0%B8%20%D0%BD%D0%B0%D0%B4%D0%B5%D0%B6%D0%BD%D1%8B%D0%B9%20%D1%81%D0%BF%D0%BE%D1%81%D0%BE%D0%B1%20%D0%BF%D1%80%D0%BE%D0%B2%D0%B5%D1%80%D0%B8%D1%82%D1%8C%20%D0%BD%D0%B0%D0%B2%D1%8B%D0%BA%D0%B8%20%D0%BC%D0%B5%D0%BD%D0%B5%D0%B4%D0%B6%D0%B5%D1%80%D0%BE%D0%B2%3A%20%D0%B4%D0%B5%D0%BB%D0%BE%D0%B2%D0%B0%D1%8F%20%D0%BE%D0%BD%D0%BB%D0%B0%D0%B9%D0%BD%20%D1%81%D0%B8%D0%BC%D1%83%D0%BB%D1%8F%D1%86%D0%B8%D1%8F%2C%20%D0%B8%D0%BC%D0%B8%D1%82%D0%B8%D1%80%D1%83%D1%8E%D1%89%D0%B0%D1%8F%20%D1%80%D0%B5%D0%B0%D0%BB%D1%8C%D0%BD%D1%8B%D0%B9%20%D1%80%D0%B0%D0%B1%D0%BE%D1%87%D0%B8%D0%B9%20%D0%B4%D0%B5%D0%BD%D1%8C%20%D1%81%20%D1%82%D0%B8%D0%BF%D0%B8%D1%87%D0%BD%D1%8B%D0%BC%D0%B8%20%D1%83%D0%BF%D1%80%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%BC%D0%B8%20%D0%B7%D0%B0%D0%B4%D0%B0%D1%87%D0%B0%D0%BC%D0%B8%20%D0%B8%20%D1%81%D0%B8%D1%82%D1%83%D0%B0%D1%86%D0%B8%D1%8F%D0%BC%D0%B8%20%D0%BF%D1%80%D0%B8%D0%BD%D1%8F%D1%82%D0%B8%D1%8F%20%D1%80%D0%B5%D1%88%D0%B5%D0%BD%D0%B8%D0%B9&amp;ct=1&amp;pre=http%3A%2F%2Flive.skiliks.com%2F&amp;tt=0&amp;captcha_provider=nucaptcha" target="_blank" title="Google+"><span class=" at300bs at15nc at15t_google_plusone_share"><span class="at_a11y"></span></span></a>
        <a class="addthis_button_linkedin at300b" href="http://www.addthis.com/bookmark.php?v=300&amp;winname=addthis&amp;pub=ra-5158c9c22198d938&amp;source=tbx32-300&amp;lng=en-US&amp;s=linkedin&amp;url=http%3A%2F%2Fplayer.vimeo.com%2Fvideo%2F61279471&amp;title=Skiliks%20-%20game%20the%20skills&amp;ate=AT-ra-5158c9c22198d938/-/-/51c092cc6369ed49/3&amp;frommenu=1&amp;uid=51c092ccbe7e2162&amp;description=%D0%A1%D0%B0%D0%BC%D1%8B%D0%B9%20%D0%BF%D1%80%D0%BE%D1%81%D1%82%D0%BE%D0%B9%20%D0%B8%20%D0%BD%D0%B0%D0%B4%D0%B5%D0%B6%D0%BD%D1%8B%D0%B9%20%D1%81%D0%BF%D0%BE%D1%81%D0%BE%D0%B1%20%D0%BF%D1%80%D0%BE%D0%B2%D0%B5%D1%80%D0%B8%D1%82%D1%8C%20%D0%BD%D0%B0%D0%B2%D1%8B%D0%BA%D0%B8%20%D0%BC%D0%B5%D0%BD%D0%B5%D0%B4%D0%B6%D0%B5%D1%80%D0%BE%D0%B2%3A%20%D0%B4%D0%B5%D0%BB%D0%BE%D0%B2%D0%B0%D1%8F%20%D0%BE%D0%BD%D0%BB%D0%B0%D0%B9%D0%BD%20%D1%81%D0%B8%D0%BC%D1%83%D0%BB%D1%8F%D1%86%D0%B8%D1%8F%2C%20%D0%B8%D0%BC%D0%B8%D1%82%D0%B8%D1%80%D1%83%D1%8E%D1%89%D0%B0%D1%8F%20%D1%80%D0%B5%D0%B0%D0%BB%D1%8C%D0%BD%D1%8B%D0%B9%20%D1%80%D0%B0%D0%B1%D0%BE%D1%87%D0%B8%D0%B9%20%D0%B4%D0%B5%D0%BD%D1%8C%20%D1%81%20%D1%82%D0%B8%D0%BF%D0%B8%D1%87%D0%BD%D1%8B%D0%BC%D0%B8%20%D1%83%D0%BF%D1%80%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%BC%D0%B8%20%D0%B7%D0%B0%D0%B4%D0%B0%D1%87%D0%B0%D0%BC%D0%B8%20%D0%B8%20%D1%81%D0%B8%D1%82%D1%83%D0%B0%D1%86%D0%B8%D1%8F%D0%BC%D0%B8%20%D0%BF%D1%80%D0%B8%D0%BD%D1%8F%D1%82%D0%B8%D1%8F%20%D1%80%D0%B5%D1%88%D0%B5%D0%BD%D0%B8%D0%B9&amp;ct=1&amp;pre=http%3A%2F%2Flive.skiliks.com%2F&amp;tt=0&amp;captcha_provider=nucaptcha" target="_blank" title="Linkedin"><span class=" at300bs at15nc at15t_linkedin"><span class="at_a11y"></span></span></a>
        </div><span class="proxima-bold">Рекомендовать:</span>
</div>
<div class="razdelitel"></div>

<section class="bg-lblue-primary home-contnt-box border-large pad-xxsize">
    <article>
        <h2 class="font-blue-dark">Самый простой</h2>
        <ul class="unstyled">
            <li>Экономит время</li>
            <li>Позволяет тестировать любое количество людей в любой точке мира</li>
            <li>Не требует затрат на софт, оборудование или помещения! Просто убедитесь, что вы онлайн.</li>
            <li>Моментально предоставляет готовые для использования результаты оценки</li>
        </ul>
        <h2 class="font-blue-dark">Надежный</h2>
        <ul class="unstyled">
            <li>Оценивает ключевые для бизнеса навыки</li>
            <li>Основан на лучшей управленческой практике</li>
            <li>Максимально приближен к реальному деловому окружению, <br>задачам и ситуациям</li>
            <li>Использует математические методы анализа<br />поведения, а не субъективные ощущения</li>
        </ul>
    </article>
</section>

<div class="razdelitel"></div>



</div>
</div>
</body>

<script type="text/javascript">
    jQuery(document).ready(function(){
        Cufon.replace('.menu-site li, .unstyled li, p, label, input, select, .proxima-reg, .sbHolder a', {fontFamily:"ProximaNova-Regular", hover: true});
        Cufon.replace('.btn, .proxima-bold, h1, h2, h3, h4, h5, .dark-labels label, .list-dark li', {fontFamily:"ProximaNova-Bold", hover: true});
        Cufon.replace('.semi, .yiiPager li, .yiiPager a, .yiiPager .next a, .yiiPager .next a', {fontFamily:"ProximaNova-Semibold", hover: true});
<script>
    $(document).ready(function() {
        $('#my-button').popover({content: '#my-popover > .popup-content'});
    });
</script>
