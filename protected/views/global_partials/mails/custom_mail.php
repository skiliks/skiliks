<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Ruseller Email Newsletter</title>
</head>
<body>

<table width="880" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <img src="cid:mail-top-angela" style="display:block;" />
        </td>
    </tr>
    <tr>
        <td valign="top">
            <table cellpadding="0" cellspacing="0" width="880">
                <tr>
                    <td bgcolor="f2f2f2">
                        <table cellspacing="0" cellpadding="5"><tbody><tr><td></td></tr></tbody></table>
                    </td>
                    <td bgcolor="fdfbc6">
                        <table cellspacing="0" cellpadding="15"><tbody><tr><td></td></tr></tbody></table>
                    </td>
                    <td alight="right">
                        <table cellpadding="0" cellspacing="0" width="840" border="0">
                            <tr>
                                <td valign="top" bgcolor="fdfbc6" width="481">
                                    <h1 style="color:#626250;font-family:Tahoma, Geneva, sans-serif;font-size:28px;margin:0 0 15px 0;padding:0;">
                                        Приветствуем, <?= $name ?>!
                                    </h1>
                                    <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                                        Рады сообщить о выходе новой версии skiliks!<br/><br/>
                                        Сначала о главном:
                                        теперь skiliks можно использовать в браузерах InternetExplorer 10 и 11!
                                        Спасибо поклонникам Microsoft за терпение :-).<br/><br/>
                                        В рабочем кабинете корпоративного пользователя добавлены:<br/>
                                    </p>
                                        <ul style="color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                                            <li> Инструкция по интерпретации результатов оценки, где пошагово изложен
                                                алгоритм анализа полученных результатов
                                                (<a style="text-decoration:none;color:#147b99;font-family:Tahoma, Geneva, sans-serif;font-size:14px;"
                                                    href="<?= MailHelper::createUrlWithHostname("public/site/Skiliks_results.pdf") ?>">скачать</a>)</li>
                                            <li> Сводный аналитический отчёт по всем прошедшим тестирование,
                                                выгруженный в Excel</li>
                                        </ul>
                                </td>
                                <td alight="right" width="335"><img src="cid:mail-bottom-angela" style="display:block;" /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
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
                        <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                            В результатах оценки добавлены:
                        </p>
                        <ul style="color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                            <li> Детальное описание каждого показателя</li>
                            <li> Полный отчёт с результатами оценки и описанием всех показателей в формате pdf для печати</li>
                        </ul>
                        <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                            Кроме того, мы повысили стабильность процесса тестирования, точность результатов,
                            улучшили работу настроек безопасности, сделали кабинет и симуляцию немного удобнее.<br/><br/>
                            Используйте skiliks с удовольствием и делитесь своими впечатлениями!<br/><br/>
                            Пожалуйста, <a style="text-decoration:none;color:#147b99;font-family:Tahoma, Geneva, sans-serif;font-size:14px;" href="<?= MailHelper::createUrlWithHostname("user/auth") ?>">зайдите</a> в ваш кабинет для запуска новой версии и отправки приглашений кандидатам.<br/><br/>
                            Спасибо, что вы с нами!</p>
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