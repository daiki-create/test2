$(function() {
    $('#my-page-name').on('click',function(){
        $('#mask').show();
        $('#name-modal').show();
    });
    $('#my-page-mail').on('click',function(){
        $('#mask').show();
        $('#mail-modal').show();
    });
    $('#mask').on('click',function(){
        $('#mask').css('display','none')
        $('#name-modal').hide();
        $('#mail-modal').hide();
    });
});
