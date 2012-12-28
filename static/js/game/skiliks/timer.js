/*global console, php, simulation, dialogController */
(function () {
    "use strict";
    var timer = {
        timer: 0,
        lastDisplayFlag:false,
        issetDiv:false,
        divTop:50,
        divLeft:50,
        speedFactor:config.speedFactor,
        divZindex:50,
        saveExcelTime:null,
        saveExcelTimeout:null,
        recalcCounter:0,
        recalcReq:10,

        setSpeedFactor:function (val) {
            this.speedFactor = val;
        },
        setDivTop:function (val) {
            this.divTop = val;
        },
        setDivLeft:function (val) {
            this.divLeft = val;
        },
        switchDisplayMode:function (mode) {
            if (mode === 'normal') {
                $('#timerMainDiv').css('z-index', this.divZindex);
            } else if (mode === 'dialog') {
                var zIndex = dialogController.getZindex();
                $('#timerMainDiv').css('z-index', (zIndex - 1));
            }
        },
        createDiv:function () {
            var div = document.createElement('div');
            div.setAttribute('id', 'timerMainDiv');
            div.setAttribute('class', 'timerMainDiv');
            div.style.position = "absolute";
            div.style.zIndex = this.divZindex;
            document.body.appendChild(div);
            $('#timerMainDiv').css('top', this.divTop + 'px');
            /*$('#timerMainDiv').css('width', '40px');
             $('#timerMainDiv').css('height', '25px');*/
            $('#timerMainDiv').css('left', this.divLeft + 'px');
            $('#timerMainDiv').css('line-height', '35px');

            this.issetDiv = true;
        },
        draw:function (timeString) {
            if (!this.issetDiv) {
                this.createDiv();
            }

            if (!timeString) { timeString = this.clockDefault; }

            var html = this.html;
            html = php.str_replace('{clock}', timeString, html);
            $('#timerMainDiv').html(html);
        },
        update:function () {
            if (this.recalcCounter < this.recalcReq) {
                this.recalcCounter++;
            } else {
                this.recalcCounter = 0;
                this.drawNewTime();
            }
        },
        drawNewTime:function () {
            var timeString = this.getCurTime();
            this.draw(timeString);
        },
        getCurTime:function (format) {
            if (typeof(format) === "undefined") {
                format = 'regular';
            }

            var unixtime = this.getCurUnixtime();

            if (this.timer === 0) {
                this.timer = unixtime;
            }

            var variance = unixtime - this.timer;
            variance = variance * this.speedFactor;
            var startTimeParts = config.startTime.split(':');

            var startTimePartHour = parseInt(startTimeParts[0], 10);
            var startTimePartMin = parseInt(startTimeParts[1], 10);
            if (format === "timeStamp") {
                //добавляем наши несчастные 9 часов
                variance = variance + (startTimePartHour * 60 + startTimePartMin) * 60;
                return variance;
            }

            var unixtimeMins = Math.floor(variance / 60);
            var clockH = Math.floor(unixtimeMins / 60);
            var clockM = unixtimeMins - (clockH * 60);
            //учет 9-и часов
            clockH = clockH + startTimePartHour + Math.floor((clockM + startTimePartMin) / 60);
            clockM = (clockM + startTimePartMin) % 60;
            var clockS = variance - (unixtimeMins * 60);

            if (clockH >= 21) {
                simulation.stop();
            }
            var separator;
            if (!this.lastDisplayFlag) {
                separator = ':';
                this.lastDisplayFlag = true;
            } else {
                separator = ' ';
                this.lastDisplayFlag = false;
            }
            var timeString = php.LdgZero(clockH, 2) + separator + php.LdgZero(clockM, 2);
            if (format === "full") {
                timeString += separator + php.LdgZero(clockS, 2);
            }
            return timeString;
        },
        getCurTimeFormatted:function (format) {
            if (typeof(format) === "undefined") {
                format = 'regular';
            }

            var timeString = this.getCurTime(format);
            if (format === "timeStamp") {
                //взовращаем без обработки
                return timeString;
            }

            timeString = php.str_replace(' ', ':', timeString);
            return timeString;
        },
        getCurUnixtime:function () {
            var foo = new Date(); // Generic JS date object
            var unixtime_ms = foo.getTime(); // Returns milliseconds since the epoch
            return parseInt(unixtime_ms / 1000, 10);
        },
        setTimeTo:function (h, m) {
            
            var foo = new Date(); // Generic JS date object
            var unixtime_ms = foo.getTime(); // Returns milliseconds since the epoch
            var unixtime = parseInt(unixtime_ms / 1000, 10);

            var variance = unixtime - this.timer;
            variance = variance * this.speedFactor;

            var unixtimeMins = Math.floor(variance / 60);
            var clockH = Math.floor(unixtimeMins / 60);
            var clockM = unixtimeMins - (clockH * 60);
            var startTimeParts = config.startTime.split(':');

            var startTimePartHour = parseInt(startTimeParts[0], 10);
            var startTimePartMin = parseInt(startTimeParts[1], 10);
            clockH = clockH + startTimePartHour;
            clockM = clockM + startTimePartMin;

            this.setSaveExcelTime(h, m);
            this.timer = (this.timer - ((h - clockH) * 60 * 60 / this.speedFactor) - ((m - clockM) * 60 / this.speedFactor));
        },
        setSaveExcelTime : function(h, m){
            var startH = parseInt(h);
            var startM = parseInt(m);
            var endTime = timer.getSimulationEndTime();
            var endH = endTime.hours;
            var endM = endTime.minutes;
            console.log('startTime:'+startH+':'+startM);
            console.log('endTime:'+endH+':'+endM);
            console.log("this.speedFactor : "+this.speedFactor);
            //(((64800)+(0)) - (32400)+(0)) / 60 * 8 * 1000
            var timeout = (((endH * 60 * 60) + (endM * 60)) - ((startH * 60 * 60) + (startM * 60))) / this.speedFactor * 1000;
            console.log("timeout : "+timeout);
            window.clearTimeout(this.saveExcelTime);
            this.saveExcelTime = window.setTimeout(excel.windowsSaveExcel, timeout);
        
        },
        setSaveExcelTimeout : function(){
            if(excel.fileId != excel.curExcelID){
                if(excel.fileId == 0){
                    excel.draw(excel.curExcelID);
                }else{
                    excel.draw(excel.fileId);
                    excel.draw(excel.curExcelID);
                }
            }
            if(this.saveExcelTimeout == null){
                var time = 5 * 60 / this.speedFactor * 1000;
                this.saveExcelTimeout = window.setInterval(excel.windowsSaveExcel, time);
            }
            
        },
        getSimulationStartTime : function() {
            var startTime = config.startTime.split(':');
            return {hours:parseInt(startTime[0]), minutes:parseInt(startTime[1])};
        },
        getSimulationEndTime : function(){
            var endTime = config.endTime.split(':');
            return {hours:parseInt(endTime[0]), minutes:parseInt(endTime[1])};
        },
        html1:'<span class="badge badge-inverse">{clock}</span>',
        html:'<ul class="main-screen-stat">' +
            '<li>{clock}</li>' +
            '<li><img src="/static/img/main-screen/icon-bat-full.png" alt="" /></li>' +
            '<li><a><img alt="" src="/static/img/main-screen/icon-help.png"></a></li>' +
            '</ul>',
        clockDefault: config.startTime
    };
    window.timer = timer;
})();