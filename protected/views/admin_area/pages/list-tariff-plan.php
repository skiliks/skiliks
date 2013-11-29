<?php /* @var $user YumUser */ ?>
<h4>
    Все таривные планы с аккаунта,<br>
    <?= $user->profile->firstname.' '.$user->profile->lastname ?>, <?= $user->profile->email ?>, <br>
    <?= $user->account_corporate->company_name ?>
</h4>
<table class="table">

    <?php foreach($user->account_corporate->getAllTariffPlans() as $tariff_plan) : ?>
    <tr>
            <th></th>
            <th></th>
            <th></th>
    </tr>
    <tr>
        <td>Тарифный план</td>
        <td><?= $tariff_plan->id ?></td>
        <td rowspan="3">
            <form class="block-form" action="/admin_area/update-tariff-plan">
                <label>с </label>
                <input type="text" name="started_at" value="<?= $tariff_plan->started_at ?>"><br>
                <label>до </label>
                <input type="text" name="finished_at" value="<?= $tariff_plan->finished_at ?>"><br>
                <input type="hidden" name="tariff_plan_id" value="<?= $tariff_plan->id ?>">
                <input type="submit" class="btn btn-warning" name="" value="Сменить"><br>
            </form>
        </td>
    </tr>
    <tr>
        <td>Статус</td>
        <td><span class="label label-success"><?= $tariff_plan->status ?></span></td>
    </tr>
    <tr>
        <td>Тариф</td>
        <td><span class="label label-info"><?= $tariff_plan->tariff->slug ?></span></td>
    </tr>
    <p></p>
    <?php endforeach ?>
</table>