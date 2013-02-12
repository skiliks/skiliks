mathematics = {
    /*
     * получение угла в радианах из угла в градусах
     * 
     * @param int angle
     * @return int
     */
    get_radian :function(angle)
    {
        return (angle*(Math.PI/180));
    },
    /*
     * получение угла в градусах из угла в радианах
     * 
     * @param int radian
     * @return int
     */
    get_angle :function(radian)
    {
        return (radian/(Math.PI/180));
    },
    /*
     * получение числа в заданном диапазоне
     * 
     * @param int min
     * @param int max
     * @return int
     */
    getRandom: function(min, max)
    {
      var random = Math.random() * (max - min) + min;

      return random.toFixed(0);
    },
    /*
     * расчет центра картинки игрока
     * 
     * @param string player //имя игрока
     * @return array
     */
    calcPlayerImageCenter: function (player)
    {
        var centerX = parseInt(players[player].x) + imgConfig.player.sizeX/2;
        var centerY = parseInt(players[player].y) + imgConfig.player.sizeY/2;

        return [centerX, centerY];
    },
    /*
     * расчет коэфициентов для уравнения прямой
     * 
     * @param int x1
     * @param int y1
     * @param int x2
     * @param int y2
     * @return array
     */
    lineEquationFactorsBy2Points: function(x1,y1, x2,y2)
    {
        //ax+by+c=0
        var a = 0;
        var b = 0;
        var c = 0;
        var k = false;
        
        if(x1 != x2){
            k = (y2-y1)/(x1-x2);
            b = 1;
            a = k;
            c = -(k*x1+y1);
        }else if(y1 != y2){
            b = 0;
            a = 1;
            c = -x1
        }else{
            //logic error, 2 same points
            messages.dw_alert('mathematics lineEquationFactorsBy2Points critical error, we hawe 2 same points');
            return false;
        }
        
        return [a,b,c];
    },
    /*
     * расчет точки пересечения 2-х прямых
     * 
     * @param int a1
     * @param int b1
     * @param int c1
     * @param int a2
     * @param int b2
     * @param int c2
     * @return array
     */
    intersectionPoint2Lines: function(a1,b1,c1,a2,b2,c2)
    {
        var x = 0;
        var y = 0;
        
        if((a1*b2-a2*b1)==0){
            return false;
        }
        
        if(a1!=0){
            if( (b2-(b1*a2)/a1) == 0 ){
                y = 0;
            }else{
                y = (-c2+(c1*a2)/a1)/(b2-(b1*a2)/a1);
            }
            x = (-b1*y-c1)/a1;
        }else{
            if(b1!=0){
                if((a2-(a1*b2)*b1) == 0){
                    x = 0;
                }else{
                    x = (-c2+(c1*b2)/b1)/(a2-(a1*b2)*b1);
                }
                y = (-a1*x-c1)/b1;
            }else{
                messages.dw_alert('mathematics intersectionPoint2Lines critical error, we hawe non line factors');
                return false;
            }
        }
        
        x = x.toFixed(5);
        y = y.toFixed(5);
        
        return [x,y];
    },
    /*
     * проверка на вхождение точки в заданный отрезок
     * 
     * @param int x1
     * @param int y1
     * @param int x2
     * @param int y2
     * @param int px
     * @param int py
     * @return boolean
     */
    checkEntryPointIntoTheSegment: function(x1,y1, x2,y2, px,py)
    {
        var xf = (x1-px)*(x2-px);
        var yf = (y1-py)*(y2-py);
        
        if(xf==0 && yf==0){
            //точка является одной из исходных точек
            return 2;
        }else if(xf<=0 && yf<=0){
            //точка входит в отрезок
            return 1;
        }else{
            //точка не входит в отрезок
            return 0;
        }
    },
    /*
     * расчет угла по 3-м точкам, вершина - точка2 результат в радианах
     * 
     * @param object point1
     * @param object point1
     * @param object point1
     * @return int
     */
    getAngleBy3Points: function(point1,point2,point3)
    {
        var angle = 0;
        //cos cornerG = (a*a+b*b-c*c)/(2*a*b)
        //c= 13  a=12  b=23
        var c = this.getLengthBy2Points(point1,point3);
        var a = this.getLengthBy2Points(point1,point2);
        var b = this.getLengthBy2Points(point2,point3);
        
        if(a!=0 && b!=0){
            var cornerRcos = (a*a+b*b-c*c)/(2*a*b);
            angle = Math.acos(cornerRcos);
        }
        
        return angle;
    },
    /*
     * расчет длины отрезка по двум его точкам
     * 
     * @param object point1
     * @param object point1
     * @return int
     */
    getLengthBy2Points: function(point1,point2)
    {
        return Math.sqrt( (point2.x-point1.x)*(point2.x-point1.x)+(point2.y-point1.y)*(point2.y-point1.y) );
    }
}