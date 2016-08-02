function mix(object) {
    var mixins = Array.prototype.slice.call(arguments, 1);
    for (var i = 0; i < mixins.length; ++i) {
        for (var prop in mixins[i]) {
            if (typeof object.prototype[prop] === "undefined") {
                object.prototype[prop] = mixins[i][prop];
            }
        }
    }
}

var requestMixin = {
    baseRouteName: typeof baseRouteName != 'undefined' ? baseRouteName : '',
    baseRouteParams: typeof baseRouteParams != 'undefined' ? baseRouteParams : {},
    request: function (route, data, routeParams, success) {
        var params = {};
        routeParams = $.extend({}, routeParams, this.baseRouteParams);
        if (typeof success == 'undefined') {
            // default success
            params.success = this.successSubmit.bind(this);
        } else {
            if (success) {
                params.success = success;
            }
        }
        $.ajax($.extend({
            url: Routing.generate(this.baseRouteName + '_' + route, routeParams),
            data: data,
            dataType: "json",
            type: "POST",
            error: this.errorSubmit.bind(this)
        }, params));
    },
    errorSubmit: function (responce) {
        alert(responce.status + ' ' + responce.statusText);
    },
    successSubmit: function (responce) {

    }
};

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

var extend = function (child, parent) {
        for (var key in parent) {
            if (hasProp.call(parent, key)) child[key] = parent[key];
        }
        function ctor() {
            this.constructor = child;
        }

        ctor.prototype = parent.prototype;
        child.prototype = new ctor();
        child.__super__ = parent.prototype;
        return child;
    },
    hasProp = {}.hasOwnProperty;

function equalHeight(group) {
    var tallest = 0;
    group.each(function () {
        thisHeight = $(this).height();
        if (thisHeight > tallest) {
            tallest = thisHeight;
        }
    });
    group.height(tallest);
}
