<?php $titles = [
    'Дата',
    'Транзакция',
    'Место спровоцировавцее транзакцию (в коде проекта)',
] ?>
<div class="row fix-top">

    <h2>Логи движения инвайтов <?= $account->ownership_type ?> "<?= $account->company_name ?>"</h2>

    <strong>Текущий тарифный план:</strong> <?= $account->tariff->label ?><br/>
    <strong>Текущее количество доступных приглашений:</strong> <?= $account->invites_limit ?><br/>

    <br/>

    <strong>Пользователь:</strong> <?= $account->user->profile->firstname ?> <?= $account->user->profile->lastname ?><br/>
    <strong>Личный email:</strong> <?= $account->user->profile->email ?><br/>
    <strong>Корпоративный email:</strong> <?= $account->corporate_email ?><br/>

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
                <td><?= $log->amount ?> <?= $log->direction ?> до <?= $log->limit_after_transaction ?></td>
                <td><?= $log->action ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>