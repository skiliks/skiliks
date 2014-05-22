/*
 *
 */
var SKDocumentsManager;
(function() {
    "use strict";
    /**
     * @class SKMailTask
     * @augments Backbone.Model
     */
    SKDocumentsManager = Backbone.Model.extend({

        /**
         * Empty construtor
         */
        initialize: function() { },

        /**
         * Обёртка для isPasteOperationAllowedInExcel
         * - отображает окно-предупреждение
         */
        checkIsPasteOperationAllowedInExcel: function() {
            var me = this;
            if (false === me.isPasteOperationAllowedInExcel()) {
                me.warning = new SKDialogView({
                    'message':'Обнаружены циклические ссылки между листами в формулах. <br/> Вставка данных отменена.',
                    'buttons':[
                        {
                            'value':'Ок',
                            'onclick':function () {
                                delete SKApp.simulation.documentsManager.warning;
                            }
                        }
                    ]
                });
            }
        },

        /**
         * Вставлять нельзя, если в копируемом текстк просутствует название текущего листа ексель
         * @returns {boolean}
         */
        isPasteOperationAllowedInExcel: function() {
            var pastedPiece = SocialCalc.Clipboard.clipboard;
            var currentSheetName = $('.sim-window-id-'
                + SKApp.simulation.window_set.getActiveWindow().window_uid).find(".sheet-tabs .active").text();

            if (pastedPiece.indexOf(currentSheetName+'!') > -1) {
                return false;
            } else {
                return true;
            }
        }
    });
})();
