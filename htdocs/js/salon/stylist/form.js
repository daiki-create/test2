
$(function() {
    $('#status-active').click(function() {
        $(this).hide();
        $('#status-inactive').show();
        $('input[name=status]').val('0');
    });
    $('#status-inactive').click(function() {
        $(this).hide();
        $('#status-active').show();
        $('input[name=status]').val('1');
    });

});

