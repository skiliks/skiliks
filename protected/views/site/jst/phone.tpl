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
                        <@ if(model.get('type') === '2') { @>
                        <a class="phone_call_back phone-call-btn" data-dialog-code="<@=model.get('dialog_code')@>">Позвонить</a>
                        <@ } @>
                    </td>
                </tr>
                </tbody>
            </table>
        </li>
        <@ }) @>
    </ul>
</script>

<script type="text/template" id="Phone_Themes">
    <ul>
        <@ themes.each(function(model) { @>
        <li class="phone_call" data-theme-id="<@=model.get('themeId')@>" data-contact-id="<@=contactId@>"><@=model.get('themeTitle')@></li>
        <@ }) @>
    </ul>
</script>

<script type="text/template" id="Phone_Contacts">
    <ul class="phone-contact-list">
        <@ contacts.each(function(model) { @>
        <li id="contactLi_<@=model.get('code')@>" class="contact-li">
            <table>
                <tr>
                    <td class="hover-hide phone-contact-list-img"><img
                            src="<@=assetsUrl@>/img/phone/icon-ch<@=model.get('code')@>.png" alt=""/></td>
                    <td class="hover-show phone-contact-list-img"><img alt=""
                                                                       src="<@=assetsUrl@>/img/phone/icon-ch<@=model.get('code')@>-1.png">
                    </td>
                    <td>
                        <p class="phone-contact-list-f0"><@=model.get('name')@></p>

                        <p class="phone-contact-list-f1"><@=model.get('title')@></p>

                        <p class="hover-show phone-contact-list-f1"><@=model.get('phone')@></p>
                        <a class="hover-show phone_get_themes phone-call-btn" data-contact-id="<@=model.get('code')@>">Позвонить</a>
                    </td>
                </tr>
            </table>
        </li>
        <@ }) @>
    </ul>
</script>
<!--html-->
<script type="text/template" id="Phone_Html">
    <div class="phone-popup-content">
        <div class="phone-bl popup">
            <div class="phone-screen" id="phoneMainScreen">

                <ul class="phone-main-menu">
                    <li class="phone_get_contacts">
                        <img src="<@=assetsUrl@>/img/phone/icon-contact.png" alt="">

                        <p>Список контактов</p>
                    </li>
                    <li class="phone_get_history">
                        <img src="<@=assetsUrl@>/img/phone/icon-contact.png" alt="">

                        <p>История Вызовов</p>
                    </li>
                </ul>
            </div>
            <p class="phone-menu-btn phone_get_menu">меню</p>

        </div>
    </div>
</script>

<script type="text/template" id="Phone_Call">
    <div class="phone-popup-content">
        <div class="phone-bl popup">
        <div class="phone-screen" id="phoneMainScreen">

            <div class="phone-call in">
                <div class="phone-call-img">
                    <img src="<@=SKConfig.assetsUrl@>/img/phone/icon-call-ch<@=call[0].ch_from@>.png" alt="<@=call[0].name@>">
                </div>
                <p class="phone-call-text">
                        <span class="name">
                            <@=call[0].name@>
                        </span>
                    <br><@=call[0].title@><br>
                    <span class="post">&nbsp;</span>
                </p>
                <ul class="phone-call-in-btn">
                    <@ if (undefined != call[1]) { @>
                        <li><a id="phone_reply" data-dialog-id="<@=call[1].id@>" class="btn0"><@=call[1].text@></a></li>
                    <@ } @>

                    <@ if (undefined != call[2]) { @>
                        <li><a id="phone_no_reply" data-dialog-id="<@=call[2].id@>" class="btn1"><@=call[2].text@></a></li>
                    <@ } @>
                </ul>
            </div>

        </div>
        <p class="phone-menu-btn phone_get_menu">меню</p>
            </div>
    </div>
</script>

<script type="text/template" id="Phone_Menu">

            <ul class="phone-main-menu">
                <li class="phone_get_contacts">
                    <img src="<@=assetsUrl@>/img/phone/icon-contact.png" alt="">

                    <p>Список контактов</p>
                </li>
                <li class="phone_get_history">
                    <img src="<@=assetsUrl@>/img/phone/icon-contact.png" alt="">

                    <p>История Вызовов</p>
                </li>
            </ul>

</script>
<!--dialogHTML-->
<script type="text/template" id="Phone_Dialog">
   <div class="phone-content">
        <audio src="<@= audio_src @>" autoplay="autoplay"></audio>
        <div class="phone-bl main">
            <div class="phone-screen">
                <div class="phone-call">
                    <div class="phone-call-img"><img alt=""
                                                     src="<@= SKConfig.assetsUrl @>/img/phone/icon-call-ch<@= (remote_replica !== null  ? remote_replica.ch_from : my_replicas[0].ch_to ) @>.png">
                    </div>
                    <p class="phone-call-text">
                        <span class="name"><@= (remote_replica !== null  ? remote_replica.name : my_replicas[0].remote_name) @></span><br>
                        <@= (remote_replica !== null  ? remote_replica.title : my_replicas[0].remote_title)@><br>
                        <span class="post">&nbsp;</span>
                    </p>
                    <@ if(isUserCanFinalizeCall){ @>
                        <a class="phone-call-end phone-draw-menu">Завершить</a>
                    <@ } @>
                </div>
            </div>

            <a class="phone-menu-btn phone-draw-menu">меню</a>
        </div>

        <div class="phone-reply-field">
            <@ if (remote_replica !== null) { @>
            <p class="phone-reply-ch max"><@=remote_replica.text@></p>
            <@ } @>

            <ul class="phone-reply-h" id="phoneAnswers">
                <@ my_replicas.forEach(function (replica) { @>
                <li><p><a class="replica-select"
                    <@if (replica.is_final_replica === "1") { @>
                    data-is-final="true"
                    <@ } @>
                    data-id="<@= replica.id @>"><@= replica.text @></a></p>
                    <span></span></li>
                <@ }) @>
            </ul>
        </div>
   </div>
</script>