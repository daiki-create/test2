
$(function() {

    $('input[name=agreement]').change(function() {
        if ($(this).prop('checked')) {
            $('#submit-btn').prop('disabled', false);
        } else {
            $('#submit-btn').prop('disabled', true);
        }
    });

});

