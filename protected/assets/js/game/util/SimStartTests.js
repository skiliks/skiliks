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
                updateImageLoaderBar('Проверка совместимости браузера...');
                if (cfg.isSkipBrowserCheck) {
                    return true;
                }

                var minSupport = {
                    mozilla: 18,
                    chrome: 27,
                    msie: 10
                };

                /**
                 * Также есть проверка в SiteController->actionSimulation().
                 * Она нужна -- потому что в IE8, текущая JS проверка валится.
                 */

                if (window.httpUserAgent.indexOf('YaBrowser') != -1) {
                    location.href = cfg.oldBrowserUrl;
                    return false;
                }

                for (var name in minSupport) {
                    if (minSupport.hasOwnProperty(name)) {
                        if ($.browser[name]) {
                            if (parseFloat($.browser.version) >= minSupport[name] && this.isAllowOS(cfg.isSkipOsCheck, ['Windows', 'MacOS'])) {
                                updateImageLoaderBar('Проверка совместимости браузера... OK!', 0.85, true);
                                return true;
                            } else {
                                location.href = cfg.oldBrowserUrl;
                                return false;
                            }
                        }
                    }
                }

                location.href = cfg.oldBrowserUrl;
                return false;
            },

            processorSpeed: function(cfg) {
                updateImageLoaderBar('Проверка текущего быстродействия...');

                var processorTestResult = jsBogoMips.getAveragedJsBogoMips(3);

                var isDevMode = document.location.href.indexOf('developer') > -1;

                if(true == isDevMode) {
                    return true;
                }

                if (cfg.isSkipSpeedTest) {
                    return true;
                }

                $.ajax({
                    url: '/index.php/logService/addInviteLog',
                    data: {
                        inviteId: window.inviteId,
                        action: 'Предупреждение о низкой скорости процессора. Уровень ' + processorTestResult.average,
                        uniqueId: -1,
                        time: '00:00:00'
                    },
                    type: 'POST',
                    cache: false,
                    async: false
                });

                if(processorTestResult.average > 1) {
                    updateImageLoaderBar('Проверка текущего быстродействия... OK!', 0.90, true);
                    return true;
                }
                else {
                    // Spike to make alert ok works fine
                    // TODO: refactor all dialog views to one style
                    if (alert('Мы сожалеем, но конфигурация Вашего компьютера ниже минимально допустимой. ' +
                        'Минимальные системный требования для комфортной игры двухядерный процессор (2х1,1ГГц)'+
                        ' и 2 Гб оперативной памяти. Попробуйте запустить игру на другом компьютере. ' +
                        'Производительность вашего компьютера ' + processorTestResult.average + ' баллов.')) {
                        location.href = '/dashboard';
                        return false;
                    }
                    else {
                        location.href = '/dashboard';
                        return false;
                    }
                }

                updateImageLoaderBar('Проверка текущего быстродействия... OK!', 0.90, true);
                return true;
            },

            downloadSpeed: function(cfg) {
                updateImageLoaderBar('Проверка скорости соединения...');
                window.netSpeedVerbose = 'fast';

                var isDevMode = document.location.href.indexOf('developer') > -1;

                var start = new Date(),
                    callback = function() {
                        // flag variable
                        window.netSpeedVerbose = 'slow';

                        // logging {
                        if (false == isDevMode) {
                            $.ajax({
                                url: '/index.php/logService/addInviteLog',
                                data: {
                                    inviteId: window.inviteId,
                                    action: 'Warning about low internet connection speed has been displayed.',
                                    uniqueId: -1,
                                    time: '00:00:00'
                                },
                                type: 'POST',
                                cache: false,
                                async: false
                            });
                        }
                        // logging }

                        // TODO: Make translation
                        if (confirm('Ваша скорость интернета ниже допустимой. Мы не гарантируем комфортной работы. Для комфортной работы вам понадобится соединение с интернет на скорости от 1Мб/сек.') === false) {
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

                updateImageLoaderBar('Запуск симуляции...', 0.95, true);
                return true;
            },

            isAllowOS:function(isSkipOsCheck, allowed_os_list) {

                if(isSkipOsCheck) {
                    return true
                }
                var os_name ="Unknown OS";
                if (navigator.appVersion.indexOf("Win")!=-1) { os_name = "Windows"; }
                if (navigator.appVersion.indexOf("Mac")!=-1) { os_name = "MacOS"; }
                if (navigator.appVersion.indexOf("X11")!=-1) { os_name = "UNIX"; }
                if (navigator.appVersion.indexOf("Linux")!=-1) { os_name = "Linux"; }

                var result = false;

                $.each(allowed_os_list, function(i, current_os_name) {
                    if(current_os_name === os_name){
                        result = true;
                        return false;
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
    } catch(exception) {
        if (window.Raven) {
            window.Raven.captureMessage(exception.message + ',' + exception.stack);
        }
    }
});