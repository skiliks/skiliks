function testSimulation(email, password, server, cb, fail_cb) {
    _.templateSettings.interpolate = /<@=(.+?)@>/g;
    _.templateSettings.evaluate = /<@(.+?)@>/g;
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
        buster.log("login success");
        var simulation = SKApp.user.startSimulation(1);
        var success = sinon.spy();
        simulation.on('start', success);
        simulation.on('start', function () {
            cb(function () {
                buster.assert.defined(SKApp.user);
                SKApp.user.stopSimulation();
            });

        });
        server.requests[server.requests.length - 1].respond(
            200,
            { "Content-Type": "application/json" },
            JSON.stringify({ result: 1 })
        );
        buster.assert.calledOnce(success);

    });
    SKApp.session.on('login:failure', function () {
        if (fail_cb !== undefined) {
            fail_cb();
        }
    });
    server.requests[server.requests.length - 1].respond(
        200,
        { "Content-Type": "application/json" },
        JSON.stringify({ result: 1 })
    );
    buster.assert.calledOnce(success);
}