function mix(object) {
    var mixins = Array.prototype.slice.call(arguments, 1);
    for (var i = 0; i < mixins.length; ++i) {
        for (var prop in mixins[i]) {
            if (typeof object.prototype[prop] === "undefined") {
                object.prototype[prop] = mixins[i][prop];
            }
        }
    }
}

var requestMixin = {
    request: function (route, data, success) {
        var params = {};
        if (typeof success == 'undefined') {
            // default success
            params.success = this.successSubmit.bind(this);
        } else {
            if (success) {
                params.success = success;
            }
        }
        console.log(this);
        $.ajax($.extend({
            url: Routing.generate(baseRouteName + '_' + route, {id: orderId}),
            data: data,
            dataType: "json",
            type: "POST",
            error: this.errorSubmit.bind(this)
        }, params));
    },
    errorSubmit: function (responce) {
        alert(responce.status + ' ' + responce.statusText);
    },
    successSubmit: function (responce) {
        if (responce.partial) {
            $(document).trigger('sizes.new_partial', [responce.partial]);
        }
    }
};

/**
 * Order size control
 */
var OrderSize = (function () {
    function OrderSize($size) {
        this.$size = $size;
        this.init();
    }

    OrderSize.prototype.init = function () {
        this.$size.find('.js_submit_move_size').on('click', this.moveSize.bind(this));
    };

    OrderSize.prototype.moveSize = function () {
        var quantity = parseInt(this.$size.find('[name="move_size"]').val());
        if (quantity) {
            this.request('move_size', {size: this.$size.data('size-id'), quantity: quantity});
        }
    };

    mix(OrderSize, requestMixin);

    return OrderSize;
})();

/**
 * Order sizes partial control
 */
var OrderSizes = (function () {
    function OrderSizes($partial) {
        this.$partial = $partial;
        this.sizes = [];
        this.init();
    }

    /**
     * Initialize sizes partial
     */
    OrderSizes.prototype.init = function () {
        var self = this;
        $('input[name=individualDiscount], input[name=additionalSolar]').on('keyup change', function () {
            var $discountedPrice = $('#individualDiscountedTotalPrice'),
                $price = $('#discountedTotalPrice'),
                price = $price.text() - parseFloat($('input[name=individualDiscount]').val())
                    + parseFloat($('input[name=additionalSolar]').val());
            price = parseFloat(price);
            if (isNaN(price)) {
                price = $price.text();
            }
            $discountedPrice.text(price)
        });

        this.$partial.find('[data-size-id]').each(function () {
            self.sizes.push(new OrderSize($(this)));
        });

        this.$partial.find('.js_order_update').on('change', this.updateOrderField.bind(this));
    };

    /**
     * Initialize sizes partial
     */
    OrderSizes.prototype.updateOrderField = function (e) {
        var $input = $(e.target), data = {};

        data[$input.attr('name')] = $input.val();

        this.request.call(this, 'ajax_update', data);
    };

    mix(OrderSizes, requestMixin);

    return OrderSizes;
})();

$(document).ready(function () {
    var $sizes = $('#orderSizes'),
        orderSize = new OrderSizes($sizes);

    $(document).on('sizes.new_partial', function (e, partial) {
        $sizes.replaceWith($(partial));
        $sizes = $('#orderSizes');
        orderSize = new OrderSizes($sizes);
    });

});
