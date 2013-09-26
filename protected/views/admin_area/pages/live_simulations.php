<script>
    setTimeout("location.reload(true);", 15000);
</script>

<?php
    $titles = [
        'Игрок <br/> Тип аккаунта',
        'ID Симуляции <br/> Тип симуляции',
        'Статус приглашения <br/>ID Приглашения',
        'Статус симуляции',
        'Текущее игровое время',
        'Время старта <br/> Время окончания',
        'Запросы',
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

        <?php foreach($allSimulation as $simulations) : ?>
            <?php foreach($simulations as $simulation) : ?>
                <?php $i++; ?>
                <tr>
                    <td>
                        <?php if(!empty($simulation->user)) : ?>
                        <a href="/admin_area/user/<?=$simulation->user->id ?>/details">
                            <?= $simulation->user->profile->firstname." ".$simulation->user->profile->lastname ?>
                        </a>
                        <?php else: ?>
                            <a href="#">Нет юзера</a>
                        <?php endif ?>
                        <br/>
                        <?php
                            $class = "label-warning"; // просто оранжевый заметнее, никакого предупреждения в этом нет
                            if (!empty($simulation->user) && $simulation->user->isCorporate()) {
                                $class = "label-inverse";
                            }
                        ?>

                        <?php if(!empty($simulation->user)) : ?>
                            <a href="mailto: <?= $simulation->user->profile->email ?>" title="Отправить письмо">
                                <i class="icon-envelope "></i>
                            </a>
                        <?php else: ?>
                            <a href="#">Нет юзера</a>
                        <?php endif ?>
                        &nbsp;
                        <span class="label <?= $class ?>">
                            <?= empty($simulation->user)?'------':$simulation->user->getAccountName() ?>
                        </span>
                    </td>
                    <td>
                        <span class="label <?= $simulation->game_type->getSlugCss() ?>" style="margin: 0 auto;">
                            <?= $simulation->game_type->slug?>
                        </span>
                        &nbsp;
                        <a href="/admin_area/simulation/<?=$simulation->id ?>/site-logs" target="_blank">
                            <?=$simulation->id ?>
                        </a>
                    </td>
                    <td>
                        <?php if(isset($simulation->invite->id)) : ?>
                            <span class="label <?= $simulation->invite->getStatusCssClass() ?>">
                                <?= $simulation->invite->getStatusText() ?>
                            </span>
                            &nbsp;
                            <a href="/admin_area/invite/<?=$simulation->invite->id ?>/site-logs" target="_blank">
                                <?=$simulation->invite->id ?>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="label <?= $simulation->getStatusCss() ?>">
                            <?= $simulation->status ?>
                        </span>
                    </td>
                    <td><?= $simulation->getCurrentGameTime() ?></td>
                    <td>
                        <?php if(isset($simulation->start)) echo $simulation->start ?>
                        <br/>
                        <?php if(isset($simulation->end)) echo $simulation->end ?>
                    </td>
                    <td>
                        <a target="_blank" class="btn btn-info"
                           href="/admin_area/simulation/<?=$simulation->id ?>/requests" target="_blank">
                            Логи запросов
                        </a>
                    </td>
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

            <?php endforeach; ?>
        <?php endforeach; ?>

        </table>
    <? else : ?>
        На данный момент нету активных симуляций.
    <? endif; ?>
    <!-- DEV modes: -->


</div>