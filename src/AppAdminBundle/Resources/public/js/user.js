$(document).ready(function () {
    $('.user-orders .order-num').on('click', function (e) {
        e.preventDefault();

        //$('.my-orders__i > .item.active .item-drop').slideUp();
        //$('.my-orders__i > .item.active').removeClass('active');
        if ($(this).closest('.item').hasClass('active')) {
            $(this).closest('.item').removeClass('active');
            $(this).closest('.item').find('.item-drop').slideUp();
        } else {
            $(this).closest('.item').addClass('active');
            $(this).closest('.item').find('.item-drop').slideDown(function () {
                $('.user-orders .active .item-drop .col').css('height', 'auto');
                equalHeight($('.user-orders  .active .item-drop .col'), 2);
            });
        }
    });
});
