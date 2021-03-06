var phoneFormat = "+38(999)-999-99-99";

/**
 * Function to do extending
 *
 * @param child
 * @param parent
 * @returns {*}
 */
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

function is_touch_device() {
    return 'ontouchstart' in window // works on most browsers
        || 'onmsgesturechange' in window; // works on ie10
}


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

function getParameterByName(name, url) {
    if (!url) {
        url = window.location.href;
    }
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function parseISO8601(dateStr) {
    var a = dateStr.split(" ");
    var d = a[0].split("-");
    var t = a[1].split(":");
    return new Date(d[0], (d[1] - 1), d[2], t[0], t[1], t[2]);
}

function initSelects ($content) {
    $content = $content ? $content : $('body');
    $content.find('.select').select2({
        minimumResultsForSearch: 2
    }).on('change select2-opening', function () {
        $(this).removeClass('error');
    });

    var sort_select = $content.find('select.sorting_dropdown').data('sort');
    $content.find("select.sorting_dropdown").val(sort_select);
    $content.find('select.sorting_dropdown').select2({
        minimumResultsForSearch: -1,
        dropdownCssClass: 'sorting_dropdown'
    });
}

function initAmountSelects ($content) {
    $content = $content ? $content : $('body');

    $content.find('.amount-select .inp').on('click', ':not(.amount-select-drop)', function (e) {
        $content.find(this).toggleClass('dropActive');
    });
    if($content.find('.amount-select-drop').length > 0) {
        $content.find('.amount-select-drop').mCustomScrollbar();
    }
    $content.find('.amount-select-drop a').on('click', function(e){
        e.preventDefault();
        var value = $(this).text(),
            inp = $(this).closest('.inp').find('input');
        inp.val(value).trigger('change');
        $(this).closest('.inp').removeClass('dropActive');
        return false;
    });

    $content.find(".amount-control .plus").click(function (e) {
        var text = $(this).prev("input"),
            value = parseInt(text.val(), 10);
        e.preventDefault();

        value = isNaN(value) ? 0 : value;
        text.val(value + 1).trigger('change');

        return false;
    });

    $content.find(".amount-control .minus").click(function (e) {
        var text = $(this).next("input"),
            value = parseInt(text.val(), 10);
        e.preventDefault();

        value = isNaN(value) ? 0 : value;

        if ((text.val() == text.data('min'))||(text.val() <= 0)) {
            return false
        }
        else {
            text.val(value - 1).trigger('change');
        }

        return false;
    });
}

/**
 * Execute last command on body click
 */
var BodyCommands = (function () {
    function BodyCommands() {
        this.stack = [];
        $('body').on('click', this.executeLastCommand.bind(this));
    }

    BodyCommands.prototype.push = function (command) {
        this.stack.push(command);
    };

    BodyCommands.prototype.executeLastCommand = function (e) {
        if (this.stack.length) {
            var command = this.stack[this.stack.length - 1];
            if (command(e)) {
                this.stack.splice(-1, 1)
            }
        }
    };

    return BodyCommands;
})();

/**
 * Model images gallery
 */
var OwlGallery = (function () {
    function OwlGallery($holder) {
        this.$holder = $holder;
        this.init();
    }

    OwlGallery.prototype.init = function () {
        var self = this;
        this.$holder.on('initialized.owl.carousel', function (event) {
            var items = event.item.count;
            var item = event.item.index;
            self.$holder.find('.owl-item').each(function () {
                var $this = $(this);
                $this.attr('rel', $this.index());
            });

            $(this).closest('.gallery-box__main').find('.overall').text(items);
            $(this).closest('.gallery-box__main').find('.current').text(item + 1);
        });

        this.$holder.on('translate.owl.carousel', function (event) {
            var item = event.item.index;
            $(this).closest('.gallery-box__main').find('.current').text(item + 1);
        });

        this.$holder.owlCarousel({
            items: 1,
            nav: true,
            loop: this.$holder.children().length > 1,
            navText: [
                "",
                "<i class='icon icon-popup-arr-next'></i><i class='icon icon-popup-arr-next-hover hover'></i>"
            ],
            thumbs: true,
            thumbImage: true,
            responsive: false,
            thumbContainerClass: 'gallery-box__thumb',
            thumbItemClass: 'owl-thumb-item'
        });
    };

    OwlGallery.prototype.to = function (index) {
        this.$holder.trigger('to', index);
    };

    return OwlGallery;
})();

var bodyCommands = new BodyCommands;

jQuery.extend(verge);
var desktop = true,
    tablet = false,
    mobile = false;


$(window).resize(function () {
    if ($.viewportW() >= 1041) {
        desktop = true;
        tablet = false;
        mobile = false;
    }
    if ($.viewportW() >= 768 && $.viewportW() <= 1040) {
        desktop = false;
        tablet = true;
        mobile = false;
    } else {
        if ($.viewportW() <= 767) {
            desktop = false;
            tablet = false;
            mobile = true;
        }
    }


}).resize();

$(function () {
    function equalHeight(group, groupSize) {
        if (!group.length) {
            return;
        }
        groupSize = +(groupSize || 0);
        if (groupSize < 1) {
            groupSize = group.length;
        }
        var start = -groupSize, part;
        while ((part = group.slice(start += groupSize, start + groupSize)) && part.length) {
            part.height(Math.max.apply(null, $.makeArray(part.map(function () {
                return $(this).height();
            }))));
        }
    }


});
//ACTION COUNTER FUNCTION START//
(function (e) {
    e.fn.countdown = function (t, n) {
        function i() {
            eventDate = parseISO8601(r.date) / 1e3;
            currentDate = Math.floor(e.now() / 1e3);
            if (eventDate <= currentDate) {
                n.call(this);
                clearInterval(interval)
            }
            seconds = eventDate - currentDate;
            days = Math.floor(seconds / 86400);
            seconds -= days * 60 * 60 * 24;
            hours = Math.floor(seconds / 3600);
            seconds -= hours * 60 * 60;
            minutes = Math.floor(seconds / 60);
            seconds -= minutes * 60;
            days == 1 ? thisEl.find(".timeRefDays").text("дней") : thisEl.find(".timeRefDays").text("дней");
            hours == 1 ? thisEl.find(".timeRefHours").text("часов") : thisEl.find(".timeRefHours").text("часов");
            minutes == 1 ? thisEl.find(".timeRefMinutes").text("минут") : thisEl.find(".timeRefMinutes").text("минут");
            seconds == 1 ? thisEl.find(".timeRefSeconds").text("секунд") : thisEl.find(".timeRefSeconds").text("секунд");
            if (r["format"] == "on") {
                days = String(days).length >= 2 ? days : "0" + days;
                hours = String(hours).length >= 2 ? hours : "0" + hours;
                minutes = String(minutes).length >= 2 ? minutes : "0" + minutes;
                seconds = String(seconds).length >= 2 ? seconds : "0" + seconds
            }
            if (!isNaN(eventDate)) {
                thisEl.find(".days").text(days);
                thisEl.find(".hours").text(hours);
                thisEl.find(".minutes").text(minutes);
                thisEl.find(".seconds").text(seconds)
            } else {
                alert("Неверная дата. Пример: 30 October 2013 15:50:00");
                clearInterval(interval)
            }
        }

        var thisEl = e(this);
        var r = {
            date: null,
            format: null
        };
        t && e.extend(r, t);
        i();
        interval = setInterval(i, 1e3)
    }
})(jQuery);
//ACTION COUNTER FUNCTION END//

function flashSuccess() {
    if ($('#flash-success').length) {
        var popupSettings={
            'scrolling':0,
            'autoScale': true,
            'transitionIn': 'elastic',
            'transitionOut': 'elastic',
            'speedIn': 500,
            'speedOut': 300,
            'autoDimensions': true,
            'centerOnScroll': true,
            'href' : '#flash-success',
            'padding' : '0',
            'height' : 'auto',
            helpers: {
                overlay: {
                    locked: false
                }
            }
        };

        $.fancybox.open(popupSettings);
    }
}

function toogleTable() {
    var table = $('.bonuses-table');
    table.hide();
    $(document).on('click','.my-discount .toggle',function (e) {
        e.preventDefault();
        table.toggle();
    })
}

$(document).ready(function () {

    flashSuccess();
    toogleTable();

    $.validator.addMethod("customemail",
        function(email) {
            var emailReg = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
            var valid = emailReg.test(email);

            if(!valid) {
                return false;
            } else {
                return true;
            }
        },
        "Введите корректный E-mail"
    );

    $('#fos_reg_form').validate({
        highlight: function (element) {
            $(element).closest('.inp').addClass("inp_error");
        },
        unhighlight: function (element) {
            $(element).closest('.inp').removeClass("inp_error");
        },
        rules: {
            'fos_user_registration_form[phone]': {
                required: true
            },
            'fos_user_registration_form[email]': {
                required: true,
                email: true
            },
            'fos_user_registration_form[plainPassword][first]': {
                required: true
            },
            'fos_user_registration_form[plainPassword][second]': {
                required: true,
                equalTo: '#fos_user_registration_form_plainPassword_first'
            },
            'fos_user_registration_form[name]': {
                required: true
            },
            'fos_user_registration_form[surname]': {
                required: true
            }
        },
        messages: {
            'fos_user_registration_form[phone]': {
                required: "Введите Ваш телефон"
            },
            'fos_user_registration_form[email]': {
                required: "Укажите Ваш E-mail",
                email: "Введите корректный E-mail"
            },
            'fos_user_registration_form[plainPassword][first]': {
                required: "Введите пароль"
            },
            'fos_user_registration_form[plainPassword][second]': {
                required: "Повторите пароль",
                equalTo: "Пароли не совпадают"
            },
            'fos_user_registration_form[name]': {
                required: "Укажите Ваше имя"
            },
            'fos_user_registration_form[surname]': {
                required: "Укажите Ваше фамилию"
            }
        },
        submitHandler: function(form) {
            form.submit();
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.inp'));
        }
    });

    var already = false;
    $('#create_order_phone').keyup(function() {
        var phone = $('#create_order_phone').val();
        if(phone.slice(-1)!='_'){
            if(!already){
                $.ajax({
                    url: Routing.generate('get_order_info_by_phone'),
                    data: {
                        phone: phone,
                    },
                    dataType: "json",
                    type: "POST",
                    success: function (data) {
                        if(data.status =='OK'){
                            console.log(data);
                            if(data.info){
                                already = true;
                                $('#create_order_name').val(data.info.name);
                                $('#create_order_surname').val(data.info.surname);
                                $('#create_order_email').val(data.info.email);
                                $("#create_order_delivery_type_"+data.info.carriers).click();
                                $("#create_order_pay_"+data.info.pay).click();
                                if(data.info.carriers == 1){
                                    $('#create_order_np_delivery_city').val(data.info.cities);
                                    $('#create_order_np_delivery_city').trigger("change");
                                    setTimeout(function() {
                                        $('#create_order_np_delivery_store').val(data.info.stores);
                                        $('#create_order_np_delivery_store').trigger("change");
                                    }, 500);
                                }else {
                                    $('#create_order_customDelivery').val(data.info.custom_delivery);
                                }
                            }
                            return false;
                        }else{
                            console.log(data);
                        }
                    },
                    error: function (message) {
                        console.log(message.status + ' ' + message.statusText);
                    },
                });
            }
        }
    });

    $('.change-passw-form').validate({
        highlight: function (element) {
            $(element).closest('.inp').addClass("inp_error");
        },
        unhighlight: function (element) {
            $(element).closest('.inp').removeClass("inp_error");
        },
        rules: {
            username: {
                required: true,
                customemail: true
            }
        },
        messages: {
            username: {
                required: "Укажите Ваш E-mail",
                email: "Введите корректный E-mail"
            }
        },
        submitHandler: function(form) {
            /*
             * Get all form values
             */
            var values = {};
            $.each( $(form).serializeArray(), function(i, field) {
                values[field.name] = field.value;
            });
            $.ajax({
                url: $(form).attr('action'),
                data: values,
                //dataType: "json",
                type: "POST",
                success: function (data) {
                    //console.log(data);
                    $('#email-change .text-popup__i').html('');
                    $('#email-change .text-popup__i .btn-row').remove();
                    $('#email-change .text-popup__i').prepend(data);
                    $.fancybox.open({href: '#email-change', type: 'inline'});
                    return false;
                },
                error: function (message) {
                    $('#email-change .text-popup__i').html('');
                    $('#email-change .text-popup__i .btn-row').remove();
                    console.log(message.status + ' ' + message.statusText);
                    return false;
                },
            });
            return false;
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.inp'));
        }
    });

    $('.chief-form').validate({
        highlight: function (element) {
            $(element).closest('.inp').addClass("inp_error");

        },
        unhighlight: function (element) {
            $(element).closest('.inp').removeClass("inp_error");
        },
        rules: {
            email: {
                required: true
            },
            'form[email]': {
                required: true
            },
            name: {
                required: true
            },
            'form[name]': {
                required: true
            },
            message: {
                required: true
            },
            'form[message]': {
                required: true
            }
        },
        messages: {
            email: {
                required: "Укажите Ваш E-mail"
            },
            'form[email]': {
                required: "Укажите Ваш E-mail"
            },
            name: {
                required: "Укажите Ваше имя"
            },
            'form[name]': {
                required: "Укажите Ваше имя"
            },
            message: {
                required: "Введите Ваше сообщение"
            },
            'form[message]': {
                required: "Введите Ваше сообщение"
            }
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.inp'));
        }
    });

    $('.back-call form').validate({
        highlight: function (element) {
            $(element).closest('.phone-inp').addClass("inp_error");
        },
        unhighlight: function (element) {
            $(element).closest('.phone-inp').removeClass("inp_error");
        },
        rules: {
            phone: {
                required: true
            }
        },
        messages: {
            phone: {
                required: "Укажите Ваш телефон"
            }
        },
        submitHandler: function(form) {
            var phone = $(form).find('.phone-inp').val();
            $.ajax({
                url: Routing.generate('callback'),
                data: {
                    phone: phone,
                },
                dataType: "json",
                type: "POST",
                success: function (data) {
                    if(data.status =='OK'){
                        $.fancybox.open({href: '#tnx', type: 'inline'});
                        $(form).find('input[type=text], textarea').val('');
                        return false;
                    }else{
                        console.log(data);
                        return false;
                    }
                },
                error: function (message) {
                    console.log(message.status + ' ' + message.statusText);
                    return false;
                },
            });
            return false;
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.inp'));
        }
    });

    $('body').on('focus click', '.inp_error input, .inp_error textarea', function () {
        $(this).closest('.inp_error').removeClass('inp_error').find('.error-msg').remove();
    });

    $('body').on('click', '.inp_error select, .inp_error .select', function () {
        $(this).closest('.inp_error').removeClass('inp_error').find('.error-msg').remove();
    });

    $('body').on('click', 'input.error', function () {
        $(this).removeClass('error');
    });

    //FORM VALIDATION END//


    //ACTION COUNTER START//
    function e() {
        var e = new Date;
        e.setDate(e.getDate() + 60);
        dd = e.getDate();
        mm = e.getMonth() + 1;
        y = e.getFullYear();
        futureFormattedDate = mm + "/" + dd + "/" + y;
        return futureFormattedDate
    }

    $(".share-counter").each(function () {
        var el = $(this);
        var dataCount = el.attr('data-count-end');

        el.countdown({
            date: dataCount,
            format: 'on'
        })
    });
    //ACTION COUNTER END//


    //floating panel
    var floatBox = $('.floating-box');

    if (floatBox.length) {
        floatBox.removeClass('fixed').removeClass('static');
        var floatBoxTopPos = floatBox.offset().top,
            floatBoxWr = ($('.floating-box-wr').offset().top + $('.floating-box-wr').outerHeight()),
            floatBoxElHeight = floatBox.outerHeight();
        $(window).resize(function () {
            floatBox.css('width', $('.floating-box-main').width());
            if ($(window).height() > floatBoxElHeight) {

                $(window).scroll(function () {
                    floatBoxWr = ($('.floating-box-wr').offset().top + $('.floating-box-wr').outerHeight() - 60);

                    var top = $(document).scrollTop() + $('header').height() + 20;

                    if (top > floatBoxTopPos && top < floatBoxWr - floatBoxElHeight) {
                        floatBox.addClass('fixed').removeClass('static');
                    } else if (top > floatBoxWr - floatBoxElHeight) {
                        floatBox.removeClass('fixed').addClass('static');
                    } else {
                        floatBox.removeClass('fixed').removeClass('static');
                    }
                });
            } else {
                $(window).scroll(function () {
                    floatBox.removeClass('fixed').removeClass('static');
                });
            }
        }).resize();
    }

});

$(window).load(function () {
    setTimeout(function () {
        $('.preloader').addClass('disable');
        $('body').removeClass('loading');
    }, 200);

    if ($(window).width() > 991) {
        equalHeight($('.promo .item'));
    }

    equalHeight($('.product-list .item'));
    $('.warranty-info__i .col__i').css('height','auto');
    $('.warranty-info__i').each(function(){
        equalHeight($(this).find('.col__i'), 2);
    });
	
	$(document).on('click', '.js_delivery_multiple', function(){
		var customDelivery = $('input[name="create_order[customDelivery]"');
		customDelivery.show();
        if ($('.js_delivery_multiple').length>1) {           
           var input = $("<div />").append(customDelivery.clone()).html();
           customDelivery.remove();
           $(this).closest('.inp-i').after(input);
        }
	});
	$(document).on('click', '.js_delivery_single', function(){
        var customDelivery = $('input[name="create_order[customDelivery]"');
        customDelivery.hide();
	});

    if($('.slider__i .item').length > 1){
        var $slider = $('.slider__i');
        $slider.owlCarousel({
            responsive: true,
            items: 1,
            loop: $slider.children().length > 1,
            nav: true,
            animateOut: 'pulse',
            auto: true,
            autoplay: true,
            autoplayTimeout: 7000,
            navText: [
                "<i class='icon icon-slider-prev'></i><i class='icon icon-slider-prev-hover hover'></i>",
                "<i class='icon icon-slider-next'></i><i class='icon icon-slider-next-hover hover'></i>"
            ],
            transitionStyle: "fade"
        });
    } else {
        $('.slider .controls').hide()
    }

    $('.opt-list .add-to-cart').each(function () {
        $(this).height($(this).closest('td').height());
    });

    $('.opt-list .open-btn').on('click', function (e) {
        e.preventDefault();
        $(this).closest('.item').toggleClass('active').find('.item-drop').slideToggle();
    });

    $('.close-collect').on('click', function (e) {
        e.preventDefault();
        $(this).closest('.amount-control').find('.collect').removeClass('active')
    });

    $('.catalog .item').on('mouseenter', function () {
        if ($(this).closest('.item').find('.color-slider__i a').length > 3) {
            var $slider = $(this).closest('.item').find('.color-slider__i');
            $slider.owlCarousel({
                responsive: true,
                items: 3,
                loop: $slider.children().length > 1,
                nav: true,
                auto: false,
                navText: [
                    "<i class='icon icon-small-arr-prev'></i><i class='icon icon-small-arr-prev hover'></i>",
                    "<i class='icon icon-small-arr-next'></i><i class='icon icon-small-arr-next hover'></i>"
                ]
            });
        }
    });

    $('.catalog .item').on('mouseenter', function () {
        if ($(this).closest('.item').find('.color-slider__i a').length > 3) {

        }
    });
    $('.last-viewed__i').owlCarousel({
        responsive: true,
        items: 4,
        nav: true,
        auto: false,
        slideBy: 4,
        navText: [
            "<i class='icon icon-view-prev'></i><i class='icon icon-view-prev-hover hover'></i>",
            "<i class='icon icon-view-next'></i><i class='icon icon-view-next-hover hover'></i>"
        ]
    });

    $('.slider__i .wrap').height($('.slider__i').height());


    $('.slider .prev').on('click', function (e) {
        e.preventDefault();
        $('.owl-prev').click();
    });
    $('.slider .next').on('click', function (e) {
        e.preventDefault();
        $('.owl-next').click();
    });

    $('.pass-recovery-btn').on('click', function (e) {
        e.preventDefault();
        $(this).closest('.login').hide();

        $('.pass-recovery').show();
    });
    $('.pass-recovery .close').on('click', function (e) {
        e.preventDefault();
        $(this).closest('.pass-recovery ').hide();

        $('.login').show();
    });
    $('.search-btn').on('click', function (e) {
        e.preventDefault();
        $('.search-drop-results').removeClass('active');

        $(this).addClass('active');

        $('.search-drop').addClass('active');
        $('.header').addClass('search-active');
        $('.profile-btn') && $('.profile-drop') && $('.cart-btn') && $('.cart-drop') && $('.opt-cart-drop').removeClass('active');
        $('.header').removeClass('profile-active');
        $('.header').removeClass('cart-active');
    });
    $('.cart-btn').on('click', function (e) {
        e.preventDefault();
        var count = $('.js_cart_total_count').html();
        if(count == 0){
            $('.cart-drop .inp').hide();
        }
        $(this).addClass('active');
        $('.header').addClass('cart-active');
        $('.cart-drop').addClass('active');
        $('.opt-cart-drop').addClass('active');
        $('.profile-btn') && $('.profile-drop') && $('.search-drop').removeClass('active');
        $('.header').removeClass('profile-active');
        $('.header').removeClass('search-active');
    });
    $('.profile-btn, .preorder-auth').on('click', function (e) {
        e.preventDefault();
        if($(this).hasClass('preorder-auth')){
            $.fancybox.close();
        }
        $('.pass-recovery').hide();
        $('.login').show();
        $(this).addClass('active');
        $('.header').addClass('profile-active');
        $('.profile-drop').addClass('active');

        $('.cart-btn') && $('.cart-drop') && $('.opt-cart-drop') && $('.search-drop').removeClass('active');
        $('.header').removeClass('cart-active');
        $('.header').removeClass('search-active');

    });

    $('.profile-btn, .preorder-auth').on('click', function(e) {
        var $usrLoginDrop = $('.user-login-drop');
        if(!$usrLoginDrop.hasClass('active')) {
            $usrLoginDrop.addClass('active');
            // Close popup on next body click
            bodyCommands.push(function (e) {
                if ($(e.target).closest('.user-login-drop').length) {
                    // not hide element
                    return false;
                }
                $usrLoginDrop.removeClass('active');
                return true;
            });
        }
        e.stopPropagation();
    });

    //$().focus(function(){
    $('.search-drop input[type="search"]').on("submit", function() {
        if($(this).val().length >= 3){
            //console.log($(this).val());
            var search_string = $(this).val();
            $.ajax({
                url: Routing.generate('ajax_search'),
                data: {
                    search: search_string
                },
                dataType: "json",
                type: "POST",
                success: function (data) {
                    if(data.status =='OK'){
                        console.log(data.result);
                        $('.search-drop-results').html(data.html);
                        $('.search-drop-results').addClass('active');
                        return false;
                    }else{
                        console.log(data);
                        return false;
                    }
                },
                error: function (message) {
                    console.log(message.status + ' ' + message.statusText);
                    return false;
                },
            });
        }
        return false;
    });
    //return false;
    //});

    //SUBMENU HOVER START//
    if ($(window).width() > 1140) {
        $(".top-nav__i > ul > li").on({
            mouseenter: function () {
                var el = $(this);

                if (el.children('.sub-nav').length > 0) {
                    $('.header_sub-nav').addClass('active');

                }


            },
            mouseleave: function () {
                $('.header_sub-nav').removeClass('active');

            }
        });
    }
    //SUBMENU HOVER END//



//MOBILE MENU START
    if ($(window).width() < 1140) {
        $('.has-sub-nav > a').on('click', function (e) {
                e.preventDefault();
                $('.has-sub-nav.active').find('.sub-nav').slideUp();
                $('.has-sub-nav.active').removeClass('active');

                if (!$(this).closest('li').hasClass("active")) {
                    $(this).closest('li').addClass('active');
                    $(this).closest('li').find('.sub-nav').slideDown();

                }
            }
        )
    }

    $('.mobile-nav-btn').on('click', function (e) {
        e.preventDefault();
        var el = $(this);
        if (el.hasClass('active')) {
            el.removeClass('active');
            $('.top-nav').removeClass('active');
            $('.header').removeClass('top-nav-active')
        } else {
            el.addClass('active');
            $('.top-nav').addClass('active');
            $('.header').addClass('top-nav-active')
        }
    });
//MOBILE MENU END


    $(document).on('click', function (e) {
        if ($(e.target).closest(".search-active .user-nav").length === 0) {
            $('.search-drop').removeClass('active');
            $('.header').removeClass('search-active');
        }
        if ($(e.target).closest(".cart-active .user-nav").length === 0) {
            $('.cart-drop').removeClass('active');
            $('.header').removeClass('cart-active');
        }
        if ($(e.target).closest(".cart-active .user-nav").length === 0) {
            $('.opt-cart-drop').removeClass('active');
            $('.header').removeClass('cart-active');
        }
        if ($(e.target).closest(".total-cell.active").length === 0) {
            $('.tooltip').removeClass('active');
            $('.tooltip').removeClass('active');
        }
        if ($(e.target).closest(".profile-active .user-nav").length === 0) {
            $('.profile-drop').removeClass('active');
            $('.header').removeClass('profile-active');
        }
        if ($(e.target).closest(".product-list .item.active").length === 0) {
            $('.darkness').removeClass('active');
            $('.product-list .item').removeClass('active');
        }
        if ($(e.target).closest(".inp.dropActive").length === 0) {
            $('.inp.dropActive').removeClass('dropActive');
        }
        if ($(window).width() < 1140) {
            if ($(e.target).closest(".top-nav-active").length === 0) {
                $('.top-nav').removeClass('active');
                $('.header').removeClass('top-nav-active');
                $('.mobile-nav-btn').removeClass('active');
            }
        }
    });

    $('.close-popup').click(function (e) {
        e.preventDefault();
        parent.$.fancybox.close();
    });

    $('.phone-inp' + '').mask(phoneFormat);


    //PRICE SLIDER START//

    var minPriceInp = $('#min');
    var maxPriceInp = $('#max');
    var slider = $(".price-slider__i");
    minPriceInp.val(minPriceInp.data('value'));
    maxPriceInp.val(maxPriceInp.data('value'));

    slider.slider({
        range: true,
        min: minPriceInp.data('min'),
        max: maxPriceInp.data('max'),
        values: [minPriceInp.data('value'), maxPriceInp.data('value')],
        slide: function (event, ui) {
            minPriceInp.val(ui.values[0]).trigger('change');
            maxPriceInp.val(ui.values[1]).trigger('change');
        }
    });

    minPriceInp.change(function () {
        var low = minPriceInp.val() || minPriceInp.data('min'),
            high = maxPriceInp.val() || maxPriceInp.data('max');
        low = Math.min(low, high);
        low = Math.max(low, minPriceInp.data('min'));
        minPriceInp.val(low);
        slider.slider('values', 0, low);
    });

    maxPriceInp.change(function () {
        var low = minPriceInp.val() || minPriceInp.data('min'),
            high = maxPriceInp.val() || maxPriceInp.data('max');
        high = Math.max(low, high);
        high = Math.min(high, maxPriceInp.data('max'));
        maxPriceInp.val(high);
        slider.slider('values', 1, high);
    });

    var input = $('.price-slider input');
    input.on('keydown', function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 || (e.keyCode == 65 && e.ctrlKey === true) || (e.keyCode == 67 && e.ctrlKey === true) || (e.keyCode == 88 && e.ctrlKey === true) || (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });


    $('input.only-integers').on('keyup change', function () {
        var $this = $(this),
            val;
        if (!$this.val().match(/^[1-9][0-9]*$/)) {
            val = $this.val().replace(/[^\d]/g, '');
            if (val.charAt(0) == '0') {
                val = val.substr(1);
            }
            $this.val(val);
        }
    });

    var inputNum = $('input.only-numbers');
    inputNum.on('keydown', function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 || (e.keyCode == 65 && e.ctrlKey === true) || (e.keyCode == 67 && e.ctrlKey === true) || (e.keyCode == 88 && e.ctrlKey === true) || (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    var valKeyDown;
    var valKeyUp;

    function integerOnly(e) {
        e = e || window.event;
        var code = e.which || e.keyCode;
        if (!e.ctrlKey) {
            var arrIntCodes1 = [96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 8, 9, 116];   // 96 TO 105 - 0 TO 9 (Numpad)
            if (!e.shiftKey) {                          //48 to 57 - 0 to 9
                arrIntCodes1.push(48);                  //These keys will be allowed only if shift key is NOT pressed
                arrIntCodes1.push(49);                  //Because, with shift key (48 to 57) events will print chars like @,#,$,%,^, etc.
                arrIntCodes1.push(50);
                arrIntCodes1.push(51);
                arrIntCodes1.push(52);
                arrIntCodes1.push(53);
                arrIntCodes1.push(54);
                arrIntCodes1.push(55);
                arrIntCodes1.push(56);
                arrIntCodes1.push(57);
            }
            var arrIntCodes2 = [35, 36, 37, 38, 39, 40, 46];
            if ($.inArray(e.keyCode, arrIntCodes2) != -1) {
                arrIntCodes1.push(e.keyCode);
            }
            if ($.inArray(code, arrIntCodes1) == -1) {
                return false;
            }
        }
        return true;
    }

    $('.only-numbers-for-bonuses').keydown(function (event) {
        valKeyDown = this.value;
        return integerOnly(event);
    });

    $('.only-numbers-for-bonuses').keyup(function (event) {
        valKeyUp = this.value;
        if (!new RegExp('^[0-9]*$').test(valKeyUp)) {
            $(this).val(valKeyDown);
        }
    });

    $('.only-numbers-for-bonuses').bind('input propertychange', function(e) {    //if user copy-pastes some character value using mouse
        valKeyUp = this.value;
        if (!new RegExp('^[0-9]*$').test(valKeyUp)) {
            $(this).val(valKeyDown);
        }
        this.value = this.value.replace(/^[0]/g, '');
    });

    //PRICE SLIDER END//

    //FILTER DROPS START
    $('.filters .item-name a').on('click', function (e) {
        e.preventDefault();
        $(this).closest('.item').toggleClass('active');
        $(this).closest('.item').find('.item-drop').slideToggle();
    });
    $('.filters .item-name a[data-active="1"]').click();

    //FILTER DROPS END
    initSelects();

    //ORDER FORM PASSWORD RECOVERY START//

    $('.regular-buyer .passw-recovery').on('click', function (e) {
        e.preventDefault();
        $('.regular-buyer').addClass('hide');
        $('.order-info .pass-recovery').removeClass('hide');
    });

    $('.order-info .pass-recovery .back').on('click', function (e) {
        e.preventDefault();
        $('.order-info .pass-recovery').addClass('hide');
        $('.regular-buyer').removeClass('hide');

    });
    //ORDER FORM PASSWORD RECOVERY END//


    //PRODUCT SLIDER WITH PREVIEW START//
    var $sync1 = $(".big-img_slider"),
        $sync2 = $(".main-img-slider__i"),
        flag = false,
        duration = 300;

    $sync1
        .owlCarousel({
            items: 1,
            nav: false,
            dots: false
        })
        .on('changed.owl.carousel', function (e) {
            if (!flag) {
                flag = true;
                $sync2.trigger('to.owl.carousel', [e.item.index, duration, true]);
                flag = false;
            }
        });

    $sync2
        .owlCarousel({
            items: 4,
            nav: true,
            dots: false,
            loop: $sync2.children().length > 1

        })
        .on('click', '.owl-item', function () {
            $sync1.trigger('to.owl.carousel', [$(this).index() - 4, duration, true]);

        })
        .on('changed.owl.carousel', function (e) {
            if (!flag) {
                flag = true;
                $sync1.trigger('to.owl.carousel', [e.item.index - 4, duration, true]);
                flag = false;
            }
        });

    $(".popup_gallery").each(function () {
        let $this = $(this);
        if($($this.attr('href')).length) {
            let owlGlry = new OwlGallery($($this.attr('href')).find('.big-img'));

            $this.fancybox({
                padding: 0,
                helpers: {
                    overlay: {
                        locked: false
                    }
                },
                tpl: {
                    closeBtn: '<div title="Close" class="fancybox-item fancybox-close"><i class="icon close-btn"></i></div>'
                },
                wrapCSS: 'gallery-wr',
                beforeShow: function () {
                    let index = this.element.closest('.owl-item').index();
                    owlGlry.to(index != -1 ? index : 0);
                }
            });
        } else {
            $this.on('click', function (e) {
                e.preventDefault();
            })
        }
    });

    $('.popup_js').fancybox({
        helpers: {
            overlay: {
                locked: false
            }
        }
    });

    //PRODUCT SLIDER WITH PREVIEW END//


    $(window).scroll(function () {
        var scroll = $(window).scrollTop();


        if (scroll > 600) {
            $('.to-top').fadeIn();
        } else {
            $('.to-top').fadeOut();
        }


    });


    $('.change-passw-btn').on('click', function (e) {
        e.preventDefault();
        $('.passw-change').slideToggle();
    });

    $('.remove-info-btn').on('click', function (e) {
        e.preventDefault();
        $(this).closest('.item').find('.tooltip').addClass('active');
        $(this).closest('.item').find('.total-cell').addClass('active');
    });


    //PRODUCT SIZE TABLE START//
    $(".size-table table").find("td:not(:first)").mouseover(function () {
        var E = $(this).parent();
        var D = E.parent();
        var C = E.find("td").index(this);
        var F = D.parent().find("tr").index(E);
        D.find("th, td").stop(true, true).removeClass("lightHor").removeClass("lightVer").removeClass("selectedCell");
        if (C > 0) {
            $(this).stop(true, true).addClass("selectedCell");
            D.find("tr td:nth-child(" + (C + 1) + "), tr th:nth-child(" + (C + 1) + ")").stop(true, true).addClass("lightVer");
            E.find("td").stop(true, true).addClass("lightHor")
        }
    });
    //PRODUCT SIZE TABLE END//

    $('.thumb-prev').on('click', function (e) {
        e.preventDefault();
        $('.product-popup .owl-prev').trigger('click');
    });
    $('.thumb-next').on('click', function (e) {
        e.preventDefault();
        $('.product-popup .owl-next').trigger('click');
    });


    //ACORDION START//
    var AccControl = $('.accordion .item-title a');
    var AccDrop = $('.item-drop');

    AccControl.on('click', function (e) {
        var el = $(this);
        e.preventDefault();
        $('.accordion .item').removeClass('active');
        AccDrop.slideUp();
        if (!el.closest('.item').hasClass('active')) {
            el.closest('.item').addClass('active').find(AccDrop).slideDown(function () {
                $('.accordion .item').removeClass('opened');

            });

        }
    });
    $('.add-comment').on('click', function (e) {
        e.preventDefault();
        $(this).closest('.inp').find('.inp-drop').slideToggle();
    });
    //ACORDION END//

    $(".js_delivery_radio").on("change", function () {
        $('.inp-i').removeClass('active');
        $(this).closest('.inp-i').addClass('active');
    });

    $('.main-img__i a').on('click', function (e) {
        e.preventDefault();

        $('.main-img-slider .owl-item:first-child a').trigger('click')
    });

    $('.sizes select').each(function () {
        var data = {}, $this = $(this);
        $this.find('option').each(function () {
            data[$(this).attr('value')] = $(this).data();
        });
        $this.data('options', data);
    });
    $('.sizes select').selectize({
        onFocus: function () {
            var input = 'selectize-input input',
                wrapper = 'selectize-input';
            $('.' + input).attr('readonly', true);
            $('.' + input + ', .' + wrapper).css('cursor', 'pointer');
        },
        onChange: function () {
        }
    });

    $('.product-list .btn_buy').on('click', function (e) {
    });

    $('.scroll-to').on('click', function (e) {
        var id = $(this).attr('href');
        e.preventDefault();
        $.scrollTo(id, 1000, {offset: -60});
    });

    $('.my-orders .order-num').on('click', function (e) {
        e.preventDefault();

        //$('.my-orders__i > .item.active .item-drop').slideUp();
        //$('.my-orders__i > .item.active').removeClass('active');
        if ($(this).closest('.item').hasClass('active')) {
            $(this).closest('.item').removeClass('active');
            $(this).closest('.item').find('.item-drop').slideUp();
        } else {
            $(this).closest('.item').addClass('active');
            $(this).closest('.item').find('.item-drop').slideDown(function () {
                $('.my-orders .active .item-drop .col').css('height', 'auto');
                equalHeight($('.my-orders  .active .item-drop .col'), 2);
            });
        }
    });
    //$('.amount-select input').focus(function () {
    //	$(this).closest('.inp').toggleClass('dropActive');
    //});
    initAmountSelects();
});

$(window).on('resize load', function () {


    $('.promo .item').css('height', 'auto');
    equalHeight($('.promo .item'));

    equalHeight($('.my-orders .item-drop .col'), 2);


    $('.slider__i .wrap').height($('.slider__i').height());
}).resize();

if ($('#map').length) {

    function initialize() {
        var isDraggable = $(document).width() > 480 ? true : false;
        var myLatlng = new google.maps.LatLng(49.1926136, 26.8220925, 10);
        var locations = [
            [49.1926136, 26.8220925]
        ];

        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,
            center: myLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP,

            mapTypeControl: false,
            scaleControl: false,
            draggable: isDraggable
        });

        var marker = [], i, temp;
        var imageStar = 'img/icons/map-marker.png';
        for (i = 0; i < locations.length; i++) {
            temp = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][0], locations[i][1]),
                map: map,
                icon: imageStar,
                number: i
            });

            marker.push(temp);
        }
    }

    google.maps.event.addDomListener(window, 'load', initialize);
}

