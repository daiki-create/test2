
$(function() {

    $('#enable-questionnaire-btn').click(function() {
        MC.confirm(
            'アンケートを有効にします。<br>よろしいですか。',
            function() {
                $('#enable-questionnaire-form').submit();
            },
            function() {
            },
        );
    });

    $('#disable-questionnaire-btn').click(function() {
        MC.confirm(
            'アンケートを停止します。<br>よろしいですか。',
            function() {
                $('#disable-questionnaire-form').submit();
            },
            function() {
            },
        );
    });

    $('#remove-questionnaire-btn').click(function() {
        MC.confirm(
            'アンケートを削除します。<br>よろしいですか。',
            function() {
                $('#remove-questionnaire-form').submit();
            },
            function() {
            },
        );
    });

});

