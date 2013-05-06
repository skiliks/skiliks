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
            var inbox_start = {
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
                    }
                },
                "type":"inbox"
            };

            var inbox_added = {
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
                        JSON.stringify(inbox_start)]);

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

            it("update mail", function () {
                //init simulation
                var simulation = SKApp.simulation;
                simulation.start();
                server.respond();
                var mail_window = new SKWindow({name:'mailEmulator', subname:'mailMain'});
                mail_window.open();

                buster.assert.defined(simulation.mailClient);

                var mailClientView = new SKMailClientView({model_instance:mail_window});
                mailClientView.render();
                server.respond();
                expect(mailClientView.$('tr[data-email-id=1274] td.mail-emulator-received-list-cell-theme').text()).toBe('По ценовой политике');
                //console.log(server.responses);
                server.responses.forEach(function(response, index){
                    if(response.url === '/index.php/mail/getMessages'){
                        server.responses[index].response[2] = JSON.stringify(inbox_added);
                        //console.log(server.responses[index].response[2]);
                    }
                });
                mailClientView.mailClient.getInboxFolderEmails(function(){
                    mailClientView.doRenderFolder(mailClientView.mailClient.aliasFolderInbox, false, true);
                });
                server.respond();

                expect(mailClientView.$('tr[data-email-id=1278] td.mail-emulator-received-list-cell-theme').text()).toBe('срочно! Отчетность');

            });
        });
    });
});