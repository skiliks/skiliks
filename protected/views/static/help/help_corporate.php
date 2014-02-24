<div class="help-container">
    <section class="dashboard corpdashboard">

        <?php $this->renderPartial('_help_menu', ['active' => ['corporate' => true]]) ?>

        <div class="div-questions-container">

            <div class="nice-border border-radius-standard">
                <ul class="question-container column-full">
                    <li class="solo ">
                        Логин и пароль
                        <div class="hide">
                            <p class="">
                                Логином в системе является корпоративный email, предоставленный при регистрации.
                            </p><p class="">
                                Используйте пароль, введённый при регистрации.
                                Если вы забыли свой пароль, вы можете восстановить его, воспользовавшись ссылкой "Забыли пароль" в меню входа на сайт.
                                Вы в любое время можете сменить пароль в своём  <a href="http://skiliks.com/profile/corporate/password">профиле</a>
                            </p>
                        </div>

                    </li>
                </ul>
            </div>

            <br/>

            <h2>Рабочий кабинет</h2>

            <br/>

            <div class="nice-border border-radius-standard">
                <ul class="question-container column-full">

                    <li class="">
                        Отправка приглашений
                        <div class="hide">
                            <p class="">
                                Введите имя, фамилию и email человека, которого вы хотите протестировать с помощью симуляции.
                                Имя и фамилия отобразятся в тексте приглашения, которое получит адресат.
                            </p><p class="">
                                Выберите вакансию/позицию, на которую вы проводите тестирование, это позволит вам сравнивать кандидатов
                                и сотрудников в рамках данной позиции. Данная вакансия/позиция также будет отображена в отправляемом
                                приглашении.
                            </p><p class="">
                                В окне приглашения вы можете скорректировать текст письма по своему усмотрению.
                            </p>
                        </div>
                    </li>

                    <li class="">
                        Мониторинг статуса приглашений
                        <div class="hide">
                            <p class="">
                                В таблице отправленных приглашений вы можете видеть статус каждого приглашения и время, когда этот статус возник:
                            </p>
                            <br/>
                            <ul class="default ">
                                <li>Ожидание – приглашение отправлено вами, приглашённый пока не предпринял никаких действий</li>
                                <li>Принято – приглашённый зарегистрировался на сайте и принял приглашение на прохождение симуляции</li>
                                <li>Отклонено – приглашённый отклонил ваше приглашение на прохождение симуляции</li>
                                <li>Начато – приглашённый начал прохождение симуляции</li>
                                <li>Готово – симуляция пройдена, доступны результаты</li>
                                <li>Просрочено – прошло 5 дней с момента отправки приглашения, а приглашённый не предпринял никаких действий.</li>
                            </ul>
                            </p>
                            <br/>
                            <p class="">
                                В настройках приглашения вы можете отправить приглашение заново или удалить приглашение (кроме приглашений в статусе "Принято" и "Начато").
                            </p>
                        </div>
                    </li>

                    <li class="">
                        Прохождение симуляции
                        <div class="hide">
                            <p class="">
                                По кнопке "Начать симуляцию" вам доступны симуляции для собственного прохождения. Ваши личные прохождения вычитаются из доступного вам количества симуляций.
                            </p><p class="">
                                Демо-версия симуляции доступна всегда в неограниченном количестве, в ней не происходит оценка навыков, а только демонстрация интерфейсов.
                            </p>
                        </div>
                    </li>

                    <li class="">
                        Просмотр результатов тестирования
                        <div>
                            <p class="">
                                В столбце "Относительный рейтинг" вы видите результат тестируемого по отношению ко всем проходившим
                                симуляцию. Кликнув на данный рейтинг, вы перейдёте к просмотру детального отчёта по оценке навыков.
                            </p>
                        </div>
                    </li>

                    <li class="">
                        Продление и смена тарифного плана
                        <div class="hide">

                            <p class="">
                                Вы можете продлить ваш текущий тарифный план или перейти на новый. Продление текущего тарифного плана
                                возможно по истечении времени его действия. При переходе на новый тарифный план неиспользованные по прежнему тарифному плану симуляции пропадают.
                            </p>
                        </div>
                    </li>

                    <li class="">
                        Способы оплаты
                        <div class="hide">
                            <p class="">Вы можете выбрать один из двух предпочтительных способов оплаты – по счёту или картой.</p>
                            <p class="">Для оплаты по счёту введите ваши реквизиты и мы вышлем вам счёт на email, указанный при регистрации.
                                Симуляции будут зачислены вам после подтверждения оплаты. Банковский перевод обычно занимает 2-3 рабочих дня,
                                если вы хотите начать использовать симуляции раньше, <a href="mailto: invoice@skiliks.com">свяжитесь с нами</a> и мы решим этот вопрос.</p>
                            <p class="">Для оплаты по карте вы будете перенаправлены на сайт платёжной системы.</p>
                        </div>
                    </li>
                </ul>
            </div>

            <br/>

            <h2>Мой профиль</h2>

            <br/>

            <div class="nice-border border-radius-standard">
                <ul class="question-container column-full">

                    <li class="">
                        Личные данные
                        <div class="hide">
                            <p class="">
                                В разделе "Мой профиль" Ваш email является идентификатором в системе, он не изменяем.
                                Ваша должность нужна нам для понимания, какие услуги вам лучше предлагать.
                            </p>
                        </div>
                    </li>

                    <li class="">
                        Пароль
                        <div class="hide">
                            <p class="">
                                Возможность изменения пароля доступа к сайту.
                            </p>
                        </div>
                    </li>

                    <li class="">
                        Информация о компании
                        <div class="hide">
                            <p class="">
                                Название вашей компании используется в приглашениях, которые вы отправляете кандидатам
                                на прохождение симуляции.
                            </p><p class="">
                                Отрасль и размер компании нужны нам для понимания, какие услуги вам лучше предлагать,
                                а также для будущего сервиса сравнения менеджеров по отраслям и размеру компании.
                            </p><p class="">
                                Описание компании может использоваться в приглашениях кандидатам, данный сервис находится в разработке.
                            </p>
                        </div>
                    </li>


                    <li class="">
                        Тариф
                        <div class="hide">
                            <p class="">
                                При первоначальной регистрации активируется тарифный план LiteFree, по которому вы можете использовать
                                3 симуляции бесплатно.
                            </p><p class="">
                                Для смены или продления тарифного плана воспользуйтесь соответствующими кнопками.
                            </p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</div>

<div class="clearfix"></div>