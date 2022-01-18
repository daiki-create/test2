
MC.clear_form = function() {
    $('#stylist-form input[type=text], #stylist-form input[type=email], #stylist-form input[type=tel], #stylist-form textarea').val('');
    $('#stylist-form input[type=checkbox]').prop('checked', false);
};

MC.get_stylist = function(stylist_id) {

    $('#stylist-update-form .stylist-name .badge').hide();
    $('#stylist-update-form input[name=stylist_id]').val('');
    $('#stylist-update-form input[name=kana]').val('').prop('readonly', true);
    $('#stylist-update-form input[name=name]').val('').prop('readonly', true);
    $('#stylist-update-form input[name=loginid]').val('').prop('readonly', true);
    $('#stylist-update-form input[name=phone1]').val('').prop('readonly', true);
    $('#stylist-update-form input[name=phone2]').val('').prop('readonly', true);
    $('#stylist-update-form input[name=phone3]').val('').prop('readonly', true);
    $('#stylist-update-form input[name=trial_limited_on]').val('').prop('readonly', true);
    $('#stylist-update-form textarea[name=note]').val('').prop('readonly', true);
    $('#update-stylist-btn').hide();
    $('#detail-stylist-btn').data('stylist-id', '').hide();
    $('#update-manager-flag').hide();
    $('#manager-flag-label').show();
    $('#edit-stylist-btn').show();
    $('#questionnaire-list-tbody').empty();
    $('#disable-stylist-btn').hide();
    $('#enable-stylist-btn').hide();
    $('#delete-stylist-btn').hide();
    $('#select-salon-btn').hide();
    $('#change-salon-form > input[name=stylist_id]').val('');
    $('#last-replied-date, #last-replied-hour').text('');

    MC.ajax_get(
        '/ajax/stylist/get_stylist/'+MC.salon_id+'/'+stylist_id+'/',
        function(response) {
            var stylist = response.data.stylist;
            //console.log(stylist);
            var questionnaires = response.data.questionnaires;
            var phone = ['', '', ''];
            if (stylist.phone) {
                phone = stylist.phone.split('-');
            }
            if (stylist.status === '1') {
                $('#change-salon-form > input[name=stylist_id]').val(stylist_id);
                $('#select-salon-btn').show();
                $('#stylist-update-form .stylist-name .badge-info').show();
                $('#disable-stylist-btn').data('stylist-id', stylist_id).show();
            } else if (stylist.agreement_flag === '1') {
                $('#stylist-update-form .stylist-name .badge-danger').show();
                $('#enable-stylist-btn').data('stylist-id', stylist_id).show();
                $('#delete-stylist-btn').data('stylist-id', stylist_id).show();
            } else {
                $('#stylist-update-form .stylist-name .badge-light').show();
            }
            if (stylist.manager_flag === '1') {
                $('#stylist-update-form .manager-flag-0').hide();
                $('#stylist-update-form .manager-flag-1').show();
                $('#update-manager-flag-check').prop('checked', true).trigger('change');
            } else {
                $('#stylist-update-form .manager-flag-1').hide();
                $('#stylist-update-form .manager-flag-0').show();
                $('#update-manager-flag-check').prop('checked', false).trigger('change');
            }
            $('#stylist-update-form input[name=stylist_id]').val(stylist.id);
            $('#stylist-update-form input[name=kana]').val(stylist.kana);
            $('#stylist-update-form input[name=name]').val(stylist.name);
            $('#stylist-update-form input[name=loginid]').val(stylist.loginid);
            $('#stylist-update-form input[name=phone1]').val(phone[0]);
            $('#stylist-update-form input[name=phone2]').val(phone[1]);
            $('#stylist-update-form input[name=phone3]').val(phone[2]);
            if (stylist.reply_count) {
                $('#reply-count').text(parseInt(stylist.reply_count, 10).toLocaleString('en-IN'));
            }
            if (stylist.last_replied_at) {
                $('#last-replied-date').text(moment(stylist.last_reply_at).format('YYYY/M/D'));
                $('#last-replied-hour').text(moment(stylist.last_reply_at).format('h:m'));
            }
            var trial_limited_on = '';
            if (stylist.trial_limited_on) {
                trial_limited_on = stylist.trial_limited_on;
            }
            $('#stylist-update-form input[name=trial_limited_on]').val(trial_limited_on);
            $('#stylist-update-form textarea[name=note]').val(stylist.note);
            $('#stylist-list').slideUp(400, function() {
                $('#stylist-detail').slideDown();
            });
            $('#detail-stylist-btn').data('stylist-id', stylist.id);
            $('#sendmail-stylist-btn').data('stylist-id', stylist.id);
            $.each(questionnaires, function(i, q) {
                var $tr = $('#questionnaire-dummy-tbody > tr').clone(true);
                $tr.find('.questionnaire-title').text(q.title);
                var $a = $('<a></a>', {
                    href: q.url,
                    text: q.url
                }).attr('target', '_blank');
                $tr.find('.questionnaire-url').append($a);
                $tr.find('.questionnaire-qr > img').attr('src', '/dl/image/qr_code/'+stylist.salon_id+'/'+q.code).attr('alt', q.url);
                $('#questionnaire-list-tbody').append($tr);
            });
        },
        function(response) {
        },
        function() {
        }
    );
};

