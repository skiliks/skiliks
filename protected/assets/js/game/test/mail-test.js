buster.spec.expose();

var spec = describe('mail client', function(run) {
    require(["game/models/SKApplication", "game/views/mail/SKMailClientView"], function (SKApplication, SKMailClientView) {
        run(function () {
            before(function () {
                server = sinon.fakeServer.create();
                //clock = sinon.useFakeTimers();
                //this.timeout = 10000;
            });
            after(function () { server.restore(); clock.restore(); });

            it("displays and hides window", function (done) {
                buster.log("Start");
                testSimulation('asd','123', server,function () {
                    buster.log("init done");
                    $(function() {
                        var mail = new SKMailClientView();
                        mail.render();
                        expect(1).toBe(1);
                        done();
                    });
                }, function () {
                    expect(1).toBe(2);
                    done();
                });
            });
        });
    });
});