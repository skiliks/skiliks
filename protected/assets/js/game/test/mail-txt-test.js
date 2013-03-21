/*global buster, sinon, describe, before, after, require */
buster.spec.expose();

var inbox = {"result":1, "messages":{
    "996241":{
        "id":"996241",
        "subject":"По ценовой политике",
        "text":"Наша ценовая политика — говно",
        "template":"MY1",
        "sentAt":"03.10.2012 10:32",
        "sender":"\u041a\u0440\u0443\u0442\u044c\u043a\u043e \u041c. <krutko.ma@skiliks.com>",
        "receiver":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>",
        "copy":"",
        "readed":"0",
        "attachments":1,
        "subjectSort":"\u043f\u043e \u0446\u0435\u043d\u043e\u0432\u043e\u0439 \u043f\u043e\u043b\u0438\u0442\u0438\u043a\u0435",
        "attachmentName":"\u0426\u0435\u043d\u043e\u0432\u0430\u044f \u043f\u043e\u043b\u0438\u0442\u0438\u043a\u0430.xlsx",
        "attachmentId":"37837",
        "attachmentFileId": "236255"
    }, "996242":{
        "id":"996242",
        "subject":"\u0424\u043e\u0440\u043c\u0430 \u043e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u0438 \u0434\u043b\u044f \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0430",
        "text":"\u0414\u043e\u0431\u0440\u043e\u0433\u043e \u0432\u0430\u043c \u0432\u0440\u0435\u043c\u0435\u043d\u0438 \u0441\u0443\u0442\u043e\u043a! \n\u041f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0435\u043d\u043d\u044b\u0439 \u043e\u0442\u0434\u0435\u043b \u043f\u0440\u043e\u0441\u0438\u0442 \u0432\u0430\u0441 \u0440\u0430\u0441\u0441\u043c\u043e\u0442\u0440\u0435\u0442\u044c \u0432\u043e\u0437\u043c\u043e\u0436\u043d\u043e\u0441\u0442\u044c \u0432\u043d\u0435\u0441\u0435\u043d\u0438\u044f \u0438\u0437\u043c\u0435\u043d\u0435\u043d\u0438\u0439 \u0432 \u0442\u0435\u043a\u0443\u0449\u0443\u044e \u0444\u043e\u0440\u043c\u0443 \u043e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u0438 \u043f\u043e \u043e\u0431\u044a\u0435\u043c\u0430\u043c \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0430 \u0438 \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0435\u043d\u043d\u044b\u043c \u043c\u043e\u0449\u043d\u043e\u0441\u0442\u044f\u043c. \u041d\u0430 \u0442\u0435\u043a\u0443\u0449\u0438\u0439 \u043c\u043e\u043c\u0435\u043d\u0442 \u0432 \u043e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u0438 \u043d\u0435 \u0434\u043e\u0441\u0442\u0430\u0435\u0442 \u0440\u0430\u0437\u0432\u0435\u0440\u043d\u0443\u0442\u043e\u0433\u043e \u0430\u043d\u0430\u043b\u0438\u0437\u0430 \u043e\u0441\u0442\u0430\u0442\u043a\u043e\u0432 \u043d\u0430 \u0432\u0441\u0435\u0445 \u043d\u0430\u0448\u0438\u0445 \u0441\u043a\u043b\u0430\u0434\u0430\u0445, \u0432\u043a\u043b\u044e\u0447\u0430\u044f \u0442\u043e\u0440\u0433\u043e\u0432\u044b\u0435. \u042d\u0442\u043e \u043f\u0440\u0438\u0432\u043e\u0434\u0438\u0442 \u043a \u0442\u043e\u043c\u0443, \u0447\u0442\u043e \u043c\u044b \u043f\u0435\u0440\u0438\u043e\u0434\u0438\u0447\u0435\u0441\u043a\u0438 \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0438\u043c \u0442\u043e\u0432\u0430\u0440, \u043a\u043e\u0442\u043e\u0440\u044b\u0439 \u0443\u0436\u0435 \u0435\u0441\u0442\u044c \u0432 \u0440\u0435\u0433\u0438\u043e\u043d\u0430\u0445. \u041b\u043e\u0433\u0438\u0441\u0442\u044b \u0433\u043e\u0432\u043e\u0440\u044f\u0442, \u0447\u0442\u043e \u0432\u043f\u043e\u043b\u043d\u0435 \u043c\u043e\u0433\u043b\u0438 \u0431\u044b \u043e\u0431\u0435\u0441\u043f\u0435\u0447\u0438\u0442\u044c \u043f\u0435\u0440\u0435\u0431\u0440\u043e\u0441\u0443 \u0442\u043e\u0432\u0430\u0440\u0430 \u0438\u0437 \u043e\u0434\u043d\u043e\u0433\u043e \u0440\u0435\u0433\u0438\u043e\u043d\u0430 \u0432 \u0434\u0440\u0443\u0433\u043e\u0439. \u0422\u0430\u043a\u0438\u043c \u043e\u0431\u0440\u0430\u0437\u043e\u043c, \u043d\u0430\u043c \u0443\u0434\u0430\u043b\u043e\u0441\u044c \u0431\u044b \u0441\u044d\u043a\u043e\u043d\u043e\u043c\u0438\u0442\u044c \u043d\u0430 \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0435\u043d\u043d\u044b\u0445 \u0438\u0437\u0434\u0435\u0440\u0436\u043a\u0430\u0445 \u0431\u0435\u0437 \u0441\u043d\u0438\u0436\u0435\u043d\u0438\u044f \u043e\u0431\u044a\u0435\u043c\u043e\u0432 \u043f\u0440\u043e\u0434\u0430\u0436. \u041f\u0440\u043e\u0448\u0443 \u0432\u0430\u0441 \u043e\u0446\u0435\u043d\u0438\u0442\u044c \u0441\u0440\u043e\u043a\u0438, \u0442\u0440\u0443\u0434\u043e\u0435\u043c\u043a\u043e\u0441\u0442\u044c \u0438 \u043d\u0430\u043b\u0438\u0447\u0438\u0435 \u0432\u043e\u0437\u043c\u043e\u0436\u043d\u043e\u0441\u0442\u0438 \u0434\u043e\u0431\u0430\u0432\u0438\u0442\u044c \u0432 \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0435\u043d\u043d\u044b\u0439 \u043e\u0442\u0447\u0435\u0442 \u0434\u0430\u043d\u043d\u044b\u0435 \u043f\u043e \u0441\u043a\u043b\u0430\u0434\u0441\u043a\u0438\u043c \u043e\u0441\u0442\u0430\u0442\u043a\u0430\u043c.\n\u0417\u0430\u0440\u0430\u043d\u0435\u0435 \u0431\u043b\u0430\u0433\u043e\u0434\u0430\u0440\u044e, \u0411\u043e\u0431\u0440 \u0412.,  \n\u041d\u0430\u0447. \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0435\u043d\u043d\u043e\u0433\u043e \u043e\u0442\u0434\u0435\u043b\u0430.", "template":"MY2", "sentAt":"03.10.2012 11:02", "sender":"\u0412\u0430\u0441\u0438\u043b\u0438\u0439 \u0411\u043e\u0431\u0440 <bobr.vs@skiliks.com>", "receiver":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>", "copy":"", "readed":"0", "attachments":0, "subjectSort":"\u0444\u043e\u0440\u043c\u0430 \u043e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u0438 \u0434\u043b\u044f \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0430"}, "996243":{"id":"996243", "subject":"\u041d\u043e\u0432\u0430\u044f \u0441\u0438\u0441\u0442\u0435\u043c\u0430 \u043c\u043e\u0442\u0438\u0432\u0430\u0446\u0438\u0438", "text":"\u041a\u043e\u043b\u043b\u0435\u0433\u0438, \u0434\u043e\u0431\u0440\u044b\u0439 \u0434\u0435\u043d\u044c!        \n\u041f\u043e\u0441\u043b\u0435 \u043d\u0435\u0441\u043a\u043e\u043b\u044c\u043a\u0438\u0445 \u043f\u0440\u043e\u0434\u0443\u043a\u0442\u0438\u0432\u043d\u044b\u0445 \u0441\u043e\u0432\u0435\u0449\u0430\u043d\u0438\u0439 \u0441 \u0432\u0430\u043c\u0438 (\u043e\u0442\u0434\u0435\u043b\u044c\u043d\u043e\u0435 \u0441\u043f\u0430\u0441\u0438\u0431\u043e \u0424\u0435\u0434\u043e\u0440\u043e\u0432\u0443 \u0410. \u0437\u0430 \u0440\u0435\u0433\u0443\u043b\u044f\u0440\u043d\u043e\u0435 \u0443\u0447\u0430\u0441\u0442\u0438\u0435!) \u043c\u044b \u043f\u043e\u0434\u0433\u043e\u0442\u043e\u0432\u0438\u043b\u0438 \u0438\u0442\u043e\u0433\u043e\u0432\u0443\u044e \u0432\u0435\u0440\u0441\u0438\u044e \u0441\u0438\u0441\u0442\u0435\u043c\u044b \u043c\u043e\u0442\u0438\u0432\u0430\u0446\u0438\u0438 \u0434\u043b\u044f \u043c\u0435\u043d\u0435\u0434\u0436\u0435\u0440\u043e\u0432 \u043f\u0435\u0440\u0432\u043e\u0433\u043e \u0438 \u0432\u0442\u043e\u0440\u043e\u0433\u043e \u0443\u0440\u043e\u0432\u043d\u044f. \u041c\u044b \u043f\u043b\u0430\u043d\u0438\u0440\u0443\u0435\u043c \u0432\u0432\u043e\u0434\u0438\u0442\u044c \u0435\u0435 \u0432 \u0434\u0435\u0439\u0441\u0442\u0432\u0438\u0435 \u0441 \u043d\u0430\u0447\u0430\u043b\u0430 02 \u0433\u043e\u0434\u0430 (\u0438\u043c\u0435\u043d\u043d\u043e \u043e\u043d\u0430 \u0438 \u0431\u0443\u0434\u0435\u0442 \u0437\u0430\u0431\u044e\u0434\u0436\u0435\u0442\u0438\u0440\u043e\u0432\u0430\u043d\u0430). \u0416\u0434\u0435\u043c \u0432\u0430\u0448\u0438\u0445 \u043e\u043a\u043e\u043d\u0447\u0430\u0442\u0435\u043b\u044c\u043d\u044b\u0445 \u043a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0435\u0432 \u0438 \u043a\u043e\u0440\u0440\u0435\u043a\u0442\u0438\u0440\u043e\u0432\u043e\u043a. \u0415\u0441\u043b\u0438 \u043d\u0435 \u0443\u0447\u0430\u0441\u0442\u0432\u0443\u0435\u0442\u0435 \u0432 \u043e\u0431\u0441\u0443\u0436\u0434\u0435\u043d\u0438\u0438 - \u043f\u043e\u0442\u043e\u043c \u043d\u0435 \u0441\u0435\u0442\u0443\u0439\u0442\u0435 \u043d\u0430 \u0442\u043e, \u0447\u0442\u043e \u0441\u0438\u0441\u0442\u0435\u043c\u0430 \u0431\u044b\u043b\u0430 \u043f\u0440\u0438\u043d\u044f\u0442\u0430 \u0431\u0435\u0437 \u0432\u0430\u0448\u0435\u0433\u043e \u0432\u0435\u0441\u043e\u043c\u043e\u0433\u043e \u0433\u043e\u043b\u043e\u0441\u0430! \u0411\u043b\u0430\u0433\u043e\u0434\u0430\u0440\u044e \u0437\u0430 \u043e\u043f\u0435\u0440\u0430\u0442\u0438\u0432\u043d\u043e\u0441\u0442\u044c! \n\u0421 \u043d\u0430\u0438\u043b\u0443\u0447\u0448\u0438\u043c\u0438 \u043f\u043e\u0436\u0435\u043b\u0430\u043d\u0438\u044f\u043c\u0438, \u041b\u044e\u0434\u043e\u0432\u043a\u0438\u043d\u0430 \u0421.", "template":"MY3", "sentAt":"28.09.2012 18:45", "sender":"\u041b\u044e\u0434\u043e\u0432\u043a\u0438\u043d\u0430 \u0421. <lyudovkina.sm@skiliks.com>",
        "receiver":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>",
        "copy":"",
        "readed":"0",
        "attachments":1,
        "subjectSort":"\u043d\u043e\u0432\u0430\u044f \u0441\u0438\u0441\u0442\u0435\u043c\u0430 \u043c\u043e\u0442\u0438\u0432\u0430\u0446\u0438\u0438", "attachmentName":"\u041d\u043e\u0432\u0430\u044f \u0441\u0438\u0441\u0442\u0435\u043c\u0430 \u043c\u043e\u0442\u0438\u0432\u0430\u0446\u0438\u0438.docx", "attachmentId":"37838"
    }, "996244":{
        "id":"996244",
        "subject":"\u0422\u0440\u0443\u0434\u043e\u0432\u043e\u0439 \u0434\u043e\u0433\u043e\u0432\u043e\u0440",
        "text":"\u041a\u043e\u043b\u043b\u0435\u0433\u0438! \u0412 \u0441\u0432\u044f\u0437\u0438 \u0441 \u0438\u0437\u043c\u0435\u043d\u0435\u043d\u0438\u044f\u043c\u0438 \u0442\u0440\u0443\u0434\u043e\u0432\u043e\u0433\u043e \u0437\u0430\u043a\u043e\u043d\u043e\u0434\u0430\u0442\u0435\u043b\u044c\u0441\u0442\u0432\u0430 \u044e\u0440\u0438\u0434\u0438\u0447\u0435\u0441\u043a\u0438\u0439 \u043e\u0442\u0434\u0435\u043b \u0432\u044b\u043d\u0443\u0436\u0434\u0435\u043d \u0432\u043d\u0435\u0441\u0442\u0438 \u0432 \u0442\u0440\u0443\u0434\u043e\u0432\u043e\u0439 \u0434\u043e\u0433\u043e\u0432\u043e\u0440, \u0434\u0435\u0439\u0441\u0442\u0432\u0443\u044e\u0449\u0438\u0439 \u0432 \u0434\u0430\u043d\u043d\u044b\u0439 \u043c\u043e\u043c\u0435\u043d\u0442 \u0432 \u043a\u043e\u043c\u043f\u0430\u043d\u0438\u0438 \u043a\u0430\u043a \u043e\u0441\u043d\u043e\u0432\u043d\u043e\u0439, \u0440\u044f\u0434 \u043a\u043e\u0440\u0440\u0435\u043a\u0442\u0438\u0432. \u041f\u0440\u043e\u0441\u0438\u043c \u0432\u0430\u0441 \u043e\u0437\u043d\u0430\u043a\u043e\u043c\u0438\u0442\u044c\u0441\u044f \u0441 \u043d\u0438\u043c\u0438 \u0438 \u0434\u043e\u043d\u0435\u0441\u0442\u0438 \u0434\u043e \u0441\u0432\u043e\u0438\u0445 \u0441\u043e\u0442\u0440\u0443\u0434\u043d\u0438\u043a\u043e\u0432, \u0447\u0442\u043e \u0441 01 \u044f\u043d\u0432\u0430\u0440\u044f 02 \u0433\u043e\u0434\u0430 \u0431\u0443\u0434\u0435\u0442 \u0437\u0430\u043f\u0443\u0449\u0435\u043d\u0430 \u043a\u0430\u043c\u043f\u0430\u043d\u0438\u044f \u043f\u043e \u043f\u043e\u0434\u043f\u0438\u0441\u0430\u043d\u0438\u044e \u0442\u0430\u043a\u043e\u0433\u043e \u0432\u0430\u0440\u0438\u0430\u043d\u0442\u0430 \u0442\u0440\u0443\u0434\u043e\u0432\u043e\u0433\u043e \u0434\u043e\u0433\u043e\u0432\u043e\u0440\u0430 \u0441\u043e \u0432\u0441\u0435\u043c\u0438 \u0430\u043a\u0442\u0443\u0430\u043b\u044c\u043d\u044b\u043c\u0438 \u0438 \u0432\u043d\u043e\u0432\u044c \u043f\u0440\u0438\u0448\u0435\u0434\u0448\u0438\u043c\u0438 \u0441\u043e\u0442\u0440\u0443\u0434\u043d\u0438\u043a\u0430\u043c\u0438 \u043a\u043e\u043c\u043f\u0430\u043d\u0438\u0438. \u041f\u0440\u043e\u0441\u0438\u043c \u043e\u0442\u043d\u0435\u0441\u0442\u0438\u0442\u044c \u043a \u044d\u0442\u043e\u0439 \u0440\u0430\u0431\u043e\u0442\u0435 \u0441\u043e \u0432\u0441\u0435\u0439 \u043e\u0442\u0432\u0435\u0442\u0441\u0442\u0432\u0435\u043d\u043d\u043e\u0441\u0442\u044c\u044e \u0438 \u0434\u043e\u043b\u0436\u043d\u044b\u043c \u0432\u043d\u0438\u043c\u0430\u043d\u0438\u0435\u043c. \n\u0421 \u0443\u0432\u0430\u0436\u0435\u043d\u0438\u0435\u043c, \u0410\u0434\u0432\u043e\u043a\u0430\u0442\u043e\u0432 \u042e.",
        "template":"MY4",
        "sentAt":"01.10.2012 16:30",
        "sender":"\u0410\u0434\u0432\u043e\u043a\u0430\u0442\u043e\u0432 \u042e. <advokatov.yv@skiliks.com>",
        "receiver":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>",
        "copy":"",
        "readed":"0",
        "attachments":1,
        "subjectSort":"\u0442\u0440\u0443\u0434\u043e\u0432\u043e\u0439 \u0434\u043e\u0433\u043e\u0432\u043e\u0440",
        "attachmentName":"\u0422\u0440\u0443\u0434\u043e\u0432\u043e\u0439 \u0434\u043e\u0433\u043e\u0432\u043e\u0440.docx",
        "attachmentId":"37839"
    }
}, "type":"inbox"};

