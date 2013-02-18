//задаем ид текущего юзера
//var curUserId = mathematics.getRandom(1,10000);

var IE='\v'=='v';

//определяем глобальные переменные НАЧАЛО
var allLoaded = 0;
var toLoad = 0;
var frameChanged = [];

var mouseX = 0;
var mouseY = 0;
var mapOffsetX = 0;
var mapOffsetY = 0;
var mapSizeX = 0;
var mapSizeY = 0;

//type, params
var mapObjects = [];

//определяем глобальные переменные КОНЕЦ

/**
 * слушаем устройство ввода (клаву)
 */
var input = function()
{
    /*
     * 65 - a
     * 83 - s
     * 87 - w
     * 68 - d
     * 
     * 37 - <
     * 38 - ^
     * 39 - >
     * 40 - \/
     */
    var keyCode = '';
    
    if (keyboard[37]) keyCode='left'; //<
    if (keyboard[38]) keyCode='up'; //^
    if (keyboard[39]) keyCode='right'; //>
    if (keyboard[40]) keyCode='down'; //\/
    
    if(keyCode!=''){
        drawGame.drawKeyController(keyCode);
    }
};

/**
 * запуск движка (инилка)
 */
function runGame()
{
    //objects.loadingObjects();
}

/**
 * запуск движка (инилка)
 */
function stopGame()
{
	dante.stop();
}

function close()
{
    messages.dw_alert('Вы были отключены от игрового сервера');

    world.drawDefault();
}