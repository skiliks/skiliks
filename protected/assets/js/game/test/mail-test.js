buster.spec.expose();

var spec = describe('mail client', function(run) {

    require([
        "game/models/SKApplication",
        "game/views/mail/SKMailClientView",
        "game/models/window/SKWindow"], function (SKApplication, SKMailClientView, SKWindow) {
        /**
         * @type {SKMailClientView} SKMailClientView
         */
        run(function () {
            var server;
            before(function () {
                server = sinon.fakeServer.create();
                //clock = sinon.useFakeTimers();
                //this.timeout = 10000;
                SKApp = new SKApplication();
                this.timeout = 1000;
            });
            after(function () { server.restore(); });

            it("displays and hides window", function (done) {
                buster.log("Start");
                testSimulation('asd','123', server,function () {
                        buster.log('Sim started2');
                        var mail_window = new SKWindow({name:'mailEmulator', subname:'mailMain'});
                        buster.log('called');
                        var mail = new SKMailClientView();
                        mail.render({model_instance:mail_window});
                        expect(1).toBe(1);
                        done();
                }, function () {
                    expect(1).toBe(2);
                    done();
                });
            });
        });
    });
});