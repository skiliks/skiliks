<script>
    setTimeout("location.reload(true);", 15000);

</script>
<div>
    <h1>Live Simulations</h1>

    <? if(!empty($full_simulations) || !empty($lite_simulations) || !empty($tutorial_simulations)) : ?>

        <table class="table">
            <tr>
                <th>Игрок</th>
                <th>ID Симуляции</th>
                <th>ID Приглашения</th>
                <th>Тип симуляции</th>
                <th>Время старта</th>
                <th>Время окончания</th>
                <th>Текущее игровое время</th>
            </tr>

        <? foreach($full_simulations as $simulation) : ?>
            <tr>
                <td><a href="mailto: <?=$simulation->user->profile->email ?>">
                    <?= $simulation->user->profile->firstname." ".$simulation->user->profile->lastname ?>
                    </a>
                </td>
                <td>
                    <a href="/admin_area/simulation/<?=$simulation->id ?>/site-logs" target="_blank">
                        <?=$simulation->id ?>
                    </a>
                </td>
                <td>
                    <? if(isset($simulation->invite->id)) : ?>
                        <a href="/admin_area/invite/<?=$simulation->invite->id ?>/site-logs" target="_blank">
                            <?=$simulation->invite->id ?>
                        </a>
                    <? endif; ?>
                </td>
                <td><? if(isset($simulation->game_type->slug)) echo $simulation->game_type->slug ?></td>
                <td><? if(isset($simulation->start)) echo $simulation->start ?></td>
                <td><? if(isset($simulation->end)) echo $simulation->end ?></td>
                <td><? echo $simulation->getCurrentGameTime() ?></td>
            </tr>
        <? endforeach; ?>

        <? foreach($lite_simulations as $simulation) : ?>
            <tr>
                <td><a href="mailto: <?=$simulation->user->profile->email ?>">
                        <?= $simulation->user->profile->firstname." ".$simulation->user->profile->lastname ?>
                    </a>
                </td>
                <td>
                    <a href="/admin_area/simulation/<?=$simulation->id ?>/site-logs" target="_blank">
                        <?=$simulation->id ?>
                    </a>
                </td>
                <td>
                    <? if(isset($simulation->invite->id)) : ?>
                        <a href="/admin_area/invite/<?=$simulation->invite->id ?>/site-logs" target="_blank">
                            <?=$simulation->invite->id ?>
                        </a>
                    <? endif; ?>
                </td>
                <td><? if(isset($simulation->game_type->slug)) echo $simulation->game_type->slug ?></td>
                <td><? if(isset($simulation->start)) echo $simulation->start ?></td>
                <td><? if(isset($simulation->end)) echo $simulation->end ?></td>
                <td><? echo $simulation->getCurrentGameTime() ?></td>
            </tr>
        <? endforeach; ?>

        <? foreach($tutorial_simulations as $simulation) : ?>
            <tr>
                <td><a href="mailto: <?=$simulation->user->profile->email ?>">
                        <?= $simulation->user->profile->firstname." ".$simulation->user->profile->lastname ?>
                    </a>
                </td>
                <td>
                    <a href="/admin_area/simulation/<?=$simulation->id ?>/site-logs" target="_blank">
                        <?=$simulation->id ?>
                    </a>
                </td>
                <td>
                    <? if(isset($simulation->invite->id)) : ?>
                        <a href="/admin_area/invite/<?=$simulation->invite->id ?>/site-logs" target="_blank">
                            <?=$simulation->invite->id ?>
                        </a>
                    <? endif; ?>
                </td>
                <td><? if(isset($simulation->game_type->slug)) echo $simulation->game_type->slug ?></td>
                <td><? if(isset($simulation->start)) echo $simulation->start ?></td>
                <td><? if(isset($simulation->end)) echo $simulation->end ?></td>
                <td><? echo $simulation->getCurrentGameTime() ?></td>
            </tr>
        <? endforeach; ?>

        </table>
    <? else : ?>
        На данный момент нету активных симуляций.
    <? endif; ?>
    <!-- DEV modes: -->


</div>