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

    <h2>Лог операций над приглашением <?= $simulation->invite->id ?></h2>

    <table class="table table-bordered">
        <tr><td>От</td><td> <?= $simulation->invite->ownerUser->profile->email ?></td></tr>
        <tr><td>для</td><td> <?= $simulation->invite->email ?></td></tr>
        <tr><td>Оценка</td><td> <?= (null !== $simulation->invite->getOverall()) ? $simulation->invite->getOverall() : '-'; ?></td></tr>
        <tr><td>Процентиль</td><td> <?= (null !== $simulation->invite->getPercentile()) ? $simulation->invite->getPercentile() : '-'; ?></td></tr>
    </table>

    <?php $this->renderPartial('//admin_area/partials/_invite_actions', [
        'invite' => $simulation->invite,
    ]) ?>

    <br/>
    <br/>

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
        'simulation'       => $simulation
    ]) ?>
