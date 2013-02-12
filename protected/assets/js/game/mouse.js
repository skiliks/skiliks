mouse = {
    /**
     * получаемкоординаты мышки)  старая функа
     */
    getMouseXY: function(e) {
        if (IE) { // grab the x-y pos.s if browser is IE
          mouseX = event.clientX + document.body.scrollLeft;
          mouseY = event.clientY + document.body.scrollTop;
        }
        else {  // grab the x-y pos.s if browser is NS
          mouseX = e.pageX;
          mouseY = e.pageY;
        }  

        if (mouseX < 0){mouseX = 0;}
        if (mouseY < 0){mouseY = 0;}

        return true;
    },

    /**
     * получаемкоординаты мышки)
     */
    mouseLayerXY: function (e)
    {
        if (!e) {e = window.event;e.target = e.srcElement}
        var x = 0;
        var y = 0;

        if (e.layerX)//Gecko
        {
        x = e.layerX - parseInt(this.getElementComputedStyle(e.target, "border-left-width"));
        y = e.layerY - parseInt(this.getElementComputedStyle(e.target, "border-top-width"));
        }
        else if (e.offsetX)//IE, Opera
        {
        x = e.offsetX;
        y = e.offsetY;
        }

        mouseX = x;
        mouseY = y;

        return {x:x,y:y};
    },

    getElementComputedStyle: function (elem, prop)
    {
      if (typeof elem!="object") elem = document.getElementById(elem);

      // external stylesheet for Mozilla, Opera 7+ and Safari 1.3+
      if (document.defaultView && document.defaultView.getComputedStyle)
      {
        if (prop.match(/[A-Z]/)) prop = prop.replace(/([A-Z])/g, "-$1").toLowerCase();
        return document.defaultView.getComputedStyle(elem, "").getPropertyValue(prop);
      }

      // external stylesheet for Explorer and Opera 9
      if (elem.currentStyle)
      {
        var i;
        while ((i=prop.indexOf("-"))!=-1) prop = prop.substr(0, i) + prop.substr(i+1,1).toUpperCase() + prop.substr(i+2);
        return elem.currentStyle[prop];
      }

      return "";
    },
    /**
     * определяем сдвиги для карты
     */
    mapOffsets : function(e)
    {
        var mouseXtemp = mouseX;
        var mouseYtemp = mouseY;
        mouse.mouseLayerXY(e);
        mapOffsetX += (mouseX-mouseXtemp);
        mapOffsetY += (mouseY-mouseYtemp);

        //проверяем оффсет по X

        //не выходим ли мы за рамки карты по минимуму
        if(mapOffsetX<0){mapOffsetX=0;}
        //не выходим ли мы за рамки карты по максимуму
        if(mapOffsetX>(mapSizeX-config.activeFrame.width)){mapOffsetX=(mapSizeX-config.activeFrame.width);}

        //проверяем оффсет по Y

        //не выходим ли мы за рамки карты по минимуму
        if(mapOffsetY<0){mapOffsetY=0;}
        //не выходим ли мы за рамки карты по максимуму
        if(mapOffsetY>(mapSizeY-config.activeFrame.height)){mapOffsetY=(mapSizeY-config.activeFrame.height);}
    }
}