MC.create_stylist = function() {
    $('#stylist-form-block .alert').hide();
    var post = $('#stylist-form').serialize();
    MC.ajax_post(
        '/ajax/stylist/create_stylist/'+MC.salon_id,
        post,
        function(response) {
            var stylist = response.data.stylist;
            var $tr = $('#stylist-dummy-tbody tr').clone(true);
            $tr.data('stylist-id', stylist.id);
            $tr.find('.stylist-name > a').text(stylist.name);
            $tr.find('.stylist-loginid > a').text(stylist.loginid);
            $tr.find('.stylist-name').attr('title', stylist.kana).tooltip();
            $tr.find('.stylist-phone > a').text(stylist.phone);
            $tr.find('.status-active').hide();
            $tr.find('.status-inactive').hide();
            $tr.find('.status-temporary').show();
            if (stylist.manager_flag === '1') {
                $tr.find('.manager-flag').append('<i class="far fa-circle fa-lg text-warning"></i>');
            }
            $('#stylist-list-tbody').prepend($tr);
            $('#stylist-form-btn').trigger('click');
        },
        function(response) {
            var error_msg = 'スタイリストの登録に失敗しました。';
            $('#stylist-form-block .alert').append($('<p class="mb-1"></p').text(error_msg));
            //console.log(response.errors);
            if (response.errors && $.isArray(response.errors)) {
                $('#stylist-form-block .alert').empty();
                $.each(response.errors, function(i, error) {
                    $.each(error.error_msgs, function(k, error_msg) {
                        $('#stylist-form-block .alert').append($('<p class="mb-1"></p').text(error_msg));
                    });
                });
            }
            $('#stylist-form-block .alert').show();
        },
        function() {
        }
    );
};

MC.update_stylist = function() {
    $('#stylist-detail .alert').hide();
    var stylist_id = $('#stylist-update-form input[name=stylist_id]').val();
    var manager_flag = false;
    if ($('#stylist-update-form input[name=manager_flag]').prop('checked')) {
        manager_flag = true;
    }
    var $tr = $('#stylist-list-tbody tr[data-stylist-id='+stylist_id+']');
    var post = $('#stylist-update-form').serialize();
    MC.ajax_post(
        '/ajax/stylist/update_stylist/'+MC.salon_id,
        post,
        function(response) {
            var stylist = response.data.stylist;
            $tr.find('.stylist-name > a').text(stylist.name);
            $tr.find('.stylist-loginid > a').text(stylist.loginid);
            $tr.find('.stylist-name').attr('title', stylist.kana).tooltip();
            $tr.find('.stylist-phone > a').text(stylist.phone);

            if (manager_flag) {
                $tr.find('.manager-flag').html('<i class="far fa-circle fa-lg text-warning"></i>');
            } else {
                $tr.find('.manager-flag').empty();
            }
            $('#detail-stylist-btn').trigger('click');
        },
        function(response) {
            var error_msg = 'スタイリストの登録に失敗しました。';
            $('#stylist-detail .alert').append($('<p class="mb-1"></p').text(error_msg));
            if (response.errors && $.isArray(response.errors)) {
                $('#stylist-detail .alert').empty();
                $.each(response.errors, function(i, error) {
                    $.each(error.error_msgs, function(k, error_msg) {
                        $('#stylist-detail .alert').append($('<p class="mb-1"></p').text(error_msg));
                    });
                });
            }
            $('#stylist-detail .alert').show();
        },
        function() {
            $('#update-stylist-btn').prop('disabled', false);
        }
    );
};

