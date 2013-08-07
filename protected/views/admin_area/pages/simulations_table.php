<?php $titles = [
    'ID-симуляции',
    'Email соискателя, игрока',
    'Время начала симуляции',
    'Время конца симуляции',
    'Тип симуляции',
    'Оценка',
    'ID-инвайта',
];
?>
<div class="row fix-top">
    <h2>Симуляции</h2>

    <?php $this->widget('CLinkPager',array(
        'header'         => '',
        'pages'          => $pager,
        'maxButtonCount' => 5, // максимальное вол-ко кнопок
    )); ?>

    Страница <?= $page ?> из <?= ceil($totalItems/$itemsOnPage) ?> (<?= $itemsOnPage ?> записей отображено, найдено <?= $totalItems ?>)

    <br/>
    <br/>

    <form action="" method="post" style="display: inline-block;">
        <i class="icon-filter"></i> &nbsp; email соискателя:
        <input name="email-for-filtration" value="<?= $emailForFiltration ?>"/>
        <input type="submit" value="фильтровать" class="btn btn-warning"/>
    </form>

    <form action="" method="post" style="display: inline-block;">
        <input type="submit" value="Сбросить фильтр" class="btn btn-warning"/>
    </form>

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
            <?php
                $isSimulationBroken = (false === empty($simulation->start) && empty($simulation->end));

                $bgColor = '#ffffff';
                if ($isSimulationBroken) {
                    $bgColor = '#FFFF66';
                }
            ?>
            <tr class="invites-row" style="background-color: <?= $bgColor ?>">
                <td><?= (empty($simulation->id) ? 'Не найден' : $simulation->id)?></td>
                <td class="ownerUser-email"><?= (empty($simulation->user->profile->email)) ? 'Не найден':$simulation->user->profile->email ?></td>
                <td class="simulation_time-start"><?= (empty($simulation->start) ? '---- -- -- --' : $simulation->start) ?></td>
                <td class="simulation_time-end" style="text-align: center;">
                    <?php if ($isSimulationBroken) : ?>
                        <a href="/admin_area/simulation/<?= $simulation->id ?>/fixEndTime"
                           class="btn btn-success">
                            <i class="icon-ok icon-white"></i> &nbsp; set(0001-01-01 01:01:01)
                        </a>
                    <?php else: ?>
                        <?= (empty($simulation->end) ? '--' : $simulation->end) ?>
                    <?php endif ?>

                </td>
                <td><span class="label <?= $simulation->game_type->getSlugCss() ?>"><?= $simulation->game_type->slug?></span></td>
                <td><?= (null!== $simulation->invite) ? $simulation->invite->getOverall() : '-'?></td>
                <td><?= (isset($invites[$simulation->id])) ? $invites[$simulation->id] : 'Не найдено' ?></td>
                <td>
                    <a href="/admin_area/simulation/<?= $simulation->id?>/site-logs">Логи сайта</a><br/>
                    <a href="/admin_area/simulation/<?= $simulation->id?>/requests">Запросы</a>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>