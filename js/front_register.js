/**
 * Created by JoetheJunkie on 13.10.2016.
 */

jQuery(document).ready(function(){

    jQuery('input[name="food"]').change(function(e){
       if(jQuery(this).val() == "2"){
           jQuery('input[name="food_sonst"]').attr({required:"required"});
           jQuery('input[name="food_sonst"]').show();
       }else{
           jQuery('input[name="food_sonst"]').removeAttr("required");
           jQuery('input[name="food_sonst"]').hide();
       }
    });

    jQuery('input[name="gotit"]').change(function(e){
        if(jQuery(this).val() == "6"){
            jQuery('input[name="gotit_sonst"]').attr({required:"required"});
            jQuery('input[name="gotit_sonst"]').show();
        }else{
            jQuery('input[name="gotit_sonst"]').removeAttr("required");
            jQuery('input[name="gotit_sonst"]').hide();
        }
    });

    jQuery('input[name="paytype"]').change(function(e){
        console.log(jQuery(this).val());
        if(jQuery(this).val() == "1"){
            jQuery('#tnb').html(jQuery('#pt1').val());
            return;
        }
        if(jQuery(this).val() == "2"){
            jQuery('#tnb').html(jQuery('#pt2').val());
            return;
        }
        jQuery('#tnb').html("0");
    });

    if (jQuery('input[name="paytype"]:checked').val() == "1" || jQuery('input[name="paytype"]:checked').val() == "2"){
        jQuery('#tnb').html(jQuery('#pt'+jQuery('input[name="paytype"]:checked').val()).val());
    }else{
        jQuery('#tnb').html(0);
    }

});