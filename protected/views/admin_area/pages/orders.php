<?php $titles = [
    'ID <br/>заказа',
    'Email',
    'Имя',
    'Фамилия',
    'Компания',
    'Тестовый?',
    '',
    'Время заказа',
    'Время оплаты',
    '',
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
        <form class="form-inline" action="/admin_area/orders" method="GET">
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
                               <?php if($filters["cash"] !== false) : ?>checked <?php endif; ?>>
                        <label for="cash">Оплата по счету</label>
                    </td>
                    <td><input type="checkbox" name="robokassa" id="robokassa" value="robokassa"
                               <?php if($filters["robokassa"] !== false) : ?>checked <?php endif; ?>>
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
                               <?php if($filters["done"]) : ?>checked <?php endif; ?>>
                        <label for="done">Оплаченные</label></td>
                    <td><input type="checkbox" name="notDone" id="notDone" value="notDone"
                               <?php if($filters["notDone"]) : ?>checked <?php endif; ?>>
                        <label for="notDone">Не оплаченные</label>
                    </td>
                </tr>

                <tr>
                    <td>Статус оплаты</td>
                    <td>
                        <button class="btn btn-check-all">Отметить все</button>
                        <button class="btn btn-uncheck-all">Снять все</button>
                    </td>
                    <td><input type="checkbox" name="isTestPayment" id="isTestPayment"
                            <?php if(true === $filters["isTestPayment"]) : ?>checked <?php endif; ?>>
                        <label for="isTestPayment">Тестовые</label></td>
                    <td><input type="checkbox" name="isRealPayment" id="isRealPayment"
                            <?php if(true === $filters["isRealPayment"]) : ?>checked <?php endif; ?>>
                        <label for="isRealPayment">Реальные</label>
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
        <?php /* @var Invoice $model */ ?>
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
            <!-- order id    -->
            <td><?= $model->id?></td>

            <td>
                <!-- email -->
                <a href="/admin_area/user/<?= $model->user->profile->id ?>/details" target="_blank"><i class="icon-user"></i></a>
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

            <!-- is test payment? -->
            <td><?= (1 == $model->is_test_payment)
                    ? '<span class="label">тестовый</span>'
                    : '<span class="label label-success">$ реальный</span>' ?>
            </td>

            <!-- toggle is_test_payment value -->
            <td>
                <a class="btn action-toggle-is-test btn-success"
                   data-invoice-id="<?= $model->id?>">
                       сделать <?= (1 == $model->is_test_payment) ? 'Реальным' : 'Тестовым' ?>
                   </a>
            </td>

            <!-- Created at -->
            <td>
                <?=(empty($model->created_at)?'---- -- -- --':$model->created_at)?>
            </td>

            <!-- payed at -->
            <td>
                <span class="invoice-date-paid">
                    <?=(empty($model->paid_at) ?
                        '<span class="label label-important"><i class="icon icon-fire icon-white"></i> не оплачен</span>' :
                        $model->paid_at)?>
                </span>
            </td>

            <!-- Подтвердить/Откатить заказ -->
            <td>
                <a class="btn btn-success complete-invoice"
                   style="<?= (null == $model->paid_at) ? '' : 'display : none;'; ?>"
                   data-invoice-id="<?= $model->id ?>" data-simulations="<?= $model->simulation_selected ?>"
                   data-user-email="<?= $model->user->profile->email ?>" data-amount="<?= $model->amount ?>"
                    >Подтвердить</a>

                <a class="btn btn-warning disable-invoice"
                   style="<?= (null == $model->paid_at) ? 'display : none;' : ''; ?>"
                   data-invoice-id="<?= $model->id ?>" data-simulations="<?= $model->simulation_selected ?>"
                   data-user-email="<?= $model->user->profile->email ?>" data-amount="<?= $model->amount ?>"
                    >Откатить</a>
            </td>

            <!-- Price -->
            <td>
                <textarea class="invoice-price" rows="1" style="height: 18px; width: 80px;"><?php
                    echo $model->amount
                ?></textarea>&nbsp;руб.
                <br/>
                <span class="btn change-invoice-price-action" data-invoice-id="<?= $model->id?>" style="width: 120px">
                    Изменить
                </span>

            </td>

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

            <!-- Комментарий -->
            <td>
                <textarea class="invoice-comment""><?= $model->comment ?></textarea>
                <br/>
                <span class="btn change-invoice-comment-action" data-invoice-id="<?= $model->id?>">
                    Сохранить
                </span>
            </td>

            <!-- Скачать лог -->
            <td>
                <span href="#" class="btn btn-info view-payment-log" data-invoice-id="<?= $model->id?>">
                    Лог
                </span>
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
    <div class="modal-body" id="myModalBody" style="height: 300px; overflow: scroll;">
        <p>One fine body…</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary">Save changes</button>
    </div>
</div>