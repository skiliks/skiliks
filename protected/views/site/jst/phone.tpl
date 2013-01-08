<!--historyOneHTML-->
<script type="text/template" id="Phone_HistoryOne">
<li>
    <table>
            <tbody>
                <tr>
                    <td class="phone-contact-list-img"><img alt=""  src="'+SKConfig.assetsUrl+'/img/phone/icon-call-<@=type@>.png"></td>
                <td>
                    <p class="phone-contact-list-f0"><@=name@></p>
                    <p class="phone-contact-list-f1"><@=date@></p>
                </td>
                </tr>
            </tbody>
    </table>
</li>
</script>
<!--contactHTML-->
<script type="text/template" id="Phone_Contact">
        <table>
            <tr>
            <td class="phone-contact-list-img"><img src="'+SKConfig.assetsUrl+'/img/phone/icon-ch<@=charackter_id@>.png" alt="" /></td>
            <td>
            <p class="phone-contact-list-f0"><@=name@></p>
            <p class="phone-contact-list-f1"><@=title@></p>
            </td>
            </tr>
        </table>
</script>
<!--contactHTMLActive-->
<script type="text/template" id="Phone_ContactActive">
        <table class="active">
            <tbody><tr>
            <td class="phone-contact-list-img"><img alt=""  src="'+SKConfig.assetsUrl+'/img/phone/icon-ch<@=id@>-1.png"></td>
            <td>
            <p class="phone-contact-list-f0"><@=name@></p>
            <p class="phone-contact-list-f1"><@=title@></p>
            <p class="phone-contact-list-f1"><@=phone@></p>
            <a class="phone-call-btn" onclick="phone.getThemes(<@=id@>)">Позвонить</a>
            </td>
            </tr>
            </tbody></table>
</script>
<!--html-->
<script type="text/template" id="Phone_Html">
        <section class="phone popup">
            <header>
            <h1>Телефон</h1>

            <ul class="btn-window">
            <li><button class="btn-set">&nbsp;</button></li>
            <li><button class="btn-cl win-close">&nbsp;</button></li>
            </ul>
            </header>

            <div class="phone-bl popup">
            <div class="phone-screen" id="phoneMainScreen">

            <ul class="phone-main-menu">
            <li onclick="phone.getContacts()">
            <img src="<@=assetsUrl@>/img/phone/icon-contact.png" alt="">
            <p>Список контактов</p>
            </li>
            <li onclick="phone.getHistory()">
            <img src="<@=assetsUrl@>/img/phone/icon-contact.png" alt="">
            <p>История Вызовов</p>
            </li>
            </ul>

            </div>
            <a class="phone-menu-btn" onclick="phone.drawMenu()" href="#">меню</a>
            </div>
            </section>
</script>
<!--dialogHTML-->
<script type="text/template" id="Phone_Dialog">
        <section class="phone">
            <header>
            <h1>Телефон</h1>

            <ul class="btn-window">
            <li><button class="btn-set">&nbsp;</button></li>
            <li><button class="btn-cl" onclick="phone.draw()">&nbsp;</button></li>
            </ul>
            </header>

            <div class="phone-bl main">
            <div class="phone-screen">
            <div class="phone-call">
            <div class="phone-call-img"><img alt="" src="' +SKConfig.assetsUrl+ '/img/phone/icon-call-ch<@=id@>.png"></div>
            <p class="phone-call-text">
            <span class="name"><@=name@></span><br>
            <@=title@><br>
            <span class="post">&nbsp;</span>
            </p>
            <a class="phone-call-end" onclick="phone.drawMenu(\'menu\')">Завершить</a>
            </div>	
            </div>

            <a class="phone-menu-btn" onclick="phone.drawMenu(\'menu\')">меню</a>
            </div>

            <div class="phone-reply-field">
            <p class="phone-reply-ch max"><@=dialog_text@></p>

            <ul class="phone-reply-h" id="phoneAnswers">
            <@=dialog_answers@>
            </ul>
            </div>
            </section>
</script>