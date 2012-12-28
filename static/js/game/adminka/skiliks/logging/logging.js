logging = {
    draw : function(type){
    $("#location").html(menuMain.get()+'<table id="logging_table"></table><div id="logging_div"></div>');
    menuMain.setActive('logging');
    
    $.ajax({
        url: config.host.name+'index.php/Admin/Log',
        type: "GET",
        data: {data:'json', type:type},
        dataType: "json",
        success: function(json) {
            var colNames = [];
            var colModel = [];
            for(var col in json.headers){
                colNames.push(json.headers[col]);
                colModel.push({name:col,index:col});
            }
            $("#logging_table").jqGrid({
            data: json.data,
            datatype: "local",
            height: 500,
            rowNum: 50,
            rowList: [25,50,100],
            colNames:colNames,
            colModel:colModel,
            pager: "#logging_div",
            loadonce:true,
            rownumbers: true,
            rownumWidth: 40,
            gridview: true,
            viewrecords: true,
            caption: json.title
        });
        $("#logging_div").jqGrid('navGrid','#logging_table',{del:false,add:false,edit:false,search:false});
        $("#logging_table").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false});
        $("#location").append("<a href=\""+config.host.name+'index.php/Admin/Log?type='+type+'&data=csv'+"\">Save CSV</a>");
    }
    });
    
    

    },
    DialogDetail : function(){
        this.draw("DialogDetail");
    },
    DialogPointsDetail : function(){
        this.draw("DialogPointsDetail");
    },
    DialogAggregate : function(){
        this.draw("DialogAggregate");
    },
    ExcelAssessmentDetail : function(){
        this.draw("ExcelAssessmentDetail");
    },
    FullAggregate : function(){
        this.draw("FullAgregatedLog");
    },
    Windows : function(){
        this.draw("Windows");
    },
    MailInDetail : function(){
        this.draw("MailInDetail");
    },
    MailInAggregate : function(){
        this.draw("MailInAggregate");
    },
    MailOutDetail: function(){
        this.draw("MailOutDetail");
    },
    MailOutAggregate: function(){
        this.draw("MailOutAggregate");
    },
    Documents: function(){
        this.draw("Documents");
    },
    DayPlan: function(){
        this.draw("DayPlan");
    },
    Dialogs: function(){
        this.draw("Dialogs");
    },
    LegActionsDetail: function(){
        this.draw("LegActionsDetail");
    }
    
}

