function serialize(objs) {    var out = "";    for (var i = 0; i < objs.length; i++) {        var propKey = objs[i].name + "=";        if (out.indexOf(propKey) == -1) {            out += "&" + propKey;        }        var position = out.indexOf(propKey) + propKey.length;        out = out.substring(0, position) + objs[i].value + "," + out.substring(position);    }    return out.substring(1, out.length - 1).replace(/\,\&/g, "&");}/** * Filters control class */var Filters = (function () {    function Filters() {        var self = this;        this.clearUrl = Routing.generate('filter', {'category': currentCategoryAlias});        this.$filters = $('#filters');        this.$filters.on('change', 'input', this.applyFilters.bind(this));    }    /**     * Apply filters     */    Filters.prototype.applyFilters = function () {        var serialised_array = this.$filters.serializeArray();        var filters = serialize(serialised_array);        var price_from = $('#min').val();        var price_to = $('#max').val();        setTimeout(function () {            window.location = this.clearUrl + '?' + filters + '&price_from=' + price_from + '&price_to=' + price_to + '&sort=' + $('#sort').val();        }.bind(this), 1500);    };    return Filters;})();$(document).ready(function () {    new Filters();});