/*global buster, sinon, describe, before, after, require */

buster.spec.expose();

define([
    "game/models/SKApplication",
    "game/models/SKSimulation",
    "game/views/plan/SKDayPlanView",
    "game/models/window/SKWindow"], function (SKApplication, SKSimulation, SKPayPlanView, SKWindow) {

    spec = describe('DayPlan test', function (run) {
        "use strict";
        /**
         * @type {SKMailClientView} SKMailClientView
         */
        run(function () {

            var data_todo = {
                "result":1,
                "data":{
                    "0":{
                        "id":"7",
                        "title":"\u041f\u043e\u0441\u0442\u0430\u0432\u0438\u0442\u044c \u0437\u0430\u0434\u0430\u0447\u0438 \u0441\u043e\u0442\u0440\u0443\u0434\u043d\u0438\u043a\u0430\u043c \u043d\u0430 \u0432\u0440\u0435\u043c\u044f \u043c\u043e\u0435\u0433\u043e \u043e\u0442\u043f\u0443\u0441\u043a\u0430",
                        "duration":90
                    },
                    "16":{
                        "id":"8",
                        "title":"\u041f\u0440\u043e\u0432\u0435\u0440\u0438\u0442\u044c \u0440\u0430\u0431\u043e\u0442\u0443 \u0410\u043d\u0430\u043b\u0438\u0442\u0438\u043a\u0430 2 \u043f\u043e \u0446\u0435\u043d\u043e\u0432\u043e\u0439 \u043f\u043e\u043b\u0438\u0442\u0438\u043a\u0435",
                        "duration":30
                    },
                    "15":{
                        "id":"9",
                        "title":"\u0421\u043e\u0433\u043b\u0430\u0441\u043e\u0432\u0430\u0442\u044c \u0441 \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u0435\u043d\u043d\u044b\u043c \u043e\u0442\u0434\u0435\u043b\u043e\u043c \u043d\u043e\u0432\u0443\u044e \u043e\u0442\u0447\u0435\u0442\u043d\u0443\u044e \u0444\u043e\u0440\u043c\u0443",
                        "duration":30
                    },
                    "14":{
                        "id":"10",
                        "title":"\u0417\u0430\u043f\u0443\u0441\u0442\u0438\u0442\u044c \u0441\u0431\u043e\u0440 \u0438\u043d\u0444\u043e\u0440\u043c\u0430\u0446\u0438\u0438 \u043f\u043e \u043f\u0440\u043e\u0434\u0430\u0436\u0430\u043c 3 \u043a\u0432\u0430\u0440\u0442\u0430\u043b\u0430",
                        "duration":30
                    },
                    "13":{
                        "id":"11",
                        "title":"\u041f\u043e\u0437\u0432\u043e\u043d\u0438\u0442\u044c \u0432 \u0410\u0425\u041e \u043f\u0440\u043e \u0440\u0430\u0431\u043e\u0442\u0443 \u0431\u0430\u0442\u0430\u0440\u0435\u0439 - \u043f\u043b\u043e\u0445\u043e \u0440\u0430\u0431\u043e\u0442\u0430\u044e\u0442",
                        "duration":30
                    },
                    "12":{
                        "id":"12",
                        "title":"\u041f\u043e\u0434\u0433\u043e\u0442\u043e\u0432\u0438\u0442\u044c \u0438\u0442\u043e\u0433\u043e\u0432\u044b\u0439 \u043e\u0442\u0447\u0435\u0442 \"\u041f\u0440\u0438\u0431\u044b\u043b\u0438 \u0438 \u0443\u0431\u044b\u0442\u043a\u0438\" \u0434\u043b\u044f \u0413\u0435\u043d\u0435\u0440\u0430\u043b\u044c\u043d\u043e\u0433\u043e \u0434\u0438\u0440\u0435\u043a\u0442\u043e\u0440\u0430 \u043f\u043e 1 \u043f\u043e\u043b\u0443\u0433\u043e\u0434\u0438\u044e (\u0436\u0434\u0443 \u043f\u0440\u043e\u0442\u043e\u043a\u043e\u043b \u041f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u044f)",
                        "duration":90
                    },
                    "11":{
                        "id":"13",
                        "title":"\u041e\u0442\u0432\u0435\u0442\u0438\u0442\u044c \u043d\u0430 \u0437\u0430\u043f\u0440\u043e\u0441 HR \u043f\u043e \u043d\u043e\u0432\u043e\u0439 \u0441\u0438\u0441\u0442\u0435\u043c\u0435 \u043c\u043e\u0442\u0438\u0432\u0430\u0446\u0438\u0438",
                        "duration":90
                    },
                    "10":{
                        "id":"14",
                        "title":"\u0414\u043e\u043a\u043b\u0430\u0434 \u0413\u0414 \u043d\u0430 \u043a\u043e\u043d\u0444\u0435\u0440\u0435\u043d\u0446\u0438\u0438 \u0432 \u0434\u0435\u043a\u0430\u0431\u0440\u0435 ",
                        "duration":180
                    },
                    "9":{
                        "id":"15",
                        "title":"\u0420\u0430\u0441\u0441\u043a\u0430\u0437\u0430\u0442\u044c \u043c\u043e\u0438\u043c \u0441\u043e\u0442\u0440\u0443\u0434\u043d\u0438\u043a\u0430\u043c \u043e \u043d\u043e\u0432\u043e\u0439 \u0441\u0438\u0441\u0442\u0435\u043c\u0435 \u043f\u0440\u0435\u043c\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u044f \u0441 4 \u043a\u0432. ",
                        "duration":30
                    },
                    "8":{
                        "id":"16",
                        "title":"\u041f\u043e\u0441\u043c\u043e\u0442\u0440\u0435\u0442\u044c \u0434\u043e\u0433\u043e\u0432\u043e\u0440 \u043e\u0442 \u044e\u0440\u0438\u0441\u0442\u043e\u0432 (\u0443\u0436\u0435 \u0442\u0440\u0435\u0442\u0438\u0439 \u0440\u0430\u0437 \u043f\u0440\u0438\u0441\u044b\u043b\u0430\u044e\u0442)",
                        "duration":60
                    },
                    "7":{
                        "id":"17",
                        "title":"\u041f\u0440\u043e\u0432\u0435\u0440\u0438\u0442\u044c, \u0447\u0442\u043e \u0441\u0434\u0435\u043b\u0430\u043b \u0430\u043d\u0430\u043b\u0438\u0442\u0438\u043a 1 \u043f\u043e \u0437\u0430\u0434\u0430\u0447\u0435 \u043b\u043e\u0433\u0438\u0441\u0442\u0438\u0447\u0435\u0441\u043a\u043e\u0433\u043e \u043e\u0442\u0434\u0435\u043b\u0430.  \u0422\u0440\u0443\u0434\u044f\u043a\u0438\u043d \u043f\u0440\u043e\u0441\u0438\u043b \u0441\u0435\u0433\u043e\u0434\u043d\u044f.",
                        "duration":30
                    },
                    "6":{
                        "id":"18",
                        "title":"\u0412\u0441\u0442\u0440\u0435\u0442\u0438\u0442\u044c\u0441\u044f \u0441 \u0430\u043d\u0430\u043b\u0438\u0442\u0438\u043a\u043e\u043c 3 \u043f\u043e \u0440\u0435\u0437\u0443\u043b\u044c\u0442\u0430\u0442\u0430\u043c \u0438\u0441\u043f\u044b\u0442\u0430\u0442\u0435\u043b\u044c\u043d\u043e\u0433\u043e \u0441\u0440\u043e\u043a\u0430",
                        "duration":60
                    },
                    "5":{
                        "id":"19",
                        "title":"\u0421\u0440\u043e\u0447\u043d\u043e \u0434\u043e\u0434\u0435\u043b\u0430\u0442\u044c \u0441\u0432\u043e\u0434\u043d\u044b\u0439 \u0431\u044e\u0434\u0436\u0435\u0442",
                        "duration":180
                    },
                    "4":{
                        "id":"20",
                        "title":"\u041f\u0440\u043e\u0432\u0435\u0440\u0438\u0442\u044c \u043f\u0440\u0435\u0437\u0435\u043d\u0442\u0430\u0446\u0438\u044e \u0434\u043b\u044f \u0413\u0414 \u0432 \u0447\u0435\u0442\u0432\u0435\u0440\u0433. \u041a\u0440\u0443\u0442\u044c\u043a\u043e \u043e\u0431\u0435\u0449\u0430\u043b\u0430 \u0432 15.30",
                        "duration":30
                    },
                    "3":{
                        "id":"21",
                        "title":"\u0412\u0441\u0442\u0440\u0435\u0447\u0430 \u0441 \u043a\u043b\u0438\u0435\u043d\u0442\u043e\u043c \u043f\u043e \u0430\u0432\u0442\u043e\u043c\u0430\u0442\u0438\u0437\u0430\u0446\u0438\u0438 \u0434\u0430\u043d\u043d\u044b\u0445, \u043f\u0440\u0435\u0434\u0432\u0430\u0440\u0438\u0442\u0435\u043b\u044c\u043d\u043e \u0447\u0435\u0442\u0432\u0435\u0440\u0433, \u0447\u0430\u0441 \u0438\u043b\u0438 \u0434\u0432\u0430 \u0434\u043d\u044f",
                        "duration":60
                    },
                    "2":{
                        "id":"22",
                        "title":"\u0421\u043e\u0432\u0435\u0449\u0430\u043d\u0438\u0435 \" \u041e \u0441\u0442\u0430\u0440\u0442\u0435 \u0433\u043e\u0434\u043e\u0432\u043e\u0439 \u0430\u0442\u0442\u0435\u0441\u0442\u0430\u0446\u0438\u0438\", \u0441\u0435\u0433\u043e\u0434\u043d\u044f, 17.00",
                        "duration":60
                    },
                    "1":{
                        "id":"24",
                        "title":"\u041e\u0431\u0435\u0434",
                        "duration":30
                    },
                    "17":{
                        "id":"25",
                        "title":"\u0423\u0439\u0442\u0438 \u0441 \u0440\u0430\u0431\u043e\u0442\u044b \u043d\u0430 \u0434\u0435\u043d\u044c \u0440\u043e\u0436\u0434\u0435\u043d\u0438\u044f \u0442\u0435\u0449\u0438 (\u0437\u0430\u0435\u0445\u0430\u0442\u044c \u0437\u0430 \u0446\u0432\u0435\u0442\u0430\u043c\u0438)",
                        "duration":60
                    }
                }
            };

            var data_day_plan = {
                "result":1,
                "data":[
                    {
                        "date":"16:00",
                        "task_id":"23",
                        "day":"1",
                        "title":"\u0412\u0441\u0442\u0440\u0435\u0447\u0430 \u0441 \u0413\u0414 \u0432 16.00 \u043f\u043e \u043f\u0440\u0435\u0437\u0435\u043d\u0442\u0430\u0446\u0438\u0438",
                        "duration":"30",
                        "type":"1"
                    }
                ]
            };

            _.templateSettings.interpolate = /<@=(.+?)@>/g;
            _.templateSettings.evaluate = /<@(.+?)@>/g;
            var server;
            before(function () {
                server = sinon.fakeServer.create();

                server.respondWith("POST", "/index.php/simulation/start",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify({result:1})]);

                server.respondWith("POST", "/index.php/todo/get",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify(data_todo)]);

                server.respondWith("POST", "/index.php/dayPlan/get",
                    [200, { "Content-Type":"application/json" },
                        JSON.stringify(data_day_plan)]);

                //this.timeout = 10000;
                window.SKApp = new SKApplication({'start':'9:00', "skiliksSpeedFactor":8 });
                this.timeout = 1000;
            });
            after(function () {
                server.restore();
            });

            it("day plan", function () {

                var simulation = SKApp.simulation;
                simulation.start();
                var plan_window = new SKWindow({name:'plan', subname:'plan'});
                plan_window.open();

                var planView = new SKPayPlanView({model_instance:plan_window});
                planView.render();


                server.respond();

                //console.log(planView.$('.planner-task.day-plan-todo-task.regular.locked').find(".title").text());

                expect(planView.$('.planner-task.day-plan-todo-task.regular.locked').find(".title").text()).toBe('Встре­ча с ГД в 16.00 по пре­зен­та­ции');
                //expect(1).toBe(1);

            });
        });
    });
});