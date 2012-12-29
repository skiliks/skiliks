var testArray1 = [
    {name:'40%',value:40, color:'#00FF00'},
    {name:'50%',value:50, color:'#FF0000'},
    {name:'10%',value:10, color:'#E8E8E8'}
];

var testArray2 = [
    {name:'',value:2, color:'#00FF00'},
    {name:'',value:2, color:'#FF0000'},
    {name:'',value:90, color:'#E8E8E8'}
];


var bodyHtml = '<div id="pieDiv1"></div>';

$('#body').html(bodyHtml);

function updatePie()
{
    var step = 2;
    
    if(testArray2[1].value < 50){
        testArray2[1].value = testArray2[1].value + step;
    }
    
    if(testArray2[0].value < 40){
        testArray2[0].value = testArray2[0].value + step;
    }
    testArray2[2].value = 100 - testArray2[0].value - testArray2[1].value;
    
    skiliksD3Pie.drawPie(testArray2, '#pieDiv1');
    
    if(testArray2[2].value == 10){
        clearInterval(intervalID);
        drawFinalInterface();
    }
}

var intervalID  = setInterval(function() {
		updatePie();
	}, 75);

function drawFinalInterface()
{
    skiliksD3Pie.drawPie(testArray1, '#pieDiv1');
        	
    setTimeout(tooltip1,500);
    setTimeout(tooltip2,1000);
    setTimeout(tooltip3,1500);
}

function tooltip1(){
    $('.tooltip1').show('1500');
}
function tooltip2(){
    $('.tooltip2').show('1500');
}
function tooltip3(){
    $('.tooltip3').show('1500');
}