/*!
 * jQuery confirmExit plugin
 * https://github.com/dunglas/jquery.confirmExit
 *
 * Copyright 2012 Kévin Dunglas <dunglas@gmail.com>
 * Released under the MIT license
 * http://www.opensource.org/licenses/mit-license.php
 */
(function ($) {
    function serializeWithoutExtraFields($form) {
        let url = $.url($form.attr('action')),
            uniquid = url.param('uniqid');
        if (uniquid) {
            let $clone = $form.clone();
            ['textarea', 'input', 'select'].forEach(function (selector) {
                $clone.find(selector + ':not([name^=' + uniquid + '])').remove();
            });
            $clone.find('select').each(function () {
                let $this = $(this);
                $this.val($form.find('select[name="' + $this.attr('name') + '"]').val());
            });
            return $clone.serialize();
        }

        return $form.serialize();
    }

    $.fn.confirmExit = function () {
        let $this = $(this);

        $this.attr('data-original', serializeWithoutExtraFields($this));

        $this.on('submit', function () {
            $this.removeAttr('data-original');
        });

        return $this;
    };

    $(window).on('beforeunload', function (event) {
        var e = event || window.event,
            message = window.SONATA_TRANSLATIONS.CONFIRM_EXIT,
            changes = false
            ;

        $('form[data-original]').each(function () {
            if ($(this).attr('data-original') !== serializeWithoutExtraFields($(this))) {
                changes = true;

                return;
            }
        });

        if (changes) {
            // For old IE and Firefox
            if (e) {
                e.returnValue = message;
            }

            return message;
        }
    });
})(jQuery);
