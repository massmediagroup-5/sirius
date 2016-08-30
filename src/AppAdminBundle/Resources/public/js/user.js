var WholesaleRoleWatcher = (function () {
    function WholesaleRoleWatcher() {
        this.$input = $('[name$="[roles][]"][value="ROLE_WHOLESALER"]');
        this.$input.on('ifChanged', this.update.bind(this));

        this.update();
    }

    WholesaleRoleWatcher.prototype.update = function () {
        var $bonusesInput = $('[id$="_bonuses"]'), $discountInput = $('[id$="_discount"]');
        if (this.$input.prop('checked')) {
            $bonusesInput.hide();
            $discountInput.show();
        } else {
            $bonusesInput.show();
            $discountInput.hide();
        }
    };

    return WholesaleRoleWatcher;
})();

$(document).ready(function () {
    new WholesaleRoleWatcher();
});
