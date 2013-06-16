<? $titles = [
    'ID-симуляции',
    'Email работодателя',
    'Email соискателя',
    'ID инвайта',
    'Статус инвайта',
    'Время начала симуляции',
    'Время конца симуляции',
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
            <? foreach($titles as $title) :?>
            <th><?=$title?></th>
            <? endforeach ?>
        </tr>
        </thead>
        <tbody>
        <? /* @var $model Invite*/ ?>
        <? $step = 12; $i = 0; ?>
        <? foreach($models as $model) : ?>
        <? $i++ ?>
        <? if($i === $step) : ?>
                <tr>
                    <? foreach($titles as $title) :?>
                        <th><?=$title?></th>
                    <? endforeach ?>
                </tr>
        <? $i= 0 ?>
        <? endif ?>
        <tr class="invites-row">
            <td><?=(empty($model->simulation->id)?'Не найден':$model->simulation->id)?></td>
            <td class="ownerUser-email"><?=(empty($model->ownerUser->profile->email))?'Не найден':$model->ownerUser->profile->email?></td>
            <td class="receiverUser-email"><?=(empty($model->receiverUser->profile->email))?'Не найден':$model->receiverUser->profile->email?></td>
            <td><?=$model->id?></td>
            <td><span class="label"><?=$model->getStatusText()?></span></td>
            <td class="simulation_time-start"><?=(empty($model->simulation->start)?'---- -- -- --':$model->simulation->start)?></td>
            <td class="simulation_time-end"><?=(empty($model->simulation->end)?'---- -- -- --':$model->simulation->end)?></td>
            <td><span class="label label-inverse"><?=(empty($model->scenario->slug)?'Нет данных':$model->scenario->slug)?></span></td>
            <td><?=$model->getOverall() ?></td>
            <td class="actions">
                <div class="btn-group">
                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                        Выбрать
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <? if(!empty($model->simulation->id)) : ?>
                        <li>
                            <a href="/static/admin/saveLog/<?=$model->simulation->id?>">Скачать логи</a>
                        </li>
                        <li>
                            <a target="_blank" href="/admin_area/simulation_detail?sim_id=<?=$model->simulation->id?>">Открыть оценки</a>
                        </li>
                        <li>
                            <a href="/admin_area/simulation_detail?sim_id=<?=$model->simulation->id?>">Скачать "Сводный бюджет"(D1)</a>
                        </li>
                        <? endif ?>
                        <li>
                            <a class="reset-invite" href="/admin_area/invite/reset?invite_id=<?=$model->id?>">Откатить инвайт</a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        <? endforeach ?>
        </tbody>
    </table>
</div>