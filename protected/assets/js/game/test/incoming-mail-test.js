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


                it('Incoming mail test', function() {

                    /* init simulation */

                    SKApp.simulation.start();

                    server.respond();


                    server.respond();


                    server.respond();
                    /* test */

                    // check counter
                    expect(SKApp.simulation.events.unread_mail_count).toBe(4);

                    var events = [
                        {result:1,events:[{result:1,id:'46791',eventType:'M'}],serverTime:'09:05:00'},
                        {"result":0,"message":"\u041d\u0435\u0442 \u0431\u043b\u0438\u0436\u0430\u0439\u0448\u0438\u0445 \u0441\u043e\u0431\u044b\u0442\u0438\u0439","code":4,"serverTime":"09:05:00"}
                    ];
                    server.responses = _.filter(server.responses, function (response) {return response.method !== 'POST' || response.url !== "/index.php/events/getState";});
                    server.respondWith(
                        "POST",
                        "/index.php/events/getState",
                        function (xhr) {
                            var event = events[0];
                            events.shift();
                            xhr.respond(
                                200,
                                { "Content-Type":"application/json" },
                                JSON.stringify(event)
                            );
                        }
                    );
                    this.mailCounter = "5";
                    var mail_event_spy = sinon.spy();
                    SKApp.simulation.events.on('event:mail', mail_event_spy);
                    SKApp.simulation.getNewEvents();
                    server.respond();
                    assert(mail_event_spy.calledOnce);
                    expect(SKApp.simulation.events.unread_mail_count).toBe(5);

                });
            });
        });
    }
);