<?php /** @var Invite $invite */ ?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Skiliks</title>
</head>
<body>

<table width="870" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <img src="cid:mail-top" style="display:block;" />
        </td>
        <td valign="top">
            <img src="cid:mail-top-2" style="display:block;" />
        </td>
    </tr>
    <tr>
        <td valign="top">
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td bgcolor="f2f2f2">
                        <table cellspacing="0" cellpadding="5"><tbody><tr><td></td></tr></tbody></table>
                    </td>
                    <td bgcolor="fdfbc6">
                        <table cellspacing="0" cellpadding="15"><tbody><tr><td></td></tr></tbody></table>
                    </td>
                    <td>
                        <table cellpadding="0" cellspacing="0" width="800">
                            <tr>
                                <td valign="top" bgcolor="fdfbc6">
                                    <img src="cid:mail-right-1" align="right" style="display:block;"/>
                                    <h1 style="color:#626250;font-family:Tahoma, Geneva, sans-serif;font-size:28px;margin:0 0 15px 0;padding:0;">
                                        <?= $invite->getReceiverFirstName() ?>, приветствуем вас!
                                    </h1>

                                    <?php if (Yii::app()->params['emails']['isDisplayStandardInvitationMailTopText']): ?>
                                        <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">Компания <?= $invite->ownerUser->account_corporate->company_name ?> предлагает вам пройти тест «Базовый менеджмент» на позицию
                                            <a target="_blank" href="<?= $invite->vacancy->link ?: '#' ?>"><?= $invite->getVacancyLabel() ?></a>.</p>
                                        <?php if (empty($invite->receiverUser)): ?>
                                            <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;"><a href="<?= $this->createAbsoluteUrl('static/pages/product') ?>">«Базовый менеджмент»</a> - это деловая симуляция, позволяющая оценить менеджерские навыки в форме увлекательной игры</p>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <p  style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                                        <?= $invite->message ?>
                                    </p>

                                    <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                                        <?php if ($invite->receiverUser && !$invite->receiverUser->isActive()): ?>
                                            Пожалуйста, <a href="<?=$invite->receiverUser->getActivationUrl()?>">активируйте ваш аккаунт</a>,
                                            выберите индивидуальный профиль, войдите в свой кабинет
                                            и примите приглашение на тестирование для прохождения симуляции.
                                        <?php elseif ($invite->receiverUser && $invite->receiverUser->isPersonal()): ?>
                                            Пожалуйста,
                                            <a target="_blank" style="text-decoration:none;color:#147b99;font-family:Tahoma, Geneva, sans-serif;font-size:14px;" href="<?= $this->createAbsoluteUrl('/dashboard') ?>">
                                                зайдите
                                            </a> в свой кабинет и примите приглашение на тестирование для прохождения симуляции.
                                        <?php elseif ($invite->receiverUser && $invite->receiverUser->isCorporate()): ?>
                                            Пожалуйста,
                                            <a target="_blank" style="text-decoration:none;color:#147b99;font-family:Tahoma, Geneva, sans-serif;font-size:14px;" href="<?= $invite->getInviteLink() ?>">
                                                создайте личный профиль
                                            </a> или
                                            <a target="_blank" style="text-decoration:none;color:#147b99;font-family:Tahoma, Geneva, sans-serif;font-size:14px;" href="<?= $this->createAbsoluteUrl('/dashboard') ?>">
                                                войдите в личный кабинет
                                            </a> и примите приглашение на тестирование для прохождения симуляции.
                                        <?php else: ?>
                                            Пожалуйста,
                                            <a target="_blank" style="text-decoration:none;color:#147b99;font-family:Tahoma, Geneva, sans-serif;font-size:14px;" href="<?= $invite->getInviteLink() ?>">
                                                зарегистрируйтесь
                                            </a>, войдите в свой кабинет и примите приглашение на тестирование для прохождения симуляции.
                                        <?php endif; ?>
                                    </p>
                                </td>
                                <td bgcolor="f2f2f2" valign="top">
                                    <img src="cid:mail-right-2" style="display:block;">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td valign="top">
            <img src="cid:mail-right-3" style="display:block;" />
        </td>
    </tr>
    <tr>
        <td valign="top">
            <table cellpadding="0" cellspacing="0" width="840">
                <tr>
                    <td bgcolor="f2f2f2">
                        <table cellspacing="0" cellpadding="5"><tbody><tr><td></td></tr></tbody></table>
                    </td>
                    <td bgcolor="fdfbc6">
                        <table cellspacing="0" cellpadding="15"><tbody><tr><td></td></tr></tbody></table>
                    </td>
                    <td bgcolor="fdfbc6" width="760">
                        <p style="margin:0 0 15px 0;text-align:justify;line-height:20px;"><strong style="color:#555545;font-family:Tahoma, Geneva, sans-serif;font-weight:bold;font-size:14px">Ваш skiliks</strong></p>
                        <p><a style="text-decoration:none;color:#147b99;font-family:Tahoma, Geneva, sans-serif;font-size:14px;" href="http://www.skiliks.com">www.skiliks.com</a></p></td>
                    <td bgcolor="fdfbc6">
                        <table cellspacing="0" cellpadding="15"><tbody><tr><td></td></tr></tbody></table>
                    </td>
                    <td bgcolor="f2f2f2">
                        <table cellspacing="0" cellpadding="5"><tbody><tr><td></td></tr></tbody></table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <img src="cid:mail-bottom" style="display:block;" />
        </td>
        <td>

        </td>
    </tr>
</table>
<style type="text/css">
    img {
        display: block;
    }
    h1 {
        color:#626250;font-family:Tahoma, Geneva, sans-serif;font-size:28px;margin:0 0 15px 0;padding:0;
    }
    p {
        margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;
    }
    a {
        text-decoration:none;color:#147b99;font-family:Tahoma, Geneva, sans-serif;font-size:14px;
    }
</style>
</body>
</html>