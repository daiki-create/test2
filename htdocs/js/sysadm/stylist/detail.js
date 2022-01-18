
$(function() {
    $('#delete-stylist-btn').click(function() {
        MC.confirm(
            'スタイリストを削除します。<br>よろしいですか。',
            function() {
                $('#delete-stylist-form').submit();
            },
            function() {
            }
        );
    });

    $('#enable-stylist-btn').click(function() {
        MC.confirm(
            'スタイリストの状態を<br>「<span class="text-info">有効</span>」にします。<br>よろしいですか。',
            function() {
                $('#enable-stylist-form').submit();
            },
            function() {
            }
        );
    });

    $('#disable-stylist-btn').click(function() {
        MC.confirm(
            'スタイリストの状態を<br>「<span class="text-warning">停止</span>」にします。<br>よろしいですか。',
            function() {
                $('#disable-stylist-form').submit();
            },
            function() {
            }
        );
    });

    $('#select-salon-modal .select-salon-btn').on('click', function() {
        var salon_id = $(this).data('salon-id');
        var salon_name = $.trim($(this).parents('tr').find('.salon-name').text());
        MC.confirm(
            $('#stylist-name').text()+'さんを<br>「'+salon_name+'」<br>に所属登録します。<br>よろしいですか。',
            function() {
                $('#select-salon-modal').modal('hide');
                $('#belon-to-salon-form > input[name=salon_id]').val(salon_id);
                $('#belon-to-salon-form').submit();
            },
            function() {
            }
        );
    });

});

