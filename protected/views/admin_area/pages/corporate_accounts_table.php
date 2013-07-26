<?php $titles = [
    'ID юзера',
    'Имя',
    'Фамилия',
    'Личный email',
    'Корпоративный email',
    'Название компании',
    'Количество приглашений',
    'Тарифный план',
    'Дата регистрации',
    'Дата последнего посещения',
    'Действие',
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
                <td><?= $account->user->id ?></td>
                <td><?= $account->user->profile->firstname ?></td>
                <td><?= $account->user->profile->lastname ?></td>
                <td><?= $account->user->profile->email ?></td>
                <td><?= $account->corporate_email ?></td>
                <td><?= $account->ownership_type ?> "<?= $account->company_name ?>"</td>
                <td><?= $account->invites_limit ?></td>
                <td><?= ($account->tariff) ? $account->tariff->label : '--' ?></td>
                <td><?= date('Y-m-d H:i:s', strtotime($account->user->createtime)) ?></td>
                <td><?= date('Y-m-d H:i:s', strtotime($account->user->lastvisit)) ?></td>
                <td><a href="/admin_area/corporate-account/<?= $account->user_id ?>/invite-limit-logs">Смотреть лог движения инвайтов</a></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>