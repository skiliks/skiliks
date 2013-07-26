<?php $titles = [
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
    'Действие'
] ?>
<div class="row fix-top">
    <h2>Инвайты</h2>
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
        <?php /* @var $model Invite*/ ?>
        <?php $step = 12; $i = 0; ?>
        <?php foreach($models as $model) : ?>
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
            <td><?=(empty($model->simulation->id)?'Не найден':$model->simulation->id)?></td>
            <td class="ownerUser-email"><?=(empty($model->ownerUser->profile->email))?'Не найден':$model->ownerUser->profile->email?></td>
            <td class="receiverUser-email"><?=(empty($model->receiverUser->profile->email))?'Не найден':$model->receiverUser->profile->email?></td>
            <td><?=$model->id?></td>
            <td><span class="label"><?=$model->getStatusText()?></span></td>
            <td class="simulation_time-start"><?=(empty($model->simulation->start)?'---- -- -- --':$model->simulation->start)?></td>
            <td class="simulation_time-end"><?=(empty($model->simulation->end)?'---- -- -- --':$model->simulation->end)?></td>
            <td class="simulation_tutorial_time-end"><?=(empty($model->tutorial_finished_at)?'---- -- -- --':$model->tutorial_finished_at)?></td>
            <td><span class="label label-inverse"><?=(empty($model->scenario->slug)?'Нет данных':$model->scenario->slug)?></span></td>
            <td><?=$model->getOverall() ?></td>
            <td class="actions">
                <div class="btn-group">
                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                        Выбрать
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <?php if(!empty($model->simulation->id)) : ?>
                        <li>
                            <a href="/static/admin/saveLog/<?=$model->simulation->id?>">
                                <i class="icon-download-alt"></i> Скачать лог
                            </a>
                        </li>
                        <li>
                            <a target="_blank" href="/admin_area/simulation_detail?sim_id=<?=$model->simulation->id?>">
                                <i class="icon-star"></i> Открыть оценки
                            </a>
                        </li>
                        <?php if(!empty($model->receiverUser->profile)) : ?>
                            <li>
                                <a href="/admin_area/invite/calculate/estimate?sim_id=<?=$model->simulation->id?>&email=<?=$model->receiverUser->profile->email?>">
                                    <i class="icon-refresh"></i>Пересчитать оценки
                                </a>
                            </li>
                        <?php endif ?>
                        <li>
                            <a href="/admin_area/budget?sim_id=<?=$model->simulation->id?>">
                                <i class="icon-book"></i> Скачать "Сводный бюджет"(D1)
                            </a>
                        </li>
                        <?php endif ?>
                        <li>
                            <a class="reset-invite" href="/admin_area/invite/reset?invite_id=<?=$model->id?>">
                                <i class="icon-fast-backward"></i> Откатить инвайт
                            </a>
                        </li>
                        <li style="padding-right: 15px;">
                            <a href="#"><i class="icon-tag"></i> Сменить статус на</a>
                            <?php foreach(Invite::$statusText as $id => $text) : ?>
                                <?php if((string)$id !== $model->status) : ?>
                                    <a class="action-invite-status" style="padding-left: 50px;"
                                       href="/admin_area/invite/action/status?invite_id=<?=$model->id?>&status=<?=$id?>">
                                          - <?=$text?>
                                    </a>
                                <?php endif ?>
                            <?php endforeach ?>
                        </li>
                        <li>
                            <a href="/admin_area/invite/<?= $model->id?>/site-logs">
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