frame_switcher = {
    setToHTML:function(){
        var canvasFrame = document.getElementById('canvas');
        canvasFrame.style.display = 'none';
        
        var activeFrame = document.getElementById('location');
        activeFrame.style.display = 'block';
        activeFrame.style.width = '1000px';
        activeFrame.style.height = '600px';
        activeFrame.width = 1000;
        activeFrame.height = 600;

        return activeFrame;
    },
    setToCanvas:function(){
        //todo отцентровка канваса
        var canvasFrame = document.getElementById('location');
        canvasFrame.style.display = 'none';
        
        var activeFrame = document.getElementById('canvas');
        activeFrame.style.display = 'block';
        activeFrame.style.width = '1000px';
        activeFrame.style.height = '600px';
        activeFrame.width = 1000;
        activeFrame.height = 600;

        return activeFrame;
    }
}