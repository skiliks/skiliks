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

<!-- Управление аккаунтом ------------------------------------------ -->

<h3>Управление аккаунтом</h3>

<?php if (Yii::app()->user->data()->can('user_change_password')) : ?>
    <a class="btn btn-success"
       href="<?= $this->createAbsoluteUrl('admin_area/AdminPages/UpdatePassword', ['userId' => $user->id]) ?>">
        <i class="icon icon-pencil icon-white"></i>&nbsp;
        Изменить пароль</a>
<?php endif ?>

<?php if ($user->isCorporate()): ?>
    <?php if (Yii::app()->user->data()->can('corp_user_ban_unban')) : ?>
        &nbsp; &nbsp;
        <?php if($user->isBanned()) : ?>
        <a class="btn btn-success unban-corporate-user" data-id="<?= $user->id ?>" data-email="<?= $user->profile->email ?>">
            <i class="icon icon-ban-circle icon-white"></i>
            Разбанить аккаунт
        </a>
        <?php else : ?>
            <a class="btn btn-success ban-corporate-user" data-id="<?= $user->id ?>" data-email="<?= $user->profile->email ?>">
                <i class="icon icon-ban-circle icon-white"></i>
                Забанить аккаунт
            </a>
        <?php endif ?>
    <?php endif ?>
<?php endif; ?>

<?php if (Yii::app()->user->data()->can('user_login_ghost')) : ?>
    &nbsp; &nbsp;
    <a class="btn btn-success" href="/admin_area/login/ghost/<?= $user->id ?>">
        <i class="icon icon-home icon-white"></i>
        Войти на сайт от имени пользователя
    </a>
<?php endif ?>

<?php if (Yii::app()->user->data()->can('user_logs_view')) : ?>
    &nbsp; &nbsp;
    <a class="btn btn-info" href="/admin_area/site-log-account-action/?user_id=<?= $user->id ?>">
        Логи аккаунта
    </a>
<?php endif ?>

<?php if (Yii::app()->user->data()->can('user_auth_block_unblock')) : ?>
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
<?php endif ?>

<!-- разделитель кнопок 1 -->
<p>&nbsp; &nbsp;</p>


<?php if (Yii::app()->user->data()->can('invites_list_view')) : ?>
    <a class="btn btn-info" href="/admin_area/invites?page=1&receiver-email-for-filtration=<?= urlencode($user->profile->email) ?>&invite_statuses[0]=on&invite_statuses[1]=on&invite_statuses[5]=on&invite_statuses[2]=on&invite_statuses[4]=on&invite_statuses[3]=on&invite_status[]=on&filter_scenario_id=&is_invite_crashed=">
        <i class="icon icon-arrow-down icon-white"></i>
        Приглашения для меня
    </a>
<?php endif ?>

<?php if($user->isCorporate()) : ?>
    <?php if (Yii::app()->user->data()->can('user_invite_movement_logs_view')) : ?>
        &nbsp; &nbsp;
        <a class="btn btn-info" href="/admin_area/corporate-account/<?= $user->id ?>/invite-limit-logs">
            <i class="icon icon-list icon-white"></i>
            Логи списания/зачисления симуляций
        </a>
    <?php endif ?>


    <?php if (Yii::app()->user->data()->can('invites_list_view')) : ?>
    &nbsp; &nbsp;
        <a class="btn btn-info" href="/admin_area/invites?page=1&owner_email_for_filtration=<?= urlencode($user->profile->email) ?>&invite_statuses[0]=on&invite_statuses[1]=on&invite_statuses[5]=on&invite_statuses[2]=on&invite_statuses[4]=on&invite_statuses[3]=on&invite_status[]=on&filter_scenario_id=&is_invite_crashed=">
            <i class="icon icon-arrow-up icon-white"></i>
            Приглашения от меня
        </a>
    <?php endif ?>

    <?php if (Yii::app()->user->data()->can('user_balk_send_invites')) : ?>
        &nbsp; &nbsp;
        <a class="btn btn-success" href="/admin_area/user/<?= $user->id ?>/send-invites">
            <i class="icon icon-envelope icon-white"></i>
            Отправить приглашения
        </a>
    <?php endif ?>

    <!-- разделитель кнопок 2 -->
    <p>&nbsp; &nbsp;</p>

    <?php if (Yii::app()->user->data()->can('user_add_remove_from_news_mail_list')) : ?>
        <?php if($user->account_corporate->excluded_from_mailing === UserAccountCorporate::EXCLUDED_FROM_MAILING_YES) : ?>
            <a class="btn btn-success" href="/admin_area/excluded_from_mailing?user_id=<?= $user->id ?>&set=<?=UserAccountCorporate::EXCLUDED_FROM_MAILING_NO?>">
                Включить почту
            </a>
        <?php else : ?>
            <a class="btn btn-success" href="/admin_area/excluded_from_mailing/?user_id=<?= $user->id ?>&set=<?=UserAccountCorporate::EXCLUDED_FROM_MAILING_YES?>">
                Исключить почту из рассылки
            </a>
        <?php endif ?>
    <?php endif ?>

    &nbsp; &nbsp;

    <a href="/admin_area/user/<?= $user->id ?>/vacancies-list" class="btn btn-info">
        <i class="icon icon-briefcase icon-white"></i>
        Список позиций
    </a>
