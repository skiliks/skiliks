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

            var receivers = {
                "result":1,
                "data":{
                    "1":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>",
                    "2":"\u0414\u0435\u043d\u0435\u0436\u043d\u0430\u044f \u0420.\u0420. <denezhnaya.rr@skiliks.com>",
                    "3":"\u0422\u0440\u0443\u0442\u043d\u0435\u0432 \u0421. <trutnev.ss@skiliks.com>",
                    "4":"\u041a\u0440\u0443\u0442\u044c\u043a\u043e \u041c. <krutko.ma@skiliks.com>",
                    "5":"\u041b\u043e\u0448\u0430\u0434\u043a\u0438\u043d \u041c. <loshadkin.ms@skiliks.com>",
                    "6":"\u0411\u043e\u0441\u0441 \u0412.\u0421. <boss@skiliks.com>",
                    "7":"\u0414\u043e\u043b\u0433\u043e\u0432\u0430 \u041d.\u0422. <dolgova.nt@skiliks.com>",
                    "8":"\u041e\u043b\u0435\u0433 \u0420\u0430\u0437\u0443\u043c\u043d\u044b\u0439 <razumniy.or@skiliks.com>",
                    "9":"\u0421\u043a\u043e\u0440\u043e\u0431\u0435\u0439 \u0410.\u041c. <skorobey.am@skiliks.com>",
                    "10":"\u0416\u0435\u043b\u0435\u0437\u043d\u044b\u0439 \u0421. <zhelezniy.so@skiliks.com>",
                    "11":"\u0412\u0430\u0441\u0438\u043b\u0438\u0439 \u0411\u043e\u0431\u0440 <bobr.vs@skiliks.com>",
                    "12":"\u0415\u0433\u043e\u0440 \u0422\u0440\u0443\u0434\u044f\u043a\u0438\u043d <trudyakin.ek@skiliks.com>",
                    "13":"\u041b\u044e\u0434\u043e\u0432\u043a\u0438\u043d\u0430 \u0421. <lyudovkina.sm@skiliks.com>",
                    "14":"\u0412\u0430\u0441\u0438\u043b\u0438\u0439 \u0425\u043e\u0437\u0438\u043d <khozin.vk@skiliks.com>",
                    "15":"\u0422\u043e\u0447\u043d\u044b\u0445 \u0410. <tochnykh.ay@skiliks.com>",
                    "16":"\u0421\u0435\u043c\u0435\u043d\u043e\u0432\u0430 \u041e. <semenova.oo@skiliks.com>",
                    "17":"\u0410\u043d\u043d\u0430 \u0416\u0443\u043a\u043e\u0432\u0430 <zhukova.ar@skiliks.com>",
                    "18":"\u0410\u0434\u0432\u043e\u043a\u0430\u0442\u043e\u0432 \u042e. <advokatov.yv@skiliks.com>",
                    "19":"\u0424\u0430\u0438\u043d\u0430 \u0413\u043e\u043b\u044c\u0446 <golts.fe@skiliks.com>",
                    "20":"\u041a\u0430\u043c\u0435\u043d\u0441\u043a\u0438\u0439 \u0412. <kamenskiy.vp@region.skiliks.com>",
                    "21":"\u0412\u0430\u0441\u0438\u043b\u044c\u0435\u0432 \u0410. <vasiliev.aa@region.skiliks.com>",
                    "22":"\u042e\u0440\u0438\u0439 \u041c\u044f\u0433\u043a\u043e\u0432 <myagkov.ys@skiliks.com>",
                    "23":"\u041f\u0435\u0442\u0440\u0430\u0448\u0435\u0432\u0438\u0447 \u0418. <petrashevich.iv@skiliks.com>",
                    "24":"\u0410\u043d\u0442\u043e\u043d \u0421\u0435\u0440\u043a\u043e\u0432 <serkov.af@skiliks.com>",
                    "25":"\u0414\u043e\u0431\u0440\u043e\u0445\u043e\u0442\u043e\u0432 \u0418. <dobrokhotov@gmail.com>",
                    "26":"\u0410\u043d\u0436\u0435\u043b\u0430 \u0411\u043b\u0435\u0441\u043a <blesk@mckinsey.com>",
                    "27":"\u041b\u044e\u0431\u0438\u043c\u0430\u044f \u0436\u0435\u043d\u0430 <lapochka@gmail.com>",
                    "28":"\u041f\u0435\u0442\u0440 \u041f\u043e\u0433\u043e\u0434\u043a\u0438\u043d <petya1984@gmail.com>",
                    "29":"\u041e\u043b\u0435\u0433 \u0421\u043a\u043e\u0440\u043a\u0438\u043d <ckorkin@gmail.com>",
                    "30":"\u0421\u0435\u0440\u0435\u0433\u0430 <serjio@gmail.com>",
                    "31":"\u0421\u0442\u0435\u043f\u0430\u043d\u043e\u0432 \u0421. <stepanov@lpolet.com>",
                    "32":"\u041c\u0430\u0440\u0438\u043d\u043a\u0430 <marina_pet@gmail.com>",
                    "33":"\u041e.\u0418.\u0418\u0432\u0430\u043d\u043e\u0432\u0430 <ivanova@businessanalytycs.com>",
                    "34":" <>",
                    "35":" <>",
                    "36":" <>",
                    "37":" <>",
                    "38":" <>",
                    "39":" <>",
                    "40":" <>",
                    "41":"\u0413\u043e\u0440\u0431\u0430\u0442\u044e\u043a \u0415.\u0414. <gorbatyuk@luch.com>"
                }
            };

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

                server.respondWith("POST", "/index.php/mail/getReceivers",
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
                window.SKApp = new SKApplication();
                window.SKConfig = {'simulationStartTime':'9:00', "skiliksSpeedFactor":8 };
                SKApp.user = {};
                this.timeout = 1000;
            });
            after(function () {
                server.restore();
            });

            it("reply all m1", function (done) {
                var simulation = SKApp.user.simulation = new SKSimulation();
                simulation.start();
                var mail_window = new SKWindow({name:'mailEmulator', subname:'mailMain'});
                mail_window.open();
                buster.assert.defined(simulation.mailClient);
                var mail = new SKMailClientView({model_instance:mail_window});
                mail.render();
                server.respond();
                expect(mail.$('tr[data-email-id=1278] td.mail-emulator-received-list-cell-theme').text()).toBe('срочно! Отчетность');
                mail.$('tr[data-email-id=1278] td.mail-emulator-received-list-cell-theme').click();
                server.respond();
                mail.renderReplyAllScreen();
                server.respond();
                var email = mail.generateNewEmailObject();
                var validationDialogResult = mail.mailClient.validationDialogResult(email);
                expect(validationDialogResult).toBe(true);
                mail.doSendEmail();
                server.respondWith("POST", "/index.php/mail/sendMessage",
                    function (xhr) {
                        var data = {}
                        decodeURIComponent(xhr.requestBody).split('&')
                            .forEach(function (val) {
                                var vals = val.split('=');
                                data[vals[0]] = vals[1];
                            });
                        expect(data).toEqual({
                            copies: "",
                            fileId: "",
                            messageId: "1274",
                            phrases: "",
                            receivers: "9,",
                            subject: "1278",
                            time: "09:00"
                        });
                        xhr.respond(200,  { "Content-Type": "application/json" }, JSON.stringify({'result':1}));
                        done();
                    });
                server.respond();
            });

            it("has characters for replyAll", function () {
             //   console.log($('#MailClient_NewLetterSubject').val());
                var client = new SKMailClient();
                client.updateRecipientsList();
                expect(client.getFormatedCharacterList()).toEqual(["Федоров А.В.", "Денежная Р.Р.", "Трутнев С.", "Крутько М.", "Лошадкин М.", "Босс В.С.", "Долгова Н.Т.", "Олег Разумный", "Скоробей А.М.", "Железный С.", "Василий Бобр", "Егор Трудякин", "Людовкина С.", "Василий Хозин", "Точных А.", "Семенова О.", "Анна Жукова", "Адвокатов Ю.", "Фаина Гольц", "Каменский В.", "Васильев А.", "Юрий Мягков", "Петрашевич И.", "Антон Серков", "Доброхотов И.", "Анжела Блеск", "Любимая жена", "Петр Погодкин", "Олег Скоркин", "Серега", "Степанов С.", "Маринка", "О.И.Иванова", "Горбатюк Е.Д."]);
           });
        });
    });
});