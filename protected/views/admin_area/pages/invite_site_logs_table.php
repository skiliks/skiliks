<?php $titlesInvite = [
    'ID инвайта',
    'статус',
    'ID симуляции',
    'action',
    'Дата и время',
    'комментарий',
];
?>

<div class="row fix-top">

    <!-- Invite: -->

    <h2>Лог операций над приглашением</h2>
    <table class="table table-hover">
        <thead>
        <tr>
            <?php foreach($titlesInvite as $title) :?>
                <th><?=$title?></th>
            <?php endforeach ?>
        </tr>
        </thead>
        <tbody>
        <?php /* @var $model Invite*/ ?>
        <?php $step = 12; $i = 0; ?>
        <?php foreach($logInvite as $itemI) : ?>
            <?php $i++ ?>
            <?php if($i === $step) : ?>
                <tr>
                    <?php foreach($titlesInvite as $title) :?>
                        <th><?=$title?></th>
                    <?php endforeach ?>
                </tr>
                <?php $i= 0 ?>
            <?php endif ?>
            <tr class="invites-row">
                <td><?= $itemI->invite_id ?></td>
                <td><?= $itemI->status ?></td>
                <td><?= $itemI->sim_id ?></td>
                <td><?= $itemI->action ?></td>
                <td><?= $itemI->real_date ?></td>
                <td><?= $itemI->comment ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>

    <?php if (0 === count($logInvite)): ?>
        <div style="text-align: center; width: 100%;">Нет записей.</div>
    <?php endif; ?>

<hr/>

    <!-- Simulation: -->

    <?php $this->renderPartial('//admin_area/pages/simulation_site_logs_table', [
        'logSimulation'    => $logSimulation,
    ]) ?>
