/**
 * Application entry point
 *
 * TODO: Add depending on jQuery and underscore
 */
require([
    'backbone',
    'game/util/SimStartTests',
    'game/views/SKIntroView',
    'game/views/world/SKApplicationView',
    'game/models/SKApplication'
], function(backbone, SimStartTests, SKIntroView, SKApplicationView, SKApplication) {
    "use strict";
    try {
        _.templateSettings.interpolate = /<@=(.+?)@>/g;
        _.templateSettings.evaluate = /<@(.+?)@>/g;
        Backbone.emulateJSON = true;

        if (SimStartTests.check(window.gameConfig)) {
            $(function () {
                window.SKApp = new SKApplication(window.gameConfig);
                window.AppView = new SKApplicationView();

                var intro = new SKIntroView();
                if (!$.cookie('intro_is_watched_2') && window.gameConfig.type === 'tutorial') {
                    intro.show();
                } else {
                    intro.appLaunch();
                }
            });
        }
    } catch(exception) {
        if (window.Raven) {
            window.Raven.captureMessage(exception.message + ',' + exception.stack);
        }
    }

});