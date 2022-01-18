
$(function() {
    $('#search-address-btn').click(function() {
        var postcode = $('input[name=postcode1]').val()+'-'+$('input[name=postcode2]').val();
        MC.search_address(postcode, $('#address-group'));
    });
});
