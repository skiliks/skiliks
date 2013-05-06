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
                color:#626250;
                font-family: 'ProximaNova-Bold', Arial;
                font-size:28px;
                margin:2px 0 20px 0;
                padding:0;}
            p {
                margin:0 0 17px 0;
                color:#555545;
                font-family:'ProximaNova-Regular', Tahoma;
                font-size:16px;
                text-align:justify;
                line-height:22px;
            }
            a {
                text-decoration:none;
                color:#147b99;
                font-family: 'ProximaNova-Regular', Tahoma;
                font-size: 16px;
            }
        </style>
    </head>
    <body>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <img src="cid:mail-top">
                </td>
            </tr>
            <tr>
                <td>
                    <table cellpadding="0" cellspacing="0">
                        <tr style="vertical-align: top;">
                            <td width="31" bgcolor="f2f2f2" style="padding-left:10px;">
                                <table bgcolor="fdfbc6" width="100%" height="100%">
                                    <tr><td></td></tr>
                                </table>
                            </td>
                            <td bgcolor="fdfbc6" width="489" height="344" valign="top">
                                <h1>
                                        <?= $invite->getReceiverUserName() ?>, приветствуем Вас!
                                </h1>

                                <p>
                                       <?= $invite->ownerUser->account_corporate->company_name ?: 'Компания' ?>
                                        предлагает вам пройти тест «Базовый менеджмент» для участия в конкурсе на вакансию
                                        <a href="<?= $invite->vacancy->link ?: '#' ?>">
                                                <?= $invite->getVacancyLabel() ?>
                                        </a>.
                                </p>

                                <?php if (empty($invite->receiverUser)): ?>
                                    <p>
                                        <a href="<?= $this->createAbsoluteUrl('static/pages/product') ?>">
                                                «Базовый менеджмент»
                                        </a> - это деловая симуляция, позволяющая оценить менеджерские навыки в форме
                                        увлекательной игры.
                                    </p>
                                <?php endif; ?>

                                <p>
                                    <?= $invite->message ?>
                                </p>

                                <p>
                                    <?php if ($invite->receiverUser): ?>
                                        Пожалуйста,
                                        <a href="<?= $this->createAbsoluteUrl('dashboard') ?>">
                                             зайдите
                                        </a> в свой кабинет и примите приглашение на
                                        тестирование для прохождения симуляции.
                                    <?php else: ?>
                                        Пожалуйста,
                                        <a href="<?= $invite->getInviteLink() ?>">
                                            зарегистрируйтесь
                                        </a> и в своем кабинете примите приглашение на
                                        тестирование для прохождения симуляции.
                                    <?php endif; ?>
                                </p>
                                <p>
                                    <b>Ваш Skiliks</b>
                                </p>
                                <br/>
                                <a href="http://www.skiliks.com">
                                        www.skiliks.com
                                </a>
                            </td>
                            <td width="300" style="padding-right:30px;">
                                <table bgcolor="f2f2f2" width="100%" height="100%" cellspacing="0" cellpadding="0" style="padding-right:10px;">
                                    <tr style="vertical-align: top;">
                                        <td bgcolor="fdfbc6">
                                            <img src="cid:mail-right" style="margin:0 -40px 0 0;"/>
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <img src="cid:mail-bottom" />
                </td>
            </tr>
        </table>
    </body>
</html>