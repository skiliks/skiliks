<div id="invite-friend-popup" style="display: none;">
    <div class="more-side-pads">
        <h2 class="title">Пригласить друга</h2>

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

        <div class="row">
            <?= $form->labelEx($referralInviteModel, 'E-mails: ') ?><br/>
            <?= $form->textField($referralInviteModel, 'emails', []) ?>
            <?= $form->error($referralInviteModel, 'emails') ?>
        </div>
        <br/><br/><br/>
        <div class="row">
            <?= $form->labelEx($referralInviteModel, 'Текст приглашения') ?><br/>
            <?= $form->textArea($referralInviteModel, 'text', []) ?>
            <?= $form->error($referralInviteModel, 'text') ?>
        </div>
<br/>
<br/>
        <div class="submit">
            <?= CHtml::submitButton('Отправить приглашения', ['id'=>'sendRefferInviteButton']); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</div>