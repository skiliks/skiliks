<?php
/**
 * @var UserAccountCorporate $account
 * @var Invoice $invoice
 * @var Tariff $tariff
 */
?>
<h1 class="page-header"><?= Yii::t('site', 'Оформление заказа') ?></h1>

<div class="block-border border-primary order-page">
    <div class="order-header">
        <div class="order-item bg-yellow border-primary pull-right font-brown block-border">
            <div class="pad-norm">
                <header class="font-white font-xxlarge text-center">Ваш заказ</header>

                <h4 class="font-4xlarge proxima-bold text-center font-brown"><?= $tariff->label ?></h4>
                <div class="font-slarge text-center">1 Месяц</div>

                <div class="item-price font-6xlarge proxima-bold text-center">
                    <?= $tariff->getFormattedPrice() ?>
                    <span class="font-large"><?= $tariff->getFormattedCyName() ?></span>
                </div>
            </div>
        </div>

        <div class="order-status">
            <div class="row font-white">
                <label class="grid-cell font-xslarge">Выбран тарифный план</label>
                <div class="value grid-cell"><strong class="font-xslarge"><?= strtolower($account->getTariffLabel()) ?></strong><small class="font-small font-lightgrey"><?= $account->tariff->getFormattedPrice() ?> в месяц</small>
                </div>
            </div>
            <div class="row font-white">
                <label class="grid-cell font-xslarge">Выбрано количество месяцев</label>
                <div class="value grid-cell">
                    <select><option value="1">1</option></select>
                    <small class="font-small font-lightgrey">Срок окончания - <?= date('d.m.Y', strtotime($account->tariff_expired_at)) ?></small>
                </div>
            </div>
        </div>
    </div>

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

            <div class="order-method payment-invoice grid-cell">
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
                    <div class="method-description font-small font-lighterbrown">Заполните ваши реквизиты и на ваш email придет счет. Тарифный план будет подключён после получения платежа.</div>
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

            <div class="order-method font-lightestbrown grid-cell">
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
                    <div class="method-description font-small font-lightestbrown"><span class="cardsbg"></span><span class="nocommision">Без дополнительных комиссий</span><br/><span>Выбирая данную опцию, вы будете перенаправлены на страницу провайдера платежа - ХХХ</span></div>
                </div>
            </div>

            <div class="btn-wrap btn-large-wrap btn-green-wrap text-center margin-vert"><?= CHtml::submitButton('Оплатить'); ?></div>
            <div class="font-small text-center">
                <?= $form->checkBox($invoice, 'agreeWithTerms', ['value' => 'yes', 'uncheckValue' => null]); ?>
                <?= $form->labelEx($invoice, 'agreeWithTerms', ['label' => 'Я ознакомился и принимаю <a href="#" class="font-blue-dark">Условия</a>']); ?>
                <?= $form->error($invoice, 'agreeWithTerms'); ?>
            </div>

        <?php $this->endWidget(); ?>
</div>