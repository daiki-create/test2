$(function() {

    $('#reset-loginpw-form input[name=new_password], #reset-loginpw-form input[name=confirm_password]').keyup(function() {
        if ($('#reset-loginpw-form input[name=new_password]').val().length >= 6 &&
            $('#reset-loginpw-form input[name=confirm_password]').val().length >= 6 &&
            $('#reset-loginpw-form input[name=new_password]').val() === $('#reset-loginpw-form input[name=confirm_password]').val()) {
            $('#reset-loginpw-form input').removeClass('is-invalid').addClass('is-valid');
            $('#reset-loginpw-btn').prop('disabled', false);
        } else {
            $('#reset-loginpw-form input').removeClass('is-valid').addClass('is-invalid');
            $('#reset-loginpw-btn').prop('disabled', true);
        }
    });

});