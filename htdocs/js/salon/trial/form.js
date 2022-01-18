
MC.check_form = function() {
    var filled = true;
    $('input[type=text]:required, input[type=tel]:required, input[type=email]:required, select:required').each(function() {
        var val = $(this).val();
        if (val.length === 0) {
            filled = false;
        }
    });

    if ( ! $('input[name=salon\\[questionnaire_id\\]]:checked').val()) {
        filled = false;
    }

    if (filled) {
        $('#submit-btn').prop('disabled', false);
    } else {
        $('#submit-btn').prop('disabled', true);
    }
};

$(function() {

    $('#whats-questionnaires > .btn').popover({
        trigger: 'focus'
    });

    $('input[name=salon\\[postcode1\\]]').keyup(function(e) {
        var val = $(this).val().replace(/[^\d]/, '');
        if (e.which > 47 && e.which < 106) {
            if (val.length === 3) {
                $('input[name=salon\\[postcode2\\]]').focus();
            }
        }
        $(this).val(val);
    });
    $('input[name=salon\\[postcode2\\]]').keyup(function(e) {
        var val = $(this).val().replace(/[^\d]/, '');
        $(this).val(val);
    });

    $('#phone1').keyup(function(e) {
        var val = $(this).val().replace(/[^\d]/, '');
        if (e.which > 47 && e.which < 106) {
            if (val.length === 3 || val == '03' || val == '06') {
                $('#phone2').focus();
            }
        }
        $(this).val(val);
    });
    $('#salon-phone1').keyup(function(e) {
        var val = $(this).val().replace(/[^\d]/, '');
        if (e.which > 47 && e.which < 106) {
            if (val.length === 3 || val == '03' || val == '06') {
                $('#salon-phone2').focus();
            }
        }
        $(this).val(val);
    });
    $('#salon-fax1').keyup(function(e) {
        var val = $(this).val().replace(/[^\d]/, '');
        if (e.which > 47 && e.which < 106) {
            if (val.length === 3 || val == '03' || val == '06') {
                $('#salon-fax2').focus();
            }
        }
        $(this).val(val);
    });
    $('#phone2').keyup(function(e) {
        var val = $(this).val().replace(/[^\d]/, '');
        if (e.which > 47 && e.which < 106) {
            if (val.length === 4) {
                $('#phone3').focus();
            }
        }
        $(this).val(val);
    });
    $('#salon-phone2').keyup(function(e) {
        var val = $(this).val().replace(/[^\d]/, '');
        if (e.which > 47 && e.which < 106) {
            if (val.length === 4) {
                $('#salon-phone3').focus();
            }
        }
        $(this).val(val);
    });
    $('#salon-fax2').keyup(function(e) {
        var val = $(this).val().replace(/[^\d]/, '');
        if (e.which > 47 && e.which < 106) {
            if (val.length === 4) {
                $('#salon-fax3').focus();
            }
        }
        $(this).val(val);
    });

    $('#search-address-btn').click(function() {
        var postcode = $('input[name=salon\\[postcode1\\]]').val()+'-'+$('input[name=salon\\[postcode2\\]]').val();
        MC.search_address(postcode, $('#address-group'));
    });

    $('input:required, select:required').change(function() {
        MC.check_form();
    });

    MC.check_form();
});

