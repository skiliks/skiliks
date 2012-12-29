    (function () {
    "use strict";
    window.mailEmulator = {
        status:0,
        activeSubScreen:'',
        issetDiv:false,
        divTop:50,
        divLeft:50,
        divRight:50,

        curFolderID:1,
        curFolderType:'inbox',
        curMesage:0,
        curMesageFull:0,
        curMesageToSelect:0,
        curFile:0,

        messageArriveSound:1,
        receivers:{},
        themes:{},
        phrases:{},
        phrasesAdd:{},
        files:{},

        dropDownWidth: 400,
        prewMessageWrapperPrefix: '<pre><p style="color:blue;"> >>> ',
        prewMessageWrapperSuffix: '</p></pre>',

        selectedReceivers:'',
        selectedReceiversCopy:'',
        selectedTheme:0,
        selectedPhrases:'',
        fileSelected:0,

        newMessageOver:0,

        remember:'',

        letterType:'',

        selectedTask:0,

        orderArr:{},

        lastMailText:'',

        divZindex:0,

        mode:'normal',

        Receiver:"",

        Theme:"",

        setDivTop:function (val) {
            this.divTop = val;
        },

        setDivLeft:function (val) {
            this.divLeft = val;
        },
        setDivRight:function (val) {
            this.divRight = val;
        },

        createDiv:function () {
            var topZindex = php.getTopZindexOf();
            this.divZindex = topZindex;

            var div = document.createElement('div');
            div.setAttribute('id', 'mailEmulatorMainDiv');
            div.setAttribute('class', 'mail-emulator-main-div');
            div.style.position = "absolute";
            div.style.zIndex = this.divZindex + 0;
            document.body.appendChild(div);
            $('#mailEmulatorMainDiv').css('top', this.divTop + 'px');
            $('#mailEmulatorMainDiv').css('left', this.divLeft + 'px');
            $('#mailEmulatorMainDiv').css('right', this.divRight + 'px');

            //close
            div = document.createElement('div');
            div.setAttribute('id', 'mailEmulatorMainDivClose');
            div.setAttribute('class', 'mailEmulatorMainDivClose');
            div.style.position = "absolute";
            div.style.zIndex = this.divZindex + 0;
            document.body.appendChild(div);
            $('#mailEmulatorMainDivClose').css('top', (this.divTop - 15) + 'px');
            $('#mailEmulatorMainDivClose').css('right', (this.divLeft - 15) + 'px');

            this.issetDiv = true;
        },

        draw:function (mode) {
            if (typeof(mode) === 'undefined') {
                mode = 'normal';
            }
            this.mode = mode;

            sender.mailGetInboxUnreadedCount();
            if (this.status === 0) {
                sender.mailGetFolders();

                this.status = 1;
                window.allowScroll = false;

            } else {
                if (typeof($('#mailEmulatorNewLetterReceiverBox').get(0)) !== 'undefined') {
                    this.askForSaveDraftLetter();
                }

                $('#mailEmulatorMainDiv').remove();
                $('#mailEmulatorMainDivClose').remove();
                this.issetDiv = false;
                this.status = 0;
                allowScroll = true;

                //логируем событие
                if (this.mail_new_window && this.mail_new_window.is_opened) {
                    this.mail_new_window.switchMessage(this.curMesage);
                }
                this.mail_main_window.close();
                this.activeSubScreen = '';
                simulation.subwindowActive = "mainScreen";
            }

        },

        receive:function (data) {
            this.drawInterface(data);
        },

        drawInterface:function (data) {
            if (!this.issetDiv) {
                this.createDiv();
            }

            var html = this.html;

            var mailEmulatorFolders = '';
            var mailEmulatorFolders1 = '';
            var i = 0;
            for (var key in data["folders"]) {
                var value = data["folders"][key];

                var counter = '';
                if (value['unreaded'] != 0) {
                    counter = '(' + value['unreaded'] + ')';
                }

                mailEmulatorFolders1 += '<div id="mailFolder_' + value.id + '" class="mail-folder-inside" onclick="mailEmulator.folderSelect(\'' + value.id + '\')">' +
                    '<div id="mailFolderName_' + value.id + '" class="mail-folder-inside-name">' + value.name + '</div>' +
                    '<div id="mailFolderCounter_' + value.id + '" class="mail-folder-inside-counter">(' + counter + ')</div>' +
                    '</div>';
                mailEmulatorFolders += '<li id="mailFolder_' + value.id + '" onclick="mailEmulator.folderSelect(\'' + value.id + '\')">' +
                    '<a href="#" class="btn' + i + '">' + value.name +
                    ' <span id="mailFolderCounter_' + value.id + '">' + counter + '</span></a>' +
                    '</li>';
                i++;
            }

            html = php.str_replace('{folders}', mailEmulatorFolders, html);

            $('#mailEmulatorMainDiv').html(html);

            this.drawContent();
            this.folderSelect('1', data["messages"]);
            sender.mailGetReceivers();
        },
        receiveFolder:function (data) {
            for (var key in data["folders"]) {
                var value = data["folders"][key];

                var counter = '';
                if (value['unreaded'] != 0) {
                    counter = '(' + value['unreaded'] + ')';
                }
                $('#mailFolderCounter_' + value['folderId']).html(counter);
            }
        },
        receiveMessages:function (data) {
            var messages = data['messages'];
            this.curFolderType = data['type'];

            $('#mailEmulatorNewLetterSendDraft').hide();

            if (this.curFolderType != 'inbox' && this.curFolderType != 'trash' ) {
                this.curFolderType = data['type'];
                var colV = 'Кому';
                $('#mailEmulatorReceivedListSortSender').html(colV);

                var colV = 'Дата отправки';
                $('#mailEmulatorReceivedListSortTime').html(colV);

                $('#mailEmulatorOpenedMailAnswer').hide();
                $('#mailEmulatorOpenedMailAnswerAll').hide();
                $('#mailEmulatorOpenedMailForward').hide();
                $('#mailEmulatorOpenedMailToPlan').hide();

                if (this.curFolderType === 'drafts') {
                    $('#mailEmulatorNewLetterSendDraft').show();

                    var colV = 'Дата сохранения';
                }
                $('#mailEmulatorReceivedListSortTime').html(colV);
            }
            
            if (php.count(messages) === 0 && this.curFolderType == 'trash') {
                $('#mailEmulatorOpenedMailAnswer').hide();
                $('#mailEmulatorOpenedMailAnswerAll').hide();
                $('#mailEmulatorOpenedMailForward').hide();
                $('#mailEmulatorOpenedMailToPlan').hide();
                return;
            }

            if (php.count(messages) === 0) {
                return;
            }

            $('#mailEmulatorReceivedListTable').html('');
            var curHTML = '';

            var messageSelected = 0;

            for (var key in messages) {
                var value = messages[key];

                if (messageSelected == 0) {
                    messageSelected = value['id'];
                }

                //обрезка длины
                if (value['subject'].length > 35) {
                    value['subject'] = value['subject'].slice(0, 35) + '..';
                }

                var addClass = '';
                //прочитано ли
                if (value['readed'] == 0) {
                    addClass = ' mail-emulator-received-list-string-new';
                }

                if (this.curFolderType != 'inbox' && this.curFolderType != 'trash') {
                    value['sender'] = value['receiver'];
                    value['receivingDate'] = value['sendingDate'];
                }

                var attImg = '';
                if (value['attachments'] == 1) {
                    attImg = '<img src="'+SKConfig.assetsUrl+'/static/img/mail/icon-attach.png" style="width:12px; height:18px;" id="mailEmulatorReceivedListCellAttachImg_' + value['id'] + '">';
                }

                curHTML += '<tr class="active mail-emulator-received-list-string' + addClass + '" id="mailEmulatorReceivedListString_' + value['id'] + '">';
                curHTML += '<td  class="col0 mail-emulator-received-list-cell-sender" id="mailEmulatorReceivedListCellSender_' + value['id'] + '">' + value['sender'] + '</td>';
                curHTML += '<td  class="col1 mail-emulator-received-list-cell-theme" id="mailEmulatorReceivedListCellTheme_' + value['id'] + '">' + value['subject'] + '</td>';
                curHTML += '<td  class="col2 mail-emulator-received-list-cell-time" id="mailEmulatorReceivedListCellTime_' + value['id'] + '">' + value['receivingDate'] + '</td>';
                curHTML += '<td  class="col3 mail-emulator-received-list-cell-attach" id="mailEmulatorReceivedListCellAttach_' + value['id'] + '">&nbsp;' + attImg + '</td>';
                curHTML += '</tr>';
            }

            $('#mailEmulatorReceivedListTable').html(curHTML);
            if (this.curMesageToSelect != 0) {
                //а вдруг нам надо подсветить определенное мыло?
                mailEmulator.messageSelect(this.curMesageToSelect);
                this.curMesageToSelect = 0;
            } else if (messageSelected != 0) {
                mailEmulator.messageSelect(messageSelected);
            }
            /*else{
             this.curMesage = 0;
             mailEmulator.drawReceived();
             }*/


            //КЛИК/даблклик
            $('.mail-emulator-received-list-cell-sender, .mail-emulator-received-list-cell-theme, .mail-emulator-received-list-cell-time, .mail-emulator-received-list-cell-attach')
                .single_double_click(mailEmulator.mailElementClick, mailEmulator.mailElementDblClick, 300);

            $('.mail-emulator-received-list-string-new').mouseover(function (i) {
                var callerID = i.target.id;
                var callerIDArr = callerID.split('_');
                mailEmulator.newMessageOver = callerIDArr[1];
                setTimeout(function () {
                    mailEmulator.checkOnMouseOver(callerIDArr[1])
                }, 2000);
            });
            $('.mail-emulator-received-list-string-new').mouseout(function (i) {
                mailEmulator.newMessageOver = 0;
            });

            //перемещение дрангдропом
            $('.mail-emulator-received-list-string').each(function (i) {
                $(this).draggable({
                    cursorAt:{left:20, top:20},
                    appendTo:'body',
                    helper:'clone',
                    zIndex:300,
                    start:function (e, ui) {
                        mailEmulator.remember = $(this).html();

                        $(ui.helper).html('<div style="width:42px; height:42px;"><img src="'+SKConfig.assetsUrl+'/static/img/mail/e-mail.png"></div>');
                        $(ui.helper).css('background', 'transparent');
                    },
                    stop:function (e, ui) {
                        $(this).html(mailEmulator.remember);
                    }
                });
            });
            $('#mailFolderInside li').each(function (i) {
                $(this).droppable({
                    drop:function (event, ui) {
                        //отправка на сервер инфы о перемещении, перезагрузка папки
                        var folderId = $(this).attr('id').split('_')[1];
                        var messageId = ui.draggable.attr('id').split('_')[1];
                        mailEmulator.messageTransfer(folderId, messageId);
                    }
                });
            });

        },

        mailElementClick:function (i) {

            var callerID = i.target.id;
            var callerIDarr = callerID.split('_');
            var messageID = callerIDarr[1];
            mailEmulator.messageSelect(messageID);
        },
        mailElementDblClick:function (i) {
            var callerID = i.target.id;
            var callerIDarr = callerID.split('_');
            var messageID = callerIDarr[1];
            mailEmulator.messageMarkRead(messageID);
            sender.mailGetMessageFull(messageID);
            mailEmulator.curMesageFull = messageID;
        },

        checkOnMouseOver:function (id) {
            if (id == mailEmulator.newMessageOver) {
                mailEmulator.messageMarkRead(id);
            }
        },
        receiveMessage:function (data) {
            mailEmulator.drawMail(data);
        },
        receiveMessageFull:function (data) {
            mailEmulator.drawMailFull(data);
        },
        receiveReceivers:function (data) {
            this.receivers = data;
        },
        receiveThemes:function (data, characterThemeId) {
            this.themes = data;
            
            if ('undefined' != typeof characterThemeId) {
                $('#mailEmulatorNewLetterThemeBox')
                    .attr('data-character-subject-id', characterThemeId);
            }
            
            if (this.letterType === 'forward') {
                this.getAvalPhrases();
            }
        },
        drawMessageEdit:function (data) {
            this.phrases = data.data;
            var newLetterDiv = $('#mailEmulatorNewLetterDiv');
            if (data.message) {
                this.letterBlock = new SKMailLetterFixedTextView({ el:newLetterDiv, message:data.message});
                newLetterDiv.addClass('full');
                $('.mail-tags-bl').hide();
            } else {
                this.letterBlock = new SKMailLetterPhraseListView({
                    el:newLetterDiv

                });
                newLetterDiv.removeClass('full');
                $('.mail-tags-bl').show();
                var htmlT = '';
                for (var key in this.phrases) {
                    var value = this.phrases[key];
                    htmlT += '<li class="mailEmulatorNewLetterTextVariant mailEmulatorPhrase_' + key + '"><a href="#"><span>' + value + '</span></a></li>';
                }

                $('#mailEmulatorNewLetterTextVariants').html(htmlT);
                //add
                this.phrasesAdd = data['addData'];

                htmlT = '';
                for (var key in this.phrasesAdd) {
                    var value = this.phrasesAdd[key];
                    htmlT += '<li class="mailEmulatorNewLetterTextVariant mailEmulatorPhrase_' + key + '"><a href="#"><span>' + value + '</span></a></li>';
                }

                $('#mailEmulatorNewLetterTextVariantsAdd').html(htmlT);
                //add end
                $(function () {
                    $("#mailEmulatorNewLetterText").sortable({
                        revert:true,
                        stop:function (event, ui) {
                            mailEmulator.drawNewLetterStopSorting();
                        }
                    });
                    $(".mailEmulatorNewLetterTextVariant").draggable({
                        connectToSortable:"#mailEmulatorNewLetterText",
                        helper:"clone",
                        revert:"invalid",
                        stop:function (event, ui) {
                            mailEmulator.drawNewLetterStopDragging();
                        }
                    });
                    $("ul, li").disableSelection();
                });
            }

            if (data.previouseMessage) {
                $('#mailEmulatorNewLetterDiv')
                    .append(this.prewMessageWrapperPrefix + data.previouseMessage + this.prewMessageWrapperSuffix);
            }
        }, receivePhrases:function (data) {
            this.drawMessageEdit(data);
            if (this.hasOwnProperty('lastMailText')) {
                this.letterBlock.setQuote(this.lastMailText);
            }

        },
        messageTransfer:function (folderId, messageId) {
            if (folderId == 0) {
                return;
            }
            sender.mailMessageTransfer(folderId, messageId);
        },
        messageMarkRead:function (id) {
            //убираем непрочтенность
            $('#mailEmulatorReceivedListString_' + id).removeClass('mail-emulator-received-list-string-new');
            sender.mailMarkRead(id);
        },
        messageSelect:function (id) {
            this.curMesage = id;
            $('.mail-emulator-received-list-string').removeClass('mail-emulator-received-list-string-selected');
            $('.mail-emulator-received-list-string').removeClass('active');
            $('#mailEmulatorReceivedListString_' + id).addClass('mail-emulator-received-list-string-selected');
            $('#mailEmulatorReceivedListString_' + id).addClass('active');
            sender.mailGetMessage(id);
        },
        messageDelete:function () {
            if (this.issetDiv == false) {
                return;
            }
            if (this.curFolderType != 'inbox' && this.curFolderType != 'trash') {
                return;
            }
            var selected = $('.mail-emulator-received-list-string-selected');
            if (typeof(selected.get(0)) == 'undefined') {
                return;
            }
            var curID = selected.get(0).id;
            var curIDarr = curID.split('_');
            var id = curIDarr[1];
            sender.mailMessageDelete(id);
        },
        folderSelect:function (callerID, messages) {

            
            $('.mail-emulator-opened-mail-letter').html('');
            $('section.mail>header nav ul li.active').removeClass('active');
            $('#mailFolder_' + callerID).addClass('active');

            if (callerID == 0) {
                mailEmulator.drawSettings();
                return;
            }

            this.curFolderID = callerID;

            this.drawReceived();

            if (typeof(messages) != 'undefined') {
                this.receiveMessages({"messages":messages, "type":"inbox"});
                return;
            }
            this.folderUpdate();
        },
        folderUpdate:function () {
            sender.mailGetMessages(this.curFolderID, -1, 0);
        },
        folderSort:function (type) {
            /*var order_type = 0;
             var noImg = 'img/mail/sortno.png';
             var newImg = 'img/mail/sortdown.png';
             var curSrc = $('.mail-emulator-received-list-sort-'+type+' img').attr('src');
             if(curSrc.indexOf('down') != -1)
             {
             newImg = 'img/mail/sortup.png';
             order_type = 1;
             }

             $('.mail-emulator-received-list-sort-subject img').attr('src', noImg);
             $('.mail-emulator-received-list-sort-sender img').attr('src', noImg);
             $('.mail-emulator-received-list-sort-time img').attr('src', noImg);

             $('.mail-emulator-received-list-sort-'+type+' img').attr('src', newImg);
             */
            var order_type = 0;
            if (typeof(this.orderArr[type]) != 'undefined' && this.orderArr[type] == 0) {
                order_type = 1;
            }
            this.orderArr = {};
            this.orderArr[type] = order_type;

            if (type == 'sender' && this.curFolderType != 'inbox' && this.curFolderType != 'trash') {
                type = 'receiver';
            }
            sender.mailGetMessages(this.curFolderID, type, order_type);
        },
        backAction:function (params) {
            //возвращаем нормальный режим
            this.mode = 'normal';
            if (undefined !== params && params['mailId']) {
                this.mail_new_window.setMessage(params['mailId'])
            }
            
            if ('undefined' != typeof params && 'undefined' != typeof params.after_send_draft) {
                // nothing 
                // send draft isn`t mean close window
            } else if ('undefined' != typeof params && 'undefined' != typeof params.isMailTransfer) {
                // move email-to trash isn`t mean close window
                // nothing
            } else {
                this.mail_new_window.close();
            }
            $('#mailEmulatorMainDivNew').remove();
            $('#mailEmulatorMainDiv').show();
            mailEmulator.drawReceived();
            mailEmulator.folderUpdate();
        },
        drawContent:function () {
            this.drawReceived();
        },

        drawReceived:function () {
            var contentReceived = this.receivedMail;
            $('#mailEmulatorContentDiv').html(contentReceived);
            $('#dayReceivedListDivScroll').mCustomScrollbar({'advanced':{'updateOnContentResize':true}});
            this.addReceivedListScroll();
        },

        drawMail:function (data) {
            //логируем то что мы показываем письмо
            if (this.mode == 'normal') {
                if (this.activeSubScreen != 'mailMain') {
                    var closingMailId = this.curMesage;
                    if (this.curMesageFull != 0) {
                        closingMailId = this.curMesageFull;
                    }

                    this.activeSubScreen = 'mailMain';
                    this.mail_main_window = new SKMailWindow('mailMain', this.curMesage);
                    this.mail_main_window.open()
                } else {
                    this.mail_main_window.switchMessage(this.curMesage);
                }
            }
            this.curMesageFull = 0;

            var contentOpened = this.openedMail;

            if (this.curFolderType != 'inbox' && this.curFolderType != 'trash') {
                data['receivingDate'] = data['sendingDate'];
            }

            var attachments = '';
            var saveButton = '';
            if (typeof(data['attachments']) != 'undefined' && data['attachments']) {
                attachments = '<p class="mail-attach">' + data['attachments']['name'] + ' <img alt="" src="'+SKConfig.assetsUrl+'/img/mail/icon-attach.png"></p>';
            }

            if (data['reply'] == undefined) {
                data['reply'] = '';
            } else {
                data['reply'] = this.prewMessageWrapperPrefix + data['reply'] + this.prewMessageWrapperSuffix;
            }

            contentOpened = php.str_replace('{sender}', data['sender'], contentOpened);
            contentOpened = php.str_replace('{subject}', data['subject'], contentOpened);
            contentOpened = php.str_replace('{time}', data['receivingDate'], contentOpened);
            contentOpened = php.str_replace('{receiver}', data['receiver'], contentOpened);
            contentOpened = php.str_replace('{copy}', data['copies'], contentOpened);
            contentOpened = php.str_replace('{message}', data['message'].replace(/\n/g, '<br />'), contentOpened);
            contentOpened = php.str_replace('{reply}', data['reply'], contentOpened);
            contentOpened = php.str_replace('{attach}', attachments, contentOpened);


            $('.mail-emulator-opened-mail-letter').html(contentOpened);
            $('.mail-emulator-opened-mail-letter-text').click(function () {
                mailEmulator.messageMarkRead(data['id']);
            });

            this.addOpenedMailScroll();
            $('.mail-view-body').mCustomScrollbar({'advanced':{'updateOnContentResize':true}, 'autoDraggerLength':true});
            this.lastMailText = data['message'];
        },

        drawMailFull:function (data) {
            //логируем режим
            if (this.activeSubScreen != 'mailPreview') {
                this.activeSubScreen = 'mailPreview';
                this.curMesage = this.curMesageFull;
                this.preview_window = new SKMailWindow('mailPreview', this.curMesage);
                this.preview_window.open()
            }

            $('#mlTitle').hide();
            $('#mailEmulatorContentContainer').css('margin-top', '30px');

            var contentOpened = this.openedMailFull;

            if (this.curFolderType != 'inbox' && this.curFolderType != 'trash') {
                data['receivingDate'] = data['sendingDate'];
            }

            var attachments = '';
            var saveButton = '';

            if (typeof(data['attachments']) != 'undefined' && data['attachments']) {
                if (this.curFolderType == 'inbox' && this.curFolderType != 'trash') {
                    saveButton = '<img src="'+SKConfig.assetsUrl+'/img/interface/savedoc.png"  class="mail-emulator-save-button" onclick="mailEmulator.saveDocument(' + data['attachments']['id'] + ')">'
                }
                attachments = data['attachments']['name'] + saveButton;
            }

            if (data['reply'] == undefined) {
                data['reply'] = '';
            } else {
                data['reply'] = this.prewMessageWrapperPrefix + data['reply'] + this.prewMessageWrapperSuffix;
            }

            contentOpened = php.str_replace('{sender}', data['sender'], contentOpened);
            contentOpened = php.str_replace('{subject}', data['subject'], contentOpened);
            contentOpened = php.str_replace('{time}', data['receivingDate'], contentOpened);
            contentOpened = php.str_replace('{receiver}', data['receiver'], contentOpened);
            contentOpened = php.str_replace('{copy}', data['copies'], contentOpened);
            contentOpened = php.str_replace('{message}', data['message'], contentOpened);
            contentOpened = php.str_replace('{reply}', data['reply'], contentOpened);
            contentOpened = php.str_replace('{attach}', attachments, contentOpened);

            $('#mailEmulatorContentContainer').html(contentOpened);

            $('#mailEmulatorNewLetterSendDraft').hide();
            
            console.log('this.curFolderType: ', this.curFolderType);
            if (this.curFolderType != 'inbox' && this.curFolderType != 'trash') {
                $('#mailEmulatorOpenedMailAnswer').hide();
                $('#mailEmulatorOpenedMailAnswerAll').hide();
                $('#mailEmulatorOpenedMailForward').hide();
                $('#mailEmulatorOpenedMailToPlan').hide();

                if (this.curFolderType == 'drafts') {
                    $('#mailEmulatorNewLetterSendDraft').show();
                }
            }

            this.addOpenedMailFullScroll();

            this.lastMailText = data['message'];
        },

        sendNewLetter:function () {

            //забиваем копию
            var val = $('#mailEmulatorNewLetterCopyBox').val();
            var valArr = val.split(',');

            if (0 == val || '' == val) {
                val = $('#mailEmulatorNewLetterReceiverBox').attr('data-character-subject-id');
            }           
            
            var reqString = '';


            for (var keyV in valArr) {
                var value = valArr[keyV];
                for (var keyR in this.receivers) {
                    var receiver = this.receivers[keyR];
                    if (value == receiver) {
                        reqString += keyR + ',';
                    }
                }
            }

            // add subject for new email
            if (0 == this.selectedTheme) {
                this.selectedTheme = $('#mailEmulatorNewLetterThemeBox').val();
            }
            
            if (0 == this.selectedTheme || 'undefined' ==  typeof this.selectedTheme) {                
                this.selectedTheme = $('#mailEmulatorNewLetterThemeBox').attr('data-subject-id');
            } 
            
            if (0 == this.selectedTheme || 'undefined' ==  typeof this.selectedTheme) {
                this.selectedTheme = $('#mailEmulatorNewLetterThemeBox').attr('data-character-subject-id');
            } 

            this.selectedReceiversCopy = reqString;

            var timeString = timer.getCurTimeFormatted('timeStamp');
            var result = sender.mailSendMessage(this.curMesage, this.selectedReceivers, this.selectedReceiversCopy, this.selectedTheme, this.selectedPhrases, this.letterType, this.curFile, timeString);
        },

        saveDraftLetter:function () {
            //забиваем копию
            var val = $('#mailEmulatorNewLetterCopyBox').val();
            var valArr = val.split(',');
            var reqString = '';

            for (var keyV in valArr) {
                var value = valArr[keyV];
                for (var keyR in this.receivers) {
                    var receiver = this.receivers[keyR];
                    if (value == receiver) {
                        reqString += keyR + ',';
                    }
                }
            }

            this.selectedReceiversCopy = reqString;

            //убираем нулевую, фиктивную фразу
            var timeString = timer.getCurTimeFormatted('timeStamp');

            // add subject for new email
            if (0 == this.selectedTheme) {
                this.selectedTheme = $('#mailEmulatorNewLetterThemeBox').val();
            }
            
            if (0 == this.selectedTheme || 'undefined' ==  typeof this.selectedTheme) {                
                this.selectedTheme = $('#mailEmulatorNewLetterThemeBox').attr('data-subject-id');
            } 
            
            if (0 == this.selectedTheme || 'undefined' ==  typeof this.selectedTheme) {
                this.selectedTheme = $('#mailEmulatorNewLetterThemeBox').attr('data-character-subject-id');
            }

            sender.mailSaveDraft(this.curMesage, this.selectedReceivers, this.selectedReceiversCopy, this.selectedTheme, this.selectedPhrases, this.letterType, this.curFile, timeString);
            this.backAction();
        },

        drawNewLetter:function () {
            //логируем то что мы пишем новое письмо
            if (this.activeSubScreen != 'mailNew') {
                simulation.window_set.closeAll('mailEmulator');
                this.activeSubScreen = 'mailNew';

                this.mail_new_window = new SKMailWindow('mailNew');
                this.mail_new_window.open();
            }

            if (!this.issetDiv) {
                this.createDiv();
            }

            $('#mailEmulatorMainDiv').hide();
            this.letterType = 'new';

            this.selectedReceivers = '';
            this.selectedReceiversCopy = '';
            this.selectedTheme = 0;
            this.selectedPhrases = '';
            this.curFile = 0;
            delete this.lastMailText;

            var contentNew = this.newLetter;

            var div = document.createElement('div');
            div.setAttribute('id', 'mailEmulatorMainDivNew');
            div.style.position = "absolute";
            div.style.zIndex = this.divZindex + 1;
            document.body.appendChild(div);
            $('#mailEmulatorMainDivNew').css('top', this.divTop + 'px');
            $('#mailEmulatorMainDivNew').css('left', this.divLeft + 'px');
            $('#mailEmulatorMainDivNew').css('right', this.divRight + 'px');
            $('#mailEmulatorMainDivNew').html(contentNew);
            /*$('#mailEmulatorContentDiv').html(contentNew);

             /*$('.mail-emulator-new-letter-box').css('font-size', '13px');
             $('.mail-emulator-new-letter-box').css('height', '15px');
             $('.mail-emulator-new-letter-box').css('margin-bottom', '5px');
             $('.mail-emulator-new-letter-box').css('padding-left', '5px');
             $('.mail-emulator-new-letter-box').css('width', '420px');

             $('#mailEmulatorNewLetterText').parent().css('width', '450px');
             $('#mailEmulatorNewLetterText').css('height', '125px');
             $('#mailEmulatorNewLetterText').css('width', '450px');

             $('#mailEmulatorNewLetterTextVariants').parent().css('width', '450px');
             $('#mailEmulatorNewLetterTextVariants').css('height', '50px');
             $('#mailEmulatorNewLetterTextVariants').css('width', '450px');*/


            this.drawNewLetterAddTextVariants();
            this.addNewLetterScroll();
        },
        drawNewLetterCheckType:function () {
            if (this.letterType != 'new') {
                $("#mailEmulatorNewLetterThemeBox").prop('disabled', true);

                //Добавление исходного текста
                var lastMailText = this.lastMailText;
                if (typeof(lastMailText) === 'undefined') {
                    lastMailText = '';
                }
                lastMailText = php.str_replace('\n', '<br>>', lastMailText);
                var fStrLen = 120;

                //форматирование, перенос по строкам
                var strings = Math.ceil((lastMailText.length) / 120);

                var lastMailTextFinal = '';
                for (var i = 0; i < strings; i++) {
                    if (i == 0) {
                        lastMailTextFinal += '>' + lastMailText.substr((i * fStrLen), fStrLen);
                    } else {
                        var lastMailTextTemp = lastMailText.substr((i * fStrLen), fStrLen);
                        var spaceIndex = lastMailTextTemp.indexOf(' ');
                        var brIndex = lastMailTextTemp.indexOf('<br>');
                        var separator = '<br>>';
                        if (brIndex != -1 && brIndex <= fStrLen / 2) {
                            separator = '';
                        }

                        lastMailTextFinal += lastMailText.substr((i * fStrLen), spaceIndex);
                        lastMailTextFinal += separator + lastMailText.substr((i * fStrLen) + spaceIndex, fStrLen - spaceIndex);
                    }
                }

            }

            if (this.letterType == 'reply' || this.letterType == 'replyAll') {
                $("#mailEmulatorNewLetterReceiverBox").prop('disabled', true);
            }
        },

        messageReply:function () {
            var messageId = this.curMesage;
            sender.mailMessageReply(messageId);
        },
        messageReplyAll:function () {
            var messageId = this.curMesage;
            sender.mailMessageReplyAll(messageId);
        },
        messageForward:function () {
            var messageId = this.curMesage;
            sender.mailMessageForward(messageId);
        },
        messageToPlan:function () {
            var messageId = this.curMesage;
            sender.mailMessageToPlan(messageId);
        },
        sendDraftLetter:function () {
            var messageId = this.curMesage;
            sender.mailSendDraftLetter(messageId);
        },
        preventChangeRecipient: function() {
            $('#mailEmulatorNewLetterReceiverBox').attr('disabled', true);
            $('#mailEmulatorNewLetterReceiverBox').unbind();
        },
        messageDrawReply:function (data) {
            this.drawNewLetter();

            if (typeof(data['receiver']) != 'undefined') {
                $('#mailEmulatorNewLetterReceiverBox').val(data['receiver']);
            }
            if (typeof(data['receiverId']) != 'undefined') {
                this.selectedReceivers = data['receiverId'];
            }

            if (typeof(data['copies']) != 'undefined') {
                $('#mailEmulatorNewLetterCopyBox').val(data['copies']);
            }
            if (typeof(data['copiesId']) != 'undefined') {
                this.selectedReceiversCopy = data['copiesId'];
            }

            if (typeof(data['subject']) != 'undefined') {
                $('#mailEmulatorNewLetterThemeBox').val(data['subject']);
            }
            
            if (typeof(data['subjectId']) != 'undefined') {
                this.selectedTheme = data['subjectId'];
                $('#mailEmulatorNewLetterThemeBox').attr('data-subject-id', data['subjectId']);
            }

            if (typeof(data['phrases']) != 'undefined') {
                this.receivePhrases(data['phrases']);
            }

            this.getAvalThemes();
        },

        drawNewLetterAddTextVariants:function () {
            sender.mailGetReceivers();

            var change_receiver_cb = function (event, previousText) {
                if ($('#mailEmulatorNewLetterReceiverBox').val() != "") {
                    $("#mailEmulatorNewLetterThemeBox").prop('disabled', false);
                } else {
                    $("#mailEmulatorNewLetterThemeBox").prop('disabled', true);
                }
                if (mailEmulator.letterType == 'new') {

                    localStorage['Receiver'] = '';
                    mailEmulator.changeReceiver();
                    mailEmulator.hideReceivers();
                }

                // }
            };
            $('#mailEmulatorNewLetterReceiverBox').bind('change', change_receiver_cb);

            //адресаты
            $('#mailEmulatorNewLetterReceiverBox').focus(function () {
                if ($('#mailEmulatorNewLetterReceiverBox').val() != "") {
                    $("#mailEmulatorNewLetterThemeBox").prop('disabled', false);
                } else {
                    $("#mailEmulatorNewLetterThemeBox").prop('disabled', true);
                }
                mailEmulator.showReceivers();
                $(this).data('prev-val', $(this).val());

            });
            $('#mailEmulatorNewLetterReceiverBox').keyup(function () {
                mailEmulator.showReceiversAval();
            });
            //копия
            $('#mailEmulatorNewLetterCopyBox').focus(function () {
                mailEmulator.showReceiversCopy();
            });
            $('#mailEmulatorNewLetterCopyBox').keyup(function () {
                mailEmulator.showReceiversCopyAval();
            });
            //темы
            $('#mailEmulatorNewLetterThemeBox').focus(function () {
                $('#mailEmulatorNewLetterDiv').data('prev-html', $('#mailEmulatorNewLetterDiv').html());
                if ($('#mailEmulatorNewLetterReceiverBox').val() != "") {
                    mailEmulator.showThemes();
                } else {
                    $("#mailEmulatorNewLetterThemeBox").prop('disabled', true);
                }

            });
            $('#mailEmulatorNewLetterThemeBox').keyup(function () {
                mailEmulator.showThemesAval();
            });
        },
//адресаты
        showReceivers:function () {
            var div = document.createElement('div');
            div.setAttribute('id', 'mailEmulatorShowReceiversDiv');
            div.setAttribute('class', 'mail-new-drop');
            div.style.position = "absolute";
            div.style.zIndex = this.divZindex + 2;
            document.body.appendChild(div);
            var offsets = $('#mailEmulatorNewLetterReceiverBox').offset();

            $('#mailEmulatorShowReceiversDiv').css('top', (offsets.top + 20) + 'px');
            $('#mailEmulatorShowReceiversDiv').css('left', offsets.left + 'px');
            $('#mailEmulatorShowReceiversDiv').css('width', this.dropDownWidth + 'px');

            var receivers = '<ul>';
            for (var key in this.receivers) {
                var value = this.receivers[key];
                receivers += '<li onclick="mailEmulator.addReceiver(' + key + ')">' + value + '</li>';
            }
            receivers += '</ul>';
            var Cheight = (php.count(this.receivers) * 26);
            receivers += '<div class="mail-new-drop-scroll" style="height:' + Cheight + 'px;"></div>';

            $('#mailEmulatorShowReceiversDiv').html(receivers);

            //закрывалка
            var div = document.createElement('div');
            div.setAttribute('id', 'mailEmulatorShowReceiversDivClose');
            div.style.position = "absolute";
            div.style.zIndex = this.divZindex + 1;
            document.body.appendChild(div);

            $('#mailEmulatorShowReceiversDivClose').css('top', simulation.bounds.y + 'px');
            $('#mailEmulatorShowReceiversDivClose').css('left', simulation.bounds.x + 'px');
            $('#mailEmulatorShowReceiversDivClose').css('width', simulation.bounds.width + 'px');
            $('#mailEmulatorShowReceiversDivClose').css('height', simulation.bounds.height + 'px');

            $('#mailEmulatorShowReceiversDivClose').click(function () {
                mailEmulator.hideReceivers();
            });

            var val = $('#mailEmulatorNewLetterReceiverBox').val();
            if (val == 'Щелкните чтобы выбрать адресата') {
                $('#mailEmulatorNewLetterReceiverBox').val('');
            }


            this.receiversScroll(Cheight);
        },
        showReceiversAval:function () {
            var val = $('#mailEmulatorNewLetterReceiverBox').val();
            var valArr = val.split(',');
            var needle = php.last(valArr);

            if (needle.length < 2) {
                needle = '';
            }

            var receivers = '<ul>';
            for (var key in this.receivers) {
                var value = this.receivers[key];
                if (value.indexOf(needle) != -1) {
                    receivers += '<li onclick="mailEmulator.addReceiver(' + key + ')">' + value + '</li>';
                }
            }
            receivers += '</ul>';
            $('#mailEmulatorShowReceiversDiv').html(receivers);
        },
        hideReceivers:function () {
            var me = this;
            $('#mailReceiversScrollbar').remove();
            $('#mailEmulatorShowReceiversDivClose').remove();
            $('#mailEmulatorShowReceiversDiv').remove();
            this.getAvalThemes();

        },
        addReceiver:function (id) {
            if (this.letterType == 'new') {
                this.changeReceiver();
            }

            var val = $('#mailEmulatorNewLetterReceiverBox').val();
            localStorage['Receiver'] = val;
            var valArr = val.split(',');
            var needle = php.last(valArr);

            val = php.str_replace(needle, '', val);

            if (val.indexOf(this.receivers[id]) == -1) {
                localStorage['Receivers'] = val + this.receivers[id] + ',';
                $('#mailEmulatorNewLetterReceiverBox').val(val + this.receivers[id] + ',');
            }
            mailEmulator.hideReceivers();
            if ($('#mailEmulatorNewLetterReceiverBox').val() != "") {
                $("#mailEmulatorNewLetterThemeBox").prop('disabled', false);
            } else {
                $("#mailEmulatorNewLetterThemeBox").prop('disabled', true);
            }
        },
        getAvalThemes:function () {
            var val = $('#mailEmulatorNewLetterReceiverBox').val();
            var valArr = val.split(',');

            var reqString = '';

            for (var keyV in valArr) {
                var value = valArr[keyV];
                for (var keyR in this.receivers) {
                    var receiver = this.receivers[keyR];
                    if (value == receiver) {
                        reqString += keyR + ',';
                    }
                }
            }

            if (reqString.length > 0) {
                this.selectedReceivers = reqString;
                sender.mailGetThemes(this.selectedReceivers);
            }
        },
//копия
        showReceiversCopy:function () {
            var div = document.createElement('div');
            div.setAttribute('id', 'mailEmulatorShowReceiversCopyDiv');
            div.setAttribute('class', 'mail-new-drop');
            div.style.position = "absolute";
            div.style.zIndex = this.divZindex + 2;
            document.body.appendChild(div);
            var offsets = $('#mailEmulatorNewLetterCopyBox').offset();

            $('#mailEmulatorShowReceiversCopyDiv').css('top', (offsets.top + 20) + 'px');
            $('#mailEmulatorShowReceiversCopyDiv').css('left', offsets.left + 'px');
            $('#mailEmulatorShowReceiversCopyDiv').css('width', this.dropDownWidth + 'px');

            var receivers = '<ul>';
            for (var key in this.receivers) {
                var value = this.receivers[key];
                receivers += '<li onclick="mailEmulator.addReceiverCopy(' + key + ')">' + value + '</li>';
            }
            receivers += '</ul>';
            var Cheight = (php.count(this.receivers) * 26);
            receivers += '<div class="mail-new-drop-scroll" style="height:' + Cheight + 'px;"></div>';
            $('#mailEmulatorShowReceiversCopyDiv').html(receivers);

            //закрывалка
            var div = document.createElement('div');
            div.setAttribute('id', 'mailEmulatorShowReceiversCopyDivClose');
            div.style.position = "absolute";
            div.style.zIndex = this.divZindex + 1;
            document.body.appendChild(div);

            $('#mailEmulatorShowReceiversCopyDivClose').css('top', simulation.bounds.y + 'px');
            $('#mailEmulatorShowReceiversCopyDivClose').css('left', simulation.bounds.x + 'px');
            $('#mailEmulatorShowReceiversCopyDivClose').css('width', simulation.bounds.width + 'px');
            $('#mailEmulatorShowReceiversCopyDivClose').css('height', simulation.bounds.height + 'px');

            $('#mailEmulatorShowReceiversCopyDivClose').click(function () {
                mailEmulator.hideReceiversCopy();
            });

            var val = $('#mailEmulatorNewLetterCopyBox').val();
            if (val == 'Щелкните чтобы выбрать адресата') {
                $('#mailEmulatorNewLetterCopyBox').val('');
            }

            this.receiversCopyScroll(Cheight);
        },
        showReceiversCopyAval:function () {
            var val = $('#mailEmulatorNewLetterCopyBox').val();
            var valArr = val.split(',');
            var needle = php.last(valArr);

            if (needle.length < 2) {
                needle = '';
            }

            var receivers = '<ul>';
            for (var key in this.receivers) {
                var value = this.receivers[key];
                if (value.indexOf(needle) != -1) {
                    receivers += '<li onclick="mailEmulator.addReceiverCopy(' + key + ')">' + value + '</li>';
                }
            }
            receivers += '</ul>';
            $('#mailEmulatorShowReceiversCopyDiv').html(receivers);
        },
        hideReceiversCopy:function () {
            $('#mailReceiversCopyScrollbar').remove();
            $('#mailEmulatorShowReceiversCopyDivClose').remove();
            $('#mailEmulatorShowReceiversCopyDiv').remove();
        },
        addReceiverCopy:function (id) {
            var val = $('#mailEmulatorNewLetterCopyBox').val();
            var valArr = val.split(',');
            var needle = php.last(valArr);

            val = php.str_replace(needle, '', val);

            if (val.indexOf('@') == -1) {
                val = '';
                $('#mailEmulatorNewLetterCopyBox').val(val);
            }
            if (val.indexOf(this.receivers[id]) == -1) {
                $('#mailEmulatorNewLetterCopyBox').val(val + this.receivers[id] + ',');
            }
            mailEmulator.hideReceiversCopy();
        },
//темы
        showThemes:function () {
            this.getAvalThemes();
            if (this.letterType != 'new') {
                return;
            }
            var div = document.createElement('div');
            div.setAttribute('id', 'mailEmulatorShowThemesDiv');
            div.setAttribute('class', 'mail-new-drop');
            div.style.position = "absolute";
            div.style.zIndex = this.divZindex + 2;
            document.body.appendChild(div);
            var offsets = $('#mailEmulatorNewLetterThemeBox').offset();

            $('#mailEmulatorShowThemesDiv').css('top', (offsets.top + 20) + 'px');
            $('#mailEmulatorShowThemesDiv').css('left', offsets.left + 'px');
            $('#mailEmulatorShowThemesDiv').css('width', this.dropDownWidth + 'px');

            var receivers = '<ul>';
            if ($('#mailEmulatorNewLetterReceiverBox').val() != "") {
                for (var key in this.themes) {
                    var value = this.themes[key];
                    receivers += '<li onclick="mailEmulator.addTheme(' + key + ')">' + value + '</li>';
                }
            }
            receivers += '</ul>';
            var Cheight = (php.count(this.receivers) * 26);
            receivers += '<div class="mail-new-drop-scroll" style="height:' + Cheight + 'px;"></div>';
            $('#mailEmulatorShowThemesDiv').html(receivers);

            //закрывалка
            var div = document.createElement('div');
            div.setAttribute('id', 'mailEmulatorShowThemesDivClose');
            div.style.position = "absolute";
            div.style.zIndex = this.divZindex + 1;
            document.body.appendChild(div);

            $('#mailEmulatorShowThemesDivClose').css('top', simulation.bounds.y + 'px');
            $('#mailEmulatorShowThemesDivClose').css('left', simulation.bounds.x + 'px');
            $('#mailEmulatorShowThemesDivClose').css('width', simulation.bounds.width + 'px');
            $('#mailEmulatorShowThemesDivClose').css('height', simulation.bounds.height + 'px');

            $('#mailEmulatorShowThemesDivClose').click(function () {
                mailEmulator.hideThemes();
            });

            var val = $('#mailEmulatorNewLetterThemeBox').val();
            if (val == 'Щелкните чтобы выбрать тему') {
                $('#mailEmulatorNewLetterThemeBox').val('');
            }

            this.themesScroll(Cheight);
        },
        showThemesAval:function () {
            if (this.letterType != 'new') {
                return;
            }
            var val = $('#mailEmulatorNewLetterThemeBox').val();
            var valArr = val.split(',');
            var needle = php.last(valArr);

            if (needle.length < 2) {
                needle = '';
            }

            var receivers = '<ul>';
            for (var key in this.themes) {
                var value = this.themes[key];
                if (value.indexOf(needle) != -1) {
                    receivers += '<li onclick="mailEmulator.addTheme(' + key + ')">' + value + '</li>';
                }
            }
            receivers += '</ul>';
            $('#mailEmulatorShowThemesDiv').html(receivers);
        },
        hideThemes:function () {
            $('#mailThemesScrollbar').remove();
            $('#mailEmulatorShowThemesDivClose').remove();
            $('#mailEmulatorShowThemesDiv').remove();
            this.getAvalPhrases();
        },
        addTheme:function (id) {
            var me = this;
            this.changeTheme(function () {
                var val = $('#mailEmulatorNewLetterThemeBox').val();
                var valArr = val.split(',');
                var needle = php.last(valArr);
                me.Theme = val;
                val = php.str_replace(needle, '', val);

                if (val.indexOf(me.themes[id]) == -1) {
                    $('#mailEmulatorNewLetterThemeBox').val(me.themes[id]);
                }
                mailEmulator.hideThemes();
            });
        },
        getAvalPhrases:function (callback) {

            var val = $('#mailEmulatorNewLetterThemeBox').val();
 
            var reqString = 0;

            for (var keyR in this.themes) {
                var theme = this.themes[keyR];
                if (val == theme) {
                    reqString = keyR;
                }
            }
            
            // trick for Frd: email phrase collectionss
            if (isNaN(parseInt(val)) && 0 == reqString ) {                
                reqString  = $('#mailEmulatorNewLetterThemeBox').attr('data-character-subject-id');
            }

           this.selectedTheme = reqString;

            sender.mailGetPhrases(reqString , callback);
        },
        
        //Таски
        showTasks:function (data) {
            //логируем режим
            if (this.activeSubScreen != 'mailPlan') {
                simulation.window_set.closeAll('mailEmulator');
                this.activeSubScreen = 'mailPlan';
                this.mail_plan_window = new SKMailWindow('mailPlan', this.curMesage);
                this.mail_plan_window.open();
            }

            var div = document.createElement('div');
            div.setAttribute('id', 'mailEmulatorShowTasksDiv');
            div.style.position = "absolute";
            div.style.zIndex = this.divZindex + 2;
            document.body.appendChild(div);
            var offsets = $('#mailEmulatorOpenedMailToPlan').offset();

            $('#mailEmulatorShowTasksDiv').css('top', (offsets.top + 5) + 'px');
            $('#mailEmulatorShowTasksDiv').css('left', (offsets.left - 0) + 'px');

            var receivers = '<div class="mail-plan">';
            for (var key in data) {
                var value = data[key];

                receivers += '<table id="mailPlanTask_' + value['id'] + '" class="mail-plan-item" onclick="mailEmulator.selectTask(' + value['id'] + ')">' +
                    '<tbody>' +
                    '<tr>' +
                    '<th>' + value['name'] + '</th>' +
                    '<td>' + value['duration'] + ' мин</td>' +
                    '</tr>' +
                    '</tbody>' +
                    '</table>';
            }

            receivers += '<div class="mail-plan-btn">' +
                '<a onclick="mailEmulator.addTask()">' +
                '<span>ЗАпланировать</span>' +
                '</a>' +
                '</div>';
            receivers += '</div>';
            $('#mailEmulatorShowTasksDiv').html(receivers);

            //закрывалка
            var div = document.createElement('div');
            div.setAttribute('id', 'mailEmulatorShowTasksDivClose');
            div.style.position = "absolute";
            div.style.zIndex = this.divZindex + 1;
            document.body.appendChild(div);

            $('#mailEmulatorShowTasksDivClose').css('top', simulation.bounds.y + 'px');
            $('#mailEmulatorShowTasksDivClose').css('left', simulation.bounds.x + 'px');
            $('#mailEmulatorShowTasksDivClose').css('width', simulation.bounds.width + 'px');
            $('#mailEmulatorShowTasksDivClose').css('height', simulation.bounds.height + 'px');

            $('#mailEmulatorShowTasksDivClose').click(function () {
                mailEmulator.hideTasks();
            });
        },
        hideTasks:function () {
            this.selectedTask = 0;
            $('#mailEmulatorShowTasksDivClose').remove();
            $('#mailEmulatorShowTasksDiv').remove();

            //логируем режим
            if (this.activeSubScreen != 'mailMain') {
                simulation.window_set.closeAll('mailEmulator');
                this.activeSubScreen = 'mailMain';
                this.mail_main_window = new SKMailWindow('mailMain', this.curMesage);
                this.mail_main_window.open();
            } else {
                this.mail_main_window.switchMessage(this.curMesage);
            }
        },
        selectTask:function (id) {
            this.mail_plan_window.setPlan(id);
            //id="mailPlanTask_'+value['id']+'" class="mail-plan-item"
            $('.mail-plan-item').removeClass('active');
            this.selectedTask = id;
            $('#mailPlanTask_' + id).addClass('active');
        },
        addTask:function () {
            var id = this.selectedTask;
            if (id == 0) {
                return;
            }
            sender.mailAddTask(id, this.curMesage);
            mailEmulator.hideTasks();
            this.selectedTask = 0;
            sender.dayPlanTodoGetCount();
        },
        gotoTask:function (taskId) {
            dayPlan.taskdayPlanToSelect = taskId;
            dayPlan.draw();
        },
//documents begin
        hideAttachForm:function () {
            this.fileSelected = 0;
            $('#attachFormScrollbar').remove();
            $('#mailEmulatorShowAttachDivClose').remove();
            $('#mailEmulatorShowAttachDiv').remove();
        },
        selectFile:function () {
            if (this.curFile == 0) {
                this.curFile = this.fileSelected;
            } else {
                var message = 'Можно добавить только одно вложение';
                var lang_alert_title = 'Mail';
                var lang_confirmed = 'Ок';
                messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
            }

            this.drawSelectedAttach();
            this.hideAttachForm();
        },
        unselectFile:function () {
            this.curFile = 0;
            this.drawSelectedAttach();
        },
        drawSelectedAttach:function () {
            if (this.curFile == 0) {
                $('#mailEmulatorNewLetterAttachBox').html('');
            } else {
                var html = this.files[this.curFile]['name'] + '<button onclick="mailEmulator.unselectFile()"></button>';

                $('#mailEmulatorNewLetterAttachBox').html(html);
            }
        },
        showAttachForm:function () {
            sender.mailGetDocumentsList();
        },
        drawAttachForm:function (data) {
            for (var key in data) {
                var value = data[key];
                this.files[value['id']] = data[key];
            }

            this.drawAttachFormHtml();

            this.drawFiles();
        },
        drawFiles:function () {
            var html = '';
            var num = 1;
            for (var key in this.files) {
                var value = this.files[key];
                var htmlTemp = php.str_replace('{name}', value['name'], this.fileHTML);
                htmlTemp = php.str_replace('{id}', value['id'], htmlTemp);
                htmlTemp = php.str_replace('{num}', num, htmlTemp);

                var valueArr = value['name'].split('.');
                var type = valueArr[1];
                htmlTemp = php.str_replace('{type}', type, htmlTemp);

                html += htmlTemp;
                num++;
            }

            var Cheight = (php.count(this.files) * 26);
            html += '<div class="mail-new-drop-scroll" style="height:' + Cheight + 'px;"></div>';

            $('#documentsContentDivMail').html(html);

            this.attachFormScroll(Cheight);
        },
        setActiveFile:function (id) {
            $('.documents-file-active').removeClass('documents-file-active');
            $('#documentsFile_' + id).addClass('documents-file-active');
            this.fileSelected = id;

            this.selectFile();
        },
        drawAttachFormHtml:function () {
            var div = document.createElement('div');
            div.setAttribute('id', 'mailEmulatorShowAttachDiv');
            div.setAttribute('class', 'mail-new-drop');
            div.style.position = "absolute";
            div.style.zIndex = this.divZindex + 2;
            document.body.appendChild(div);
            var offsets = $('#mailEmulatorNewLetterThemeBox').offset();

            $('#mailEmulatorShowAttachDiv').css('top', (offsets.top + 50) + 'px');
            $('#mailEmulatorShowAttachDiv').css('left', (offsets.left + 30) + 'px');
            $('#mailEmulatorShowAttachDiv').css('width', this.dropDownWidth + 'px');


            $('#mailEmulatorShowAttachDiv').html(this.filesHTML);

            //закрывалка
            var div = document.createElement('div');
            div.setAttribute('id', 'mailEmulatorShowAttachDivClose');
            div.style.position = "absolute";
            div.style.zIndex = this.divZindex + 1;
            document.body.appendChild(div);

            $('#mailEmulatorShowAttachDivClose').css('top', simulation.bounds.y + 'px');
            $('#mailEmulatorShowAttachDivClose').css('left', simulation.bounds.x + 'px');
            $('#mailEmulatorShowAttachDivClose').css('width', simulation.bounds.width + 'px');
            $('#mailEmulatorShowAttachDivClose').css('height', simulation.bounds.height + 'px');

            $('#mailEmulatorShowAttachDivClose').click(function () {
                mailEmulator.hideAttachForm();
            });
        },
//documents end
//конец
        drawNewLetterStopDragging:function () {
            $('#mailEmulatorNewLetterText li').each(function (index) {
                $(this).children("span").addClass('mailEmulatorNewLetterTextVariantPhrase');
            });
        },
        drawNewLetterStopSorting:function () {
            var phrases = '';
            $('#mailEmulatorNewLetterText li').each(function (index) {
                var className = $(this).attr('class');
                //находим нужный класс, оторый содержит ИД
                var classNameArr = className.split(' ');

                //mailEmulatorPhrase_7
                for (var key in classNameArr) {
                    var value = classNameArr[key];
                    if (value.indexOf('mailEmulatorPhrase') != -1) {
                        var valueArr = value.split('_');
                        phrases += valueArr[1] + ',';
                    }
                }
            });

            this.selectedPhrases = phrases;
        },
        drawSettings:function () {
            var contentSettings = this.settingsPage;
            $('#mailEmulatorContentDiv').html(contentSettings);

            $('#mailEmulatorSettingsSpamFilterBox').css('font-size', '13px');
            $('#mailEmulatorSettingsSpamFilterBox').css('height', '15px');
            $('#mailEmulatorSettingsSpamFilterBox').css('width', '450px');

            sender.mailGetSettings();
        },
        receiveSettings:function (data) {
            var image = 'soundfieldoff.png';
            this.messageArriveSound = data;
            if (this.messageArriveSound == 1) {
                var image = 'btn-switcher.png';
            }
            var background = 'url("/img/mail/' + image + '") no-repeat scroll 0 0 transparent';
            $('#mailButtonSwitcher').attr('background', background);
        },
        changeSound:function () {
            if (this.messageArriveSound == 1) {
                this.messageArriveSound = 0;
            } else {
                this.messageArriveSound = 1;
            }
            this.receiveSettings(this.messageArriveSound);
            sender.mailSaveSettings(this.messageArriveSound);
        },
        saveDocument:function (id) {
            sender.mailSaveDocument(id);
        },
        showSettings:function () {
            if ($('.btn-window li div.set').css('display') != 'none') {
                $('.btn-window li div.set').hide();
            } else {
                $('.btn-window li div.set').show();
            }
        },

        receivedMail:'<ul class="actions">' +
            '<li id="mailEmulatorReceivedButton"><a class="btn0" onclick="mailEmulator.drawNewLetter();">новое письмо</a></li>' +
            '<li id="mailEmulatorOpenedMailAnswer"><a class="btn1" onclick="mailEmulator.messageReply();">ответить</a></li>' +
            '<li id="mailEmulatorOpenedMailAnswerAll"><a class="btn2" onclick="mailEmulator.messageReplyAll();">ответить всем</a></li>' +
            '<li id="mailEmulatorOpenedMailForward"><a class="btn3" onclick="mailEmulator.messageForward();">переслать</a></li>' +
            '<li id="mailEmulatorOpenedMailToPlan"><a class="btn4" onclick="mailEmulator.messageToPlan();">запланировать</a></li>' +
            '<li id="mailEmulatorNewLetterSendDraft"><a onclick="mailEmulator.sendDraftLetter()" class="btn4">отправить</a></li>' +
            '</ul>' +

            '<ul class="btn-window">' +
            '<li>' +
            '<button class="btn-set" onclick="mailEmulator.showSettings()">&nbsp;</button>' +
            '<div class="set">' +
            '<table>' +
            '<tr>' +
            '<th>Звук прихода новых сообщений</th>' +
            '<td><button class="switcher" id="mailButtonSwitcher" onclick="mailEmulator.changeSound()">&nbsp;</button></td>' +
            '</tr>' +
            '</table>' +
            '</div>' +
            '</li>' +
            '<li><button class="btn-cl" onclick="mailEmulator.draw();">&nbsp;</button></li>' +
            '</ul>' +

            '<table class="ml-title" id="mlTitle">' +
            '<col class="col0" />' +
            '<col class="col1" />' +
            '<col class="col2" />' +
            '<col class="col3" />' +
            '<tr>' +
            '<td onclick="mailEmulator.folderSort(\'sender\')"><span id="mailEmulatorReceivedListSortSender">От кого</span></td>' +
            '<td onclick="mailEmulator.folderSort(\'subject\')"><span>Тема</span></td>' +
            '<td onclick="mailEmulator.folderSort(\'time\')"><span id="mailEmulatorReceivedListSortTime">Дата получения</span></td>' +
            '<td><img src="'+SKConfig.assetsUrl+'/img/mail/icon-attach.png" alt="" /></td>' +
            '</tr>' +
            '</table>' +
            '<div id="mailEmulatorContentContainer">' +
            '<div class="ml-wrap" id="dayReceivedListDivScroll">' +
            '<table class="ml" id="mailEmulatorReceivedListTable">' +
            '</table>' +
            '</div>' +

            '<div class="mail-view pre mail-emulator-opened-mail-letter">' +
//            '<div class="mail-view-header">' +
//            '<table>' +
//            '<tr>' +
//            '<th>От кого:</th>' +
//            '<td><strong>МойСклад &lt;info@moysklad.ru&gt;</strong></td>' +
//            '</tr>' +
//            '<tr>' +
//            '<th>Кому:</th>' +
//            '<td>a-scetch@mail.ru</td>' +
//            '</tr>' +
//            '<tr>' +
//            '<th>Копия:</th>' +
//            '<td>Elena Levina &lt;a.levina@gmail.com&gt;</td>' +
//            '</tr>' +
//            '<tr>' +
//            '<th>Тема:</th>' +
//            '<td>Re: Рабочие материалы</td>' +
//            '</tr>' +
//            '</table>' +
//            '</div>' +
//            '<div class="mail-view-body mail-emulator-opened-mail-letter-text">' +
//            '<p>' +
//            'Добрый день!<br /><br />' +
//            'Теперь у вас есть комплексное решение для управления торговлей и бухгалтерского учета от ' +
//            'МоегоСклада. Начните работать в Бухгалтерии онлайн. До 1 сентября 2012 года она доступна ' +
//            'в бесплатном тестовом режиме. Бухгалтерия онлайн.' +
//            '</p>' +
//            '<div class="mail-view-scroll pre"></div>' +
//            '</div>' +
//            '</div>' +
            '</div>',
        receivedMail1:'<div class="mail-emulator-received">' +
            '<div class="mail-emulator-head-buttons">' +
            '<div id="mailEmulatorReceivedButton" class="mail-emulator-buttons write-letter-button" onclick="mailEmulator.drawNewLetter();">' +
            'Написать письмо</div>' +
            '<div id="mailEmulatorOpenedMailAnswer" class="mail-emulator-buttons answer-button" onclick="mailEmulator.messageReply();">Ответить</div>' +
            '<div id="mailEmulatorOpenedMailAnswerAll" class="mail-emulator-buttons answer-all-button" onclick="mailEmulator.messageReplyAll();">Ответить всем</div>' +
            '<div id="mailEmulatorOpenedMailForward" class="mail-emulator-buttons forward-button" onclick="mailEmulator.messageForward();">Переслать</div>' +
            '<div id="mailEmulatorOpenedMailToPlan" class="mail-emulator-buttons to-plan-button" onclick="mailEmulator.messageToPlan();">В план</div>' +
            '<div id="mailEmulatorNewLetterSendDraft" class="mail-emulator-buttons send-button-head"  onclick="mailEmulator.sendDraftLetter()">Отправить</div>' +
            '</div>' +
            '<div class="mail-emulator-received-list-sort">' +
            '<div id="mailEmulatorReceivedListSortSender" class="mail-emulator-received-list-sort-sender" onclick="mailEmulator.folderSort(\'sender\')">' +
            '<img src="'+SKConfig.assetsUrl+'/img/mail/sortno.png">От кого' +
            '</div>' +
            '<div id="mailEmulatorReceivedListSortSubject" class="mail-emulator-received-list-sort-subject" onclick="mailEmulator.folderSort(\'subject\')">' +
            '<img src="'+SKConfig.assetsUrl+'/img/mail/sortno.png">Тема' +
            '</div>' +
            '<div id="mailEmulatorReceivedListSortTime" class="mail-emulator-received-list-sort-time" onclick="mailEmulator.folderSort(\'time\')">' +
            '<img src="'+SKConfig.assetsUrl+'/img/mail/sortno.png">Дата получения' +
            '</div>' +
            '<div id="mailEmulatorReceivedListSortAttach" class="mail-emulator-received-list-sort-attach">' +
            'Вложения' +
            '</div>' +
            '</div>' +
            '<div class="mail-emulator-received-list">' +
            '<table cellpadding=0 cellspacing=0 id="mailEmulatorReceivedListTable">' +
            '</table>' +
            '</div>' +
            '</div>' +

            '<div class="mail-emulator-opened-mail">' +
            '<div class="mail-emulator-opened-mail-letter">' +
            '<div class="mail-emulator-opened-mail-letter-head">' +
            '<div id="mailEmulatorOpenedMailSender">Для отображения вам нужно выбрать письмо</div>' +
            '<div id="mailEmulatorOpenedMailTheme"></div>' +
            '</div>' +
            '<div class="mail-emulator-opened-mail-letter-text">' +
            '' +
            '</div>' +
            '</div>' +
            '</div>',

        openedMail:'<div class="mail-view-header">' +
            '<table>' +
            '<tr>' +
            '<th>От кого:</th>' +
            '<td><strong>{sender}</strong></td>' +
            '</tr>' +
            '<tr>' +
            '<th>Кому:</th>' +
            '<td>{receiver}</td>' +
            '</tr>' +
            '<tr>' +
            '<th>Копия:</th>' +
            '<td>{copy}</td>' +
            '</tr>' +
            '<tr>' +
            '<th>Тема:</th>' +
            '<td>{subject}</td>' +
            '</tr>' +
            /*'<tr>'+
             '<th><img alt="" src="'+SKConfig.assetsUrl+'/img/mail/icon-attach.png"></th>'+
             '<td>{attach}</td>'+
             '</tr>'+*/
            '</table>' +
            '{attach}' +
            '</div>' +
            '<div class="mail-view-body mail-emulator-opened-mail-letter-text">' +
            '<p>' +
            '{message}' +
            '</p>' +
            '{reply}' +
            '<div class="mail-view-scroll pre"></div>' +
            '<div id="mailOpenedMailScrollbar" class ="planner-dayplan-scrollbar" style="position:absolute;top:420px;right:12px;"></div>' +
            '</div>',
        openedMail1:'<div class="mail-emulator-opened-mail-letter-head">' +
            '<div id="mailEmulatorOpenedMailSender">От кого: {sender}</div>' +
            '<div id="mailEmulatorOpenedMailTheme">Тема: {subject}</div>' +
            '<div id="mailEmulatorOpenedMailTime">Дата: {time}</div>' +
            '<div id="mailEmulatorOpenedMailReceiver">Кому: {receiver}</div>' +
            '<div id="mailEmulatorOpenedMailCopy">Копия: {copy}</div>' +
            '<div id="mailEmulatorOpenedMailAttach"><img src="'+SKConfig.assetsUrl+'/img/mail/attach.png" style="width:20px; height:20px;"> {attach}</div>' +
            '</div>' +
            '<div class="mail-emulator-opened-mail-letter-text">' +
            '{message}' +
            '</div>',
        openedMailFull:'<div class="mail-view-header">' +
            '<table>' +
            '<tr>' +
            '<th>От кого:</th>' +
            '<td><strong>{sender}</strong></td>' +
            '</tr>' +
            '<tr>' +
            '<th>Кому:</th>' +
            '<td>{receiver}</td>' +
            '</tr>' +
            '<tr>' +
            '<th>Копия:</th>' +
            '<td>{copy}</td>' +
            '</tr>' +
            '<tr>' +
            '<th>Тема:</th>' +
            '<td>{subject}</td>' +
            '</tr>' +
            '<tr>' +
            '<th><img alt="" src="'+SKConfig.assetsUrl+'/img/mail/icon-attach.png"></th>' +
            '<td>{attach}</td>' +
            '</tr>' +
            '</table>' +
            '</div>' +
            '<div class="mail-view-body">' +
            '<p>' +
            '{message}' +
            '</p>' +
            '{reply}' +
            '<div class="mail-view-scroll-full"></div>' +
            '<div id="mailOpenedMailFullScrollbar" class ="planner-dayplan-scrollbar" style="position:absolute;top:220px;right:10px;"></div>' +
            '</div>',
        openedMailFull1:'<div class="mail-emulator-opened-mail">' +
            '<div class="mail-emulator-head-buttons">' +
            '<div id="mailEmulatorOpenedMailBack" class="mail-emulator-buttons back-button" onclick="mailEmulator.backAction()">Назад</div>' +
            '<div id="mailEmulatorOpenedMailAnswer" class="mail-emulator-buttons answer-button" onclick="mailEmulator.messageReply();">Ответить</div>' +
            '<div id="mailEmulatorOpenedMailAnswerAll" class="mail-emulator-buttons answer-all-button" onclick="mailEmulator.messageReplyAll();">Ответить всем</div>' +
            '<div id="mailEmulatorOpenedMailForward" class="mail-emulator-buttons forward-button" onclick="mailEmulator.messageForward();">Переслать</div>' +
            '<div id="mailEmulatorOpenedMailToPlan" class="mail-emulator-buttons to-plan-button" onclick="mailEmulator.messageToPlan();">В план</div>' +
            '<div id="mailEmulatorNewLetterSendDraft" class="mail-emulator-buttons send-button-head"  onclick="mailEmulator.sendDraftLetter()">Отправить</div>' +
            '</div>' +
            '<div class="mail-emulator-opened-mail-letter">' +
            '<div class="mail-emulator-opened-mail-letter-head">' +
            '<div id="mailEmulatorOpenedMailSender">От кого: {sender}</div>' +
            '<div id="mailEmulatorOpenedMailTheme">Тема: {subject}</div>' +
            '<div id="mailEmulatorOpenedMailTime">Дата: {time}</div>' +
            '<div id="mailEmulatorOpenedMailReceiver">Кому: {receiver}</div>' +
            '<div id="mailEmulatorOpenedMailCopy">Копия: {copy}</div>' +
            '<div id="mailEmulatorOpenedMailAttach"><img src="'+SKConfig.assetsUrl+'/img/mail/attach.png" style="width:20px; height:20px;"> {attach}</div>' +
            '</div>' +
            '<div class="mail-emulator-opened-mail-letter-text-full">' +
            '{message}' +
            '</div>' +
            '</div>' +
            '</div>',
        newLetter:'<section class="mail new">' +

            '<header>' +
            '<ul class="actions">' +
            '<li><a onclick="mailEmulator.sendNewLetter()" class="btn4">отправить</a></li>' +
            '<li><a onclick="mailEmulator.saveDraftLetter()" class="btn4">сохранить</a></li>' +
            '</ul>' +

            '<ul class="btn-window">' +
            '<li><button class="btn-cl" onclick="mailEmulator.askForSaveDraftLetter();">&nbsp;</button></li>' +
            '</ul>' +
            '</header>' +

            '<div class="mail-view new">' +

            '<div class="mail-view-header">' +
            '<table>' +
            '<tr>' +
            '<th>Кому:</th>' +
            '<td><input type="text" id="mailEmulatorNewLetterReceiverBox"/></td>' +
            '</tr>' +
            '<tr>' +
            '<th>Копия:</th>' +
            '<td><input type="text" id="mailEmulatorNewLetterCopyBox"/></td>' +
            '</tr>' +
            '<tr>' +
            '<th>Тема:</th>' +
            '<td>' +
            '<input type="text" id="mailEmulatorNewLetterThemeBox"/>' +
            '</td>' +
            '</tr>' +
            '<tr>' +
            '<th><img src="'+SKConfig.assetsUrl+'/img/mail/btn-attach.png" alt="" onclick="mailEmulator.showAttachForm()"/></th>' +
            '<td>' +
            '<div class="mail-file-attach" id="mailEmulatorNewLetterAttachBox"></div>' +
            '</td>' +
            '</tr>' +
            '</table>' +

            '<button id="switchNewLetterViewBtn" class="mail-header-btn min" onclick="mailEmulator.switchNewLetterView()"></button>' +
            '</div>' +

            '<div class="mail-new-text" id="mailEmulatorNewLetterDiv">' +
            '<ul id="mailEmulatorNewLetterText" class="mail-new-text">' +

            '</ul>' +
            '<div class="mail-new-text-scroll"></div>' +
            '<div id="newLetterScrollbar" class ="planner-dayplan-scrollbar" style="float:left;margin-top:50px;position:absolute;top:210px;right:25px;"></div>' +
            '</div>' +

            '</div>' +

            '<div class="mail-tags-bl">' +
            '<ul class="mail-tags-signs" id="mailEmulatorNewLetterTextVariantsAdd">' +

            '</ul>' +

            '<ul class="mail-tags-words" id="mailEmulatorNewLetterTextVariants">' +

            '</ul>' +
            '</div>' +

            '</section>',
        newLetter1:'<div class="mail-emulator-new-letter">' +
            '<div class="mail-emulator-head-buttons">' +
            '<div id="mailEmulatorNewLetterReturn" class="mail-emulator-buttons back-button" onclick="mailEmulator.backAction()">Назад</div>' +
            '<div id="mailEmulatorNewLetterSend" class="mail-emulator-buttons send-button"  onclick="mailEmulator.sendNewLetter()">Отправить</div>' +
            '<div id="mailEmulatorNewLetterSendDraft" class="mail-emulator-buttons send-draft-button"  onclick="mailEmulator.saveDraftLetter()">В черновики</div>' +
            '<div id="mailEmulatorNewLetterSendDraft" class="mail-emulator-buttons show-attach-form-button"  onclick="mailEmulator.showAttachForm()">Добавить вложение</div>' +
            '</div>' +
            '<div class="mail-emulator-new-letter-main">' +
            '<div class="mail-emulator-new-letter-head">' +
            '<div class="mail-emulator-new-letter-head-labels">' +
            '<div class="mail-emulator-new-letter-label">Кому:</div>' +
            '<div class="mail-emulator-new-letter-label">Копия:</div>' +
            '<div class="mail-emulator-new-letter-label">Тема:</div>' +
            '<div class="mail-emulator-new-letter-label"><img src="'+SKConfig.assetsUrl+'/img/mail/attach.png" style="width:20px; height:20px;"></div>' +
            '</div>' +
            '<div class="mail-emulator-new-letter-head-boxes">' +
            '<input id="mailEmulatorNewLetterReceiverBox" class="span3 mail-emulator-new-letter-box"' +
            'type="text" value="Щелкните чтобы выбрать адресата"></input>' +
            '<input id="mailEmulatorNewLetterCopyBox" class="span3 mail-emulator-new-letter-box"' +
            'type="text" value="Щелкните чтобы выбрать адресата"></input>' +
            '<input id="mailEmulatorNewLetterThemeBox" class="span3 mail-emulator-new-letter-box"' +
            'type="text" value="Щелкните чтобы выбрать тему"></input>' +
            '<div id="mailEmulatorNewLetterAttachBox"></div>' +
            '</div>' +
            '</div>' +
            '<div class="mail-emulator-new-letter-text">' +
            '<div class="well"><ul id="mailEmulatorNewLetterText"></ul></div>' +
            '<div class="well"><ul id="mailEmulatorNewLetterTextVariants"></ul></div>' +
            '</div>' +
            '</div>' +
            '</div>',


        settingsPage:'<div class="mail-emulator-settings">' +
            '<div class="mail-emulator-settings-sound">' +
            '<div class="mail-emulator-settings-sound-label">' +
            'Проигрывать звук при получении сообщения' +
            '</div>' +
            '<div class="mail-emulator-settings-sound-field" onclick="mailEmulator.changeSound()">' +
            '<img src="'+SKConfig.assetsUrl+'/img/mail/soundfield.png">' +
            '</div>' +
            /*'<div class="mail-emulator-settings-sound-slider">'+
             '<img src="'+SKConfig.assetsUrl+'/img/mail/soundslider.png">'+
             '</div>'+*/
            '</div>' +
            /*'<div class="mail-emulator-settings-spam-filter">'+
             'Признаки для спам-фильтра:'+
             '<br/>'+
             '<br/>'+
             '<input id="mailEmulatorSettingsSpamFilterBox" class="span3" type="text"'+
             'value="Щелкните, чтобы добавить адреса в спам-фильтр"></input>'+
             '</div>'+*/
            '</div>',

        closeHtml:'<img src="'+SKConfig.assetsUrl+'/img/interface/close.png" onclick="mailEmulator.draw();" style="cursor:pointer;">',
        html1:'<form class="well">' +
            '<div class="mail-emulator-main-div">' +
            '<div class="mail-emulator-folders" id="mailEmulatorFolders">' +
            '{folders}' +
            '<br/>' +
            '<br/>' +
            '<p id="mailFolder_0" class="mail-folder-inside" onclick="mailEmulator.folderSelect(0)">Настройки</p>' +
            '<br/>' +
            '<br/>' +
            '<br/>' +
            '</div>' +
            '<div id="mailEmulatorContentDiv" class="mail-emulator-content-div">' +
            '</div>' +
            '</div>' +
            '</form>',
        html:'<section class="mail">' +

            '<header>' +
            '<h1>Почта</h1>' +
            '<nav>' +
            '<ul id="mailFolderInside">' +
            '{folders}' +
            '</ul>' +
            '</nav>' +
            '</header>' +
            '<div class="r" id="mailEmulatorContentDiv">' +
            '</div>' +
            '</section>',
        fileHTML1:'<div class="documents-file" id="documentsFile_{id}" onclick="mailEmulator.setActiveFile({id})"><img src="'+SKConfig.assetsUrl+'/img/documents/{type}.png"> {name}</div>',
        fileHTML:'<li id="documentsFile_{id}" onclick="mailEmulator.setActiveFile({id})"><img src="'+SKConfig.assetsUrl+'/img/documents/{type}.png" style="width:25px;heigth:25px;"> {name}</li>',
        filesHTML:'<ul id="documentsContentDivMail"></ul>',
        filesHTML1:'<div id="documentsContentDivMail" class="documents-content-div-mail">' +
            '</div>' +
            '<div id="documentsContentDivButtons" class="documents-content-div-buttons">' +
            '<input type="button" value="Выбрать" class="btn" onclick="mailEmulator.selectFile();">' +
            '<input type="button" value="Отмена" class="btn" onclick="mailEmulator.hideAttachForm();">' +
            '</div>',
        mailMessageHtml:'<div class="mail-popup">' +
            '<div class="mail-popup-tit"><img src="'+SKConfig.assetsUrl+'/img/mail/type-system-message.png" alt=""></div>' +

            '<p class="mail-popup-text">' +
            'Сохранить письмо в черновиках?' +
            '</p>' +

            '<table class="mail-popup-btn">' +
            '<tbody><tr>' +
            '<td>' +
            '<div onclick="mailEmulator.doResultForSaveDraftLetter(1);">' +
            '<div>не сохранять</div>' +
            '</div>' +
            '</td>' +
            '<td>' +
            '<div onclick="mailEmulator.doResultForSaveDraftLetter(2);">' +
            '<div>отмена</div>' +
            '</div>' +
            '</td>' +
            '<td>' +
            '<div onclick="mailEmulator.doResultForSaveDraftLetter(3);">' +
            '<div>сохранить</div>' +
            '</div>' +
            '</td>' +
            '</tr>' +
            '</tbody></table>' +
            '</div>',
        mailMessageReceiver:'<div class="mail-popup">' +
            '<div class="mail-popup-tit"><img src="'+SKConfig.assetsUrl+'/img/mail/type-system-message.png" alt=""></div>' +

            '<p class="mail-popup-text">' +
            'Так как получатель письма изменился, нам придется очистить текст письма. Вы уверены?' +
            '</p>' +

            '<table class="mail-popup-btn">' +
            '<tbody><tr>' +
            '<td>' +
            '<div onclick="mailEmulator.doResultForReceiver(1);">' +
            '<div>принять</div>' +
            '</div>' +
            '</td>' +
            '<td>' +
            '<div onclick="mailEmulator.doResultForReceiver(2);">' +
            '<div>отмена</div>' +
            '</div>' +
            '</td>' +
            '</tr>' +
            '</tbody></table>' +
            '</div>',
        mailMessageError:'<div class="mail-popup">' +
            '<div class="mail-popup-tit"><img src="'+SKConfig.assetsUrl+'/img/mail/type-system-message.png" alt=""></div>' +

            '<p class="mail-popup-text">' +
            '{$message}' +
            '</p>' +

            '<table class="mail-popup-btn">' +
            '<tbody><tr>' +
            '<td>' +
            '<div onclick="mailEmulator.messageError();">' +
            '<div>ok</div>' +
            '</div>' +
            '</td>' +
            '</tr>' +
            '</tbody></table>' +
            '</div>',

        askForSaveDraftLetter:function () {
            var message = this.mailMessageHtml;

            messages.showCustomSystemMessage(message);
        },
        doResultForSaveDraftLetter:function (action) {
            if (action == 1) {
                this.backAction({mailId:this.curMesage});
                $('#mailEmulatorNewLetterThemeBox').val('');
            } else if (action == 3) {
                this.saveDraftLetter();
                $('#mailEmulatorNewLetterThemeBox').val('');
                this.backAction({mailId:this.curMesage});
            }
            this.hideMessage();
        },
        askForReceiver:function () {
            var message = this.mailMessageReceiver;

            messages.showCustomSystemMessage(message);
            messages.disableCloseByClick();
        },
        changeReceiver:function () {
            // 0 0 0
            // 0 1 0
            // 1 0 1
            // 1 1 1
            //$('#mailEmulatorNewLetterCopyBox').val() != "" && - Копия
            if ($('#mailEmulatorNewLetterThemeBox').val() !== "") {
                this.askForReceiver();
            }
        },
        changeTheme:function (cb) {
            var me = this;
            //$('#mailEmulatorNewLetterCopyBox').val() != "" && - Копия
            if ($('#mailEmulatorNewLetterText').find('li').length) {
                var dialog = new SKDialogView({
                    'message': 'Так как тема письма изменились, нам придется очистить текст письма. Вы уверены?',
                    'buttons': [
                        {
                            'value': 'Да',
                            'onclick': cb
                        }, {
                            'value': 'Нет',
                            'onclick': function () {
                                $('#mailThemesScrollbar').remove();
                                $('#mailEmulatorShowThemesDivClose').remove();
                                $('#mailEmulatorShowThemesDiv').remove();
                            }
                        }
                    ]
                });
            } else {
                cb();
            }
        },
        doResultForReceiver:function (action) {
            var me = this;
            if (action == 1) {
                $('#mailEmulatorNewLetterThemeBox').val("");
                $('#mailEmulatorNewLetterText').html('');
                $('#mailEmulatorNewLetterTextVariants').html('');
                this.getAvalPhrases(function (data) {
                    me.drawMessageEdit(data);
                });
            } else if (action == 2) {
                $('#mailEmulatorNewLetterReceiverBox').val($('#mailEmulatorNewLetterReceiverBox').data('prev-val'));
                this.getReceirves();
            }

            this.hideMessage();
        },
        hideMessage:function () {
            messages.hideCustomSystemMessage();
        },
        switchNewLetterView:function () {
            var upK = 140;
            var className = $('#switchNewLetterViewBtn').attr('class');
            if (className == 'mail-header-btn min') {
                $('#switchNewLetterViewBtn').attr('class', 'mail-header-btn max');
                $('.mail-view-header').addClass('min');
                $('.mail-new-text').addClass('max');
                $('.mail-new-text-scroll').addClass('max');

                $('#newLetterScrollbar').css('top', '70px');
                $('#newLetterScrollbar').css('height', '165px');
            } else {
                $('#switchNewLetterViewBtn').attr('class', 'mail-header-btn min');
                $('.mail-view-header').removeClass('min');
                $('.mail-new-text').removeClass('max');
                $('.mail-new-text-scroll').removeClass('max');

                $('#newLetterScrollbar').css('top', '210px');
                $('#newLetterScrollbar').css('height', '25px');
            }
        },
        addReceivedListScroll:function () {

            $("#mailReceivedListScrollbar").slider({
                orientation:"vertical",
                min:0,
                max:825,
                value:825,
                slide:function (event, ui) {
                    mailEmulator.scrollReceivedListScroll(ui.value);
                }

            });
            $('#mailReceivedListScrollbar').css('height', (135) + 'px');
            $('#mailReceivedListScrollbar').css('width', '1px');
            $('#mailReceivedListScrollbar').css('border', '0px');
        },
        scrollReceivedListScroll:function (value) {
            var scrollValue = 825 - value;
            $("#dayReceivedListDivScroll").scrollTop(scrollValue);
        },
        addOpenedMailScroll:function () {
            $("#mailOpenedMailScrollbar").slider({
                orientation:"vertical",
                min:0,
                max:825,
                value:825,
                slide:function (event, ui) {
                    mailEmulator.scrollOpenedMailScroll(ui.value);
                }

            });
            $('#mailOpenedMailScrollbar').css('height', (90) + 'px');
            $('#mailOpenedMailScrollbar').css('width', '1px');
            $('#mailOpenedMailScrollbar').css('border', '0px');
        },
        scrollOpenedMailScroll:function (value) {
            var scrollValue = 825 - value;
            $(".mail-view-body").scrollTop(scrollValue);
        },
        addOpenedMailFullScroll:function () {
            $("#mailOpenedMailFullScrollbar").slider({
                orientation:"vertical",
                min:0,
                max:825,
                value:825,
                slide:function (event, ui) {
                    mailEmulator.scrollOpenedMailFullScroll(ui.value);
                }

            });
            $('#mailOpenedMailFullScrollbar').css('height', (290) + 'px');
            $('#mailOpenedMailFullScrollbar').css('width', '1px');
            $('#mailOpenedMailFullScrollbar').css('border', '0px');
        },
        scrollOpenedMailFullScroll:function (value) {
            var scrollValue = 825 - value;
            $(".mail-view-body").scrollTop(scrollValue);
        },
        receiversScroll:function (Cheight) {
            var topZindex = php.getTopZindexOf();

            var div = document.createElement('div');
            div.setAttribute('id', 'mailReceiversScrollbar');
            div.setAttribute('class', 'planner-dayplan-scrollbar');
            div.style.position = "absolute";
            div.style.zIndex = (topZindex + 1);
            document.body.appendChild(div);
            $('#mailReceiversScrollbar').css('top', (this.divTop + 130) + 'px');
            $('#mailReceiversScrollbar').css('left', (this.divLeft + this.dropDownWidth + 78) + 'px');
            $('#mailReceiversScrollbar').css('height', '110px');
            $('#mailReceiversScrollbar').css('width', '1px');
            $('#mailReceiversScrollbar').css('border', '0px');


            $("#mailReceiversScrollbar").slider({
                orientation:"vertical",
                min:0,
                max:Cheight,
                value:Cheight,
                slide:function (event, ui) {
                    mailEmulator.scrollreceiversScroll(ui.value, Cheight);
                }

            });
        },
        scrollreceiversScroll:function (value, Cheight) {
            var scrollValue = Cheight - value;
            $("#mailEmulatorShowReceiversDiv").scrollTop(scrollValue);
        },
        receiversCopyScroll:function (Cheight) {
            var topZindex = php.getTopZindexOf();

            var div = document.createElement('div');
            div.setAttribute('id', 'mailReceiversCopyScrollbar');
            div.setAttribute('class', 'planner-dayplan-scrollbar');
            div.style.position = "absolute";
            div.style.zIndex = (topZindex + 1);
            document.body.appendChild(div);
            $('#mailReceiversCopyScrollbar').css('top', (this.divTop + 165) + 'px');
            $('#mailReceiversCopyScrollbar').css('left', (this.divLeft + this.dropDownWidth + 78) + 'px');
            $('#mailReceiversCopyScrollbar').css('height', '110px');
            $('#mailReceiversCopyScrollbar').css('width', '1px');
            $('#mailReceiversCopyScrollbar').css('border', '0px');


            $("#mailReceiversCopyScrollbar").slider({
                orientation:"vertical",
                min:0,
                max:Cheight,
                value:Cheight,
                slide:function (event, ui) {
                    mailEmulator.scrollreceiversCopyScroll(ui.value, Cheight);
                }

            });
        },
        scrollreceiversCopyScroll:function (value, Cheight) {
            var scrollValue = Cheight - value;
            $("#mailEmulatorShowReceiversCopyDiv").scrollTop(scrollValue);
        },
        themesScroll:function (Cheight) {
            var topZindex = php.getTopZindexOf();

            var div = document.createElement('div');
            div.setAttribute('id', 'mailThemesScrollbar');
            div.setAttribute('class', 'planner-dayplan-scrollbar');
            div.style.position = "absolute";
            div.style.zIndex = (topZindex + 1);
            document.body.appendChild(div);
            $('#mailThemesScrollbar').css('top', (this.divTop + 195) + 'px');
            $('#mailThemesScrollbar').css('left', (this.divLeft + this.dropDownWidth + 84) + 'px');
            $('#mailThemesScrollbar').css('height', '110px');
            $('#mailThemesScrollbar').css('width', '1px');
            $('#mailThemesScrollbar').css('border', '0px');


            $("#mailThemesScrollbar").slider({
                orientation:"vertical",
                min:0,
                max:Cheight,
                value:Cheight,
                slide:function (event, ui) {
                    mailEmulator.scrollThemesScroll(ui.value, Cheight);
                }

            });
        },
        scrollThemesScroll:function (value, Cheight) {
            var scrollValue = Cheight - value;
            $("#mailEmulatorShowThemesDiv").scrollTop(scrollValue);
        },
        addNewLetterScroll:function () {
            $('#newLetterScrollbar').css('height', '25px');
            $('#newLetterScrollbar').css('width', '1px');
            $('#newLetterScrollbar').css('border', '0px');

            $("#newLetterScrollbar").slider({
                orientation:"vertical",
                min:0,
                max:500,
                value:500,
                slide:function (event, ui) {
                    mailEmulator.scrollNewLetterScroll(ui.value);
                }

            });
        },
        scrollNewLetterScroll:function (value) {
            var scrollValue = 500 - value;
            $(".mail-new-text").scrollTop(scrollValue);
        },
        messageErrorRender:function (message) {
            if (message == null || message == '' || message == undefined) {
                message = "Не известная ошибка!";
            }
            var mess = this.mailMessageError.replace("{$message}", message);
            messages.showCustomSystemMessage(mess);

        },
        getReceirves : function(data){
            if(data == undefined){
                data = $('#mailEmulatorNewLetterReceiverBox').val();
            }
            var res = "";
            var items = data.split(",");
            for(var item in items){
                for(var col in this.receivers){
                    if(items[item] == this.receivers[col]){
                        res+=col+',';
                    }
                }
            }
            this.selectedReceivers = res;
            
        },
        attachFormScroll:function (Cheight) {
            var topZindex = php.getTopZindexOf();

            var div = document.createElement('div');
            div.setAttribute('id', 'attachFormScrollbar');
            div.setAttribute('class', 'planner-dayplan-scrollbar');
            div.style.position = "absolute";
            div.style.zIndex = (topZindex + 1);
            document.body.appendChild(div);
            $('#attachFormScrollbar').css('top', (this.divTop + 232) + 'px');
            $('#attachFormScrollbar').css('left', (this.divLeft + this.dropDownWidth + 108) + 'px');
            $('#attachFormScrollbar').css('height', '110px');
            $('#attachFormScrollbar').css('width', '1px');
            $('#attachFormScrollbar').css('border', '0px');


            $("#attachFormScrollbar").slider({
                orientation:"vertical",
                min:0,
                max:Cheight,
                value:Cheight,
                slide:function (event, ui) {
                    mailEmulator.scrollAttachFormScroll(ui.value, Cheight);
                }

            });
        },
        scrollAttachFormScroll:function (value, Cheight) {
            var scrollValue = Cheight - value;
            $("#mailEmulatorShowAttachDiv").scrollTop(scrollValue);
        }
    };
})();

jQuery.fn.single_double_click = function (single_click_callback, double_click_callback, timeout) {
    return this.each(function () {
        var clicks = 0, self = this;
        jQuery(this).click(function (event) {
            clicks++;
            if (clicks == 1) {
                setTimeout(function () {
                    if (clicks == 1) {
                        single_click_callback.call(self, event);
                    } else {
                        double_click_callback.call(self, event);
                    }
                    clicks = 0;
                }, timeout || 300);
            }
        });
    });
};