MC.init_stylist = function(stylist_id) {
    //console.log(stylist_id);
    MC.ajax_post(
        '/ajax/stylist/init_stylist/'+MC.salon_id,
        {stylist_id: stylist_id},
        function(response) {
            toastr.success('メールを送信しました。');
        },
        function(response) {
            toastr.error('失敗しました。');
        },
        function() {
            $('#sendmail-stylist-btn').prop('disabled', false);
        }
    );
};

MC.update_stylist_status = function(stylist_id, status, $btn) {
    MC.ajax_post(
        '/ajax/stylist/update_status/'+MC.salon_id,
        {stylist_id: stylist_id, status: status},
        function(response) {
            MC.get_stylist(stylist_id);
            toastr.success('スタイリストの状態を更新しました。');
        },
        function(response) {
            toastr.error('失敗しました。');
        },
        function() {
            $btn.prop('disabled', false);
        }
    );
};

MC.delete_stylist = function(stylist_id, $btn) {
    MC.ajax_post(
        '/ajax/stylist/delete_stylist/'+MC.salon_id,
        {stylist_id: stylist_id},
        function(response) {
            $('#stylist-list-tbody tr[data-stylist-id='+stylist_id+']').remove();
            $('#stylist-list-btn').trigger('click');
            $('#ajax-alert').text('スタイリストを削除しました。').addClass('alert-info').show();
            setTimeout(function() {
                $('#ajax-alert').text('').removeClass('alert-info').hide();
            }, 3000);
        },
        function(response) {
            $('#ajax-alert').text('スタイリストを削除できません。').addClass('alert-danger').show();
            setTimeout(function() {
                $('#ajax-alert').text('').removeClass('alert-danger').hide();
            }, 3000);
        },
        function() {
            $btn.prop('disabled', false);
        }
    );
};

