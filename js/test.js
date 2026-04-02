/**
 * Created by JoetheJunkie on 10.10.2016.
 */

jQuery("document").ready(function(){
    jQuery('#but').click(function(){
       jQuery.post(
           ajax_object.ajaxurl,
           {
               action: 'ajax_action',
               post_id: jQuery('#num').val()
           }, function(data){
               alert(data);
           }
       )
    });
});