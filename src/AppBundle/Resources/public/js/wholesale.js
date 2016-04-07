var updatePageData = function (data) {    $('.js_cart_total_count').text(data.totalCount);    $('.js_cart_total_price').text(data.totalPrice);    $('.js_cart_total_discounted_price').text(data.discountedTotalPrice);    $('.js_cart_pre_order_total_price').text(data.preOrderItemsPrice);    $('.js_cart_standard_total_price').text(data.standardItemsPrice);    $('.js_cart_single_items_count').text(data.singleItemsCount);    $('.js_cart_packages_count').text(data.packagesCount);    $.each(data.cartItems, function (productId, product) {        $.each(product.sizes, function (sizeId, sizeAmount) {            $('.js_cart_item_size_count[data-id="' + product.id + '"][data-size_id="' + sizeId + '"]').text(sizeAmount);        });        $('.js_cart_item_packages_count[data-id="' + product.id + '"]').text(product.packagesAmount);    });    if(data.totalCount == 0) {        $('.js_cart').addClass('is-empty');    } else {        $('.js_cart').removeClass('is-empty');    }};