<?php
$cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-ui-1.8.24.custom.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.tablesorter.js', CClientScript::POS_BEGIN);

$cs->registerCssFile($assetsUrl . '/js/jquery/jquery-ui.css');
?>

<style>
    .dashboard .form label {
        color: #555545;
        display: inline-block;
        font: 0.834em/1 "ProximaNova-Bold";
        margin-right: -5px;
        vertical-align: middle;
        width: 60px;
    }

    .dashboard .sbHolder {
        width: 334px;
    }

    .invites-list {
        width: 600px;
        background-color: #fdfbc6;
    }

    .invites-list th, .invites-list td {
        padding: 10px;
        font-size: 14px;
        font-weight: bold;
    }

    .invites-list th.sort-asc:after {
        content: '\2191';
        padding-left: 10px;
    }

    .invites-list th.sort-desc:after {
        content: '\2193';
        padding-left: 10px;
    }

    .items {
        margin: 10px 0;
    }

    .items td, .items th {
        background: #e8f7f7;
        border: 1px solid #146672;
        padding: 5px 15px;
    }

    h2 {
        font-size: 16px;
        margin: 15px 0px 10px 0px;
    }

    h3 {
        font-size: 15px;
        margin: 7px 0px 10px 30px;
    }

    #send-invite-message-form label,
    #invite-form label {
        width: 150px;
    }

    #send-invite-message-form label {
        display: inline-block;
        padding: 3px 0 0 0;
        vertical-align: top;
    }

    #send-invite-message-form .row,
    #invite-form .row {
        margin: 5px 0 5px 0 ;
    }

    #send-invite-message-form textarea,
    #send-invite-message-form input {
        display: inline-block;
        width: 550px;
    }

    #send-invite-message-form .buttons input {
        display: block;
        margin: 0 auto;
        width: 450px;
    }

    .invites-limit {
        background: none repeat scroll 0 0 #146672;
        border-radius: 3px;
        box-shadow: 0 0 0 6px rgba(255, 255, 255, 0.2);
        color: #fff;
        display: block;
        margin: 15px 0 15px 10px;
        padding: 5px 15px;
        width: 300px;
    }

    .small-invites-limit {
        box-shadow: 0 0 0 6px rgba(255, 255, 255, 0.8);
        font-size: 16px;
        font-weight: bold;
    }

    .yiiPager li {
        display: inline-block;
        padding: 2px 7px;
    }

    .yiiPager li a{
        color: #146672;
    }

    .yiiPager li.selected a{
        color: #000;
        cursor: default;
        font-weight: bold;
        text-decoration: none;
    }

    .errorMessage {
        background-color: #FFE0E1;
        border-radius: 5px;
        color: #cd0a0a;
        display: block;
        margin: 0 0 5px 0;
        padding: 2px 7px;
        width: 535px;
    }

    .flash {
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.8);
        background-color: #E8F7F7;
        border-radius: 5px;
        margin: 10px;
        opacity: 0.5;
        padding: 10px 15px;
        width: 850px;
    }
</style>

<?php foreach(Yii::app()->user->getFlashes() as $key => $message) : ?>
    <div class="flash flash-'<?php echo $key ?>"><?php echo $message ?></div>
<?php endforeach ?>

