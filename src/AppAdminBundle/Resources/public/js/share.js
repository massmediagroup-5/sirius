var OrderSizesDialog = (function () {
    function OrderSizesDialog() {
        this.$selectSizeDialog = $('#share-sizes-select').dialog({
            autoOpen: false,
            height: 640,
            width: 1080,
            modal: true,
            dialogClass: 'ui-dialog-material'
        });
        this.lastParams = {};
        this.loading = 0;
        this.filtersSelect = new DialogFiltersSelect(this.$selectSizeDialog.find('#selectFilters'));
        this.sizesSelect = new DialogSizesSelect(this.$selectSizeDialog.find('#selectSizes'));
        this.conflictSizes = new DialogConflictSizesSelect(this.$selectSizeDialog.find('#selectConflictSizes'));
    }

    OrderSizesDialog.prototype.openAddSizeDialog = function (sizesGroupId) {
        this.$selectSizeDialog.dialog("open");
        $('a[href="#selectFilters"]').click();
        this.filtersSelect.setSizesGroupId(sizesGroupId);
        this.filtersSelect.loadContent();
        this.sizesSelect.setSizesGroupId(sizesGroupId);
        this.conflictSizes.setSizesGroupId(sizesGroupId);
    };

    mix(OrderSizesDialog, requestMixin);

    return OrderSizesDialog;
})();

/**
 * Abstract class LoadableContent
 */
var LoadableContent = (function () {
    function LoadableContent($holder) {
        this.$holder = $holder;
        this.lastParams = {};
        this.initLoading();
    }

    LoadableContent.prototype.loadContent = function (data) {
        this.showLoading();
        if (!data) {
            data = {};
        }
        $.extend(this.lastParams, data);
    };

    LoadableContent.prototype.showLoading = function () {
        this.$loading.css('display', 'table');
    };

    LoadableContent.prototype.hideLoading = function () {
        this.$loading.hide()
    };

    LoadableContent.prototype.initLoading = function () {
        this.$loading = $('<div class="loading-holder">\
                <div class="loading-cell">\
                    <div class="spinner">\
                        <div class="dot1"></div>\
                        <div class="dot2"></div>\
                    </div>\
                </div>\
            </div>');
        this.$holder.append(this.$loading);
        this.$loading.hide();
    };

    mix(LoadableContent, requestMixin);

    return LoadableContent;
})();

var modelsListMixin = {
    modelRowClick: function (e) {
        var $this = $(e.target).closest('.js_model_row'),
            $sizes = $this.parent().find('.js_size_row[data-model-id=' + $this.data('model-id') + ']');
        if ($this.hasClass('active')) {
            $this.removeClass('active');
            $sizes.hide();
        } else {
            $this.addClass('active');
            $sizes.show();
        }
    }
};

/**
 * Dialog filters select control
 */
var DialogFiltersSelect = (function (superClass) {
    extend(DialogFiltersSelect, superClass);

    function DialogFiltersSelect($holder) {
        var self = this;
        DialogFiltersSelect.__super__.constructor.call(this, $holder);
        $('a[href="#' + this.$holder.attr('id') + '"]').on('show.bs.tab', function () {
            self.loadContent();
        });
    }

    DialogFiltersSelect.prototype.setSizesGroupId = function (sizesGroupId) {
        this.sizesGroupId = sizesGroupId;
    };

    DialogFiltersSelect.prototype.loadContent = function (data) {
        this.lastParams = {};
        DialogFiltersSelect.__super__.loadContent.call(this, data);

        this.request('get_filters_sizes', this.lastParams, {sizes_group_id: this.sizesGroupId}, this.contentLoaded.bind(this));
    };

    DialogFiltersSelect.prototype.contentLoaded = function (response) {
        var self = this;
        this.$holder.children().not(this.$loading).remove();
        this.$holder.append(response.sizes);
        this.$holder.find('input').iCheck({
            checkboxClass: 'icheckbox_minimal',
            radioClass: 'iradio_minimal'
        });
        this.$holder.find('a.js_link').on('click', this.contentLinkClick.bind(this));
        this.filtersForm = this.$holder.find('.js_filters_form');
        this.filtersForm.on('submit', this.filtersFormChanged.bind(this));
        this.filtersForm.on('click', '[type=submit]', function () {
            $("[type=submit]", $(this).parents("form")).removeAttr("clicked");
            $(this).attr("clicked", "true");
        });
        this.$holder.find('.js_model_row').on('click', this.modelRowClick.bind(this));
        this.$holder.find('.js_model_row').each(function () {
            new DialogSelectableModelItem($(this), self.sizesGroupId);
        });
        this.$holder.find('.js_size_row').each(function () {
            new DialogSelectableSizeItem($(this), self.sizesGroupId);
        });
        this.hideLoading();
    };

    DialogFiltersSelect.prototype.filtersFormChanged = function (e) {
        var data = this.filtersForm.serializeObject();
        e.preventDefault();

        data[this.filtersForm.find("[type=submit][clicked=true]").attr('name')] = 1;

        this.loadContent(data);
    };

    DialogFiltersSelect.prototype.contentLinkClick = function (e) {
        var $this = $(e.target);
        e.preventDefault();

        this.loadContent($this.data('params'));
    };

    mix(DialogFiltersSelect, modelsListMixin);

    return DialogFiltersSelect;

})(LoadableContent);

