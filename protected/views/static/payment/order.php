<?php
/**
 * @var UserAccountCorporate $account
 * @var Invoice $invoice
 * @var Tariff $tariff
 */
?>
<h2 class="thetitle"><?= Yii::t('site', 'Оформление заказа') ?></h2>

<div class="order-page">
    <div class="order-header">
        <div class="order-item">
            <h3>Ваш заказ</h3>

            <label class="tariff-name"><?= $tariff->label ?></label>
            <div class="period">1 Месяц</div>

            <div class="item-price">
                <?= $tariff->getFormattedPrice() ?>
                <span><?= $tariff->getFormattedCyName() ?></span>
            </div>
        </div>

        <div class="order-status">
            <div class="row">
                <label>Выбран тарифный план</label>
                <div class="value"><?= strtolower($account->getTariffLabel()) ?>
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

        echo $form->hiddenField($tariff, 'id');

        ?>

            <div class="order-method payment-invoice">
                <div class="row method-checked">
                    <?= $form->radioButton(
                        $account,
                        'preference_payment_method',
                        [
                            'checked' => 'checked',
                            'value' => UserAccountCorporate::PAYMENT_METHOD_INVOICE,
                            'id' => 'payment_invoice',
                            'uncheckValue' => null
                        ]
                    ) ?>
                    <?= $form->labelEx($account, 'preference_payment_method', ['label' => 'Оплата по счёту', 'for' => 'payment_invoice']) ?>
                    <div class="method-description">
                        <small>Заполните Ваши реквизиты и на Ваш email придет счет. Тарифный план будет подключён после получения платежа.</small>
                    </div>
                </div>

                <div class="row">
                    <?= $form->labelEx($invoice, 'ИНН') ?>
                    <?= $form->textField($invoice, 'inn', ['maxlength' => 10]) ?>
                    <?= $form->error($invoice, 'inn') ?>
                </div>

                <div class="row">
                    <?= $form->labelEx($invoice, 'КПП') ?>
                    <?= $form->textField($invoice, 'cpp', ['maxlength' => 9]) ?>
                    <?= $form->error($invoice, 'cpp') ?>
                </div>

                <div class="row">
                    <?= $form->labelEx($invoice, 'Расчётный счёт') ?>
                    <?= $form->textField($invoice, 'account', ['maxlength' => 20]) ?>
                    <?= $form->error($invoice, 'account') ?>
                </div>

                <div class="row">
                    <?= $form->labelEx($invoice, 'БИК') ?>
                    <?= $form->textField($invoice, 'bic', ['maxlength' => 9]) ?>
                    <?= $form->error($invoice, 'bic') ?>
                </div>
            </div>

            <div class="order-method payment-card">
                <div class="row method-checked">
                    <?= $form->radioButton(
                        $account,
                        'preference_payment_method',
                        [
                            'disabled' => 'disabled',
                            'value' => UserAccountCorporate::PAYMENT_METHOD_CARD,
                            'id' => 'payment_card',
                            'uncheckValue' => null
                        ]
                    ) ?>
                    <?= $form->label($account, 'preference_payment_method', ['label' => 'Оплата картой', 'for' => 'payment_card']) ?>
                    <div class="method-description">
                        <small>
                            <span class="cardsbg"></span><span class="nocommision">Без дополнительных комиссий</span><br/>
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
                    <?= $form->checkBox($invoice, 'agreeWithTerms', ['value' => 'yes', 'uncheckValue' => null]); ?>
                    <?= $form->labelEx($invoice, 'agreeWithTerms', ['label' => 'Я ознакомился и принимаю <a href="#" class="terms">Условия</a>']); ?>
                    <?= $form->error($invoice, 'agreeWithTerms'); ?>
                </div>
            </div>

        <?php $this->endWidget(); ?>
    </div>
</div>