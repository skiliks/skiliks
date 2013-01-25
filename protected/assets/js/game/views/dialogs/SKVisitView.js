/*global SKVisitView:true, SKWindowView, Backbone, _, SKApp, SKConfig, SKDialogWindow*/
(function () {
    "use strict";
    /**
     * @class
     * @type {*}
     */
    window.SKVisitView = SKWindowView.extend(
        /** @lends SKVisitView.prototype */
        {
        'el':'body .visitor_door',
        'events':{
            "click body .visitor_door .visitor-allow":'allow',
            "click body .visitor_door .visitor-deny":'deny'
        },
        'initialize':function () {
            var me = this;
            $('#canvas').append($('<div class="visitor_door"></div>'));
            this.render();

        },
        'close':function () {
            console.log("Click");
            $('.visitor_door').remove();
        },
        'render':function () {
            var event = this.options.event;
            console.log(event);
            //this.$el.html(_.template($('#visitor_door').html(), _.defaults(SKConfig)));
            this.renderTPL('.visitor_door', '#visit_door', {'visit':event.get('data')});

        },
        'allow':function(e){
            console.log("click");
            var dialogId = $(e.currentTarget).attr('data-dialog-id');
            this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {
            });
            this.close();
        },
        'deny':function(e){
            console.log("click");
            var dialogId = $(e.currentTarget).attr('data-dialog-id');
            this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {
            });
            this.close();
        }
    });
})();