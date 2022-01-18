
$(function() {

    $('.activate-btn').click(function() {
        var stylist_id = $(this).data('stylist-id');
        if( !stylist_id ){
            return false;
        }
        MC.confirm(
            '「<span class="text-info">有効</span>」に更新します。<br>よろしいですか。',
            function() {
                $('#activate-form input[name=stylist_id]').val(stylist_id);
                $('#activate-form').submit();
            },
            function() {
            }
        );
    });

    $('.deactivate-btn').click(function() {
        var stylist_id = $(this).data('stylist-id');
        if( !stylist_id ){
            return false;
        }
        MC.confirm(
            'この申請を「<span class="text-danger">拒否</span>」します。<br>よろしいですか。',
            function() {
                if( confirm('本当に拒否しても宜しいですか？') ){
                    $('#deactivate-form input[name=stylist_id]').val(stylist_id);
                    $('#deactivate-form').submit();
                }
            },
            function() {
            }
        );
    });


});

