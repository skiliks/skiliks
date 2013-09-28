<?php
$assetsUrl = $this->getAssetsUrl();
?>

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
    <a class="btn btn-info" target="_blank"
       href="<?= $this->createAbsoluteUrl('admin_area/AdminPages/UserReferrs', ['userId' => $user->id]) ?>">
        <i class="icon icon-share icon-white"></i>&nbsp;
        Рефераллы</a>
<?php endif; ?>

<a class="btn btn-info" href="/admin_area/corporate-account/<?= $user->id ?>/invite-limit-logs">Логи списания/зачисления симуляций</a>

<br/>
<br/>

<table class="table">
    <tr>
        <td style="width: 25%">Имя и Фамилия</td>
        <td style="width: 25%"><?= $user->profile->firstname ?> <?= $user->profile->lastname ?></td>
        <td style="width: 25%">Личный email</td>
        <td style="width: 25%">
            <i class="icon icon-user"></i>
            <?= $user->profile->email ?>
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
        </td>
        <?php if ($user->isCorporate()) : ?>
            <td>Корпоративный email</td>
            <td>
                <i class="icon icon-briefcase"></i>
                <?= $user->getAccount()->corporate_email ?>
            </td>
        <?php endif; ?>
    </tr>
    <tr>
        <?php if ($user->isCorporate()) : ?>
            <td>Текущий тарифный план</td>
            <td>
                <?= $user->getAccount()->tariff->label ?>

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
                <?= $user->getAccount()->getTotalAvailableInvitesLimit() ?>
            </td>
        <?php endif; ?>
    </tr>
    <tr>
        <?php if ($user->isCorporate()) : ?>
            <td>Тариф истекает</td>
            <td>
                <?= $user->getAccount()->tariff_expired_at ?>
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

        <?php if ($user->isCorporate()) : ?>
            <tr>
                <td>Показывать попап рефералов: </td>
                <td>
                    <?php if ($user->account_corporate->is_display_referrals_popup) : ?>
                        Показывается
                    <?php else : ?>
                       Не показывается
                    <?php endif ?>
                        <form action="/admin_area/user/<?= $user->id ?>/details/"
                              method="post" style="display: inline-block;">

                            <button class="btn btn-success" name="changeReferPopup" type="submit">
                                <i class="icon icon-refresh icon-white"></i> Сменить
                            </button>
                        </form>

                </td>
            </tr>
        <?php endif; ?>
</table>