/**
 * Wish list control class
 */
var WishList = (function () {
    function WishList() {
        var self = this;
        this.$counters = $('.js_wish_counter');

        $(document).on('click', '.js_wish_button', function (e) {
            self.toggle($(this));
            e.stopPropagation();
            e.preventDefault();
        });
    }

    WishList.prototype.toggle = function ($item) {
        // Return when ajax request is pending for this item
        if ($item.data('processing')) return;

        $item.data('processing', true);

        $.ajax({
            url: Routing.generate('wishlist_toggle'),
            data: {model_id: $item.data('id')},
            dataType: "json",
            type: "POST",
            success: function (data) {
                this.$counters.html(data.count);
                $item.toggleClass('active');
                if ($item.data('auto-remove') && !$item.hasClass('active')) {
                    $item.parents('.js_item').remove();
                    if($('.js_item').length == 0) {
                        $(document).trigger('content.goBlank');
                    }
                }
            }.bind(this),
            error: function (message) {
                alert(message.status + ' ' + message.statusText);
            },
            complete: function () {
                $item.data('processing', false);
            }
        });
    };

    return WishList;
})();

/**
 * Add to cart buttons control base class
 */
var CartButtons = (function () {
    function CartButtons() {
        var self = this;

        $(document).on('submit', '.js_model_form', function () {
            self.submitForm($(this));
            return false;
        });

    }

    CartButtons.prototype.submitForm = function ($form) {
        $.ajax({
            url: $form.attr('action'),
            data: $form.serialize(),
            dataType: "json",
            type: "POST",
            success: this.successSubmit.bind(this, $form.closest('.js_item')),
            error: this.errorSubmit.bind(this, $form.closest('.js_item'))
        });
    };

    CartButtons.prototype.successSubmit = function ($item, data) {
        $item.find('.not-added-part').fadeOut(300).delay(3600).fadeIn(300);
        $item.find('.added-part').delay(300).fadeIn(300).delay(3000).fadeOut(300);

        this.updatePrices(data);
    };

    CartButtons.prototype.updatePrices = function (data) {
        $('.js_cart_total_count').text(data.totalCount);
        $('.js_cart_total_discounted_price').text(data.discountedTotalPrice);

        // todo reset sizes select
    };

    CartButtons.prototype.errorSubmit = function ($item, data) {
        alert(message.status + ' ' + message.statusText);
    };

    CartButtons.prototype.canAddIncart = function ($button) {
        var $item = $button.closest('.js_item');
        if($item.data('preorder') && !userIsAuthenticated) {
            $.fancybox.open({href: '#preorder-login', type: 'inline'});
            return false;
        }
        return true;
    };

    CartButtons.prototype.changeFormAction = function ($select) {
        var $item = $select.closest('.js_item'),
            $selectedOption = $select.find('option:selected'), data;

        if($select.hasClass('selectized')) {
            data = $select.data('options')[$selectedOption.attr('value')];
        } else {
            data = $selectedOption.data();
        }

        $item.find('.js_product_price').hide();
        $item.find('.js_product_price[data-size-id=' + data.id + ']').show();

        $select.closest('.js_model_form').attr('action', Routing.generate('cart_add', {'id': $select.val()}));

        $item.data('preorder', data.preorderflag);
        if (data.preorderflag) {
            $item.find('.js_preorder_btn').css('display', 'block');
            $item.find('.js_buy_btn').hide();
        } else {
            $item.find('.js_preorder_btn').hide();
            $item.find('.js_buy_btn').css('display', 'block');
        }
    };

    return CartButtons;
})();

