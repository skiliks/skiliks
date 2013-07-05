<? $titlesInvite = [

];
?>

<div class="row fix-top">

    <!-- Invite: -->

    <h2>Лог операций над приглашением</h2>
    <table class="table table-hover">
        <thead>
        <tr>
            <? foreach($titlesInvite as $title) :?>
                <th><?=$title?></th>
            <? endforeach ?>
        </tr>
        </thead>
        <tbody>
        <? /* @var $model Invite*/ ?>
        <? $step = 12; $i = 0; ?>
        <? foreach($logInvite as $itemI) : ?>
            <? $i++ ?>
            <? if($i === $step) : ?>
                <tr>
                    <? foreach($titlesInvite as $title) :?>
                        <th><?=$title?></th>
                    <? endforeach ?>
                </tr>
                <? $i= 0 ?>
            <? endif ?>
            <tr class="invites-row">
                <td></td>
            </tr>
        <? endforeach ?>
        </tbody>
    </table>

    <?php if (0 === count($logInvite)): ?>
        Нет записей.
    <?php endif; ?>

<hr/>

    <!-- Simulation: -->

<?php $titlesSimulation = [
    'ID',
    'Invite id',
    'Тестируемый',
    'Сценарий',
    'Режим',
    'action',
    'real date',
    'front gametime',
    'back gametime',
    'комментарий'
]?>

    <h2>... над симуляцией</h2>
    <table class="table table-hover">
        <thead>
        <tr>
            <? foreach($titlesSimulation as $title) :?>
                <th><?=$title?></th>
            <? endforeach ?>
        </tr>
        </thead>
        <tbody>
        <? /* @var $model Invite*/ ?>
        <? $step = 12; $i = 0; ?>
        <? foreach($logSimulation as $itemS) : ?>
            <? $i++ ?>
            <? if($i === $step) : ?>
                <tr>
                    <? foreach($titlesSimulation as $title) :?>
                        <th><?=$title?></th>
                    <? endforeach ?>
                </tr>
                <? $i= 0 ?>
            <? endif ?>
            <tr class="invites-row">
                <td><?= $itemS->sim_id ?></td>

                <td><?= $itemS->invite_id ?></td>

                <td><?= $itemS->user->profile->email ?>,
                    <?= $itemS->user->profile->firstname ?>
                    <?= $itemS->user->profile->lastname ?></td>

                <td><?= $itemS->scenario_name ?></td>
                <td><?= $itemS->mode ?></td>
                <td><?= $itemS->action ?></td>
                <td><?= $itemS->real_date ?></td>
                <td><?= $itemS->game_time_frontend ?></td>
                <td><?= $itemS->game_time_backend ?></td>
                <td><?= $itemS->comment ?></td>
            </tr>
        <? endforeach ?>
        </tbody>
    </table>
</div>

<?php if (0 === count($logSimulation)): ?>
    Нет записей.
<?php endif; ?>