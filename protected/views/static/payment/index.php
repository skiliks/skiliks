<?php
/**
 * @var UserAccountCorporate $account
 * @var Tariff $tariff
 */
?>

<div class="order-item">
    <h3>Ваш заказ</h3>

    <label class="tariff-name"><?= $tariff->label ?></label>
    <div class="period">1 Месяц</div>

    <div class="item-price">
        <?php if (floor($tariff->getPrice() / 1000)): ?>
            <span><?= floor($tariff->getPrice() / 1000) ?></span>
        <?php endif ?>
        <?= $tariff->getPrice() % 1000 ?>
    </div>
</div>

<div class="order-status">
    <div class="row">
        <label>Выбран тарифный план</label>
        <div class="value">
            <?= strtolower($account->getTariffLabel()) ?>
            <br/>
            <small class="tarifprice"><?= $account->tariff->getFormattedPrice() ?> в месяц</small>
        </div>
    </div>
    <div class="row">
        <label>Выбрано количество месяцев</label>
        <div class="value">
            <select>
                <option value="1">1</option>
            </select>
            <br/>
            <small class="expire-date">
                Срок окончания - <?= date('d.m.Y', strtotime($account->tariff_expired_at)) ?>
            </small>
        </div>
    </div>
</div>

<div class="order-methods">
    <?php
    /** @var CActiveForm $form */
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'payment-form',
        'htmlOptions' => ['class' => 'payment-form'],
        'action' => '/payment/do',
        'enableAjaxValidation' => true,
        'clientOptions' => [
            'validateOnSubmit' => true,
            'validateOnChange' => false,
            'afterValidate'    => 'js:paymentSubmit',
        ]
    ));

    $form->hiddenField($tariff, 'id');

    ?>

        <div class="order-method payment-invoice">
            <div class="row">
                <?= $form->radioButton($account, 'preference_payment_method', ['value' => UserAccountCorporate::PAYMENT_METHOD_INVOICE]) ?>
                <?= $form->labelEx($account, 'preference_payment_method', ['label' => 'Оплата по счёту']) ?>
                <div class="method-description">
                    <small>Заполните Ваши реквизиты и на Ваш email придет счет. Тарифный план будет подключён после получения платежа.</small>
                </div>
            </div>

            <div class="row">
                <?= $form->labelEx($account, 'inn') ?>
                <?= $form->textField($account, 'inn') ?>
                <?= $form->error($account, 'inn') ?>
            </div>

            <div class="row">
                <?= $form->labelEx($account, 'cpp') ?>
                <?= $form->textField($account, 'cpp') ?>
                <?= $form->error($account, 'cpp') ?>
            </div>

            <div class="row">
                <?= $form->labelEx($account, 'bank_account_number') ?>
                <?= $form->textField($account, 'bank_account_number') ?>
                <?= $form->error($account, 'bank_account_number') ?>
            </div>

            <div class="row">
                <?= $form->labelEx($account, 'bic') ?>
                <?= $form->textField($account, 'bic') ?>
                <?= $form->error($account, 'bic') ?>
            </div>
        </div>

        <div class="order-method payment-card">
            <div class="row">
                <?= $form->radioButton($account, 'preference_payment_method', ['disabled' => 'disabled', 'value' => UserAccountCorporate::PAYMENT_METHOD_CARD]) ?>
                <?= $form->label($account, 'preference_payment_method', ['label' => 'Оплата картой']) ?>
                <div class="method-description">
                    <small>
                        <img src="" alt="Visa, MasterCard" /> Без дополнительных комиссий <br/>
                        Выбирая данную опцию, Вы будете перенаправлены на страницу провайдера платежа - ХХХ
                    </small>
                </div>
            </div>
        </div>

        <div class="form-footer">
            <div class="submit">
                <?= CHtml::submitButton('Оплатить'); ?>
            </div>
            <div class="terms-confirm">
                <?= CHtml::checkBox('agree', false, ['id' => 'terms_agreement']) ?>
                <?= CHtml::label('Я ознакомился и принимаю <a href="#">Условия</a>', 'terms_agreement') ?>
            </div>
        </div>

    <?php $this->endWidget(); ?>
</div>