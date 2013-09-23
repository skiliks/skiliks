<?php $titles = [
    'ID <br/>заказа',
    'Email, <br/>ФИО, <br/>Компания',
    'Название <br/>тарифа',
    'Время заказа,<br/> Время оплаты',
    'Стоимость',
    'Платежная<br/>система',
    'Дополнительные<br/>данные',
    'Комментарий',
    '&nbsp;',

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
            <td>
                <?=(empty($model->created_at)?'---- -- -- --':$model->created_at)?><br/>
                <span class="invoice-date-paid"><?=(empty($model->paid_at) ? 'Не оплачен' :$model->created_at)?></span>
            </td>
            <td><?= Yii::app()->numberFormatter->formatCurrency($model->amount, "RUR") ?></td>
            <td><?= $model->payment_system?></td>
            <td><?= nl2br($model->additional_data)?></td>

            <td>
                <textarea class="invoice-comment""><?=$model->comment ?></textarea>
                <br/><a class="btn change-comment-button" data-invoice-id="<?=$model->id?>">Сохранить</a>
            </td>

            <td>
                <a href="#" class="btn btn-info view-payment-log" data-invoice-id="<?=$model->id?>">Лог</a>
            </td>

            <td>
                <? if(!$model->isComplete()) : ?>
                    <a class="btn btn-success complete-invoice" data-invoice-id="<?=$model->id?>"
                       data-tariff="<?=$model->tariff->label?>"  data-months="<?=$model->month_selected ?>"  data-user-email="<?=$model->user->profile->email?>">Подтвердить</a>
                <? else : ?>
                    <span>Оплачен</span>
                <? endif; ?>
            </td>
        </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>

<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Log model</h3>
    </div>
    <div class="modal-body" id="myModalBody">
        <p>One fine body…</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary">Save changes</button>
    </div>
</div>