/**
 * Auto form submit on change
 */
var AjaxAutoForm = (function () {
    function AjaxAutoForm($form) {
        var self = this;
        this.$form = $form;
        this.$form.on('change', 'input, textarea, select', this.submitForm.bind(this));
        this.$form.on('click', '[type="submit"]', this.submitForm.bind(this));
        this.$form.on('submit', function () {
            self.submitForm();
            return false;
        });
    }

    AjaxAutoForm.prototype.submitForm = function () {
        $.ajax({
            url: this.$form.attr('action'),
            data: this.$form.serialize(),
            dataType: "json",
            type: "POST",
            success: this.successSubmit.bind(this),
            error: this.errorSubmit.bind(this)
        });
        return false;
    };

    AjaxAutoForm.prototype.successSubmit = function () {

    };

    AjaxAutoForm.prototype.errorSubmit = function (responce) {
        alert(responce.status + ' ' + responce.statusText);
    };

    return AjaxAutoForm;
})();

/**
 * Auto form submit on change
 */
var AjaxForm = (function () {
    function AjaxForm($form) {
        var self = this;
        this.$form = $form;
        this.$form.on('submit', function () {
            self.submitForm();
            return false;
        });
    }

    AjaxForm.prototype.submitForm = function () {
        $.ajax({
            url: this.$form.attr('action'),
            data: this.$form.serialize(),
            dataType: "json",
            type: "POST",
            success: this.successSubmit.bind(this),
            error: this.errorSubmit.bind(this)
        });
        return false;
    };

    AjaxForm.prototype.successSubmit = function (responce) {
        if(typeof responce.redirect != 'undefined') {
            window.location.href = responce.redirect;
        }
    };

    AjaxForm.prototype.errorSubmit = function (responce) {
        if(responce.status == 422) {
            if(responce.responseJSON.errors) {
                $.each(responce.responseJSON.errors, function (key, error) {
                    if(!parseInt(key)) {
                        this.$form.find('[name$=' + key + '], [name$="' + key + ']"]').closest('.inp').addClass('inp_error');
                    }
                }.bind(this));
            }
            msg.alert(responce.responseJSON.messages);
        }
    };

    return AjaxForm;
})();

