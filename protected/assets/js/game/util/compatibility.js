/**
 * Script checks browser compatibility and internet connection speed
 *
 * TODO: Add depending on jQuery
 */
define(['game/views/SKDialogView'], function(DialogView) {
    "use strict";

    var checkers = {
        browser: function(cfg) {
            var minSupport = {
                mozilla: 9,
                webkit: 535.11,

            };

            for (var name in minSupport) {
                if (minSupport.hasOwnProperty(name)) {
                    if ($.browser[name]) {
                        if (parseFloat($.browser.version) > minSupport[name]) {
                            return true;
                        } else {
                            location.href = cfg.oldBrowserUrl;
                        }
                    }
                }
            }

            location.href = cfg.badBrowserUrl;
            return false;
        },
        speed: function(cfg) {
            var start = new Date(),
                result = true;

            $.ajax({
                url: cfg.dummyFilePath,
                cache: false,
                async: false,
                timeout: 8000,
                error: function() {
                    if (confirm('Ваш интернет - говно!') === false) {
                        history.back();
                    }
                }
            });

            return result;
        }
    };

    return {
        check: function(config) {
            for (var func in checkers) {
                if (checkers.hasOwnProperty(func) && checkers[func](config) === false) {
                    return false;
                }
            }

            return true;
        }
    };
});