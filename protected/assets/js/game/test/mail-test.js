/*global buster, sinon, describe, before, after, require */
buster.spec.expose();

var inbox = {
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
};

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
                            data: {
                                1: 'bob <bob@skiliks.com>',
                                2: 'john <john@skiliks.com>'
                            }
                        })]);
                server.respondWith("POST", "/index.php/mail/getMessages",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({
                            result:1,
                            messages: inbox
                        })]);
                server.respondWith("POST", "/index.php/simulation/start",
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

            it("can display mail client", function () {
                var simulation = SKApp.user.simulation = new SKSimulation();
                simulation.start();
                var mail_window = new SKWindow({name:'mailEmulator', subname:'mailMain'});
                mail_window.open();
                buster.assert.defined(simulation.mailClient);
                var mail = new SKMailClientView({model_instance:mail_window});
                var spy = sinon.spy();
                mail.mailClient.on('init_completed', spy);
                buster.log('created');
                mail.render();
                server.respond();

                assert.calledOnce(spy, 'Init completed triggered');
                //buster.log(mail.$el.html());
                assert.defined(mail.mailClient.getEmailByMySqlId(916046));

                /* 4 letters at sim start */
                expect(mail.$('.mail-emulator-received-list-cell-sender').length).toBe(4);
                expect(mail.$('tr[data-email-id=916048] td.mail-emulator-received-list-cell-theme').text()).toBe('Новая система мотивации');
                expect(mail.mailClient.getInboxFolder().name).toBe('Входящие');
                assert.calledOnce(spy);
                server.respond();
            });

            it("has characters", function () {
                var client = new SKMailClient();
                client.updateRecipientsList();
                expect(client.getFormatedCharacterList()).toEqual(["bob, bob@skiliks.com (1)", "john, john@skiliks.com (2)"]);
            });
            it("can create new letter", function () {
                var simulation = SKApp.user.simulation = new SKSimulation();
                simulation.start();
                var mail_window = new SKWindow({name:'mailEmulator', subname:'mailMain'});
                mail_window.open();
                var mail = new SKMailClientView({model_instance:mail_window});
                mail.render();
                server.respond();
                mail.$el.find('.NEW_EMAIL').click();
                server.respond();
                expect(mail.$('.SEND_EMAIL').length, 1);
                mail.$('#MailClient_RecipientsList').focus();
                server.respond();
                expect(1).toBe(1);
            });
        });
    });
});