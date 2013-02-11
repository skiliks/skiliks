loading = {
    createWaitingDialog: function () {
        var div = document.createElement('div');
        div.setAttribute('id', 'loadingScreen');
        div.setAttribute('class', 'loadingScreen');
        div.style.position = "absolute";
        div.style.zIndex = (100);
        document.body.appendChild(div);

            $("#loadingScreen").dialog({
                    autoOpen: false,
                    dialogClass: "loadingScreenWindow",
                    closeOnEscape: false,
                    draggable: false,
                    width: 460,
                    minHeight: 50, 
                    modal: true,
                    buttons: {},
                    resizable: false,
                    open: function() {
                            // scrollbar fix for IE
                            $('body').css('overflow','hidden');
                    },
                    close: function() {
                            // reset overflow
                            $('body').css('overflow','auto');
                            $('.loadingScreen').remove();
                    }
            }); // end of dialog
    },
    waitingDialog: function (waiting) {
            this.createWaitingDialog();
            $("#loadingScreen").html(waiting.message && '' != waiting.message ? waiting.message : 'Пожалуйста подождите...');
            $("#loadingScreen").dialog('option', 'title', waiting.title && '' != waiting.title ? waiting.title : 'Загрузка');
            $("#loadingScreen").dialog('open');
    },
    closeWaitingDialog: function () {
            $("#loadingScreen").dialog('close');
    }
}