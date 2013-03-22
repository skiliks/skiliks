<div class="invites-limit <?php echo (Yii::app()->user->data()->countInvitesToGive() < 10) ? 'small-invites-limit' : ''; ?>">
    У Вас осталось: <?php echo Yii::app()->user->data()->getAccount()->invites_limit?> приглашений
</div>

<br/>
<br/>
Тариф: хХх