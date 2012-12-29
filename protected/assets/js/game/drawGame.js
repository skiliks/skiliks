drawGame = {
    //image,sx,sy,sWidth,sHeight,centerX,cenerY,dWidth,dHeight,angle,scaleX,alpha
    drawObjects:[],
    drawController: function(){},
    drawKeyController: function(keyCode){},
    /**
     * отрисовка игрового поля (повторяющаяся байда)
     */
    drawGame : function (canvas)
    {
        var sf = imgConfig.sizeFactor;
        
        //если не все загружно - нафиг
        if(allLoaded != toLoad){
            return;
        }

        canvas.clearRect(0,0,1000,600);
        canvas.beginPath();
        
        //отрисовываем обьекты на карте  НАЧАЛО
        for (var objectKey in drawGame.drawObjects)
        {
            var object = drawGame.drawObjects[objectKey];
            canvas.save();
            canvas.translate( sf*object.centerX, sf*object.cenerY);
            canvas.rotate(mathematics.get_radian(object.angle));
            canvas.scale(object.scaleX, 1);
            canvas.globalAlpha = object.alpha;
            canvas.drawImage(object.imageSrc, object.sx, object.sy, object.sWidth, object.sHeight, (-sf*object.dWidth/2), (-sf*object.dHeight/2), sf*object.dWidth, sf*object.dHeight);
            canvas.restore();
        }
        //отрисовываем остальные на карте  КОНЕЦ
        
        
        
         //debug info
        /*canvas.fillStyle    = '#00f';
        canvas.font         = 'italic 16px sans-serif';
        canvas.textBaseline = 'top';
        canvas.fillText  ('Map x,y:'+mapSizeX+','+mapSizeY+'; Map offsets: '+mapOffsetX+', '+mapOffsetY + '; Mouse X,Y:'+mouseX+', '+mouseY, 0, 0);
        */
       
       
        canvas.closePath();

        //обновляем статусы флажков, что новые изменения вошли в силу
        for (var frameToChange in frameChanged)
        {
            frameChanged[frameToChange] = 1;
        }
        //перенаправляем на родителя - обработчик
        drawGame.drawController();
    }
    
}