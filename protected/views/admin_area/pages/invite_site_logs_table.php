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
<? if($simulation !== null) : ?>
    <h2>Лог операций над приглашением <?= $simulation->invite->id ?></h2>

    <table class="table table-bordered">
        <tr><td>От</td><td> <?= (null !== $simulation->invite && null !== $simulation->invite->ownerUser)?$simulation->invite->ownerUser->profile->email:'-' ?></td></tr>
        <tr><td>для</td><td> <?= (null !== $simulation->invite)?$simulation->invite->email:'-' ?></td></tr>
        <tr><td>Оценка</td><td> <?= (null !== $simulation->invite->getOverall()) ? $simulation->invite->getOverall() : '-'; ?></td></tr>
        <tr><td>Процентиль</td><td> <?= (null !== $simulation->invite->getPercentile()) ? $simulation->invite->getPercentile() : '-'; ?></td></tr>
        <tr><td>Дата создания</td><td> <?= (null !== $simulation->invite->sent_time) ? date("Y-m-d H:i:s", $simulation->invite->sent_time) : '-'; ?></td></tr>
        <tr><td>Дата окончание</td><td> <?= (null !== $simulation->invite->expired_at) ? $simulation->invite->expired_at : '-'; ?></td></tr>
    </table>

    <?php if(null !== $simulation->invite) : ?>
    <?php $this->renderPartial('//admin_area/partials/_invite_actions', [
        'invite' => $simulation->invite,
    ]) ?>
    <?php endif ?>
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

    <hr/>

    <p>
        <strong>Результат теста</strong>:
        <?php if($simulation->invite->is_crashed !== null) : ?>
            <?= ($simulation->invite->is_crashed == 1) ? "Fail" : "Success"; ?>
        <? endif; ?>
    </p>

    <p>
        <strong>Stacktrace</strong>:
        <?php if($simulation->invite->stacktrace !== null) : ?>
            <p>
                <?= $simulation->invite->stacktrace; ?>
            </p>
        <? endif; ?>
    
    </p>

<?php else : ?>
    <h2>
        По данному приглашению не найдена симмуляция.
    </h2>
<?php endif; ?>
</div>