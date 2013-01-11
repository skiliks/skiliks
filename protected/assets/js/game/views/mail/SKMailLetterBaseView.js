/*global Backbone, _ */
/*(function () {
    "use strict";
    window.SKMailLetterBaseView = Backbone.View.extend({
        'setQuote': function (message) {
            var me = this;
            var quote_div = $('<div class="message-quote"></div>');
            message.split('\n').forEach(function(row) {
                quote_div.append(document.createTextNode('> ' + row));
                quote_div.append($('<br />'));
            });
            me.$('.message-container').append(quote_div);
            this.$el.mCustomScrollbar("update");
        }
    });
})();*/