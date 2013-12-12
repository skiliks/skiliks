<?php $titles = [
    'Email',
    'Имя',
    'Фамилия',
    'Компания',
    'ID <br/>заказа',
    'Название <br/>тарифа',
    'Время заказа',
    'Время оплаты',
    'Стоимость',
    'Платежная<br/>система',
    'ИНН',
    'КПП',
    'Счет',
    'БИК',
    'Комментарий',
    '&nbsp;',
    '&nbsp;',

] ?>
<div class="row fix-top">
    <h2>Заказы</h2>

    <div class="row">
        <form class="form-inline" action="/admin_area/orders">
            <table class="table table-bordered" style="margin-left: 40px; width: 80%;">
                <tr>
                    <td>Е-мейл клиента</td>
                    <td colspan="3"><input type="text" style="margin-left:40px;" value="<?=$filters['email'] ?>" name="email" placeholder="email"></td>
                </tr>

                <tr>
                    <td>Платежные системы</td>
                    <td>
                        <button class="btn btn-check-all">Отметить все</button>
                        <button class="btn btn-uncheck-all">Снять все</button>
                    </td>
                    <td><input type="checkbox" name="cash" id="cash" value="cash"
                               <?php if($filters["cash"] !== null) : ?>checked <?php endif; ?>>
                        <label for="cash">Оплата по счету</label>
                    </td>
                    <td><input type="checkbox" name="robokassa" id="robokassa" value="robokassa"
                               <?php if($filters["robokassa"] !== null) : ?>checked <?php endif; ?>>
                        <label for="robokassa">Оплата робокассой</label>
                    </td>
                </tr>

                <tr>
                    <td>Статус оплаты</td>
                    <td>
                        <button class="btn btn-check-all">Отметить все</button>
                        <button class="btn btn-uncheck-all">Снять все</button>
                    </td>
                    <td><input type="checkbox" name="done" id="done" value="done"
                               <?php if($filters["done"] !== null) : ?>checked <?php endif; ?>>
                        <label for="done">Проведенные</label></td>
                    <td><input type="checkbox" name="notDone" id="notDone" value="notDone"
                               <?php if($filters["notDone"] !== null) : ?>checked <?php endif; ?>>
                        <label for="notDone">Не проведенные</label>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <a style="margin-left:20px;" name="form-send" class="btn disable-filters">Снять фильтры</a>
                        <input style="margin-left:20px;" type="submit" name="form-send" class="btn btn-success" value="Применить фильтры">
                    </td>
                </tr>
            </table>
        </form>
    </div>

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
            <td>
                <!-- email -->
                <a href="/admin_area/user/<?=$model->user->profile->id ?>/details" target="_blank"><i class="icon-user"></i></a>
                <?= (empty($model->user->profile->email)) ? 'Не найден' : $model->user->profile->email ?>
            </td>

            <td>
                <!-- last name -->
                <?= (empty($model->user->profile->lastname)) ? '-' : $model->user->profile->lastname?>
            </td>

            <!-- first name -->
            <td><?= (empty($model->user->profile->firstname)) ? '-' : $model->user->profile->firstname?></td>

            <!-- company_name -->
            <td><?= (empty($model->user->account_corporate->company_name)) ? '--' : $model->user->account_corporate->company_name?></td>

            <!-- order id    -->
            <td><?=$model->id?></td>

            <!-- tariff  -->
            <td><span class="label"><?=(empty($model->tariff->label))?'Не найден':$model->tariff->label?></span></td>

            <td>
                <?=(empty($model->created_at)?'---- -- -- --':$model->created_at)?>
            </td>

            <td>
                <span class="invoice-date-paid"><?=(empty($model->paid_at) ? 'Не оплачен' :$model->paid_at)?></span>
            </td>

            <td><?= Yii::app()->numberFormatter->formatCurrency($model->amount, "RUR") ?></td>

            <td><?= $model->payment_system?></td>

            <?php if(json_decode($model->additional_data) instanceof stdClass) : ?>
                <?php foreach(json_decode($model->additional_data) as $key => $value) : ?>
                    <?= '<td>'.$value.'</td>'?>
                <?php endforeach; ?>
                <?php else : ?>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            <?php endif; ?>

            <td>
                <textarea class="invoice-comment""><?=$model->comment ?></textarea>
                <br/><a class="btn change-comment-button" data-invoice-id="<?=$model->id?>">Сохранить</a>
            </td>

            <td>
                <a href="#" class="btn btn-info view-payment-log" data-invoice-id="<?=$model->id?>">Лог</a>
            </td>

            <td>
                <?php if(!$model->isComplete()) : ?>
                    <a class="btn btn-success complete-invoice" data-invoice-id="<?=$model->id?>"
                       data-tariff="<?=$model->tariff->label?>"  data-months="<?=$model->month_selected ?>"  data-user-email="<?=$model->user->profile->email?>">Подтвердить</a>
                    <a class="btn btn-warning disable-invoice" style="display:none;" data-invoice-id="<?=$model->id?>"
                       data-tariff="<?=$model->tariff->label?>"  data-months="<?=$model->month_selected ?>"  data-user-email="<?=$model->user->profile->email?>">Откатить</a>
                <?php else : ?>
                    <a class="btn btn-success complete-invoice" style="display:none;" data-invoice-id="<?=$model->id?>"
                       data-tariff="<?=$model->tariff->label?>"  data-months="<?=$model->month_selected ?>"  data-user-email="<?=$model->user->profile->email?>">Подтвердить</a>
                    <a class="btn btn-warning disable-invoice" data-invoice-id="<?=$model->id?>"
                       data-tariff="<?=$model->tariff->label?>"  data-months="<?=$model->month_selected ?>"  data-user-email="<?=$model->user->profile->email?>">Откатить</a>
                <?php endif; ?>
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