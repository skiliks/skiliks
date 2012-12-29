imageManager = {
    /**
     * разбивка картинки по фреймам
     */
    parsePlayerImage: function (name,obj)
    {
        playerFrames[name] = new Array();
        var counter = 0;
        frameY = 0;

        while(frameY < obj.height)
        {
            frameX = 0;
            while(frameX < obj.width)
            {
                playerFrames[name][counter] = [frameX, frameY];
                counter = counter + 1;
                frameX = frameX + imgConfig.player.sizeX;
            }
            frameY = frameY + imgConfig.player.sizeY;
        }
    }
}