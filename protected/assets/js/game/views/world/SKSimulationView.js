/*global Backbone, _, $, SKApp, SKDebugView, SKIconPanelView */
(function () {
    "use strict";
    window.SKSimulationView = Backbone.View.extend({
        'el':'body',
        'events':{
            'click .btn-simulation-stop':'doSimulationStop'
        },
        'initialize':function () {
            var me = this;
            var simulation = this.simulation = SKApp.user.simulation;
            this.addSimulationEvents();
            simulation.on('tick', function () {
                me.updateTime();
            });
        },
        'render':function () {
            var login_html = _.template($('#simulation_template').html(), {});
            this.$el.html(login_html);
            this.icon_view = new SKIconPanelView({'el':this.$('.main-screen-icons')});
            if (this.simulation.isDebug()) {
                this.debug_view = new SKDebugView({'el':this.$('.debug-panel')});
            }
            var canvas = this.$('.canvas');
            this.updateTime();
        },
        'updateTime':function () {
            var parts = this.simulation.getGameTime().split(':');
            this.$('.time .hour').text(parts[0]);
            this.$('.time .minute').text(parts[1]);
        },
        'addSimulationEvents':function () {
            SKApp.user.simulation.events.on('add', function (event) {
                if (event.getTypeSlug() === 'immediate-visit') {
                    // TODO: incorrect location
                    var visit_view = new SKVisitView({'event':event});
                    event.complete();
                }
            });
        },

        'doSimulationStop':function () {
            SKApp.user.stopSimulation();
        }
    });
})();