$(function() {

    $('#stylist-form-block').on('shown.bs.collapse', function() {
        MC.clear_form();
        $('#stylist-form-btn').html('スタイリスト一覧').removeClass('btn-warning').addClass('btn-secondary');
    });
    $('#stylist-form-block').on('hidden.bs.collapse', function() {
        $('.alert').not('#ajax-alert').hide();
        $('#stylist-form-btn').html('スタイリスト登録').removeClass('btn-secondary').addClass('btn-warning');
    });

    $('#create-stylist-btn').click(function(e) {
        e.preventDefault();
        MC.create_stylist();
    });

    $('#stylist-list-table a').on('click', function(e) {
        e.preventDefault();
        var stylist_id = $(this).parents('tr').data('stylist-id');
        //console.log(stylist_id);
        window.location.hash = 'stylist-detail-'+stylist_id;
        MC.get_stylist(stylist_id);
    });

    $('#edit-stylist-btn').click(function() {
        $(this).hide();
        $('#sendmail-stylist-btn').hide();
        $('#update-stylist-btn').show();
        $('#detail-stylist-btn').show();
        $('#manager-flag-label').hide();
        $('#update-manager-flag').show();
        $('#stylist-update-form input[name=kana]').prop('readonly', false);
        $('#stylist-update-form input[name=name]').prop('readonly', false);
        $('#stylist-update-form input[name=loginid]').prop('readonly', false);
        $('#stylist-update-form input[name=phone1]').prop('readonly', false);
        $('#stylist-update-form input[name=phone2]').prop('readonly', false);
        $('#stylist-update-form input[name=phone3]').prop('readonly', false);
        $('#stylist-update-form input[name=trial_limited_on]').prop('readonly', false);
        $('#stylist-update-form textarea[name=note]').prop('readonly', false);
    });

    $('#detail-stylist-btn').click(function() {
        $('#stylist-detail .alert').hide();
        $('#update-manager-flag').hide();
        $('#manager-flag-label').show();
        $(this).hide();
        $('#sendmail-stylist-btn').show();
        var stylist_id = $(this).data('stylist-id');
        MC.get_stylist(stylist_id);
    });

    $('#stylist-list-btn').click(function() {
        $('.alert').not('#my-confirm-msg, #ajax-alert').hide();
        $('#stylist-detail').slideUp(400, function() {
            $('#stylist-list').slideDown();
            location.hash = '#stylists-tab';
        });
    });

    $('#update-stylist-btn').click(function() {
        $(this).prop('disabled', true);
        MC.update_stylist();
    });

    $('#sendmail-stylist-btn').click(function() {
        $(this).prop('disabled', true);
        var stylist_id = $(this).data('stylist-id');
        MC.init_stylist(stylist_id);
    });

    $('#salon-tab').on('show.bs.tab', function(e) {
        var target = $(e.target).attr('id');
        if (target === 'salon-detail-tab') {
            location.hash = '';
        } else {
            var hash = location.hash;
            var reg = /^#stylist-detail-(\d+)/;
            var res = reg.exec(hash);
            if (res && res.length > 1) {
                var stylist_id =res[1];
                if (stylist_id) {
                    $('#stylist-list-table tr[data-stylist-id='+stylist_id+'] > td.stylist-name > a').trigger('click');
                } else {
                    $('#stylist-list-btn').trigger('click');
                }
            } else {
                location.hash = '#'+target;
            }
        }
    });

    $(window).on('load hashchange', function() {
        var target = location.hash;
        if (target === '') {
            $('#salon-detail-tab').tab('show');
        } else {
            $('#stylists-tab').tab('show');
            if (target === '#stylists-tab') {
                $('#stylist-list-btn').trigger('click');
            }
        }
    });

    /* 停止ボタン */
    $('#disable-btn').click(function() {
        var $btn = $(this);
        $btn.prop('disabled', true);
        MC.confirm(
            '利用停止にします。<br>よろしいですか。',
            function() {
                $('#update-status-form').submit();
            },
            function() {
                $btn.prop('disabled', false);
            }
        );
    });

    /* 有効化ボタン */
    $('#enable-btn').click(function() {
        var $btn = $(this);
        $btn.prop('disabled', true);
        MC.confirm(
            '利用再開(有効)にします。<br>よろしいですか。',
            function() {
                $('#update-status-form').submit();
            },
            function() {
                $btn.prop('disabled', false);
            }
        );
    });

    /* 削除ボタン */
    $('#delete-btn').click(function() {
        var $btn = $(this);
        $btn.prop('disabled', true);
        MC.confirm(
            '削除します。<br>よろしいですか。<br>間違えて削除した場合は、開発者へお問い合わせください。',
            function() {
                $('#delete-form').submit();
            },
            function() {
                $btn.prop('disabled', false);
            }
        );
    });

    $('#disable-stylist-btn').click(function() {
        var $btn = $(this);
        $(this).prop('disabled', true);
        var stylist_id = $(this).data('stylist-id');
        MC.confirm(
            'スタイリストを利用停止にします。<br>よろしいですか。',
            function() {
                MC.update_stylist_status(stylist_id, '0', $btn);
            },
            function() {
                $btn.prop('disabled', false);
            }
        );
    });

    $('#enable-stylist-btn').click(function() {
        var $btn = $(this);
        $(this).prop('disabled', true);
        var stylist_id = $(this).data('stylist-id');
        MC.confirm(
            'スタイリストを有効にします。<br>よろしいですか。',
            function() {
                MC.update_stylist_status(stylist_id, '1', $btn);
            },
            function() {
                $btn.prop('disabled', false);
            }
        );
    });

    $('#delete-stylist-btn').click(function() {
        var $btn = $(this);
        $(this).prop('disabled', true);
        var stylist_id = $(this).data('stylist-id');
        MC.confirm(
            'スタイリストを削除します。<br>よろしいですか。',
            function() {
                MC.delete_stylist(stylist_id, $btn);
            },
            function() {
                $btn.prop('disabled', false);
            }
        );
    });

    $('#select-salon-modal .select-salon-btn').on('click', function() {
        var new_salon_id = $(this).data('salon-id');
        var salon_name = $.trim($(this).parents('tr').find('.salon-name').text());
        MC.confirm(
            $('#stylist-update-form .stylist-name input[name=name]').val()+'さんを<br>「'+salon_name+'」<br>に所属登録します。<br>よろしいですか。',
            function() {
                $('#select-salon-modal').modal('hide');
                $('#change-salon-form > input[name=new_salon_id]').val(new_salon_id);
                $('#change-salon-form').submit();
            },
            function() {
            }
        );
    });

});

