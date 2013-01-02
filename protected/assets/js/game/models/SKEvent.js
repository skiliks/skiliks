/*global Backbone*/
(function () {
    "use strict";
    var event_types = {
        'M': 'mail',
        'MS': 'mail',
        'D': 'document'
    };

    window.SKEvent = Backbone.Model.extend({
        'getTypeSlug': function () {
            if (this.get('type') === undefined) {
                throw 'Unknown event type: ' + this.get('type');
            } else  if (this.get('type') === 1) {
                if (this.get('data')[0].dialog_subtype === '1') {
                    return 'phone';
                }
            }
            return event_types[this.get('type')];
        }
    });
})();