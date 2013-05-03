/* global SKSimulationView, buster, define, describe, _, sinon, after, it, expect, SKApp, SKApplication, SKWindow,
 SKVisitView, before, SKApplicationView, SKImmediateVisitView, assert, */

buster.spec.expose();

// "text!game/jst/world/simulation_template.jst", simulation_template
//         'game/models/SKSimulation',
//  ,
//    'game/models/window/SKWindow',
//    'game/views/dialogs/SKVisitView',
//    'game/views/dialogs/SKImmediateVisitView',
//    'game/views/world/SKIconPanelView'

define(
    [
        'game/models/SKApplication',
        'game/views/world/SKApplicationView'
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
                            {id: '798', ch_from: '35', ch_to: '1', dialog_subtype: '5', is_final_replica: '0', sound: '', text: '', code: "RVT1"},
                            {id: '799', ch_from: '1', ch_to: '35', dialog_subtype: '5', is_final_replica: '1', sound: '', text: '', code: "RVT1"},
                            {id: '800', ch_from: '1', ch_to: '35', dialog_subtype: '5', is_final_replica: '1', sound: '', text: '', code: "RVT1"}
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
                            {id: '801', ch_from: '35', ch_to: '1', dialog_subtype: '4', is_final_replica: '0', sound: '', text: '', code: "RV1"},
                            {id: '802', ch_from: '1', ch_to: '35', dialog_subtype: '4', is_final_replica: '0', sound: '', text: '', code: "RV1"},
                            {id: '803', ch_from: '1', ch_to: '35', dialog_subtype: '4', is_final_replica: '0', sound: '', text: '', code: "RV1"}
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
                            {id: '804', ch_from: '35', ch_to: '1', dialog_subtype: '4', is_final_replica: '0', sound: '', text: '', code: "RV1"},
                            {id: '805', ch_from: '1', ch_to: '35', dialog_subtype: '4', is_final_replica: '0', sound: '', text: '', code: "RV1"},
                            {id: '806', ch_from: '1', ch_to: '35', dialog_subtype: '4', is_final_replica: '0', sound: '', text: '', code: "RV1"},
                            {id: '807', ch_from: '1', ch_to: '35', dialog_subtype: '4', is_final_replica: '1', sound: '', text: '', code: "RV1"}
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
                    var me = this;
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

                    server.respondWith(
                        "POST",
                        "/index.php/auth/auth",
                        [
                            200,
                            { "Content-Type":"application/json" },
                            JSON.stringify({"result":1,"sid":"bssjmqscv57ti3f19t17upq0u2","simulations":{"1":"promo","2":"developer"}})
                        ]
                    );

                    server.respondWith(
                        "POST",
                        "/index.php/auth/checkSession",
                        [
                            200,
                            { "Content-Type":"application/json" },
                            JSON.stringify({"result":1,"sid":"bssjmqscv57ti3f19t17upq0u2","simulations":{"1":"promo","2":"developer"}})
                        ]
                    );
                    server.respondWith(
                        "POST",
                        "/index.php/mail/getInboxUnreadCount",
                        function (xhr) {

                            xhr.respond(
                                200,
                                { "Content-Type":"application/json" },
                                JSON.stringify({"result":1,"unreaded":me.mailCounter||"4"})
                            );
                        }
                    );
                    server.respondWith(
                        "POST",
                        "/index.php/events/getState",
                        function (xhr) {
                            xhr.respond(200, { "Content-Type":"application/json" }, JSON.stringify(visitorEventResponse));
                        }
                    );
                    this.config = {'start': '9:00', 'skiliksSpeedFactor': 8, 'finish': '20:00', 'end': '18:00'};
                    window.SKApp = new SKApplication(this.config);
                    this.timeout = 1000;
                });

                after(function () {
                    server.restore();
                });

                it('Visitor dialog test', function() {
                    server.responses = _.filter(server.responses, function (response) {return response.method !== 'POST' || response.url !== "/index.php/events/getState";});
                    server.respondWith(
                        "POST",
                        "/index.php/events/getState", function (xhr) {
                            xhr.respond(200, { "Content-Type":"application/json" }, JSON.stringify(visitorEventResponse));
                        });

                    server.respondWith(
                        "POST",
                        "/index.php/dialog/get",
                        [200, { "Content-Type":"application/json" }, JSON.stringify(dialogStep1Response)]
                    );
                    var simulation = SKApp.simulation;
                    simulation.start();
                    server.respond();
                    expect(simulation.window_set.length).toBe(1);
                    expect(simulation.events.length).toBe(1);
                    var event = simulation.events.at(0);
                    expect(event.getTypeSlug()).toBe('visit');

                    var wndModel = new SKWindow({name: 'visitor', subname: 'visitorEntrance', sim_event: event});
                    wndModel.open();
                    var visitorView = new SKVisitView({model_instance: wndModel});
                    visitorView.render();
                    expect(visitorView.$('.visitor-allow').attr('data-dialog-id')).toEqual(799);

                    visitorView.$('.visitor-allow').click();

                    server.respond();
                    expect(simulation.events.length).toBe(2);

                    wndModel = simulation.window_set.at(1);
                    visitorView = new SKImmediateVisitView({model_instance: wndModel});
                    visitorView.render();
                    expect(visitorView.$('.replica-select').length).toBe(2);

                    visitorView.$('.replica-select[data-id=802]').click();
                    server.responses = _.filter(server.responses, function (response) {return response.method !== 'POST' || response.url !== "/index.php/dialog/get";});

                    server.respondWith(
                        "POST",
                        "/index.php/dialog/get",
                        [200, { "Content-Type":"application/json" }, JSON.stringify(dialogStep2Response)]
                    );

                    server.respond();
                    expect(simulation.events.length).toBe(3);
                    expect(wndModel.get('params').lastDialogId).toBe('802');

                    event = simulation.events.at(2);
                    expect(event.getMyReplicas()[2].is_final_replica).toBe('1');

                    event.selectReplica(807, function() {});
                    simulation.stop();
                    server.respond();

                });

            });
        });
    }
);