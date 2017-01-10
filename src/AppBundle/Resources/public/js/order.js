$(document).on('order.cart_size.changed', function (e, data) {
    $('.js_cart_total_count').text(data.totalCount);
    $('.js_cart_total_discounted_price').text(data.discountedTotalPrice);

    $('#cartContent').html(data.cartContentPartial);

    $(document).trigger('order.cart_content_inserted');
});

/**
 * Form to change cart item count
 */
var OrderItemCountAutoForm = (function (superClass) {
    extend(OrderItemCountAutoForm, superClass);

    function OrderItemCountAutoForm($form) {
        OrderItemCountAutoForm.__super__.constructor.call(this, $form);

        this.oldValue = this.currentQuantity();
    }

    OrderItemCountAutoForm.prototype.successSubmit = function (data) {
        $(document).trigger('order.cart_size.changed', [data]);
    };

    OrderItemCountAutoForm.prototype.submitForm = function () {
        // Set quantity to increment
        this.$form.find('input[id$=_quantity]').val(this.currentQuantity() - this.oldValue);
        this.oldValue = this.currentQuantity();

        OrderItemCountAutoForm.__super__.submitForm.call(this);
    };

    OrderItemCountAutoForm.prototype.currentQuantity = function () {
        return this.$form.find('.js-quantity').val();
    };

    return OrderItemCountAutoForm;
})(AjaxAutoForm);

/**
 * Form to change cart item size
 */
var OrderItemSizeAutoForm = (function (superClass) {
    extend(OrderItemSizeAutoForm, superClass);

    function OrderItemSizeAutoForm($form) {
        OrderItemSizeAutoForm.__super__.constructor.call(this, $form);
        this.$size = this.$form.find('.js_item_size_id');
        this.$size.data('current', this.$size.val());
        this.setFormId();
    }

    OrderItemSizeAutoForm.prototype.submitForm = function () {
        if(!userIsAuthenticated && this.$size.find('option:selected').data('preorderflag')) {
            $.fancybox.open({href: '#preorder-login', type: 'inline'});
            this.$size.select2('val', this.$size.data('current'));
            return false;
        }

        OrderItemSizeAutoForm.__super__.submitForm.call(this);
    };

    OrderItemSizeAutoForm.prototype.successSubmit = function (data) {
        var currentItemSizeId = this.$form.find('select.js_item_size_id').val(),
            $lastOption = this.$size.find('option[value="' + this.$size.data('current') + '"]');
        this.$form.closest('.js_cart_item').find('.js_item_price').attr('data-id', currentItemSizeId);
        this.$form.closest('.js_cart_item').find('.js_item_size_id').val(currentItemSizeId);
        this.$form.closest('.js_cart_item').find('.js_item_old_size_id').val(currentItemSizeId);
        this.setFormId();

        // If model size type changed
        if(this.$size.find('option:selected').data('preorderflag') == $lastOption.data('preorderflag')) {
            $(document).trigger('order.cart_size.changed', [data]);
            this.$size.data('current', this.$size.val());
        } else {
            window.location.reload();
        }
    };

    OrderItemSizeAutoForm.prototype.setFormId = function (id) {
        var currentItemSizeId = this.$form.find('select.js_item_size_id').val();
        this.$form.data('id', currentItemSizeId);
        this.$form.attr('action', Routing.generate('cart_change_size', {id: currentItemSizeId}));
    };

    return OrderItemSizeAutoForm;
})(AjaxAutoForm);

/**
 * Form to change cart item size
 */
var OrderItemRemoveAutoForm = (function (superClass) {
    extend(OrderItemRemoveAutoForm, superClass);

    function OrderItemRemoveAutoForm($form) {
        OrderItemRemoveAutoForm.__super__.constructor.call(this, $form);
    }

    OrderItemRemoveAutoForm.prototype.successSubmit = function (data) {
        $(document).trigger('order.cart_size.changed', [data]);
    };

    return OrderItemRemoveAutoForm;
})(AjaxAutoForm);

$(document).on('ready order.cart_content_inserted', function () {
    $('.js_auto_ajax_form[name="change_product_size_quantity"]').each(function () {
        new OrderItemCountAutoForm($(this));
    });
    $('.js_auto_ajax_form[name="change_product_size"]').each(function () {
        new OrderItemSizeAutoForm($(this));
    });
    $('.js_auto_ajax_remove_form').each(function () {
        new OrderItemRemoveAutoForm($(this));
    });

    $(document).on('order.cart_item_removed', function () {
        var $preOrderListHolder = $('.js_preorder_cart_list'),
            $mainListHolder = $('.js_main_cart_list');

        [$preOrderListHolder, $mainListHolder].forEach(function ($holder) {
            if ($holder.find('.js_cart_item').length == 0) {
                $holder.remove();
            }
        });
    });

});

$(document).on('order.cart_content_inserted', function () {
    var $content = $('#cartContent');
    initSelects($content);
    initAmountSelects($content);
});
