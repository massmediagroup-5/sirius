(function ($) {
    $(document).ready(function () {
        $('input[id$=_individualDiscount], input[id$=_additionalSolar]').on('keyup change', function () {
            var $discountedPrice = $('#individualDiscountedTotalPrice'),
                $price = $('#discountedTotalPrice'),
                price = $price.text() - parseFloat($('input[id$=_individualDiscount]').val())
                    + parseFloat($('input[id$=_additionalSolar]').val());
            price = parseFloat(price);
            if (isNaN(price)) {
                price = $price.text();
            }
            $discountedPrice.text(price)
        });
    });
})(jQuery);
