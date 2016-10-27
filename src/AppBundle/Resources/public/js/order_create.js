/** * List add to cart buttons */var CompleteOrderAjaxForm = (function(superClass) {    extend(CompleteOrderAjaxForm, superClass);    function CompleteOrderAjaxForm() {        this.$bonusesInput = $('.js_user_bonuses_count');        this.$bonusesInput.on('change keyup', this.bonusesChanged.bind(this));        return CompleteOrderAjaxForm.__super__.constructor.apply(this, arguments);    }    CompleteOrderAjaxForm.prototype.bonusesChanged = function (responce) {        this.$bonusesInput.val() ? this.$form.find('input[id$=bonuses]').val(this.$bonusesInput.val())           : this.$form.find('input[id$=bonuses]').val(0);    };    return CompleteOrderAjaxForm;})(AjaxForm);var OrderPricesControl = (function() {    function OrderPricesControl() {        this.$bonusesInput = $('.js_user_bonuses_count');        this.$totalPrice = $('.js_total_discounted_price');        this.$bonusesInput.on('change keyup', this.bonusesChanged.bind(this));        this.startPrice = parseInt(this.$totalPrice.text());    }    OrderPricesControl.prototype.bonusesChanged = function (e) {        var bonuses = parseInt(this.$bonusesInput.val()) || 0;        if (bonuses > this.$bonusesInput.attr('max') || bonuses < this.$bonusesInput.attr('min')) {            this.$bonusesInput.val(0).trigger('change');        } else {            this.$totalPrice.text(this.startPrice - bonuses);        }    };    return OrderPricesControl;})();$(window).ready(function () {    $(document).on('change', '#create_order_np_delivery_city', function () {        var city_id = $(this).val();        var next_select = $(this).parent().next().find('select.select');        $.ajax({            url: Routing.generate('get_stores_by_city'),            data: {                city_id: city_id,            },            dataType: "json",            type: "POST",            success: function (data) {                next_select.select2('data', null);                $.each(data.stores, function (key, value) {                    next_select.append(new Option(value.name, value.id));                });                return false;            },            error: function (message) {                console.log(message.status + ' ' + message.statusText);            }        });    });    new CompleteOrderAjaxForm($('.new-buyer-form'));    new CompleteOrderAjaxForm($('.js_quick_order'));    new OrderPricesControl();});$(window).on('load', function () {    if (orderFormSubmitFlag) {        $('#order_new_user a').click();    }});