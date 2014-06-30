<?php $titlesInvite = [
    'ID инвайта',
    'статус',
    'ID симуляции',
    'action',
    'Дата и время',
    'комментарий',
];
?>

<div class="row fix-top">

    <!-- Invite: -->
    <h2>Лог операций над приглашением <?= $invite->id ?></h2>

    <table class="table table-bordered">
        <tr>
            <td>От</td>
            <td> <?= (null !== $invite && null !== $invite->ownerUser) ? $invite->ownerUser->profile->email:'-' ?></td>
        </tr>
        <tr>
            <td>для</td>
            <td> <?= (null !== $invite) ? $invite->email:'-' ?></td>
        </tr>
        <tr>
            <td>Оценка</td>
            <td> <?= (null !== $invite->getOverall()) ? $invite->getOverall() : '-'; ?></td>
        </tr>
        <tr>
            <td>Процентиль</td>
            <td> <?= (null !== $invite->getPercentile()) ? $invite->getPercentile() : '-'; ?></td>
        </tr>
        <tr>
            <td>Статус</td>
            <td> <?= Invite::$statusTextRus[$invite->status] ?></td>
        </tr>
        <tr>
            <td>Дата создания</td>
            <td> <?= (null !== $invite->sent_time) ? $invite->sent_time : '-'; ?></td>
        </tr>
        <tr>
            <td>Текущая sim id</td>
            <td>
                <?php if (null !== $invite->simulation_id) : ?>
                    <?php if (Yii::app()->user->data()->can('invite_sim_change')) : ?>
                        <?= $invite->simulation_id ?>
                        (<?= $invite->simulation->game_type->slug ?>)
                        &nbsp;
                        <form action="/admin_area/invite/<?= $invite->id ?>/change-sim-id" method="post" style="display: inline-block">
                            <input type="hidden" name="inviteId" value="<?= $invite->id ?>" />
                            <select name="simId">
                                <option value=""></option>

                                <?php foreach ($simulations as $simulation) : ?>
                                    <?php if (
                                        (null == $simulation->invite)
                                        || (null != $simulation->invite && $invite->id != $simulation->invite->id)
                                    ): ?>
                                        <?php /** @var Simulation $simulation */ ?>
                                        <option value="<?= $simulation->id ?>">
                                            simId: <?= $simulation->id ?> (<?= $simulation->game_type->slug ?>)
                                            <?php if (null !== $simulation->invite) : ?>
                                                ,&nbsp;&nbsp;&nbsp;
                                                current invite: <?= (null !== $simulation->invite) ? $simulation->invite->id : '-' ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endif ?>
                                <?php endforeach; ?>
                            </select>
                            &nbsp;
                            <input type="submit" value="Сменить" class="btn btn-success" style="margin-top: -11px;"/>
                        </form>
                        <br/>
                        <span class="help-inline">
                            (В списке отображаются все симуляции от данного корпоративного аккаунта к персональному,
                            указанному в приглашении. Кроме той, что уже связана с данным приглашением.).
                        </span>
                    <?php else: ?>
                        <?= $invite->simulation_id ?>
                    <?php endif; ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <?php if(null !== $invite) : ?>
        <?php $this->renderPartial('//admin_area/partials/_invite_actions', [
            'invite' => $invite,
        ]) ?>
    <?php endif ?>
    <br/>
    <br/>

    <table class="table table-hover">
        <thead>
        <tr>
            <?php foreach($titlesInvite as $title) :?>
                <th><?=$title?></th>
            <?php endforeach ?>
        </tr>
        </thead>
        <tbody>
        <?php /* @var $model Invite*/ ?>
        <?php $step = 12; $i = 0; ?>
        <?php foreach($logInvite as $itemI) : ?>
            <?php $i++ ?>
            <?php if($i === $step) : ?>
                <tr>
                    <?php foreach($titlesInvite as $title) :?>
                        <th><?=$title?></th>
                    <?php endforeach ?>
                </tr>
                <?php $i= 0 ?>
            <?php endif ?>
            <tr class="invites-row">
                <td><?= $itemI->invite_id ?></td>
                <td><?= $itemI->status ?></td>
                <td><?= $itemI->sim_id ?></td>
                <td><?= $itemI->action ?></td>
                <td><?= $itemI->real_date ?></td>
                <td><?= $itemI->comment ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>

    <?php if (0 === count($logInvite)): ?>
        <div style="text-align: center; width: 100%;">Нет записей.</div>
    <?php endif; ?>

<?php if (null !== $simulation) : ?>
    <!-- Simulation: -->

    <?php $this->renderPartial('//admin_area/pages/simulation_site_logs_table', [
        'logSimulation'    => $logSimulation,
        'simulation'       => $simulation
    ]) ?>

<?php endif; ?>

    <hr/>

    <p>
        <strong>Результат selenium-теста</strong>:
        <?php if($invite->is_crashed !== null) : ?>
            <?= ($invite->is_crashed == 1) ? "Fail" : "Success"; ?>
        <?php else: ?>
            симуляция была пройдена не тестом, а человеком.
        <? endif; ?>
    </p>

    <p>
        <strong>Stack-trace for Selenium text</strong>:
        <?php if($invite->stacktrace !== null) : ?>
            <p>
                <?= $invite->stacktrace; ?>
            </p>
        <?php else: ?>
            пустой stack-trace.
        <? endif; ?>
    </p>
</div>