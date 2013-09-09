<div>
    <h1>Dashboard</h1>

    <br/>

    <!-- DEV modes: -->

    <h4>Запуск симуляций в DEV режиме:</h4>

    <a class="btn" style="margin-right: 50px;"
        href="/simulation/<?php echo Simulation::MODE_DEVELOPER_LABEL ?>/<?php echo Scenario::TYPE_LITE ?>">
        </i>Developer (lite)</a>

    <a class="btn btn-success"
       href="/simulation/<?php echo Simulation::MODE_DEVELOPER_LABEL ?>/<?php echo Scenario::TYPE_FULL ?>">
        Developer (full)</a>

    <br/><br/>

    <!-- Cheats: -->

    <?php if (Yii::app()->user->data()->isCorporate()) : ?>
        <h4>Cheats:</h4>

        <a class="btn" href="/invite/add-10">
            <i class="icon-plus"></i> Добавить себе 10 приглашений в корп. аккаунт
        </a>

        <br/><br/>
    <?php endif ?>

    <!-- Analyzer: -->
    <!--
    <h4>Ссылки на анализатор сценария (не рабочие :) ):</h4>
    <a href="/cheat/uploadDialogsToAnalyzer" style="line-height: 30px;">
        Открыть анализатор диалогов произвольного ексел-файла</a>
        <br/>
    <a href="/cheat/dialogsAnalyzer">Открыть анализатор диалогов БД</a>

    <br/><br/>
    -->

    <!-- "New" CSS: -->

    <!--
    <h4>Страницы с "новыми" CSS:</h4>
    <a href="/debug/styleCss">style css</a><br/><br/>
    <a href="/debug/styleForPopupCss">style popup css</a><br/><br/>
    <a href="/debug/styleBlocks">style blocks</a><br/><br/>
    <a href="/debug/styleGrid">style grid</a><br/><br/>
    <a href="/debug/styleGridResults">style grid results</a><br/><br/>
    <a href="/debug/styleEmpty1280">style empty 1280</a><br/><br/>
    <a href="/debug/styleEmpty1024">style empty 1024</a><br/><br/>
    <a href="/dashboard-new" >dashboard Corporate new</a><br/><br/>
    <a href="/profile-corporate-tariff-new">tariff Corporate new</a><br/><br/>
    <a href="/profile-corporate-company-info-new" >company info Corporate new</a><br/><br/>
    <a href="/profile-corporate-user-info-new" >user info Corporate new</a><br/><br/>
    <a href="/profile-corporate-password-new" >Corporate password new</a><br/><br/>
    <a href="/profile-corporate-vacancies-new" >Corporate vacavcies new</a><br/><br/>
    <a href="/product-new" >Product new</a><br/><br/>
    <a href="/team-new" >Team new</a><br/><br/>
    <a href="/form-errors-standard" >form errors standard</a><br/><br/>
    <a href="/home-new" >home standard</a><br/><br/>
    <a href="/old-browser-new" >old browser standard</a><br/><br/>
    <a href="/static/tariffs-new" >static tariffs-new</a><br/><br/>
    <a href="/order-new/starter" >order "starter" tariff</a><br/><br/>
    <br/><br/>
    <a href="/static/drag-and-drop">Drag & Drop prototype</a>
    -->

</div>