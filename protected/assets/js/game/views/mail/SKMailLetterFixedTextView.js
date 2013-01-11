/*global Backbone, _, SKMailLetterBaseView */
/*(function () {
    "use strict";
    window.SKMailLetterFixedTextView = SKMailLetterBaseView.extend({
        'initialize': function () {
            this.render();
        },
        'render': function () {
            var mail_text = this.options.message;
            var template = _.template( $("#mail_fixed_text_template").html(), {'mail_text': mail_text} );
            this.$el.html(template);
        }
    });
})();*/