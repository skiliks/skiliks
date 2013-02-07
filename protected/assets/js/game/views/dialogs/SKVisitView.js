/*global SKVisitView:true, SKWindowView, Backbone, _, SKApp, SKConfig, SKDialogWindow*/
(function () {
    "use strict";
    /**
     * @class
     * @extends {SKWindowView}
     * @type {SKWindowView}
     */
    window.SKVisitView = SKWindowView.extend(
        /** @lends SKVisitView.prototype */
        {
            isDisplayCloseWindowsButton: false,
            
            'events':_.defaults({
                "click .visitor-allow":'allow',
                "click .visitor-deny":'deny'
            }, SKWindowView.prototype.events),
            
            'initialize':function () {
                var me = this;
                SKWindowView.prototype.initialize.call(this);
            },
            
            'renderWindow':function (el) {
                var event = this.options.model_instance.get('sim_event');
                el.html(_.template($('#visit_door').html(), {
                    'visit' :                     event.get('data'),
                    isDisplayCloseWindowsButton : this.isDisplayCloseWindowsButton
                }));

            },
            
            'allow':function (e) {
                var dialogId = $(e.currentTarget).attr('data-dialog-id');
                this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {});
                this.options.model_instance.close();
            },
            
            'deny':function (e) {
                //console.log("click");
                var dialogId = $(e.currentTarget).attr('data-dialog-id');
                this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {
                });
                this.options.model_instance.close();
            }
        });
})();