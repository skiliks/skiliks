
<?php $isHasMenuItems = (
    Yii::app()->user->data()->can('sim_results_popup_view')
    || Yii::app()->user->data()->can('sim_logs_and_d1_view')
    || Yii::app()->user->data()->can('sim_site_logs_view')
    || Yii::app()->user->data()->can('sim_server_requests_list_view')
); ?>

<?php if(null !== $invite->getOverall() && $isHasMenuItems) : ?>
    <div class="btn-group">
        <a class=" btn btn-info dropdown-toggle" data-toggle="dropdown" href="#">
            Оценки
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu pull-right">

            <?php if (Yii::app()->user->data()->can('sim_results_popup_view')) : ?>
                <li>
                    <a target="_blank" href="/admin_area/simulation_detail?sim_id=<?=$invite->simulation->id?>">
                        <i class="icon-star"></i> Открыть оценки
                    </a>
                </li>
            <?php endif ?>

            <?php if (Yii::app()->user->data()->can('sim_logs_and_d1_view')) : ?>
                <li>
                    <a href="/static/admin/saveLog/<?=$invite->simulation->id?>">
                        <i class="icon-download-alt"></i> Скачать лог с оценками
                    </a>
                </li>
            <?php endif ?>

            <?php if (Yii::app()->user->data()->can('sim_logs_and_d1_view')) : ?>
                <li>
                    <a href="/admin_area/budget?sim_id=<?=$invite->simulation->id?>">
                        <i class="icon-book"></i> Скачать "Сводный бюджет"(D1)
                    </a>
                </li>
            <?php endif ?>

            <?php if (Yii::app()->user->data()->can('sim_site_logs_view')) : ?>
                <li>
                    <a target="_blank" href="/admin_area/simulation/<?= $invite->simulation->id?>/site-logs">
                        <i class="icon-list"></i> Смотреть логи сайта
                    </a>
                </li>
            <?php endif ?>

            <?php if (Yii::app()->user->data()->can('sim_server_requests_list_view')) : ?>
                <li>
                    <a target="_blank" href="/admin_area/simulation/<?= $invite->simulation->id?>/requests">
                        <i class="icon-retweet"></i> Смотреть запросы
                    </a>
                </li>
            <?php endif ?>
        </ul>
    </div>
<?php endif ?>

<?php $isCanRecalculate = !empty($invite->receiverUser->profile)
    && null != $invite->simulation
    && Yii::app()->user->data()->can('invite_recalculate'); ?>

<?php if (
    true == Yii::app()->user->data()->can('invite_roll_back')
    || true == $isCanRecalculate
    || true == Yii::app()->user->data()->can('invite_status_change')
) : ?>

    <div class="btn-group">
        <a class=" btn btn-success dropdown-toggle" data-toggle="dropdown" href="#">
            Статусы
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu pull-right">
            <?php if (Yii::app()->user->data()->can('invite_roll_back')) : ?>
                <li>
                    <a class="reset-invite" href="/admin_area/invite/reset?invite_id=<?=$invite->id?>">
                        <i class="icon-fast-backward"></i> Откатить инвайт
                    </a>
                </li>
            <?php endif ?>

            <?php if($isCanRecalculate) : ?>
                <li>
                    <a href="/admin_area/invite/calculate/estimate?sim_id=<?= $invite->simulation->id ?>&email=<?= $invite->receiverUser->profile->email ?>">
                        <i class="icon-refresh"></i>Пересчитать оценки
                    </a>
                </li>
            <?php endif ?>

            <!-- Сменить статус: -->
            <?php if (Yii::app()->user->data()->can('invite_status_change')): ?>
                <li style="padding-right: 15px;">
                    <a href="#"><i class="icon-tag"></i> Сменить статус на</a>
                    <?php foreach(Invite::$statusText as $id => $text) : ?>
                        <?php if((string)$id !== $invite->status) : ?>
                            <a class="action-invite-status" style="padding-left: 50px;"
                               href="/admin_area/invite/action/status?invite_id=<?=$invite->id?>&status=<?=$id?>">
                                - <?=$text?>
                            </a>
                        <?php endif ?>
                    <?php endforeach ?>
                </li>
            <?php endif ?>
        </ul>
    </div>

<?php endif ?>