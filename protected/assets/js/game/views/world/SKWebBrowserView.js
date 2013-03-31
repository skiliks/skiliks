/*global SKWindow, _, SKWindowView, SKConfig, SKApp, SKPhoneContactsCollection, SKDocumentsWindow
 */

/**
 * @class SKDocumentsListView
 * @augments window.SKWindowView
 */
var SKWebBrowserView;
define([
    "text!game/jst/world/web_browser.jst",

    "game/views/SKWindowView"
], function (
    web_browser_template
    ) {
    "use strict";


    /**
     *
     * @type {*}
     */
    SKWebBrowserView = SKWindowView.extend(
        /** @lends SKDocumentsListView.prototype */
        {
            title: 'IE 5+',

            addClass: 'web-browser',

            events: {
                'click li':         'doUpload',
                'click .win-close': 'doClose'
            },

            dimensions: {
                width: 1000,
                height: 400
            },

            /**
             * @method
             * @param {jQuery} el
             */
            renderContent: function () {
                this.$el.find('.sim-window-content').html(_.template(web_browser_template));
            },

            doUpload: function(event) {
                this.$el.find('iframe').attr('src', $(event.target).attr('link'));
            },

            doClose: function() {
                SKApp.simulation.window_set.toggle('browser', 'browserMain');
            }
        });
});