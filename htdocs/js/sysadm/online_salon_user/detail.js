
$(function() {

    $('#left-btn').click(function() {
        MC.confirm(
            'この会員を「<span class="text-danger">退会</span>」させます。<br>よろしいですか。',
            function() {
                if( confirm('本当に退会させても宜しいですか？') ){
                    $('#left-form').submit();
                }
            },
            function() {
            }
        );
    });


    $('#delete-btn').click(function() {
        MC.confirm(
            'この仮登録を「<span class="text-danger">削除</span>」します。<br>よろしいですか。',
            function() {
                if( confirm('本当に削除しても宜しいですか？') ){
                    $('#delete-form').submit();
                }
            },
            function() {
            }
        );
    });

    $('#charge-ignore-btn').click(function() {
        MC.confirm(
            'この会員の課金を「<span class="text-danger">免除</span>」します。<br>よろしいですか。',
            function() {
                $('#charge-ignore-form input[name=flag]').val(1);
                $('#charge-ignore-form').submit();
            },
            function() {
            }
        );
    });

    $('#charge-ignore-chancel-btn').click(function() {
        MC.confirm(
            '課金免除を「<span class="text-danger">解除</span>」します。<br>よろしいですか。',
            function() {
                $('#charge-ignore-form input[name=flag]').val(0);
                $('#charge-ignore-form').submit();
            },
            function() {
            }
        );
    });

});

