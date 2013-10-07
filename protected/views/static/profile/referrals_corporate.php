<h2 class="thetitle"><?php echo Yii::t('site', 'Profile') ?></h2>



<div class="transparent-boder profilewrap">

    <div>

        <?php $this->renderPartial('_menu_corporate', ['active' => ['referrals' => true]]) ?>
        <div class="profileform radiusthree referalls_list_box">
            <div class="total-rows">Всего приглашенных: </div>
            <span class="referrals_total ProximaNova-Bold"><?=$totalReferrals ?></span><br/>
            <?php $this->renderPartial('_referrals_list', []) ?>

        </div>

    </div>


    <div class="dialogReferralRejected" style="display: none;">
        <div class="list-ordered">
            <p class="font-xxlarge">Вам уже начислена 1 симуляция за приглашение пользователя из <span class="domainName ProximaNova-Bold"></span>.
                <a data-selected="Тарифы и оплата" class="feedback-close-other" href="#">Свяжитесь с нами</a>,
                если вы приглашаете разных корпоративных пользователей в одной компании.
            </p>
        </div>
    </div>

    <div class="dialogReferralPending" style="display: none;">
        <div class="list-ordered">
            <p class="font-xxlarge">Пользователь ещё не зарегистрировался на www.skiliks.com</p>
        </div>
    </div>
</div>