/*global buster, sinon, describe, before, after, require */

/*
 Пересылка письма Трутневу и Крутько, проверка правильности выбранной темы
 */
buster.spec.expose();

define([
    "game/models/SKApplication",
    "game/models/SKSimulation",
    "game/views/mail/SKMailClientView",
    "game/models/window/SKWindow"], function (SKApplication, SKSimulation, SKMailClientView, SKWindow) {

    spec = describe('Forward M8', function (run) {
        "use strict";
        /**
         * @type {SKMailClientView} SKMailClientView
         */
        run(function () {
            var inbox = {
                "result":1,
                "messages":{
                    "4125":{
                        "id":"4125",
                        "subject":"\u041f\u043e \u0446\u0435\u043d\u043e\u0432\u043e\u0439 \u043f\u043e\u043b\u0438\u0442\u0438\u043a\u0435",
                        "text":"\u0414\u043e\u0431\u0440\u044b\u0439 \u0434\u0435\u043d\u044c! \n\n\u042f \u043d\u0435\u043c\u043d\u043e\u0433\u043e \u0441 \u043e\u043f\u0435\u0440\u0435\u0436\u0435\u043d\u0438\u0435\u043c \u0441\u0434\u0435\u043b\u0430\u043b\u0430 \u0440\u0430\u0431\u043e\u0442\u0443 \u043f\u043e \u0446\u0435\u043d\u043e\u0432\u043e\u0439 \u043f\u043e\u043b\u0438\u0442\u0438\u043a\u0435 (\u0432\u0447\u0435\u0440\u0430 \u0432\u044b\u0434\u0430\u043b\u0441\u044f \u0441\u0432\u043e\u0431\u043e\u0434\u043d\u044b\u0439 \u0432\u0435\u0447\u0435\u0440). \u041c\u043d\u0435 \u043a\u0430\u0436\u0435\u0442\u0441\u044f, \u0447\u0442\u043e \u044f \u043e\u0442\u0440\u0430\u0437\u0438\u043b\u0430 \u0432\u0441\u0435 \u043c\u044b\u0441\u043b\u0438, \u043a\u043e\u0442\u043e\u0440\u044b\u0435 \u043c\u044b \u043e\u0431\u0441\u0443\u0436\u0434\u0430\u043b\u0438 \u043d\u0430 \u0443\u0441\u0442\u0430\u043d\u043e\u0432\u043e\u0447\u043d\u043e\u0439 \u0432\u0441\u0442\u0440\u0435\u0447\u0435. \u0411\u0443\u0434\u0435\u0442 \u0432\u0440\u0435\u043c\u044f \u0432 \u043e\u0442\u043f\u0443\u0441\u043a\u0435 - \u043f\u043e\u0441\u043c\u043e\u0442\u0440\u0438\u0442\u0435. \n\n\u0421 \u0443\u0432\u0430\u0436\u0435\u043d\u0438\u0435\u043c, \u041c\u0430\u0440\u0438\u043d\u0430 \u041a\u0440\u0443\u0442\u044c\u043a\u043e  \n\u0412\u0435\u0434\u0443\u0449\u0438\u0439 \u0430\u043d\u0430\u043b\u0438\u0442\u0438\u043a \u043e\u0442\u0434\u0435\u043b\u0430 \u0430\u043d\u0430\u043b\u0438\u0437\u0430 \u0438 \u043f\u043b\u0430\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u044f",
                        "template":"MY1",
                        "sentAt":"03.10.2012 10:32",
                        "sender":"\u041a\u0440\u0443\u0442\u044c\u043a\u043e \u041c. <krutko.ma@skiliks.com>",
                        "receiver":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>",
                        "copy":"",
                        "readed":"1",
                        "attachments":1,
                        "subjectSort":"\u043f\u043e \u0446\u0435\u043d\u043e\u0432\u043e\u0439 \u043f\u043e\u043b\u0438\u0442\u0438\u043a\u0435",
                        "attachmentName":"\u0426\u0435\u043d\u043e\u0432\u0430\u044f \u043f\u043e\u043b\u0438\u0442\u0438\u043a\u0430.xlsx",
                        "attachmentId":"815",
                        "attachmentFileId":"986"
                    },
                    "4126":{
                        "id":"4126",
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
                    "4127":{
                        "id":"4127",
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
                        "attachmentId":"816",
                        "attachmentFileId":"987"
                    },
                    "4128":{
                        "id":"4128",
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
                        "attachmentId":"817",
                        "attachmentFileId":"988"
                    },
                    "4136":{
                        "id":"4136",
                        "subject":"!\u043f\u0440\u043e\u0431\u043b\u0435\u043c\u0430 \u0441 \u0441\u0435\u0440\u0432\u0435\u0440\u043e\u043c!",
                        "text":"\u0410\u043b\u0435\u043a\u0441\u0435\u0439, \u0441\u0440\u043e\u0447\u043d\u044b\u0439 \u0432\u043e\u043f\u0440\u043e\u0441! \n\n\u041c\u044b \u043f\u0440\u043e\u0442\u0435\u0441\u0442\u0438\u0440\u043e\u0432\u0430\u043b\u0438 \u0432\u0430\u0448 \u0441\u0435\u0440\u0432\u0435\u0440 \u043d\u0430 \u043f\u0440\u0435\u0434\u043c\u0435\u0442 \u0432\u043e\u0441\u0441\u0442\u0430\u043d\u043e\u0432\u043b\u0435\u043d\u0438\u044f \u0438 \u0430\u043f-\u0433\u0440\u0435\u0439\u0434\u0430. \u0420\u0435\u0431\u044f\u0442\u0430 \u0432\u043e\u0437\u0438\u043b\u0438\u0441\u044c \u0441 \u043d\u0438\u043c \u043f\u043e\u0447\u0442\u0438 \u043d\u0435\u0434\u0435\u043b\u044e, \u043a \u0441\u043e\u0436\u0430\u043b\u0435\u043d\u0438\u044e, \u0432\u043e\u0441\u0441\u0442\u0430\u043d\u043e\u0432\u043b\u0435\u043d\u0438\u044e \u0438 \u0440\u0435\u043c\u043e\u043d\u0442\u0443 \u043e\u043d \u043d\u0435 \u043f\u043e\u0434\u043b\u0435\u0436\u0438\u0442! \u041d\u0443\u0436\u043d\u043e \u043c\u0435\u043d\u044f\u0442\u044c \u0441\u0440\u043e\u0447\u043d\u043e! \n\n\u0412 \u043d\u0430\u0448\u0435\u043c \u0431\u044e\u0434\u0436\u0435\u0442\u0435 \u0434\u0435\u043d\u0435\u0433 \u043d\u0430 \u0441\u043c\u0435\u043d\u0443 \u0441\u0435\u0440\u0432\u0435\u0440\u0430 \u043d\u0435 \u043f\u0440\u0435\u0434\u0443\u0441\u043c\u043e\u0442\u0440\u0435\u043d\u043e. \u042f \u0442\u0430\u043a \u043f\u043e\u043d\u0438\u043c\u0430\u044e, \u0442\u044b \u0442\u043e\u0436\u0435 \u0442\u0430\u043a\u043e\u0433\u043e \u0444\u043e\u0440\u0441-\u043c\u0430\u0436\u043e\u0440\u0430 \u043d\u0435 \u043f\u0440\u0435\u0434\u0432\u0438\u0434\u0435\u043b?!\n\n\u041f\u0438\u0448\u0438 \u0441\u043b\u0443\u0436\u0435\u0431\u043a\u0443 \u043d\u0430 \u0434\u0435\u043d\u044c\u0433\u0438. \u041d\u0430\u0434\u043e \u0425\u0425 \u0442\u044b\u0441. \u043d\u0430 \u0415\u04255 \u0441\u0435\u0440\u0432\u0435\u0440 (\u043c\u0435\u043d\u0435\u0435 \u043c\u043e\u0449\u043d\u044b\u0439 \u043f\u043e\u043a\u0443\u043f\u0430\u0442\u044c \u0431\u0435\u0441\u0441\u043c\u044b\u0441\u0441\u043b\u0435\u043d\u043e). \n\n\u041f\u0440\u043e\u0442\u044f\u043d\u0435\u0448\u044c - \u0431\u0443\u0434\u0435\u043c \u0440\u0430\u0437\u0433\u0440\u0435\u0431\u0430\u0442\u044c \u043f\u0440\u043e\u0431\u043b\u0435\u043c\u044b \u0441 \u043f\u043e\u0442\u0435\u0440\u0435\u0439 \u0438\u043d\u0444\u043e\u0440\u043c\u0430\u0446\u0438\u0438. \u041c\u043d\u0435 \u0432\u0430\u0448\u0438 \u043e\u0431\u044a\u0435\u043c\u044b \u0438\u043d\u0444\u043e\u0440\u043c\u0430\u0446\u0438\u0438 \u0445\u0440\u0430\u043d\u0438\u0442\u044c \u043d\u0435\u0433\u0434\u0435. \n\n\u0412\u0441\u0435\u0433\u043e, \u0421\u0435\u043c\u0435\u043d",
                        "template":"M8",
                        "sentAt":"04.10.2012 11:01",
                        "sender":"\u0416\u0435\u043b\u0435\u0437\u043d\u044b\u0439 \u0421. <zhelezniy.so@skiliks.com>",
                        "receiver":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>",
                        "copy":"",
                        "readed":"0",
                        "attachments":0,
                        "subjectSort":"!\u043f\u0440\u043e\u0431\u043b\u0435\u043c\u0430 \u0441 \u0441\u0435\u0440\u0432\u0435\u0440\u043e\u043c!"
                    }
                },
                "type":"inbox"
            };

            var forward = {
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

            var receivers = [
                {id:1, fio: "\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412.", email: "fedorov.av@skiliks.com"},
                {id:2, fio: "\u0414\u0435\u043d\u0435\u0436\u043d\u0430\u044f \u0420.\u0420.", email: "denezhnaya.rr@skiliks.com"},
                {id:3, fio: "\u0422\u0440\u0443\u0442\u043d\u0435\u0432 \u0421.", email: "trutnev.ss@skiliks.com"},
                {id:4, fio: "\u041a\u0440\u0443\u0442\u044c\u043a\u043e \u041c.", email: "krutko.ma@skiliks.com"},
                {id:5, fio: "\u041b\u043e\u0448\u0430\u0434\u043a\u0438\u043d \u041c.", email: "loshadkin.ms@skiliks.com"},
                {id:6, fio: "\u0411\u043e\u0441\u0441 \u0412.\u0421.", email: "boss@skiliks.com"},
                {id:7, fio: "\u0414\u043e\u043b\u0433\u043e\u0432\u0430 \u041d.\u0422.", email: "dolgova.nt@skiliks.com"},
                {id:8, fio: "\u041e\u043b\u0435\u0433 \u0420\u0430\u0437\u0443\u043c\u043d\u044b\u0439", email: "razumniy.or@skiliks.com"},
                {id:9, fio: "\u0421\u043a\u043e\u0440\u043e\u0431\u0435\u0439 \u0410.\u041c.", email: "skorobey.am@skiliks.com"},
                {id:10, fio: "\u0416\u0435\u043b\u0435\u0437\u043d\u044b\u0439 \u0421.", email: "zhelezniy.so@skiliks.com"},
                {id:11, fio: "\u0412\u0430\u0441\u0438\u043b\u0438\u0439 \u0411\u043e\u0431\u0440", email: "bobr.vs@skiliks.com"},
                {id:12, fio: "\u0415\u0433\u043e\u0440 \u0422\u0440\u0443\u0434\u044f\u043a\u0438\u043d", email: "trudyakin.ek@skiliks.com"},
                {id:13, fio: "\u041b\u044e\u0434\u043e\u0432\u043a\u0438\u043d\u0430 \u0421.", email: "lyudovkina.sm@skiliks.com"},
                {id:14, fio: "\u0412\u0430\u0441\u0438\u043b\u0438\u0439 \u0425\u043e\u0437\u0438\u043d", email: "khozin.vk@skiliks.com"},
                {id:15, fio: "\u0422\u043e\u0447\u043d\u044b\u0445 \u0410.", email: "tochnykh.ay@skiliks.com"},
                {id:16, fio: "\u0421\u0435\u043c\u0435\u043d\u043e\u0432\u0430 \u041e.", email: "semenova.oo@skiliks.com"},
                {id:17, fio: "\u0410\u043d\u043d\u0430 \u0416\u0443\u043a\u043e\u0432\u0430", email: "zhukova.ar@skiliks.com"},
                {id:18, fio: "\u0410\u0434\u0432\u043e\u043a\u0430\u0442\u043e\u0432 \u042e.", email: "advokatov.yv@skiliks.com"},
                {id:19, fio: "\u0424\u0430\u0438\u043d\u0430 \u0413\u043e\u043b\u044c\u0446", email: "golts.fe@skiliks.com"},
                {id:20, fio: "\u041a\u0430\u043c\u0435\u043d\u0441\u043a\u0438\u0439 \u0412.", email: "kamenskiy.vp@region.skiliks.com"},
                {id:21, fio: "\u0412\u0430\u0441\u0438\u043b\u044c\u0435\u0432 \u0410.", email: "vasiliev.aa@region.skiliks.com"},
                {id:22, fio: "\u042e\u0440\u0438\u0439 \u041c\u044f\u0433\u043a\u043e\u0432", email: "myagkov.ys@skiliks.com"},
                {id:23, fio: "\u041f\u0435\u0442\u0440\u0430\u0448\u0435\u0432\u0438\u0447 \u0418.", email: "petrashevich.iv@skiliks.com"},
                {id:24, fio: "\u0410\u043d\u0442\u043e\u043d \u0421\u0435\u0440\u043a\u043e\u0432", email: "serkov.af@skiliks.com"},
                {id:25, fio: "\u0414\u043e\u0431\u0440\u043e\u0445\u043e\u0442\u043e\u0432 \u0418.", email: "dobrokhotov@gmail.com"},
                {id:26, fio: "\u0410\u043d\u0436\u0435\u043b\u0430 \u0411\u043b\u0435\u0441\u043a", email: "blesk@mckinsey.com"},
                {id:27, fio: "\u041b\u044e\u0431\u0438\u043c\u0430\u044f \u0436\u0435\u043d\u0430", email: "lapochka@gmail.com"},
                {id:28, fio: "\u041f\u0435\u0442\u0440 \u041f\u043e\u0433\u043e\u0434\u043a\u0438\u043d", email: "petya1984@gmail.com"},
                {id:29, fio: "\u041e\u043b\u0435\u0433 \u0421\u043a\u043e\u0440\u043a\u0438\u043d", email: "ckorkin@gmail.com"},
                {id:30, fio: "\u0421\u0435\u0440\u0435\u0433\u0430", email: "serjio@gmail.com"},
                {id:31, fio: "\u0421\u0442\u0435\u043f\u0430\u043d\u043e\u0432 \u0421.", email: "stepanov@lpolet.com"},
                {id:32, fio: "\u041c\u0430\u0440\u0438\u043d\u043a\u0430", email: "marina_pet@gmail.com"},
                {id:33, fio: "\u041e.\u0418.\u0418\u0432\u0430\u043d\u043e\u0432\u0430", email: "ivanova@businessanalytycs.com"}
            ];


            var theme = {
                "result":1,
                "data":{
                    "1788":"Fwd: !\u043f\u0440\u043e\u0431\u043b\u0435\u043c\u0430 \u0441 \u0441\u0435\u0440\u0432\u0435\u0440\u043e\u043c!"
                },
                "characterThemeId":"1788"
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

                server.respondWith("POST", "/index.php/mail/forward",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify(forward)]);

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
                        JSON.stringify(theme)]);

                server.respondWith("POST", "/index.php/mail/getPhrases",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({
                                "result": 1,
                                "data":{"1":"phrase 1", "2":"phrase 2", "3":"phrase 3"},
                                "addData":{"3613":".","3614":",","3615":":","3616":"\"","3617":"-","3618":";"},
                                "message":""}
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

            it("forward for M8", function () {
                var simulation = SKApp.simulation;
                simulation.start();
                var mail_window = new SKWindow({name:'mailEmulator', subname:'mailMain'});
                mail_window.open();

                buster.assert.defined(simulation.mailClient);

                var mailClientView = new SKMailClientView({model_instance:mail_window});
                mailClientView.render();

                server.respond();

                expect(mailClientView.$('tr[data-email-id=4136] td.mail-emulator-received-list-cell-theme').text()).toBe('!проблема с сервером!');
                mailClientView.$('tr[data-email-id=4136] td.mail-emulator-received-list-cell-theme').click();

                server.respond();

                mailClientView.$el.find('.FORWARD_EMAIL').click();
                $('body').append(mailClientView.$el);

                server.respond();

                //mailClientView.renderForwardEmailScreen();

                //server.respond();

                // Crazy DOM stuff
//                $('#MailClient_RecipientsList').append('<li class="tagItem">Денежная Р.Р.</li>');
//                $('#MailClient_RecipientsList').append('<li class="tagItem">Крутько М.</li>');
//
//                mailClientView.$("#MailClient_NewLetterSubject").ddslick();
//                //mailClientView.$("#MailClient_NewLetterSubject").data('ddslick'). = undefined;
//
//                var email = mailClientView.generateNewEmailObject();
//                server.respond();
//
//                var validationDialogResult = mailClientView.mailClient.validationDialogResult(email);
//                server.respond();
//
//                // check is email valid
//                expect(validationDialogResult).toBe(true);
//                mailClientView.mailClient.reloadSubjects([2,4]);
//                server.respond();
//
//                expect(SKApp.simulation.mailClient.availableSubjects[0].text).toBe('Fwd: !проблема с сервером!');
//                mailClientView.$('.SEND_EMAIL').click();
//                server.respond();
//                server.requests.forEach(function(request){
//                    if(request.url === '/index.php/mail/sendMessage'){
//                        expect(request.requestBody).toBe('copies=&fileId=&messageId=4125&phrases=&receivers=2%2C4%2C&subject=1788&time=09%3A00&letterType=forward');
//                    }
//                });
            });
        });
    });
});