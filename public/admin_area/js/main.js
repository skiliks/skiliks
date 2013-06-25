$(document).ready(function(){
    $(".reset-invite").click(function() {

        var simulationTimeStart = $(this).parents(".invites-row").find(".simulation_time-start").text();
        var simulationTimeEnd = $(this).parents(".invites-row").find(".simulation_time-end").text();
        var receiverUserEmail = $(this).parents(".invites-row").find(".receiverUser-email").text();
        return confirm("Вы точно хотите откатить симуляцию от "+simulationTimeStart+" для "+receiverUserEmail+" за "+simulationTimeEnd+"(дата)");

    });

    $(".action-invite-status").click(function() {

        return confirm("Вы точно хотите сменить статус?");

    });
});