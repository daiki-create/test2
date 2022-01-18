
MC.get_salons = function(url) {
    $('#salons').empty();
    MC.ajax_get(
        url,
        function(res) {
            var salons = res.data.salons;
            $.each(salons, function(i, salon) {
                console.log(salon);
                var $tr = $('#dummy-salon > tr').clone(true);
                $tr.find('.salon-name').text(salon.name);
                $tr.find('.salon-phone').text(salon.phone);
                $tr.find('.salon-address').text(salon.address);
                if ((MC.salon_id && MC.salon_id == salon.id) || (typeof MC.salon_id == 'undefined' && salon.id === '0')) {
                    $tr.find('.btn').prop('disabled', true);
                    $tr.addClass('active');
                } else {
                    $tr.find('.btn').data('salon-id', salon.id);
                }
                $('#salons').append($tr);
            });
            if (res.pagination) {
                MC.render_pagination(res.pagination, $('#salons-pagination'), function($a) {
                    MC.get_salons($a.attr('href'));
                });
            }
        },
        function(res) {
        },
        function() {
        }
    );
};

$(function() {
    $('#select-salon-modal').on('show.bs.modal', function(e) {
        if (e.relatedTarget) {
            var url = "/ajax/salon/get_salons/0";
            MC.get_salons(url);
        }
    });
});

