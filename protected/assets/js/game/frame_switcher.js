frame_switcher = {
    setToHTML:function(){
        var canvasFrame = document.getElementById('canvas');
        canvasFrame.style.display = 'none';
        
        var activeFrame = document.getElementById('location');
        activeFrame.style.display = 'block';
        activeFrame.width = config.activeFrame.width;
        activeFrame.height = config.activeFrame.height;
        activeFrame.style.width = config.activeFrame.width;
        activeFrame.style.height = config.activeFrame.height;
        
        return activeFrame;
    },
    setToCanvas:function(){
        //todo отцентровка канваса
        var canvasFrame = document.getElementById('location');
        canvasFrame.style.display = 'none';
        
        var activeFrame = document.getElementById('canvas');
        activeFrame.style.display = 'block';
        activeFrame.width = config.activeFrame.width;
        activeFrame.height = config.activeFrame.height;
        activeFrame.style.width = config.activeFrame.width;
        activeFrame.style.height = config.activeFrame.height;
        
        return activeFrame;
    }
}