<?php endif ?>

<br/>
<br/>
<br/>

<!-- Личные данные ------------------------------------------ -->

<h3>Личные данные</h3>

<table class="table">

    <!-- --- -->

    <tr>
        <td style="width: 25%">Имя и Фамилия</td>
        <td style="width: 25%"><span style='text-label-200px'><?= $user->profile->firstname ?></span> <span style='text-label-200px'><?= $user->profile->lastname ?></span></td>
        <td style="width: 25%">
            Личный email
            <br/>
            <small style="color: #888;">Для корпоративных аккаунтов, личный и корпоративный емейл - это одно и тоже.</small>
        </td>
        <td style="width: 25%">
            <i class="icon icon-user" style="margin: -2px 4px 0 0;"></i>

            <?php if (Yii::app()->user->can('user_change_email')): ?>
                <?php $loginWidget = $this->beginWidget('CActiveForm', [
                    'action'      => Yii::app()->request->hostInfo.'/admin_area/user/'.$user->id.'/change-email',
                    'enableAjaxValidation' => true,
                    'clientOptions'        => array(
                        'validateOnSubmit' => true,
                        'validateOnChange' => false,
                        'afterValidate'    => 'js:changeEmailValidation',
                    ),
                    'htmlOptions' => [
                        'style' => 'display: inline;',
                    ]
                ]); ?>

                <?php echo $loginWidget->error($user->profile, 'email'); ?>
                <?php echo $loginWidget->textField($user->profile, "email", [
                    'style' => 'display: inline-block;',
                ]) ?>

                <?php echo CHtml::submitButton( 'Изменить', [
                    'class' => 'btn btn-success',
                    'style' => 'display: inline-block;',
                ]); ?>

                <?php $this->endWidget(); ?>
            <?php else: ?>
                <?=$user->profile->email ?>
            <?php endif ?>
        </td>
    </tr>

    <!-- --- -->

    <tr>
        <td style="width: 25%">Дата регистрации</td>
        <td style="width: 25%"><?= date('Y-m-d H:i:s', $user->createtime) ?></td>
        <td style="width: 25%">Дата последнего визита</td>
        <td style="width: 25%"><?= date('Y-m-d H:i:s', $user->lastvisit) ?></td>
    </tr>

    <!-- --- -->

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

    <!-- --- -->

    <tr>
        <td> Вид оценки</td>
        <td>
            <?= $user->profile->assessment_results_render_type ?>
        </td>
        <td>IP Address</td>
        <td><?= ($user->ip_address !== null) ? $user->ip_address : "-"; ?></td>

    </tr>

    <!-- --- -->

    <?php if ($user->isCorporate()) : ?>
        <tr>
            <td>
                <?php if (Yii::app()->user->data()->can('user_invite_movement_logs_view')) : ?>
                    Количество доступных приглашений
                <?php endif ?>
            </td>
            <td>
                <?php if (Yii::app()->user->data()->can('user_invite_movement_logs_view')) : ?>
                    <?= $user->getAccount()->invites_limit ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (Yii::app()->user->data()->can('user_add_remove_simulations')) : ?>
                    Добавить симуляции в аккаунт
                    <br/>
                    <small style="color: #888;">Чтоб забрать симуляции введите отрицательное значение.</small>
                <?php endif; ?>
            </td>
            <td>
                <?php if (Yii::app()->user->data()->can('user_add_remove_simulations')) : ?>
                    <form action="/admin_area/user/<?= $user->id ?>/set-invites-limit/"
                          method="post" style="display: inline-block;">
                        <input name="new_value" type="integer" size="3" style="width:30px;" value="0" />
                        <input class="btn btn-success" id="add_invites_button" type="submit" value="Добавить/списать">
                    </form>
                <?php endif; ?>
            </td>

        </tr>
    <?php endif; ?>

    <!-- key { -->

    <?php if (1 != $user->activationKey): ?>
        <tr>
            <td>Ключь</td>
            <td colspan="3">
                <div style="max-width: 900px; overflow: auto;">
                    <?= $user->activationKey ?>
                </div>
            </td>
        </tr>
    <?php endif ?>

    <!-- key } -->

    <!-- Права -->
    <?php if (Yii::app()->user->data()->can('user_change_role')) : ?>
        <tr>
            <td>Назначенная в системе прав роль</td>
            <td>
                <form method="post" action="/admin_area/user/<?= $user->id?>/change-role">
                    <select name="newRole" style="width: 200px;">
                        <?php /** @var YumRole $role */ ?>
                        <?php foreach ($roles as $role) : ?>
                            <?php $attributeSelected = ($role->title == trim($user->getRoles())) ? ' selected="selected" ' : '' ; ?>
                            <option value="<?= $role->title ?>" <?= $attributeSelected ?> >
                                <?= $role->title ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input style="margin-top: 1px; vertical-align: top;" class="btn btn-success" type="submit" value="Сменить" />
                </form>
            </td>
            <td></td>
            <td></td>
        </tr>
    <?php endif ?>

    <tr>
        <td>Возможности доступные пользователю:</td>
        <td colspan="3">
            <?php foreach ($roles as $role) : ?>
                <?php if($role->title == trim($user->getRoles())) : ?>
                    <ul>
                        <?php foreach ($role->getPermissionsSorted() as $permission) : ?>
                            <?php if (isset(YumRole::$subtitle[$permission->Action->order_no])) : ?>
                                </ul>
                                    <h5>
                                        <?= YumRole::$subtitle[$permission->Action->order_no] ?>
                                    </h5>
                                <ul>
                            <?php endif ?>
                            <li style="list-style: none;">
                                <?= $permission->Action->order_no ?>.
                                <?= $permission->Action->subject ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (0 == count($role->getPermissionsSorted())) : ?>
                        Роль пользователя полностью лишена прав.
                    <?php endif ?>
                <?php endif ?>
            <?php endforeach; ?>
        </td>
    </tr>

