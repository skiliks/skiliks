<?php $titles = [
    'Email, <br/>ФИО, <br/>Компания',
    'ID <br/>заказа',
    'Время <br/>заказа',
    'Название <br/>тарифа',
    'Статус',
    'Валидность',
    'ИНН',
    'КПП',
    'Расчётный <br/>счёт',
    'БИК',
    'Пометить как',
] ?>
<div class="row fix-top">
    <h2>Заказы</h2>

    <?php $this->widget('CLinkPager',array(
        'header'         => '',
        'pages'          => $pager,
        'maxButtonCount' => 5, // максимальное вол-ко кнопок
    )); ?>

    Страница <?= $page ?> из <?= ceil($totalItems/$itemsOnPage) ?> (<?= $itemsOnPage ?> записей на странице из <?= $totalItems ?>)

    <br/>
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
        <?php /* @var $model Invoice */ ?>
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
        <tr class="orders-row">
            <td>
                <?= (empty($model->user->profile->email)) ? 'Не найден' : $model->user->profile->email ?>
                <br/>
                <?= (empty($model->user->profile->firstname)) ? '-' : $model->user->profile->firstname?>
                <?= (empty($model->user->profile->lastname)) ? '-' : $model->user->profile->lastname?>
                <br/>
                <?= (empty($model->user->account_corporate->company_name))
                    ? '--' : $model->user->account_corporate->company_name?>
            </td>
            <td><?=$model->id?></td>
            <td><?=(empty($model->updated_at)?'---- -- -- --':$model->updated_at)?></td>
            <td><span class="label"><?=(empty($model->tariff->label))?'Не найден':$model->tariff->label?></span></td>
            <td><span class="label <?=$model->getStatusLabel()?>"><?=$model->status?></span></td>
            <td><span class="label <?=$model->getValidationStatusLabel()?>"><?=$model->getValidationStatus()?></span></td>
            <td><?=$model->inn?></td>
            <td><?=$model->cpp?></td>
            <td><?=$model->account?></td>
            <td><?=$model->bic?></td>
            <td>
                <a href="<?=$model->getValidationAction()?>" style="margin-bottom: 10px;"
                   class="btn <?=$model->getValidationStatusBtn()?>">
                    <?=$model->getValidationStatusBtnText()?>
                </a>
                <br/>
                <a href="<?=$model->getStatusAction()?>" class="btn <?=$model->getStatusBtn()?>">
                    <?=$model->getStatusBtnText()?>
                </a>
            </td>
        </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>