/*global buster, sinon, describe, before, after, require */
buster.spec.expose();

var folder_structure = {
    "result":1,
    "folders":{
        "1":{
            "id":1,
            "folderId":1,
            "name":"\u0412\u0445\u043e\u0434\u044f\u0449\u0438\u0435",
            "unreaded":"3"
        },
        "2":{
            "id":2,
            "folderId":2,
            "name":"\u0427\u0435\u0440\u043d\u043e\u0432\u0438\u043a\u0438",
            "unreaded":0
        },
        "3":{
            "id":3,
            "folderId":3,
            "name":"\u0418\u0441\u0445\u043e\u0434\u044f\u0449\u0438\u0435",
            "unreaded":0
        },
        "4":{
            "id":4,
            "folderId":4,
            "name":"\u041a\u043e\u0440\u0437\u0438\u043d\u0430",
            "unreaded":0
        }
    },
    "messages":{
        "inbox":{
            "916046":{
                "id":"916046",
                "subject":"\u041f\u043e \u0446\u0435\u043d\u043e\u0432\u043e\u0439 \u043f\u043e\u043b\u0438\u0442\u0438\u043a\u0435",
                "sentAt":"03.10.2012 10:32",
                "sender":"\u041a\u0440\u0443\u0442\u044c\u043a\u043e \u041c. <krutko.ma@skiliks.com>",
                "receiver":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>",
                "readed":"1",
                "attachments":1,
                "subjectSort":"\u043f\u043e \u0446\u0435\u043d\u043e\u0432\u043e\u0439 \u043f\u043e\u043b\u0438\u0442\u0438\u043a\u0435"
            },
            "916047":{
                "id":"916047",
                "subject":"\u0424\u043e\u0440\u043c\u0430 \u043e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u0438 \u0434\u043b\u044f \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0430",
                "sentAt":"03.10.2012 11:02",
                "sender":"\u0412\u0430\u0441\u0438\u043b\u0438\u0439 \u0411\u043e\u0431\u0440 <bobr.vs@skiliks.com>",
                "receiver":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>",
                "readed":"0",
                "attachments":0,
                "subjectSort":"\u0444\u043e\u0440\u043c\u0430 \u043e\u0442\u0447\u0435\u0442\u043d\u043e\u0441\u0442\u0438 \u0434\u043b\u044f \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0430"
            },
            "916048":{
                "id":"916048",
                "subject":"\u041d\u043e\u0432\u0430\u044f \u0441\u0438\u0441\u0442\u0435\u043c\u0430 \u043c\u043e\u0442\u0438\u0432\u0430\u0446\u0438\u0438",
                "sentAt":"28.09.2012 18:45",
                "sender":"\u041b\u044e\u0434\u043e\u0432\u043a\u0438\u043d\u0430 \u0421. <lyudovkina.sm@skiliks.com>",
                "receiver":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>",
                "readed":"0",
                "attachments":1,
                "subjectSort":"\u043d\u043e\u0432\u0430\u044f \u0441\u0438\u0441\u0442\u0435\u043c\u0430 \u043c\u043e\u0442\u0438\u0432\u0430\u0446\u0438\u0438"
            },
            "916049":{
                "id":"916049",
                "subject":"\u0422\u0440\u0443\u0434\u043e\u0432\u043e\u0439 \u0434\u043e\u0433\u043e\u0432\u043e\u0440",
                "sentAt":"01.10.2012 16:30",
                "sender":"\u0410\u0434\u0432\u043e\u043a\u0430\u0442\u043e\u0432 \u042e. &lt;advokatov.yv@skiliks.com&gt;",
                "receiver":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. &lt;fedorov.av@skiliks.com&gt;",
                "readed":"0",
                "attachments":1,
                "subjectSort":"\u0442\u0440\u0443\u0434\u043e\u0432\u043e\u0439 \u0434\u043e\u0433\u043e\u0432\u043e\u0440"
            }
        },
        "sended":{
            "915994":{
                "id":"915994",
                "subject":"\u041e\u0442\u0447\u0435\u0442 \u0434\u043b\u044f \u041f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u044f",
                "sentAt":"03.10.2012 16:20", "sender":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>",
                "receiver":"\u0414\u0435\u043d\u0435\u0436\u043d\u0430\u044f \u0420.\u0420. <denezhnaya.rr@skiliks.com>",
                "readed":1,
                "attachments":1,
                "subjectSort":"\u043e\u0442\u0447\u0435\u0442 \u0434\u043b\u044f \u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u044f"
            }
        }
    }
};

