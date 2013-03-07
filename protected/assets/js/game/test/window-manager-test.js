/*jshint no*/
// Expose describe and it functions globally
buster.spec.expose();

var spec = describe('window manager', function (run) {
    require(["game/models/SKApplication", "game/models/SKSimulation"], function (SKApplication, SKSimulation) {
        run(function () {
            var server;
            var timers;
            before(function () {
                SKConfig = {
                    "skiliksSpeedFactor":8,
                    "simulationStartTime":"9:00",
                    "simulationEndTime":"18:00",
                    "storageURL":"http:\/\/storage.skiliks.com\/v1\/",
                    "assetsUrl":"\/assets\/3259e654"
                };
                SKApp = new SKApplication();
                server = sinon.fakeServer.create();
                server.respondWith("POST", "/index.php/simulation/start",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:1})]);
                server.respondWith("POST", "/index.php/myDocuments/getList",
                    [200, { "Content-Type": "application/json" },
                        JSON.stringify({result: 1})]);
                server.respondWith("POST", "/index.php/events/getState",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:0})]);
                server.respondWith("POST", "/index.php/todo/get",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:0})]);
                server.respondWith("POST", "/index.php/dayPlan/get",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:0})]);
                timers = sinon.useFakeTimers();
            });
            after(function () {
                server.restore();
                timers.restore();
            });
            it("can set z-index", function () {
                SKApp.user = {};
                var simulation = SKApp.user.simulation = new SKSimulation();
                simulation.start();
                server.respond();
                var window1 = new SKWindow({'name': 'plan', 'subname': 'plan'});
                var window2 = new SKWindow({'name': 'phone', 'subname': 'phoneTalk'});
                var openOneSpy = sinon.spy();
                var openTwoSpy = sinon.spy();
                var activateOneSpy = sinon.spy();
                var activateTwoSpy = sinon.spy();
                var deactivateOneSpy = sinon.spy();
                window1.on('open', openOneSpy);
                window2.on('open', openTwoSpy);
                window2.on('activate', activateOneSpy);
                window1.on('deactivate', deactivateOneSpy);
                window2.on('activate', activateTwoSpy);
                window1.open();
                window2.open();
                assert.calledOnce(openOneSpy);
                assert.calledOnce(openTwoSpy);
                assert.calledOnce(activateOneSpy);
                assert.calledOnce(deactivateOneSpy);
                assert.calledOnce(activateTwoSpy);
                expect(window1.get('zindex')).toBe(1);
                expect(window2.get('zindex')).toBe(2);
                window1.setOnTop();
                expect(window1.get('zindex')).toBe(2);
                expect(window2.get('zindex')).toBe(1);
                window2.setOnTop();
                expect(window1.get('zindex')).toBe(1);
                expect(window2.get('zindex')).toBe(2);
                window2.setOnTop();
                expect(window1.get('zindex')).toBe(1);
                expect(window2.get('zindex')).toBe(2);

            });

        });
    });
});