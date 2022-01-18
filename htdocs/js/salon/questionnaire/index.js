
$(function() {

    var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';

    $('.clipboard').click(function(e) {
        e.preventDefault();
        window.getSelection().removeAllRanges();
        var $input = $(this).parents('.card-body.questionnaire').find('.questionnaire-url');
        if (navigator.userAgent.match(/ipad|ipod|iphone/i)) {
            $input.attr('contentEditable', true);
        }
        $input.prop('readonly', false);
        $input.addClass('animated pulse').one(animationEnd, function() {
            $(this).removeClass('animated pulse');
        });

        $(this).addClass('animated bounceIn').one(animationEnd, function() {
            $(this).removeClass('animated bounceIn');
        });

        /*
        $input.focus();
        $input.select();
        */
        var range = document.createRange();
        range.selectNode($input[0]);
        window.getSelection().addRange(range);
        document.execCommand("copy");
        if (navigator.userAgent.match(/ipad|ipod|iphone/i)) {
            $input.attr('contentEditable', false);
        }
        var selection = window.getSelection();
        selection.removeAllRanges();
        $input.blur();
        $input.prop('readonly', true);
        var $btn = $(this);
        setTimeout(function() {
            $btn.tooltip('dispose');
        }, 2000);
    });

    var $input = $('.card-body.questionnaire').find('.questionnaire-url');
    $input.focus(function() {
        $(this).select();
    });

    $('.printout-btn').click(function(e) {
        e.preventDefault();
        $(this).parents('.card-body.questionnaire').children('.print-area').print();
    });

});

