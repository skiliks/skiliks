<?php /* @var $user YumUser */ ?>
<?php /* @var $invites Invite[] */ ?>
<?php /* @var $invites Invite[] */ ?>
<h3>Групповая отправка преглашений от <?= $user->profile->email ?><br>
    <?= $user->profile->firstname.' '.$user->profile->lastname ?>, компания <?= empty($user->account_corporate->company_name)?$user->account_corporate->company_name_for_sales : $user->account_corporate->company_name ?>
</h3>
<br>
<a href="/admin_area/user/43/details">
    &lt;- Вернутья назад
</a>
<br>
<br>
<table class="table">
    <tr>
        <th>Email</th>
        <th>Имя</th>
        <th>Фамилия</th>
        <th>Статус</th>
    </tr>
        <?php foreach($invites as $invite) : ?>
    <tr>
        <td><?= $invite->email ?></td>
        <td><?= $invite->firstname ?></td>
        <td><?= $invite->lastname ?></td>
        <td><?= $invite->getStatusValidationOrSend($isSend) ?></td>
    </tr>
        <?php endforeach ?>
</table>
<br>
<?php if(!$isSend) : ?>
    <?php if(!$has_errors && $isValid) : ?>
        <?php
        $innerText = '
            Для вас был создан аккаунт с логином {email} и паролем {password}<br>
            Пожалуйста,
            <a target="_blank" style="text-decoration:none;color:#147b99;font-family:Tahoma, Geneva, sans-serif;font-size:14px;"
            href="' . Yii::app()->createAbsoluteUrl('/user/auth') . '">
                зайдите
            </a> в свой кабинет и примите приглашение на тестирование для прохождения симуляции.';

        $mailOptions          = new SiteEmailOptions();
        $mailOptions->setText($data->message);
        $mailOptions->text1 = '
            <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                Компания '. $user->account_corporate->company_name .' предлагает вам пройти тест "Базовый менеджмент".<br/>
                <a target="_blank" style="text-decoration: none; color: #147b99;" href="' . Yii::app()->createAbsoluteUrl('static/pages/product') .'">"Базовый менеджмент"</a>
                - это деловая симуляция, позволяющая оценить менеджерские навыки в форме увлекательной игры.<br/>
            </p>
            <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">'.
            $mailOptions->text1.
            '</p>';

        $mailOptions->text2 = '<p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">'.
            $mailOptions->text2.'
            </p>
             <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">'
            . $innerText .
            '</p>';
        ?>
        <div style="border:1px solid #000000; padding: 10px 10px 10px 10px;">

            <?=  $mailOptions->text1 ?>
            <?=  $mailOptions->text2 ?>

        </div>
    <?php endif ?>
<?php endif ?>
<?php if(!$isSend) : ?>
<br>
<form class="form" method="post">
    <table class="table">
        <tr>
            <th style="width: 15%;">Email</th>
            <th style="width: 15%;">Имя</th>
            <th style="width: 15%;">Фамилия</th>
            <th>Текст</th>
        </tr>
        <tr>
            <td><textarea name="data[email]" style="height: 300px;"><?= $data->email?></textarea></td>
            <td><textarea name="data[first_name]" style="height: 300px;"><?= $data->first_name?></textarea></td>
            <td><textarea name="data[last_name]" style="height: 300px;"><?= $data->last_name?></textarea></td>
            <td>
                <textarea name="data[message]" style="height: 190px; width: 95%;"><?= $data->message?></textarea>
                <br>
                Позиция &nbsp;&nbsp;
                <select name="data[vacancy]">
                    <option value=""></option>
                    <?php foreach(Vacancy::model()->findAllByAttributes(['user_id' => $user->id]) as $vacancy) : ?>
                    <option <? if($vacancy->id == $data->vacancy) : ?>selected<? endif ?> value="<?= $vacancy->id ?>"><?= $vacancy->label ?></option>
                    <?php endforeach ?>
                </select>
                <br>
                Скрыть результаты &nbsp;&nbsp;<input <? if($data->hide_result == 1) : ?> checked="checked" <? endif ?> type="checkbox" name="data[hide_result]" value="1">
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>
                <?php if(!$isSend) : ?>
                <button type="submit" name="valid_form" value="true" class="btn btn-success">Проверить</button>
                <?php endif ?>
                <?php if(!$has_errors && $isValid) : ?>
                    <?php if(!$isSend) : ?>
                    <input type="hidden" name="valid_form" value="true">
                    <button type="submit" name="send_form" value="true" class="btn btn-success">Отправить</button>
                    <?php endif ?>
                <?php endif ?>
            </td>
        </tr>
    </table>
</form>
<?php endif ?>