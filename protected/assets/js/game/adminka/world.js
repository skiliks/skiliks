world = {
    drawDefault: function ()
    {
        this.draw(this.defaultHtml);
    },
    draw:function(string)
    {
        var activeFrame = frame_switcher.setToHTML();
        console.log('frame_switcher: ', frame_switcher);
        var html='';
        html += menuMain.get();
        html += string;
        activeFrame.innerHTML = html;
    },
    defaultHtml: '<div>'+
            '<br><br>Добро пожаловать в админку<br></br>'+
            '</div>'
}