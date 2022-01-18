MC.check_lp_date = function($modal) {
    var $since_date = $modal.find('input[name=since_date]');
    var $until_date = $modal.find('input[name=until_date]');

    if ($until_date.val() !== '') {
        $since_date.prop('max', $until_date.val());
    } else {
        $since_date.prop('max', '');
    }

    $until_date.removeClass('is-invalid').addClass('is-valid');
    $since_date.removeClass('is-invalid').addClass('is-valid');

    if ($until_date.val() !== '' && $since_date.val() !== '') {
        if ($since_date.val() > $until_date.val()) {
            $since_date.addClass('is-invalid').removeClass('is-valid');
            $until_date.addClass('is-invalid').removeClass('is-valid');
            $modal.find('button[type=submit]').prop('disabled', true);
            $modal.find('.alert-danger').text('終了日は開始日と同じかそれ以降の日付で設定してください。').fadeIn('slow').removeClass('d-none');
        } else {
            $modal.find('button[type=submit]').prop('disabled', false);
            $modal.find('.alert-danger').text('').addClass('d-none');
        }
    }
};

// -------------------------------------------------------------------------

$(function() {

    $('#update-lp-modal').on('show.bs.modal', function(e) {
        var $tr = $(e.relatedTarget).parents('tr');
        if ($tr.length > 0) {
            var landing_page_id = $tr.data('landing-page-id');
            var since_date = $tr.find('.since-date').text();
            var until_date = $tr.find('.until-date').text();
            var lp_url = $tr.find('.lp-url').text();

            $(this).find('input[name=landing_page_id]').val(landing_page_id);
            if (since_date !== '') {
                $(this).find('input[name=since_date]').val(moment(since_date).format('YYYY/MM/DD'));
            }
            if (until_date !== '') {
                $(this).find('input[name=until_date]').val(moment(until_date).format('YYYY/MM/DD'));
            }
            $(this).find('input[name=lp_url]').val(lp_url);
        }
    });

    $('#delete-landing-page').click(function() {
        MC.confirm(
            'ランディングページを削除します。<br>よろしいですか。',
            function() {
                $('#delete-lp-form').submit();
            },
            function() {
            },
        );
    });

    $('#since-datepicker').datetimepicker(MC.datepick_option);
    $('#until-datepicker').datetimepicker(MC.datepick_option);
    $('#update-since-datepicker').datetimepicker(MC.datepick_option);
    $('#update-until-datepicker').datetimepicker(MC.datepick_option);

    $('#since-datepicker').on('change.datetimepicker', function(e){
        MC.check_lp_date($('#lp-modal'));
    });
    $('#until-datepicker').on('change.datetimepicker', function(e){
        MC.check_lp_date($('#lp-modal'));
    });
    $('#update-since-datepicker').on('change.datetimepicker', function(e){
        MC.check_lp_date($('#update-lp-modal'));
    });
    $('#update-until-datepicker').on('change.datetimepicker', function(e){
        MC.check_lp_date($('#update-lp-modal'));
    });

});