<section class="dashboard">
    <h2><?php echo Yii::t('site', 'Dashboard') ?></h2>

    <div class="form">
        <h3><?php echo Yii::t('site', 'Invitations') ?></h3>

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'invite-form'
        )); ?>

        <?php echo $form->error($invite, 'invitations'); // You has no available invites! ?>

        <div class="row">
            <?php echo $form->labelEx($invite, 'full_name'); ?>
            <?php echo $form->textField($invite, 'firstname', ['placeholder' => Yii::t('site','First name')]); ?>
            <?php echo $form->textField($invite, 'lastname', ['placeholder'  => Yii::t('site','Last Name')]); ?>
            <?php echo $form->error($invite, 'firstname'); ?>
            <?php echo $form->error($invite, 'lastname'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($invite, 'email'); ?>
            <?php echo $form->textField($invite, 'email', ['placeholder' => Yii::t('site','Enter Email address')]); ?>
            <?php echo $form->error($invite, 'email'); ?>
        </div>

        <div class="row wide">
            <?php echo $form->labelEx($invite, 'position_id'); ?>
            <?php echo $form->dropDownList($invite, 'position_id', $positions); ?>
            <?php echo $form->error($invite, 'position_id'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Отправить приглашение', ['name' => 'prevalidate']); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>

    <?php if (Yii::app()->user->data()->isCorporate()) : ?>
        <div class="invites-limit <?php echo (Yii::app()->user->data()->countInvitesToGive() < 10) ? 'small-invites-limit' : ''; ?>">
            У Вас осталось: <?php echo Yii::app()->user->data()->getAccount()->invites_limit?> приглашений
        </div>
    <?php endif ?>

    <?php if (!empty($valid)): ?>
    <div class="form form-invite-message message_window" title="Введите текст письма">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'send-invite-message-form',
        )); ?>

        <?php echo $form->hiddenField($invite, 'firstname'); ?>
        <?php echo $form->hiddenField($invite, 'lastname'); ?>
        <?php echo $form->hiddenField($invite, 'email'); ?>
        <?php echo $form->hiddenField($invite, 'position_id'); ?>

        <div class="row">
            <?php echo $form->labelEx($invite, 'To'); ?>
            <?php echo $form->textField($invite, 'fullname'); ?>
        </div>

        <br/>
        <br/>

        <div class="row">
            <?php echo $form->labelEx($invite, 'message text'); ?>
            <?php echo $form->textArea($invite, 'message', ['rows' => 10, 'cols' => 60]); ?>
            <?php echo $form->error($invite, 'message'); ?>
        </div>

        <br/>
        <br/>

        <div class="row">
            <?php echo $form->labelEx($invite, 'signature'); ?>
            <?php echo $form->textField($invite, 'signature'); ?>
            <?php echo $form->error($invite, 'signature'); ?>
        </div>

        <br/>
        <br/>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Отправить', ['name' => 'send']); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
    <?php endif; ?>

    <?php // edit invite pop-up form { ?>
    <?php $this->renderPartial('../partials/edit-invite-pop-up-form', [
        'invite'    => $inviteToEdit,
        'positions' => $positions,
    ]) ?>
    <?php if (0 < count($inviteToEdit->getErrors())): ?>
        <script type="text/javascript">
            $(function(){
                $( ".form-invite-message-editor").dialog('open');
            });
        </script>
    <?php endif; ?>

    <?php // edit invite pop-up form } ?>

    <?php
        $this->widget('zii.widgets.grid.CGridView', [
            'dataProvider' => Invite::model()->search(Yii::app()->user->data()->id), //$dataProvider,
            'summaryText' => '',
            'pager' => [
                'header'        => false,
                'firstPageLabel' => '<< начало',
                'prevPageLabel' => '< назад',
                'nextPageLabel' => 'далее >',
                'lastPageLabel' => 'конец >>',
            ],
            'columns' => [
                ['header' => Yii::t('site', 'Full name')  , 'name' => 'name'        , 'value' => '$data->getFullname()'],
                ['header' => Yii::t('site', 'Position')   , 'name' => 'position_id' , 'value' => '$data->position->label'],
                ['header' => Yii::t('site', 'Status')     , 'name' => 'status'      , 'value' => '$data->getStatusText()'],
                ['header' => Yii::t('site', 'Date / time'), 'name' => 'sent_time'   , 'value' => '$data->getSentTime()->format("j/m/y G\h i\m")'],
                ['header' => Yii::t('site', 'Score')                                , 'value' => '"-"'],
                ['header' => ''                                                     , 'value' => '"<a href=\"invite/remove/$data->id\">удалить</a>"'                , 'type' => 'html'],
                ['header' => ''                                                     , 'value' => '"<a class=\"edit-invite\" href=\"$data->id&&$data->position_id\" title=\"$data->firstname, $data->lastname\">исправить</a>"', 'type' => 'html'],
                ['header' => ''                                                     , 'value' => '"<a href=\"invite/resend/$data->id\">отправить <br/>ещё раз</a>"' , 'type' => 'html'],
            ]
        ]);
    ?>

</section>

<script type="text/javascript">
    $(function() {
        // @link: http://jqueryui.com/dialog/
        $( ".message_window" ).dialog({
            modal: true,
            width: 780

        });

        $( ".message_window").dialog('open');
    });

    $.tablesorter.addParser({
        id: 'customDate',
        is: function (s) {
            return /\d{1,2}\/\d{1,2}\/\d{1,4} \d{1,2}h \d{1,2}m/.test(s);
        },
        format: function (s) {
            s = s.match(/(\d+)\/(\d+)\/(\d+) (\d+)h (\d+)m/);
            return $.tablesorter.formatFloat(new Date('20'. s[3], s[2] - 1, s[1], s[4], s[5], 0).getTime());
        },
        type: 'numeric'
    });

    $('.invites-list').tablesorter({
        cssAsc: 'sort-asc',
        cssDesc: 'sort-desc',
        sortList: [[3, 1]]
        });
</script>