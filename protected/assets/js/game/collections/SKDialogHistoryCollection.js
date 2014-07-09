/*global SKApp*/

define([
    "game/models/SKDialogHistory"
], function (SKDialogHistory) {
    "use strict";

    var SKDialogHistoryCollection = Backbone.Collection.extend({
        model: SKDialogHistory
    });

    return SKDialogHistoryCollection;
});
