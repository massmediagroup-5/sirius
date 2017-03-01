/**
 * Dialog filters select control
 */
var SharesProducts = (function (superClass) {
    extend(ShareProductsList, superClass);

    function ShareProductsList($holder) {
        ShareProductsList.__super__.constructor.call(this, $holder);

        this.loadContent();
    }

    ShareProductsList.prototype.loadContent = function (data) {
        this.lastParams = {};
        ShareProductsList.__super__.loadContent.call(this, data);

        this.request('share_products', this.lastParams, {sizes_group_id: this.sizesGroupId}, this.contentLoaded.bind(this));
    };

    ShareProductsList.prototype.contentLoaded = function (response) {
        this.$holder.find('a').on('click', this.contentLinkClick.bind(this));

        this.$holder.append(response.content);

        this.$holder.find('.js_model_row').on('click', this.modelRowClick.bind(this));

        this.hideLoading();
    };

    ShareProductsList.prototype.modelRowClick = function (e) {
        let $this = $(e.target).closest('.js_model_row'),
            $sizes = $this.parent().find('.js_size_row[data-model-id=' + $this.data('model-id') + ']');
        if ($this.hasClass('active')) {
            $this.removeClass('active');
            $sizes.hide();
        } else {
            $this.addClass('active');
            $sizes.show();
        }
    };

    return ShareProductsList;

})(LoadableContent);

$(document).ready(function () {
    new SharesProducts($('#shareProducts'));
});
