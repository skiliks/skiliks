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
                    SKApp.user = new SKUser({});
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
                    expect(event.getTypeSlug()).toBe('visit');

                    var wndModel = new SKWindow({name: 'visitor', subname: 'visitorEntrance', sim_event: event});
                    wndModel.open();
                    var visitorView = new SKVisitView({model_instance: wndModel});
                    visitorView.render();
                    expect(visitorView.$('.visitor-allow').attr('data-dialog-id')).toEqual(799);

                    visitorView.$('.visitor-allow').click();
                    server.respond();
                    expect(simulation.events.length).toBe(2);

                    event = simulation.events.at(1);
                    wndModel = new SKDialogWindow({name: 'visitor', subname: 'visitorTalk', sim_event: event});
                    wndModel.open();
                    visitorView = new SKImmediateVisitView({model_instance: wndModel});
                    visitorView.render();
                    expect(visitorView.$('.replica-select').length).toBe(2);

                    visitorView.$('.replica-select[data-id=802]').click();
                    server.responses = [];
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
                });

            /**
             * Visitor phone call test
             */

                it('Visitor phone call test', function() {
                    expect(1).toBe(1);
                    /*var applicationView = new SKApplicationView();

                    SKApp.user.startSimulation(1);

                    applicationView.frame = new SKSimulationStartView({'simulations': SKApp.user.simulations});

                    console.log(SKApp.user.simulation);

                    // console.log('-----------------------------------------------------');

                    /*
                    server.respondWith(
                        "POST",
                        "/index.php/events/getState",
                        [
                            200,
                            { "Content-Type":"application/json" },
                            JSON.stringify({
                                "result":     1,
                                "serverTime": "09:03:00",
                                "events":[
                                    { "result":1, "eventType":1, "eventTime":"09:02:00",
                                        "data":[
                                            { "id":"786", "ch_from":"28", "ch_from_state":"1", "ch_to":"1",  "ch_to_state":"1", "dialog_subtype":"1", "text":"звук телефонного звонка", "sound":null, "is_final_replica":"0", "code":"RST10", "excel_id":"765", "title":"Друг", "name":"Петр Погодкин", "remote_title":"Начальник отдела анализа и планирования", "remote_name":"Федоров А.В." },
                                            { "id":"787", "ch_from":"1", "ch_from_state":"1", "ch_to":"28", "ch_to_state":"1", "dialog_subtype":"1", "text":"Ответить", "sound":null, "is_final_replica":"1", "code":"RST10", "excel_id":"766" },
                                            { "id":"788", "ch_from":"1", "ch_from_state":"1", "ch_to":"28", "ch_to_state":"1", "dialog_subtype":"1", "text":"Не ответить", "sound":null,  "is_final_replica":"1", "code":"RST10", "excel_id":"767" }
                                        ]
                                    }
                                ]

                            })
                        ]
                    );

                    server.respondWith(
                        "POST",
                        "/index.php/dialog/get",
                        [
                            200,
                            { "Content-Type":"application/json" },
                            JSON.stringify(dialogStep1Response)
                        ]
                    );

                    server.respondWith(
                        "POST",
                        "/index.php/mail/getInboxUnreadCount",
                        [
                            200,
                            { "Content-Type":"application/json" },
                            JSON.stringify({
                                "result": 1
                            })
                        ]
                    );

                    var simulation = SKApp.user.simulation = new SKSimulation();
                    simulation.start();

                    var login_html = _.template(simulation_template, {});
                    var iconPanelView = new SKIconPanelView({'el': $(login_html).find('.main-screen-icons')});

                    simulation.getNewEvents();
                    server.respond();

                    // check that event has been added ti queue
                    expect(simulation.events.length).toBe(1);

                    // check than phone icon - has been activated
                    expect(iconPanelView.$el.find('.phone').hasClass('icon-active')).toBe(true);

                    iconPanelView.$el.find('.phone').click();

                    console.log($('#canvas').text());*/

                    /*var event = simulation.events.at(0);
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
                    event.selectReplica(807, function() {});*/
                });
            });
        });
    }
);