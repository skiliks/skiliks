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

    <?php
        if (null !== $simulation) {
            $this->renderPartial('//admin_area/partials/_user_info', ['simulation' => $simulation]);
        }
    ?>
    <h2>Логи действий над симуляцией <?= $simulation->id ?></h2>

    <?php $this->renderPartial('//admin_area/partials/_simulation_log_buttons', [
        'simulation' => $simulation,
    ]) ?>

<?php /*
    <a class="btn btn-info" target="_blank" href="/admin/displayLog/<?= $simulation->id?>">
        Смотреть логи в виде таблиц на сайте
    </a>
 */ ?>
     <br/>
     <br/>

    <table class="table table-hover table-bordered">
        <thead>
        <tr style="background-color: #EEE">
            <?php foreach($titlesSimulation as $title) :?>
                <th><?= $title ?></th>
            <?php endforeach ?>
        </tr>
        </thead>
        <tbody>
        <?php /* @var $model Invite*/ ?>
        <?php $step = 12; $i = 0; ?>
        <?php foreach($logSimulation as $itemS) : ?>
            <?php $i++ ?>
            <?php if($i === $step) : ?>
                <tr style="background-color: #EEE">
                    <?php foreach($titlesSimulation as $title) :?>
                        <th><?=$title?></th>
                    <?php endforeach ?>
                </tr>
                <?php $i= 0 ?>
            <?php endif ?>
            <tr class="invites-row">
                <td><?= $itemS->sim_id ?></td>

                <td><?= $itemS->invite_id ?></td>

                <td><?= ($itemS->user instanceof YumUser) ? $itemS->user->profile->email : '-' ?>,
                    <?= ($itemS->user instanceof YumUser) ?  $itemS->user->profile->firstname : '-' ?>
                    <?= ($itemS->user instanceof YumUser) ? $itemS->user->profile->lastname : '-' ?></td>

                <td><span class="label label-inverse"><?= $itemS->scenario_name ?></span></td>
                <td><span class="label"><?= $itemS->mode ?></span></td>
                <td><?= $itemS->action ?></td>
                <td><?= $itemS->real_date ?></td>
                <td><?= $itemS->game_time_frontend ?></td>
                <td><?= $itemS->game_time_backend ?></td>
                <td><?= $itemS->comment ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>

<?php if (0 === count($logSimulation)): ?>
    <div style="text-align: center; width: 100%;">Нет записей.</div>
<?php endif; ?>