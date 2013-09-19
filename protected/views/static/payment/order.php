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
                <label>Текущий тарифный план</label>
                <div class="value"><?= strtolower($account->getTariffLabel()) ?>
                    <small class="tarifprice"><?= $account->tariff->getFormattedPrice() ?> р. в месяц</small><small class="tarifprice">
                        Срок окончания - <?= date('d.m.Y', strtotime($account->tariff_expired_at)) ?>
                    </small>
                </div>
            </div>
            <div class="row">
                <label>Выбрано количество месяцев</label>
                <div class="value">
                    <select>
                        <option value="1">1</option>
                    </select>
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
            'action' => '/payment/doCashPayment',
            'enableAjaxValidation' => true,
            'clientOptions' => [
                'validateOnSubmit' => true,
                'validateOnChange' => false,
                'afterValidate'    => 'js:paymentSubmit',
            ]
        ), $paymentMethodCash);

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
                        <small>Заполните ваши реквизиты и на ваш email придет счет. Тарифный план будет подключён после получения платежа.</small>
                    </div>
                </div>

                <div class="row">
                    <?= $form->labelEx($paymentMethodCash, 'ИНН') ?>
                    <?= $form->textField($paymentMethodCash, 'inn', ['maxlength' => 10]) ?>
                    <?= $form->error($paymentMethodCash, 'inn') ?>
                </div>

                <div class="row">
                    <?= $form->labelEx($paymentMethodCash, 'КПП') ?>
                    <?= $form->textField($paymentMethodCash, 'cpp', ['maxlength' => 9]) ?>
                    <?= $form->error($paymentMethodCash, 'cpp') ?>
                </div>

                <div class="row">
                    <?= $form->labelEx($paymentMethodCash, 'Расчётный счёт') ?>
                    <?= $form->textField($paymentMethodCash, 'account', ['maxlength' => 20]) ?>
                    <?= $form->error($paymentMethodCash, 'account') ?>
                </div>

                <div class="row">
                    <?= $form->labelEx($paymentMethodCash, 'БИК') ?>
                    <?= $form->textField($paymentMethodCash, 'bic', ['maxlength' => 9]) ?>
                    <?= $form->error($paymentMethodCash, 'bic') ?>
                </div>
            </div>

            <div class="order-method payment-card">
                <div class="row method-checked">
                    <?= $form->radioButton(
                        $account,
                        'preference_payment_method',
                        [
                            'value' => UserAccountCorporate::PAYMENT_METHOD_CARD,
                            'id' => 'payment_card',
                            'uncheckValue' => null
                        ]
                    ) ?>
                    <?= $form->label($account, 'preference_payment_method', ['label' => 'Оплата картой', 'for' => 'payment_card']) ?>
                    <div class="method-description">
                        <small>
                            <span class="cardsbg"></span><span class="nocommision">Без дополнительных комиссий</span><br/>
                            Выбирая данную опцию, вы будете перенаправлены на страницу провайдера платежа
                        </small>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <div class="submit">
                    <?= CHtml::submitButton('Оплатить'); ?>
                </div>
            </div>

        <?php $this->endWidget(); ?>
    </div>
    <? $this->renderPartial($paymentMethodRobokassa->payment_method_view, ["robokassa" => $paymentMethodRobokassa, "tariff" => $tariff]); ?>

    <script>
        $("input[type='submit']").click(function(e) {
            e.preventDefault();
            if($("#payment_card:checked").length === 1) {
                proceedRobokassaPayment();
                return false;
            }
            else {
                $("#payment-form").submit();
            }
        });
    </script>
</div>