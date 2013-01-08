/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(function () {
    "use strict";

    window.SKPhoneDialogView = window.SKWindowView.extend({
        el: 'body .phone-dialog-div',
        initialize:function (){
            console.log("SKPhoneDialogView");
            this.render();
        },
        render:function () {
            var me = this;
            var dialog = this.options.event;

            //логируем
            if (this.activeSubScreen !== 'phoneTalk') {
                SKApp.user.simulation.window_set.closeAll('phone');
                this.activeSubScreen = 'phoneTalk';
                this.talk_window = new SKDialogWindow('phone', 'phoneTalk', dialog ? dialog[0].id : undefined);
                this.talk_window.open();
            }
            var callInHtml = this.dialogHTML;
            var callInHtmlAns = '';


            var fromUsFlag = 0;
            var toUsLastId = 0;

            var soundDuration = 0;
            var sound = '';
            var i = 0;
            dialog.forEach(function (value) {
                if (value['ch_to'] == 1) {
                    sound = value['sound'];
                    toUsLastId = value['id'];
                    soundDuration = value['duration'];

                    callInHtml = php.str_replace('{id}', value['ch_from'], callInHtml);
                    callInHtml = php.str_replace('{name}', value['name'], callInHtml);
                    callInHtml = php.str_replace('{title}', value['title'], callInHtml);
                    callInHtml = php.str_replace('{dialog_text}', value['text'], callInHtml);

                } else {
                    callInHtmlAns += '<li><p onclick="phone.getSelect(\'' + value['id'] + '\')">' + value['text'] + '</p><span></span></li>';
                    fromUsFlag = 1;
                    i++;
                }
            });

            callInHtml = php.str_replace('{id}', '', callInHtml);
            callInHtml = php.str_replace('{name}', '', callInHtml);
            callInHtml = php.str_replace('{title}', '', callInHtml);
            callInHtml = php.str_replace('{dialog_text}', '', callInHtml);

            callInHtml = php.str_replace('{dialog_answers}', callInHtmlAns, callInHtml);

            this.$el.html(callInHtml);


            if (sound != '' && sound != '#') {
                if (sound.indexOf('.wav') > 0) {
                    $('#phoneAnswers').hide();
                    sounds.start(sound, function (audio) {
                        soundDuration = audio.duration;
                        //а вдруг нам надо послушать звук?
                        if (soundDuration > 0) {
                            me.answersShowFlag = 0;
                            setTimeout(function() {$('#phoneAnswers').show()}, soundDuration * 1000);
                        }
                    });
                }
            }

            //а вдруг вариантов ответа нет
            if (fromUsFlag == 0) {
                this.talk_window.setLastDialog(toUsLastId);
                setTimeout(function () {
                    phone.getSelectByTimeout(toUsLastId);
                }, 5000);
            }


        },
        close:function () {
            $('#phoneMainDiv').remove();
            $('#phoneBackDiv').remove();
            $('.phoneMainScreenScrollbar').remove();
            $('.phone-screen-scroll').remove();
            this.issetDiv = false;
            this.status = 0;
        },
        drawInterface:function (action, dialog) {
            if (!this.issetDiv) {
                this.createDiv();
            } else {
                //нас открывают не в первый раз, телефон был открыт
            }
            $('#phoneMainDiv').css('left', this.divLeft + 'px');
            //отрисовываем подложку
            this.removeBack();
            this.createBack();

            this.drawMenu();
            if (typeof(action) === 'undefined') {
                //логируем
                if (this.activeSubScreen !== 'phoneMain') {
                    SKApp.user.simulation.window_set.closeAll('phone');
                    this.activeSubScreen = 'phoneMain';
                    this.main_window = new SKDialogWindow('phone', 'phoneMain', dialog ? dialog[0].id : undefined);
                    this.main_window.open();
                }
            } else if (action === 'income') {
                this.drawIncome(dialog);
            } else if (action === 'dialog') {
                this.dialogDisplay(dialog);
            }

        },
        drawMenu:function () {
            if (this.cancelDialogId != 0) {
                //отправляем запрос, что звонок был отклонен
                sender.phoneGetSelect(this.cancelDialogId);
                this.cancelDialogId = 0;
            }

            $('.phoneMainScreenScrollbar').remove();
            $('.phone-screen-scroll').remove();

            var html = this.html;
            $('#phoneMainDiv').html(html);
        },
        getContacts:function () {
            sender.phoneGetContacts();
        },
        receiveContacts:function (data) {
            //логируем
            if (this.activeSubScreen != 'phoneMain') {
                SKApp.user.simulation.window_set.closeAll('phone');
                this.activeSubScreen = 'phoneMain';
                this.main_window = new SKDialogWindow('phone', 'phoneMain');
                this.main_window.open();
            }

            this.contacts = {};

            var contactsHtml = '<ul class="phone-contact-list">';
            for (var key in data) {
                var value = data[key];
                this.contacts[value.id] = value;

                var contactHtml = this.contactHTML;
                contactHtml = php.str_replace('{charackter_id}', value.id, contactHtml);
                contactHtml = php.str_replace('{id}', value.id, contactHtml);
                contactHtml = php.str_replace('{name}', value.name, contactHtml);
                contactHtml = php.str_replace('{title}', value.title, contactHtml);

                contactHtml = '<li id="contactLi_' + value.id + '" class="contact-li">' +
                    contactHtml +
                    '</li>';

                contactsHtml += contactHtml;
            }
            contactsHtml += '</ul>';

            $('#phoneMainScreen').html(contactsHtml);

            this.scrollK = php.count(this.contacts) * 70;

            this.doScrollable();

            //funcs
            $('.contact-li').hover(function () {
                var id = $(this).get(0).id;
                var idArr = id.split('_');

                if (phone.contactHover == idArr[1]) {
                    return;
                }
                phone.contactHover = idArr[1];

                phone.receiveContacts(phone.contacts);
                var data = phone.contacts[idArr[1]];

                var contactHtml = phone.contactHTMLActive;
                var contactHtml = php.str_replace('{charackter_id}', data['id'], contactHtml);
                contactHtml = php.str_replace('{id}', data['id'], contactHtml);
                contactHtml = php.str_replace('{name}', data['name'], contactHtml);
                contactHtml = php.str_replace('{title}', data['title'], contactHtml);
                contactHtml = php.str_replace('{phone}', data['phone'], contactHtml);

                $('#' + id).html(contactHtml);
            }, function () {
                var id = $(this).get(0).id;
                var idArr = id.split('_')

                var data = phone.contacts[idArr[1]];

                var contactHtml = phone.contactHTML;
                var contactHtml = php.str_replace('{charackter_id}', data['id'], contactHtml);
                contactHtml = php.str_replace('{id}', data['id'], contactHtml);
                contactHtml = php.str_replace('{name}', data['name'], contactHtml);
                contactHtml = php.str_replace('{title}', data['title'], contactHtml);
                contactHtml = php.str_replace('{phone}', data['phone'], contactHtml);

                $('#' + id).html(contactHtml);
            });
        },
        getThemes:function (id) {
            this.companion = id;
            sender.phoneGetThemes(id);
        },
//темы для общения начало
        receiveThemes:function (data) {
            var topZindex = php.getTopZindexOf();

            var div = document.createElement('div');
            div.setAttribute('id', 'phoneCallThemesDiv');
            div.setAttribute('class', 'mail-new-drop');
            div.style.position = "absolute";
            div.style.zIndex = (topZindex + 2);
            document.body.appendChild(div);
            var offsets = $('#phoneMainDiv').offset();

            $('#phoneCallThemesDiv').css('top', (offsets.top + 20) + 'px');
            $('#phoneCallThemesDiv').css('left', (offsets.left + 400) + 'px');
            $('#phoneCallThemesDiv').css('width', '300px');

            var receivers = '<ul>';
            for (var key in data) {
                var value = data[key];
                receivers += '<li onclick="phone.callTo(' + key + ')">' + value + '</li>';
            }
            receivers += '</ul>';
            var Cheight = (php.count(data) * 26);
            receivers += '<div class="phone-call-themes-scroll" style="height:' + Cheight + 'px;"></div>';
            $('#phoneCallThemesDiv').html(receivers);

            //закрывалка
            var div = document.createElement('div');
            div.setAttribute('id', 'phoneCallThemesDivClose');
            div.style.position = "absolute";
            div.style.zIndex = (topZindex + 1);
            document.body.appendChild(div);

            $('#phoneCallThemesDivClose').css('top', '0px');
            $('#phoneCallThemesDivClose').css('left', '0px');
            $('#phoneCallThemesDivClose').css('width', '100%');
            $('#phoneCallThemesDivClose').css('height', '100%');

            $('#phoneCallThemesDivClose').click(function () {
                phone.hideThemes();
            });

            this.themesScroll(Cheight);
        },
        hideThemes:function () {
            $('#phoneThemesScrollbar').remove();
            $('#phoneCallThemesDivClose').remove();
            $('#phoneCallThemesDiv').remove();
        },
        //темы для общения конец
        callTo:function (themeId) {
            this.hideThemes();
            sender.phoneCallTo(this.companion, themeId);
        },
        getHistory:function () {
            sender.phoneGetHistory();
        },
        receiveHistory:function (data) {
            //логируем
            if (this.activeSubScreen != 'phoneMain') {
                simulation.window_set.closeAll('phone');
                this.activeSubScreen = 'phoneMain';
                this.main_window = new SKDialogWindow('phone', 'phoneMain');
                this.main_window.open();
            }

            this.missedCalls = 0;
            icons.removeIconCounter('phone');

            var historyHtml = '<ul class="phone-contact-list history">';
            for (var key in data) {
                var value = data[key];
                var type = 'in';
                if (value['type'] == 1) {
                    type = 'out';
                } else if (value['type'] == 2) {
                    type = 'miss';
                }

                var historyOneHTML = this.historyOneHTML;
                historyOneHTML = php.str_replace('{type}', type, historyOneHTML);
                historyOneHTML = php.str_replace('{name}', value['name'], historyOneHTML);
                historyOneHTML = php.str_replace('{date}', value['date'], historyOneHTML);

                historyHtml += historyOneHTML;
            }
            historyHtml += '</ul>';

            $('#phoneMainScreen').html(historyHtml);

            this.scrollK = php.count(data) * 70;
            this.doScrollable();
        },
        drawIncome:function (dialog) {
            //логируем
            if (this.activeSubScreen != 'phoneCall') {
                SKApp.user.simulation.window_set.closeAll('phone');
                this.activeSubScreen = 'phoneCall';
                this.window = new SKDialogWindow('phone', 'phoneCall', dialog[0].id);
                this.window.open();
            }

            var callInHtml = '<div class="phone-call in">';
            var callInHtmlMain = '';
            var callInHtmlAns = '<ul class="phone-call-in-btn">';


            var fromUsFlag = 0;
            var toUsLastId = 0;

            var sound = '';
            var i = 0;
            for (var key in dialog) {
                var value = dialog[key];
                if (value['ch_to'] == 1) {
                    sound = value['sound']
                    toUsLastId = value['id'];
                    callInHtmlMain += '<div class="phone-call-img"><img src="'+ SKConfig.assetsUrl +'/img/phone/icon-call-ch' + value['ch_from'] + '.png" alt=""></div>' +
                        '<p class="phone-call-text">' +
                        '<span class="name">' + value['name'] + '</span><br>' +
                        '' + value['title'] + '<br>' +
                        '<span class="post">&nbsp</span>' +
                        '</p>';

                } else {

                    value['text'] = php.str_replace('не ответить', 'ОТКЛОНИТЬ', value['text']);
                    value['text'] = php.str_replace('ответить', 'ПРИНЯТЬ', value['text']);


                    callInHtmlAns += '<li><a onclick="phone.getSelect(\'' + value['id'] + '\',' + i + ');" class="btn' + i + '">' + value['text'] + '</a></li>';
                    fromUsFlag = 1;
                    i++;
                }
            }

            // check way with no "Cancel" button
            if ('undefined' !== typeof dialog[2] && 'undefined' !== typeof dialog[2]['id']) {
                this.cancelDialogId = dialog[2]['id'];
            }

            callInHtmlAns += '</ul>'


            callInHtml += callInHtmlMain + callInHtmlAns;
            callInHtml += '</div>';

            $('#phoneMainScreen').html(callInHtml);


            if (sound != '' && sound != '#') {
                if (sound.indexOf('.wav') > 0) {
                    sounds.start(sound);
                }
            }

            //а вдруг вариантов ответа нет
            if (fromUsFlag == 0) {
                this.talk_window.setLastDialog(toUsLastId);
                setTimeout(function () {
                    phone.getSelectByTimeout(toUsLastId);
                }, 5000);
            }
        },
        getSelectByTimeout:function (dialogId, flag) {
            if (!sounds.isset) {
                sender.phoneGetSelect(dialogId);
            } else {
                setTimeout(function () {
                    phone.getSelectByTimeout(dialogId);
                }, 1000);
            }


        },
        getSelect:function (dialogId, flag) {
            var me = this;
            this.cancelDialogId = 0;
            SKApp.server.api('dialog/get',
                {
                    'dialogId':dialogId,
                    'timeString':SKApp.user.simulation.getGameMinutes()
                }, function (data) {
                    if(data.result===1){

                        var win = (me.window && me.window.is_opened) ? me.window : me.talk_window;

                        win.setLastDialog(dialogId);
                        if (data.events && data.events[0].data && data.events[0].data.length && data.events[0].data[0].step_number === '1' &&
                            data.events[0].data[0].dialog_subtype === '2') {
                            win.switchDialog(data.events[0].data[0].id);
                        }
                        SKApp.user.simulation.parseNewEvents(data.events);

                        if(flag===1){
                            me.drawMenu();
                        }
                    }
                });
        },
        dialogDisplay:function (dialog) {

        },
        update:function () {
            if (this.answersShowFlag == 1) {
                return;
            }
            /*var curTime = timer.getCurUnixtime();
             if(this.timeToShow < curTime){
             $('#phoneAnswers').show();
             this.answersShowFlag = 1;
             }*/

            if (!sounds.isset || session.getSimulationType() == 'dev') {
                $('#phoneAnswers').show();
                this.answersShowFlag = 1;
            }
        }
    });
});
