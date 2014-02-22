<?php /* @var $user YumUser */ ?>
<h3>Групповая отправка преглашений от <?= $user->profile->email ?><br>
    <?= $user->profile->firstname.' '.$user->profile->lastname ?>, компания <?= empty($user->account_corporate->company_name)?$user->account_corporate->company_name_for_sales : $user->account_corporate->company_name ?>
</h3>
<br>
<a href="/admin_area/user/43/details">
    &lt;- Вернутья назад, к данным аккаунта пользователя
</a>
<br>
<br>
<form class="form" method="post">
    <table class="table">
        <tr>
            <th style="width: 20%;">Email</th>
            <th style="width: 20%;">Имя</th>
            <th style="width: 20%;">Фамилия</th>
            <th>Текст</th>
        </tr>
        <tr>
            <td><textarea name="site" style="height: 300px;"></textarea></td>
            <td><textarea name="site" style="height: 300px;"></textarea></td>
            <td><textarea name="site" style="height: 300px;"></textarea></td>
            <td>
                <textarea name="site" style="height: 190px; width: 95%;"></textarea>
                <br>
                Позиция &nbsp;&nbsp;
                <select name="filter_scenario_id">
                    <option value=""></option>
                    <option value="1">lite</option>
                    <option value="2">full</option>
                    <option value="3">tutorial</option>
                </select>
                <br>
                Скрыть результаты &nbsp;&nbsp;<input type="checkbox" name="" value="">
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td><button type="submit" name="save_form" value="true" class="btn btn-success">Проверить</button></td>
        </tr>
    </table>
</form>