/**
 * List add to cart buttons
 */
var LoginAjaxForm = (function(superClass) {
    extend(LoginAjaxForm, superClass);

    function LoginAjaxForm() {
        return LoginAjaxForm.__super__.constructor.apply(this, arguments);
    }

    LoginAjaxForm.prototype.successSubmit = function (responce) {
        var redirect = this.$form.data('redirect') || responce.redirect;
        if(redirect) {
            window.location.href = redirect;
        }
    };

    return LoginAjaxForm;
})(AjaxForm);

/**
 * Add to cart buttons control base class
 */
var QuickOrder = (function () {
    function QuickOrder() {
        $('.js_fast_buy form').on('submit', this.tryToSubmitForm.bind(this));
    }

    QuickOrder.prototype.tryToSubmitForm = function (e) {
        var $form = $(e.target);

        if($form.data('preorder') && !userIsAuthenticated) {
            $.fancybox.open({href: '#preorder-login', type: 'inline'});
        } else {
            this.submitForm($form);
        }
        e.preventDefault();
    };

    QuickOrder.prototype.submitForm = function ($form) {
        $.ajax({
            url: $form.attr('action'),
            data: $form.serialize(),
            dataType: "json",
            type: "POST",
            success: this.successSubmit.bind(this, $form),
            error: this.errorSubmit.bind(this)
        });
    };

    QuickOrder.prototype.successSubmit = function ($form) {
        $.fancybox.open({href: '#tnx', type: 'inline'});
        $form.find('[id$=_phone]').val('').trigger('change');
    };

    QuickOrder.prototype.errorSubmit = function (message) {
        alert(message.status + ' ' + message.statusText);
    };

    return QuickOrder;
})();

var Msg = function() {
    this.$holder = $('#msg_popup');
    this.$holder.on('click', '.close', (function(_this) {
        return function(e) {
            _this.hide($(e.target).parent('.alert'));
            return false;
        };
    })(this));
    this.alert = function(messages) {
        this.$holder.find('.js_message').html('')
        $.each(messages, function (key, text) {
            this.$holder.find('.js_message').append($('<p>').text(text));
        }.bind(this));
        $.fancybox.open({href: '#msg_popup', type: 'inline'});
    };
    this.showFlashMessages = function () {
        var $messages = $('.js_flash_message'),
            messages = $messages.map(function () {
                return $(this).html();
            });
        $messages.remove();
        if (messages.length) {
            this.alert(messages);
        }
    };
};

var msg;

$(window).ready(function () {
    new WishList();
    $('.login-form').each(function () {
        new LoginAjaxForm($(this));
    });
    new QuickOrder();
    msg = new Msg;

    $(document).on('click', '.js_click_submit', function(e) {
        e.preventDefault();
        $(this).closest('form').submit();
    });
});

$(window).load(function () {
    msg.showFlashMessages();
});

$(document).on('content.goBlank', function () {
    window.location.reload();
});