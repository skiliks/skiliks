<style>
    .registration-by-link .form label {
        color: #555545;
        display: inline-block;
        font: 0.834em/1 "ProximaNova-Bold";
        margin-right: -5px;
        vertical-align: middle;
        width: 60px;
    }

    .registration-by-link .sbHolder {
        width: 334px;
    }

    .decline-form-box {
        position: absolute;
        width: 500px;
        height: 400px;
        left: 50%;
        top: 200px;
        margin-left: -250px;
        z-index: 5;
        background-color: #7db8c0;
        border: 1px solid black;
        padding: 20px;
    }

    .hidden {
        display: none;
    }
</style>

<section class="registration-by-link">
    <h2>Sign up to start simulation</h2>

    <div class="form">
        <h3>Пожалуйста зарегистрируйтесь, чтобы перейти к тестированию</h3>

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'registration-by-link-form'
        )); ?>

        <div class="row">
            <?php echo $form->labelEx($profile, 'Name'); ?>
            <?php echo $form->textField($profile, 'firstname', ['placeholder' => 'Имя']); ?>
            <?php echo $form->textField($profile, 'lastname', ['placeholder' => 'Фамилия']); ?>
            <?php echo $form->error($profile, 'firstname'); ?>
            <?php echo $form->error($profile, 'lastname'); ?>
        </div>

        <div class="row wide">
            <?php echo $form->labelEx($account, 'industry_id'); ?>
            <?php echo $form->dropDownList($account, 'industry_id', $industries); ?>
            <?php echo $form->error($account, 'industry_id'); ?>
        </div>

        <div class="row wide">
            <?php echo $form->labelEx($account, 'position_id'); ?>
            <?php echo $form->dropDownList($account, 'position_id', $positions); ?>
            <?php echo $form->error($account, 'position_id'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($user, 'password'); ?>
            <?php echo $form->passwordField($user, 'password'); ?>
            <?php echo $form->error($user, 'password'); ?>
        </div>

        <br/>

        <div class="row">
            <?php echo $form->labelEx($user, 'password_again'); ?>
            <?php echo $form->passwordField($user, 'password_again'); ?>
            <?php echo $form->error($user, 'password_again'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Register'); ?>
            <a href="/decline-invite/<?php echo $invite->code; ?>" class="decline-invite">Decline</a>
        </div>

        <?php $this->endWidget(); ?>
    </div>

    <div class="decline-form-box hidden">
        <form class="decline-form" action="/decline-invite/<?php echo $invite->code; ?>" method="POST">
            <h3>Пожалуйста укажите причину отказа</h3>

            <div class="row">
                <input type="radio" name="reason" value="1" checked="checked" /> Не хочу регистрироваться
            </div>

            <div class="row">
                <input type="radio" name="reason" value="2" /> Не интересует вакансия
            </div>

            <div class="row">
                <input type="radio" name="reason" value="3" /> Не хочу проходить тест
            </div>

            <div class="row">
                <input type="radio" name="reason" value="4" /> Другое
            </div>

            <div class="row">
                <textarea name="reason-desc" placeholder="Причина отказа"></textarea>
            </div>

            <div class="submit">
                <input type="submit" value="Отказаться">
                <input class="back" type="button" value="Вернуться к регистрации">
            </div>
        </form>
    </div>
</section>

<script type="text/javascript">
    $('.decline-invite').click(function() {
        var href = $(this).attr('href');

        $('.decline-form-box').removeClass('hidden');

        return false;
    });

    $('.decline-form .back').click(function() {
        $('.decline-form-box').addClass('hidden');
    });
</script>