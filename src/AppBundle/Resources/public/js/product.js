/** * List add to cart buttons */var ProductCartButton = (function (superClass) {    extend(ProductCartButton, superClass);    function ProductCartButton() {        var self = this;        ProductCartButton.__super__.constructor.apply(this, arguments);        $(document).on('change', '.js_cart_size', function (e) {            self.changeFormAction($(this));            e.preventDefault();        });        $('.js_preorder_btn').hide();    }    ProductCartButton.prototype.successSubmit = function ($item, data) {        ProductCartButton.__super__.successSubmit.call(this, $item, data);        $item.find('.select').select2('data', null);        $item.find('.js_product_price').hide();        $item.find('.js_product_price[data-model-id]').show();    };    ProductCartButton.prototype.submitForm = function ($form) {        if(this.canAddIncart($form)) {            var validFlag = true;            // validate form            $form.find('select.select').each(function () {                var $this = $(this);                if (!$this.val()) {                    $('#s2id_' + $this.attr('id')).addClass('error');                    validFlag = false;                }            });            if (validFlag) {                ProductCartButton.__super__.submitForm.call(this, $form);            }        }    };    ProductCartButton.prototype.changeFormAction = function ($select, data) {        ProductCartButton.__super__.changeFormAction.call(this, $select, data);        var $selectedOption = $select.find('option:selected'),            $form = $('.js_fast_buy form');        // Change fast buy form action        $form.attr('action', Routing.generate('cart_quick_order_single_product', {            id: $selectedOption.data('id')        }));                // Change quick buy form preorder flag        $form.data('preorder', $select.closest('.js_item').data('preorder'));    };    ProductCartButton.prototype.errorSubmit = function ($item, data) {        $.each(data.responseJSON.errors, function (field, messages) {            $item.find('select.select').each(function () {                var $this = $(this);                if ($this.attr('name').match(new RegExp(field))) {                    $('#s2id_' + $this.attr('id')).addClass('error');                }            });        });    };    return ProductCartButton;})(CartButtons);$(window).ready(function () {    new ProductCartButton();});