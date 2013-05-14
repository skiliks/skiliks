/*global buster, sinon, describe, before, after, require */


buster.spec.expose();

define([
    "game/models/SKApplication",
    "game/models/SKSimulation",
    "game/views/mail/SKMailClientView",
    "game/models/window/SKWindow"], function (SKApplication, SKSimulation, SKMailClientView, SKWindow) {

    spec = describe('new mail', function (run) {
        "use strict";
        /**
         * @type {SKMailClientView} SKMailClientView
         */
        run(function () {
            var inbox = {
                "result":1,
                "messages":{
                    "1274":{
                        "id":"1274",
                        "subject":"\u041f\u043e \u0446\u0435\u043d\u043e\u0432\u043e\u0439 \u043f\u043e\u043b\u0438\u0442\u0438\u043a\u0435",
                        "text":"\u0414\u043e\u0431\u0440\u044b\u0439 \u0434\u0435\u043d\u044c! \n\n\u042f \u043d\u0435\u043c\u043d\u043e\u0433\u043e \u0441 \u043e\u043f\u0435\u0440\u0435\u0436\u0435\u043d\u0438\u0435\u043c \u0441\u0434\u0435\u043b\u0430\u043b\u0430 \u0440\u0430\u0431\u043e\u0442\u0443 \u043f\u043e \u0446\u0435\u043d\u043e\u0432\u043e\u0439 \u043f\u043e\u043b\u0438\u0442\u0438\u043a\u0435 (\u0432\u0447\u0435\u0440\u0430 \u0432\u044b\u0434\u0430\u043b\u0441\u044f \u0441\u0432\u043e\u0431\u043e\u0434\u043d\u044b\u0439 \u0432\u0435\u0447\u0435\u0440). \u041c\u043d\u0435 \u043a\u0430\u0436\u0435\u0442\u0441\u044f, \u0447\u0442\u043e \u044f \u043e\u0442\u0440\u0430\u0437\u0438\u043b\u0430 \u0432\u0441\u0435 \u043c\u044b\u0441\u043b\u0438, \u043a\u043e\u0442\u043e\u0440\u044b\u0435 \u043c\u044b \u043e\u0431\u0441\u0443\u0436\u0434\u0430\u043b\u0438 \u043d\u0430 \u0443\u0441\u0442\u0430\u043d\u043e\u0432\u043e\u0447\u043d\u043e\u0439 \u0432\u0441\u0442\u0440\u0435\u0447\u0435. \u0411\u0443\u0434\u0435\u0442 \u0432\u0440\u0435\u043c\u044f \u0432 \u043e\u0442\u043f\u0443\u0441\u043a\u0435 - \u043f\u043e\u0441\u043c\u043e\u0442\u0440\u0438\u0442\u0435. \n\n\u0421 \u0443\u0432\u0430\u0436\u0435\u043d\u0438\u0435\u043c, \u041c\u0430\u0440\u0438\u043d\u0430 \u041a\u0440\u0443\u0442\u044c\u043a\u043e  \n\u0412\u0435\u0434\u0443\u0449\u0438\u0439 \u0430\u043d\u0430\u043b\u0438\u0442\u0438\u043a \u043e\u0442\u0434\u0435\u043b\u0430 \u0430\u043d\u0430\u043b\u0438\u0437\u0430 \u0438 \u043f\u043b\u0430\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u044f",
                        "template":"MY1",
                        "sentAt":"03.10.2012 10:32",
                        "sender":"\u041a\u0440\u0443\u0442\u044c\u043a\u043e \u041c. <krutko.ma@skiliks.com>",
                        "receiver":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>",
                        "copy":"",
                        "readed":"0",
                        "attachments":1,
                        "subjectSort":"\u043f\u043e \u0446\u0435\u043d\u043e\u0432\u043e\u0439 \u043f\u043e\u043b\u0438\u0442\u0438\u043a\u0435",
                        "attachmentName":"\u0426\u0435\u043d\u043e\u0432\u0430\u044f \u043f\u043e\u043b\u0438\u0442\u0438\u043a\u0430.xlsx",
                        "attachmentId":"251",
                        "attachmentFileId":"316"
                    },
                    "1275":{
                        "id":"1275",
                        "subject":"\u0424\u043e\u0440\u043c\u0430 \u043e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u0438 \u0434\u043b\u044f \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0430",
                        "text":"\u0414\u043e\u0431\u0440\u043e\u0433\u043e \u0432\u0430\u043c \u0432\u0440\u0435\u043c\u0435\u043d\u0438 \u0441\u0443\u0442\u043e\u043a! \n\u041f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0435\u043d\u043d\u044b\u0439 \u043e\u0442\u0434\u0435\u043b \u043f\u0440\u043e\u0441\u0438\u0442 \u0432\u0430\u0441 \u0440\u0430\u0441\u0441\u043c\u043e\u0442\u0440\u0435\u0442\u044c \u0432\u043e\u0437\u043c\u043e\u0436\u043d\u043e\u0441\u0442\u044c \u0432\u043d\u0435\u0441\u0435\u043d\u0438\u044f \u0438\u0437\u043c\u0435\u043d\u0435\u043d\u0438\u0439 \u0432 \u0442\u0435\u043a\u0443\u0449\u0443\u044e \u0444\u043e\u0440\u043c\u0443 \u043e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u0438 \u043f\u043e \u043e\u0431\u044a\u0435\u043c\u0430\u043c \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0430 \u0438 \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0435\u043d\u043d\u044b\u043c \u043c\u043e\u0449\u043d\u043e\u0441\u0442\u044f\u043c. \u041d\u0430 \u0442\u0435\u043a\u0443\u0449\u0438\u0439 \u043c\u043e\u043c\u0435\u043d\u0442 \u0432 \u043e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u0438 \u043d\u0435 \u0434\u043e\u0441\u0442\u0430\u0435\u0442 \u0440\u0430\u0437\u0432\u0435\u0440\u043d\u0443\u0442\u043e\u0433\u043e \u0430\u043d\u0430\u043b\u0438\u0437\u0430 \u043e\u0441\u0442\u0430\u0442\u043a\u043e\u0432 \u043d\u0430 \u0432\u0441\u0435\u0445 \u043d\u0430\u0448\u0438\u0445 \u0441\u043a\u043b\u0430\u0434\u0430\u0445, \u0432\u043a\u043b\u044e\u0447\u0430\u044f \u0442\u043e\u0440\u0433\u043e\u0432\u044b\u0435. \u042d\u0442\u043e \u043f\u0440\u0438\u0432\u043e\u0434\u0438\u0442 \u043a \u0442\u043e\u043c\u0443, \u0447\u0442\u043e \u043c\u044b \u043f\u0435\u0440\u0438\u043e\u0434\u0438\u0447\u0435\u0441\u043a\u0438 \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0438\u043c \u0442\u043e\u0432\u0430\u0440, \u043a\u043e\u0442\u043e\u0440\u044b\u0439 \u0443\u0436\u0435 \u0435\u0441\u0442\u044c \u0432 \u0440\u0435\u0433\u0438\u043e\u043d\u0430\u0445. \u041b\u043e\u0433\u0438\u0441\u0442\u044b \u0433\u043e\u0432\u043e\u0440\u044f\u0442, \u0447\u0442\u043e \u0432\u043f\u043e\u043b\u043d\u0435 \u043c\u043e\u0433\u043b\u0438 \u0431\u044b \u043e\u0431\u0435\u0441\u043f\u0435\u0447\u0438\u0442\u044c \u043f\u0435\u0440\u0435\u0431\u0440\u043e\u0441\u0443 \u0442\u043e\u0432\u0430\u0440\u0430 \u0438\u0437 \u043e\u0434\u043d\u043e\u0433\u043e \u0440\u0435\u0433\u0438\u043e\u043d\u0430 \u0432 \u0434\u0440\u0443\u0433\u043e\u0439. \u0422\u0430\u043a\u0438\u043c \u043e\u0431\u0440\u0430\u0437\u043e\u043c, \u043d\u0430\u043c \u0443\u0434\u0430\u043b\u043e\u0441\u044c \u0431\u044b \u0441\u044d\u043a\u043e\u043d\u043e\u043c\u0438\u0442\u044c \u043d\u0430 \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0435\u043d\u043d\u044b\u0445 \u0438\u0437\u0434\u0435\u0440\u0436\u043a\u0430\u0445 \u0431\u0435\u0437 \u0441\u043d\u0438\u0436\u0435\u043d\u0438\u044f \u043e\u0431\u044a\u0435\u043c\u043e\u0432 \u043f\u0440\u043e\u0434\u0430\u0436. \u041f\u0440\u043e\u0448\u0443 \u0432\u0430\u0441 \u043e\u0446\u0435\u043d\u0438\u0442\u044c \u0441\u0440\u043e\u043a\u0438, \u0442\u0440\u0443\u0434\u043e\u0435\u043c\u043a\u043e\u0441\u0442\u044c \u0438 \u043d\u0430\u043b\u0438\u0447\u0438\u0435 \u0432\u043e\u0437\u043c\u043e\u0436\u043d\u043e\u0441\u0442\u0438 \u0434\u043e\u0431\u0430\u0432\u0438\u0442\u044c \u0432 \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0435\u043d\u043d\u044b\u0439 \u043e\u0442\u0447\u0435\u0442 \u0434\u0430\u043d\u043d\u044b\u0435 \u043f\u043e \u0441\u043a\u043b\u0430\u0434\u0441\u043a\u0438\u043c \u043e\u0441\u0442\u0430\u0442\u043a\u0430\u043c.\n\u0417\u0430\u0440\u0430\u043d\u0435\u0435 \u0431\u043b\u0430\u0433\u043e\u0434\u0430\u0440\u044e, \u0411\u043e\u0431\u0440 \u0412.,  \n\u041d\u0430\u0447. \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0435\u043d\u043d\u043e\u0433\u043e \u043e\u0442\u0434\u0435\u043b\u0430.",
                        "template":"MY2",
                        "sentAt":"03.10.2012 11:02",
                        "sender":"\u0412\u0430\u0441\u0438\u043b\u0438\u0439 \u0411\u043e\u0431\u0440 <bobr.vs@skiliks.com>",
                        "receiver":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>",
                        "copy":"",
                        "readed":"0",
                        "attachments":0,
                        "subjectSort":"\u0444\u043e\u0440\u043c\u0430 \u043e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u0438 \u0434\u043b\u044f \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0430"
                    },
                    "1276":{
                        "id":"1276",
                        "subject":"\u041d\u043e\u0432\u0430\u044f \u0441\u0438\u0441\u0442\u0435\u043c\u0430 \u043c\u043e\u0442\u0438\u0432\u0430\u0446\u0438\u0438",
                        "text":"\u041a\u043e\u043b\u043b\u0435\u0433\u0438, \u0434\u043e\u0431\u0440\u044b\u0439 \u0434\u0435\u043d\u044c!        \n\u041f\u043e\u0441\u043b\u0435 \u043d\u0435\u0441\u043a\u043e\u043b\u044c\u043a\u0438\u0445 \u043f\u0440\u043e\u0434\u0443\u043a\u0442\u0438\u0432\u043d\u044b\u0445 \u0441\u043e\u0432\u0435\u0449\u0430\u043d\u0438\u0439 \u0441 \u0432\u0430\u043c\u0438 (\u043e\u0442\u0434\u0435\u043b\u044c\u043d\u043e\u0435 \u0441\u043f\u0430\u0441\u0438\u0431\u043e \u0424\u0435\u0434\u043e\u0440\u043e\u0432\u0443 \u0410. \u0437\u0430 \u0440\u0435\u0433\u0443\u043b\u044f\u0440\u043d\u043e\u0435 \u0443\u0447\u0430\u0441\u0442\u0438\u0435!) \u043c\u044b \u043f\u043e\u0434\u0433\u043e\u0442\u043e\u0432\u0438\u043b\u0438 \u0438\u0442\u043e\u0433\u043e\u0432\u0443\u044e \u0432\u0435\u0440\u0441\u0438\u044e \u0441\u0438\u0441\u0442\u0435\u043c\u044b \u043c\u043e\u0442\u0438\u0432\u0430\u0446\u0438\u0438 \u0434\u043b\u044f \u043c\u0435\u043d\u0435\u0434\u0436\u0435\u0440\u043e\u0432 \u043f\u0435\u0440\u0432\u043e\u0433\u043e \u0438 \u0432\u0442\u043e\u0440\u043e\u0433\u043e \u0443\u0440\u043e\u0432\u043d\u044f. \u041c\u044b \u043f\u043b\u0430\u043d\u0438\u0440\u0443\u0435\u043c \u0432\u0432\u043e\u0434\u0438\u0442\u044c \u0435\u0435 \u0432 \u0434\u0435\u0439\u0441\u0442\u0432\u0438\u0435 \u0441 \u043d\u0430\u0447\u0430\u043b\u0430 02 \u0433\u043e\u0434\u0430 (\u0438\u043c\u0435\u043d\u043d\u043e \u043e\u043d\u0430 \u0438 \u0431\u0443\u0434\u0435\u0442 \u0437\u0430\u0431\u044e\u0434\u0436\u0435\u0442\u0438\u0440\u043e\u0432\u0430\u043d\u0430). \u0416\u0434\u0435\u043c \u0432\u0430\u0448\u0438\u0445 \u043e\u043a\u043e\u043d\u0447\u0430\u0442\u0435\u043b\u044c\u043d\u044b\u0445 \u043a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0435\u0432 \u0438 \u043a\u043e\u0440\u0440\u0435\u043a\u0442\u0438\u0440\u043e\u0432\u043e\u043a. \u0415\u0441\u043b\u0438 \u043d\u0435 \u0443\u0447\u0430\u0441\u0442\u0432\u0443\u0435\u0442\u0435 \u0432 \u043e\u0431\u0441\u0443\u0436\u0434\u0435\u043d\u0438\u0438 - \u043f\u043e\u0442\u043e\u043c \u043d\u0435 \u0441\u0435\u0442\u0443\u0439\u0442\u0435 \u043d\u0430 \u0442\u043e, \u0447\u0442\u043e \u0441\u0438\u0441\u0442\u0435\u043c\u0430 \u0431\u044b\u043b\u0430 \u043f\u0440\u0438\u043d\u044f\u0442\u0430 \u0431\u0435\u0437 \u0432\u0430\u0448\u0435\u0433\u043e \u0432\u0435\u0441\u043e\u043c\u043e\u0433\u043e \u0433\u043e\u043b\u043e\u0441\u0430! \u0411\u043b\u0430\u0433\u043e\u0434\u0430\u0440\u044e \u0437\u0430 \u043e\u043f\u0435\u0440\u0430\u0442\u0438\u0432\u043d\u043e\u0441\u0442\u044c! \n\u0421 \u043d\u0430\u0438\u043b\u0443\u0447\u0448\u0438\u043c\u0438 \u043f\u043e\u0436\u0435\u043b\u0430\u043d\u0438\u044f\u043c\u0438, \u041b\u044e\u0434\u043e\u0432\u043a\u0438\u043d\u0430 \u0421.",
                        "template":"MY3",
                        "sentAt":"28.09.2012 18:45",
                        "sender":"\u041b\u044e\u0434\u043e\u0432\u043a\u0438\u043d\u0430 \u0421. <lyudovkina.sm@skiliks.com>",
                        "receiver":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>",
                        "copy":"",
                        "readed":"0",
                        "attachments":1,
                        "subjectSort":"\u043d\u043e\u0432\u0430\u044f \u0441\u0438\u0441\u0442\u0435\u043c\u0430 \u043c\u043e\u0442\u0438\u0432\u0430\u0446\u0438\u0438",
                        "attachmentName":"\u041d\u043e\u0432\u0430\u044f \u0441\u0438\u0441\u0442\u0435\u043c\u0430 \u043c\u043e\u0442\u0438\u0432\u0430\u0446\u0438\u0438.docx",
                        "attachmentId":"252",
                        "attachmentFileId":"317"
                    },
                    "1277":{
                        "id":"1277",
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
                        "attachmentId":"253",
                        "attachmentFileId":"318"
                    },
                    "1278":{
                        "id":"1278",
                        "subject":"срочно! Отчетность",
                        "text":"\u0414\u043e\u0431\u0440\u044b\u0439 \u0434\u0435\u043d\u044c, \u043a\u043e\u043b\u043b\u0435\u0433\u0438! \n\u042f, \u043a\u0430\u043a \u0432\u0441\u0435\u0433\u0434\u0430, \u043f\u043e \u0441\u0440\u043e\u0447\u043d\u043e\u043c\u0443 \u0432\u043e\u043f\u0440\u043e\u0441\u0443. \u041d\u0438 \u0443 \u043a\u043e\u0433\u043e \u0432\u0435\u0434\u044c \u043d\u0435\u0442 \u0441\u043e\u043c\u043d\u0435\u043d\u0438\u0439, \u0447\u0442\u043e \u043f\u0440\u043e\u0434\u0430\u0436\u0438 \u043a\u0440\u0430\u0439\u043d\u0435 \u0432\u0430\u0436\u043d\u044b \u0434\u043b\u044f \u043a\u043e\u043c\u043f\u0430\u043d\u0438\u0438?! \u0421\u0443\u0434\u044f \u043f\u043e \u0442\u043e\u043c\u0443, \u043a\u0430\u043a \u0443 \u043d\u0430\u0441 \u0440\u0430\u0431\u043e\u0442\u0430\u0435\u0442 \u0430\u043d\u0430\u043b\u0438\u0442\u0438\u0447\u0435\u0441\u043a\u0438 \u043e\u0442\u0434\u0435\u043b - \u044d\u0442\u043e\u0442 \u043f\u0440\u0438\u043e\u0440\u0438\u0442\u0435\u0442 \u0435\u043c\u0443 \u043d\u0435 \u044f\u0441\u0435\u043d. \u0414\u0435\u043b\u043e \u0432 \u0442\u043e\u043c, \u0447\u0442\u043e \u0443 \u043d\u0430\u0441 \u0440\u0435\u0433\u0443\u043b\u044f\u0440\u043d\u043e \u0437\u0430\u043f\u0430\u0437\u0434\u0432\u0430\u0435\u0442 \u043e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u044c, \u044f \u0442\u0430\u043a \u0440\u0430\u0431\u043e\u0442\u0430\u0442\u044c \u043d\u0435 \u043c\u043e\u0433\u0443 - \u0434\u0430\u043d\u043d\u044b\u0435 \u043f\u0440\u0438\u0445\u043e\u0434\u044f\u0442 \u0442\u043e\u0433\u0434\u0430, \u043a\u043e\u0433\u0434\u044f \u044f \u0443\u0436\u0435 \u0432\u0441\u0435 \u043f\u0440\u043e\u0434\u0430\u043b\u0430, \u0438 \u043f\u043b\u0430\u043d\u0438\u0440\u043e\u0432\u0430\u0442\u044c \u043f\u043e\u0437\u0434\u043d\u043e. \u0422\u0430\u043a\u0438\u043c \u043e\u0431\u0440\u0430\u0437\u043e\u043c, \u044f \u0438 \u043c\u043e\u0438 \u043b\u044e\u0434\u0438 \u043f\u043e\u0441\u0442\u043e\u044f\u043d\u043d\u043e \u0434\u0435\u0439\u0441\u0442\u0432\u0443\u0435\u043c \u0432\u0441\u043b\u0435\u043f\u0443\u044e!  \u0418 \u044d\u0442\u043e \u043f\u043e \u0442\u0440\u0435\u043c \u0442\u044b\u0441\u044f\u0447\u0430\u043c \u043a\u043b\u0438\u0435\u043d\u0442\u043e\u0432!  \u041a\u0430\u043a\u0438\u0435 \u0443 \u0432\u0430\u0441 \u0435\u0441\u0442\u044c \u043c\u043d\u0435\u043d\u0438\u044f, \u0447\u0442\u043e \u0441 \u044d\u0442\u0438\u043c \u0434\u0435\u043b\u0430\u0442\u044c. ",
                        "template":"M1",
                        "sentAt":"04.10.2012 15:30",
                        "sender":"\u0421\u043a\u043e\u0440\u043e\u0431\u0435\u0439 \u0410.\u041c. <skorobey.am@skiliks.com>",
                        "receiver":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>,\u0414\u0435\u043d\u0435\u0436\u043d\u0430\u044f \u0420.\u0420. <denezhnaya.rr@skiliks.com>,\u0412\u0430\u0441\u0438\u043b\u0438\u0439 \u0411\u043e\u0431\u0440 <bobr.vs@skiliks.com>,\u0415\u0433\u043e\u0440 \u0422\u0440\u0443\u0434\u044f\u043a\u0438\u043d <trudyakin.ek@skiliks.com>",
                        "copy":"",
                        "readed":"0",
                        "attachments":0,
                        "subjectSort":"\u0441\u0440\u043e\u0447\u043d\u043e! \u043e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u044c"
                    }
                },
                "type":"inbox"
            };

            var replay_all = {
                "result":1,
                "subjectId":"1278",
                "subject":"Re: \u0441\u0440\u043e\u0447\u043d\u043e! \u041e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u044c",
                "receiver":"\u0421\u043a\u043e\u0440\u043e\u0431\u0435\u0439 \u0410.\u041c. <skorobey.am@skiliks.com>",
                "receiver_id":"9",
                "copiesIds":"2,11,12",
                "copies":"\u0414\u0435\u043d\u0435\u0436\u043d\u0430\u044f \u0420.\u0420. <denezhnaya.rr@skiliks.com>,\u0412\u0430\u0441\u0438\u043b\u0438\u0439 \u0411\u043e\u0431\u0440 <bobr.vs@skiliks.com>,\u0415\u0433\u043e\u0440 \u0422\u0440\u0443\u0434\u044f\u043a\u0438\u043d <trudyakin.ek@skiliks.com>",
                "phrases":{
                    "message":"",
                    "data":[

                    ],
                    "previouseMessage":"\u0414\u043e\u0431\u0440\u044b\u0439 \u0434\u0435\u043d\u044c, \u043a\u043e\u043b\u043b\u0435\u0433\u0438! \n\u042f, \u043a\u0430\u043a \u0432\u0441\u0435\u0433\u0434\u0430, \u043f\u043e \u0441\u0440\u043e\u0447\u043d\u043e\u043c\u0443 \u0432\u043e\u043f\u0440\u043e\u0441\u0443. \u041d\u0438 \u0443 \u043a\u043e\u0433\u043e \u0432\u0435\u0434\u044c \u043d\u0435\u0442 \u0441\u043e\u043c\u043d\u0435\u043d\u0438\u0439, \u0447\u0442\u043e \u043f\u0440\u043e\u0434\u0430\u0436\u0438 \u043a\u0440\u0430\u0439\u043d\u0435 \u0432\u0430\u0436\u043d\u044b \u0434\u043b\u044f \u043a\u043e\u043c\u043f\u0430\u043d\u0438\u0438?! \u0421\u0443\u0434\u044f \u043f\u043e \u0442\u043e\u043c\u0443, \u043a\u0430\u043a \u0443 \u043d\u0430\u0441 \u0440\u0430\u0431\u043e\u0442\u0430\u0435\u0442 \u0430\u043d\u0430\u043b\u0438\u0442\u0438\u0447\u0435\u0441\u043a\u0438 \u043e\u0442\u0434\u0435\u043b - \u044d\u0442\u043e\u0442 \u043f\u0440\u0438\u043e\u0440\u0438\u0442\u0435\u0442 \u0435\u043c\u0443 \u043d\u0435 \u044f\u0441\u0435\u043d. \u0414\u0435\u043b\u043e \u0432 \u0442\u043e\u043c, \u0447\u0442\u043e \u0443 \u043d\u0430\u0441 \u0440\u0435\u0433\u0443\u043b\u044f\u0440\u043d\u043e \u0437\u0430\u043f\u0430\u0437\u0434\u0432\u0430\u0435\u0442 \u043e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u044c, \u044f \u0442\u0430\u043a \u0440\u0430\u0431\u043e\u0442\u0430\u0442\u044c \u043d\u0435 \u043c\u043e\u0433\u0443 - \u0434\u0430\u043d\u043d\u044b\u0435 \u043f\u0440\u0438\u0445\u043e\u0434\u044f\u0442 \u0442\u043e\u0433\u0434\u0430, \u043a\u043e\u0433\u0434\u044f \u044f \u0443\u0436\u0435 \u0432\u0441\u0435 \u043f\u0440\u043e\u0434\u0430\u043b\u0430, \u0438 \u043f\u043b\u0430\u043d\u0438\u0440\u043e\u0432\u0430\u0442\u044c \u043f\u043e\u0437\u0434\u043d\u043e. \u0422\u0430\u043a\u0438\u043c \u043e\u0431\u0440\u0430\u0437\u043e\u043c, \u044f \u0438 \u043c\u043e\u0438 \u043b\u044e\u0434\u0438 \u043f\u043e\u0441\u0442\u043e\u044f\u043d\u043d\u043e \u0434\u0435\u0439\u0441\u0442\u0432\u0443\u0435\u043c \u0432\u0441\u043b\u0435\u043f\u0443\u044e!  \u0418 \u044d\u0442\u043e \u043f\u043e \u0442\u0440\u0435\u043c \u0442\u044b\u0441\u044f\u0447\u0430\u043c \u043a\u043b\u0438\u0435\u043d\u0442\u043e\u0432!  \u041a\u0430\u043a\u0438\u0435 \u0443 \u0432\u0430\u0441 \u0435\u0441\u0442\u044c \u043c\u043d\u0435\u043d\u0438\u044f, \u0447\u0442\u043e \u0441 \u044d\u0442\u0438\u043c \u0434\u0435\u043b\u0430\u0442\u044c. ",
                    "addData":[

                    ]
                }
            };

            var receivers = [{"id":"42","title":"\u041d\u0430\u0447\u0430\u043b\u044c\u043d\u0438\u043a \u041e\u0410\u0438\u041f","fio":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412.","email":"fedorov.av@skiliks.com","code":"1","phone":"+7(926)720-1148","has_mail_theme":"0","has_phone_theme":"0"},{"id":"43","title":"\u0424\u0438\u043d.\u0434\u0438\u0440\u0435\u043a\u0442\u043e\u0440","fio":"\u0414\u0435\u043d\u0435\u0436\u043d\u0430\u044f \u0420.\u0420.","email":"denezhnaya.rr@skiliks.com","code":"2","phone":"+7(926)720-0012","has_mail_theme":"0","has_phone_theme":"0"},{"id":"44","title":"\u0410\u043d\u0430\u043b\u0438\u0442\u0438\u043a","fio":"\u0422\u0440\u0443\u0442\u043d\u0435\u0432 \u0421.","email":"trutnev.ss@skiliks.com","code":"3","phone":"+7(926)720-1172","has_mail_theme":"0","has_phone_theme":"0"},{"id":"45","title":"\u0412\u0435\u0434.\u0430\u043d\u0430\u043b\u0438\u0442\u0438\u043a","fio":"\u041a\u0440\u0443\u0442\u044c\u043a\u043e \u041c.","email":"krutko.ma@skiliks.com","code":"4","phone":"+7(926)720-1173","has_mail_theme":"0","has_phone_theme":"0"},{"id":"46","title":"\u0410\u043d\u0430\u043b\u0438\u0442\u0438\u043a","fio":"\u041b\u043e\u0448\u0430\u0434\u043a\u0438\u043d \u041c.","email":"loshadkin.ms@skiliks.com","code":"5","phone":"+7(926)720-1174","has_mail_theme":"0","has_phone_theme":"0"},{"id":"47","title":"\u0413\u0435\u043d.\u0434\u0438\u0440\u0435\u043a\u0442\u043e\u0440","fio":"\u0411\u043e\u0441\u0441 \u0412.\u0421.","email":"boss@skiliks.com","code":"6","phone":"+7(926)720-0000","has_mail_theme":"0","has_phone_theme":"0"},{"id":"48","title":"\u0410\u0441\u0441\u0438\u0441\u0442\u0435\u043d\u0442 \u0413\u0414","fio":"\u0414\u043e\u043b\u0433\u043e\u0432\u0430 \u041d.\u0422.","email":"dolgova.nt@skiliks.com","code":"7","phone":"+7(926)720-0002","has_mail_theme":"0","has_phone_theme":"0"},{"id":"49","title":"\u0414\u0438\u0440.\u0440\u0430\u0437\u0432\u0438\u0442\u0438\u0435","fio":"\u041e\u043b\u0435\u0433 \u0420\u0430\u0437\u0443\u043c\u043d\u044b\u0439","email":"razumniy.or@skiliks.com","code":"8","phone":"+7(926)720-0014","has_mail_theme":"0","has_phone_theme":"0"},{"id":"50","title":"\u0414\u0438\u0440.\u043f\u0440\u043e\u0434\u0430\u0436\u0438","fio":"\u0421\u043a\u043e\u0440\u043e\u0431\u0435\u0439 \u0410.\u041c.","email":"skorobey.am@skiliks.com","code":"9","phone":"+7(926)720-0016","has_mail_theme":"0","has_phone_theme":"0"},{"id":"51","title":"\u041d\u0430\u0447.\u043e\u0442\u0434\u0435\u043b\u0430 \u0418\u0422","fio":"\u0416\u0435\u043b\u0435\u0437\u043d\u044b\u0439 \u0421.","email":"zhelezniy.so@skiliks.com","code":"10","phone":"+7(926)720-1212","has_mail_theme":"0","has_phone_theme":"0"},{"id":"52","title":"\u041d\u0430\u0447.\u043f\u0440\u043e\u0438\u0437\u0432-\u0432\u0430","fio":"\u0412\u0430\u0441\u0438\u043b\u0438\u0439 \u0411\u043e\u0431\u0440","email":"bobr.vs@skiliks.com","code":"11","phone":"+7(926)720-1113","has_mail_theme":"0","has_phone_theme":"0"},{"id":"53","title":"\u041d\u0430\u0447. \u043b\u043e\u0433\u0438\u0441\u0442\u0438\u043a\u0438","fio":"\u0415\u0433\u043e\u0440 \u0422\u0440\u0443\u0434\u044f\u043a\u0438\u043d","email":"trudyakin.ek@skiliks.com","code":"12","phone":"+7(926)720-1114","has_mail_theme":"0","has_phone_theme":"0"},{"id":"54","title":"\u041d\u0430\u0447.HR \u043e\u0442\u0434\u0435\u043b","fio":"\u041b\u044e\u0434\u043e\u0432\u043a\u0438\u043d\u0430 \u0421.","email":"lyudovkina.sm@skiliks.com","code":"13","phone":"+7(926)720-1115","has_mail_theme":"0","has_phone_theme":"0"},{"id":"55","title":"\u041d\u0430\u0447.\u0410\u0425\u041e","fio":"\u0412\u0430\u0441\u0438\u043b\u0438\u0439 \u0425\u043e\u0437\u0438\u043d","email":"khozin.vk@skiliks.com","code":"14","phone":"+7(926)720-1116","has_mail_theme":"0","has_phone_theme":"0"},{"id":"56","title":"\u041d\u0430\u0447.\u0441\u043b.\u0430\u0443\u0434\u0438\u0442\u0430","fio":"\u0422\u043e\u0447\u043d\u044b\u0445 \u0410.","email":"tochnykh.ay@skiliks.com","code":"15","phone":"+7(926)720-1117","has_mail_theme":"0","has_phone_theme":"0"},{"id":"57","title":"\u0421\u043f\u0435\u0446.\u0441\u043b.\u043a\u0430\u0434\u0440\u043e\u0432","fio":"\u0421\u0435\u043c\u0435\u043d\u043e\u0432\u0430 \u041e.","email":"semenova.oo@skiliks.com","code":"16","phone":"+7(926)720-1118","has_mail_theme":"0","has_phone_theme":"0"},{"id":"58","title":"\u041e\u0442\u0434\u0435\u043b \u043f\u0435\u0440\u0441\u043e\u043d\u0430\u043b\u0430","fio":"\u0410\u043d\u043d\u0430 \u0416\u0443\u043a\u043e\u0432\u0430","email":"zhukova.ar@skiliks.com","code":"17","phone":"+7(926)720-1119","has_mail_theme":"0","has_phone_theme":"0"},{"id":"59","title":"\u042e\u0440\u0438\u0441\u0442","fio":"\u0410\u0434\u0432\u043e\u043a\u0430\u0442\u043e\u0432 \u042e.","email":"advokatov.yv@skiliks.com","code":"18","phone":"+7(926)720-1120","has_mail_theme":"0","has_phone_theme":"0"},{"id":"60","title":"\u0411\u0443\u0445\u0433\u0430\u043b\u0442\u0435\u0440","fio":"\u0424\u0430\u0438\u043d\u0430 \u0413\u043e\u043b\u044c\u0446","email":"golts.fe@skiliks.com","code":"19","phone":"+7(926)720-1121","has_mail_theme":"0","has_phone_theme":"0"},{"id":"61","title":"\u0418\u0441\u043f.\u0434\u0438\u0440.\u0440\u0435\u0433. \u0414","fio":"\u041a\u0430\u043c\u0435\u043d\u0441\u043a\u0438\u0439 \u0412.","email":"kamenskiy.vp@region.skiliks.com","code":"20","phone":"+7(909)726-1528","has_mail_theme":"0","has_phone_theme":"0"},{"id":"62","title":"\u041d\u0430\u0447.\u043f\u0440\u043e\u0434.\u0440\u0435\u0433.\u0410","fio":"\u0412\u0430\u0441\u0438\u043b\u044c\u0435\u0432 \u0410.","email":"vasiliev.aa@region.skiliks.com","code":"21","phone":"+7(915)124-6617","has_mail_theme":"0","has_phone_theme":"0"},{"id":"63","title":"\u0421\u043f\u0435\u0446.\u043e\u0442\u0434. \u0418\u0422","fio":"\u042e\u0440\u0438\u0439 \u041c\u044f\u0433\u043a\u043e\u0432","email":"myagkov.ys@skiliks.com","code":"22","phone":"+7(926)720-1122","has_mail_theme":"0","has_phone_theme":"0"},{"id":"64","title":"\u0421\u043f\u0435\u0446.\u043e\u0442\u0434.\u0440\u0435\u043a\u043b.","fio":"\u041f\u0435\u0442\u0440\u0430\u0448\u0435\u0432\u0438\u0447 \u0418.","email":"petrashevich.iv@skiliks.com","code":"23","phone":"+7(926)720-1150","has_mail_theme":"0","has_phone_theme":"0"},{"id":"65","title":"\u0421\u043f\u0435\u0446.\u043e\u0442\u0434.\u043f\u0440\u043e\u0434\u0430\u0436","fio":"\u0410\u043d\u0442\u043e\u043d \u0421\u0435\u0440\u043a\u043e\u0432","email":"serkov.af@skiliks.com","code":"24","phone":"+7(926)720-1123","has_mail_theme":"0","has_phone_theme":"0"},{"id":"66","title":"\u041a\u043b\u0438\u0435\u043d\u0442","fio":"\u0414\u043e\u0431\u0440\u043e\u0445\u043e\u0442\u043e\u0432 \u0418.","email":"dobrokhotov@gmail.com","code":"25","phone":"+7(915)828-1421","has_mail_theme":"0","has_phone_theme":"0"},{"id":"67","title":"\u041a\u043e\u043d\u0441\u0443\u043b\u044c\u0442\u0430\u043d\u0442","fio":"\u0410\u043d\u0436\u0435\u043b\u0430 \u0411\u043b\u0435\u0441\u043a","email":"blesk@mckinsey.com","code":"26","phone":"+7(495)765-0051","has_mail_theme":"0","has_phone_theme":"0"},{"id":"68","title":"\u0421\u0443\u043f\u0440\u0443\u0433\u0430","fio":"\u041b\u044e\u0431\u0438\u043c\u0430\u044f \u0436\u0435\u043d\u0430","email":"lapochka@gmail.com","code":"27","phone":"+7(905)343-1722","has_mail_theme":"0","has_phone_theme":"0"},{"id":"69","title":"\u0414\u0440\u0443\u0433","fio":"\u041f\u0435\u0442\u0440 \u041f\u043e\u0433\u043e\u0434\u043a\u0438\u043d","email":"petya1984@gmail.com","code":"28","phone":"+7(909)812-9243","has_mail_theme":"0","has_phone_theme":"0"},{"id":"70","title":"\u0417\u043d\u0430\u043a\u043e\u043c\u044b\u0439","fio":"\u041e\u043b\u0435\u0433 \u0421\u043a\u043e\u0440\u043a\u0438\u043d","email":"ckorkin@gmail.com","code":"29","phone":"+7(926)544-5345","has_mail_theme":"0","has_phone_theme":"0"},{"id":"71","title":"\u0411\u0440\u0430\u0442","fio":"\u0421\u0435\u0440\u0435\u0433\u0430","email":"serjio@gmail.com","code":"30","phone":"+7(905)818-0643","has_mail_theme":"0","has_phone_theme":"0"},{"id":"72","title":"\u0413\u0414 \u041b\u0435\u0433\u043a\u0438\u0439 \u043f\u043e\u043b\u0435\u0442","fio":"\u0421\u0442\u0435\u043f\u0430\u043d\u043e\u0432 \u0421.","email":"stepanov@lpolet.com","code":"31","phone":"+7(909)989-9543","has_mail_theme":"0","has_phone_theme":"0"},{"id":"73","title":"\u041e\u0434\u043d\u043e\u043a\u0443\u0440\u0441\u043d\u0438\u0446\u0430","fio":"\u041c\u0430\u0440\u0438\u043d\u043a\u0430","email":"marina_pet@gmail.com","code":"32","phone":"+7(909)121-7654","has_mail_theme":"0","has_phone_theme":"0"},{"id":"74","title":"\u041e\u0440\u0433.\u043a\u043e\u043d\u0444\u0435\u0440\u0435\u043d\u0446\u0438\u0438","fio":"\u041e.\u0418.\u0418\u0432\u0430\u043d\u043e\u0432\u0430","email":"ivanova@businessanalytycs.com","code":"33","phone":"+7(915)675-0532","has_mail_theme":"0","has_phone_theme":"0"},{"id":"75","title":"\u041e\u0445\u0440\u0430\u043d\u043d\u0438\u043a","fio":"\u041e\u0445\u0440\u0430\u043d\u043d\u0438\u043a","email":"","code":"34","phone":"+7(495)720-1225","has_mail_theme":"0","has_phone_theme":"0"},{"id":"76","title":"\u0420\u0435\u043c\u043e\u043d\u0442 \u043a\u043e\u043d\u0434\u0438\u0446.","fio":"\u0420\u0435\u043c\u043e\u043d\u0442 \u043a\u043e\u043d\u0434\u0438\u0446.","email":"","code":"35","phone":"+7(495)720-1265","has_mail_theme":"0","has_phone_theme":"0"},{"id":"77","title":"\u0428\u0438\u043d\u043e\u043c\u043e\u043d\u0442\u0430\u0436","fio":"\u0428\u0438\u043d\u043e\u043c\u043e\u043d\u0442\u0430\u0436","email":"","code":"36","phone":"+7(495)422-1187","has_mail_theme":"0","has_phone_theme":"0"},{"id":"78","title":"\u0412\u0435\u0441\u044c \u043e\u0444\u0438\u0441","fio":"\u0412\u0435\u0441\u044c \u043e\u0444\u0438\u0441","email":"office@skiliks.com","code":"37","phone":"","has_mail_theme":"0","has_phone_theme":"0"},{"id":"79","title":"\u041c\u0435\u043d\u0435\u0434\u0436\u0435\u0440\u044b","fio":"\u041c\u0435\u043d\u0435\u0434\u0436\u0435\u0440\u044b","email":"manager@skiliks.com","code":"38","phone":"","has_mail_theme":"0","has_phone_theme":"0"},{"id":"80","title":"\u0412\u0441\u0435 \u0430\u043d\u0430\u043b\u0438\u0442\u0438\u043a\u0438","fio":"\u0412\u0441\u0435 \u0430\u043d\u0430\u043b\u0438\u0442\u0438\u043a\u0438","email":"analitics@skiliks.com","code":"39","phone":"","has_mail_theme":"0","has_phone_theme":"0"},{"id":"81","title":"\u041d\u0435\u0438\u0437\u0432\u0435\u0441\u0442\u043d\u0430\u044f","fio":"\u041d\u0435\u0438\u0437\u0432\u0435\u0441\u0442\u043d\u0430\u044f","email":"","code":"40","phone":"+7(495)811-1515","has_mail_theme":"0","has_phone_theme":"0"},{"id":"82","title":"\u0413\u0414 \u041b\u0443\u0447","fio":"\u0413\u043e\u0440\u0431\u0430\u0442\u044e\u043a \u0415.\u0414.","email":"gorbatyuk@luch.com","code":"41","phone":"+7(495)248-1416","has_mail_theme":"0","has_phone_theme":"0"}];

            _.templateSettings.interpolate = /<@=(.+?)@>/g;
            _.templateSettings.evaluate = /<@(.+?)@>/g;
            var server;
            before(function () {
                server = sinon.fakeServer.create();

                server.respondWith("POST", "/index.php/simulation/start",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:1})]);

                server.respondWith("POST", "/index.php/mail/MarkRead",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:1})]);

                server.respondWith("POST", "/index.php/myDocuments/getList",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:1})]);

                server.respondWith("POST", "/index.php/mail/getMessages",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify(inbox)]);

                server.respondWith("POST", "/index.php/mail/replyAll",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify(replay_all)]);

                server.respondWith("POST", "/index.php/todo/get",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:1})]);

                server.respondWith("POST", "/index.php/dayPlan/get",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:1})]);

                server.respondWith("POST", "/index.php/character/list",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify(receivers)]);

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
                                "result": 1,
                                "data":{"1":"phrase 1", "2":"phrase 2", "3":"phrase 3"},
                                "addData":{"3613":".","3614":",","3615":":","3616":"\"","3617":"-","3618":";"},
                                "message":""}
                        )]);

                //clock = sinon.useFakeTimers();
                //this.timeout = 10000;
                window.SKApp = new SKApplication({'start':'9:00', "skiliksSpeedFactor":8 });
                this.timeout = 1000;
            });
            after(function () {
                server.restore();
            });

            it("reply all m1", function (done) {
                //init simulation
                var simulation = SKApp.simulation;
                simulation.start();
                server.respond();
                var mail_window = new SKWindow({name:'mailEmulator', subname:'mailMain'});
                mail_window.open();

                buster.assert.defined(simulation.mailClient);

                var mailClient = new SKMailClientView({model_instance:mail_window});
                mailClient.render();
                server.respond();

                // ? (Check is email in list has right subject?)
                expect(mailClient.$('tr[data-email-id=1278] td.mail-emulator-received-list-cell-theme').text())
                    .toBe('срочно! Отчетность');

                mailClient.$('tr[data-email-id=1278] td.mail-emulator-received-list-cell-theme').click();
                server.respond();

                mailClient.renderReplyAllScreen();
                server.respond();

                expect(SKApp.simulation.characters.length).toBe(41);
                var email = mailClient.generateNewEmailObject();

                var validationDialogResult = mailClient.mailClient.validationDialogResult(email);

                // check is email valid
                expect(validationDialogResult).toBe(true);

                mailClient.doSendEmail();

                // check sendMessage request to server
                server.respondWith("POST", "/index.php/mail/sendMessage",
                    function (xhr) {
                        var data = {}
                        decodeURIComponent(xhr.requestBody).split('&')
                            .forEach(function (val) {
                                var vals = val.split('=');
                                data[vals[0]] = vals[1];
                            });
                        expect(data).toEqual({
                            copies:    "",
                            fileId:    "",
                            messageId: "1274",
                            phrases:   "",
                            receivers: "9,",
                            subject:   "1278",
                            time:      "09:00",
                            letterType: "replyAll"
                        });
                        xhr.respond(200,  { "Content-Type": "application/json" }, JSON.stringify({'result':1}));
                        done();
                    });

                server.respond(); // to protect against Fail by response timeout
            });

            it("has characters for replyAll", function () {
                SKApp.simulation.start();
                server.respond();
                var client = new SKMailClient();
                client.updateRecipientsList();
                expect(client.getFormatedCharacterList()).toEqual(["Федоров А.В.", "Денежная Р.Р.", "Трутнев С.", "Крутько М.", "Лошадкин М.", "Босс В.С.", "Долгова Н.Т.", "Олег Разумный", "Скоробей А.М.", "Железный С.", "Василий Бобр", "Егор Трудякин", "Людовкина С.", "Василий Хозин", "Точных А.", "Семенова О.", "Анна Жукова", "Адвокатов Ю.", "Фаина Гольц", "Каменский В.", "Васильев А.", "Юрий Мягков", "Петрашевич И.", "Антон Серков", "Доброхотов И.", "Анжела Блеск", "Любимая жена", "Петр Погодкин", "Олег Скоркин", "Серега", "Степанов С.", "Маринка", "О.И.Иванова"]);
           });
        });
    });
});