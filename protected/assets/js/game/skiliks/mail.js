    (function () {
    "use strict";
    window.mailEmulator = {
        issetDiv:false,
        divTop:50,
        divLeft:50,
        divRight:50,

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
        }
    };
})();