/**
 * Dialog sizes select control
 */
var DialogSizesSelect = (function (superClass) {
    extend(DialogSizesSelect, superClass);

    function DialogSizesSelect($holder) {
        var self = this;
        DialogSizesSelect.__super__.constructor.call(this, $holder);
        // Reload content when tab activated
        $('a[href="#' + this.$holder.attr('id') + '"]').on('show.bs.tab', function () {
            self.loadContent();
        });
    }

    DialogSizesSelect.prototype.setSizesGroupId = function (sizesGroupId) {
        this.sizesGroupId = sizesGroupId;
    };

    DialogSizesSelect.prototype.loadContent = function ($sizeSelect) {
        DialogSizesSelect.__super__.loadContent.call(this, $sizeSelect);
        this.request('get_sizes', this.lastParams, {sizes_group_id: this.sizesGroupId}, this.contentLoaded.bind(this));

    };

    DialogSizesSelect.prototype.contentLoaded = function (response) {
        var self = this;
        this.$holder.children().not(this.$loading).remove();
        this.$holder.append(response.sizes);
        this.$holder.find('a').on('click', this.contentLinkClick.bind(this));
        this.$holder.find('.js_model_row').on('click', this.modelRowClick.bind(this));
        this.$holder.find('.js_submit_filters').on('click', this.submitFilters.bind(this));
        this.$holder.find('input').iCheck({
            checkboxClass: 'icheckbox_minimal',
            radioClass: 'iradio_minimal'
        });
        this.$holder.find('.js_model_row').each(function () {
            new DialogSelectableModelItem($(this), self.sizesGroupId);
        });
        this.$holder.find('.js_size_row').each(function () {
            new DialogSelectableSizeItem($(this), self.sizesGroupId);
        });
        this.hideLoading();
    };

    DialogSizesSelect.prototype.contentLinkClick = function (e) {
        var $this = $(e.target);
        e.preventDefault();

        this.loadContent($this.data('params'));
    };

    DialogSizesSelect.prototype.submitFilters = function (e) {
        var params = {};
        e.preventDefault();

        this.$holder.find('.js_filter').each(function () {
            var $this = $(this);
            params[$this.attr('name')] = $this.val();
        });

        this.loadContent(params);
    };

    mix(DialogSizesSelect, modelsListMixin);

    return DialogSizesSelect;

})(LoadableContent);

var DialogSelectableModelItem = (function () {
    function DialogSelectableModelItem($content, sizesGroupId) {
        this.$content = $content;
        this.sizesGroupId = sizesGroupId;
        this.modelId = this.$content.data('model-id');
        this.$content.find('.js_check_model').on('ifChanged', this.modelCheckToggle.bind(this));
    }

    DialogSelectableModelItem.prototype.modelCheckToggle = function (e) {
        e.stopPropagation();
        this.request('toggle_group_model', {}, {sizes_group_id: this.sizesGroupId, model_id: this.modelId});
        $(document).trigger('shares.model_toggle_' + this.$content.data('model-id'), [e.currentTarget.checked])
    };

    mix(DialogSelectableModelItem, requestMixin);

    return DialogSelectableModelItem;
})();

