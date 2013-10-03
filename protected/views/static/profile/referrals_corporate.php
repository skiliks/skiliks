
<h2 class="thetitle"><?php echo Yii::t('site', 'Profile') ?></h2>



<div class="transparent-boder profilewrap">

    <div>

        <?php $this->renderPartial('_menu_corporate', ['active' => ['referrals' => true]]) ?>

    <style>

        .profileform #yw0 * {
            border-radius: 0 !important;
            border: 0;
        }

        .profileform #yw0 td {
            border:0;
        }

        .profileform #yw0 td a {
            color: #146672;
        }

        .profileform #yw0 td, #yw0 th {
            padding: 10px;
        }

        .profileform #yw0 td:first-child, #yw0 th:first-child {
            padding-left: 30px;
        }

        .profileform #yw0 th {
            background: #146672;
            text-align: left;
            border: 0;
        }

        .profileform #yw0 th:not(:first-child) {
            border-left: 1px solid #387d88;
        }

        .profileform .grid-view .pager {
            text-align: center;
            margin-bottom: 20px;
        }

        .profileform li.page.selected a {
            color: #146672 !important;
        }

        .profileform li.page a {
            color: #232323 !important;
        }

        .profileform .grid-view .items th a, .items th a, #corporate-invitations-list-box .grid-view .items th a {
            display: block;
            font-weight: normal;
            padding-right: 12px;
        }

        .profileform .grid-view table.items th a {
            -moz-text-blink: none;
            -moz-text-decoration-color: -moz-use-text-color;
            -moz-text-decoration-line: none;
            -moz-text-decoration-style: solid;
            color: #EEEEEE;
        }

        .profileform .previous a {
            background: #146672 !important;
        }

        .profileform .previous.hidden a {
            background: #c6e0e3 !important;
        }

        .profileform .next.hidden a {
            background: #c6e0e3 !important;
        }

        .profileform .next a {
            background: #146672 !important;
        }

        .profileform .summary {
            display: none;
        }

    </style>

        <div class="profileform radiusthree " style="width:660px; padding: 0;">
            <div class="total-rows" style="margin: 30px 0 0 30px; display: inline-block; font-size: 15px;">Всего приглашенных: </div>
            <strong style="display: inline-block;"><?=$totalReferrals ?></strong><br/>
            <?php $this->renderPartial('_referrals_list', []) ?>

        </div>

    </div>


    <div class="dialogReferralRejected" style="display: none;">
        <p>Вам уже начислена 1 симуляция за приглашение пользователя из <span class="domainName"></span>.
        <a data-selected="Тарифы и оплата" class="feedback-close-other" href="#">Свяжитесь с нами</a>, если вы приглашаете разных корпоративных пользователей в одной компании.</p>
    </div>

    <div class="dialogReferralPending" style="display: none;">
        <p>Пользователь ещё не зарегистрировался на www.skiliks.com</p>
    </div>
</div>