<h2 class="thetitle"><?php echo Yii::t('site', 'Profile') ?></h2>



<div class="transparent-boder profilewrap">

    <div>

        <?php $this->renderPartial('_menu_corporate', ['active' => ['referrals' => true]]) ?>
        <div class="profileform radiusthree referalls_list_box">
            <div class="total-rows">Всего приглашенных: </div>
            <span class="referrals_total ProximaNova-Bold"><?=$totalReferrals ?></span><br/>
            <?php $this->renderPartial('_referrals_list', ['dataProvider'=>$dataProvider]) ?>

        </div>

    </div>


    <div class="dialogReferralRejected" style="display: none;">
        <div class="list-ordered"><p class="ProximaNova reject-reason-p"></p></div>
    </div>

    <div class="dialogReferralPending" style="display: none;">
        <div class="list-ordered">
            <p class="ProximaNova">Пользователь ещё не зарегистрировался на www.skiliks.com</p>
        </div>
    </div>
</div>