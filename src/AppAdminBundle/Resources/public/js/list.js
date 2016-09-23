;(function($) {
   $.fn.fixMe = function() {
      return this.each(function() {
         var $this = $(this),
            $t_fixed;
         function init() {
            $this.wrap('<div class="tablewrap" />');
            $t_fixed = $this.clone();
            $t_fixed.find("tbody").remove().end().addClass("tfixed").insertBefore($this).wrap('<div class="tablewrap-head" />');
            resizeFixed();
         }
         function resizeFixed() {
            $t_fixed.find("th").each(function(index) {
               $(this).css("width",$this.find("th").eq(index).outerWidth()+"px");
            });
         }
         $(window).resize(resizeFixed);
         init();
      });
   };
})(jQuery); 

$(document).ready(function () {
    $('.js-filters-toggle').on('click', function () {
        $('#filters').toggleClass('hidden')
    });
    if ($('.table-import').length>0) {
        $(".table-import").fixMe();
        $('.sonata-ba-content').attr('style','');
        $('.tablewrap').height($(window).height()-$('.tablewrap').offset().top);
        $('.tablewrap').closest('.content').css('padding-bottom',0);    
        $('.tablewrap').scroll(function(){
            var offset = $(this).scrollTop();
            $('.tablewrap-head').css('top',offset);
        });
    }
});
