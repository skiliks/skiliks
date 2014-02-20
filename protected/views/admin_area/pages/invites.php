<?php
$invites = $models;

$titles = [
    'ID инвайта',
    'Sim. ID',
    'Дата создания',
    'Email работодателя',
    'Email соискателя',
    'Сценарий',
    'Статус инвайта',
    'Время окончания tutorial',
    'Время начала симуляции',
    'Время окончания симуляции',
    'Оценка',
    'Процентиль',
    'Результат теста',
    'Можно заново <br/>стартовать приглашение?',
    'Действие'
] ?>
<div class="row fix-top">
    <h2>Инвайты</h2>

    <!--a class="btn btn-primary pull-right" href="/admin_area/invites/save">Экспорт списка</a-->

    <?php $this->widget('CLinkPager',array(
        'header'         => '',
        'pages'          => $pager,
        'maxButtonCount' => 15, // максимальное вол-ко кнопок
    )); ?>

    Страница <?= $page ?> из <?= ceil($totalItems/$itemsOnPage) ?> (<?= $itemsOnPage ?> записей отображено, найдено <?= $totalItems ?>)

    <br/>
    <br/>

    <form id="invites-filter" action="/admin_area/invites" method="get" style="display: inline-block;">
        <input id="invites-filter-page" type="hidden" name="page" value="<?= $page ?>" />
        <table class="table table-bordered">
            <tr>
                <td> <i class="icon-filter"></i> &nbsp; email соискателя: </td>
                <td> <input name="receiver-email-for-filtration" value="<?= $receiverEmailForFiltration ?>"/> </td>
                <td> <i class="icon-filter"></i> &nbsp; Invite id: </td>
                <td> <input name="invite_id" value="<?= $invite_id ?>" style="width: 60px;"/> </td>
            </tr>

            <tr>
                <td> <i class="icon-filter"></i> &nbsp; email отправителя: </td>
                <td> <input name="owner_email_for_filtration" value="<?= $ownerEmailForFiltration ?>"/> </td>
                <td> <i class="icon-filter"></i> &nbsp; Сценарий:  </td>
                <td>
                    <select name="filter_scenario_id">
                        <option value=""></option>
                        <?php if(isset($scenarios)) : ?>
                            <?php foreach($scenarios as $scenario) : ?>
                                <option <?php if($scenario_id == $scenario->id) echo 'selected="selected"' ?>
                                    value="<?=$scenario->id ?>"><?=$scenario->slug ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td></td>
                <td></td>
                <td>Результат теста:</td>
                <td>
                    <select name="is_invite_crashed">
                        <option value="" ></option>
                        <option <?php if($is_invite_crashed != "" && $is_invite_crashed == "0") echo 'selected="selected"' ?> value="0">Success</option>
                        <option <?php if($is_invite_crashed != "" && $is_invite_crashed == "1") echo 'selected="selected"' ?> value="1">Fail</option>
                    </select>
                </td>
            </tr>


            <tr>
                <td> Исключить приглашения самому себе: </td>
                <td> <input type="checkbox" name="exclude_invites_from_ne_to_me"
                    <?= (isset($formFilters['exclude_invites_from_ne_to_me']) && $formFilters['exclude_invites_from_ne_to_me'])
                        ? 'checked="checked"' : ''; ?>
                    /> </td>
                <td> Исключить прохождения разработчиков: </td>
                <td> <input type="checkbox" name="exclude_developers_emails"
                    <?= (isset($formFilters['exclude_developers_emails']) && $formFilters['exclude_developers_emails'])
                        ? 'checked="checked"' : ''; ?>
                    /> </td>
            </tr>
        </table>

        <table class="table table-bordered invite-statuses">
            <tr>
                <td> Показывать приглашения со статусом: </td>
                <td>
                    <span class="btn btn-warning btn-check-all">Отметить все</span>
                    <span class="btn btn-warning btn-uncheck-all">Снять все</span>
                </td>
                <td>
                    <?= Invite::$statusText[Invite::STATUS_PENDING] ?>
                    <input type="checkbox"
                        name="invite_statuses[<?= Invite::STATUS_PENDING ?>]"
                        <?= ($formFilters['invite_statuses'][Invite::STATUS_PENDING]) ? 'checked="checked"' : ''; ?>
                        />
                </td>
                <td>
                    <?= Invite::$statusText[Invite::STATUS_ACCEPTED] ?>
                    <input type="checkbox"
                        name="invite_statuses[<?= Invite::STATUS_ACCEPTED ?>]"
                        <?= ($formFilters['invite_statuses'][Invite::STATUS_ACCEPTED]) ? 'checked="checked"' : ''; ?>
                        />
                </td>
                <td>
                    <?= Invite::$statusText[Invite::STATUS_IN_PROGRESS] ?>
                    <input type="checkbox"
                        name="invite_statuses[<?= Invite::STATUS_IN_PROGRESS ?>]"
                        <?= ($formFilters['invite_statuses'][Invite::STATUS_IN_PROGRESS]) ? 'checked="checked"' : ''; ?>
                        />
                </td>
                <td>
                    <?= Invite::$statusText[Invite::STATUS_COMPLETED] ?>
                    <input type="checkbox"
                        name="invite_statuses[<?= Invite::STATUS_COMPLETED ?>]"
                        <?= ($formFilters['invite_statuses'][Invite::STATUS_COMPLETED]) ? 'checked="checked"' : ''; ?>
                        />
                </td>
                <td>
                    <?= Invite::$statusText[Invite::STATUS_EXPIRED] ?>
                    <input type="checkbox"
                        name="invite_statuses[<?= Invite::STATUS_EXPIRED ?>]"
                        <?= ($formFilters['invite_statuses'][Invite::STATUS_EXPIRED]) ? 'checked="checked"' : ''; ?>
                        />
                </td>
                <td>
                    <?= Invite::$statusText[Invite::STATUS_DECLINED] ?>
                    <input type="checkbox"
                        name="invite_statuses[<?= Invite::STATUS_DECLINED ?>]"
                        <?= ($formFilters['invite_statuses'][Invite::STATUS_DECLINED]) ? 'checked="checked"' : ''; ?>
                        />
                </td>
                <td>
                    <?= Invite::$statusText[Invite::STATUS_DELETED] ?>
                    <input type="checkbox"
                        name="invite_status[]" alue="accepted"
                        <?= ($formFilters['invite_statuses'][Invite::STATUS_DELETED]) ? 'checked="checked"' : ''; ?>
                        />
                </td>
            <tr>
        </table>
        <input type="submit" value="Фильтровать" class="btn btn-warning"/>
        &nbsp; &nbsp; &nbsp;
        <input type="submit" value="Сбросить фильтр" name="clear_form" class="btn btn-warning clear_filter_button"/>
    </form>

    <br/>

    <table class="table table-hover">
        <thead>
        <tr>
            <?php foreach($titles as $title) :?>
                <th><?=$title?></th>
            <?php endforeach ?>
        </tr>
        </thead>
        <tbody>
        <?php /* @var $invite Invite*/ ?>
        <?php $step = 8; $i = 0; ?>

        <?php if (0 == count($invites)): ?>
            <tr>
                <td colspan="<?= count($titles) ?>">Нет результатов.</td>
            </tr>
        <?php endif; ?>

        <?php foreach($invites as $invite) : ?>
        <?php $i++ ?>
        <?php if($i === $step) : ?>
                <tr>
                    <?php foreach($titles as $title) :?>
                        <th><?=$title?></th>
                    <?php endforeach ?>
                </tr>
        <?php $i= 0 ?>
        <?php endif ?>
        <tr class="invites-row">

            <!-- IDs { -->
            <td style="width: 80px;">
                    <i class="icon icon-tag" style="opacity: 0.1" title="Invite ID"></i>
                    <a href="/admin_area/invite/<?= $invite->id?>/site-logs">
                        <?= $invite->id?>
                    </a>
            </td>
            <td>
                <i class="icon icon-check" style="opacity: 0.1" title="Simulation ID"></i>
                <?php if (null === $invite->simulation): ?>
                    --
                <?php else: ?>
                    <a href="/admin_area/simulation/<?= $invite->simulation->id?>/site-logs">
                        <?= $invite->simulation->id ?>
                    </a>
                <?php endif; ?>
            </td>
            <!-- IDs } -->

            <td> <?= (null !== $invite->sent_time) ? $invite->sent_time : '-'; ?></td>

            <!-- users { -->
            <td style="width: 150px;">
                <i class="icon icon-briefcase" style="opacity: 0.1"></i>

                <?php if (null === $invite->ownerUser): ?>
                    --
                <?php else: ?>
                    <a href="/admin_area/user/<?= $invite->ownerUser->id?>/details">
                        <?= $invite->ownerUser->profile->email ?>
                    </a>
                <?php endif; ?>
            </td>
            <td>
                <i class="icon icon-user" style="opacity: 0.1"></i>

                <?php if (null === $invite->receiverUser): ?>
                    <?= $invite->email ?>
                <?php else: ?>
                    <a href="/admin_area/user/<?= $invite->receiverUser->id?>/details">
                        <?= $invite->receiverUser->profile->email ?>
                    </a>
                <?php endif; ?>
            </td>
            <!-- users } -->

            <!-- Scenario, Status -->
            <td style="width: 120px;">
                <span class="label <?= $invite->scenario->getSlugCss() ?>">
                    <?=(empty($invite->scenario->slug)?'Нет данных':$invite->scenario->slug)?>
                </span>
            </td>

            <td>
                <span class="label <?= $invite->getStatusCssClass() ?>">
                    <?= $invite->getStatusText() ?>
                </span>
            </td>

            <td style="width: 220px;">
                <?=(empty($invite->tutorial_finished_at)?'---- -- -- --':$invite->tutorial_finished_at)?>
            </td>

            <td><?=(empty($invite->simulation->start)?'---- -- -- --':$invite->simulation->start)?></td>
            <td><?=(empty($invite->simulation->end)?'---- -- -- --':$invite->simulation->end)?></td>

            <td>
                <?= (null === $invite->getOverall()) ? '--' : $invite->getOverall(); ?>
            </td>

            <td>
                <?= (null !== $invite->getPercentile()) ? $invite->getPercentile() : '--'; ?>
            </td>

            <td>
                <?php if($invite->is_crashed !== null) : ?>
                    <?= ($invite->is_crashed == 1) ? "Fail" : "Success"; ?>
                <? endif; ?>
            </td>

            <td>
                <span style="width: 20px; margin-right: 10px; display: inline-block;">
                    <?= (true == $invite->can_be_reloaded) ? 'yes' : ' no' ?>
                </span>
                <a class="btn btn-success" href="/admin_area/invite/<?= $invite->id ?>/switch-can-be-reloaded">
                    <strong style="color: <?= (true == $invite->can_be_reloaded) ? '#fff' : '#0f0' ?>;">
                        set <?= (true == $invite->can_be_reloaded) ? 'no' : 'yes' ?>
                    </strong>
                </a>
            </td>
            <td class="actions">
                <?php $this->renderPartial('//admin_area/partials/_invite_actions', [
                    'invite' => $invite,
                ]) ?>
            </td>



        </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>