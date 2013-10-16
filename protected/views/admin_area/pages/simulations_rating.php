<?php $titles = [
    'ID инвайта',
    'Sim. ID',
    'Email соискателя',
    'Время начала симуляции',
    'Время конца симуляции',
    'Сценарий',
    'Статус',
    'Дата',
    'Оценка',
    'Процентиль'
];
?>
<div class="row fix-top">
    <h2>Рейтинг симуляций</h2>

    Всего (<?= count($simulations) ?>) симуляций.

    <a class="btn btn-info" style="float: right; margin-right: 100px;" href="/admin_area/simulations/rating/csv">
        <i class='icon icon-download-alt icon-white'></i>
        Скачать рейтинг в CSV
    </a>

    <br/>
    <br/>

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

        <?php if (0 == count($simulations)): ?>
            <tr>
                <td colspan="<?= count($titles) ?>">Нет результатов.</td>
            </tr>
        <?php endif; ?>

        <?php /* @var $model Invite*/ ?>
        <?php /* @var Simulation[] $simulations*/ ?>
        <?php $step = 8; $i = 0; ?>
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
            ?>
            <tr class="invites-row">

                <!-- IDs { -->
                <td style="width: 80px;">
                    <i class="icon icon-tag" style="opacity: 0.1" title="Invite ID"></i>
                    <?php if (null === $simulation->invite): ?>
                        --
                    <?php else: ?>
                        <a href="/admin_area/invite/<?= $simulation->invite->id?>/site-logs">
                            <?= $simulation->invite->id ?>
                        </a>
                    <?php endif; ?>
                </td>
                <td>
                    <i class="icon icon-check" style="opacity: 0.1" title="Simulation ID"></i>
                    <a href="/admin_area/simulation/<?= $simulation->id?>/site-logs">
                        <?= $simulation->id?>
                    </a>
                </td>
                <!-- IDs } -->

                <td class="ownerUser-email">
                    <?= (empty($simulation->user->profile->email)) ? 'Не найден':$simulation->user->profile->email ?>
                </td>
                <td class="simulation_time-start">

                    <?php
                        $today = date('Y-m-d');
                        $simulationEndDay = date('Y-m-d', strtotime($simulation->end));
                    ?>

                    <?= (empty($simulation->start) ? '--' : $simulation->start) ?>
                </td>
                <td>
                    <?= (empty($simulation->end) ? '--' : $simulation->end) ?>
                </td>
                <td>
                    <span class="label <?= $simulation->game_type->getSlugCss() ?>">
                        <?= $simulation->game_type->slug?>
                    </span>
                </td>

                <td>
                    <span class="label <?= $simulation->getStatusCss() ?>">
                        <?= $simulation->status ?>
                    </span>
                <td>
                    <?php if ($today == $simulationEndDay) : ?>
                        <span class="label label-warning">TODAY!</span>
                    <?php endif ?></td>
                </td>

                <td>
                    <?= (null!== $simulation->invite && null !== $simulation->invite->getOverall())
                        ? $simulation->invite->getOverall() : '--'; ?>
                </td>
                <td>
                    <?= (null!== $simulation->invite && null !== $simulation->invite->getPercentile())
                        ? $simulation->invite->getPercentile() : '--'; ?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>