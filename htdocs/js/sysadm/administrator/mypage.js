
MC.check_input = function() {
    var loginpw = $('#change-password-modal input[name=loginpw]').val();
    var confirm_loginpw =  $('#change-password-modal input[name=confirm_loginpw]').val();

    if (loginpw.length >= 6 && loginpw == confirm_loginpw) {
        $('#submit-change-password-btn').prop('disabled', false);
    }
};

$(function() {

    $('#change-password-modal input[name=loginpw], #change-password-modal input[name=confirm_loginpw]').change(function() {
        MC.check_input();
    });

    $('#submit-change-password-btn').click(function() {
        MC.confirm(
            'パスワードを更新します。<br>よろしいですか。',
            function() {
                $('#update-password-form').submit();
            },
            function() {
            }
        );
    });

});

