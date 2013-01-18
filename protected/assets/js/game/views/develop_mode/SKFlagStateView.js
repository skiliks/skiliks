/*global Backbone, _ */
(function () {
    "use strict";
    window.SKFlagStateView = Backbone.View.extend({
        initialize: function () {},
        
        updateValues: function(flagsState) {
            // clean old data AND init base structure, 
            // may be will be better to put this code to template, but it so short and use once! :)
            $('.form-flags table').html('<thead><tr></tr></thead><tbody><tr></tr></tbody>');
            
            for (var i in flagsState) {
                var el = $('<th/>', {text: i});
                $('.form-flags table thead tr').append(el);
                el = $('<td/>', {text: flagsState[i]});
                $('.form-flags table tbody tr').append(el);
            }
        }
    });
})();