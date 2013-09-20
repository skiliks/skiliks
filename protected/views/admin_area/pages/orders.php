<?php $titles = [
    'ID <br/>заказа',
    'Email, <br/>ФИО, <br/>Компания',
    'Название <br/>тарифа',
    'Время <br/>заказа',
    'Время <br/>оплаты',
    'Стоимость',
    'Платежная<br/>система',
    'Дополнительные<br/>данные',
    '',
    '',

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
        <?php $step = 9; $i = 0; ?>
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
            <td><?=$model->id?></td>
            <td>

                <a href="/admin_area/user/<?=$model->user->profile->id ?>/details" target="_blank"><i class="icon-user"></i></a>
                <?= (empty($model->user->profile->email)) ? 'Не найден' : $model->user->profile->email ?>
                <br/>
                <?= (empty($model->user->profile->firstname)) ? '-' : $model->user->profile->firstname?>
                <?= (empty($model->user->profile->lastname)) ? '-' : $model->user->profile->lastname?>
                <br/>
                <?= (empty($model->user->account_corporate->company_name))
                    ? '--' : $model->user->account_corporate->company_name?>
            </td>
            <td><span class="label"><?=(empty($model->tariff->label))?'Не найден':$model->tariff->label?></span></td>
            <td><?=(empty($model->created_at)?'---- -- -- --':$model->created_at)?></td>
            <td><?=(empty($model->paid_at) ? 'Не оплачен' :$model->created_at)?></td>
            <td><?= Yii::app()->numberFormatter->formatCurrency($model->amount, "RUR") ?></td>
            <td><?= $model->payment_system?></td>
            <td><?= $model->additional_data?></td>
            <td><a class="btn btn-info">Лог</a> </td>
            <td><a class="btn btn-success">Подтвердить</a> </td>
        </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>