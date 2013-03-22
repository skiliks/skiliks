/**
 * Application entry point
 *
 * TODO: Add depending on jQuery and underscore
 */
require(['game/util/compatibility'], function(compatibility) {
    "use strict";

    _.templateSettings.interpolate = /<@=(.+?)@>/g;
    _.templateSettings.evaluate = /<@(.+?)@>/g;

    if (compatibility.check(window.gameConfig)) {
        require(
            [
                'game/views/world/SKApplicationView',
                'game/models/SKApplication'
            ],
            function (SKApplicationView, SKApplication) {
                $(function () {
                    window.SKApp = new SKApplication(window.gameConfig);
                    window.AppView = new SKApplicationView();
                });
            }
        );
    }

});