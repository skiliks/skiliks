/*global Backbone, _ */

var SKFlagStateView;

define([],
    function () {
    "use strict";

    SKFlagStateView = Backbone.View.extend({
        initialize: function () {},

        'el': '.form-flags',

        'events':{
            'click a': 'doSwitchFlag'
        },

        doSwitchFlag: function(e) {
            e.preventDefault(e);
            e.stopPropagation(e);

            // to prevent doubled requests
            if (SKApp.user.simulation.isFlagsUpdated) {
                return;
            }

            SKApp.user.simulation.isFlagsUpdated = true;

            var me = this;
            var flagName = $(e.currentTarget).attr('data-flag-code');
            var flagValue = $(e.currentTarget).attr('data-flag-value');
            if (1 == flagValue) {
                flagValue = 0;
            } else {
                flagValue = 1;
            }

            SKApp.server.api(
                'events/switchFlag',
                {
                    flagName: $(e.currentTarget).attr('data-flag-code')
                },
                function (response) {
                    if (response.result) {
                        $('body form.trigger-event').append('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Флаг ' + flagName + ' переключён в "' + flagValue + '"!</div>');
                        me.updateValues(response.flags);
                        window.scrollTo(0, 0);
                    } else {
                        $('body form.trigger-event').append('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Флаг ' + flagName + ' не переключён в "' + flagValue + '"!</div>');
                    }
                    $('body form.trigger-event .alert').fadeOut(4000)
                });
        },

        updateValues: function(flagsState) {
            SKApp.user.simulation.isFlagsUpdated = false;

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


                var el1 = $('<a/>', {text: i}); // create link
                el1.undelegate();

                el1.attr('title', flagsState[i].name); // add title
                el1.attr('data-flag-code',  i); // add title
                el1.attr('data-flag-value', flagsState[i].value); // add title

                // generate th
                var el = $('<th/>', {});
                el.append(el1);
                $('.form-flags table:eq('+j+') thead tr').append(el);

                el = $('<td/>', {text: flagsState[i].value});
                $('.form-flags table:eq('+j+') tbody tr').append(el);

                n++;
            }
        }
    });

    return SKFlagStateView;
});
