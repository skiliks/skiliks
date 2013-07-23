<? $titles = [
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
            <? foreach($titles as $title) :?>
            <th><?=$title?></th>
            <? endforeach ?>
        </tr>
        </thead>
        <tbody>
        <? /* @var $model Invoice */ ?>
        <? $step = 12; $i = 0; ?>
        <? foreach($logs as $log) : ?>
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
                <td><?= $log->date ?></td>
                <td><?= $log->amount ?> <?= $log->direction ?> до <?= $log->limit_after_transaction ?></td>
                <td><?= $log->action ?></td>
            </tr>
        <? endforeach ?>
        </tbody>
    </table>
</div>