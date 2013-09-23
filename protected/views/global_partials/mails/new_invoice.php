<?php /** @var Invite $invite */ ?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Skiliks</title>
</head>
<body>
<p>Поступил новый заказ.</p><br/><br/>
<table>
    <tr>
        <td>Номер заказа</td>
        <td><?=$invoice->id ?></td>
    </tr>

    <tr>
        <td>Название тарифа</td>
        <td><?=$invoice->tariff->slug?></td>
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
        <td><?=$user->profile->email ?></td>
    </tr>

    <tr>
        <td>Данные для оплаты</td>
        <td><?=nl2br($invoice->additional_data) ?></td>
    </tr>

    <tr>
        <td>Сумма, показанная для оплаты</td>
        <td><?=$invoice->amount ?> руб.</td>
    </tr>

</table>
</body>
</html>