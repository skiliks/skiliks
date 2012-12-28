/*global _, Backbone, simulation, SKSettingsView:true, world, messages*/
(function () {
    "use strict";
    window.SKApplicationView = Backbone.View.extend({
        'initialize': function () {
            this.render();
        },
        'render': function () {
            var code = _.template($('#settings_template').html(), {});
            this.$el.html(code);
        }
    });
})();