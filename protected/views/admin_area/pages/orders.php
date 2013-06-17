<? $titles = [
    'Email работодателя',
    'Имя работодателя',
    'Название компании',
    'ID заказа',
    'Время заказа',
    'Название тарифа',
    'Статус',
    'Валидность',
    'ИНН',
    'КПП',
    'Расчётный счёт',
    'БИК',
    'Пометить как',
    ''
] ?>
<div class="row fix-top">
    <h2>Заказы</h2>
    <table class="table table-hover">
        <thead>
        <tr>
            <? foreach($titles as $title) :?>
            <th><?=$title?></th>
            <? endforeach ?>
        </tr>
        </thead>
        <tbody>
        <? /* @var $model Invoice */ ?>
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
        <tr class="orders-row">
            <td><?=(empty($model->user->profile->email))?'Не найден':$model->user->profile->email?></td>
            <td><?=(empty($model->user->profile->firstname) || empty($model->user->profile->lastname))?'Не найден':$model->user->profile->firstname." ".$model->user->profile->lastname?></td>
            <td><?=(empty($model->user->account_corporate->company_name))?'Не найден':$model->user->account_corporate->company_name?></td>
            <td><?=$model->id?></td>
            <td><?=(empty($model->updated_at)?'---- -- -- --':$model->updated_at)?></td>
            <td><span class="label"><?=(empty($model->tariff->label))?'Не найден':$model->tariff->label?></span></td>
            <td><span class="label <?=$model->getStatusLabel()?>"><?=$model->status?></span></td>
            <td><span class="label <?=$model->getValidationStatusLabel()?>"><?=$model->getValidationStatus()?></span></td>
            <td><?=$model->inn?></td>
            <td><?=$model->cpp?></td>
            <td><?=$model->account?></td>
            <td><?=$model->bic?></td>
            <td><a href="<?=$model->getValidationAction()?>" class="btn <?=$model->getValidationStatusBtn()?>"><?=$model->getValidationStatusBtnText()?></a></td>
            <td><a href="<?=$model->getStatusAction()?>" class="btn <?=$model->getStatusBtn()?>"><?=$model->getStatusBtnText()?></a></td>
        </tr>
        <? endforeach ?>
        </tbody>
    </table>
</div>