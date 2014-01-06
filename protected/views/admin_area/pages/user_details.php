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
    &nbsp; &nbsp;
    <a class="btn btn-info"
       href="<?= $this->createAbsoluteUrl('admin_area/AdminPages/UserReferrals', ['userId' => $user->id]) ?>">
        <i class="icon icon-gift icon-white"></i>&nbsp;
        Рефераллы</a>
    &nbsp; &nbsp;
    <a class="btn btn-info" href="/admin_area/corporate-account/<?= $user->id ?>/invite-limit-logs">
        <i class="icon icon-list icon-white"></i>
        Логи списания/зачисления симуляций
    </a>
    &nbsp; &nbsp;
    <a class="btn btn-info" href="/admin_area/list-tariff-plan?user_id=<?= $user->id ?>">
        <strong>$</strong>
        Тарифные планы
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
            <td>Текущий тарифный план</td>
            <td>
                <?= $user->getAccount()->getActiveTariff()->label ?>

                &nbsp;&nbsp;

                <div class="btn-group">
                    <a class=" btn btn-success dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="icon icon-refresh icon-white"></i>
                        Сменить на:
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <?php foreach (Tariff::$tarifs as $tariff): ?>
                            <?php if (strtolower($user->getAccount()->tariff->label) == strtolower($tariff)) {
                                continue;
                            } ?>
                            <li>
                                <a href="/admin_area/user/<?= $user->id ?>/set-tariff/<?= $tariff ?>">
                                    <?= ucfirst(Yii::t('site', $tariff)); ?>
                                </a>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </td>
            <td>Количество доступных приглашений</td>
            <td>
                <?= $user->getAccount()->invites_limit.' / '.$user->getAccount()->referrals_invite_limit.'(за рефералов)' ?>
            </td>
        <?php endif; ?>
    </tr>
    <tr>
        <?php if ($user->isCorporate()) : ?>
            <td>Тариф истекает</td>
            <td>
                <?= $user->getAccount()->getActiveTariffPlan()->finished_at ?>
            </td>
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
        <td>Показывать попап что тарифный план истёк </td>
        <td>
            <?php if ($user->isCorporate() && $user->getAccount()->is_display_tariff_expire_pop_up) : ?>
                <i class="icon icon-eye-open"></i> Да
            <?php else : ?>
                <i class="icon icon-eye-close"></i> Нет
            <?php endif ?>
            <form action="/admin_area/user/<?= $user->id ?>/details/"
                  method="post" style="display: inline-block;">

                <button class="btn btn-success" name="switchTariffExpiredPopup" type="submit">
                    <i class="icon icon-refresh icon-white"></i> Сменить
                </button>
            </form>
        </td>
        <td> Вид оценки</td>
        <td>
            <?= $user->profile->assessment_results_render_type ?>
        </td>
    </tr>

        <?php if ($user->isCorporate()) : ?>
            <tr>
                <td>Показывать попап рефералов </td>
                <td>
                    <?php if ($user->account_corporate->is_display_referrals_popup) : ?>
                        <i class="icon icon-eye-open"></i> Да
                    <?php else : ?>
                        <i class="icon icon-eye-close"></i> Нет
                    <?php endif ?>
                        <form action="/admin_area/user/<?= $user->id ?>/details/"
                              method="post" style="display: inline-block;">

                            <button class="btn btn-success" name="switchReferralInfoPopup" type="submit">
                                <i class="icon icon-refresh icon-white"></i> Сменить
                            </button>
                        </form>

                </td>
                <?php if ($user->isCorporate()) : ?>
                    <td>Правило для срока годности приглашения</td>
                    <td>
                        <?= $user->getAccount()->expire_invite_rule ?>

                        &nbsp;&nbsp;

                        <div class="btn-group">
                            <a class=" btn btn-success dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="icon icon-refresh icon-white"></i>
                                Сменить на:
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <?php if($user->getAccount()->expire_invite_rule === 'standard') : ?>
                                    <li>
                                        <a href="/admin_area/change-invite-expire-rule/?user_id=<?= $user->id ?>&rule=by_tariff">
                                            By Tariff
                                        </a>
                                    </li>
                                <?php else : ?>
                                    <li>
                                        <a href="/admin_area/change-invite-expire-rule/?user_id=<?= $user->id ?>&rule=standard">
                                            Standard
                                        </a>
                                    </li>
                                <?php endif ?>
                            </ul>
                        </div>
                    </td>
                <?php else : ?>
                    <td></td>
                    <td></td>
                <?php endif ?>
            </tr>
        <?php endif ?>
    <tr>
        <td>IP Address</td>
        <td><?= ($user->ip_address !== null) ? $user->ip_address : "-"; ?></td>
        <?php if ($user->isCorporate()) : ?>
        <td>Tariff Plan id</td>
        <td><?= $user->account_corporate->getActiveTariffPlan()->id ?></td>
        <?php else: ?>
        <td></td>
        <td></td>
        <?php endif ?>
    </tr>

</table>