/*global buster, sinon, describe, before, after, require, spec, _, it, expect, SKApp, SKApplication,
$, assert, console */

buster.spec.expose();


define([
    "game/models/SKApplication",
    "game/models/SKSimulation",
    "game/views/mail/SKMailClientView",
    "game/collections/SKCharacterCollection",
    "game/models/window/SKWindow"], function (SKApplication, SKSimulation, SKMailClientView, SKCharacterCollection, SKWindow) {
    "use strict";
    buster.spec = describe('mail client', function (run) {
        var inbox = {"result": 1, "messages": {
            "996241": {
                "id": "996241",
                "subject": "По ценовой политике",
                "text": "Наша ценовая политика — говно",
                "template": "MY1",
                "sentAt": "03.10.2012 10:32",
                "sender": 'bob <bob@skiliks.com>',
                "receiver": "me <me@skiliks.com>",
                "copy": "",
                "readed": "0",
                "attachments": 1,
                "subjectSort": "наша ценовая политика — говно",
                "attachmentName": "говно.xlsx",
                "attachmentId": "37837",
                "attachmentFileId": "236255"
            }, "996242": {
                "id": "996242",
                "subject": "\u0424\u043e\u0440\u043c\u0430 \u043e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u0438 \u0434\u043b\u044f \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0430",
                "text": "\u0414\u043e\u0431\u0440\u043e\u0433\u043e \u0432\u0430\u043c \u0432\u0440\u0435\u043c\u0435\u043d\u0438 \u0441\u0443\u0442\u043e\u043a! \n\u041f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0435\u043d\u043d\u044b\u0439 \u043e\u0442\u0434\u0435\u043b \u043f\u0440\u043e\u0441\u0438\u0442 \u0432\u0430\u0441 \u0440\u0430\u0441\u0441\u043c\u043e\u0442\u0440\u0435\u0442\u044c \u0432\u043e\u0437\u043c\u043e\u0436\u043d\u043e\u0441\u0442\u044c \u0432\u043d\u0435\u0441\u0435\u043d\u0438\u044f \u0438\u0437\u043c\u0435\u043d\u0435\u043d\u0438\u0439 \u0432 \u0442\u0435\u043a\u0443\u0449\u0443\u044e \u0444\u043e\u0440\u043c\u0443 \u043e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u0438 \u043f\u043e \u043e\u0431\u044a\u0435\u043c\u0430\u043c \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0430 \u0438 \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0435\u043d\u043d\u044b\u043c \u043c\u043e\u0449\u043d\u043e\u0441\u0442\u044f\u043c. \u041d\u0430 \u0442\u0435\u043a\u0443\u0449\u0438\u0439 \u043c\u043e\u043c\u0435\u043d\u0442 \u0432 \u043e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u0438 \u043d\u0435 \u0434\u043e\u0441\u0442\u0430\u0435\u0442 \u0440\u0430\u0437\u0432\u0435\u0440\u043d\u0443\u0442\u043e\u0433\u043e \u0430\u043d\u0430\u043b\u0438\u0437\u0430 \u043e\u0441\u0442\u0430\u0442\u043a\u043e\u0432 \u043d\u0430 \u0432\u0441\u0435\u0445 \u043d\u0430\u0448\u0438\u0445 \u0441\u043a\u043b\u0430\u0434\u0430\u0445, \u0432\u043a\u043b\u044e\u0447\u0430\u044f \u0442\u043e\u0440\u0433\u043e\u0432\u044b\u0435. \u042d\u0442\u043e \u043f\u0440\u0438\u0432\u043e\u0434\u0438\u0442 \u043a \u0442\u043e\u043c\u0443, \u0447\u0442\u043e \u043c\u044b \u043f\u0435\u0440\u0438\u043e\u0434\u0438\u0447\u0435\u0441\u043a\u0438 \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0438\u043c \u0442\u043e\u0432\u0430\u0440, \u043a\u043e\u0442\u043e\u0440\u044b\u0439 \u0443\u0436\u0435 \u0435\u0441\u0442\u044c \u0432 \u0440\u0435\u0433\u0438\u043e\u043d\u0430\u0445. \u041b\u043e\u0433\u0438\u0441\u0442\u044b \u0433\u043e\u0432\u043e\u0440\u044f\u0442, \u0447\u0442\u043e \u0432\u043f\u043e\u043b\u043d\u0435 \u043c\u043e\u0433\u043b\u0438 \u0431\u044b \u043e\u0431\u0435\u0441\u043f\u0435\u0447\u0438\u0442\u044c \u043f\u0435\u0440\u0435\u0431\u0440\u043e\u0441\u0443 \u0442\u043e\u0432\u0430\u0440\u0430 \u0438\u0437 \u043e\u0434\u043d\u043e\u0433\u043e \u0440\u0435\u0433\u0438\u043e\u043d\u0430 \u0432 \u0434\u0440\u0443\u0433\u043e\u0439. \u0422\u0430\u043a\u0438\u043c \u043e\u0431\u0440\u0430\u0437\u043e\u043c, \u043d\u0430\u043c \u0443\u0434\u0430\u043b\u043e\u0441\u044c \u0431\u044b \u0441\u044d\u043a\u043e\u043d\u043e\u043c\u0438\u0442\u044c \u043d\u0430 \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0435\u043d\u043d\u044b\u0445 \u0438\u0437\u0434\u0435\u0440\u0436\u043a\u0430\u0445 \u0431\u0435\u0437 \u0441\u043d\u0438\u0436\u0435\u043d\u0438\u044f \u043e\u0431\u044a\u0435\u043c\u043e\u0432 \u043f\u0440\u043e\u0434\u0430\u0436. \u041f\u0440\u043e\u0448\u0443 \u0432\u0430\u0441 \u043e\u0446\u0435\u043d\u0438\u0442\u044c \u0441\u0440\u043e\u043a\u0438, \u0442\u0440\u0443\u0434\u043e\u0435\u043c\u043a\u043e\u0441\u0442\u044c \u0438 \u043d\u0430\u043b\u0438\u0447\u0438\u0435 \u0432\u043e\u0437\u043c\u043e\u0436\u043d\u043e\u0441\u0442\u0438 \u0434\u043e\u0431\u0430\u0432\u0438\u0442\u044c \u0432 \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0435\u043d\u043d\u044b\u0439 \u043e\u0442\u0447\u0435\u0442 \u0434\u0430\u043d\u043d\u044b\u0435 \u043f\u043e \u0441\u043a\u043b\u0430\u0434\u0441\u043a\u0438\u043c \u043e\u0441\u0442\u0430\u0442\u043a\u0430\u043c.\n\u0417\u0430\u0440\u0430\u043d\u0435\u0435 \u0431\u043b\u0430\u0433\u043e\u0434\u0430\u0440\u044e, \u0411\u043e\u0431\u0440 \u0412.,  \n\u041d\u0430\u0447. \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0435\u043d\u043d\u043e\u0433\u043e \u043e\u0442\u0434\u0435\u043b\u0430.", "template": "MY2", "sentAt": "03.10.2012 11:02", "sender": "\u0412\u0430\u0441\u0438\u043b\u0438\u0439 \u0411\u043e\u0431\u0440 <bobr.vs@skiliks.com>", "receiver": "\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>", "copy": "", "readed": "0", "attachments": 0, "subjectSort": "\u0444\u043e\u0440\u043c\u0430 \u043e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u0438 \u0434\u043b\u044f \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0430"}, "996243": {"id": "996243", "subject": "\u041d\u043e\u0432\u0430\u044f \u0441\u0438\u0441\u0442\u0435\u043c\u0430 \u043c\u043e\u0442\u0438\u0432\u0430\u0446\u0438\u0438", "text": "\u041a\u043e\u043b\u043b\u0435\u0433\u0438, \u0434\u043e\u0431\u0440\u044b\u0439 \u0434\u0435\u043d\u044c!        \n\u041f\u043e\u0441\u043b\u0435 \u043d\u0435\u0441\u043a\u043e\u043b\u044c\u043a\u0438\u0445 \u043f\u0440\u043e\u0434\u0443\u043a\u0442\u0438\u0432\u043d\u044b\u0445 \u0441\u043e\u0432\u0435\u0449\u0430\u043d\u0438\u0439 \u0441 \u0432\u0430\u043c\u0438 (\u043e\u0442\u0434\u0435\u043b\u044c\u043d\u043e\u0435 \u0441\u043f\u0430\u0441\u0438\u0431\u043e \u0424\u0435\u0434\u043e\u0440\u043e\u0432\u0443 \u0410. \u0437\u0430 \u0440\u0435\u0433\u0443\u043b\u044f\u0440\u043d\u043e\u0435 \u0443\u0447\u0430\u0441\u0442\u0438\u0435!) \u043c\u044b \u043f\u043e\u0434\u0433\u043e\u0442\u043e\u0432\u0438\u043b\u0438 \u0438\u0442\u043e\u0433\u043e\u0432\u0443\u044e \u0432\u0435\u0440\u0441\u0438\u044e \u0441\u0438\u0441\u0442\u0435\u043c\u044b \u043c\u043e\u0442\u0438\u0432\u0430\u0446\u0438\u0438 \u0434\u043b\u044f \u043c\u0435\u043d\u0435\u0434\u0436\u0435\u0440\u043e\u0432 \u043f\u0435\u0440\u0432\u043e\u0433\u043e \u0438 \u0432\u0442\u043e\u0440\u043e\u0433\u043e \u0443\u0440\u043e\u0432\u043d\u044f. \u041c\u044b \u043f\u043b\u0430\u043d\u0438\u0440\u0443\u0435\u043c \u0432\u0432\u043e\u0434\u0438\u0442\u044c \u0435\u0435 \u0432 \u0434\u0435\u0439\u0441\u0442\u0432\u0438\u0435 \u0441 \u043d\u0430\u0447\u0430\u043b\u0430 02 \u0433\u043e\u0434\u0430 (\u0438\u043c\u0435\u043d\u043d\u043e \u043e\u043d\u0430 \u0438 \u0431\u0443\u0434\u0435\u0442 \u0437\u0430\u0431\u044e\u0434\u0436\u0435\u0442\u0438\u0440\u043e\u0432\u0430\u043d\u0430). \u0416\u0434\u0435\u043c \u0432\u0430\u0448\u0438\u0445 \u043e\u043a\u043e\u043d\u0447\u0430\u0442\u0435\u043b\u044c\u043d\u044b\u0445 \u043a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0435\u0432 \u0438 \u043a\u043e\u0440\u0440\u0435\u043a\u0442\u0438\u0440\u043e\u0432\u043e\u043a. \u0415\u0441\u043b\u0438 \u043d\u0435 \u0443\u0447\u0430\u0441\u0442\u0432\u0443\u0435\u0442\u0435 \u0432 \u043e\u0431\u0441\u0443\u0436\u0434\u0435\u043d\u0438\u0438 - \u043f\u043e\u0442\u043e\u043c \u043d\u0435 \u0441\u0435\u0442\u0443\u0439\u0442\u0435 \u043d\u0430 \u0442\u043e, \u0447\u0442\u043e \u0441\u0438\u0441\u0442\u0435\u043c\u0430 \u0431\u044b\u043b\u0430 \u043f\u0440\u0438\u043d\u044f\u0442\u0430 \u0431\u0435\u0437 \u0432\u0430\u0448\u0435\u0433\u043e \u0432\u0435\u0441\u043e\u043c\u043e\u0433\u043e \u0433\u043e\u043b\u043e\u0441\u0430! \u0411\u043b\u0430\u0433\u043e\u0434\u0430\u0440\u044e \u0437\u0430 \u043e\u043f\u0435\u0440\u0430\u0442\u0438\u0432\u043d\u043e\u0441\u0442\u044c! \n\u0421 \u043d\u0430\u0438\u043b\u0443\u0447\u0448\u0438\u043c\u0438 \u043f\u043e\u0436\u0435\u043b\u0430\u043d\u0438\u044f\u043c\u0438, \u041b\u044e\u0434\u043e\u0432\u043a\u0438\u043d\u0430 \u0421.", "template": "MY3", "sentAt": "28.09.2012 18:45", "sender": "\u041b\u044e\u0434\u043e\u0432\u043a\u0438\u043d\u0430 \u0421. <lyudovkina.sm@skiliks.com>",
                "receiver": "\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>",
                "copy": "",
                "readed": "0",
                "attachments": 1,
                "subjectSort": "\u043d\u043e\u0432\u0430\u044f \u0441\u0438\u0441\u0442\u0435\u043c\u0430 \u043c\u043e\u0442\u0438\u0432\u0430\u0446\u0438\u0438", "attachmentName": "\u041d\u043e\u0432\u0430\u044f \u0441\u0438\u0441\u0442\u0435\u043c\u0430 \u043c\u043e\u0442\u0438\u0432\u0430\u0446\u0438\u0438.docx", "attachmentId": "37838"
            }, "996244": {
                "id": "996244",
                "subject": "\u0422\u0440\u0443\u0434\u043e\u0432\u043e\u0439 \u0434\u043e\u0433\u043e\u0432\u043e\u0440",
                "text": "\u041a\u043e\u043b\u043b\u0435\u0433\u0438! \u0412 \u0441\u0432\u044f\u0437\u0438 \u0441 \u0438\u0437\u043c\u0435\u043d\u0435\u043d\u0438\u044f\u043c\u0438 \u0442\u0440\u0443\u0434\u043e\u0432\u043e\u0433\u043e \u0437\u0430\u043a\u043e\u043d\u043e\u0434\u0430\u0442\u0435\u043b\u044c\u0441\u0442\u0432\u0430 \u044e\u0440\u0438\u0434\u0438\u0447\u0435\u0441\u043a\u0438\u0439 \u043e\u0442\u0434\u0435\u043b \u0432\u044b\u043d\u0443\u0436\u0434\u0435\u043d \u0432\u043d\u0435\u0441\u0442\u0438 \u0432 \u0442\u0440\u0443\u0434\u043e\u0432\u043e\u0439 \u0434\u043e\u0433\u043e\u0432\u043e\u0440, \u0434\u0435\u0439\u0441\u0442\u0432\u0443\u044e\u0449\u0438\u0439 \u0432 \u0434\u0430\u043d\u043d\u044b\u0439 \u043c\u043e\u043c\u0435\u043d\u0442 \u0432 \u043a\u043e\u043c\u043f\u0430\u043d\u0438\u0438 \u043a\u0430\u043a \u043e\u0441\u043d\u043e\u0432\u043d\u043e\u0439, \u0440\u044f\u0434 \u043a\u043e\u0440\u0440\u0435\u043a\u0442\u0438\u0432. \u041f\u0440\u043e\u0441\u0438\u043c \u0432\u0430\u0441 \u043e\u0437\u043d\u0430\u043a\u043e\u043c\u0438\u0442\u044c\u0441\u044f \u0441 \u043d\u0438\u043c\u0438 \u0438 \u0434\u043e\u043d\u0435\u0441\u0442\u0438 \u0434\u043e \u0441\u0432\u043e\u0438\u0445 \u0441\u043e\u0442\u0440\u0443\u0434\u043d\u0438\u043a\u043e\u0432, \u0447\u0442\u043e \u0441 01 \u044f\u043d\u0432\u0430\u0440\u044f 02 \u0433\u043e\u0434\u0430 \u0431\u0443\u0434\u0435\u0442 \u0437\u0430\u043f\u0443\u0449\u0435\u043d\u0430 \u043a\u0430\u043c\u043f\u0430\u043d\u0438\u044f \u043f\u043e \u043f\u043e\u0434\u043f\u0438\u0441\u0430\u043d\u0438\u044e \u0442\u0430\u043a\u043e\u0433\u043e \u0432\u0430\u0440\u0438\u0430\u043d\u0442\u0430 \u0442\u0440\u0443\u0434\u043e\u0432\u043e\u0433\u043e \u0434\u043e\u0433\u043e\u0432\u043e\u0440\u0430 \u0441\u043e \u0432\u0441\u0435\u043c\u0438 \u0430\u043a\u0442\u0443\u0430\u043b\u044c\u043d\u044b\u043c\u0438 \u0438 \u0432\u043d\u043e\u0432\u044c \u043f\u0440\u0438\u0448\u0435\u0434\u0448\u0438\u043c\u0438 \u0441\u043e\u0442\u0440\u0443\u0434\u043d\u0438\u043a\u0430\u043c\u0438 \u043a\u043e\u043c\u043f\u0430\u043d\u0438\u0438. \u041f\u0440\u043e\u0441\u0438\u043c \u043e\u0442\u043d\u0435\u0441\u0442\u0438\u0442\u044c \u043a \u044d\u0442\u043e\u0439 \u0440\u0430\u0431\u043e\u0442\u0435 \u0441\u043e \u0432\u0441\u0435\u0439 \u043e\u0442\u0432\u0435\u0442\u0441\u0442\u0432\u0435\u043d\u043d\u043e\u0441\u0442\u044c\u044e \u0438 \u0434\u043e\u043b\u0436\u043d\u044b\u043c \u0432\u043d\u0438\u043c\u0430\u043d\u0438\u0435\u043c. \n\u0421 \u0443\u0432\u0430\u0436\u0435\u043d\u0438\u0435\u043c, \u0410\u0434\u0432\u043e\u043a\u0430\u0442\u043e\u0432 \u042e.",
                "template": "MY4",
                "sentAt": "01.10.2012 16:30",
                "sender": "\u0410\u0434\u0432\u043e\u043a\u0430\u0442\u043e\u0432 \u042e. <advokatov.yv@skiliks.com>",
                "receiver": "\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>",
                "copy": "",
                "readed": "0",
                "attachments": 1,
                "subjectSort": "\u0442\u0440\u0443\u0434\u043e\u0432\u043e\u0439 \u0434\u043e\u0433\u043e\u0432\u043e\u0440",
                "attachmentName": "\u0422\u0440\u0443\u0434\u043e\u0432\u043e\u0439 \u0434\u043e\u0433\u043e\u0432\u043e\u0440.docx",
                "attachmentId": "37839"
            }
        }, "type": "inbox"};
        var outbox = {
            "result": 1,
            "messages": {
                1: {
                    "id": "996241",
                    "subject": "По ценовой политике",
                    "text": "Наша ценовая политика — говно",
                    "template": "MY1",
                    "sentAt": "03.10.2012 10:32",
                    "sender": 'bob <bob@skiliks.com>',
                    "receiver": "me <me@skiliks.com>",
                    "copy": "",
                    "readed": "0",
                    "attachments": 1,
                    "subjectSort": "наша ценовая политика — говно",
                    "attachmentName": "говно.xlsx",
                    "attachmentId": "37837",
                    "attachmentFileId": "236255"
                }
            },
            "type": "outbox"
        };
        var drafts = {
            "result": 1,
            "messages": {},
            "type": "outbox"
        };
        /**
         * @type {SKMailClientView} SKMailClientView
         */

        run(function () {
            _.templateSettings.interpolate = /<@=(.+?)@>/g;
            _.templateSettings.evaluate = /<@(.+?)@>/g;
            var server;
            var timers;
            before(function () {
                server = sinon.fakeServer.create();

                server.respondWith("POST", "/index.php/myDocuments/getList",
                    [200, { "Content-Type": "application/json" },
                        JSON.stringify({
                            "result":1,"data":[
                                {
                                    "id":"5546",
                                    "name":"kvartalnyj_plan_01_Q4.pptx",
                                    "srcFile":"kvartalnyj_plan_01_Q4.pdf",
                                    "mime":"application\/pdf"
                                },{
                                    "id":"5549",
                                    "name":"kvartalnyj_plan_01_Q4.pptx",
                                    "srcFile":"kvartalnyj_plan_01_Q4.pdf",
                                    "mime":"application\/pdf"
                                }
                            ]
                        })
                    ]);

                server.respondWith("POST", "/index.php/character/list",
                    [200, { "Content-Type": "application/json" },
                        JSON.stringify([
                                {'id': 1, 'fio': 'bob', email: 'bob@skiliks.com', 'code': 2},
                                {'id': 2, 'fio': 'john', email: 'john@skiliks.com', 'code': 3}
                        ])]);

                server.respondWith("POST", "/index.php/mail/getMessages",
                    function (xhr) {
                        var data;
                        if (xhr.requestBody.match('folderId=1')) {
                            data = inbox;
                        } else if (xhr.requestBody.match('folderId=2')) {
                            data = drafts;
                        } else if (xhr.requestBody.match('folderId=3')) {
                            data = outbox;
                        } else {
                            data = {
                                result: 1
                            };
                        }
                        xhr.respond(200, { "Content-Type": "application/json" },
                            JSON.stringify(data));
                    });

                server.respondWith("POST", "/index.php/simulation/start",
                    [200, { "Content-Type": "application/json" },
                        JSON.stringify({result: 1})]);

                server.respondWith("POST", "/index.php/mail/saveDraft", function (xhr) {
                    var draft_message = {
                        "id": "996242",
                        "subject": "По ценовой политике",
                        "text": "Наша ценовая политика — говно",
                        "template": "MY1",
                        "sentAt": "03.10.2012 10:32",
                        "sender": 'bob <bob@skiliks.com>',
                        "receiver": "me <me@skiliks.com>",
                        "copy": "",
                        "readed": "0",
                        "attachments": 1,
                        "subjectSort": "наша ценовая политика — говно",
                        "attachmentName": "говно.xlsx",
                        "attachmentId": "37837",
                        "attachmentFileId": "236255"
                    };
                    drafts.messages[draft_message.id] = draft_message;
                    xhr.respond(200, { "Content-Type": "application/json" },
                        JSON.stringify({result: 1}));

                });

                server.respondWith("POST", "/index.php/mail/sendDraft", function (xhr) {
                    var id = xhr.requestBody.split('=')[1];
                    outbox.messages[id] = drafts.messages[id];
                    delete drafts.messages[id];
                    xhr.respond(200, { "Content-Type": "application/json" },
                        JSON.stringify({result: 1}));

                });

                server.respondWith("POST", "/index.php/mail/MarkRead",
                    [200, { "Content-Type": "application/json" },
                        JSON.stringify({result: 1})]);

                server.respondWith("POST", "/index.php/events/getState",
                    [200, { "Content-Type": "application/json" },
                        JSON.stringify({result: 0})]);

                server.respondWith("POST", "/index.php/todo/get",
                    [200, { "Content-Type": "application/json" },
                        JSON.stringify({result: 1})]);

                server.respondWith("POST", "/index.php/dayPlan/get",
                    [200, { "Content-Type": "application/json" },
                        JSON.stringify({result: 1})]);

                server.respondWith("POST", "/index.php/mail/getThemes",
                    [200, { "Content-Type": "application/json" },
                        JSON.stringify({
                            "result": 1,
                            "data": {
                                "1": "subject 1",
                                "2": "subject 2"
                            },
                            "characterThemeId": 101
                        })]);

                server.respondWith("POST", "/index.php/mail/getPhrases",
                    [200, { "Content-Type": "application/json" },
                        JSON.stringify({
                                "result": 1,
                                "data": {"1": "phrase 1", "2": "phrase 2", "3": "phrase 3"},
                                "addData": {"3613": ".", "3614": ",", "3615": ":", "3616": "\"", "3617": "-", "3618": ";"},
                                "message": ""}
                        )]);

                server.respondWith("POST", "/index.php/mail/sendMessage",
                    [200, { "Content-Type": "application/json" },
                        JSON.stringify({
                            "result": 1,
                            "mailId": "1245"
                        })]);

                //clock = sinon.useFakeTimers();
                //this.timeout = 10000;
                window.SKApp = new SKApplication({'start': '9:00', "skiliksSpeedFactor": 8 });
                SKApp.set('finish', '20:00');
                SKApp.set('end', '18:00');
                this.timeout = 1000;
                timers = sinon.useFakeTimers();
            });
            after(function () {
                server.restore();
                timers.restore();
            });

            it("can display mail client", function () {
                var simulation = SKApp.simulation;
                simulation.start();
                server.respond();

                var mail_window = new SKWindow({name: 'mailEmulator', subname: 'mailMain'});
                mail_window.open();
                buster.assert.defined(simulation.mailClient);
                var mail = new SKMailClientView({model_instance: mail_window});
                var spy = sinon.spy();
                mail.mailClient.on('init_completed', spy);
                mail.render();
                server.respond();

                assert.calledOnce(spy, 'Init completed triggered');
                //buster.log(mail.$el.html());
                assert.defined(mail.mailClient.getEmailByMySqlId(996241));

                /* 4 letters at sim start */
                expect(mail.$('.mail-emulator-received-list-cell-sender').length).toBe(4);
                expect(mail.$('tr[data-email-id=996241] td.mail-emulator-received-list-cell-theme').text()).toBe('По ценовой политике');
                expect(mail.mailClient.getInboxFolder().name).toBe('Входящие');
                assert.calledOnce(spy);
                server.respond();
            });

            it("has characters", function () {
                var collection = new SKCharacterCollection();
                collection.fetch();
                server.respond();
                expect(collection.pluck('fio')).toEqual(["bob", "john"]);
                server.respond();
            });

            it("can save draft and send draft", function () {
                var simulation = SKApp.simulation;
                simulation.start();
                server.respond();

                var mail_window = new SKWindow({name: 'mailEmulator', subname: 'mailMain'});
                mail_window.open();

                var mailView = new SKMailClientView({model_instance: mail_window});

                mailView.render();
                server.respond();
                mailView.$('#FOLDER_DRAFTS').click();
                server.respond();
                expect(0).toBe(0);

                expect(mailView.$el.find('.email-list-line').length).toBe(0);
                mailView.$('#FOLDER_INBOX').click();
                server.respond();
                expect(mailView.$el.find('.email-list-line').length).toBe(4);
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

                server.respond()
                // check subjects
                expect(SKApp.simulation.mailClient.availableSubjects.length).toBe(2);

                //mailView.$el.find('#MailClient_NewLetterSubject option:eq(1)').focus();
                mailView.$el.find("#MailClient_NewLetterSubject").ddslick('select', {'index': 1 });

                mailView.doUpdateMailPhrasesList();

                mailView.$el.find('.SAVE_TO_DRAFTS').click();

                server.respond();

                mailView.$el.find('#FOLDER_DRAFTS').click();
                server.respond();
                expect(mailView.$el.find('.email-list-line').length).toBe(1);

                mailView.doSendDraft();
                server.respond();
                expect(mailView.$el.find('.email-list-line').length).toBe(1);
                mailView.$el.find('#FOLDER_SENDED').click();
                server.respond();
                expect(mailView.$el.find('.email-list-line').length).toBe(1);
                server.respond();
            });

            it("can create and send new letter (phrases)", function () {
                var simulation = SKApp.simulation;
                simulation.start();
                server.respond();

                var mail_window = new SKWindow({name: 'mailEmulator', subname: 'mailMain'});
                mail_window.open();

                var mailView = new SKMailClientView({model_instance: mail_window});

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


                server.respond();
                // check subjects
                expect(SKApp.simulation.mailClient.availableSubjects.length).toBe(2);

                mailView.$el.find("#MailClient_NewLetterSubject").ddslick('select', {'index': 1 });

                mailView.doUpdateMailPhrasesList();
                server.respond();

                // check than phrases not empty
                expect(mailView.$el.find('#mailEmulatorNewLetterTextVariants li').length).toBe(3);

                server.respond();

                // check phrases
                expect(SKApp.simulation.mailClient.availablePhrases.length).toBe(3);

                mailView.$el.find('.SEND_EMAIL').click();

                // one server.respond response on all request from front on requests query
                server.respond();

                // check is email send
                expect(server.requests[server.requests.length - 1].url).toBe('/index.php/mail/sendMessage');
                server.respond();

                // check response
                expect(server.responses[server.responses.length - 1].response[2])
                    .toBe('{"result":1,"mailId":"1245"}');

                // check that mail main screen opened after mail send
                expect(mailView.$el.find('#MailClient_IncomeFolder_List').length).toBe(0);
                server.respond();
            });

            it("can create and send new letter with attachment", function () {
                var simulation = SKApp.simulation;
                simulation.start();
                server.respond();

                var mail_window = new SKWindow({name: 'mailEmulator', subname: 'mailMain'});
                mail_window.open();

                var mailView = new SKMailClientView({model_instance: mail_window});

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

                server.respond();
                // check subjects
                expect(SKApp.simulation.mailClient.availableSubjects.length).toBe(2);

                //mailView.$el.find('#MailClient_NewLetterSubject option:eq(1)').focus();
                mailView.$("#MailClient_NewLetterSubject").ddslick('select', {'index': 1 });

                mailView.doUpdateMailPhrasesList();
                server.respond();

                // check than phrases not empty
                expect(mailView.$('#mailEmulatorNewLetterTextVariants li').length)
                    .toBe(3);

                server.respond();

                mailView.$("#MailClient_NewLetterAttachment div.list").ddslick('select', {'index': 1 });

                // check phrases
                expect(SKApp.simulation.mailClient.availablePhrases.length).toBe(3);

                mailView.$el.find('.SEND_EMAIL').click();

                // one server.respond response on all request from front on requests query
                server.respond();

                // check is email send
                expect(server.requests[server.requests.length-1].url).toBe('/index.php/mail/sendMessage');
                server.respond();
                expect(server.requests[server.requests.length - 1].requestBody).toMatch('fileId=5546');

                // check response
                expect(server.responses[server.responses.length - 1].response[2])
                    .toBe('{"result":1,"mailId":"1245"}');

                // check that mail main screen opened after mail send
                expect(mailView.$el.find('#MailClient_IncomeFolder_List').length).toBe(0);
                server.respond();
            });

            it("Check mail logs", function () {
                var simulation = SKApp.simulation;

                // action 1:
                simulation.start();
                server.respond();

                var mail_window = new SKWindow({name: 'mailEmulator', subname: 'mailMain'});
                mail_window.open();

                var mailView = new SKMailClientView({model_instance: mail_window});

                // action 2:
                mailView.render();
                server.respond();

                timers.tick(10 * 1000); // 10 sec
                server.respond();

                // action 3:
                mailView.$('#FOLDER_DRAFTS').click();
                timers.tick(10 * 1000); // 10 sec
                server.respond();

                // action 4:
                mailView.$('a.NEW_EMAIL').click();
                timers.tick(3 * 1000); // 10 sec
                server.respond();

                // action 5:
                mailView.$('.btn-cl.win-close').click();
                timers.tick(3 * 1000); // 10 sec
                simulation.mailClient.message_window.$('.mail-popup-button:eq(0)').click();
                timers.tick(5 * 1000); // 10 sec
                server.respond();

                // action 6:
                mailView.$('#FOLDER_INBOX').click();
                timers.tick(5 * 1000); // 10 sec
                mailView.$('.email-list-line:eq(1)').click();
                timers.tick(10 * 1000); // 10 sec
                server.respond();

                // combine logs for assets [
                var logs = new Array();

                logs[0] = {
                    'logs[0][0]': "1",
                    'logs[0][1]': "1",
                    'logs[0][2]': "activated",
                    'logs[0][3]': "32400",
                    'logs[0][window_uid]': "uid1-1",
                    'timeString': "540"
                };

                logs[1] = {
                    'logs[0][0]': "1",
                    'logs[0][1]': "1",
                    'logs[0][2]': "deactivated",
                    'logs[0][3]': "32400",
                    'logs[0][window_uid]': "uid1-2",
                    'logs[1][0]': "10",
                    'logs[1][1]': "11",
                    'logs[1][2]': "activated",
                    'logs[1][3]': "32400",
                    'logs[1][window_uid]': "uid2-3",
                    'logs[2][0]': "10",
                    'logs[2][1]': "11",
                    'logs[2][2]': "deactivated",
                    'logs[2][3]': "32400",
                    'logs[2][window_uid]': "uid2-4",
                    'logs[3][0]': "10",
                    'logs[3][1]': "11",
                    'logs[3][2]': "activated",
                    'logs[3][3]': "32400",
                    //'logs[3][4][mailId]': "996241",
                    'logs[3][window_uid]': "uid2-5",
                    'logs[4][0]': "10",
                    'logs[4][1]': "11",
                    'logs[4][2]': "deactivated",
                    'logs[4][3]': "32400",
                    //'logs[4][4][mailId]': "996241",
                    'logs[4][window_uid]': "uid2-6",
                    'logs[5][0]': "10",
                    'logs[5][1]': "11",
                    'logs[5][2]': "activated",
                    'logs[5][3]': "32400",
                    //'logs[5][4][mailId]': "996241",
                    'logs[5][window_uid]': "uid2-7",
                    'timeString': "541"
                };

                logs[2] = {
                    'logs[0][0]': "10",
                    'logs[0][1]': "11",
                    'logs[0][2]': "deactivated",
                    'logs[0][3]': "32480",
                    //'logs[0][4][mailId]': "996241",
                    'logs[0][window_uid]': "uid2-8",
                    'logs[1][0]': "10",
                    'logs[1][1]': "11",
                    'logs[1][2]': "activated",
                    'logs[1][3]': "32480",
                    'logs[1][window_uid]': "uid2-9",
                    'timeString': "542"
                };

                logs[3] = {
                    'logs[0][0]': "10",
                    'logs[0][1]': "11",
                    'logs[0][2]': "deactivated",
                    'logs[0][3]': "32560",
                    'logs[0][window_uid]': "uid2-10",
                    'logs[1][0]': "10",
                    'logs[1][1]': "13",
                    'logs[1][2]': "activated",
                    'logs[1][3]': "32560",
                    'logs[1][window_uid]': "uid3-11",
                    'timeString': "543"
                };

                logs[4] = {
                    'logs[0][0]': "10",
                    'logs[0][1]': "13",
                    'logs[0][2]': "deactivated",
                    'logs[0][3]': "32608",
                    'logs[0][window_uid]': "uid3-12",
                    'logs[1][0]': "10",
                    'logs[1][1]': "11",
                    'logs[1][2]': "activated",
                    'logs[1][3]': "32608",
                    'logs[1][window_uid]': "uid2-13",
                    'timeString': "544"
                };

                logs[5] = {
                    'logs[0][0]': "10",
                    'logs[0][1]': "11",
                    'logs[0][2]': "deactivated",
                    'logs[0][3]': "32648",
                    'logs[0][window_uid]': "uid2-14",
                    'logs[1][0]': "10",
                    'logs[1][1]': "11",
                    'logs[1][2]': "activated",
                    'logs[1][3]': "32648",
                    //'logs[1][4][mailId]': "996241",
                    'logs[1][window_uid]': "uid2-15",
                    'timeString': "545"
                };
                // combine logs for assets }

                // window uids random - so we need to fix our predefine values of uid {
                var windowUid_1 = null;
                var windowUid_2 = null;
                var windowUid_2_2 = null;
                var windowUid_2_3 = null;
                var windowUid_2_4 = null;
                var windowUid_2_5 = null;
                var windowUid_3 = null;
                var windowUid_4 = null;
                var windowUid_5 = null;
                // window uids random - so we need to fix our predefine values of uid }

                var i = 0;
                _.each(server.requests, function(item) {
                    if ('/index.php/events/getState' === item.url) {
                        var requestArray = URLToArray(decodeURIComponent(item.requestBody));
                        //console.log(requestArray);
                        //console.log('-----------------------');

                        if (0 === i) {
                            //console.log(i);
                            windowUid_1 = requestArray['logs[0][window_uid]'];
                            logs[i]['logs[0][window_uid]'] = windowUid_1;
                         }

                        if (1 === i) {
                            //console.log(i);
                            //console.log(requestArray)
                            windowUid_2 = requestArray['logs[1][window_uid]'];
                            windowUid_2_2 = requestArray['logs[3][window_uid]'];
                            windowUid_2_3 = requestArray['logs[5][window_uid]'];
                            logs[i]['logs[0][window_uid]'] = windowUid_1;
                            logs[i]['logs[1][window_uid]'] = windowUid_2;
                            logs[i]['logs[2][window_uid]'] = windowUid_2;
                            logs[i]['logs[3][window_uid]'] = windowUid_2_2;
                            logs[i]['logs[4][window_uid]'] = windowUid_2_2;
                            logs[i]['logs[5][window_uid]'] = windowUid_2_3;
                        }

                        if (2 === i) {
                            //console.log(i);
                            //windowUid_2_4 = requestArray['logs[0][window_uid]'];
                            windowUid_2_4 = requestArray['logs[1][window_uid]'];
                            logs[i]['logs[0][window_uid]'] = windowUid_2_3;
                            logs[i]['logs[1][window_uid]'] = windowUid_2_4;

                        }

                        if (3 === i) {
                            //console.log(i);

                            windowUid_3 = requestArray['logs[1][window_uid]'];
                            logs[i]['logs[0][window_uid]'] = windowUid_2_4;
                            logs[i]['logs[1][window_uid]'] = windowUid_3;
                        }

                        if (4 === i) {
                            //console.log(i);
                            windowUid_2_4 = requestArray['logs[1][window_uid]'];
                            logs[i]['logs[0][window_uid]'] = windowUid_3;
                            logs[i]['logs[1][window_uid]'] = windowUid_2_4;
                        }

                        if (5 === i) {
                            //console.log(i);
                            //console.log(requestArray);
                            windowUid_5 = requestArray['logs[1][window_uid]'];
                            logs[i]['logs[0][window_uid]'] = windowUid_2_4;
                            logs[i]['logs[1][window_uid]'] = windowUid_5;
                        }

                        //console.log(i);
                        _.each(logs[i], function(logsItem, key) {
//                            if (2 < i) {
//                                console.log('key:', key);
//                            }
                            expect(logsItem.toString()).toBe(requestArray[key].toString());
                        });
                        i++;
                    }
                });
            });
        });
    });
});

function URLToArray(url) {
    var request = {};
    var pairs = url.substring(url.indexOf('?') + 1).split('&');
    for (var i = 0; i < pairs.length; i++) {
        var pair = pairs[i].split('=');
        request[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
    }
    return request;
}