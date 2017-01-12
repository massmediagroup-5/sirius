/** * Product model size item */var WholesaleProductItemSize = (function () {    /**     * @param $size     * @param {WholesaleProductItem} item     * @constructor     */    function WholesaleProductItemSize($size, item) {        this.$size = $size;        this.item = item;        this.$size.get('sizes');        this.$amountsChanger = this.$size.find('.js_size_amount_changer');        this.$price = this.$size.find('.js_item_size_price');        this.$amountsChanger.on('change', this.amountChanged.bind(this));        this.$size.on('click', '.js_add_size_to_cart', this.addToCart.bind(this))    }    /**     * Set size amount     *     * @param amount     */    WholesaleProductItemSize.prototype.setAmount = function (amount) {        this.$amountsChanger.val(Math.abs(parseInt(amount)));        this.updatePrice();    };    /**     * Get size amount     *     * @returns {Number}     */    WholesaleProductItemSize.prototype.getAmount = function () {        return Math.abs(parseInt(this.$amountsChanger.val()));    };    /**     * Add item to cart.     *     * @param e     */    WholesaleProductItemSize.prototype.addToCart = function (e) {        var products = {}, sizes = {};        if (this.getAmount() > 0) {            sizes[this.$size.data('id')] = this.getAmount();            products[this.item.$item.data('id')] = sizes;            $.ajax({                url: Routing.generate('cart_batch_increment'),                data: {products: products},                dataType: "json",                type: "POST",                success: this.successSubmit.bind(this),                error: function (message) {                    alert(message.status + ' ' + message.statusText);                }            });        }        e.preventDefault();    };    /**     * Success add to cart handler     *     * @param response     */    WholesaleProductItemSize.prototype.successSubmit = function (response) {        this.setAmount(0);        this.item.recalculateAmount();        updatePageData(response);    };    /**     * Do required on amount changing things     */    WholesaleProductItemSize.prototype.amountChanged = function () {        this.item.recalculateAmount();        this.updatePrice();    };    /**     * Update total model size price     */    WholesaleProductItemSize.prototype.updatePrice = function () {        var packagesAmount = this.getAmount(),            price = packagesAmount ? this.$price.data('price') * packagesAmount : this.$price.data('price');        this.$amountsChanger.val(packagesAmount);        this.$price.text(parseFloat(price).toFixed(2));    };    return WholesaleProductItemSize;})();/** * Product model item */var WholesaleProductItem = (function () {    /**     * @param $item     * @constructor     */    function WholesaleProductItem($item) {        var self = this;        this.$item = $item;        this.packagesCount = 0;        this.$amountsChanger = this.$item.find('.js_pack_amounts_changer');        this.$collectMessage = this.$item.find('.js_collect_message');        this.$price = this.$item.find('.js_item_price');        this.$item.on('click', '.js_add_pack_to_cart', this.addToCart.bind(this));        this.$amountsChanger.on('change', this.amountChanged.bind(this));        this.sizes = [];        this.$item.find('.js_wholesale_item_size').each(function () {            self.sizes.push(new WholesaleProductItemSize($(this), self));        });    }    /**     * Add product to cart     * @param e     */    WholesaleProductItem.prototype.addToCart = function (e) {        var products = {}, sizes = {};        if (this.getAmount() > 0) {            this.sizes.forEach(function (size) {                // put size with amount equal to selected package amount                sizes[size.$size.data('id')] = this.getAmount();            }.bind(this));            products[this.$item.data('id')] = sizes;            $.ajax({                url: Routing.generate('cart_batch_increment'),                data: {products: products},                dataType: "json",                type: "POST",                success: this.successSubmit.bind(this),                error: function (message) {                    alert(message.status + ' ' + message.statusText);                }            });        }        e.preventDefault();    };    /**     * Success add to cart handler     * @param response     */    WholesaleProductItem.prototype.successSubmit = function (response) {        this.sizes.forEach(function (size) {            // subtract packages amount from each size            size.setAmount(size.getAmount() - this.getAmount());        }.bind(this));        this.setAmount(0, false);        this.recalculateAmount();        updatePageData(response);    };    /**     * Set product model amount want to buy     * @param amount     * @param sync     */    WholesaleProductItem.prototype.setAmount = function (amount, sync) {        typeof sync != 'undefined' || (sync = true);        this.$amountsChanger.val(parseInt(amount));        this.updatePrice();        if (sync) {            this.amountChanged();        }    };    /**     * Get current selected amount     * @returns {Number}     */    WholesaleProductItem.prototype.getAmount = function () {        return Math.abs(parseInt(this.$amountsChanger.val()));    };    /**     * Do required on amount changing things     */    WholesaleProductItem.prototype.amountChanged = function () {        var packagesAmount = this.getAmount();        this.$amountsChanger.val(packagesAmount);        this.sizes.forEach(function (size) {            size.setAmount(packagesAmount);        });        this.updatePrice();    };    /**     * Update total model price     */    WholesaleProductItem.prototype.updatePrice = function () {        var packagesAmount = this.getAmount(),            price = packagesAmount ? this.$price.data('price') * packagesAmount : this.$price.data('price');        this.$price.text(parseFloat(price).toFixed(2));    };    /**     * Recalculate product model packages amount basing on model sizes amounts     */    WholesaleProductItem.prototype.recalculateAmount = function () {        var min = Math.min.apply(null, this.sizes.map(function (size) {            return size.getAmount();        }));        if(min > this.getAmount()) {            this.$collectMessage.addClass('active');        }        this.setAmount(min, false);    };    return WholesaleProductItem;})();/** * Wholesale page, list of models */var WholesalePage = (function () {    /**     * @constructor     */    function WholesalePage() {        var self = this;        this.items = [];        $('.js_wholesale_item').each(function () {            self.items.push(new WholesaleProductItem($(this)));        });        this.$amountsChanger = $('.js_amounts_changer');        this.$amountsChanger.on('change', this.changeAmounts.bind(this));    }    /**     * Change models amounts     */    WholesalePage.prototype.changeAmounts = function () {        this.items.map(function (item) {            item.setAmount(this.$amountsChanger.val());        }.bind(this));    };    return WholesalePage;})();$(window).ready(function () {    new WholesalePage();});