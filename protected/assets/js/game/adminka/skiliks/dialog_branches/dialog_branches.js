dialogBranches = {
    draw: function (){
        var html = '';
        html += this.defaultHtml;
        html += jgridController.htmlCode;
        
        jgridController.setParams(this.params);
        grid = jgridController.getHTML();
        
        world.draw(grid.html);
        eval(grid.script);
        
        menuMain.setActive('dialogBranches');
    },
    params: {
            'navId' : 'navgrid',
            'pagerId' : 'pagernav',
            'colNames' : "'№','Название'",
            'colModel' : [
                {
                    'name':"'id'",
                    'index':"'id'",
                    'width':"50",
                    'align' : '"center"',
                    'formatter':"'integer'",
                    'editable':'false'
                    },
                {
                    'name':"'title'",
                    'index':"'title'", 
                    'width':"900",
                    'align' : '"center"',
                    'editable':'true',
                    'editoptions':{
                        'readonly' : 'false',
                        'size' : "10"
                        },
                    'editrules':{
                        'required':'true'
                        }
                    }
                ],
                'sortname' : 'id',
                'caption' : 'Ветки диалога',
                'url' : config.host.name+'index.php/dialogBranches/draw',
                'editurl' : config.host.name+'index.php/dialogBranches/edit',
                'height' : '300',
            'editOptionsA' : {
                    'width' : '450',
                    'height' : '150',
                    'reloadAfterSubmit' : 'true',
                    'closeAfterEdit' : 'true'
                },
            'addOptionsA' : {
                        'width' : '450',
                        'height' : '150',
                        'reloadAfterSubmit' : 'true',
                        'closeAfterAdd' : 'true'
                    }
        }
}