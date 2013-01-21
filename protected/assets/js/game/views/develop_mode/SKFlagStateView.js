/*global Backbone, _ */
(function () {
    "use strict";
    window.SKFlagStateView = Backbone.View.extend({
        initialize: function () {},
        
        updateValues: function(flagsState) {
            $('.form-flags table').remove();
            // clean old data AND init base structure, 
            // may be will be better to put this code to template, but it so short and use once! :)
            $('.form-flags fieldset').append($('<table class="table table-bordered"><thead><tr></tr></thead><tbody><tr></tr></tbody></table>'));
            
            var j = 0;
            var n = 0;
            for (var i in flagsState) {
                
                if (9 < n) {
                    j++;
                    n = 0;
                    $('.form-flags fieldset').append($('<table class="table table-bordered"><thead><tr></tr></thead><tbody><tr></tr></tbody></table>'));
                }
                
                var el = $('<th/>', {text: i});
                $('.form-flags table:eq('+j+') thead tr').append(el);
                el = $('<td/>', {text: flagsState[i]});
                $('.form-flags table:eq('+j+') tbody tr').append(el);
                
                n++;
            }
        }
    });
})();