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
                        [
                            200,
                            { "Content-Type":"application/json" },
                            JSON.stringify({"result":1,"unreaded":"4"})
                        ]
                    );
                    this.config = {'simulationStartTime': '9:00', 'skiliksSpeedFactor': 8};
                    window.SKApp = new SKApplication(this.config);
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
                    var simulation = SKApp.simulation;
                    simulation.start();
                    server.respond();
                    expect(simulation.window_set.length).toBe(1);
                    SKApp.simulation.getNewEvents();
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

                    wndModel = simulation.window_set.at(1);
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

                    /* init simulation */
                    var applicationView = new SKApplicationView();
                    server.respond();


                    server.respond();


                    server.respond();

                    /* test */

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
                    expect(SKApp.simulation.events.length).toBe(0);
                    var phone_spy = sinon.spy();
                    SKApp.simulation.events.on('event:phone', phone_spy);

                    SKApp.simulation.getNewEvents();
                    server.respond();
                    assert.calledOnce(phone_spy);
                    // check that event has been added to queue
                    expect(SKApp.simulation.events.length).toBe(1);

                    // check than phone icon - has been activated
                    expect(applicationView.frame.icon_view.$el.find('.phone').hasClass('icon-active')).toBe(true);

                    applicationView.frame.icon_view.$el.find('.icons-panel .phone.icon-active a').click();

                    server.respond();

                    server.respondWith(
                        "POST",
                        "/index.php/dialog/get",
                        [
                            200,
                            { "Content-Type":"application/json" },
                            JSON.stringify({"result":1,"events":[{"result":1,"data":[{"id":"789","ch_from":"28","ch_from_state":"1","ch_to":"1","ch_to_state":"1","dialog_subtype":"2","text":" \u2014 \u041f\u0440\u0438\u0432\u0435\u0442, \u0434\u0440\u0443\u0433! \u0422\u044b \u0436\u0438\u0432? \u041d\u0430\u0434\u0435\u044e\u0441\u044c, \u0434\u043e \u043e\u0442\u043f\u0443\u0441\u043a\u0430 \u0434\u043e\u0442\u044f\u043d\u0435\u0448\u044c?","sound":"RS10_1.wav","step_number":"1","is_final_replica":"0","code":"RS10","excel_id":"768","title":"\u0414\u0440\u0443\u0433","name":"\u041f\u0435\u0442\u0440 \u041f\u043e\u0433\u043e\u0434\u043a\u0438\u043d","remote_title":"\u041d\u0430\u0447\u0430\u043b\u044c\u043d\u0438\u043a \u043e\u0442\u0434\u0435\u043b\u0430 \u0430\u043d\u0430\u043b\u0438\u0437\u0430 \u0438 \u043f\u043b\u0430\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u044f","remote_name":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412."},{"id":"790","ch_from":"1","ch_from_state":"1","ch_to":"28","ch_to_state":"1","dialog_subtype":"2","text":" \u2014 \u041f\u0435\u0442\u0440, \u043f\u0440\u043e\u0441\u0442\u0438, \u044f \u043d\u0435 \u043c\u043e\u0433\u0443 \u0433\u043e\u0432\u043e\u0440\u0438\u0442\u044c, \u043f\u0435\u0440\u0435\u0437\u0432\u043e\u043d\u044e! ","sound":null,"step_number":"1","is_final_replica":"0","code":"RS10","excel_id":"769"},{"id":"791","ch_from":"1","ch_from_state":"1","ch_to":"28","ch_to_state":"1","dialog_subtype":"2","text":" \u2014 \u041f\u0440\u0438\u0432\u0435\u0442, \u041f\u0435\u0442\u0440. \u0423 \u0442\u0435\u0431\u044f \u0447\u0442\u043e-\u0442\u043e \u0441\u0440\u043e\u0447\u043d\u043e\u0435?","sound":null,"step_number":"1","is_final_replica":"0","code":"RS10","excel_id":"770"},{"id":"792","ch_from":"1","ch_from_state":"1","ch_to":"28","ch_to_state":"1","dialog_subtype":"2","text":" \u2014 \u041f\u0435\u0442\u0440, \u043f\u0440\u0438\u0432\u0435\u0442! \u0420\u0430\u0434 \u0442\u0435\u0431\u044f \u0441\u043b\u044b\u0448\u0430\u0442\u044c! \u0422\u043e\u043b\u044c\u043a\u043e \u043e\u0442\u043f\u0443\u0441\u043a\u043e\u043c \u0438 \u0436\u0438\u0432 ! \u041a\u0430\u043a \u0442\u044b?","sound":null,"step_number":"1","is_final_replica":"0","code":"RS10","excel_id":"771"}],"eventType":1}]})
                        ]
                    );

                    expect(applicationView.frame.$el.find('#phone_reply').length).toBe(1);
                    expect(applicationView.frame.$el.find('.phone-content').length).toBe(0);
                    applicationView.frame.$el.find('#phone_reply').click(); // .call_view
                    server.respond();
                    expect(applicationView.frame.$el.find('.phone-content').length).toBe(1);

                    var requestChecked = false;
                    for(var i in server.requests) {
                        //console.log(server.requests[i].url);
                        if (server.requests[i].url == '/index.php/dialog/get') {
                            expect(!!server.requests[i].requestBody.match(/dialogId=787&time=09%3A00/)).toBeTrue();
                            requestChecked = true;
                        }
                    }

                    expect(requestChecked).toBe(true); // front not send dialog/get request

                });

                /**
                 * Visitor phone call test
                 */

                it('Incoming mail test', function() {

                    /* init simulation */

                    var applicationView = new SKApplicationView();
                    //SKApp.simulation.start();

                    server.respond();


                    server.respond();

                    applicationView.simulation_view = new SKSimulationView();
                    applicationView.simulation_view.render();

                    server.respond();

                    /* test */

                    // check counter
                    expect(applicationView.simulation_view.$el.find('#icons_email').text()).toBe('4');

                    SKApp.simulation.getNewEvents();
                    server.requests[server.requests.length-1].respond(
                        200,
                        { "Content-Type":"application/json" },
                        JSON.stringify({result:1,events:[{result:1,id:'46791',eventType:'M'}],serverTime:'09:05:00'})
                    );
                    server.requests[server.requests.length-1].respond(
                        200,
                        { "Content-Type":"application/json" },
                        JSON.stringify({"result": 1,"unreaded":"5"})
                    );
                    server.requests[server.requests.length-1].respond(
                        200,
                        { "Content-Type":"application/json" },
                        JSON.stringify({"result":0,"message":"\u041d\u0435\u0442 \u0431\u043b\u0438\u0436\u0430\u0439\u0448\u0438\u0445 \u0441\u043e\u0431\u044b\u0442\u0438\u0439","code":4,"serverTime":"09:05:00"})
                    );



                    server.respond();

                    // check icon animation
                    expect(applicationView.simulation_view.$el.find('.icons-panel .mail.icon-active').length).toBe(1);

                    // check counter
                    expect(applicationView.simulation_view.$el.find('#icons_email').text()).toBe('5');
                });
            });
        });
    }
);