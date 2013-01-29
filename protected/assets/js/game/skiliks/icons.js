icons = {
    issetDiv:false,
    divTop:50,
    divLeft:50,
    divZindex:50,

    newEvents:{
        'visit':[],
        'phone':[],

        'todo':[],
        'email':[],
        'documents':[]
    },

    recalcCounter:0,
    recalcReq:10,
    setDivTop:function (val) {
        this.divTop = val;
    },
    setDivLeft:function (val) {
        this.divLeft = val;
    },
    switchDisplayMode:function (mode) {
        if (mode == 'normal') {
            $('#iconsMainDiv').css('z-index', this.divZindex);
        } else if (mode == 'dialog') {
            var zIndex = dialogController.getZindex();
            $('#iconsMainDiv').css('z-index', (zIndex - 1));
        }
    },
    createDiv:function () {
        var div = document.createElement('div');
        div.setAttribute('id', 'iconsMainDiv');
        div.setAttribute('class', 'iconsMainDiv');
        div.style.position = "absolute";
        div.style.zIndex = this.divZindex;
        document.body.appendChild(div);
        $('#iconsMainDiv').css('top', this.divTop + 'px');
        $('#iconsMainDiv').css('left', this.divLeft + 'px');
        $('#iconsMainDiv').css('right', this.divLeft + 'px');

        this.issetDiv = true;
    },
    addNewEvent:function (newEvent) {

        if (newEvent[0]['dialog_subtype'] == 1) {
            var curUnixtime = timer.getCurUnixtime();
            newEvent[0]['curUnixtime'] = curUnixtime;

            this.newEvents['phone'].push(newEvent);
        } else if (newEvent[0]['dialog_subtype'] == 5) {
            var curUnixtime = timer.getCurUnixtime();
            newEvent[0]['curUnixtime'] = curUnixtime;

            this.newEvents['visit'].push(newEvent);
        } else {
            if (newEvent[0]['dialog_subtype'] == 2) {
                dialogController.draw('close');
                phone.draw('dialog', newEvent);
                return;
            } else {
                phone.draw('close');
                dialogController.draw('dialog', newEvent);
                return;
            }
        }
    },
    update:function () {
        if (php.count(this.newEvents['phone']) > 0) {
            for (var key in this.newEvents['phone']) {
                var event = this.newEvents['phone'][key];
                var curUnixtime = timer.getCurUnixtime();

                // catch Missed phone call as Ignoder {
                if (event[0]['curUnixtime'] < (curUnixtime - 12)) {
                    phone.missedCalls++;
                    icons.setIconCounter('phone', phone.missedCalls);
                    simulation.isRecentlyIgnoredPhone = true;
                    var my = this.newEvents['phone'].shift() // => "удаляем первый элемент"

                    // if ('undefined' !== typeof event[2]) - trick :(, just to prevent JS warning
                    if ('undefined' !== typeof event[2]) {
                        sender.phoneGetSelect(event[2]['id']);
                    }
                    return;
                }
                // catch Missed phone call as Ignoder }
            }
        }

        if (php.count(this.newEvents['visit']) > 0) {
            for (var key in this.newEvents['visit']) {
                var event = this.newEvents['visit'][key];
                var curUnixtime = timer.getCurUnixtime();

                if (event[0]['curUnixtime'] < (curUnixtime - 15)) {
                    var my = this.newEvents['visit'].shift() // => "удаляем первый элемент"

                    sender.dialogsGetSelect(event[2]['id']);
                    return;
                }
            }
        }

        this.animateIcons('phone');
        this.animateIcons('visit');

        if (php.count(this.newEvents['todo']) > 0) {
            for (var key in this.newEvents['todo']) {
                var event = this.newEvents['todo'][key];
                var curUnixtime = timer.getCurUnixtime();

                if (event['curUnixtime'] < (curUnixtime - 15)) {
                    var my = this.newEvents['todo'].shift() // => "удаляем первый элемент"
                    return;
                }
            }
        }
        if (php.count(this.newEvents['email']) > 0) {
            for (var key in this.newEvents['email']) {
                var event = this.newEvents['email'][key];
                var curUnixtime = timer.getCurUnixtime();

                if (event['curUnixtime'] < (curUnixtime - 15)) {
                    var my = this.newEvents['email'].shift() // => "удаляем первый элемент"
                    return;
                }
            }
        }
        if (php.count(this.newEvents['documents']) > 0) {
            for (var key in this.newEvents['documents']) {
                var event = this.newEvents['documents'][key];
                var curUnixtime = timer.getCurUnixtime();

                if (event['curUnixtime'] < (curUnixtime - 15)) {
                    var my = this.newEvents['documents'].shift() // => "удаляем первый элемент"
                    return;
                }
            }
        }
        this.animateIcons('todo');
        this.animateIcons('email');
        this.animateIcons('documents');
    },
    animateIcons:function (icon) {
        var icon_el = $('#icons_' + icon).parent();
        if (php.count(this.newEvents[icon]) > 0) {
            if (!icon_el.hasClass('icon-active')) {
                icon_el.addClass('icon-active');
                var bounceIcon;
                bounceIcon = function () {
                    if (!icon_el.hasClass('icon-active')) {
                        return;
                    }
                    icon_el.animate({'margin-left':'-20px'}, 'fast', function () {
                        icon_el.animate({'margin-left':'20px'}, 'fast', bounceIcon)
                    });
                };
                bounceIcon();
            }
            /*
             $('#icons_'+icon).css('width',  mathematics.getRandom(50, 80)+'px');
             $('#icons_'+icon).css('height', mathematics.getRandom(50, 80)+'px');
             $('#icons_'+icon).css('background', 'red');
             */
        } else {
            icon_el.removeClass('icon-active');
            icon_el.css({'margin-left':'0px'});
            /*
             $('#icons_'+icon).css('width', '78px');
             $('#icons_'+icon).css('height', '78px');
             $('#icons_'+icon).css('background', '');
             */
        }
    },
    showEvent:function (icon) {
        if (php.count(this.newEvents[icon]) > 0) {
            var popped = this.newEvents[icon].pop();

            if (icon == 'todo') {
                dayPlan.draw();
                dayPlan.taskdayPlanToSelect = popped['id'];
            } else if (icon == 'email') {
                if (popped['flag'] == 'MS') {
                    mailEmulator.status = 0;
                    mailEmulator.draw('new');
                    mailEmulator.drawNewLetter();
                } else {
                    mailEmulator.status = 0;
                    mailEmulator.draw();
                    mailEmulator.curMesageToSelect = popped['id'];
                }
            } else if (icon == 'documents') {
                documents.fileToSelect = popped['id'];
                documents.draw();
            } else if (icon == 'phone') {
                phone.draw('income', popped);
            } else {
                dialogController.draw('income', popped);
            }
        } else {
            if (icon == 'todo') {
                dayPlan.draw()
            } else if (icon == 'email') {
                mailEmulator.draw()
            } else if (icon == 'documents') {
                documents.draw()
            } else if (icon == 'phone') {
                phone.draw();
            } else {
                var message = 'События по личной инициативе вам на данный момент не доступны';
                var lang_alert_title = 'События';
                var lang_confirmed = 'Ок';
                messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
            }
        }
    },
    setIconCounter:function (icon, counter) {
        $('#icons_' + icon).html('<span>' + counter + '</span>');
    },
    removeIconCounter:function (icon) {
        $('#icons_' + icon).html('');
    }
}