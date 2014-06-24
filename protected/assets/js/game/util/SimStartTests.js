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
                    msie: 10,
                    safari: 6
                };

                var maxSupport = {
                    mozilla: window.gameConfig.firefox_max_support,
                    chrome:  window.gameConfig.chrome_max_support,
                    msie:    window.gameConfig.msie_max_support,
                    safari:  window.gameConfig.safari_max_support
                };

                var BrowserFullNames = {
                    mozilla: 'Mozilla Firefox',
                    chrome: 'Google Chrome',
                    msie: 'Internet Explorer',
                    safari: 'Safari'
                };

                // формируем строки с перечнем поддерживаемых версий браузеров
                var chrome_versions_to_block = window.gameConfig.chrome_version_to_block.replace(' ','').split(",");
                var internet_explorer_versions_to_block = window.gameConfig.internet_explorer_version_to_block.replace(' ','').split(",");
                var firefox_versions_to_block = window.gameConfig.firefox_version_to_block.replace(' ','').split(",");
                var safari_versions_to_block = window.gameConfig.safari_version_to_block.replace(' ','').split(",");

                // CHROME {
                var chromeVersionsSupport = [];
                for(var i = minSupport.chrome; i <= maxSupport.chrome; i++) {
                    chromeVersionsSupport[i] = true;
                }
                for(var index in chrome_versions_to_block) {
                    if (parseInt(chrome_versions_to_block[index]) <= parseInt(maxSupport.chrome)
                        && '' != chrome_versions_to_block[index]) {
                        chromeVersionsSupport[chrome_versions_to_block[index].replace(' ','')] = false;
                    }
                }

                // формируем текст
                var chromeMiddleText = '';
                var before = null;
                for(var version in chromeVersionsSupport) {
                    if ('' == chromeMiddleText && true == chromeVersionsSupport[version]) {
                        // формирую первую цифру
                        chromeMiddleText += '' + version + '-';
                    } else if (true == before && false == chromeVersionsSupport[version]
                        && '-' == chromeMiddleText.substr(chromeMiddleText.length -1, 1)) {
                        // замыкаем последнюю цифру в группе
                        chromeMiddleText += '' + (version - 1);
                        if (version != maxSupport.chrome) {
                            chromeMiddleText += ',';
                        }
                    } else if (false == before && true == chromeVersionsSupport[version]) {
                        // начало группы в середине массима
                        chromeMiddleText += '' + version + '-';
                    } else if (version == maxSupport.chrome
                        && true == chromeVersionsSupport[version]
                        && '-' == chromeMiddleText.substr(chromeMiddleText.length -1, 1)) {
                        // завершаем
                        chromeMiddleText += '' + version;
                    }

                    // убираем диаппазоны из одной цифры: "23-23" => "23"
                    chromeMiddleText = chromeMiddleText.replace((version-1) + '-' + (version-1), (version-1));

                    // запоминаем предыдущее состояние
                    before = chromeVersionsSupport[version];
                }

                if ('-' == chromeMiddleText.substr(chromeMiddleText.length -1, 1)) {
                    chromeMiddleText = chromeMiddleText.substr(0, chromeMiddleText.length -1);
                }

                if (',' != chromeMiddleText.substr(chromeMiddleText.length -1, 1)) {
                    chromeMiddleText += ',';
                }
                // CHROME }

                // FIREFOX {
                var firefoxVersionsSupport = [];
                for(var i = minSupport.mozilla; i <= maxSupport.mozilla; i++) {
                    firefoxVersionsSupport[i] = true;
                }
                for(var index in firefox_versions_to_block) {
                    if (parseInt(firefox_versions_to_block[index]) <= parseInt(maxSupport.mozilla)
                        && '' != firefox_versions_to_block[index]) {
                        firefoxVersionsSupport[firefox_versions_to_block[index].replace(' ','')] = false;
                    }
                }

                // формируем текст
                var firefoxMiddleText = '';
                var before = null;
                for(var version in firefoxVersionsSupport) {
                    if ('' == firefoxMiddleText && true == firefoxVersionsSupport[version]) {
                        // формирую первую цифру
                        firefoxMiddleText += '' + version + '-';
                    } else if (true == before && false == firefoxVersionsSupport[version]
                        && '-' == firefoxMiddleText.substr(firefoxMiddleText.length -1, 1)) {
                        // замыкаем последнюю цифру в группе
                        firefoxMiddleText += '' + (version - 1);
                        if (version != maxSupport.mozilla) {
                            firefoxMiddleText += ',';
                        }
                    } else if (false == before && true == firefoxVersionsSupport[version]) {
                        // начало группы в середине массима
                        firefoxMiddleText += '' + version + '-';
                    } else if (version == maxSupport.mozilla
                        && true == firefoxVersionsSupport[version]
                        && '-' == firefoxMiddleText.substr(firefoxMiddleText.length -1, 1)) {
                        // завершаем
                        firefoxMiddleText += '' + version;
                    }

                    // убираем диаппазоны из одной цифры: "23-23" => "23"
                    firefoxMiddleText = firefoxMiddleText.replace((version-1) + '-' + (version-1), (version-1));

                    // запоминаем предыдущее состояние
                    before = firefoxVersionsSupport[version];
                }

                if ('-' == firefoxMiddleText.substr(firefoxMiddleText.length -1, 1)) {
                    firefoxMiddleText = firefoxMiddleText.substr(0, firefoxMiddleText.length -1);
                }

                if (',' != firefoxMiddleText.substr(firefoxMiddleText.length -1, 1)) {
                    firefoxMiddleText += ',';
                }
                // FIREFOX }

                // IE {
                var ieVersionsSupport = [];
                for(var i = minSupport.msie; i <= maxSupport.msie; i++) {
                    ieVersionsSupport[i] = true;
                }
                for(var index in internet_explorer_versions_to_block) {
                    if (parseInt(internet_explorer_versions_to_block[index]) <= parseInt(maxSupport.msie)
                        && '' != internet_explorer_versions_to_block[index]) {
                        ieVersionsSupport[internet_explorer_versions_to_block[index].replace(' ','')] = false;
                    }
                }

                // формируем текст
                var ieMiddleText = '';
                var before = null;
                for(var version in ieVersionsSupport) {
                    if ('' == ieMiddleText && true == ieVersionsSupport[version]) {
                        // формирую первую цифру
                        ieMiddleText += '' + version + '-';
                    } else if (true == before && false == ieVersionsSupport[version]
                        && '-' == ieMiddleText.substr(ieMiddleText.length -1, 1)) {
                        // замыкаем последнюю цифру в группе
                        ieMiddleText += '' + (version - 1);
                        if (version != maxSupport.msie) {
                            ieMiddleText += ',';
                        }
                    } else if (false == before && true == ieVersionsSupport[version]) {
                        // начало группы в середине массима
                        ieMiddleText += '' + version + '-';
                    } else if (version == maxSupport.msie
                        && true == ieVersionsSupport[version]
                        && '-' == ieMiddleText.substr(ieMiddleText.length -1, 1)) {
                        // завершаем
                        ieMiddleText += '' + version;
                    }

                    // убираем диаппазоны из одной цифры: "23-23" => "23"
                    ieMiddleText = ieMiddleText.replace((version-1) + '-' + (version-1), (version-1));

                    // запоминаем предыдущее состояние
                    before = ieVersionsSupport[version];
                }

                if ('-' == ieMiddleText.substr(ieMiddleText.length -1, 1)) {
                    ieMiddleText = ieMiddleText.substr(0, ieMiddleText.length -1);
                }

                if (',' != ieMiddleText.substr(ieMiddleText.length -1, 1)) {
                    ieMiddleText += ',';
                }
                // IE }

                // SAFARI {
                var safariVersionsSupport = [];
                for(var i = minSupport.safari; i <= maxSupport.safari; i++) {
                    safariVersionsSupport[i] = true;
                }
                for(var index in safari_versions_to_block) {
                    if (parseInt(safari_versions_to_block[index]) <= parseInt(maxSupport.msie)
                        && '' != safari_versions_to_block[index]) {
                        safariVersionsSupport[safari_versions_to_block[index].replace(' ','')] = false;
                    }
                }

                // формируем текст
                var safariMiddleText = '';
                var before = null;
                for(var version in safariVersionsSupport) {
                    if ('' == safariMiddleText && true == safariVersionsSupport[version]) {
                        // формирую первую цифру
                        safariMiddleText += '' + version + '-';
                    } else if (true == before && false == safariVersionsSupport[version]
                        && '-' == safariMiddleText.substr(safariMiddleText.length -1, 1)) {
                        // замыкаем последнюю цифру в группе
                        safariMiddleText += '' + (version - 1);
                        if (version != maxSupport.safari) {
                            safariMiddleText += ',';
                        }
                    } else if (false == before && true == safariVersionsSupport[version]) {
                        // начало группы в середине массима
                        safariMiddleText += '' + version + '-';
                    } else if (version == maxSupport.safari
                        && true == safariVersionsSupport[version]
                        && '-' == safariMiddleText.substr(safariMiddleText.length -1, 1)) {
                        // завершаем
                        safariMiddleText += '' + version;
                    }

                    // убираем диаппазоны из одной цифры: "23-23" => "23"
                    safariMiddleText = safariMiddleText.replace((version-1) + '-' + (version-1), (version-1));

                    // запоминаем предыдущее состояние
                    before = safariVersionsSupport[version];
                }

                if ('-' == safariMiddleText.substr(safariMiddleText.length -1, 1)) {
                    safariMiddleText = safariMiddleText.substr(0, safariMiddleText.length -1);
                }

                // 'safari' последний, после него запятая не нужна
                // SAFARI }

                var supportText = {
                    mozilla: BrowserFullNames.mozilla + " " + firefoxMiddleText + " ",
                    chrome:  BrowserFullNames.chrome + " " + chromeMiddleText + " ",
                    msie:    BrowserFullNames.msie + " " + ieMiddleText + " ",
                    safari:  BrowserFullNames.safari + " " + safariMiddleText
                };

                /**
                 * Также есть проверка в SiteController->actionSimulation().
                 * Она нужна -- потому что в IE8, текущая JS проверка валится.
                 */

                if (window.httpUserAgent.indexOf('YaBrowser') != -1 || // -1 != -1 = false
                    window.httpUserAgent.indexOf('MRCHROME') != -1 ||
                    window.httpUserAgent.indexOf('IceDragon') != -1 ||
                    window.httpUserAgent.indexOf('Maxthon') != -1 ||
                    window.httpUserAgent.indexOf('PaleMoon') != -1 ||
                    window.httpUserAgent.indexOf('PB0.') != -1 ||
                    window.httpUserAgent.indexOf('QIPSurf') != -1 ||
                    window.httpUserAgent.indexOf('Sleipnir') != -1 ||
                    window.httpUserAgent.indexOf('SlimBrowser') != -1 ||
                    window.httpUserAgent.indexOf('Nichrome') != -1 ||
                    window.httpUserAgent.indexOf('Mobile') != -1
                ) {
                    location.href = cfg.oldBrowserUrl;
                    return false;
                }

                $.browser['chrome'] = false;
                $.browser['msie'] = true;
                $.browser.name = 'msie';
                $.browser.version = '10';
                $.browser.versionNumber = '10';

                for (var name in minSupport) {
                    if (minSupport.hasOwnProperty(name)) {
                        if ($.browser[name]) {

                            // блокируемые версии браузеров {
                            var isBlockedChromeVersion = ('chrome' == name
                                && chrome_versions_to_block.indexOf($.browser.version.substring(0, 2)) > -1);

                            var isBlockedFirefoxVersion = ('mozilla' == name
                                && firefox_versions_to_block.indexOf($.browser.version.substring(0, 2)) > -1);

                            var isBlockedInternetExplorerVersion = ('msie' == name
                                && internet_explorer_versions_to_block.indexOf($.browser.version.substring(0, 2)) > -1);

                            var isBlockedSafariVersion = ('safari' == name
                                && safari_versions_to_block.indexOf($.browser.version.substring(0, 1)) > -1);

                            if (isBlockedChromeVersion || isBlockedFirefoxVersion
                                || isBlockedInternetExplorerVersion || isBlockedSafariVersion) {
                                var oldBrowserText = "К сожалению мы не гарантируем стабильную работу в вашей версии браузера "
                                    + "(" + BrowserFullNames[name] + " " + $.browser.version + "). "
                                    + "Рекомендуем использовать следующие версии браузеров (";

                                for(var index in supportText) {
                                    oldBrowserText += supportText[index];
                                }

                                oldBrowserText += ').';

                                alert(oldBrowserText);
                                location.href = '/dashboard';
                                return false;
                            }
                            // блокируемые версии браузеров }

                            // не поддерживаемые версии браузеров {
                            if (parseFloat($.browser.version) >= minSupport[name] && this.isAllowOS(cfg.isSkipOsCheck, ['Windows', 'MacOS'])) {
                                if(parseFloat($.browser.version) > maxSupport[name]) {

                                    var oldBrowserText = "К сожалению мы не гарантируем стабильную работу в вашей версии браузера "
                                        + "(" + BrowserFullNames[name] + " " + $.browser.version + "). "
                                        + "Рекомендуем использовать следующие версии браузеров (";

                                    for(var index in supportText) {
                                        oldBrowserText += supportText[index];
                                    }

                                    oldBrowserText += ').';

                                    if (false == confirm(oldBrowserText)) {
                                        location.href = '/dashboard';
                                        return false;
                                    }
                                }
                                updateImageLoaderBar('Проверка совместимости браузера... OK!', 0.85, true);
                                return true;
                            } else {
                                location.href = cfg.oldBrowserUrl;
                                return false;
                            }
                            // не поддерживаемые версии браузеров }
                        }
                    }
                }
//                location.href = cfg.oldBrowserUrl;
//                return false;
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

                if(processorTestResult.average > 0.69) {
                    updateImageLoaderBar('Проверка текущего быстродействия... OK!', 0.90, true);
                    return true;
                } else {
                    // Spike to make alert ok works fine
                    // TODO: refactor all dialog views to one style
                    if (alert("Конфигурация вашего компьютера ниже минимально допустимой. \n" +
                        "Уровень производительности менее 1 балла (" + processorTestResult.average + ").\n"+
                        "Минимальные системные требования для комфортной игры: \n"+
                        "\n"+
                        "   • двухъядерный процессор (2х1,1 ГГц);\n"+
                        "   • 2 Гб оперативной памяти.\n"+
                        "\n"+
                        "Попробуйте запустить игру в другом браузере или на другом компьютере"
                    )) {
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