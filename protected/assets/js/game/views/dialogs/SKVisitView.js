/*global SKVisitView:true, SKWindowView, Backbone, _, SKApp, SKConfig, SKDialogWindow*/

var SKVisitView;

define([
        'game/views/SKWindowView',

        'text!game/jst/visit/visit_door.jst'
    ], function (
        SKWindowView,

        visitDoorTpl
    ) {
    "use strict";
    /**
     * @class SKVisitView
     * @extends {SKWindowView}
     * @type {SKWindowView}
     */
    SKVisitView = SKWindowView.extend(
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
                el.html(_.template(visitDoorTpl, {
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
                var dialogId = $(e.currentTarget).attr('data-dialog-id');
                this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {
                });
                this.options.model_instance.close();
            }
        });
    return SKVisitView;
});