define([
    "game/models/SKApplication",
    "game/views/mail/SKMailClientView",
    "game/models/window/SKWindow"], function (SKApplication, SKMailClientView, SKWindow) {

    spec = describe('mail client', function (run) {
        "use strict";
        /**
         * @type {SKMailClientView} SKMailClientView
         */
        run(function () {
            var server;
            before(function () {
                server = sinon.fakeServer.create();
                //clock = sinon.useFakeTimers();
                //this.timeout = 10000;
                window.SKApp = new SKApplication();
                this.timeout = 1000;
            });
            after(function () {
                server.restore();
                debugger;
            });

            it("displays and hides window", function (done) {
                buster.log("Start");
                server.respondWith("POST", "/index.php/myDocuments/getList",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:1})]);
                server.respondWith("POST", "/index.php/mail/getReceivers",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:1})]);

                testSimulation('asd', '123', server, function (cb) {

                    buster.log('Sim started2');
                    buster.assert.defined(SKWindow);
                    var mail_window = new SKWindow({name:'mailEmulator', subname:'mailMain'});
                    mail_window.open();
                    buster.log('called');
                    buster.assert.defined(SKApp.user.simulation.mailClient);
                    var mail = new SKMailClientView({model_instance:mail_window});
                    buster.log('created');
                    mail.render();
                    var spy = sinon.spy();
                    mail.mailClient.on('init_completed', spy);
                    server.requests[server.requests.length - 1].respond(200, { "Content-Type":"application/json" }, JSON.stringify(folder_structure));
                    assert.defined(mail.mailClient.getEmailByMySqlId(916046));
                    server.requests[server.requests.length - 1].respond(200, { "Content-Type":"application/json" },
                        JSON.stringify({
                            "result":1,
                            "data":{
                                "id":"916046",
                                "subject":"\u041f\u043e \u0446\u0435\u043d\u043e\u0432\u043e\u0439 \u043f\u043e\u043b\u0438\u0442\u0438\u043a\u0435",
                                "message":"\u0414\u043e\u0431\u0440\u044b\u0439 \u0434\u0435\u043d\u044c! \n\n\u042f \u043d\u0435\u043c\u043d\u043e\u0433\u043e \u0441 \u043e\u043f\u0435\u0440\u0435\u0436\u0435\u043d\u0438\u0435\u043c \u0441\u0434\u0435\u043b\u0430\u043b\u0430 \u0440\u0430\u0431\u043e\u0442\u0443 \u043f\u043e \u0446\u0435\u043d\u043e\u0432\u043e\u0439 \u043f\u043e\u043b\u0438\u0442\u0438\u043a\u0435 (\u0432\u0447\u0435\u0440\u0430 \u0432\u044b\u0434\u0430\u043b\u0441\u044f \u0441\u0432\u043e\u0431\u043e\u0434\u043d\u044b\u0439 \u0432\u0435\u0447\u0435\u0440). \u041c\u043d\u0435 \u043a\u0430\u0436\u0435\u0442\u0441\u044f, \u0447\u0442\u043e \u044f \u043e\u0442\u0440\u0430\u0437\u0438\u043b\u0430 \u0432\u0441\u0435 \u043c\u044b\u0441\u043b\u0438, \u043a\u043e\u0442\u043e\u0440\u044b\u0435 \u043c\u044b \u043e\u0431\u0441\u0443\u0436\u0434\u0430\u043b\u0438 \u043d\u0430 \u0443\u0441\u0442\u0430\u043d\u043e\u0432\u043e\u0447\u043d\u043e\u0439 \u0432\u0441\u0442\u0440\u0435\u0447\u0435. \u0411\u0443\u0434\u0435\u0442 \u0432\u0440\u0435\u043c\u044f \u0432 \u043e\u0442\u043f\u0443\u0441\u043a\u0435 - \u043f\u043e\u0441\u043c\u043e\u0442\u0440\u0438\u0442\u0435. \n\n\u0421 \u0443\u0432\u0430\u0436\u0435\u043d\u0438\u0435\u043c, \u041c\u0430\u0440\u0438\u043d\u0430 \u041a\u0440\u0443\u0442\u044c\u043a\u043e  \n\u0412\u0435\u0434\u0443\u0449\u0438\u0439 \u0430\u043d\u0430\u043b\u0438\u0442\u0438\u043a \u043e\u0442\u0434\u0435\u043b\u0430 \u0430\u043d\u0430\u043b\u0438\u0437\u0430 \u0438 \u043f\u043b\u0430\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u044f", "sentAt":"03.10.2012 10:32", "sender":"\u041a\u0440\u0443\u0442\u044c\u043a\u043e \u041c. <krutko.ma@skiliks.com>",
                                "receiver":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412. <fedorov.av@skiliks.com>",
                                "copies":"",
                                "attachments":{
                                    "id":"221763",
                                    "name":"\u0426\u0435\u043d\u043e\u0432\u0430\u044f \u043f\u043e\u043b\u0438\u0442\u0438\u043a\u0430.xlsx"
                                }
                            }
                        }));

                    /* 4 letters at sim start */
                    expect(mail.$('.mail-emulator-received-list-cell-sender').length).toBe(4);
                    expect(mail.mailClient.getInboxFolder().name).toBe('Входящие');

                    assert.calledOnce(spy);
                    mail.$el.find('.NEW_EMAIL').click();
                    server.respond();
                    //server.requests[server.requests.length - 1].respond(200, { "Content-Type": "application/json" }, JSON.stringify({result:1}));
                    //buster.log(mail.$el.html());
                    expect(1).toBe(1);
                    mail.remove();
                    cb(done);
                }, function () {
                    expect(1).toBe(2);
                    done();
                });
            });
        });
    });
});