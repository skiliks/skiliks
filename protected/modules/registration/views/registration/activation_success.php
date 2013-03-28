<h2 class="thetitle longercontent text-center"><?php echo Yii::t('site', 'Your account has been activated'); ?> </h2>
<div class="form registrationform">
    <div class="transparent-boder">
        <div class="radiusthree yellowbg">
            <div class="registermessage registerpads">
                <a class="regicon icon-choose" href="/simulation/promo/2">Выбрать</a>
                <h3>Пробный тест</h3>
                <div class="testtime"><strong>45</strong> Минут</div>
                <ul>
                    <li>Частичная оценка навыков бесплатно</li>
                    <li>Погружение в игровую среду для понимания, как работает симуляция</li>
                    <li>Опыт прохождения теста</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<p class="text-center longercontent"> <?php Yum::t('Click {here} to go to the login form', array(
			'{here}' => CHtml::link(Yum::t('here'), Yum::module()->loginUrl
				))); ?> </p>

<div class="text-center longercontent"><a href="/dashboard/" class="bigbtnsubmt">Далее</a></div>
