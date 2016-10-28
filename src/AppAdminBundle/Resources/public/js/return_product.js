/**
 * Order sizes partial control
 */
var ReturnSizes = (function () {
    function ReturnSizes($partial) {
        this.$partial = $partial;

        this.init();
    }

    /**
     * Initialize sizes partial
     */
    ReturnSizes.prototype.init = function () {
        $('.js-return-count').on('change', this.recalculatePrice);
        this.recalculatePrice();
    };

    /**
     * Initialize sizes partial
     */
    ReturnSizes.prototype.recalculatePrice = function () {
        var $returnedPrice = $('#individualReturnedTotalPrice'),
            price = 0;

        $('.js-row-size').each(function () {
            price += $(this).find('.js-return-count').val() *
            $(this).find('.js-disc-price').text();
        });
        var reteurnedPrice = price - $('#bonuses').text() - $('#individualDiscount').text();
        $returnedPrice.text(reteurnedPrice > 0 ? reteurnedPrice : 0);
    };

    return ReturnSizes;
})();

$(document).ready(function () {
    var $sizes = $('#orderSizes');

    new ReturnSizes($sizes);
});
