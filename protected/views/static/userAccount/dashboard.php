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
</style>

<?php
$cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();
$cs->registerCssFile($assetsUrl . '/js/jquery/jquery-ui.css');
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery.tablesorter.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-ui-1.8.24.custom.js', CClientScript::POS_BEGIN);
?>
<script type="text/javascript">
    $(function() {
        $( ".message_window" ).dialog({
            modal: true,
            width: 550

        });
    });
</script>

<section class="dashboard">
    <h2>Dashboard</h2>

    <div class="form">
        <h3>Invite People</h3>

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'invite-form'
        )); ?>

        <div class="row">
            <?php echo $form->labelEx($invite, 'Name'); ?>
            <?php echo $form->textField($invite, 'firstname', ['placeholder' => 'First name']); ?>
            <?php echo $form->textField($invite, 'lastname', ['placeholder' => 'Last Name']); ?>
            <?php echo $form->error($invite, 'firstname'); ?>
            <?php echo $form->error($invite, 'lastname'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($invite, 'email'); ?>
            <?php echo $form->textField($invite, 'email', ['placeholder' => 'Enter Email address']); ?>
            <?php echo $form->error($invite, 'email'); ?>
        </div>

        <div class="row wide">
            <?php echo $form->labelEx($invite, 'position_id'); ?>
            <?php echo $form->dropDownList($invite, 'position_id', $positions); ?>
            <?php echo $form->error($invite, 'position_id'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Send invite', ['name' => 'prevalidate']); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>

    <?php if (!empty($valid)): ?>
    <div class="form message_window" title="Введите текст письма">
        <h3>Message</h3>

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

        <div class="row">
            <?php echo $form->labelEx($invite, 'message'); ?>
            <?php echo $form->textArea($invite, 'message', ['rows' => 10, 'cols' => 60]); ?>
            <?php echo $form->error($invite, 'message'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($invite, 'signature'); ?>
            <?php echo $form->textField($invite, 'signature'); ?>
            <?php echo $form->error($invite, 'signature'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Send', ['name' => 'send']); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
    <?php endif; ?>

    <?php
    $dataProvider = new CActiveDataProvider('Invite', [
        'criteria' => [
            'condition' => 'inviting_user_id = :myId',
            'order' => 'sent_time',
            'params' => ['myId' => $user->id]
        ],
        'pagination' => [
            'pageSize' => 5,
            'pageVar' => 'page'
        ]
    ]);
    ?>

    <?php if ($dataProvider->getItemCount() > 0): ?>
    <table class="invites-list">
        <thead>
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Status</th>
                <th>Date / time</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getData() as $invite): ?>
            <tr>
                <td><?php echo $invite->getFullname(); ?></td>
                <td><?php echo $invite->position->label; ?></td>
                <td><?php echo $invite->getStatusText(); ?></td>
                <td><?php echo $invite->getSentTime()->format('j/m/y G\h i\m'); ?></td>
                <td>-</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php
        $this->widget('CLinkPager', array(
            'pages' => $dataProvider->getPagination(),
            'header' => false,
            'prevPageLabel' => 'Prev',
            'nextPageLabel' => 'Next'
        ));
    ?>

    <?php endif; ?>

</section>

<script type="text/javascript">
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