addAssessment = {
    issetDiv: false,
    divTop: 50,
    divLeft: 50,

    setDivTop: function(val)
    {
        this.divTop = val;
    },
    setDivLeft: function(val)
    {
        this.divLeft = val;
    },
    createDiv: function()
    {
        var div = document.createElement('div');
          div.setAttribute('id', 'addAssessmentMainDiv');
          div.setAttribute('class', 'addAssessmentMainDiv');
          div.style.position = "absolute";
          div.style.zIndex = "50";
          document.body.appendChild(div);
          $('#addAssessmentMainDiv').css('top', this.divTop+'px');
          $('#addAssessmentMainDiv').css('left',  this.divLeft+'px');
          $('#addAssessmentMainDiv').css('right',  this.divLeft+'px');
          
          this.issetDiv = true;
          
          var html = this.html;
          $('#addAssessmentMainDiv').html(html);
    },
    draw: function()
    {
        if(session.getSimulationType()!='dev'){return;}
        
        sender.addAssessmentGetList();
        sender.excelPointsDraw();
    },
    drawInterface: function(data)
    {
        if(!this.issetDiv){
            this.createDiv();
        }
        
        var AVGsumm = 0;
        var AVGsumm6x = 0;
        var AVGsummNegative = 0;
        var html = this.html;
        
        var points = '';
        points += 'Длительность: '+data['duration']+'<br>';
        points += 'Оценки поведений:'+'<br>';
        
        for (var key in data['points'])
        {
            var value = data['points'][key];
                
            if(value['count6x']=='null'){value['count6x'] = 0;}
            if(value['value6x']=='null'){value['count6x'] = 0;}
            if(value['avg6x']=='null'){value['count6x'] = 0;}
            
            points += value['code']+' - '+value['title']+' = '+value['avg']+';'+
                ' count6x:'+value['count6x']+
                ', value6x:'+value['value6x']+
                ', avg6x:'+value['avg6x']+
                
                ', countNegative:'+value['countNegative']+
                ', valueNegative:'+value['valueNegative']+
                ', avgNegative:'+value['avgNegative']+
                '<br>';
            AVGsumm += parseFloat(value['avg']);
            AVGsumm6x += parseFloat(value['avg6x']);
            AVGsummNegative += parseFloat(value['avgNegative']);
        }
        
        points += '<strong class="result-total">Сумма оценок: '+AVGsumm+'</strong><br />';
        points += '<strong class="result-total-6x">Сумма оценок 6x: '+AVGsumm6x+'</strong><br />';
        points += '<strong class="result-total-negative">Сумма оценок Negative: '+AVGsummNegative+'</strong><br />';
        html = php.str_replace('{points}', points, html);
        $('#addAssessmentMainForm').html(points);
    },
    drawExcelPoints:function(data)
    {
        var html = '';
        var summ = 0;
        
        for (var key in data)
        {
            var value = data[key];
            html += '<font style="width:300px;">'+value['formula']+' <b>=</b> </font>';
            html += '<font style="width:100px;">'+value['value']+'</font><br>';
            summ += parseFloat(value['value']);
        }
        
        html += '<b>Сумма оценок: '+summ+'</b><br>';
        
        $('#addExcelPointsMainForm').html(html);
    },
    html:'<form class="well" id="addAssessmentMainForm">'+
        '{points}'+
        '</form>'+
        '<form class="well" id="addExcelPointsMainForm">'+
        '{points}'+
        '</form>'
}