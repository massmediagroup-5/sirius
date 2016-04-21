(function ($) {
    $(document).ready(function () {
        $('input[id$=_individualDiscount]').on('keyup change', function () {
            var $discountedPrice = $('#individualDiscountedTotalPrice'),
                $price = $('#discountedTotalPrice'),
                price = $price.text() - $(this).val();
            price = parseFloat(price);
            if(isNaN(price)) {
                price = $price.text();
            }
            $discountedPrice.text(price)
        });
    });
})(jQuery);
