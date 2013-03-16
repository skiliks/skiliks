/*global buster, after, sinon, SKApp, expect*/
// Expose describe and it functions globally
buster.spec.expose();

var spec = describe('simulation', function (run) {
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
                server.respondWith("POST", "/index.php/events/getState",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:0})]);
                timers = sinon.useFakeTimers();
            });
            after(function () {
                server.restore();
                timers.restore();
            });
            it("can correct calculate time", function () {
                SKApp.user = {};
                var simulation = SKApp.simulation = new SKSimulation();
                simulation.start();
                expect(simulation.getGameMinutes()).toBe(540);
                expect(simulation.getGameSeconds()).toBe(540 * 60);
                expect(simulation.getGameTime()).toBe('09:00');
                expect(simulation.getGameTime(true)).toBe('09:00:00');
                timers.tick(100000);
                expect(simulation.getGameSeconds()).toBe(33200);
                expect(simulation.getGameMinutes()).toBe(553);
                expect(simulation.getGameTime()).toBe('09:13');
                expect(simulation.getGameTime(true)).toBe('09:13:20');

            });
            it("stops at 18:00", function () {
                var stop_spy = sinon.spy();
                SKApp.user = {'stopSimulation':stop_spy};
                var simulation = SKApp.simulation = new SKSimulation();
                simulation.start();
                server.respond();
                timers.tick(9 * 60 * 60 * 1000/8);
                buster.log(simulation.getGameTime());
                assert.calledOnce(stop_spy);

            });
        });
    });
});