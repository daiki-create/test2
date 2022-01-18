
$(function() {

    $('nav a.inner-link').click(function(e) {
        e.preventDefault();
        var speed = 800;
        var href= $(this).attr("href");
        var target = (href == "#" || href == "") ? 'html' : href;
        if (target)
        {
            var position = $(target).offset().top - $(".navbar").height();
            $("html, body").animate({scrollTop:position}, speed, "swing", function() {
                if ($('header .navbar-toggler').is(':visible')) {
                    $('header .navbar-toggler').trigger('click');
                }
            });
        }
        return false;
    });

});

$(".yamano-title").click(function () {
    if ($(".detail-content").is(":visible")) {
        $(".detail-content").slideUp();
        $(this).removeClass('open');
    } else {
        $(".detail-content").slideDown("fast");
        $(this).addClass('open');
    }
});
