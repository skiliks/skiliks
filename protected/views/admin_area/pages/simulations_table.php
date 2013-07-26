<?php $titles = [
    'ID-симуляции',
    'Email соискателя, игрока',
    'Время начала симуляции',
    'Время конца симуляции',
    'ID-инвайта',
] ?>
<div class="row fix-top">
    <h2>Симуляции</h2>
    <table class="table table-hover">
        <thead>
        <tr>
            <?php foreach($titles as $title) :?>
                <th><?=$title?></th>
            <?php endforeach ?>
        </tr>
        </thead>
        <tbody>
        <?php /* @var $model Invite*/ ?>
        <?php $step = 12; $i = 0; ?>
        <?php foreach($simulations as $simulation) : ?>
            <?php $i++ ?>
            <?php if($i === $step) : ?>
                <tr>
                    <?php foreach($titles as $title) :?>
                        <th><?=$title?></th>
                    <?php endforeach ?>
                </tr>
                <?php $i= 0 ?>
            <?php endif ?>
            <tr class="invites-row">
                <td><?= (empty($simulation->id) ? 'Не найден' : $simulation->id)?></td>
                <td class="ownerUser-email"><?= (empty($simulation->user->profile->email)) ? 'Не найден':$simulation->user->profile->email ?></td>
                <td class="simulation_time-start"><?= (empty($simulation->start) ? '---- -- -- --' : $simulation->start) ?></td>
                <td class="simulation_time-end"><?= (empty($simulation->end) ? '---- -- -- --' : $simulation->end) ?></td>
                <td><?= (isset($invites[$simulation->id])) ? $invites[$simulation->id] : 'Не найдено' ?></td>
                <td><a href="/admin_area/simulation/<?= $simulation->id?>/site-logs">Логи сайта</a></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>