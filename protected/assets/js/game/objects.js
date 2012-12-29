objects = {
    /**
     * загрузка обьектов
     */
    loadingObjects : function()
    {
        //сперва загружаем карты
        mapObjects['maps'] = this.loadImgConfig({
            'bgMain': {
                image: window.SKConfig.assetsUrl + '/img/maps/bg-main-light.jpg',
                sWidth: 1000,
                sHeight: 600
            }
        });
        
    },
    loadImgConfig: function (config)
    {
        var configArray = {};
        for (var objectKey in config)
        {
            var object = config[objectKey];
            
            if(object.image !== undefined)
            {
                toLoad = toLoad + 1;

                img = object.image;
                object.imageSrc = new Image();
                object.imageSrc.src = img;
                object.imageSrc.onload = function(){
                    allLoaded = allLoaded + 1;
                }
            }else{
                object = this.loadImgConfig(object);
            }
            
            configArray[objectKey] = object;
            
        }
        return configArray;
    },
    getBounds: function (obj){
      var w=obj.offsetWidth;
      var h=obj.offsetHeight;
      var x=0;
      var y=0;
      while(obj){
        x+=obj.offsetLeft;
        y+=obj.offsetTop;
        obj=obj.offsetParent;
      }
      return{x:x,y:y,width:w,height:h};
    }
    
}