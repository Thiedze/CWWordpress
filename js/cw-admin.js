/**
 * Created by JoetheJunkie on 11.10.2016.
 */

jQuery("document").ready(function(){
    jQuery("div[data-type='switch']").click(function(){
        if(jQuery("#"+jQuery(this).attr("data")).attr("checked") == "checked"){
            //jQuery(this).css({left: 1});
            jQuery(this).parent().switchClass("sw_on","sw_off",1,"linear");
            jQuery("#"+jQuery(this).attr("data")).removeAttr("checked");
        }else{
            //jQuery(this).css({left: 16});
            jQuery(this).parent().switchClass("sw_off","sw_on",1,"linear");
            jQuery("#"+jQuery(this).attr("data")).attr({checked:"checked"});
        }
    });

    jQuery("#cw_start").datepicker({
        altField: "#tp_cw_start",
        altFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        dateFormat: "dd.mm.yy"
    });

    jQuery("#dp").click(function(){
        jQuery("#cw_start").datepicker("show");
    });

    jQuery("#reg_start").datepicker({
        altField: "#tp_reg_start",
        altFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        dateFormat: "dd.mm.yy"
    });

    jQuery("#dp_reg").click(function(){
        jQuery("#reg_start").datepicker("show");
    });

    jQuery("td[datatype='tooltip']").tooltip({
        position: {my: "center top-30", at:"center top", collision: "flipfit"}
    });

    jQuery("input[data='number']").spinner();

    jQuery(".ed").click(function(){
       var v = jQuery('#mailtext').val();
       var vpos = jQuery('#mailtext').getCursorPosition();
       var inp = jQuery(this).html();

       var tbefore = v.substring(0,vpos);
       var tafter = v.substring(vpos,v.length);

       jQuery('#mailtext').val(tbefore + inp + tafter);

    });



    (function ($, undefined) {
        $.fn.getCursorPosition = function() {
            var el = $(this).get(0);
            var pos = 0;
            if('selectionStart' in el) {
                pos = el.selectionStart;
            } else if('selection' in document) {
                el.focus();
                var Sel = document.selection.createRange();
                var SelLength = document.selection.createRange().text.length;
                Sel.moveStart('character', -el.value.length);
                pos = Sel.text.length - SelLength;
            }
            return pos;
        }
    })(jQuery);

});
