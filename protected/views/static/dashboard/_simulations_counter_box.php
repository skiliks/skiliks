<div class="invites-limit <?php echo (Yii::app()->user->data()->countInvitesToGive() < 10) ? 'small-invites-limit' : ''; ?>">
    <strong>Осталось симуляций</strong> <span class="brightblock"><?php echo Yii::app()->user->data()->getAccount()->invites_limit?></span>
</div>
<div><strong>Тарифный план</strong> <a class="brightblock">Professional</a></div>
<a href="#" class="greenbtn">Управление подпиской</a>