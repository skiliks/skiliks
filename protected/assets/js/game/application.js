/**
 * Application entry point
 *
 * TODO: Add depending on jQuery and underscore
 */
require([
    'backbone',
    'game/util/compatibility',
    'game/views/SKIntroView',
    'game/views/world/SKApplicationView',
    'game/models/SKApplication',
    'jquery/jquery-ui-1.8.24.custom'
], function(backbone, compatibility,SKIntroView, SKApplicationView, SKApplication) {
    "use strict";

    _.templateSettings.interpolate = /<@=(.+?)@>/g;
    _.templateSettings.evaluate = /<@(.+?)@>/g;

    if (compatibility.check(window.gameConfig)) {
        $(function () {
            //var View1 = Backbone.View.extend();
           //if($.cookie('intro_is_watched') === undefined || $.cookie('intro_is_watched') === null){
               var intro = new SKIntroView();
               intro.bind('simulationStart', intro.eventHandler);
           //}else{
           //    window.SKApp = new SKApplication(window.gameConfig);
           //    window.AppView = new SKApplicationView();
           //}
        });
    }

});