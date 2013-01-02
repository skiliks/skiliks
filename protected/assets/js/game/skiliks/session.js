/*global $ */
(function () {
    "use strict";
    window.session = {
        sessionId:false,
        stype:0,
        simulationType:'normal',

        getSid:function () {
            return $.cookie('sid');
        },
        getSimulationType:function () {
            return this.simulationType;
        }
    };
})();
