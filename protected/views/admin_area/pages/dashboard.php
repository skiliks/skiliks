<div>
    <h1>Dashboard</h1>

    <br/>


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

    <h4>Перейти к пользователю по его email:</h4>
    <form metho="POST" action="/admin_area/user/by-email">
        <input name="email" /> <input class="btn" type='submit' value="Найти!">
    </form>

    <br/><br/>

    <form method="post" action="/debug/send">
        <h4>Отправить набор стандартных писем по адресу:</h4>
        Email: <input type="text" name="email" />
        <input class="btn" type="submit" value="Send!">
    </form>
    <br/><br/>
</div>