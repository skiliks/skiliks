/*global Backbone*/
(function () {
    "use strict";
    var event_types = {
        'M':'mail',
        'MS':'mail',
        'D':'document',
        2:'event'
    };

    window.SKEvent = Backbone.Model.extend({
        'initialize':function () {
            this.completed = false;
        },
        'getTypeSlug':function () {
            if (this.get('type') === 1) {
                if (this.get('data')[0].dialog_subtype === '1') {
                    return 'phone';
                } else if (this.get('data')[0].dialog_subtype === '2') {
                    return 'immediate-phone';
                } else if (this.get('data')[0].dialog_subtype === '4') {
                    return 'immediate-visit';
                } else if (this.get('data')[0].dialog_subtype === '5') {
                    return 'visit';
                } else {
                    throw 'Incorrect subtype ' + this.get('data')[0].dialog_subtype;
                }
            } else if (event_types[this.get('type')] === undefined) {
                throw 'Unknown event type: ' + this.get('type');
            }
            return event_types[this.get('type')];
        },
        'complete':function () {
            if (this.completed === true) {
                throw 'This event is already completed';
            }
            this.completed = true;
        }
    });
})();