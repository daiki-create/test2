MC.new_num = 1;

MC.reset_order = function() {

    var len = $('#questions-table > tbody').length;
    $('#questions-table > tbody').each(function(i, tbody) {
        if (i > 0) {
            $(tbody).find('.question-number').text(i).next('input.question-number-input').val(i);
        }
        if (i === 1) {
            $(tbody).find('.priority-up-btn').hide();
        } else if (i === (len - 1)) {
            $(tbody).find('.priority-up-btn').show();
            $(tbody).find('.priority-down-btn').hide();
        } else if (i > 1) {
            $(tbody).find('.priority-up-btn').show();
            $(tbody).find('.priority-down-btn').show();
        }
    });
};

$(function() {

    $('.select-type-menu > a').click(function(e) {
        e.preventDefault();
        var $tbody = $(this).parents('tbody');
        var $badge = $(this).children('span').clone(false);
        var type = $(this).data('type');
        $(this).parent('.select-type-menu').prev('.select-type').html($badge);
        $(this).parent('.select-type-menu').next('input').val(type);

        var has_sub_question = $(this).parents('tr').find('.has-sub-question').prop('checked');
        $tbody.find('.nps-flag-radio').hide();
        $tbody.find('.nps-flag').prop('checked', false);
        $tbody.find('.nps-correlation-checkbox').hide();
        $tbody.find('.nps-correlation').prop('checked', false);

        if (type == 'level') {
            $tbody.find('.nps-flag-radio').show();
            $tbody.find('.nps-correlation-checkbox').show();
            $tbody.children('tr.level-tr').show();
            if (has_sub_question)
                $tbody.children('tr').not('.space-tr, .question-tr, .level-tr, .sub-question-label-tr').hide();
            else
                $tbody.children('tr').not('.space-tr, .question-tr, .level-tr').hide();
        } else if (type == 'select_one' || type == 'select_multi') {
            if (type == 'select_one') {
                $tbody.find('.list-group .custom-control').removeClass('custom-checkbox').addClass('custom-radio').children('input').attr('type', 'radio').prop('checked', false).last().prop('checked', true);
                $tbody.find('.append-select-multi-btn').hide();
                $tbody.find('.append-select-one-btn').show();
            } else if (type == 'select_multi') {
                $tbody.find('.list-group .custom-control').removeClass('custom-radio').addClass('custom-checkbox').children('input').attr('type', 'checkbox').prop('checked', false).last().prop('checked', true);
                $tbody.find('.append-select-one-btn').hide();
                $tbody.find('.append-select-multi-btn').show();
            }
            $tbody.children('tr.selection-tr').show();
            if (has_sub_question)
                $tbody.children('tr').not('.space-tr, .question-tr, .selection-tr, .sub-question-label-tr').hide();
            else
                $tbody.children('tr').not('.space-tr, .question-tr, .selection-tr').hide();
        } else if (type == 'text') {
            $tbody.children('tr.text-tr').show();
            if (has_sub_question)
                $tbody.children('tr').not('.space-tr, .question-tr, .text-tr, .sub-question-label-tr').hide();
            else
                $tbody.children('tr').not('.space-tr, .question-tr, .text-tr').hide();
        } else if (type == 'message') {
            $tbody.children('tr.message-tr').show();
            if (has_sub_question)
            {
                $tbody.children('tr').not('.space-tr, .question-tr, .message-tr, .sub-question-label-tr').hide();
            }
            else
            {
                $tbody.children('tr').not('.space-tr, .question-tr, .message-tr').hide();
            }
        }

    });

    $('.has-sub-question').change(function() {
        var rowspan = $(this).parents('tbody').children('tr').first().children('td').first().attr('rowspan');
        if (rowspan)
            rowspan = parseInt(rowspan, 10);
        else
            rowspan = 1;

        if ($(this).prop('checked')) {
            $(this).parents('tbody').children('tr').first().children('td[rowspan]').attr('rowspan', (rowspan + 1));
            $(this).parents('tbody').children('.sub-question-label-tr').show();
        } else {
            $(this).parents('tbody').children('tr').first().children('td[rowspan]').attr('rowspan', (rowspan - 1));
            $(this).parents('tbody').children('.sub-question-label-tr').hide();
        }
    });

    $('.append-select-one-btn').click(function() {
        var question_id = $(this).data('question-id');
        var $li = $('#dummy-selection-one > li').clone(true);
        var i = $(this).parents('ul').children('li.list-selection').length;
        $li.find('.custom-control-input').attr('id', "selection-default-flag-"+i+"-"+question_id).val(i);
        $li.find('.custom-control-label').attr('for', "selection-default-flag-"+i+"-"+question_id);
        $li.find('input').each(function(n, input) {
            var name = $(input).attr('name').replace('new', question_id).replace('index', i);
            $(input).attr('name', name);
        });
        $li.find('._colorpicker').val(MC.default_colors.selections[i]).spectrum({
            preferredFormat: "hex",
            flat: false,
            showInput: true,
            color: MC.default_colors.selections[i]
        });
        $li.insertBefore($(this).parent('li'));
        if (i >= 9) {
            $(this).prop('disabled', true).hide();
        }
    });

    $('.remove-selection-btn').click(function() {
        var select_type = $(this).parents('tbody').find('input.select-type-input').val();
        var $ul = $(this).parents('ul');
        $(this).parents('li').remove();
        if (select_type == 'select_one')
            $ul.find('.append-select-one-btn').prop('disabled', false).show();
        else if (select_type == 'select_multi')
            $ul.find('.append-select-multi-btn').prop('disabled', false).show();
    });

    $('.append-select-multi-btn').click(function() {
        var question_id = $(this).data('question-id');
        var $li = $('#dummy-selection-multi > li').clone(true);
        var $tbody = $(this).parents('tbody');
        var i = $(this).parents('ul').children('li.list-selection').length;
        $li.find('.custom-control-input').attr('id', "selection-default-flag-"+i+"-"+question_id);
        $li.find('.custom-control-label').attr('for', "selection-default-flag-"+i+"-"+question_id);
        $li.find('input').each(function(n, input) {
            var name = $(input).attr('name').replace('new', question_id);
            $(input).attr('name', name);
        });
        $li.find('._colorpicker').val(MC.default_colors.selections[i]).spectrum({
            preferredFormat: "hex",
            flat: false,
            showInput: true,
            color: MC.default_colors.selections[i]
        });
        $li.insertBefore($(this).parent('li'));
        if (i >= 9) {
            $(this).prop('disabled', true).hide();
        }
    });

    var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';

    /* 順序変更 */
    $('.priority-up-btn').click(function(e) {
        e.preventDefault();
        var $tbody = $(this).parents('tbody');
        $tbody.prev('tbody').addClass('animated slideOutDown').one(animationEnd, function() {
            $(this).removeClass('animated slideOutDown');
        });
        $tbody.addClass('animated slideOutUp').one(animationEnd, function() {
            $(this).removeClass('animated slideOutUp');
            $tbody.insertBefore($tbody.prev('tbody'));
            MC.reset_order();
        });
    });

    $('.priority-down-btn').click(function(e) {
        e.preventDefault();
        var $tbody = $(this).parents('tbody');
        $tbody.next('tbody').addClass('animated slideOutUp').one(animationEnd, function() {
            $(this).removeClass('animated slideOutUp');
        });
        $tbody.addClass('animated slideOutDown').one(animationEnd, function() {
            $(this).removeClass('animated slideOutDown');
            $tbody.insertAfter($tbody.next('tbody'));
            MC.reset_order();
        });
    });

    /* 削除 */
    $('.remove-question').change(function() {
        var $tbody = $(this).parents('tbody');
        var new_num = $tbody.data('new-num');
        if ( new_num) {
            $tbody.addClass('animated fadeOut').one(animationEnd, function() {
                $(this).remove();
                MC.reset_order();
            });
        } else if ($(this).prop('checked')) {
            $tbody.addClass('remove');
        } else {
            $tbody.removeClass('remove');
        }
    });

    /* 質問事項追加 */
    $('#append-question-btn').click(function() {
        $('#questions-table > tbody:last .priority-down-btn').show();
        $tbody = $('#dummy-questions-table > tbody').clone(true);
        $tbody.insertBefore($('#questions-table > tfoot'));
        MC.reset_order();
        $tbody.data('new-num', MC.new_num);
        $tbody.find('.select-gype').attr('id', 'select-type-new'+MC.new_num);
        $tbody.find('.has-sub-question').attr('id', 'has-sub-question-new'+MC.new_num).next('label').attr('for', 'has-sub-question-new'+MC.new_num);
        $tbody.find('.remove-question').attr('id', 'remove-new'+MC.new_num).next('label').attr('for', 'remove-new'+MC.new_num);
        $tbody.find('.default-flag-radio > input').attr('id', 'selection-default-flag-0-new-'+MC.new_num);
        $tbody.find('.default-flag-radio > label').attr('for', 'selection-default-flag-0-new-'+MC.new_num);
        var l = $('#questions-table > tbody').length;
        $tbody.find('.append-select-one-btn').data('question-id', 'new-'+MC.new_num);
        $tbody.find('input[name=default_flag\\[NEW\\]]').attr('name',
            $tbody.find('input[name=default_flag\\[NEW\\]]').attr('name').replace('NEW', 'new-'+MC.new_num));
        $tbody.find('.nps-flag').attr('id', 'nps-flag-new'+MC.new_num).val('new-'+MC.new_num).next('label').attr('for', 'nps-flag-new'+MC.new_num);
        $tbody.find('.nps-correlation').attr('id', 'nps-correlation-new'+MC.new_num).next('label').attr('for', 'nps-correlation-new'+MC.new_num);
        $tbody.find('._colorpicker').val(MC.default_colors.selections[0]).spectrum({
            preferredFormat: "hex",
            flat: false,
            showInput: true,
            color: MC.default_colors.selections[0]
        });
        MC.new_num++;
    });

    /* Submit */
    $('#questionnaire-form').on('submit', function(e) {
        //e.preventDefault();
        $('#questions-table > tbody').each(function(i, tbody) {
            var new_num = $(tbody).data('new-num');
            if (new_num) {
                $(tbody).find('input[name^=questions\\[new\\]], textarea[name]').each(function(n, input) {
                    if ($(input).attr('type') != 'radio') {
                        var name = $(input).attr('name').replace('new', 'new-'+new_num);
                        $(input).attr('name', name);
                        console.log(name);
                        console.log($(input));
                    }
                });
            console.log($(tbody).find('input[name^=default_flag]'));
            }
                
        });
        return true;
    });

    $('.nps-flag').change(function() {
        if ($(this).prop('checked')) {
            $(this).parent('div').next('div').children('.nps-correlation').prop('checked', false).trigger('change');
        }
    });

    $('.nps-correlation').change(function() {
        if ($(this).prop('checked')) {
            $(this).parent('div').prev('div').children('.nps-flag').prop('checked', false);
            $(this).parents('tbody').find('.min-level').hide();
            $(this).parents('tbody').find('.max-level').hide();
            $(this).parents('tbody').find('.item-name').prop('required', true).show();
        } else {
            $(this).parents('tbody').find('.item-name').prop('required', false).hide();
            $(this).parents('tbody').find('.min-level').show();
            $(this).parents('tbody').find('.max-level').show();
        }
    });

    MC.reset_order();
});

