<?php $titles = [
    'ID юзера',
    'Имя',
    'Фамилия',
    'Статус пользователя',
    'Email',
    'Тип компании',
    'Название компании',
    'Статус',
    'Количество приглашений',
    'Тарифный план',
    'Дата регистрации',
    'Дата последнего посещения',
    'Действия',
] ?>
<div class="row fix-top">
    <h2>Корпоративные аккаунты</h2>

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
                <td style="width: 150px;">
                    <?php if ($account->tariff_expired_at < date('Y-m-d H:i:s')) : ?>
                        Просрочен
                    <?php else: ?>
                        <?= ($account->tariff) ? $account->tariff->label : '--' ?>
                    <?php endif; ?>

                    <div class="btn-group">
                        <a class="btn dropdown-toggle btn-success" data-toggle="dropdown" href="#">
                            <i class="icon-refresh"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="/static/cheats/set-tariff/<?= Tariff::SLUG_LITE ?>">
                                    <i class="icon-pencil"></i>
                                    Назначить Пробный тариф</a>
                            </li>
                            <li>
                                <a href="/static/cheats/set-tariff/<?= Tariff::SLUG_STARTER ?>">
                                    <i class="icon-pencil"></i>
                                    Назначить Малый тариф</a>
                            </li>
                            <li>
                                <a href="/static/cheats/set-tariff/<?= Tariff::SLUG_PROFESSIONAL ?>">
                                    <i class="icon-pencil"></i>
                                    Назначить Профессиональный тариф</a>
                            </li>
                            <li>
                                <a href="/static/cheats/set-tariff/<?= Tariff::SLUG_BUSINESS ?>">
                                    <i class="icon-pencil"></i>
                                    Назначить Бизнес тариф</a>
                            </li>
                            <li>
                                <a href="/static/cheats/set-tariff/" style="color: #b00;">
                                    <i class="icon-trash"></i>
                                    Очистить поле тариф</a>
                            </li>
                        </ul>
                    </div>
                </td>
                <td style="width: 140px;">
                    <?= date('Y-m-d H:i:s', $account->user->createtime) ?>
                </td>
                <td>
                    <?= date('Y-m-d H:i:s', $account->user->lastvisit) ?>
                </td>
                <td style="width: 170px;">
                     <a class="btn btn-info" style="width: 140px;"
                         href="/admin_area/corporate-account/<?= $account->user_id ?>/invite-limit-logs">
                         <strong>Смотреть лог движения инвайтов</strong></a>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>