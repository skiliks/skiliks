<?php $assetsUrl = $this->getAssetsUrl(); ?>

<div style="overflow: hidden;">
    <h1 class="additional-simulations-header ProximaNova-Bold">
        Порекомендуйте нас друзьям и получите дополнительные симуляции
    </h1>

    <div style="min-height: 15px;"></div>

    <div class="referral-left-div">
        <h2 style="">Отправить приглашение</h2>

        <div style="clear: both; min-height: 15px;"></div>

        <?php
        /** @var CActiveForm $form */
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'invite-referrer-form',
            'htmlOptions' => ['class' => ''],
            'action' => '/dashboard/inviteReferrals',
            'enableAjaxValidation' => true,
            'clientOptions' => [
                'validateOnSubmit' => true,
                'validateOnChange' => false,
                'beforeValidate'   => 'js:changeInviteReferrButton',
                'afterValidate'    => 'js:inviteFriend',
            ]
        ), $referralInviteModel = new ReferralsInviteForm());
        ?>

        <div class="referral-email-label">
            Email
        </div>

        <div class="row invite-referrals-div">
            <?= $form->textArea($referralInviteModel, 'emails', ['class' => "invite-referral-textarea",
                                                                 'placeholder' => "Введите email (ы)"]) ?>
            <div style="clear:both;"></div>
            <?= $form->error($referralInviteModel, 'emails') ?>
        </div>

        <div style="float:left;">
            <?= $form->error($referralInviteModel, 'text') ?>
            <? $referralInviteModel->text = 'Рекомендую попробовать сервис skiliks, который сэкономит часы на подбор и оценку навыков менеджеров.

Получить 10 симуляций бесплатно можно по ссылке www.skiliks.com, зарегистрировав корпоративный аккаунт.

Удачи!

'.$user->profile->firstname . " " . $user->profile->lastname ?>
            <?= $form->textArea($referralInviteModel, 'text', ['class' => "invite-referral-textarea-message"]) ?>
        </div>

        <div style="clear: both; min-height: 15px;"></div>

        <div style="float:left;">
            <?= CHtml::submitButton('Отправить', ['id'=>'sendRefferInviteButton', 'class'=>'light-btn']); ?>
            <?php $this->endWidget(); ?>
        </div>

        </div>

        <div class="referral-right-div">
            <img class="referral-present-img" src="<?=$assetsUrl?>/img/referral-present-big.png" />
            <p class="referral-span-first">
                <span class="ProximaNova-20">1 симуляция в месяц</span> <span class="ProximaNova-Bold-22px">НАВСЕГДА</span><br/>
                <span class="ProximaNova-20">за каждого нового корпоративного пользователя по вашей рекомендации</span>
            </p>
            <p class="ProximaNova-font-11px with-margin-top">
                Симуляции останутся на вашем аккаунте навсегда, даже если у вас закончится текущий тариф.
            </p>
            <p class="ProximaNova-font-11px">
                Количество симуляций по программе не ограничено.
            </p>
        </div>

        <div style="clear: both; min-height: 15px;"></div>

        <p class="referral-bottom-p ProximaNova">
            После отправки приглашений <a href="/profile/corporate/referrals">просмотрите их состояние</a>
        </p>

    </div>