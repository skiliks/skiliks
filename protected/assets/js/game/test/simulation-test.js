// Expose describe and it functions globally
buster.spec.expose();

var spec = describe('simulation', function(run) {
    require(["game/models/SKApplication"], function (SKApplication) {
        run(function () {
            var server;
            var clock;
            timeout = 1000;
            before(function () {
                SKApp = new SKApplication();
                server = sinon.fakeServer.create();
                clock = sinon.useFakeTimers();
                //this.timeout = 10000;
            });
            after(function () { server.restore(); clock.restore(); });
            it("can start simulation", function (done) {
                SKConfig = {
                    "skiliksSpeedFactor":8,
                    "simulationStartTime":"9:00",
                    "simulationEndTime":"18:00",
                    "storageURL":"http:\/\/storage.skiliks.com\/v1\/",
                    "assetsUrl":"\/assets\/3259e654"
                };
                SKApp.session.login('asd', '123');
                SKApp.session.on('login:success', function () {
                    buster.log('Login success');
                    expect(typeof SKApp.user).toBe("object");
                    SKApp.user.on('logout', function () {
                        done();
                    });
                    var simulation = SKApp.user.startSimulation(1);
                    simulation.on('stop', function () {
                        buster.log("Sim stop");
                        SKApp.user.logout();
                        server.requests[6].respond(
                            200,
                            { "Content-Type": "application/json" },
                            JSON.stringify({ result: 1 })
                        );
                    });
                    simulation.on('start', function () {
                        expect(server.requests[2].url).toBe('/index.php/todo/get');
                        expect(server.requests[3].url).toBe('/index.php/dayPlan/get');
                        expect(server.requests[4].url).toBe("/index.php/myDocuments/getList");
                        SKApp.user.stopSimulation();
                        server.requests[5].respond(
                            200,
                            { "Content-Type": "application/json" },
                            JSON.stringify({ result: 1 })
                        );
                    });
                    server.requests[1].respond(
                        200,
                        { "Content-Type": "application/json" },
                        JSON.stringify({ result: 1 })
                    );
                });
                SKApp.session.on('login:failure', function () {
                    assert(false);
                    done();
                });
                server.requests[0].respond(
                    200,
                    { "Content-Type": "application/json" },
                    JSON.stringify({ result: 1 })
                );

            });
            it("starts simulation two times", function (done) {
                buster.log("Start");
                testSimulation('asd', '123', server, function (cb) {
                    buster.log("started");
                });
            });
        });
    });
});
