MC.datepick_option = {
    debug: true,
    format: 'YYYY/MM/DD',
    locale: 'ja',
    defaultDate: false,
    useCurrent: false,
    icons: {
        time: 'fa fa-clock',
        date: 'fa fa-calendar',
        up: 'fa fa-arrow-up',
        down: 'fa fa-arrow-down',
        previous: 'fa fa-chevron-left',
        next: 'fa fa-chevron-right',
        today: 'fa fa-calendar-check',
        clear: 'fa fa-backspace',
        close: 'fa fa-times'
    },
    buttons: {showToday: true, showClear: true, showClose: true},
    dayViewHeaderFormat: 'YYYY年 MMM'
};

MC.escape = function(text) {
    $('#escape-html').text('');
    if (text !== null) {
        return $('#escape-html').text(text).html();
    }
    return '';
};

/* ------------------------------------------------------------------------------------------------ */

MC.render_pagination = function(pagination, $target, click_event) {
    $target.empty();
    if (pagination && pagination !== null && typeof pagination.link !== 'undefined') {
        var $pagination_html = $('<div class="pagination-block"><div class="pagination pagination-links">'+pagination.link+'</div><div class="pagination-page-number"><span class="text-primary">'+pagination.numbers+'</span></div></div>');
        $pagination_html.find('a').click(function(e) {
            e.preventDefault();
            click_event($(this));
        });
        $target.append($pagination_html);
    }
};

/* ------------------------------------------------------------------------------------------------ */

MC.ajax_post = function(url, data, success_cb, failure_cb, always_cb) {
    console.log('MC.ajax_post('+url+') run.');

    return $.ajax({
        url: url,
        method: 'POST',
        data: data,
        dataType: 'json'
    }).done(function(data, textStatus, jqXHR) {
        if (data.status == 'OK') {
            success_cb(data);
        } else {
            failure_cb(data);
            if (data.errors.length > 0) {
                var auth_error = false;
                $.each(data.errors, function(i, error) {
                    if (error.error_code == 'AUTH') {
                        $('#login-modal').modal('show');
                        auth_error = true;
                        return false;
                    }
                });
                if (auth_error) {
                    return false;
                }
            }
        }
        return true;
    }).fail(function(jqXHR, textStatus, errorThrown) {
        /*alert('ネットワークエラー (通信環境をご確認ください。)');*/
        console.error('Network Error. [ '+url+' ]');
        failure_cb();
    }).always(function(data, textStatus, jqXHR) {
        always_cb(data);
    });

};

MC.ajax_get = function(url, success_cb, failure_cb, always_cb) {

    return $.ajax({
        url: url,
        method: 'GET',
        dataType: 'json'
    }).done(function(data, textStatus, jqXHR) {
        if (data.status == 'OK') {
            success_cb(data);
        } else {
            failure_cb(data);
            if (data.errors.length > 0) {
                var auth_error = false;
                $.each(data.errors, function(i, error) {
                    if (error.error_code == 'AUTH') {
                        $('#login-modal').modal('show');
                        auth_error = true;
                        return false;
                    }
                });
                if (auth_error) {
                    return false;
                }
            }
        }
        return true;
    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert('ネットワークエラー (通信環境をご確認ください。)');
        failure_cb();
    }).always(function(data, textStatus, jqXHR) {
        always_cb(data);
    });

};

/* ------------------------------------------------------------------------------------------------ */

MC.search_address = function(postcode, $address_group) {
    $address_group.find('input.postcode').removeClass('is-invalid');
    if (postcode.match(/^\d{3}-?\d{4}$/)) {
        $address_group.find('.btn > i').hide();
        $address_group.find('.btn').append('<span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>');
        var data = {zipcode: postcode};
        var url = 'https://zipcloud.ibsnet.co.jp/api/search';
        $.ajax({
            url: url,
            method: 'GET',
            data: data,
            dataType: 'jsonp'
        }).done(function(res, textStatus, jqXHR) {
            if (res.status === 200 && res.results) {
                var prefecture = res.results[0].prefcode;
                var address = res.results[0].address2 + res.results[0].address3;
                //console.log(prefecture);
                //console.log(address);
                $address_group.find('select.prefecture').val(prefecture).trigger('change');
                if ($address_group.find('input.address').length > 0) {
                    $address_group.find('input.address').val(address).focus().trigger('change');
                }
            }
        }).always(function(data, textStatus, jqXHR) {
            $address_group.find('.btn > span.spinner-grow').remove();
            $address_group.find('.btn > i').show();
        });
    } else {
        $address_group.find('input.postcode').addClass('is-invalid');
    }
};

/* ------------------------------------------------------------------------------------------------ */

MC.number_format = function(num_string) {
    return num_string.toString().replace(/(\d)(?=(\d\d\d)+$)/g , '$1,');
};

/* ------------------------------------------------------------------------------------------------ */

MC.byte_format = function(bytes) {
    if (typeof (bytes) === "string" || bytes instanceof String) {
        bytes = parseInt(bytes, 10);
    }
    if      (bytes>=1073741824) {bytes=(bytes/1073741824).toFixed(2)+' GB';}
    else if (bytes>=1048576)    {bytes=(bytes/1048576).toFixed(2)+' MB';}
    else if (bytes>=1024)       {bytes=(bytes/1024).toFixed(2)+' KB';}
    else if (bytes>1)           {bytes=bytes+' bytes';}
    else if (bytes==1)          {bytes=bytes+' byte';}
    else                        {bytes='0 byte';}
    return bytes;
};

