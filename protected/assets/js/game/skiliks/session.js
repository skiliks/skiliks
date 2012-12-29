(function () {
    "use strict";
    window.session = {
        sessionId:false,
        stype:0,
        simulationType:'normal',

        setSid:function (sid) {
            this.sessionId = sid;
        },
        getSid:function () {
            return this.sessionId;
        },
        clearSid:function () {
            this.sessionId = false;
        },
        setStype:function (stype) {
            this.stype = stype;

            if (parseInt(stype, 10) === 1) {
                this.simulationType = 'normal';
            } else if (parseInt(stype, 10) === 2) {
                this.simulationType = 'dev';
            }
        },
        getSimulationType:function () {
            return this.simulationType;
        }
    };
})();
