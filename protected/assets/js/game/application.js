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
    'jquery/jquery-ui-1.10.3.custom.min'
], function(backbone, compatibility,SKIntroView, SKApplicationView, SKApplication) {
    "use strict";

    _.templateSettings.interpolate = /<@=(.+?)@>/g;
    _.templateSettings.evaluate = /<@(.+?)@>/g;

    if (compatibility.check(window.gameConfig)) {
        $(function () {
           var intro = new SKIntroView();
           if($.cookie('intro_is_watched_2') === undefined || $.cookie('intro_is_watched_2') === null){
               intro.show();
           }else{
               intro.appLaunch();
           }
        });
    }

});