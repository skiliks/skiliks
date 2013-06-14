<div class="row fix-top">
    <h2>Инвайты</h2>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>ID-симуляции</th>
            <th>Email работодателя</th>
            <th>Email соискателя</th>
            <th>ID инвайта</th>
            <th>Статус инвайта</th>
            <th>Время начала симуляции</th>
            <th>Время конца симуляции</th>
            <th>Тип (название) основного сценария</th>
            <th>Оценка</th>
            <th>Логи</th>
            <th>Оценки</th>
        </tr>
        </thead>
        <tbody>
        <? /* @var $model Invite*/ ?>
        <? foreach($models as $model) : ?>
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
        </tr>
        <? endforeach ?>
        </tbody>
    </table>
</div>