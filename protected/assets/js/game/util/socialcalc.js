/* global SocialCalc, $ */

(function() {
    "use strict";

    if (!SocialCalc.Formula.FunctionList || !SocialCalc.Formula.CalculateFunction) {
        throw new Error('SocialCalc util requires Spreadsheet Formula Library');
    }

    var fnMap = {
            "СУММ": "SUM"
        },
        origFn = SocialCalc.Formula.CalculateFunction;

    SocialCalc.Formula.CalculateFunction = function(fname, operand, sheet) {
        if (fnMap[fname]) {
            fname = fnMap[fname];
        }

        origFn.call(this, fname, operand, sheet);
    };
})();

