<?php $titles = [
    'ID юзера',
    'Имя',
    'Фамилия',
    'Статус пользователя',
    'Email',
    'Тип компании',
    'Название компании',
    'Количество приглашений',
    'Количество оплаченных заказов',
    'Количество отправленных приглашений',
    'Количество пройденных полных симуляций "сам-себе"',
    'Количество пройденных полных симуляций "для людей"',
    'Тарифный план',
    'Дата регистрации',
    'Дата последнего посещения',
] ?>
<div class="row fix-top">
    <h2>Корпоративные аккаунты</h2>
    <a class="btn btn-info" style="float: right; margin-right: 100px;" href="/admin_area/export-all-corporate-account-xlsx">
        <i class="icon icon-download-alt icon-white"></i>
        Скачать список корпоративных аккаунтов (xlsx)
    </a>
    <?php $this->widget('CLinkPager',array(
        'header'         => '',
        'pages'          => $pager,
        'maxButtonCount' => 5, // максимальное вол-ко кнопок
    )); ?>

    Страница <?= $page ?> из <?= ceil($totalItems/$itemsOnPage) ?> (<?= $itemsOnPage ?> записей на странице из <?= $totalItems ?>)

    <br/>
    <br/>

    <table class="table table-hover">
        <thead>
        <tr>
            <?php foreach($titles as $title) :?>
            <th><?=$title?></th>
            <?php endforeach ?>
        </tr>
        </thead>
        <tbody>
        <?php /* @var $model Invoice */ ?>
        <?php $step = 12; $i = 0; ?>
        <?php foreach($accounts as $account) : ?>
            <?php /* @var UserAccountCorporate $account */ ?>
            <?php $i++ ?>
            <?php if($i === $step) : ?>
                    <tr>
                        <?php foreach($titles as $title) :?>
                            <th><?=$title?></th>
                        <?php endforeach ?>
                    </tr>
            <?php $i= 0 ?>
            <?php endif ?>
            <tr class="orders-row">
                <td>
                    <i class="icon icon-user" style="opacity: 0.25"></i>
                    <a href="/admin_area/user/<?= $account->user->id ?>/details">
                        <?= $account->user->id ?>
                    </a>
                </td>
                <td>
                    <span class="text-label-200px"><?= $account->user->profile->firstname ?></span>
                </td>
                <td>
                    <span class="text-label-200px"><?= $account->user->profile->lastname ?></span>
                </td>
                <td>
                    <span class="label <?= ($account->user->status == YumUser::STATUS_ACTIVE) ? 'label-warning' : '' ?>"><?= $account->user->getStatusLabel() ?><span>
                </td>
                <td>
                    <span class="text-label-200px"><?= $account->user->profile->email ?></span>
                </td>
                <td><?=$account->ownership_type ?></td>
                <td>"<?= $account->company_name ?>"</td>
                <td style="text-align: center;"><?= $account->invites_limit ?></td>
                <td style="text-align: center;"><?= $account->getNumberOfPaidOrders() ?></td>
                <td style="text-align: center;"><?= $account->getNumberOfInvitationsSent() ?></td>
                <td style="text-align: center;"><?= $account->getNumberOfFullSimulationsForSelf() ?></td>
                <td style="text-align: center;"><?= $account->getNumberOfFullSimulationsForPeople() ?></td>
                <td style="width: 150px;">
                    <?= $account->getActiveTariffPlan()->tariff->label ?>
                </td>
                <td style="width: 140px;">
                    <?= date('Y-m-d H:i:s', $account->user->createtime) ?>
                </td>
                <td>
                    <?= date('Y-m-d H:i:s', $account->user->lastvisit) ?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>