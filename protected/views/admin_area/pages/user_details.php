<?php
$assetsUrl = $this->getAssetsUrl();
?>
<?php /* @var $user YumUser */ ?>
<br/>
<br/>

<h1>
    <?php if (null !== $user->getAccount()) : ?>
        <?php if ($user->isCorporate()): ?>
            <img src="<?=$assetsUrl?>/img/bg-registration-corporate.png" />
        <?php else: ?>
            <img src="<?=$assetsUrl?>/img/bg-registration-personal.png" />
        <?php endif ?>

        <?= $user->profile->firstname ?>
        <?= $user->profile->lastname ?>
    <?php else: ?>
        <?= $user->profile->email ?>
    <?php endif ?>
</h1>

<br/>
<br/>

<a class="btn btn-success"
   href="<?= $this->createAbsoluteUrl('admin_area/AdminPages/UpdatePassword', ['userId' => $user->id]) ?>">
    <i class="icon icon-pencil icon-white"></i>&nbsp;
    Изменить пароль</a>
<?php if ($user->isCorporate()): ?>
    &nbsp; &nbsp;
    <a class="btn btn-success ban-corporate-user" data-id="<?= $user->id ?>" data-email="<?= $user->profile->email ?>">
        <i class="icon icon-ban-circle icon-white"></i>
        Забанить аккаунт
    </a>
<?php endif; ?>

&nbsp; &nbsp;
<a class="btn btn-success" href="/admin_area/login/ghost/<?= $user->id ?>">
    <i class="icon icon-home icon-white"></i>
    Войти на сайт от имени пользователя
</a>

&nbsp; &nbsp;
<a class="btn btn-success" href="/admin_area/site-log-account-action/?user_id=<?= $user->id ?>">
    Логи аккаунта
</a>


<?php if($user->is_password_bruteforce_detected === YumUser::IS_PASSWORD_BRUTEFORCE_DETECTED) : ?>
    &nbsp; &nbsp;
    <a class="btn btn-success" href="/admin_area/user-bruteforce/?user_id=<?= $user->id ?>&set=<?=YumUser::IS_NOT_PASSWORD_BRUTEFORCE?>">
        Разблокировать авторизацию
    </a>
<?php else : ?>
    &nbsp; &nbsp;
    <a class="btn btn-danger" href="/admin_area/user-bruteforce/?user_id=<?= $user->id ?>&set=<?=YumUser::IS_PASSWORD_BRUTEFORCE_DETECTED?>">
         Заблокировать авторизацию
    </a>
<?php endif ?>

<!-- разделитель кнопок -->
<p>&nbsp; &nbsp;</p>

<a class="btn btn-info" href="/admin_area/invites?page=1&receiver-email-for-filtration=<?= urlencode($user->profile->email) ?>&invite_statuses[0]=on&invite_statuses[1]=on&invite_statuses[5]=on&invite_statuses[2]=on&invite_statuses[4]=on&invite_statuses[3]=on&invite_status[]=on&filter_scenario_id=&is_invite_crashed=">
        <i class="icon icon-arrow-down icon-white"></i>
        Приглашения для меня
</a>

<?php if($user->isCorporate()) : ?>
    <a class="btn btn-info" href="/admin_area/corporate-account/<?= $user->id ?>/invite-limit-logs">
        <i class="icon icon-list icon-white"></i>
        Логи списания/зачисления симуляций
    </a>
    &nbsp; &nbsp;
    <a class="btn btn-info" href="/admin_area/invites?page=1&owner_email_for_filtration=<?= urlencode($user->profile->email) ?>&invite_statuses[0]=on&invite_statuses[1]=on&invite_statuses[5]=on&invite_statuses[2]=on&invite_statuses[4]=on&invite_statuses[3]=on&invite_status[]=on&filter_scenario_id=&is_invite_crashed=">
            <i class="icon icon-arrow-up icon-white"></i>
            Приглашения от меня
    </a>
<?php endif ?>

<br/>
<br/>
<br/>

