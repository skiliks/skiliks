<?php
$invites = $models;

$titles = [
    'ID-симуляции',
    'Email работодателя',
    'Email соискателя',
    'ID инвайта',
    'Статус инвайта',
    'Время начала симуляции',
    'Время окончания симуляции',
    'Время окончания tutorial',
    'Тип (название) основного сценария',
    'Оценка',
    'Можно заново стартовать приглашение?',
    'Действие'
] ?>
<div class="row fix-top">
    <h2>Инвайты</h2>

    <?php $this->widget('CLinkPager',array(
        'header'         => '',
        'pages'          => $pager,
        'maxButtonCount' => 5, // максимальное вол-ко кнопок
    )); ?>

    Страница <?= $page ?> из <?= ceil($totalItems/$itemsOnPage) ?> (<?= $itemsOnPage ?> записей на странице из <?= $totalItems ?>)

    <br/>
    <br/>

    <a class="btn btn-primary pull-right" href="/admin_area/invites/save">Экспорт списка</a>
    <table class="table table-hover">
        <thead>
        <tr>
            <?php foreach($titles as $title) :?>
            <th><?=$title?></th>
            <?php endforeach ?>
        </tr>
        </thead>
        <tbody>
        <?php /* @var $invite Invite*/ ?>
        <?php $step = 12; $i = 0; ?>
        <?php foreach($invites as $invite) : ?>
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
                $bgColor = '#ffffff';
                if (false == $invite->can_be_reloaded) {
                    $bgColor = '#FFCC66';
                }
            ?>
        <tr class="invites-row" style="background-color: <?= $bgColor ?>">
            <td><?=(empty($invite->simulation->id)?'Не найден':$invite->simulation->id)?></td>
            <td class="ownerUser-email"><?=(empty($invite->ownerUser->profile->email))?'Не найден':$invite->ownerUser->profile->email?></td>
            <td class="receiverUser-email"><?=(empty($invite->receiverUser->profile->email))?'Не найден':$invite->receiverUser->profile->email?></td>
            <td><?=$invite->id?></td>
            <td><span class="label"><?=$invite->getStatusText()?></span></td>
            <td class="simulation_time-start"><?=(empty($invite->simulation->start)?'---- -- -- --':$invite->simulation->start)?></td>
            <td class="simulation_time-end"><?=(empty($invite->simulation->end)?'---- -- -- --':$invite->simulation->end)?></td>
            <td class="simulation_tutorial_time-end"><?=(empty($invite->tutorial_finished_at)?'---- -- -- --':$invite->tutorial_finished_at)?></td>
            <td><span class="label label-inverse"><?=(empty($invite->scenario->slug)?'Нет данных':$invite->scenario->slug)?></span></td>
            <td><?=$invite->getOverall() ?></td>
            <td>
                <?php
                    $class = 'btn-success';
                    if (false == $invite->can_be_reloaded) {
                        $class = 'btn-warning';
                    }
                ?>
                <a class="btn <?= $class ?>" href="/admin/invite/<?= $invite->id ?>/switch-can-be-reloaded">
                    <i class="icon-refresh"></i>
                    <?= (true == $invite->can_be_reloaded) ? 'yes' : 'no' ?>
                </a>
            </td>
            <td class="actions">
                <div class="btn-group">
                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                        Выбрать
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <?php if(!empty($invite->simulation->id)) : ?>
                        <li>
                            <a href="/static/admin/saveLog/<?=$invite->simulation->id?>">
                                <i class="icon-download-alt"></i> Скачать лог
                            </a>
                        </li>
                        <li>
                            <a target="_blank" href="/admin_area/simulation_detail?sim_id=<?=$invite->simulation->id?>">
                                <i class="icon-star"></i> Открыть оценки
                            </a>
                        </li>
                        <?php if(!empty($invite->receiverUser->profile)) : ?>
                            <li>
                                <a href="/admin_area/invite/calculate/estimate?sim_id=<?=$invite->simulation->id?>&email=<?=$invite->receiverUser->profile->email?>">
                                    <i class="icon-refresh"></i>Пересчитать оценки
                                </a>
                            </li>
                        <?php endif ?>
                        <li>
                            <a href="/admin_area/budget?sim_id=<?=$invite->simulation->id?>">
                                <i class="icon-book"></i> Скачать "Сводный бюджет"(D1)
                            </a>
                        </li>
                        <?php endif ?>
                        <li>
                            <a class="reset-invite" href="/admin_area/invite/reset?invite_id=<?=$invite->id?>">
                                <i class="icon-fast-backward"></i> Откатить инвайт
                            </a>
                        </li>
                        <li style="padding-right: 15px;">
                            <a href="#"><i class="icon-tag"></i> Сменить статус на</a>
                            <?php foreach(Invite::$statusText as $id => $text) : ?>
                                <?php if((string)$id !== $invite->status) : ?>
                                    <a class="action-invite-status" style="padding-left: 50px;"
                                       href="/admin_area/invite/action/status?invite_id=<?=$invite->id?>&status=<?=$id?>">
                                          - <?=$text?>
                                    </a>
                                <?php endif ?>
                            <?php endforeach ?>
                        </li>
                        <li>
                            <a href="/admin_area/invite/<?= $invite->id?>/site-logs">
                                <i class="icon-list"></i> Смотреть логи сайта
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>