/* ------------------------------------------------------------------------------------------------ */

MC.confirm = function(message, yes_callback, no_callback, keep_close) {
    var opened_modal;

    if ($('.modal:visible').length) {
        opened_modal = $('.modal:visible');
        opened_modal.modal('hide');
    }

    /* Confirm Modal */
    $('#my-confirm-modal #my-confirm-yes-btn').off('click');
    $('#my-confirm-modal #my-confirm-no-btn').off('click');
    $('#my-confirm-modal #my-confirm-yes-btn').on('click', function() {
        yes_callback();
        $('#my-confirm-modal').modal('hide');
        if ( ! keep_close && opened_modal) {
            opened_modal.modal('show');
        }
    });
    $('#my-confirm-modal #my-confirm-no-btn').on('click', function() {
        no_callback();
        $('#my-confirm-modal').modal('hide');
        if (opened_modal) {
            opened_modal.modal('show');
        }
    });
    $('#my-confirm-msg').html(message);
    $('#my-confirm-modal').modal('show');
};

/* ================================================================================================= */
/* メイン */
$(function() {

    MC.session_storage = sessionStorage;
    MC.local_storage   = localStorage;

    /* POP Over */
    $('[data-toggle=popover]').popover();

    /* TOOLTIP */
    if (MC.module !== 'webview') {
        $('[data-toggle=tooltip]').tooltip();
    }

    /* Back to TOP */
    $('#back-to-top').click(function() {
        $("html,body").animate({scrollTop:0},"300");
    });

    /* Checkbox Radio */
    $('input[type=checkbox].radio').change(function() {
        if ($(this).prop('checked')) {
            $btn_group = $(this).parents('.btn-group');
            $btn_group.find('input[type=checkbox].radio').not(this).prop('checked', false).parent('button').removeClass('active');
        }
    });

    /* 二重submit禁止 */
    $('form').submit(function() {
        $(this).submit(function(e) {
            e.preventDefault();
            return false;
        });
    });

    /* INPUT MAXLENGTH */
    $('input[maxlength], textarea[maxlength]').on('focus', function() {
        var maxlength = $(this).attr('maxlength');
        var len = $(this).val().length;
        var offset = $(this).offset();
        var width = $(this).outerWidth();
        var top = offset.top - 12;
        var left = offset.left;
        $('#maxlength-text > .length').text(len);
        $('#maxlength-text > .maxlength').text(maxlength);
        $('#maxlength-wrap').offset({top: top, left: left}).width(width).show();
    }).on('blur', function() {
        $('#maxlength-wrap').offset({top:0,left:0}).hide();
    });
    $('input[maxlength], textarea[maxlength]').on('keyup', function() {
        var len = $(this).val().length;
        $('#maxlength-text > .length').text(len);
    });

    /* クリアボタン */
    $('button[type=reset]').click(function(e) {
        e.preventDefault();
        var form = $(this).attr('form');
        $('#'+form)[0].reset();
        $('#'+form).find('input[type=text]').val('');
        $('#'+form).find('input[type=tel]').val('');
        $('#'+form).find('input[type=url]').val('');
        $('#'+form).find('input[type=email]').val('');
        $('#'+form).find('input[type=number]').val('');
        $('#'+form).find('input[type=date]').val('');
        $('#'+form).find('input[type=datetime]').val('');
        $('#'+form).find('input[type=month]').val('');
        //$('#'+form).find('input[type=hidden]').val('');
        $('#'+form).find('input[type=checkbox]').prop('checked', false);
        $('#'+form).find('select').each(function() {
            var val = $(this).children('option').val();
            $(this).val(val);
        });
        if ($('#'+form).hasClass('search-form')) {
            $('#'+form).find('input[type=text], input[type=tel], input[type=number], input[type=email], input[type=url], input[type=date], input[type=datetime], input[type=month], select').each(function() {
                   $(this).removeClass('active');
            });
        }
    });

    /* メッセージボックス */
    $('.alert > .hide-message').click(function() {
        $(this).parent('.alert').hide();
    });

    /* 検索FORM */
    $('.search-form input[type=text], .search-form input[type=tel], .search-form input[type=number], .search-form input[type=email], .search-form input[type=url], .search-form input[type=date], .search-form input[type=datetime], .search-form input[type=month]').keyup(function() {
       if ($(this).val().length > 0) {
           $(this).addClass('active');
       } else {
           $(this).removeClass('active');
       }
    });
    $('.search-form input[type=text], .search-form input[type=tel], .search-form input[type=number], .search-form input[type=email], .search-form input[type=url], .search-form input[type=date], .search-form input[type=datetime], .search-form input[type=month], .search-form select').change(function() {
       if ($(this).val().length > 0) {
           $(this).addClass('active');
       } else {
           $(this).removeClass('active');
       }
    });
    $('.search-form input[type=text], .search-form input[type=tel], .search-form input[type=number], .search-form input[type=email], .search-form input[type=url], .search-form input[type=date], .search-form input[type=datetime], .search-form input[type=month], .search-form select').each(function() {
       if ($(this).val().length > 0) {
           $(this).addClass('active');
       } else {
           $(this).removeClass('active');
       }
    });

    $('.collapse-btn').click(function(e) {
        e.preventDefault();
    });

    /* Color Picker */
    $('.colorpicker').each(function() {
        var default_color = $(this).data('default-color');
        var option = {
            preferredFormat: "hex",
            flat: false,
            showInput: true
        };
        if (default_color) {
            option.color = default_color;
        }
        $(this).spectrum(option);
    });

});

