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
                    <select id="month-selected" id="month-selected">
                        <option value="1">1</option>
<!--                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        -->
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

                <input type="hidden" name="cash-month-selected" id="cash-month-selected" value="1" />
                <input type="hidden" name="tariff-label" id="tariff-label" value="<?=$tariff->label ?>" />

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
                    <?= $form->label($account, 'preference_payment_method', ['label' => 'Оплата картой и электронными деньгами', 'for' => 'payment_card']) ?>
                    <div class="method-description" style="color:#6d6d5b;">
                        <small>
                            <span class="cardsbg"></span><div class="without-commission">Без комиссий</div><br/>
                        </small>
                        <span style="font-size:14px; line-height: 25px;">Электронные деньги</span><br/>
                        <small>
                            <img src="<?=$this->getAssetsUrl()?>/img/epay-services.png" alt="Варианты оплаты" />
                            <div class="without-commission payment-method-without-commision">Без комиссий</div><br/>
                            Выставление счёта в интернет-банк и другие способы оплаты, предусмотренные платёжной системой<br/><br/>
                            <p>Выбирая данную опцию, вы будете перенаправлены на страницу платёжной системы</p>
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
    <?php $this->renderPartial($paymentMethodRobokassa->payment_method_view, ["robokassa" => $paymentMethodRobokassa, "tariff" => $tariff]); ?>

    <script>
        $("input[type='submit']").click(function(e) {
            e.preventDefault();
            if($("#payment_card:checked").length === 1) {
                proceedRobokassaPayment();
                return false;
            }
            else {
                $("#cash-month-selected").val($("#month-selected").val());
                $("#payment-form").submit();
            }
        });


        // ordering form truncating spaces
        var phone = document.getElementById('CashPaymentMethod_account'),
            cleanPhoneNumber;

        cleanPhoneNumber= function(e) {
            e.preventDefault();
            var pastedText = '';
            if (window.clipboardData && window.clipboardData.getData) { // IE
                pastedText = window.clipboardData.getData('Text');
            } else if (e.clipboardData && e.clipboardData.getData) {
                pastedText = e.clipboardData.getData('text/plain');
            }
            this.value = pastedText.replace(/\D/g, '');
        };

        phone.onpaste = cleanPhoneNumber;

    </script>
</div>