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
    server.respondWith("POST", "/index.php/todo/get",
        [200, { "Content-Type": "application/json" },
            JSON.stringify({result: 1})]);
    server.respondWith("POST", "/index.php/dayPlan/get",
        [200, { "Content-Type": "application/json" },
            JSON.stringify({result: 1})]);

    buster.log(server.requests.length);
    SKApp.user = {};
    SKApp.user.simulation = {};
    var success = sinon.spy();
    simulation.on('start', success);
    simulation.on('start', function () {
        cb(function (done) {
            buster.assert.defined(SKApp.user);
            //simulation.on('stop', done);
            var stop_spy = sinon.spy();
            simulation.on('stop', stop_spy);
            SKApp.user.stopSimulation();
            server.requests[server.requests.length - 1].respond(
                200,
                { "Content-Type": "application/json" },
                JSON.stringify({ result: 1 })
            );
            buster.assert.calledOnce(stop_spy);
            done();
        });

    });
    server.requests[server.requests.length - 1].respond(
        200,
        { "Content-Type": "application/json" },
        JSON.stringify({ result: 1 })
    );
    buster.assert.calledOnce(success);

}