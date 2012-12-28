/*global _, SKLoginView, world:true, session, frame_switcher, php, SKApp */
world = {
    simulations : {},
    drawBody: function()
    {
        "use strict";
        $('.body').html(this.bodyHTML);
    },
    drawWorld: function (simulations){
        "use strict";
        //нам пришли симуляции, или мы просто прервали текущую
        if(typeof(simulations) !== 'undefined'){
            this.simulations = simulations;
        }else{
            simulations = this.simulations;
        }
        
        
        if(session.getSid()){
            this.drawBody();
            var activeFrame = frame_switcher.setToHTML();



            var sim_start = new SKSimulationStartView({'el': activeFrame, 'simulations': simulations});
        }else{
            this.drawDefault();
        }
    },
    drawDefault: function (){
        this.drawBody();
        var activeFrame = frame_switcher.setToHTML();
        var login_view = new SKLoginView({'el': activeFrame});

        var get = php.parseGetParams();
        if(typeof(get.message) !== 'undefined'){
            var message = get.message;
            message = decodeURI(message);
            var lang_alert_title = 'Сообщение';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-success');
        }

        sender.playerCheckSession();
    },
    worldGoToArena: function (){
        arena.drawInterface();
    },
    // bodyHTML is a main game(simulation) screen
    bodyHTML:' <div style="width: 100%;">'+
        '<canvas id="canvas" class="canvas"></canvas>'+
        '<div id="location" class="location"></div>'+
        '</div>'+
        '<div id="message" class="message"></div>'
};