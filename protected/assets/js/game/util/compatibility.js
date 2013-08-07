/**
 * Script checks browser compatibility and internet connection speed
 *
 * TODO: Add depending on jQuery
 */
define(["jquery/jquery.browser"], function() {
    "use strict";
try {
        var checkers = {
            browser: function(cfg) {

                if (cfg.isSkipBrowserCheck) {
                    return true;
                }

                var minSupport = {
                    mozilla: 18,
                    chrome: 27
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

                location.href = cfg.oldBrowserUrl;
                return false;
            },

            speed: function(cfg) {
                window.netSpeedVerbose = 'fast';
                var start = new Date(),
                    callback = function() {
                        // TODO: Make translation
                        if (confirm('Ваша скорость интернета ниже допустимой. Мы не гарантируем комфортной работы') === false) {
                            window.netSpeedVerbose = 'slow';
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
    } catch(exception) {
        if (window.Raven) {
            window.Raven.captureMessage(exception.message + ',' + exception.stack);
        }
    }
});