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
    'Логи',
    'Оценки',
    'D1',
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
        <tr>
            <td><?=(empty($model->simulation->id)?'Не найден':$model->simulation->id)?></td>
            <td><?=(empty($model->ownerUser->profile->email))?'Не найден':$model->ownerUser->profile->email?></td>
            <td><?=(empty($model->receiverUser->profile->email))?'Не найден':$model->receiverUser->profile->email?></td>
            <td><?=$model->id?></td>
            <td><span class="label"><?=$model->getStatusText()?></span></td>
            <td><?=(empty($model->simulation->start)?'---- -- -- --':$model->simulation->start)?></td>
            <td><?=(empty($model->simulation->end)?'---- -- -- --':$model->simulation->end)?></td>
            <td><span class="label label-inverse"><?=(empty($model->scenario->slug)?'Нет данных':$model->scenario->slug)?></span></td>
            <td><?=$model->getOverall() ?></td>
            <td><?=(empty($model->simulation->id)?'Не найдена':"<a href=\"/static/admin/saveLog/{$model->simulation->id}\">Скачать</a>")?></td>
            <td><?=(empty($model->simulation->id)?'Не найдена':"<a target=\"_blank\" href=\"/admin_area/simulation_detail?sim_id={$model->simulation->id}\">Открыть</a>")?></td>
            <td><?=(empty($model->simulation->id)?'Не найдена':"<a href=\"/admin_area/budget?sim_id={$model->simulation->id}\">Скачать</a>")?></td>
        </tr>
        <? endforeach ?>
        </tbody>
    </table>
</div>