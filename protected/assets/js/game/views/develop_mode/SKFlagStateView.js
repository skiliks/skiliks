/*global Backbone, _, $, define, SKApp */

var SKFlagStateView;

define([],
    function () {
    "use strict";

    /**
    * @class SKFlagStateView
    * @augments Backbone.View
    */
    SKFlagStateView = Backbone.View.extend({
        /**
         * Constructor
         * @method initialize
         */
        initialize: function () {},

        /**
         * Базовый HTML DOM контейнер, должен быть уникальным
         * @var jQuery el
         */
        'el': '.form-flags',

        /**
         * События DOM на которые должна реагировать данная view
         * @var Array events
         */
        'events':{
            'click a': 'doSwitchFlag'
        },

        /**
         * Переключает флаг, с 0 на 1, с 1 на 0.
         * @param Event event, onClickEvent
         */
        doSwitchFlag: function(event) {
            try {
                event.preventDefault(event);
                event.stopPropagation(event);

                // to prevent doubled requests
                if (SKApp.simulation.isFlagsUpdated) {
                    return;
                }

                SKApp.simulation.isFlagsUpdated = true;

                var me = this;
                var flagName = $(event.currentTarget).attr('data-flag-code');
                var flagValue = $(event.currentTarget).attr('data-flag-value');
                if (1 == flagValue) {
                    flagValue = 0;
                } else {
                    flagValue = 1;
                }

                SKApp.server.api(
                    'events/switchFlag',
                    {
                        flagName: $(event.currentTarget).attr('data-flag-code')
                    },
                    function (response) {
                        if (response.result) {
                            $('body form.trigger-event').append(
                                '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Флаг '
                                + flagName
                                + ' переключён в "'
                                + flagValue
                                + '"!</div>'
                            );

                            me.updateValues(response.flags);
                            //window.scrollTo(0, 0);
                        } else {
                            $('body form.trigger-event').append(
                                '<div class="alert alert-error "><button type="button" class="close" data-dismiss="alert">&times;</button>Флаг '
                                + flagName
                                + ' не переключён в "'
                                + flagValue
                                + '"!</div>'
                            );
                        }
                        $('.debug-panel form.trigger-event .alert').css('position', 'static');
                        $('body form.trigger-event .alert').fadeOut(4000);
                    });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Обновляет таблицу значений флагов в дев режиме
         *
         * @param Array.<String> flagsState
         */
        updateValues: function(flagsState) {
            try {
                SKApp.simulation.isFlagsUpdated = false;

                $('.form-flags table').remove();
                // clean old data AND init base structure,
                // may be will be better to put this code to template, but it so short and use once! :)
                $('.form-flags fieldset').append($('<table class="table table-bordered"><thead><tr></tr></thead><tbody><tr></tr><tr></tr></tbody></table>'));

                var j = 0;
                var n = 0;
                for (var i in flagsState) {

                    if (9 < n) {
                        j++;
                        n = 0;
                        $('.form-flags fieldset').append($('<table class="table table-bordered"><thead><tr></tr></thead><tbody><tr></tr><tr></tr></tbody></table>'));
                    }


                    var el1 = $('<a/>', {text: i}); // create link
                    el1.undelegate();

                    el1.attr('title', flagsState[i].name); // add title
                    el1.attr('data-flag-code',  i); // add title
                    el1.attr('data-flag-value', flagsState[i].value); // add title
                    el1.attr('data-flag-time',  '00:00:00'); // add title
                    // generate th
                    var el_th = $('<th/>', {});
                    el_th.append(el1);
                    $('.form-flags table:eq('+j+') thead tr').append(el_th);

                    var el_td = $('<td/>', {text: flagsState[i].value});
                    el_td.addClass(i + '-value');
                    $('.form-flags table:eq('+j+') tbody tr:eq(0)').append(el_td);

                    // flag queue time

                    var flagTime = '';
                    if (null !== flagsState[i].time) {
                        flagTime = flagsState[i].time;
                    }

                    var el_td2 = $('<td/>', {text: flagTime});
                    el_td2.addClass(i + '-time');
                    $('.form-flags table:eq('+j+') tbody tr:eq(1)').append(el_td2);

                    n++;
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Используется для заполнения ячеек таблицы значений флагов, в дев режиме
         * Для флагов, которые должны будут переключиться по времени, возвращает текущее значение и время,
         * когда они будут переключены,
         * для остальных флагов - просто текущее значение флага
         *
         * @param Array.<String> data
         * @returns {String}, '0'/'1' or '0 18:00'/'1 18:00'
         */
        getFlagData: function(data){
            try {
                if(data.time === null){
                    return data.value;
                }else{
                    return data.value+" "+data.time;
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });


    return SKFlagStateView;
});