var DialogSelectableSizeItem = (function () {
    function DialogSelectableSizeItem($content, sizesGroupId) {
        this.$content = $content;
        this.sizesGroupId = sizesGroupId;
        this.sizeId = this.$content.data('size-id');
        this.$content.find('.js_check_size').on('ifChanged', this.sizeCheckToggle.bind(this));
        $(document).on('shares.model_toggle_' + this.$content.data('model-id'), this.sizeCheckToggleToListener.bind(this))
    }

    DialogSelectableSizeItem.prototype.sizeCheckToggle = function (e) {
        this.request('toggle_group_size', {}, {sizes_group_id: this.sizesGroupId, size_id: this.sizeId});
    };

    DialogSelectableSizeItem.prototype.sizeCheckToggleToListener = function (e, flag) {
        if(flag) {
            this.$content.find('.js_check_size').iCheck('check');
        } else {
            this.$content.find('.js_check_size').iCheck('uncheck');
        }
    };

    mix(DialogSelectableSizeItem, requestMixin);

    return DialogSelectableSizeItem;
})();

/**
 * Dialog sizes select control
 * Do the same as DialogSizesSelect, but with only conflict sizes
 */
var DialogConflictSizesSelect = (function (superClass) {
    extend(DialogConflictSizesSelect, superClass);

    function DialogConflictSizesSelect($holder) {
        DialogConflictSizesSelect.__super__.constructor.call(this, $holder);
    }

    DialogConflictSizesSelect.prototype.loadContent = function ($sizeSelect) {
        DialogConflictSizesSelect.__super__.__proto__.loadContent.call(this, $sizeSelect);
        this.request('get_conflict_sizes', this.lastParams, {sizes_group_id: this.sizesGroupId}, this.contentLoaded.bind(this));

    };

    return DialogConflictSizesSelect;
})(DialogSizesSelect);

/**
 * Order size control
 */
var SizesGroupControl = (function () {
    function SizesGroupControl($content) {
        this.$content = $content;
        this.init();
    }

    SizesGroupControl.prototype.init = function () {
        this.$content.find('.js_group_remove').on('click', this.removeGroup.bind(this));
        this.$content.find('.js_group_add_size').on('click', this.selectSizes.bind(this));
        this.$content.find('.js_group_update').on('change', this.updateGroup.bind(this));
    };

    SizesGroupControl.prototype.removeGroup = function (e) {
        e.preventDefault();
        this.request('remove_sizes_group', {}, {sizes_group_id: this.$content.data('group-id')});
    };

    SizesGroupControl.prototype.updateGroup = function (e) {
        var $input = $(e.target), data = {};
        data[$input.attr('name')] = $input.val();

        this.request('update_sizes_group', data, {sizes_group_id: this.$content.data('group-id')}, function () {
        });
    };

    SizesGroupControl.prototype.selectSizes = function (e) {
        e.preventDefault();
        selectSizeDialog.openAddSizeDialog(this.$content.data('group-id'));
    };

    SizesGroupControl.prototype.successSubmit = function (response) {
        $(document).trigger('groups.new_content', [response.groupsTab]);
    };

    mix(SizesGroupControl, requestMixin);

    return SizesGroupControl;
})();

/**
 * Order size control
 */
var SharesControl = (function () {
    function SharesControl() {
        this.$content = $('#shareGroups');
        this.groups = [];
        this.init();
        $(document).on('groups.new_content', this.newContent.bind(this));
    }

    SharesControl.prototype.init = function () {
        var self = this;
        this.$content.find('.js_group').each(function () {
            self.groups.push(new SizesGroupControl($(this)));
        });
        this.$content.find('.js_share_add_group').on('click', this.addGroup.bind(this));
    };

    SharesControl.prototype.addGroup = function () {
        this.request('add_sizes_group');
    };

    SharesControl.prototype.successSubmit = function (response) {
        $(document).trigger('groups.new_content', [response.groupsTab]);
    };

    SharesControl.prototype.newContent = function (e, groupsTab) {
        var $content = $(groupsTab);
        this.$content.replaceWith($content);
        this.$content = $content;
        this.init();
    };

    mix(SharesControl, requestMixin);

    return SharesControl;
})();

$(document).ready(function () {
    new SharesControl();
    window.selectSizeDialog = new OrderSizesDialog;
});