<table class="table">
    <tr>
        <td style="width: 25%">Имя и Фамилия</td>
        <td style="width: 25%"><span style='text-label-200px'><?= $user->profile->firstname ?></span> <span style='text-label-200px'><?= $user->profile->lastname ?></span></td>
        <td style="width: 25%">Личный email</td>
        <td style="width: 25%">
            <i class="icon icon-user"></i>
            <span style='text-label-200px'><?= $user->profile->email ?></span>
        </td>
    </tr>
    <tr>
        <td style="width: 25%">Дата регистрации</td>
        <td style="width: 25%"><?= date('Y-m-d H:i:s', $user->createtime) ?></td>
        <td style="width: 25%">Дата последнего визита</td>
        <td style="width: 25%"><?= date('Y-m-d H:i:s', $user->lastvisit) ?></td>
    </tr>

    <!-- key { -->
    <?php if (1 != $user->activationKey): ?>
    <tr>
        <td>Ключь</td>
        <td>
            <div style="max-width: 400px; overflow: auto;">
                <?= $user->activationKey ?>
            </div>
        </td>
        <td></td>
        <td></td>
    </tr>
    <?php endif ?>
    <!-- key } -->

    <tr>
        <td>Тип аккаунта</td>
        <td>
            <?php
                $class = "label-warning"; // просто оранжевый заметнее, никакого предупреждения в этом нет
                if ($user->isCorporate()) {
                    $class = "label-inverse";
                }
            ?>
            <span class="label <?= $class ?>">
                <?= $user->getAccountName() ?>
            </span>
            <?php if ($user->isCorporate()) : ?>

                <?php if($user->status == YumUser::STATUS_BANNED) : ?>
                    <span class="label label-important">Аккаунт заблокирован</span>
                <?php else : ?>
                    <span class="label label-success">Аккаунт не заблокирован</span>
                <?php endif; ?>

            <? endif; ?>

        </td>
        <?php if ($user->isCorporate()) : ?>
            <td>Корпоративный email</td>
            <td>
                <i class="icon icon-briefcase"></i>
                <?= $user->profile->email ?>
            </td>
        <?php endif; ?>
    </tr>
    <tr>
        <?php if ($user->isCorporate()) : ?>
            <td>Количество доступных приглашений</td>
            <td>
                <?= $user->getAccount()->invites_limit ?>
            </td>
        <?php endif; ?>
    </tr>
    <tr>
        <?php if ($user->isCorporate()) : ?>
            <td>
                Добавить симуляции в аккаунт
                <br/>
                <small style="color: #888;">Чтоб забрать симуляции введите отрицательное значение</small>
            </td>

            <td>
                <form action="/admin_area/user/<?= $user->id ?>/set-invites-limit/"
                      method="post" style="display: inline-block;">
                    <input name="new_value" type="integer" size="3" style="width:30px;" value="0" />
                    <input class="btn btn-success" id="add_invites_button" type="submit" value="Добавить/списать">
                </form>
            </td>
        <?php endif; ?>
    </tr>
    <tr>
        <td> Вид оценки</td>
        <td>
            <?= $user->profile->assessment_results_render_type ?>
        </td>
    </tr>
    <tr>
        <td>IP Address</td>
        <td><?= ($user->ip_address !== null) ? $user->ip_address : "-"; ?></td>
        <td></td>
        <td></td>
    </tr>

</table>
<?php if ($user->isCorporate()) : ?>
    <form class="form" method="post">
    <table class="table">
        <tr>
            <td>Сайт</td>
            <td>Описание для продаж</td>
        </tr>
        <tr>
            <td><textarea name="site" style="width: 90%;"><?= $user->account_corporate->site ?></textarea></td>
            <td><textarea name="description_for_sales" style="width: 90%;"><?= $user->account_corporate->description_for_sales ?></textarea></td>
        </tr>
        <tr>
            <td>Телефоны для продаж</td>
            <td>Статус для продаж</td>
        </tr>
        <tr>
            <td><textarea name="contacts_for_sales" style="width: 90%;"><?= $user->account_corporate->contacts_for_sales ?></textarea></td>
            <td><textarea name="status_for_sales" style="width: 90%;"><?= $user->account_corporate->status_for_sales ?></textarea></td>
        </tr>
        <tr>
            <td>Название компании</td>
            <td>Отрасль компании</td>
        </tr>
        <tr>
            <td><textarea name="company_name_for_sales" style="width: 90%;"><?= $user->account_corporate->company_name_for_sales ?></textarea></td>
            <td><textarea name="industry_for_sales" style="width: 90%;"><?= $user->account_corporate->industry_for_sales ?></textarea></td>
        </tr>
        <tr>
            <td></td>
            <td><button type="submit" name="save_form" value="true" class="btn btn-success">Сохранить</button></td>
        </tr>
    </table>
    </form>
<?php endif ?>