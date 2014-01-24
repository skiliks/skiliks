<section class="partial">
    <label class="partial-label"><?= __FILE__ ?></label>

    <div class="locator-form-invite-step-2">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'send-invite-message-form',
        )); ?>

        <?php echo $form->hiddenField($invite, 'firstname'); ?>
        <?php echo $form->hiddenField($invite, 'lastname'); ?>
        <?php echo $form->hiddenField($invite, 'email'); ?>
        <?php echo $form->hiddenField($invite, 'vacancy_id'); ?>

        <h3 class="pull-content-center">Сообщение</h3>

        <div class="border-radius-standard background-light-blue unstandard-box-with-blue-border">

            <!-- Имя приглашенного -->
            <p class="row">
                <span class="error-place">
                    <?= $form->error($invite, 'fullname'); ?>
                </span>
                <?php echo $form->textField($invite, 'fullname'); ?>
            </p>

            <?php if (Yii::app()->params['emails']['isDisplayStandardInvitationMailTopText']): ?>
                <p>Компания <?= $invite->ownerUser->account_corporate->company_name ?> предлагает вам пройти тест "Базовый менеджмент".</p>
                <?php if (empty($invite->receiverUser)): ?>
                    <p>
                        <a target="_blank" href="<?= $this->createAbsoluteUrl('static/pages/product') ?>">"Базовый менеджмент"</a>
                        - это деловая симуляция, позволяющая оценить менеджерские навыки в форме увлекательной игры.</p>
                <?php endif; ?>
            <?php endif; ?>

            <!-- TEXTAREA -->
            <p class="row">
                <span class="error-place">
                    <?= $form->error($invite, 'message'); ?>
                </span>
                <?= $form->textArea($invite, 'message', ['rows' => 20, 'cols' => 60]); ?>
            </p>

            <p>
                <?php if ($invite->receiverUser && !$invite->receiverUser->isActive()): ?>
                    Пожалуйста,
                    <a target="_blank" href="<?=$invite->receiverUser->getActivationUrl()?>">активируйте ваш аккаунт</a>,
                    выберите индивидуальный профиль, ввойдите в свой кабинет
                    и примите приглашение на тестирование для прохождения симуляции.
                <?php elseif ($invite->receiverUser && $invite->receiverUser->isPersonal()): ?>
                    Пожалуйста,
                    <a target="_blank" href="<?= $this->createAbsoluteUrl('/dashboard') ?>">зайдите</a>
                    в свой кабинет и примите приглашение на тестирование для прохождения симуляции.
                <?php elseif ($invite->receiverUser && $invite->receiverUser->isCorporate()): ?>
                    Пожалуйста,
                    <a target="_blank" href="<?= $this->createAbsoluteUrl('/registration') ?>">создайте личный профиль</a>
                    или
                    <a href="<?= $this->createAbsoluteUrl('/dashboard') ?>">войдите в личный кабинет</a>
                    и примите приглашение на тестирование для прохождения симуляции.
                <?php else: ?>
                    Пожалуйста,
                    <a target="_blank" href="<?= $this->createAbsoluteUrl('/registration') ?>">зарегистрируйтесь</a>
                    и примите приглашение на тестирование для прохождения симуляции.
                <?php endif; ?>
            </p>
            <p>Ваш skiliks</p>
        </div>

        <div class="pull-content-left">
            <?php echo $form->checkBox($invite, 'is_display_simulation_results', ['class'=>'inline-radio-button']); ?>
            <?php echo $form->labelEx($invite, 'is_display_simulation_results', ['class'=>'inline-radio-button-label']); ?>
        </div>

        <div class="pull-content-center">
            <?php echo CHtml::submitButton('Отправить', [
                'name'  => 'send',
                'class' => 'label background-dark-blue icon-circle-with-blue-arrow-big button-standard icon-padding-standard',
            ]); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</section>