</table>

<?php if ($user->isCorporate()) : ?>

    <!-- Скидка ------------------------------------------ -->

    <h3>Скидка:</h3>

    <form class="form-inline" method="post">
        <table class="table">
            <tr>
                <td>
                    <?php if (Yii::app()->user->data()->can('user_discount_edit')) : ?>
                        Задать скидку в размере
                    <?php else: ?>
                        Текущее значение скидки
                    <?php endif ?>
                </td>
                <td>
                    <?php if (Yii::app()->user->data()->can('user_discount_edit')) : ?>
                        <input type="text" name="discount" class="input-small" placeholder="Скидка"
                               value="<?= $user->account_corporate->discount ?>"> (0.00 ~ 100.00%).
                    <?php else: ?>
                        <?= $user->account_corporate->discount ?> %
                    <?php endif ?>
                </td>
            </tr>
            <tr>
                <td>Дата начала действия скидки</td>
                <td>
                    <?php if (Yii::app()->user->data()->can('user_discount_edit')) : ?>
                        <input type="text" name="start_discount" class="input-large" placeholder="пример - 2013-10-04"
                           value="<?= $user->account_corporate->start_discount ?>">
                    <?php else: ?>
                        <?= $user->account_corporate->start_discount ?>
                    <?php endif ?>
                </td>
            </tr>
            <tr>
                <td>Дата окончания действия скидки</td>
                <td>
                    <?php if (Yii::app()->user->data()->can('user_discount_edit')) : ?>
                        <input type="text" name="end_discount" class="input-large" placeholder="пример - 2013-11-24"
                            value="<?= $user->account_corporate->end_discount ?>">
                    <?php else: ?>
                        <?= $user->account_corporate->end_discount ?>
                    <?php endif ?>
                </td>
            </tr>

            <?php if (Yii::app()->user->data()->can('user_discount_edit')) : ?>
                <tr>
                    <td colspan="2">
                        <button type="submit" name="discount_form" class="btn btn-success" value="true" style="font-weight: bold;">
                            Изменить параменты скидки для аккаунта <?= $user->profile->email ?>
                        </button>
                    </td>
                </tr>
            <?php endif ?>
        </table>
    </form>

    <h3>Данные для менеджеров по продажам</h3>

    <form class="form" method="post">
    <table class="table">
        <tr>
            <td style="width: 200px;">Сайт</td>
            <td>
                <?php if (Yii::app()->user->data()->can('user_sales_manager_data_edit')) : ?>
                    <textarea name="site" style="width: 90%;"><?= $user->account_corporate->site ?></textarea>
                <?php else : ?>
                    <?= $user->account_corporate->industry_for_sales ?>
                <? endif ?>
            </td>

        </tr>
        <tr>
            <td>Описание для продаж</td>
            <td>
                <?php if (Yii::app()->user->data()->can('user_sales_manager_data_edit')) : ?>
                    <textarea name="description_for_sales" style="width: 90%;"><?= $user->account_corporate->description_for_sales ?></textarea>
                <?php else : ?>
                    <?= $user->account_corporate->industry_for_sales ?>
                <? endif ?>
            </td>
        </tr>
        <tr>
            <td>Телефоны для продаж</td>
            <td>
                <?php if (Yii::app()->user->data()->can('user_sales_manager_data_edit')) : ?>
                    <textarea name="contacts_for_sales" style="width: 90%;"><?= $user->account_corporate->contacts_for_sales ?></textarea>
                <?php else : ?>
                    <?= $user->account_corporate->industry_for_sales ?>
                <? endif ?>
            </td>
        </tr>
        <tr>
            <td>Статус для продаж</td>
            <td>
                <?php if (Yii::app()->user->data()->can('user_sales_manager_data_edit')) : ?>
                    <textarea name="status_for_sales" style="width: 90%;"><?= $user->account_corporate->status_for_sales ?></textarea>
                <?php else : ?>
                    <?= $user->account_corporate->industry_for_sales ?>
                <? endif ?>
            </td>
        </tr>
        <tr>
            <td>Название компании</td>
            <td>
                <?php if (Yii::app()->user->data()->can('user_sales_manager_data_edit')) : ?>
                    <textarea name="company_name_for_sales" style="width: 90%;"><?= $user->account_corporate->company_name_for_sales ?></textarea>
                <?php else : ?>
                    <?= $user->account_corporate->industry_for_sales ?>
                <? endif ?>
            </td>
        </tr>
        <tr>
            <td>Отрасль компании</td>
            <td>
                <?php if (Yii::app()->user->data()->can('user_sales_manager_data_edit')) : ?>
                    <textarea name="industry_for_sales" style="width: 90%;"><?= $user->account_corporate->industry_for_sales ?></textarea>
                <?php else : ?>
                    <?= $user->account_corporate->industry_for_sales ?>
                <? endif ?>
            </td>
        </tr>

        <?php if (Yii::app()->user->data()->can('user_sales_manager_data_edit')) : ?>
            <tr>
                <td></td>
                <td>
                    <button type="submit" name="save_form" value="true" class="btn btn-success" style="font-weight: bold;">
                        Сохранить данные об аккаунте <?= $user->profile->email ?>, для менеджеров по продажам
                    </button>
                </td>
            </tr>
        <? endif ?>
    </table>
    </form>
