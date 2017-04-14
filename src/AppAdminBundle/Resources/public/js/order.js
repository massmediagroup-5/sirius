requestMixin.successSubmit = function (responce) {
    if (responce.partial) {
        $(document).trigger('sizes.new_partial', [responce.partial]);
    }
    if (responce.history) {
        $('#orderHistoryItems').replaceWith($(responce.history));
    }
    if (responce.message) {
        alert(responce.message);
    }
};

requestMixin.errorSubmit = function (responce) {
    if (responce.status === 422) {
        alert(responce.responseJSON.errors.map(function (error) {
            return error.property_path + ': ' + error.message
        }).join('\n'));
    } else {
        alert(responce.status + ' ' + responce.statusText);
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
        this.$size.find('.js_change_size_quantity').on('click', this.changeSizeQuantity.bind(this));
        this.$quantity = this.$size.find('[name="size_quantity"]');
    };

    OrderSize.prototype.moveSize = function () {
        var quantity = parseInt(this.$size.find('[name="move_size"]').val());
        if (quantity) {
            this.request('move_size', {size: this.$size.data('size-id'), quantity: quantity});
        }
    };

    OrderSize.prototype.changeSizeQuantity = function () {
        var quantity = parseInt(this.$quantity.val()),
            oldQuantity = parseInt(this.$quantity.data('old-quantity')),
            diff = quantity - oldQuantity;

        if (!isNaN(quantity)) {
            this.request('change_size_quantity', {size: this.$size.data('model-size-id'), quantity: diff});
            this.$quantity.data('old-quantity', quantity)
        }
    };

    OrderSize.prototype.removeSize = function (e) {
        e.preventDefault();
        this.request('remove_size', {size: this.$size.data('size-id'), quantity: this.$size.data('quantity')});
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

        this.request('get_sizes', this.lastParams, {}, this.contentLoaded.bind(this));
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
        $content.find('.js_size_count').on('change', function () {
            var $this = $(this);
            $this.val(Math.abs(parseInt($this.val())));
        });

        this.hideLoading();
    };

    OrderSizesDialog.prototype.contentLinkClick = function (e) {
        var $this = $(e.target),
            params = $this.data('params');
        e.preventDefault();

        if (!params) {
            params = $.url($this.attr('href')).param();
        }

        this.loadContent(params);
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
var CarriersChanger = (function () {
    function CarriersChanger() {
        this.$select = $('select.js-custom-carriers');
        this.$cities = $('select.js-cities');
        this.$stores = $('select.js-stores');
        this.isDirty = false;
        this.$select.on('change', this.init.bind(this));
        this.init();
    }

    /**
     * Initialize sizes partial
     */
    CarriersChanger.prototype.init = function () {
        if (this.$select.val() == '3'){
            $('.js-custom-delivery').closest('.form-group').show();
            this.$cities.closest('.form-group').hide();
            this.$stores.closest('.form-group').hide();
        } else {
            $('.js-custom-delivery').closest('.form-group').hide();
            this.$cities.closest('.form-group').show();
            this.$stores.closest('.form-group').show();

            if (this.isDirty) {
                this.updateCities();
            }
        }
        this.isDirty = true;
    };

    CarriersChanger.prototype.updateCities = function () {
        var self = this;
        $.ajax({
            type: 'GET',
            url: Routing.generate(baseRouteName + '_delivery_data', baseRouteParams),
            dataType: 'json',
            data: {carrier: $('select.js-custom-carriers').val()},
            success: function(response){
                self.$cities.empty();
                response.data.forEach(function (city) {
                    var $option = $('<option></option>')
                        .val(city.id)
                        .text(city.name);
                    self.$cities.append($option);
                });
                self.$cities.find("option:selected").prop("selected", false).trigger('change');
            }
        });
    };

    return CarriersChanger;
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

/**
 * Order size control
 */
var AjaxPagination = (function () {
    function AjaxPagination($content) {
        this.$content = $content;

        this.$content.on('click', 'a', this.linkClickHandler.bind(this))
    }

    AjaxPagination.prototype.linkClickHandler = function (e) {
        e.preventDefault();
        var params = $.url($(e.target).attr('href')).param();

        if (params.page) {
            this.request('get_other_orders', params, {}, this.contentLoaded.bind(this));
        }
    };

    AjaxPagination.prototype.contentLoaded = function (data) {
        this.$content.html(data.content);

        $(document).trigger('ajax.insert_content', [this.$content]);
    };

    mix(AjaxPagination, requestMixin);

    return AjaxPagination;
})();

$(document).ready(function () {
    var $sizes = $('#orderSizes');

    new OrderSizes($sizes);

    new AjaxPagination($('.js_pagination_content'));

    new CarriersChanger();
});
