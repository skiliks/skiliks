/* global SocialCalc, $ */

(function() {
    "use strict";

    if (!SocialCalc.Formula.FunctionList || !SocialCalc.Formula.CalculateFunction) {
        throw new Error('SocialCalc util requires Spreadsheet Formula Library');
    }

    var fnMap = {
            "СУММ": "SUM",
            "сумм": "SUM"
        },
        origFn = SocialCalc.Formula.CalculateFunction;

    SocialCalc.Formula.CalculateFunction = function(fname, operand, sheet) {
        if (fnMap[fname.toUpperCase()]) {
            fname = fnMap[fname.toUpperCase()];
        }

        origFn.call(this, fname, operand, sheet);
    };
})();