<?php endif ?>

<h3>Белый список:</h3>

<small style="color: #888;">Если он не пуст - то пользователь админки будет видеть и сможет редактировать только аккаунты из списка, приглашения, симуляции этих аккаунтов.</small>

<br/>
<br/>

<?php $loginWidget = $this->beginWidget('CActiveForm', [
    'action'      => Yii::app()->request->hostInfo.'/admin_area/user/'.$user->id.'/change-white-list',
    'enableAjaxValidation' => true,
    'clientOptions'        => [
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'afterValidate'    => 'js:changeWhiteListValidation',
    ],
]); ?>

<?php if (Yii::app()->user->data()->can('user_white_list_edit')) : ?>
    <?php echo $loginWidget->error($user, 'emails_white_list'); ?>
    <?php echo $loginWidget->textArea($user, "emails_white_list", [
        'rows' => 5,
        'cols' => 60,
        'style' => 'width: 600px;',
    ]) ?>


    <?php echo CHtml::submitButton( 'Изменить', [
        'class' => 'btn btn-success',
        'style' => 'display: inline-block; vertical-align: top; margin-left: 10px;',
    ]); ?>
<?php else: ?>
    <?= Yii::app()->user->data()->emails_white_list ?>
<?php endif ?>

<?php $this->endWidget(); ?>