define([
    "game/models/SKApplication",
    "game/models/SKSimulation",
    "game/views/mail/SKMailClientView",
    "game/models/window/SKWindow"], function (SKApplication, SKSimulation, SKMailClientView, SKWindow) {

    spec = describe('mail client', function (run) {
        "use strict";
        /**
         * @type {SKMailClientView} SKMailClientView
         */
        run(function () {
            _.templateSettings.interpolate = /<@=(.+?)@>/g;
            _.templateSettings.evaluate = /<@(.+?)@>/g;
            var server;
            before(function () {
                server = sinon.fakeServer.create();

                server.respondWith("POST", "/index.php/myDocuments/getList",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:1})]);

                server.respondWith("POST", "/index.php/mail/getReceivers",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({
                            result:1,
                            data:{
                                1: 'bob <bob@skiliks.com>',
                                2: 'john <john@skiliks.com>'
                            }
                        })]);

                server.respondWith("POST", "/index.php/mail/getMessages",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify(inbox)]);

                server.respondWith("POST", "/index.php/simulation/start",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:1})]);

                server.respondWith("POST", "/index.php/mail/MarkRead",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:1})]);

                server.respondWith("POST", "/index.php/events/getState",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:0})]);

                server.respondWith("POST", "/index.php/todo/get",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:1})]);

                server.respondWith("POST", "/index.php/dayPlan/get",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:1})]);

                server.respondWith("POST", "/index.php/mail/getThemes",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({
                            "result": 1,
                            "data": {
                                "1": "subject 1",
                                "2": "subject 2"
                            },
                            "characterThemeId": 101
                        })]);

                server.respondWith("POST", "/index.php/mail/getPhrases",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({
                                "result":  1,
                                "data":    [],
                                "addData": [],
                                "message": "Dummy message."}
                        )]);

                server.respondWith("POST", "/index.php/mail/sendMessage",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({
                            "result": 1,
                            "mailId": "1245"
                        })]);

                //clock = sinon.useFakeTimers();
                //this.timeout = 10000;
                window.SKApp = new SKApplication({'start':'9:00', "skiliksSpeedFactor":8 });
                this.timeout = 1000;
            });

            after(function () {
                server.restore();
            });

            it("can create and send new letter (text constructor)", function () {
                var simulation = SKApp.simulation;
                simulation.start();
                var mail_window = new SKWindow({name:'mailEmulator', subname:'mailMain'});
                mail_window.open();

                var mailView = new SKMailClientView({model_instance:mail_window});

                mailView.render();
                server.respond();

                mailView.$el.find('.NEW_EMAIL').click();
                $('body').append(mailView.$el);

                server.respond();

                expect(mailView.$('.SEND_EMAIL').length, 1);

                mailView.$el.find('ul.ui-autocomplete:eq(0) a[data-character-id=1]').click();

                // check recipients
                expect(SKApp.simulation.mailClient.defaultRecipients.length).toBe(2);

                server.respond();

                mailView.mailClient.reloadSubjects([1]);

                $('#MailClient_RecipientsList').append('<li class="tagItem">bob</li>');

                // check subjects
                expect(SKApp.simulation.mailClient.availableSubjects.length).toBe(2);

                //mailView.$el.find('#MailClient_NewLetterSubject').focus();
                //mailView.$el.find('#MailClient_NewLetterSubject option:eq(1)').attr("selected","selected");
                mailView.$el.find("#MailClient_NewLetterSubject").ddslick('select', {'index': 1 });

                mailView.doUpdateMailPhrasesList();

                server.respond();

                // check is message has been displayed
                expect(mailView.$el.find('#mailEmulatorNewLetterText').text())
                    .toBe('Dummy message.');

                // check phrases
                expect(SKApp.simulation.mailClient.availablePhrases.length).toBe(0);

                // test TXT constructor
                expect(mailView.$el.find('#mailEmulatorNewLetterText').text()).toBe('Dummy message.');

                mailView.$el.find('.SEND_EMAIL').click();

                // one server.respond response on all request from front on requests query
                server.respond();

                // check is email send
                expect(server.requests[server.requests.length-2].url).toBe('/index.php/mail/sendMessage');

                console.log('Email has been send!');
            });
        });
    });
});