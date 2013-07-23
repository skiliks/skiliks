<? $titles = [
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
    <table class="table table-hover">
        <thead>
        <tr>
            <? foreach($titles as $title) :?>
            <th><?=$title?></th>
            <? endforeach ?>
        </tr>
        </thead>
        <tbody>
        <? /* @var $model Invoice */ ?>
        <? $step = 12; $i = 0; ?>
        <? foreach($accounts as $account) : ?>
            <? $i++ ?>
            <? if($i === $step) : ?>
                    <tr>
                        <? foreach($titles as $title) :?>
                            <th><?=$title?></th>
                        <? endforeach ?>
                    </tr>
            <? $i= 0 ?>
            <? endif ?>
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
        <? endforeach ?>
        </tbody>
    </table>
</div>