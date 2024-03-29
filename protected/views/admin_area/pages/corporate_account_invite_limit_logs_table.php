<?php $titles = [
    'Дата',
    'Транзакция',
    'Место спровоцировавцее транзакцию (в коде проекта)'
] ?>
<div class="row fix-top">

    <h2>Логи движения инвайтов <?= $account->ownership_type ?> "<?= $account->company_name ?>"</h2>

    <a href="/admin_area/user/<?= $account->user_id ?>/details">
        <i class="icon icon-arrow-left"></i> К аккаунту
    </a>

    <br/>
    <br/>

    <table class="table table-bordered" style="width: 50%">
        <tr>
            <td>Текущее количество доступных приглашений</td>
            <td><?= $account->getTotalAvailableInvitesLimit() ?></td>
        <tr>
            <td>Пользователь</td>
            <td><?= $account->user->profile->firstname ?> <?= $account->user->profile->lastname ?></td>
        </tr>
        <tr>
            <td>email</td>
            <td><?= $account->user->profile->email ?></td>
        </tr>
    <table>

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
        <?php foreach($logs as $log) : ?>
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
                <td><?= $log->date ?></td>
                <td><?= $log->amount ?> <?= $log->direction ?> до <?= ($log->limit_after_transaction) ?></td>
                <td><?= $log->message ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>