<script>
    setTimeout("location.reload(true);", 15000);
</script>

<?php
    $titles = [
        'Игрок',
        'ID Симуляции',
        'Запросы',
        'ID Приглашения',
        'Тип симуляции',
        'Время старта',
        'Время окончания',
        'Текущее игровое время',
    ];
?>

<div>
    <h1>Live Simulations</h1>

    <? if(!empty($full_simulations) || !empty($lite_simulations) || !empty($tutorial_simulations)) : ?>

        <table class="table">
            <tr>
                <?php foreach ($titles as $title) : ?>
                    <th><?= $title ?></th>
                <?php endforeach ?>
            </tr>

        <?php $allSimulation = [$full_simulations, $lite_simulations, $tutorial_simulations]; ?>

        <?php $i = 0 ?>

        <? foreach($allSimulation as $simulations) : ?>
            <? foreach($simulations as $simulation) : ?>
                <?php $i++; ?>
                <tr>
                    <td>
                        <a href="/admin_area/user/<?=$simulation->user->id ?>/details">
                            <?= $simulation->user->profile->firstname." ".$simulation->user->profile->lastname ?>
                        </a>
                        &nbsp;
                        <a href="mailto: <?= $simulation->user->profile->email ?>" class="btn btn-success" title="Отправить письмо">
                            <i class="icon-envelope icon-white"></i>
                        </a>
                    </td>
                    <td>
                        <a href="/admin_area/simulation/<?=$simulation->id ?>/site-logs" target="_blank">
                            <?=$simulation->id ?>
                        </a>
                    </td>
                    <td>
                        <a target="_blank" class="btn btn-info"
                           href="/admin_area/simulation/<?=$simulation->id ?>/requests" target="_blank">
                            Логи запросов
                        </a>
                    </td>
                    <td>
                        <? if(isset($simulation->invite->id)) : ?>
                            <a href="/admin_area/invite/<?=$simulation->invite->id ?>/site-logs" target="_blank">
                                <?=$simulation->invite->id ?>
                            </a>
                        <? endif; ?>
                    </td>
                    <td>
                        <span class="label <?= $simulation->game_type->getSlugCss() ?>">
                            <?= $simulation->game_type->slug?>
                        </span>
                    </td>
                    <td><? if(isset($simulation->start)) echo $simulation->start ?></td>
                    <td><? if(isset($simulation->end)) echo $simulation->end ?></td>
                    <td><? echo $simulation->getCurrentGameTime() ?></td>
                </tr>

                <?php // проставляем заголовки таблицы в каждой 12й строке - для удобства чтения данных ?>
                <?php if ($i == 13) : ?>
                    <tr>
                        <?php foreach ($titles as $title) : ?>
                            <th><?= $title ?></th>
                        <?php endforeach ?>
                    </tr>
                    <?php $i = 0 ?>
                <?php endif ?>

            <? endforeach; ?>
        <? endforeach; ?>

        </table>
    <? else : ?>
        На данный момент нету активных симуляций.
    <? endif; ?>
    <!-- DEV modes: -->


</div>