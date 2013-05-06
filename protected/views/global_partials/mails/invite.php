<?php /** @var Invite $invite */ ?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Ruseller Email Newsletter</title>
        <style type="text/css">
            img {
                display: block;
            }
            h1 {
                color:#626250;font-family:ProximaNova-Bold,Arial;font-size:28px;margin:0 0 15px 0;padding:0;
            }
            p {
                margin:0 0 15px 0;color:#555545;font-family:ProximaNova-Regular,Tahoma;font-size:14px;text-align:justify;line-height:20px;
            }
            a {
                text-decoration:none;color:#147b99;font-family:ProximaNova-Regular,Tahoma;font-size:14px;
            }
        </style>
    </head>
    <body>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <img src="cid:mail-top" style="display:block;">
                </td>
                <td>
                    <table cellpadding="0" cellspacing="0">
                        <tr><td></td></tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table cellpadding="0" cellspacing="0">
                        <tr style="vertical-align: top;">
                            <td bgcolor="f2f2f2">
                                <table cellpadding="5" cellspacing="0">
                                    <tr><td></td></tr>
                                </table>
                            </td>
                            <td bgcolor="fdfbc6">
                                <table cellpadding="15" cellspacing="0">
                                    <tr><td></td></tr>
                                </table>
                            </td>
                            <td bgcolor="fdfbc6" valign="top">
                                <h1 style="color:#626250;font-family:ProximaNova-Bold,Arial;font-size:28px;margin:0 0 15px 0;padding:0;">
                                        <?= $invite->getReceiverUserName() ?>, приветствуем Вас!
                                </h1>

                                <p style="margin:0 0 15px 0;color:#555545;font-family:ProximaNova-Regular,Tahoma;font-size:14px;text-align:justify;line-height:20px;">
                                       <?= $invite->ownerUser->account_corporate->company_name ?: 'Компания' ?>
                                        предлагает вам пройти тест «Базовый менеджмент» для участия в конкурсе на вакансию
                                        <a style="text-decoration:none;color:#147b99;font-family:ProximaNova-Regular,Tahoma;font-size:14px;" href="<?= $invite->vacancy->link ?: '#' ?>">
                                                <?= $invite->getVacancyLabel() ?>
                                        </a>.
                                </p>

                                <?php if (empty($invite->receiverUser)): ?>
                                    <p style="margin:0 0 15px 0;color:#555545;font-family:ProximaNova-Regular,Tahoma;font-size:14px;text-align:justify;line-height:20px;">
                                        <a style="text-decoration:none;color:#147b99;font-family:ProximaNova-Regular,Tahoma;font-size:14px;" href="<?= $this->createAbsoluteUrl('static/pages/product') ?>">
                                                «Базовый менеджмент»
                                        </a> - это деловая симуляция, позволяющая оценить менеджерские навыки в форме
                                        увлекательной игры.
                                    </p>
                                <?php endif; ?>

                                <p  style="margin:0 0 15px 0;color:#555545;font-family:ProximaNova-Regular,Tahoma;font-size:14px;text-align:justify;line-height:20px;">
                                    <?= $invite->message ?>
                                </p>

                                <p style="margin:0 0 15px 0;color:#555545;font-family:ProximaNova-Regular,Tahoma;font-size:14px;text-align:justify;line-height:20px;">
                                    <?php if ($invite->receiverUser): ?>
                                        Пожалуйста,
                                        <a style="text-decoration:none;color:#147b99;font-family:ProximaNova-Regular,Tahoma;font-size:14px;" href="<?= $this->createAbsoluteUrl('dashboard') ?>">
                                             зайдите
                                        </a> в свой кабинет и примите приглашение на
                                        тестирование для прохождения симуляции.
                                    <?php else: ?>
                                        Пожалуйста,
                                        <a style="text-decoration:none;color:#147b99;font-family:ProximaNova-Regular,Tahoma;font-size:14px;" href="<?= $invite->getInviteLink() ?>">
                                            зарегистрируйтесь
                                        </a> и в своем кабинете примите приглашение на
                                        тестирование для прохождения симуляции.
                                    <?php endif; ?>
                                </p>
                                <a style="text-decoration:none;color:#147b99;font-family:ProximaNova-Regular,Tahoma;font-size:14px;" href="http://www.skiliks.com">
                                        www.skiliks.com
                                </a>
                            </td>
                            <td>
                                <img src="cid:mail-right" style="display:block;"/>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <img src="cid:mail-bottom" style="display:block;" />
                </td>
            </tr>
        </table>
    </body>
</html>