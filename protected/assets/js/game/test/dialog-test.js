/*global buster, sinon, describe, before, after, require */
buster.spec.expose();

define([
    "game/models/SKApplication",
    "game/models/SKSimulation",
    "game/models/window/SKWindow"
    ],
    function () {

    spec = describe('mail client', function (run) {
        "use strict";
        run(function () {
            _.templateSettings.interpolate = /<@=(.+?)@>/g;
            _.templateSettings.evaluate    = /<@(.+?)@>/g;
            var server;
            before(function () {
                server = sinon.fakeServer.create();

                server.respondWith("POST", "/index.php/simulation/start",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({"result":1,"speedFactor":8,"simId":"1"})]);

                server.respondWith("POST", "/index.php/todo/get",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:1})]);

                server.respondWith("POST", "/index.php/dayPlan/get",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:1})]);

                server.respondWith("POST", "/index.php/myDocuments/getList",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:1})]);

                window.SKApp = new SKApplication();
                window.SKConfig = {'simulationStartTime':'9:00', "skiliksSpeedFactor":8 };
                SKApp.user = {};
                this.timeout = 1000;
            });

            after(function () {
                server.restore();
            });

            it("Simple dialog start test", function () {
                var simulation = SKApp.user.simulation = new SKSimulation();
                simulation.start();

                expect(simulation.events.length).toBe(0);
                var simulation_event_add_spy = sinon.spy();
                // this code copied from SKSimulationView.addSimulationEvents() {
                simulation.events.on('add', function (event) {
                    if (event.getTypeSlug() === 'immediate-visit') {
                        var win = simulation.window_set.open('visitor', 'visitorTalk', {sim_event:event});
                        event.setStatus('in progress');
                    } else if (event.getTypeSlug() === 'immediate-phone') {
                        var win = simulation.window_set.open('phone', 'phoneTalk', {sim_event:event});
                        event.setStatus('in progress');
                    }
                });
                simulation.events.on('add', simulation_event_add_spy);
                // this code copied from SKSimulationView.addSimulationEvents() }

                simulation.getNewEvents();

                server.respondWith("POST", "/index.php/events/getState",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({
                            "result":1,"serverTime":"09:24:00","events":[
                                {"result":1,"eventType":1,"eventTime":"09:23:00","data":[
                                    {
                                        "id":"331",
                                        "ch_from":"10",
                                        "ch_from_state":"1",
                                        "ch_to":"1",
                                        "ch_to_state":"1",
                                        "dialog_subtype":"4",
                                        "text":" \u2014 \u0422\u0430\u043a \u0432\u043e\u0442, \u0441\u043b\u0443\u0448\u0430\u0439 \u0438 \u0437\u0430\u043f\u0438\u0441\u044b\u0432\u0430\u0439\u2026 \u0418\u043b\u0438 \u0437\u0430\u043a\u0443\u0441\u044b\u0432\u0430\u0439! \u0425\u0430-\u0445\u0430-\u0445\u0430...\u0421\u0438\u0442\u0443\u0430\u0446\u0438\u044f \u0441 \u0441\u0435\u0440\u0432\u0435\u0440\u043e\u043c \u2013 \u0445\u0443\u0436\u0435 \u043d\u0435\u043a\u0443\u0434\u0430. \u0422\u044b \u043c\u043e\u0435 \u0443\u0442\u0440\u0435\u043d\u043d\u0435\u0435 \u043f\u0438\u0441\u044c\u043c\u043e \u0447\u0438\u0442\u0430\u043b?",
                                        "sound":"E8_3_1_\u041a\u0430\u043c\u0435\u0440\u0430.webm",
                                        "is_final_replica":"0","code":"E8.3","excel_id":"325","title":"\u041d\u0430\u0447\u0430\u043b\u044c\u043d\u0438\u043a \u043e\u0442\u0434\u0435\u043b\u0430 \u0438\u043d\u0444\u043e\u0440\u043c\u0430\u0446\u0438\u043e\u043d\u043d\u044b\u0445 \u0442\u0435\u0445\u043d\u043e\u043b\u043e\u0433\u0438\u0439 (\u0418\u0422)","name":"\u0416\u0435\u043b\u0435\u0437\u043d\u044b\u0439 \u0421.","remote_title":"\u041d\u0430\u0447\u0430\u043b\u044c\u043d\u0438\u043a \u043e\u0442\u0434\u0435\u043b\u0430 \u0430\u043d\u0430\u043b\u0438\u0437\u0430 \u0438 \u043f\u043b\u0430\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u044f",
                                        "remote_name":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412."},
                                    {
                                        "id":"332","ch_from":"1","ch_from_state":"1","ch_to":"10","ch_to_state":"1","dialog_subtype":"4",
                                        "text":" \u2014 \u041d\u0435\u0442, \u043f\u0440\u043e\u0441\u0442\u0438, \u041c\u0438\u0440\u043e\u043d. \u0421\u0435\u0433\u043e\u0434\u043d\u044f \u043f\u0440\u043e\u0441\u0442\u043e \u0441\u0443\u043c\u0430\u0441\u0448\u0435\u0434\u0448\u0438\u0439 \u0434\u0435\u043d\u044c. \u0421\u043e\u0431\u0438\u0440\u0430\u043b\u0441\u044f \u043f\u043e\u0447\u0442\u0443 \u0440\u0430\u0437\u0431\u0438\u0440\u0430\u0442\u044c \u043a\u0430\u043a \u0442\u043e\u043b\u044c\u043a\u043e \u0441\u043e \u0441\u0440\u043e\u0447\u043d\u044b\u043c\u0438 \u0432\u043e\u043f\u0440\u043e\u0441\u0430\u043c\u0438 \u0440\u0430\u0437\u0431\u0435\u0440\u0443\u0441\u044c. \u0410 \u0438\u043c \u043a\u043e\u043d\u0446\u0430 \u0438 \u043a\u0440\u0430\u044f \u043d\u0435\u0442.",
                                        "sound":"E8_3_1_\u041a\u0430\u043c\u0435\u0440\u0430.png",
                                        "is_final_replica":"0","code":"E8.3","excel_id":"326"
                                    },{
                                        "id":"333","ch_from":"1","ch_from_state":"1","ch_to":"10","ch_to_state":"1",
                                        "dialog_subtype":"4",
                                        "text":" \u2014 \u041a\u043e\u043d\u0435\u0447\u043d\u043e \u0447\u0438\u0442\u0430\u043b. \u0425\u043e\u0440\u043e\u0448\u0435\u0435 \u043f\u0438\u0441\u044c\u043c\u043e, \u043e\u0431\u0441\u0442\u043e\u044f\u0442\u0435\u043b\u044c\u043d\u043e\u0435!",
                                        "sound":null,"is_final_replica":"0","code":"E8.3","excel_id":"327"
                                    },{
                                        "id":"334","ch_from":"1","ch_from_state":"1","ch_to":"10","ch_to_state":"1","dialog_subtype":"4",
                                        "text":" \u2014 \u041a\u0430\u043a\u043e\u0435 \u043f\u0438\u0441\u044c\u043c\u043e? \u0423 \u043c\u0435\u043d\u044f \u0447\u0442\u043e, \u043f\u043e\u0447\u0442\u0430 \u0435\u0441\u0442\u044c?",
                                        "sound":null,"is_final_replica":"0","code":"E8.3","excel_id":"328"
                                    }
                                ]
                                }
                            ]
                        })
                    ]);

                server.respond();

                server.respondWith("POST", "/index.php/events/getState",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({"result":1})]);

                expect(simulation.events.length).toBe(1);
                assert.calledOnce(simulation_event_add_spy);
                console.log('Event has been added!');
            });
        });
    });
});