<!--historyOneHTML-->
<script type="text/template" id="Phone_History">

    <ul class="phone-contact-list history">
        <@ history.each(function(model) { @>
        <li>
            <table>
                <tbody>
                <tr>
                    <td class="phone-contact-list-img"><img alt=""
                                                            src="<@=assetsUrl@>/img/phone/icon-call-<@=types[model.get('type')]@>.png">
                    </td>
                    <td>
                        <p class="phone-contact-list-f0"><@=model.get('name')@></p>

                        <p class="phone-contact-list-f1"><@=model.get('date')@></p>
                    </td>
                </tr>
                </tbody>
            </table>
        </li>
        <@ }) @>
    </ul>
</script>

<script type="text/template" id="Phone_Contacts">
    <ul class="phone-contact-list">
        <@ contacts.each(function(model) { @>
        <li id="contactLi_<@=model.get('id')@>" class="contact-li">
            <table>
                <tr>
                    <td class="hover-hide phone-contact-list-img"><img
                            src="<@=assetsUrl@>/img/phone/icon-ch<@=model.get('id')@>.png" alt=""/></td>
                    <td class="hover-show phone-contact-list-img"><img alt=""
                                                                       src="<@=assetsUrl@>/img/phone/icon-ch<@=model.get('id')@>-1.png">
                    </td>
                    <td>
                        <p class="phone-contact-list-f0"><@=model.get('name')@></p>

                        <p class="phone-contact-list-f1"><@=model.get('title')@></p>

                        <p class="hover-show phone-contact-list-f1"><@=model.get('phone')@></p>
                        <a class="hover-show phone-call-btn">Позвонить</a>
                    </td>
                </tr>
            </table>
        </li>
        <@ }) @>
    </ul>
</script>
<!--html-->
<script type="text/template" id="Phone_Html">
    <section class="phone popup">
        <header>
            <h1>Телефон</h1>

            <ul class="btn-window">
                <li>
                    <button class="btn-set">&nbsp;</button>
                </li>
                <li>
                    <button window_id="<@=windowID@>" class="btn-cl win-close">&nbsp;</button>
                </li>
            </ul>
        </header>

        <div class="phone-bl popup">
            <div class="phone-screen" id="phoneMainScreen">

                <ul window_id="<@=windowID@>" class="phone-main-menu">
                    <li window_id="<@=windowID@>" class="phone_get_contacts">
                        <img window_id="<@=windowID@>" src="<@=assetsUrl@>/img/phone/icon-contact.png" alt="">

                        <p window_id="<@=windowID@>">Список контактов</p>
                    </li>
                    <li window_id="<@=windowID@>" class="phone_get_history">
                        <img window_id="<@=windowID@>" src="<@=assetsUrl@>/img/phone/icon-contact.png" alt="">

                        <p window_id="<@=windowID@>">История Вызовов</p>
                    </li>
                </ul>

            </div>
            <p window_id="<@=windowID@>" class="phone-menu-btn phone_get_menu">меню</p>
        </div>
    </section>
</script>

<script type="text/template" id="Phone_Call">
        <section class="phone popup">
            <header>
            <h1>Телефон</h1>

            <ul class="btn-window">
            <li><button class="btn-set">&nbsp;</button></li>
            <li><button window_id="<@=windowID@>" class="btn-cl win-close">&nbsp;</button></li>
            </ul>
            </header>

            <div class="phone-bl popup">
            <div class="phone-screen" id="phoneMainScreen">

                <div class="phone-call in">
                    <div class="phone-call-img">
                        <img src="<@=assetsUrl@>/img/phone/icon-call-ch<@=call[0].ch_from@>.png" alt="<@=call[0].name@>">
                    </div>
                        <p class="phone-call-text">
                        <span class="name">
                        </span>
                        <br><@=call[0].name@><br>
                        <span class="post">&nbsp;</span>
                    </p>
                    <ul class="phone-call-in-btn">
                        <li><a window_id="<@=windowID@>" id="phone_reply" data-dialog-id="<@=call[1].id@>" class="btn0"><@=call[1].text@></a></li>
                        <li><a window_id="<@=windowID@>" id="phone_no_reply" data-dialog-id="<@=call[2].id@>" class="btn1"><@=call[2].text@></a></li>
                    </ul>
                </div>
                
            </div>
                <p window_id="<@=windowID@>" class="phone-menu-btn phone_get_menu">меню</p>
            </div>
            </section>
            
</script>

<script type="text/template" id="Phone_Menu">

    <div class="phone-screen" id="phoneMainScreen">

        <ul window_id="<@=windowID@>" class="phone-main-menu">
            <li window_id="<@=windowID@>" class="phone_get_contacts">
                <img window_id="<@=windowID@>" src="<@=assetsUrl@>/img/phone/icon-contact.png" alt="">

                <p window_id="<@=windowID@>">Список контактов</p>
            </li>
            <li window_id="<@=windowID@>" class="phone_get_history">
                <img window_id="<@=windowID@>" src="<@=assetsUrl@>/img/phone/icon-contact.png" alt="">

                <p window_id="<@=windowID@>">История Вызовов</p>
            </li>
        </ul>

    </div>

</script>
<!--dialogHTML-->
<script type="text/template" id="Phone_Dialog">
    <section class="phone">
        <audio src="<@= audio_src @>" autoplay="autoplay"></audio>
        <header>
            <h1>Телефон</h1>

            <ul class="btn-window">
                <li>
                    <button class="btn-set">&nbsp;</button>
                </li>
                <li>
                    <button class="btn-cl win-close">&nbsp;</button>
                </li>
            </ul>
        </header>

        <div class="phone-bl main">
            <div class="phone-screen">
                <div class="phone-call">
                    <div class="phone-call-img"><img alt=""
                                                     src="<@= SKConfig.assetsUrl @>/img/phone/icon-call-ch<@=remote_replica.ch_from@>.png">
                    </div>
                    <p class="phone-call-text">
                        <span class="name"><@=remote_replica.name@></span><br>
                        <@=remote_replica.title@><br>
                        <span class="post">&nbsp;</span>
                    </p>
                    <a class="phone-call-end phone-draw-menu" href="">Завершить</a>
                </div>
            </div>

            <a class="phone-menu-btn phone-draw-menu">меню</a>
        </div>

        <div class="phone-reply-field">
            <p class="phone-reply-ch max"><@=remote_replica.text@></p>

            <ul class="phone-reply-h" id="phoneAnswers">
                <@ my_replicas.forEach(function (replica) { @>
                <li><p><a href="" class="replica-select" data-id="<@= replica.id @>"><@= replica.text @></a></p>
                    <span></span></li>
                <@ }) @>
            </ul>
        </div>
    </section>
</script>