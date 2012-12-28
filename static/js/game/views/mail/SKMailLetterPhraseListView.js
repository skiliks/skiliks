/*global Backbone, _, SKMailLetterBaseView */
$(function () {
    "use strict";
    /**
     * List of user's phrases added to letter
     * @type {*}
     */
    window.SKMailLetterPhraseListView = SKMailLetterBaseView.extend({
        'initialize': function () {
            this.render();
        },
        'render': function () {
            var new_letter = $('<div class="message-container"><ul id="mailEmulatorNewLetterText" class="ui-sortable"></ul></div>');
            this.$el.html(new_letter);
            this.$el.mCustomScrollbar();
        }
    });
});