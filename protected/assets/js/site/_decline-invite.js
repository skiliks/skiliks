
$(document).ready(function(){
    $.ajax({
        url: '/dashboard/decline-invite/validation',
        type: 'POST',
        success: function(data) {
            $('.locator-invite-decline-box').hide();
            $('.locator-invite-decline-box').html(data.html);

            // 14.1 ) добавление HTML кода формы
            $('.action-decline-invite').click(function(event) {
                var me = this;
                window.declineInviteId = $(this).attr('data-invite-id');

                $('.locator-invite-decline-box').dialog({
                    dialogClass: 'popup-form-wide background-middle-dark-blue ' +
                        'background-image-book-2 hide-ui-dialog-content',
                    modal:       true,
                    autoOpen:    true,
                    resizable:   false,
                    draggable:   false,
                    width:       getDialogWindowWidth_2of3(),
                    height:      373,
                    position: {
                        my: 'left top',
                        at: 'left top',
                        of: $('.locator-corporate-invitations-list-box')
                    },
                    open: function( event, ui ) {

                        $('#DeclineExplanation_invite_id').val($(me).data('invite-id'));
                        $('.action-close-popup').click(function() {
                            $('.locator-invite-decline-box').dialog('close');
                        });
                    }
                });
            });

            $(window).on('resize', function() {
                $('.locator-invite-decline-box').dialog("option", "width", getDialogWindowWidth_2of3());
                $('.locator-invite-decline-box').dialog("option", "position", "center");
                $('#DeclineExplanation_description').css('width', (getDialogWindowWidth_2of3() - 80) + 'px');
            });

            // 14.2 ) Обработка события "Да, я подтверждаю отказ от приглашения"
            $('.action-confirm-decline').click(function(event){
                var formData = $('#form-decline-explanation').serializeArray();

                $.ajax({
                    url: '/dashboard/decline-invite/validation',
                    data: formData,
                    type: 'POST',
                    success: function(responce) {
                        if (true === responce.isValid) {
                            $('#form-decline-explanation')
                                .attr('action', '/dashboard/decline-invite/' + window.declineInviteId);
                            $('#form-decline-explanation').submit();
                        } else {
                            $('.locator-box-for-validation-response').html(responce.html);
                            $('#form-decline-explanation .locator-form-fields').html(
                                $('.locator-box-for-validation-response .locator-form-fields').html()
                            );
                        }
                    }
                });
            });
        }
    });
});