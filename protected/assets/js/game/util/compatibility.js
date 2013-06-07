/**
 * Script checks browser compatibility and internet connection speed
 *
 * TODO: Add depending on jQuery
 */
define([], function() {
    "use strict";

    var checkers = {
        os: function(cfg) {
            var match = navigator.userAgent.match(/mac os(?: x)? (\d+)[\._](\d)/i),
                minVersion = 10.7,
                version;

            if (match) {
                version = match[1] + match[2] * 0.1;
                if (version < minVersion) {
                    location.href = cfg.oldBrowserUrl;
                    return false;
                }
            }

            return true;
        },
        browser: function(cfg) {
            var minSupport = {
                mozilla: 18,
                webkit: 536.5
            };

            for (var name in minSupport) {
                if (minSupport.hasOwnProperty(name)) {
                    if ($.browser[name]) {
                        if (parseFloat($.browser.version) >= minSupport[name]) {
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
                callback = function() {
                    // TODO: Make translation
                    if (confirm('Ваша скорость интернета ниже допустимой. Мы не гарантируем комфортной работы') === false) {
                        history.back();
                    }
                };

            $.ajax({
                url: cfg.dummyFilePath,
                cache: false,
                async: false,
                error: callback
            });

            if (new Date() - start > 8000) {
                callback();
            }

            return true;
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