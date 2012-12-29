var allowScroll = true;

//добавляем считывание клавиш
jQuery(document).keypress(function (e) {
     //excel
    excel.keyboardKeyPress(e);
});
//добавляем считывание клавиш
jQuery(document).keyup(function (e) {
     //excel
    excel.keyboardKeyUp(e);
});

/*  keypress    */
$(document).keypress(function(e) {
    if(e.which == 13) {
        //excel
        excel.keyboardSaveEdit();
        //documents
        documents.keyboardEnter();
    }
});

/*  keydown    */

jQuery(document).bind('keydown', 'up',function (evt){
    check_scroll(evt)
    //excel
    excel.keyboardUp();
    //documents
    documents.keyboardUp();
});
jQuery(document).bind('keydown', 'left',function (evt){
    check_scroll(evt)
    //excel
    excel.keyboardLeft();
});
jQuery(document).bind('keydown', 'down',function (evt){
    check_scroll(evt)
    //excel
    excel.keyboardDown();
    //documents
    documents.keyboardDown();
});
jQuery(document).bind('keydown', 'right',function (evt){
    check_scroll(evt)
    //excel
    excel.keyboardRight();
});

jQuery(document).bind('keydown', 'del',function (evt){
    //todo
    dayPlan.fastSwitchDayplanToTodo();
    //mail
    mailEmulator.messageDelete();
});
jQuery(document).bind('keydown', 'Ctrl+c',function (evt){
    //excel
    excel.keyboardApplyCopy();
    excel.keyboardCtrlDown();
});
jQuery(document).bind('keydown', 'Ctrl+v',function (evt){
    //excel
    excel.keyboardApplyPaste();
    excel.keyboardCtrlDown();
});

jQuery(document).bind('keydown', 'esc',function (evt){
    //excel
    excel.cancelEdit();
});

/* keyup  */


jQuery(document).bind('keyup', 'Ctrl',function (evt){
    //excel
    excel.keyboardCtrlUp();
});

function check_scroll(event) {
    
    if(allowScroll==true){return;}
    
    var event = event || window.event;

    event.preventDefault();

    return false;
}