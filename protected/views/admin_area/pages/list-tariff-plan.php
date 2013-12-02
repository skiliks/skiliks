<?php /* @var $user YumUser */ ?>
<h4 style="line-height: 28px;">
    Все таривные планы с аккаунта <?= $user->profile->firstname.' '.$user->profile->lastname ?><br>
    <?= $user->profile->email ?> <br>
    Компания: "<?= $user->account_corporate->company_name ?>"
</h4>
<a class="btn btn-info" href="/admin_area/user/<?= $user->id ?>/details">
    <i class="icon icon-arrow-left icon-white"></i> &nbsp Назад в аккаунт &nbsp
    <strong><?= $user->profile->email ?></strong>
</a>
<br/>
<br/>

<?php $color = "#FFFFFF"; ?>
<?php foreach($user->account_corporate->getAllTariffPlans() as $tariff_plan) : ?>
    <?php if ("#FFFFFF" == $color) { $color = "#FFFFCC"; } else { $color = "#FFFFFF"; } ?>
    <div class="row" style="background-color: <?= $color ?>">
        <div class="span6">
            <table class="table">
                <tr>
                    <td>Тарифный план</td>
                    <td>ID: <?= $tariff_plan->id ?></td>
                </tr>
                <tr>
                    <td>Тариф</td>
                    <td><span class="label label-info"><?= $tariff_plan->tariff->slug ?></span></td>
                </tr>
                <tr>
                    <td>Статус</td>
                    <td><span class="label label-success"><?= $tariff_plan->status ?></span></td>
                </tr>
            </table>
        </div>
        <div class="span6">
            <form class="block-form" action="/admin_area/update-tariff-plan">
                <input type="hidden" name="tariff_plan_id" value="<?= $tariff_plan->id ?>">
                <table class="table">
                    <tr>
                        <td><label>с </label></td>
                        <td><input type="text" name="started_at" value="<?= $tariff_plan->started_at ?>"></td>
                    </tr>
                    <tr>
                        <td><label>до </label></td>
                        <td><input type="text" name="finished_at" value="<?= $tariff_plan->finished_at ?>"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" class="btn btn-warning" name="" value="Сменить"></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
<?php endforeach ?>



