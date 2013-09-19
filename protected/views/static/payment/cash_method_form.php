<?php
/** @var CActiveForm $form */
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'payment-form',
    'htmlOptions' => ['class' => 'payment-form'],
    'action' => '/debug/DoCashPayment',
    'enableAjaxValidation' => true,
    'clientOptions' => [
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'afterValidate'    => 'js:paymentSubmit',
    ]
), $paymentMethodCash);

?>

<input type="hidden" name="invoice_id" value="<?=$invoice->id?>">

<div class="order-method payment-invoice">
    <?= $form->labelEx($account, 'preference_payment_method', ['label' => 'Оплата по счёту', 'for' => 'payment_invoice']) ?>
    <div class="method-description">
        <small>Заполните ваши реквизиты и на ваш email придет счет. Тарифный план будет подключён после получения платежа.</small>
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


    <div class="form-footer">
        <div class="submit">
            <?= CHtml::submitButton('Оплатить'); ?>
        </div>
    </div>

</div>
<?php $this->endWidget(); ?>


<div class="order-methods">


     <? $this->renderPartial($paymentMethodRobokassa->payment_method_view, ["invoice" => $invoice, "robokassa" => $paymentMethodRobokassa]); ?>

</div>
</div>