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
        if (responce.history) {
            $('#orderHistoryItems').replaceWith($(responce.history));
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
        this.$size.find('.js_size_remove').on('click', this.removeSize.bind(this));
    };

    OrderSize.prototype.moveSize = function () {
        var quantity = parseInt(this.$size.find('[name="move_size"]').val());
        if (quantity) {
            this.request('move_size', {size: this.$size.data('size-id'), quantity: quantity});
        }
    };

    OrderSize.prototype.removeSize = function (e) {
        e.preventDefault();
        this.request('remove_size', {size: this.$size.data('size-id')});
    };

    mix(OrderSize, requestMixin);

    return OrderSize;
})();

/**
 * Order size control
 */
var OrderSizesDialog = (function () {
    function OrderSizesDialog() {
        this.selectedCallback = null;
        this.$selectSizeDialog = $('#order-sizes-select').dialog({
            autoOpen: false,
            height: 640,
            width: 1080,
            modal: true,
            dialogClass: 'ui-dialog-material',
            buttons: {
                "Выбор": this.onSelected.bind(this),
                "Отмена": function () {
                    this.$selectSizeDialog.dialog("close");
                }.bind(this)
            }
        });
        this.lastParams = {};
    }

    OrderSizesDialog.prototype.openAddSizeDialog = function (selectedCallback) {
        this.selectedCallback = selectedCallback;

        this.$selectSizeDialog.dialog("open");
        this.loadContent();
    };

    OrderSizesDialog.prototype.loadContent = function (data) {
        this.showLoading();
        if (!data) {
            data = {};
        }

        $.extend(this.lastParams, data);

        this.request('get_sizes', this.lastParams, this.contentLoaded.bind(this));
    };

    OrderSizesDialog.prototype.onSelected = function () {
        // Get selected sizes from modal window
        var sizes = [];
        this.$selectSizeDialog.find('.js_size_count').each(function () {
            var $this = $(this),
                count = parseInt($this.val());
            if (count > 0) {
                sizes.push({id: $this.data('size-id'), count: count});
            }
        });

        this.selectedCallback(sizes);
        this.$selectSizeDialog.dialog("close");
    };

    OrderSizesDialog.prototype.showLoading = function () {
        this.$selectSizeDialog.addClass('loading')
    };

    OrderSizesDialog.prototype.hideLoading = function () {
        this.$selectSizeDialog.removeClass('loading')
    };

    OrderSizesDialog.prototype.contentLoaded = function (response) {
        var $content = this.$selectSizeDialog.find('.js_order_sizes_ajax');
        $content.html(response.sizes);
        $content.find('a').on('click', this.contentLinkClick.bind(this));
        $content.find('.js_model_row').on('click', this.modelRowClick.bind(this));
        $content.find('.js_submit_filters').on('click', this.submitFilters.bind(this));

        this.hideLoading();
    };

    OrderSizesDialog.prototype.contentLinkClick = function (e) {
        var $this = $(e.target);
        e.preventDefault();

        this.loadContent($this.data('params'));
    };

    OrderSizesDialog.prototype.submitFilters = function (e) {
        var params = {};
        e.preventDefault();

        this.$selectSizeDialog.find('.js_filter').each(function () {
            var $this = $(this);
            params[$this.attr('name')] = $this.val();
        });

        this.loadContent(params);
    };

    OrderSizesDialog.prototype.modelRowClick = function (e) {
        var $this = $(e.target).closest('.js_model_row'),
            $sizes = $this.parent().find('.js_size_row[data-model-id=' + $this.data('model-id') + ']');
        if ($this.hasClass('active')) {
            $this.removeClass('active');
            $sizes.hide();
        } else {
            $this.addClass('active');
            $sizes.show();
        }
    };

    mix(OrderSizesDialog, requestMixin);

    return OrderSizesDialog;
})();

/**
 * Order sizes partial control
 */
var OrderSizes = (function () {
    function OrderSizes($partial) {
        this.$partial = $partial;
        this.sizes = [];

        $(document).on('sizes.new_partial', this.reInit.bind(this));

        this.selectSizeDialog = new OrderSizesDialog;

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

        this.$partial.on(
            'click',
            '.js_order_add_size',
            // Method addNewSizes will run when sizes selected
            this.selectSizeDialog.openAddSizeDialog.bind(this.selectSizeDialog, this.addNewSizes.bind(this))
        );
    };

    /**
     * Initialize sizes partial
     */
    OrderSizes.prototype.updateOrderField = function (e) {
        var $input = $(e.target), data = {};

        data[$input.attr('name')] = $input.val();

        this.request.call(this, 'ajax_update', data);
    };

    /**
     * Initialize sizes partial
     */
    OrderSizes.prototype.addNewSizes = function (sizes) {
        this.request('add_sizes', {sizes: sizes});
    };

    /**
     * Reinitialize sizes partial
     */
    OrderSizes.prototype.reInit = function (e, partial) {
        var $partial = $(partial);
        this.$partial.replaceWith($partial);
        this.$partial = $partial;
        this.sizes = [];
        this.init();
    };

    mix(OrderSizes, requestMixin);

    return OrderSizes;
})();

$(document).ready(function () {
    var $sizes = $('#orderSizes');

    new OrderSizes($sizes);

});
