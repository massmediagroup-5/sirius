function serialize(objs) {    var out = "";    for (var i = 0; i < objs.length; i++) {        var propKey = objs[i].name + "=";        if (out.indexOf(propKey) == -1) {            out += "&" + propKey;        }        var position = out.indexOf(propKey) + propKey.length;        out = out.substring(0, position) + objs[i].value + "," + out.substring(position);    }    return out.substring(1, out.length - 1).replace(/\,\&/g, "&");}/** * Filters control class */var Filters = (function () {    function Filters() {        if (typeof current_route != 'undefined' && current_route == 'search') {            this.clearUrl = Routing.generate('search', {'search': currentCategoryAlias});        } else {            this.clearUrl = Routing.generate('filter', {'category': currentCategoryAlias});        }        this.$filters = $('#filters');        this.$filters.on('click', '.filters-submit', this.applyFilters.bind(this));    }    /**     * Apply filters     */    Filters.prototype.applyFilters = function (e) {        e.preventDefault();        var serialised_array = this.$filters.serializeArray();        var filters = serialize(serialised_array);        var price_from = $('#min').val();        var price_to = $('#max').val();        window.location = this.clearUrl + '?' + filters + '&price_from=' + price_from + '&price_to=' + price_to + '&sort=' + $('select.sorting_dropdown').val();    };    return Filters;})();/** * Sorting control class */var Sort = (function () {    function Sort() {        this.clearUrl = window.location.href.split('?')[0];        this.$sort = $('select.sorting_dropdown');        this.$filters = $('#filters');        this.$sort.on('change', this.applySort.bind(this));    }    /**     * Apply filters     */    Sort.prototype.applySort = function (e) {        var filters = [],            clearUrl = this.clearUrl,            price_from = $('#min').val(),            price_to = $('#max').val(),            $sortsSelect = $(e.target),            leftFilters = this.$filters.serializeArray();        setTimeout(function () {            filters.push({name: 'sort', value: $sortsSelect.val()});            if (leftFilters.length) {                filters = filters.concat(leftFilters);            }            filters.push({name: 'price_from', value: price_from});            filters.push({name: 'price_to', value: price_to});            window.location = clearUrl + '?' + serialize(filters);        }.bind(this), 500);    };    return Sort;})();$(window).ready(function () {    if ($('#filters').length) {        new Filters();    }    new Sort();});