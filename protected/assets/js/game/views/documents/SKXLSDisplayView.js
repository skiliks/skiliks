/*global SocialCalc, _, SKApp
 */

define([
    "text!game/jst/document/document_xls_template.jst",
    "text!game/jst/document/budget.jst",
    "game/views/SKWindowView",
    "game/views/documents/spreadsheets/SKSheetView"
],function (
    document_xls_template,
    budget,
    SKWindowView,
    SKSheetView
) {
    "use strict";

    /**
     * @class SKXLSDisplayView
     * @augments Backbone.View
     */
    var SKXLSDisplayView = SKWindowView.extend({

        title:'Мои документы',
        addClass: 'document-window',

        dimensions: {},
        events: _.defaults({
            'click .sheet-tabs li': 'doSelectTab'
        }, SKWindowView.prototype.events),

        /*
        * Constructor
        * @method initialize
        */
        initialize: function () {
            var me = this;
            var doc = me.options.model_instance.get('document');
            doc.get();
            this.title = doc.get('name') || 'Без названия';

            window.SKWindowView.prototype.initialize.call(this);
            return true;
        },


        /**
         * @method
         * @param el
         */
        renderContent:function (el) {
            var me = this;
            var doc = this.options.model_instance.get('document');
            var spreadsheet;
            el.html( _.template(document_xls_template, {}) );

            SocialCalc.Constants.defaultImagePrefix = SKApp.get('assetsUrl') + '/js/socialcalc/images/sc-';
            var sheetViews = [];
            var i = 0;
            doc.get('sheets').each(function (sheet) {
                setTimeout(function () {
                    var sheetView = new SKSheetView({
                        'el': me.$('.table-container'),
                        'sheet': sheet
                    });
                    sheetView.render();
                    sheetViews.push(sheetView);

                    var sheetTab = $('<li></li>');
                    sheetTab.attr('data-sheet-name', sheet.get('name'));
                    sheetTab.text(sheet.get('name'));
                    sheetTab.appendTo(this.$('.sheet-tabs'));
                    doc.get('sheets').at(0).activate();
                    me.undelegateEvents();
                    me.delegateEvents();
                }, i+=2000);
            });



        },

        doSelectTab: function doSelectTab () {
            var doc = this.options.model_instance.get('document');
            doc.get('sheets').where({'name': $(event.currentTarget).attr('data-sheet-name')})[0].activate();
        }

    });

    return SKXLSDisplayView;
});