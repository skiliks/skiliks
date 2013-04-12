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
                'game/views/IntroView'
            ],
            function (IntroView) {
                $(function () {
                    //var View1 = Backbone.View.extend();
                    window.IntroView = new IntroView();
                    //var View2 = Backbone.View.extend({
                    //    eventHandler: function(data) {alert(data)}
                   // });

                    //var v1 = new View1;
                   // var v2 = new View2;

                    //v2.bind('hello-world-event', v2.eventHandler)
                   // v2.trigger('hello-world-event', 'Hello World!')
                    window.IntroView.bind('simulationStart',window.IntroView.eventHandler);
                    //window.IntroView.trigger('simulationStart');
                    //window.IntroView = new IntroView();
                });
            }
        );
    }

});