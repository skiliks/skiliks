<?php /** @var Invite $invite */ ?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Skiliks</title>
</head>
<body>
<p>Поступил новый заказ.</p><br/><br/>
<table cellspacing="20">
    <tr>
        <td>Номер заказа</td>
        <td><?=$invoice->id ?></td>
    </tr>

    <tr>
        <td>Название тарифа</td>
        <td><?=$invoice->tariff->label?></td>
    </tr>

    <tr>
        <td>Количество месяцев</td>
        <td><?=$invoice->month_selected ?></td>
    </tr>

    <tr>
        <td>Компания</td>
        <td><?=$user->account_corporate->company_name ?></td>
    </tr>

    <tr>
        <td>Имя</td>
        <td><?=$user->profile->firstname ?></td>
    </tr>

    <tr>
        <td>E-mail</td>
        <td><?=$user->getAccount()->corporate_email ?></td>
    </tr>

    <tr>
        <td><br/><br/>Данные для оплаты:</td>
        <td></td>
    </tr>

    <tr>
        <td>ИНН:</td>
        <td><?=$invoice_data->inn ?></td>
    </tr>

    <tr>
        <td>КПП:</td>
        <td><?=$invoice_data->cpp ?></td>
    </tr>

    <tr>
        <td>Расчетный счет:</td>
        <td><?=$invoice_data->account ?></td>
    </tr>

    <tr>
        <td>БИК:</td>
        <td><?=$invoice_data->bic ?></td>
    </tr>

    <tr>
        <td><br/><br/>Сумма, показанная для оплаты</td>
        <td><?=$invoice->amount ?> руб.</td>
    </tr>

</table>
</body>
</html>