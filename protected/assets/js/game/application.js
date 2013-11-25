/**
 * Application entry point
 *
 * TODO: Add depending on jQuery and underscore
 */
require([
    'underscore',
    'backbone',
    'game/util/SimStartTests',
    'game/views/SKIntroView',
    'game/views/world/SKApplicationView',
    'game/models/SKApplication'
], function(underscore, backbone, SimStartTests, SKIntroView, SKApplicationView, SKApplication) {
    "use strict";

    try {
        //console.log('require');
        _.templateSettings.interpolate = /<@=(.+?)@>/g;
        _.templateSettings.evaluate = /<@(.+?)@>/g;
        Backbone.emulateJSON = true;
    } catch(exception) {
        if (window.Raven) {
            window.Raven.captureMessage(exception.message + ',' + exception.stack);
        }
    }

   window.SKInitApplication = function() {
        try {
            if (SimStartTests.check(window.gameConfig)) {
                window.SKApp = new SKApplication(window.gameConfig);
                window.AppView = new SKApplicationView();

                var intro = new SKIntroView();
                if (!$.cookie('intro_is_watched_2') && window.gameConfig.type === 'tutorial') {
                    intro.show();
                } else {
                    intro.appLaunch();
                }
            }
        } catch(exception) {
            if (window.Raven) {
                window.Raven.captureMessage(exception.message + ',' + exception.stack);
            }
        }

   };

    // код предзагрузки картинок {

    // список файлов, которые надо будет загрузить
    window.filesToLoad = {};
    var isAllImagesDownloaded = false;

    //console.log(preLoadImages);

    // наполнение filesToLoad адресами из preLoadImages {
    var n = 0;
    for (var i in preLoadImages) {
        var type = 'image';
    // пока не удаляю, но если jst не загрузился срезу - то require js падает неминуемо
    //        if (-1 < preLoadImages[i].indexOf('.jst')) {
    //            type = 'jst';
    //        }

        if (-1 < preLoadImages[i].indexOf('.css')) {
            type = 'css';
        }

        if (true === $.browser['msie'] && -1 < preLoadImages[i].indexOf('.cur')) {
            type = 'cursor';
        }

        filesToLoad[n] = {
            id: 'cache-image-' + n,
            url: 'http://loc.skiliks.com' + preLoadImages[i],
            size: 16, // kB,
            type: type,
            timeoutLength: undefined,
            isLoaded: false
        }
        n++;
    }
    // наполнение filesToLoad адресами из preLoadImages }

    /**
     * Метод отвечает за перерисовку полосы прогресса при старте игры
     * и отображение текста над ней (что сейчас грузится "Загузка данных...")
     *
     * @param string text
     * @param float newValue, value from 0 to 1
     * @param boolean isUpdateValue
     */
    window.updateImageLoaderBar = function(text, newValue, isUpdateValue)
    {
        $('#images-loader-text').text(text);

        if (true === isUpdateValue && 'undefiled' != typeof newValue) {
            $('#images-loader-bar').width(newValue*400  + 'px');
            return;
        }
        var currentCounter = 0;
        var total = 0;

        for (var key in filesToLoad) {
            if (filesToLoad[key].isLoaded) {
                currentCounter++;
            }
            total++;
        }

        // длинна полосы прогересса 400рх
        // но мы оставили последние 40рх для отображения прогресса SimStartTests
        $('#images-loader-bar').width((currentCounter/total)*360  + 'px');

        if (currentCounter == total && false === isAllImagesDownloaded) {
            //console.log('start!', currentCounter, total, isAllImagesDownloaded);
            isAllImagesDownloaded = true;
            $('body').css('float', 'left');

            // добавляем CSS  в правильной последовательности:
            for (var key in filesToLoad) {
                if (-1 < filesToLoad[key].url.indexOf('.css')) {
                    $('head').append('<link rel="stylesheet" href="' + filesToLoad[key].url +
                        '" type="text/css" />');
                }
            }

            window.SKInitApplication();
        }
    }

    // Инициализируем пустую полосу загрузки
    updateImageLoaderBar('Загрузка данных...', 0, true);

    /**
     * Метод рекурсивной предзагрузки одной картинки
     *
     * @param imageToLoad
     */
    var preLoadImage = function(imageToLoad) {
        var imageToLoad = imageToLoad;

        // 512 kB sec:
        imageToLoad.timeoutLength = Math.ceil(imageToLoad.size / 512) * 1000; // milliseconds

        // 6000 - чтоб слишком часто не детектировало, что "интернет пропал"
        // по сути, при хорошем коннекте, за это время загружаются все картинки, CSS и JS
        if (imageToLoad.timeoutLength < 6000) {
            imageToLoad.timeoutLength = 6000;
        }

        imageToLoad.image = $('<img width="0" height="0" style="opacity: 0;" id="' + imageToLoad.id + '" src="' + imageToLoad.url + '"/\>');

        //console.log(imageToLoad.url);

        imageToLoad.image.load(function() {

            //console.log($(this).attr('id') + ' loaded !');
            $('body').append($(this));

            for(var key2 in filesToLoad) {
                if ($(this).attr('id') == filesToLoad[key2].id) {
                    filesToLoad[key2].isLoaded = true;
                     updateImageLoaderBar('Загрузка данных...');
                    break;
                }
            }
        });

        setTimeout(function() {
            if (false == imageToLoad.isLoaded) {
                updateImageLoaderBar('Пропало интернет соединение или медленное интернет соединение ...');
                preLoadImage(imageToLoad);
            }
        }, imageToLoad.timeoutLength);
    };

    // пока не удаляю, но если jst не загрузился срезу - то require js падает неминуемо
    //    window.preLoadJst = function(jstToLoad) {
    //        var jstToLoad = jstToLoad;
    //
    //        // 512 kB sec:
    //        jstToLoad.timeoutLength = Math.ceil(jstToLoad.size / 512) * 1000; // milliseconds
    //
    //        console.log('load ' + jstToLoad.url);
    //        $.ajax({
    //            url: jstToLoad.url,
    //            type: 'GET',
    //            success: function() {
    //                console.log('loaded! ' + jstToLoad.url);
    //                for(var key2 in filesToLoad) {
    //                    if (jstToLoad.id == filesToLoad[key2].id) {
    //                        filesToLoad[key2].isLoaded = true;
    //                        updateImageLoaderBar('Загрузка данных...');
    //                        break;
    //                    }
    //                }
    //            },
    //            complete: function (xhr, text_status) {
    //                if (('timeout' === text_status || xhr.status === 0)) {
    //                    console.log('reload JST');
    //                    window.preLoadJst(jstToLoad);
    //                }
    //            },
    //            timeout: jstToLoad.timeoutLength
    //        });
    //    };

    /**
     * Метод рекурсивной предзагрузки одного CSS-файла
     *
     * @param cssToLoad
     */
    window.preLoadCss = function(cssToLoad) {
        var cssToLoad = cssToLoad;

        // 512 kB sec:
        cssToLoad.timeoutLength = Math.ceil(cssToLoad.size / 512) * 1000; // milliseconds

        // 6000 - чтоб слишком часто не детектировало, что "интернет пропал"
        // по сути, при хорошем коннекте, за это время загружаются все картинки, CSS и JS
        if (cssToLoad.timeoutLength < 6000) {
            cssToLoad.timeoutLength = 6000;
        }

        console.log('load ' + cssToLoad.url);
        $.ajax({
            url: cssToLoad.url,
            type: 'GET',
            success: function() {
                console.log('loaded! ' + cssToLoad.url);
                for(var key2 in filesToLoad) {
                    if (cssToLoad.id == filesToLoad[key2].id) {
                        filesToLoad[key2].isLoaded = true;
                        updateImageLoaderBar('Загрузка данных...');
                        break;
                    }
                }
            },
            complete: function (xhr, text_status) {
                console.log(xhr, text_status);
                if (('timeout' === text_status || xhr.status === 0)) {
                    console.log('reload CSS');
                    window.preLoadCss(cssToLoad);
                }
            },
            timeout: cssToLoad.timeoutLength
        });
    };

    /**
     * Метод рекурсивной предзагрузки одного CSS-файла
     *
     * @param cursorToLoad
     */
    window.preLoadCursor = function(cursorToLoad) {
        var cursorToLoad = cursorToLoad;

        // 512 kB sec:
        cursorToLoad.timeoutLength = Math.ceil(cursorToLoad.size / 512) * 1000; // milliseconds

        // 6000 - чтоб слишком часто не детектировало, что "интернет пропал"
        // по сути, при хорошем коннекте, за это время загружаются все картинки, CSS и JS
        if (cursorToLoad.timeoutLength < 6000) {
            cursorToLoad.timeoutLength = 6000;
        }

        cursorToLoad.image = $('<img width="0" height="0" style="opacity: 0;" id="'
            + cursorToLoad.id + '" src="' + cursorToLoad.url + '"/\>');

        //console.log('load ' + cursorToLoad.url);
        $.ajax({
            url: cursorToLoad.url,
            type: 'GET',
            success: function() {
                //console.log('loaded! ' + cursorToLoad.url);
                for(var key2 in filesToLoad) {
                    if (cursorToLoad.id == filesToLoad[key2].id) {
                        filesToLoad[key2].isLoaded = true;
                        $('body').append(filesToLoad[key2].image);

                        updateImageLoaderBar('Загрузка данных...');
                        break;
                    }
                }
            },
            complete: function (xhr, text_status) {
                if (('timeout' === text_status || xhr.status === 0)) {
                    //console.log('reload cursor');
                    window.preLoadCursor(cursorToLoad);
                }
            },
            timeout: cursorToLoad.timeoutLength
        });
    };

    /**
     * Метод вызывает функцию предзагрузки для каждой картинки
     */
    var preLoadImage_s = function() {
        //console.log(filesToLoad);
        for (var key in filesToLoad) {
            if ('image' == filesToLoad[key].type) {
                preLoadImage(filesToLoad[key]);
            }

            if ('css' == filesToLoad[key].type) {
                 preLoadCss(filesToLoad[key]);
            }

            if ('cursor' == filesToLoad[key].type) {
                preLoadCursor(filesToLoad[key]);
            }

            // пока не удаляю, но если jst не загрузился срезу - то require js падает неминуемо
            // if ('jst' == filesToLoad[key].type) {
            //     preLoadJst(filesToLoad[key]);
            // }
        }
    };

    // а вот и вызов предзагрузки картинок
    $(window).ready(preLoadImage_s());

    // код предзагрузки картинок }
});