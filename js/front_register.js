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

});