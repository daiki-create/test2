
$(function() {
    $('#delete-stylist-btn').click(function() {
        MC.confirm(
            'スタイリスト登録を削除します。<br>よろしいですか。',
            function() {
                $('#delete-stylist-form').submit();
            },
            function() {
            }
        );
    });
});

