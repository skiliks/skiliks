buster.spec.expose();

define(
    [
        'game/models/SKApplication',
        'game/models/SKSimulation',
        'game/models/window/SKWindow',
        'game/views/dialogs/SKVisitView',
        'game/views/dialogs/SKImmediateVisitView'
    ],
    function () {
        describe('visitor dialog', function(run) {
            'use strict';

            _.templateSettings.interpolate = /<@=(.+?)@>/g;
            _.templateSettings.evaluate    = /<@(.+?)@>/g;

            var visitorEventResponse = {
                result: 1,
                serverTime: '10:10:00',
                events: [
                    {
                        result: 1,
                        eventType: 1,
                        eventTime: '10:10:00',
                        data: [
                            {id: 798, ch_from: 35, ch_to: 1, dialog_subtype: '5', is_final_replica: 0, sound: '', text: '', code: "RVT1"},
                            {id: 799, ch_from: 1, ch_to: 35, dialog_subtype: '5', is_final_replica: 1, sound: '', text: '', code: "RVT1"},
                            {id: 800, ch_from: 1, ch_to: 35, dialog_subtype: '5', is_final_replica: 1, sound: '', text: '', code: "RVT1"}
                        ]
                    }
                ]
            };

            var dialogStep1Response = {
                result: 1,
                events: [
                    {
                        result: 1,
                        eventType: 1,
                        data: [
                            {id: 801, ch_from: 35, ch_to: 1, dialog_subtype: '4', is_final_replica: 0, sound: '', text: '', code: "RV1"},
                            {id: 802, ch_from: 1, ch_to: 35, dialog_subtype: '4', is_final_replica: 0, sound: '', text: '', code: "RV1"},
                            {id: 803, ch_from: 1, ch_to: 35, dialog_subtype: '4', is_final_replica: 0, sound: '', text: '', code: "RV1"}
                        ]
                    }
                ]
            };

            var dialogStep2Response = {
                result: 1,
                events: [
                    {
                        result: 1,
                        eventType: 1,
                        data: [
                            {id: 804, ch_from: 35, ch_to: 1, dialog_subtype: '4', is_final_replica: 0, sound: '', text: '', code: "RV1"},
                            {id: 805, ch_from: 1, ch_to: 35, dialog_subtype: '4', is_final_replica: 0, sound: '', text: '', code: "RV1"},
                            {id: 806, ch_from: 1, ch_to: 35, dialog_subtype: '4', is_final_replica: 0, sound: '', text: '', code: "RV1"},
                            {id: 807, ch_from: 1, ch_to: 35, dialog_subtype: '4', is_final_replica: 1, sound: '', text: '', code: "RV1"}
                        ]
                    }
                ]
            };

            var stubResponse = {
                result: 1
            };

            var simStartResponse = {result: 1, speedFactor: 8, simId: 1};

            run(function() {
                var server;

                before(function () {
                    server = sinon.fakeServer.create();

                    server.respondWith(
                        "POST",
                        "/index.php/simulation/start",
                        [200, { "Content-Type":"application/json" }, JSON.stringify(simStartResponse)]
                    );

                    server.respondWith(
                        "POST",
                        "/index.php/todo/get",
                        [200, { "Content-Type":"application/json" }, JSON.stringify(stubResponse)]
                    );

                    server.respondWith(
                        "POST",
                        "/index.php/dayPlan/get",
                        [200, { "Content-Type":"application/json" }, JSON.stringify(stubResponse)]
                    );

                    server.respondWith(
                        "POST",
                        "/index.php/myDocuments/getList",
                        [200, { "Content-Type":"application/json" }, JSON.stringify(stubResponse)]
                    );

                    window.SKApp = new SKApplication();
                    window.SKConfig = {'simulationStartTime': '9:00', 'skiliksSpeedFactor': 8};
                    SKApp.user = {};
                    this.timeout = 1000;
                });

                after(function () {
                    server.restore();
                });

                it('Visitor dialog test', function() {
                    server.respondWith(
                        "POST",
                        "/index.php/events/getState",
                        [200, { "Content-Type":"application/json" }, JSON.stringify(visitorEventResponse)]
                    );

                    server.respondWith(
                        "POST",
                        "/index.php/dialog/get",
                        [200, { "Content-Type":"application/json" }, JSON.stringify(dialogStep1Response)]
                    );

                    var simulation = SKApp.user.simulation = new SKSimulation();
                    simulation.start();
                    simulation.getNewEvents();

                    server.respond();
                    expect(simulation.events.length).toBe(1);

                    var event = simulation.events.at(0);
                    var wndModel = new SKWindow({name: 'visitor', subname: 'visitorEntrance', sim_event: event});
                    var visitorView = new SKVisitView({model_instance: wndModel});
                    visitorView.render();

                    event.selectReplica(799, function() {});

                    server.respond();
                    expect(simulation.events.length).toBe(2);

                    event = simulation.events.at(1);
                    wndModel = new SKDialogWindow({name: 'visitor', subname: 'visitorTalk', sim_event: event});
                    visitorView = new SKImmediateVisitView({model_instance: wndModel});
                    visitorView.render();

                    event.selectReplica(803, function() {});

                    server.respondWith(
                        "POST",
                        "/index.php/dialog/get",
                        [200, { "Content-Type":"application/json" }, JSON.stringify(dialogStep2Response)]
                    );

                    server.respond();
                    expect(simulation.events.length).toBe(3);

                    event = simulation.events.at(2);
                    event.selectReplica(807, function() {});
                });
            });
        });
    }
);