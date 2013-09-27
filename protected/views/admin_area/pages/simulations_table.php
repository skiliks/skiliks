<?php $titles = [
    'ID инвайта, <br/>Sim. ID',
    'Email соискателя, игрока',
    'Время начала симуляции <br/> Время конца симуляции',
    'Сценарий: статус',
    'Оценка',
    '',
    'Аварийная панель',
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

    <?php // hack to use pager with post requests { ?>
    <script type="text/javascript">
        $('.yiiPager .page').removeClass('selected');
        $('.yiiPager .page:eq(<?= $page - 1 ?>)').addClass('selected');
        $('.yiiPager a').click(function(e) {
            e.preventDefault();
            var page = $(this).text();
            $('#simulations-filter-page').attr('value', page);
            $('#simulations-filter').submit();
        });
    </script>
    <?php // hack to use pager with post requests } ?>

    <br/>
    <br/>

    <form id="simulations-filter" action="/admin_area/simulations" method="post" style="display: inline-block;">
        <input id="simulations-filter-page" type="hidden" name="page" value="<?= $page ?>" />

        <table class="table table-bordered">
            <tr>
                <td> <i class="icon-filter"></i> &nbsp; email соискателя: </td>
                <td> <input name="email-for-filtration" value="<?= $emailForFiltration  ?>"/> </td>
                <td> <i class="icon-filter"></i> &nbsp; Simulation id: </td>
                <td> <input name="simulation_id" value="<?= $simulation_id ?>" style="width: 60px;"/> </td>
            </tr>
            <tr>
                <td> Исключить прохождения разработчиков: </td>
                <td> <input type="checkbox" name="exclude_developers_emails"
                        <?= (isset($formFilters['exclude_developers_emails']) && $formFilters['exclude_developers_emails'])
                            ? 'checked="checked"' : ''; ?>
                        /> </td>
                <td>Показывать только завершенные:</td>
                <td><input type="checkbox" name="show_simulation_with_end_time"
                        <?= (isset($formFilters['show_simulation_with_end_time']) && $formFilters['show_simulation_with_end_time'])
                            ? 'checked="checked"' : ''; ?>
                        /></td>
            </tr>
        </table>

        <table class="table table-bordered invite-statuses">
            <tr class="scenarios-list">
                <td> Показывать сценарии: </td>
                <td>
                    <span class="btn btn-warning btn-check-all">Отметить все</span>
                    <span class="btn btn-warning btn-uncheck-all">Снять все</span>
                    <script type="text/javascript">
                        $('.select-all-statuses').click(function(){
                            $('.scenarios-list input').attr('checked', 'checked');
                        });
                        $('.deselect-all-statuses').click(function(){
                            $('.scenarios-list input').removeAttr('checked');
                        });
                    </script>
                </td>
                <td>
                    <?= Scenario::TYPE_FULL ?>
                    <input type="checkbox"
                           name="scenario[<?= Scenario::TYPE_FULL ?>]"
                        <?= ($formFilters['scenario'][Scenario::TYPE_FULL]) ? 'checked="checked"' : ''; ?>
                        />
                </td>
                <td>
                    <?= Scenario::TYPE_LITE ?>
                    <input type="checkbox"
                           name="scenario[<?= Scenario::TYPE_LITE ?>]"
                        <?= ($formFilters['scenario'][Scenario::TYPE_LITE]) ? 'checked="checked"' : ''; ?>
                        />
                </td>
            <tr>
        </table>

        <input type="submit" value="Фильтровать" class="btn btn-warning"/>
        &nbsp; &nbsp; &nbsp;
        <input type="submit" value="Сбросить фильтр" name="clear_form" class="btn btn-warning clear_filter_button"/>
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
                    <?php endif; ?>                    <br/>
                    <i class="icon icon-check" style="opacity: 0.1" title="Simulation ID"></i>
                    <a href="/admin_area/simulation/<?= $simulation->id?>/site-logs">
                        <?= $simulation->id?>
                    </a>
                </td>
                <!-- IDs } -->

                <td class="ownerUser-email"><?= (empty($simulation->user->profile->email)) ? 'Не найден':$simulation->user->profile->email ?></td>
                <td class="simulation_time-start">
                    <?= (empty($simulation->start) ? '--' : $simulation->start) ?>
                    <br/>
                    <?= (empty($simulation->end) ? '--' : $simulation->end) ?>
                </td>
                <td>
                    <span class="label <?= $simulation->game_type->getSlugCss() ?>">
                        <?= $simulation->game_type->slug?>
                    </span>
                    :
                    <span class="label <?= $simulation->getStatusCss() ?>">
                        <?= $simulation->status ?>
                    </span>
                </td>

                <td>
                    <?= (null!== $simulation->invite && null !== $simulation->invite->getOverall())
                        ? $simulation->invite->getOverall() : '--'; ?>
                </td>

                <td>
                    <a class="btn btn-info" href="/admin_area/simulation/<?= $simulation->id?>/site-logs">
                        Смотреть логи сайта
                    </a>
                    &nbsp;&nbsp;
                    <a class="btn btn-info" href="/admin_area/simulation/<?= $simulation->id?>/requests">
                        Смотреть запросы
                    </a>
                </td>
                <td>
                    <?= $simulation->is_emergency_panel_allowed ? 'Разрешена' : 'Запрешена' ?>
                    <a class="btn btn-success" href="/admin_area/simulation/set-emergency/<?= $simulation->id ?>">
                        <?= $simulation->is_emergency_panel_allowed ? 'Запретить' : 'Разрешить' ?>
                    </a>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>