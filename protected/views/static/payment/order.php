<?php
/**
 * @var UserAccountCorporate $account
 * @var Invoice $invoice
 */
?>
<div style="display: none;" id="payment_data">
    <?= json_encode($paymentData) ?>
</div>
<section>
    <h1 class="thetitle"><?= 'Оформление заказа' ?></h1>

    <br/>

    <div class="column-full nice-border border-radius-standard background-yellow us-order-box">
        <div class="column-full us-order-header">
            <span class="us-order-description pull-left color-ffffff">
                <div class="us-order-description-row">
                    <label>Ваш тарифный план</label>
                    <strong class="inline current-price-name"></strong>
                </div>
                <div class="us-order-description-row">
                    <label>Полная стоимость</label>
                    <strong class="order-price-total"></strong>
                    р.
                </div>
                <div class="us-order-description-row">
                    <label>Цена за 1 симуляцию</label>
                    <strong class="order-price-1-sim"></strong>
                    р.
                </div>
                <div class="us-order-description-row">
                    <label>Скидка</label>
                    <strong class="current-discount"><?= $account->getDiscount() ?>%</strong>
                </div>
                <div class="us-order-description-row">
                    <span class="error-place error_simulation_selected"></span>
                    <label>Количество симуляций</label>
                    <strong class="">
                        <?php
                            $ordered = Yii::app()->request->getParam('ordered');
                            $defaultValue = $minSimulationSelected;
                            if (null != $ordered) {
                                $defaultValue = (int)$ordered;
                            }
                        ?>
                        <input type="text" id="simulation_selected" value="<?= $defaultValue ?>">
                    </strong>
                </div>
            </span>

            <span class="us-order-preview
                nice-border border-radius-standard background-yellow pull-right pull-content-center">
                <h5 class="color-ffffff">Ваш заказ</h5>
                <br/>
                <br/>
                <strong class="locator-order-tariff-label">Lite</strong>
                <br/>
                <strong class="locator-order-price-total">
                    <span class="order-price-total-with-discount"></span> <small>р</small>
                </strong>
            </span>
        </div>

        <div class="">
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

                <span class="us-box-order-method locator-payment-method-invoice">
                    <div class="">
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
                        <div class="method-description color-6D6D5B">
                            <span>
                                Заполните ваши реквизиты и на ваш email придет счет.
                                Тарифный план будет подключён после получения платежа.
                            </span>
                        </div>
                    </div>

                    <input type="hidden" name="simulation-selected" id="simulation-selected" value="<?= $minSimulationSelected?>" />

                    <div class="row">
                        <span class="error-place">
                            <?= $form->error($paymentMethodCash, 'inn') ?>
                        </span>
                        <?= $form->labelEx($paymentMethodCash, 'ИНН') ?>
                        <?= $form->textField($paymentMethodCash, 'inn', ['maxlength' => 10]) ?>
                    </div>

                    <div class="row">
                        <span class="error-place">
                            <?= $form->error($paymentMethodCash, 'cpp') ?>
                        </span>
                        <?= $form->labelEx($paymentMethodCash, 'КПП') ?>
                        <?= $form->textField($paymentMethodCash, 'cpp', ['maxlength' => 9]) ?>
                    </div>

                    <div class="row">
                        <span class="error-place">
                            <?= $form->error($paymentMethodCash, 'account') ?>
                        </span>
                        <?= $form->labelEx($paymentMethodCash, 'Расчётный счёт') ?>
                        <?= $form->textField($paymentMethodCash, 'account', ['maxlength' => 20]) ?>
                    </div>

                    <div class="row">
                        <span class="error-place">
                            <?= $form->error($paymentMethodCash, 'bic') ?>
                        </span>
                        <?= $form->labelEx($paymentMethodCash, 'БИК') ?>
                        <?= $form->textField($paymentMethodCash, 'bic', ['maxlength' => 9]) ?>
                    </div>
                </span>

                <span class="us-box-order-method payment-card">
                    <!-- method-checked -->
                    <div class="">
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

                        <div class="method-description color-6D6D5B">
                            <span>
                                <img src="<?=$this->getAssetsUrl()?>/img/site/1280/prices/visa-mastercard.gif" alt="Варианты оплаты" /></span>
                                <span class="us-without-commission">Без комиссий</span>
                            </span>
                            <br/>
                            <span>Электронные деньги</span>
                            <br/>
                            <span>
                                <img src="<?=$this->getAssetsUrl()?>/img/site/1280/prices/epay-services.png" alt="Варианты оплаты" />
                                <span class="us-without-commission">Без комиссий</span>
                                <br/>
                                Выставление счёта в интернет-банк и другие способы оплаты, предусмотренные платёжной системой
                                <br/><br/>
                                Выбирая данную опцию, вы будете перенаправлены на страницу платёжной системы
                            </span>
                        </div>
                    </div>
                </span>

                <div class="">
                    <br/>
                    <div class="pull-content-center">
                        <?= CHtml::submitButton('Оплатить', [
                            'class' => 'background-dark-blue icon-circle-with-blue-arrow-big
                            button-standard icon-padding-standard'
                        ]); ?>
                    </div>
                    <br/>
                </div>

            <?php $this->endWidget(); ?>
        </div>
        <?php $this->renderPartial($paymentMethodRobokassa->payment_method_view, ["robokassa" => $paymentMethodRobokassa]); ?>

    </div>
</section>
<div class="clearfix"></div>