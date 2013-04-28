<?php /** @var Invite $invite */ ?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Ruseller Email Newsletter</title>
        <style type="text/css">
            img {display: block;}
            h1 {color:#626250; font: 'ProximaNova-Bold'; font-size:30px; margin:0 0 10px 0; padding:0;}
            p {margin:0 0 10px 0; color:#555545; font-family:'ProximaNova-Regular';font-size:16px;}
            a {text-decoration:none; color:#147b99;font-family: 'ProximaNova-Regular'; font-size: 18px;}
        </style>
    </head>
    <body>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <img src="http://kilimov.ho.ua/img/mail-top.png" style="display:block"/>
                </td>
            </tr>
            <tr>
                <td>
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="41">
                                <img src="http://kilimov.ho.ua/img/mail-left.png" style="display:block"/>
                            </td>
                            <td bgcolor="fdfbc6" width="489" height="344" valign="top">
                                <h1 style="color:#626250; font: 'ProximaNova-Bold'; font-size:30px; margin:0 0 10px 0; padding:0;">
                                        <?= $invite->getReceiverUserName() ?>, приветствуем Вас!
                                </h1>

                                <p style="margin:0 0 10px 0; color:#555545; font-family:'ProximaNova-Regular';font-size:16px;">
                                       <?= $invite->ownerUser->account_corporate->company_name ?: 'Компания' ?> предлагает вам пройти тест «Базовый менеджмент» для участия в конкурсе на вакансию
                                        <a style="text-decoration:none; color:#147b99;font-family: 'ProximaNova-Regular'; font-size: 18px;" href="<?= $invite->vacancy->link ?: '#' ?>">
                                                <?= $invite->getVacancyLabel() ?>
                                        </a>.
                                </p>

                                <p style="margin:0 0 10px 0; color:#555545; font-family:'ProximaNova-Regular';font-size:16px;">
                                    <?php if (empty($invite->receiverUser)): ?>
                                        <a style="text-decoration:none; color:#147b99;font-family: 'ProximaNova-Regular'; font-size: 18px;" href="<?= $this->createAbsoluteUrl('static/pages/product') ?>">
                                                «Базовый менеджмент»
                                        </a> - это деловая симуляция, позволяющая оценить менеджерские навыки в форме увлекательной игры.
                                    <?php endif; ?>
                                </p>

                                <p style="margin:0 0 10px 0; color:#555545; font-family:'ProximaNova-Regular';font-size:16px;">
                                    <?= $invite->message ?>
                                </p>

                                <p style="margin:0 0 10px 0; color:#555545; font-family:'ProximaNova-Regular';font-size:16px;">
                                    <?php if ($invite->receiverUser): ?>
                                        Пожалуйста,
                                        <a style="text-decoration:none; color:#147b99;font-family: 'ProximaNova-Regular'; font-size: 16px;" href="<?= $this->createAbsoluteUrl('dashboard') ?>">
                                             зайдите
                                        </a> в свой кабинет и примите приглашение на тестирование для прохождения симуляции.
                                    <?php else: ?>
                                        Пожалуйста,
                                        <a style="text-decoration:none; color:#147b99;font-family: 'ProximaNova-Regular'; font-size: 16px;" href="<?= $invite->getInviteLink() ?>">
                                            зарегистрируйтесь
                                        </a> и в своем кабинете примите приглашение на тестирование для прохождения симуляции.
                                    <?php endif; ?>
                                </p>

                                <a style="text-decoration:none; color:#147b99; font-family:'ProximaNova-Regular'; font-size: 16px;" href="http://www.skiliks.com">
                                        www.skiliks.com
                                </a>
                            </td>
                            <td width="340">
                                <img src="http://kilimov.ho.ua/img/mail-right.png" style="display:block"/>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <img src="http://kilimov.ho.ua/img/mail-bottom.png" style="display:block"/>
                </td>
            </tr>
        </table>
    </body>
</html>