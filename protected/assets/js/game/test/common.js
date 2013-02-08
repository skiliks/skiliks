function testSimulation(email, password, server, cb, fail_cb) {
    SKConfig = {
        "skiliksSpeedFactor":8,
        "simulationStartTime":"9:00",
        "simulationEndTime":"18:00",
        "storageURL":"http:\/\/storage.skiliks.com\/v1\/",
        "assetsUrl":"\/assets\/3259e654"
    };
    buster.log(server.requests.length);
    SKApp.session.login('asd', '123');
    var success = sinon.spy();
    SKApp.session.on('login:success', success);
    SKApp.session.on('login:success', function () {
        var simulation = SKApp.user.startSimulation(1);
        simulation.on('start', function () {
            cb(function () {
                SKApp.user.stopSimulation();
            });

        });
        server.requests[1].respond(
            200,
            { "Content-Type": "application/json" },
            JSON.stringify({ result: 1 })
        );
    });
    SKApp.session.on('login:failure', function () {
        if (fail_cb !== undefined) {
            fail_cb();
        }
    });
    server.requests[0].respond(
        200,
        { "Content-Type": "application/json" },
        JSON.stringify({ result: 1 })
    );
    success.once();
}