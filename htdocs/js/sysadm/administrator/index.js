
MC.check_form = function() {
    var fill = true;
    $('#administrator-form input[type=text], #administrator-form input[type=email]').each(function() {
        var val = $(this).val();
        if (val.length === 0) {
            fill = false;
        }
    });
    if (fill) {
        $('#administrator-modal button[type=submit]').prop('disabled', false);
    } else {
        $('#administrator-modal button[type=submit]').prop('disabled', true);
    }
};

$(function() {

    $('#administrator-modal').on('show.bs.modal', function(e) {
        var $tr = $(e.relatedTarget).parents('tr');
        console.log($tr);
        if ($tr.length > 0) {
            var admin_id = $tr.data('admin-id');
            var name = $tr.data('name');
            var loginid = $tr.data('loginid');
            var status = $tr.data('status');
            $('input[name=admin_id]').val(admin_id);
            $(this).find('input[name=name]').val(name);
            $(this).find('input[name=loginid]').val(loginid);
            if (status === 1) {
                $(this).find('input[name=status]').bootstrapToggle('on');
            } else {
                $(this).find('input[name=status]').bootstrapToggle('off');
            }
            $('#administrator-modal button[type=submit]').prop('disabled', true);
        }
    });

    $('#delete-administrator-btn').click(function() {
        MC.confirm(
            'システム管理者を削除します。<br>よろしいですか。',
            function() {
                $('#delete-administrator-form').submit();
            },
            function() {}
        );
    });

    $('#reset-password-btn').click(function() {
        MC.confirm(
            'パスワードをリセットします。<br>よろしいですか。',
            function() {
                $('#reset-password-form').submit();
            },
            function() {}
        );
    });

    $('#administrator-modal input[type=text], #administrator-modal input[type=email], #administrator-modal input[name=status]').change(function() {
        MC.check_form();
    });

});

