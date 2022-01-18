
$(function() {

    var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';

    $('.answer-value').each(function() {
        $(this).children('input').slider();
    });


    $('.prev-btn').click(function() {
        $('.question-card.active').addClass('animated slideOutRight').one(animationEnd, function() {
            $(this).removeClass('active animated slideOutRight').hide();
            $(window).scrollTop(0);
            $(this).prev('.question-card').show().addClass('active animated slideInLeft').one(animationEnd, function() {
                $(this).removeClass('animated slideInLeft');
            });
        });
    });

    $('.next-btn').click(function() {
        $('.question-card.active').addClass('animated slideOutLeft').one(animationEnd, function() {
            $(this).removeClass('active animated slideOutLeft').hide();
            $(window).scrollTop(0);
            $(this).next('.question-card').show().addClass('active animated slideInRight').one(animationEnd, function() {
                $(this).removeClass('animated slideInRight');
            });
        });
    });

});

