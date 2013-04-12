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
                'game/views/SKIntroView',
                'game/views/world/SKApplicationView',
                'game/models/SKApplication'
            ],
            function (SKIntroView, SKApplicationView, SKApplication) {
                $(function () {
                    //var View1 = Backbone.View.extend();
                   if($.cookie('intro_is_watched') === undefined || $.cookie('intro_is_watched') === null){
                       window.SKIntroView = new SKIntroView();
                       window.SKIntroView.bind('simulationStart', window.SKIntroView.eventHandler);
                   }else{
                       window.SKApp = new SKApplication(window.gameConfig);
                       window.AppView = new SKApplicationView();
                   }
                });
            }
        );
    }

});