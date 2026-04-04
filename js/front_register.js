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

    function checkSubmit() {
        var valid = true;

        // Alterscheck
        var d = parseInt(jQuery('select[name="gbd"]').val());
        var m = parseInt(jQuery('select[name="gbm"]').val()) - 1;
        var y = parseInt(jQuery('select[name="gby"]').val());
        if (d && y) {
            var cwStart  = new Date(jQuery('#cw-start').val());
            var ref      = isNaN(cwStart.getTime()) ? new Date() : cwStart;
            var birthday = new Date(y, m, d);
            var age      = ref.getFullYear() - birthday.getFullYear();
            var monthDiff = ref.getMonth() - birthday.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && ref.getDate() < birthday.getDate())) {
                age--;
            }
            if (age < 16) {
                jQuery('#age-warning').show();
                valid = false;
            } else {
                jQuery('#age-warning').hide();
            }
            if (age >= 16 && age < 18) {
                jQuery('#check2-container').show();
                jQuery('input[name="check2"]').attr('required', 'required');
            } else {
                jQuery('#check2-container').hide();
                jQuery('input[name="check2"]').removeAttr('required').prop('checked', false);
            }
        }

        // Paytype-Check
        if (!jQuery('input[name="paytype"]:checked').length) {
            jQuery('#paytype-warning').show();
            valid = false;
        } else {
            jQuery('#paytype-warning').hide();
        }

        jQuery('#btn-registerme').prop('disabled', !valid).css('opacity', valid ? '1' : '0.4');
    }

    jQuery('select[name="gbd"], select[name="gbm"], select[name="gby"]').on('change', checkSubmit);
    jQuery('input[name="paytype"]').on('change', checkSubmit);
    checkSubmit();

});