<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title><?= $title ?></title>
    </head>
    <body>

        <table style="width: 900px;" border="0" cellpadding="0" cellspacing="0">

            <?php
            /**
             * первая строка фиксирует ширины всех бужущих колонок (в одном месте)
             */
            ?>
            <tr style="height: 1px;" >
                <?php
                /**
                 * Некоторые почтовые клиенты (MacMail 7.1) не понимает CSS частично или даже полностью.
                 * Поэтому приходится фиксировать таблицу с помощью другой таблицы.
                 *
                 * (!) Если заменить внутреннюю пустую таблицу на div, то всё равно не работает :(
                 * - так что всё на таблицах.
                 */
                ?>
                <td style="width: 10px;"><table cellspacing="0" cellpadding="0" width="10px"><tbody><tr><td></td></tr></tbody></table></td>
                <td style="width: 30px;"><table cellspacing="0" cellpadding="0" width="30px"><tbody><tr><td></td></tr></tbody></table></td>
                <td style="width: 490px;"><table cellspacing="0" cellpadding="0" width="470px"><tbody><tr><td></td></tr></tbody></table></td></td>
                <td style="width: 270px;"><table cellspacing="0" cellpadding="0" width="270px"><tbody><tr><td></td></tr></tbody></table></td></td>
                <td style="width: 30px;"><table cellspacing="0" cellpadding="0" width="30px"><tbody><tr><td></td></tr></tbody></table></td>
                <td style="width: 10px;"><table cellspacing="0" cellpadding="0" width="10px"><tbody><tr><td></td></tr></tbody></table></td>
                <td style="width: 60px;"><table cellspacing="0" cellpadding="0" width="60px"><tbody><tr><td></td></tr></tbody></table></td>
            </tr>

            <?php
            /**
             * Шапка + портрет героя (кресла)
             */
            ?>
            <tr style="height: 199px;">
                <td colspan="3" style=" width: 530px; vertical-align: top; height: 199px;"><img border="0px" src="cid:top-left" style="border-color: #ff0000; display:block; width: 530px; height: 199px;" /></td>
                <?php
                /**
                 * Некоторые почтовые клиенты (Outlook 2007) отображают под картинкой отступ 3px,
                 * чтобы этого небыло надо задать картинке "display: block;"
                 *
                 * Также некоторые почтовые клиенты по умолчанию рисуют рамки у таблиц и картирок - мы обнуляем эти рамки.
                 */
                ?>
                <td colspan="4" rowspan="2" style=" width: 470px; vertical-align: top; height: 612px;"><img border="0px" src="cid:<?= $template ?>" style="border-color: #ff0000; display:block; height: 612px;" /></td>
            </tr>

            <?php
            /**
             * Основное тело письма
             */
            ?>
            <tr style="height: 413px;">
                <td style="width: 10px;  height: 413px;"  bgcolor="f2f2f2"></td>
                <td style="width: 30px;  height: 413px;"  bgcolor="fdfbc6"></td>
                <td style="width: 490px; height: 413px; vertical-align: top;" bgcolor="fdfbc6">
                    <h1 style="
                        color:#626250;
                        font-family:Tahoma, Geneva, sans-serif;
                        font-size:28px;
                        line-height:32px;
                        margin:10px 0 15px 0;
                        padding:0;
                        width: 490px;
                        ">
                        <?= $h1 ?>
                    </h1>

                    <p style="
                        margin:0 0 15px 0;
                        color:#555545;
                        font-family:Tahoma, Geneva, sans-serif;
                        font-size:14px;
                        text-align:justify;
                        line-height:20px;
                        ">
                        <?= $text1 ?>
                    </p>
                </td>
            </tr>

            <?php
            /**
             * Место для дополнительного теска, если письмо длинное и текст не влазит в основной блок
             */
            ?>
            <tr style="border: 0;">
                <td bgcolor="f2f2f2"></td>
                <td bgcolor="fdfbc6"></td>
                <td bgcolor="fdfbc6" colspan="2">
                    <p style="
                        margin:0 0 15px 0;
                        color:#555545;
                        font-family:Tahoma, Geneva, sans-serif;
                        font-size:14px;
                        text-align:justify;
                        line-height:20px;
                        ">
                        <?= $text2 ?>
                    </p>
                    <p style="margin:0 0 15px 0;text-align:justify;line-height:20px;"><strong style="color:#555545;font-family:Tahoma, Geneva, sans-serif;font-weight:bold;font-size:14px">Ваш skiliks</strong></p>
                    <p><a style="text-decoration:none;color:#147b99;font-family:Tahoma, Geneva, sans-serif;font-size:14px;" href="http://www.skiliks.com">www.skiliks.com</a></p></td>
                </td>
                <td bgcolor="fdfbc6"></td>
                <td bgcolor="f2f2f2"></td>
                <td></td>
            </tr>

            <tr style="height: 19px;">
                <td colspan="6" style="width: 840px; height: 19px; vertical-align: top;"><img height="19px" border="0px" src="cid:bottom" style="border-color: #ff0000; display:block; height: 19px; width: 840px;" /></td>
                <td><table cellspacing="0" cellpadding="30"><tbody><tr><td></td></tr></tbody></table></td>
            </tr>

        </table>

    </body>
</html>