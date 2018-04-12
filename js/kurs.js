/**
 * Created by JoetheJunkie on 11.10.2016.
 */
jQuery(document).ready(function($){
    var custom_uploader;

    jQuery('#mteil').spinner();

    jQuery('#upload_image').on("invalid", function (e) {
        e.preventDefault();
    });

    jQuery('#kurstable').DataTable({
        "paging":   false,
        "info":     false,
        "searching": false
    });

    jQuery('#tshirttable').DataTable({
        "paging":   false,
        "info":     false,
        "searching": false
    });

    jQuery('table[id^="sorttable"]').DataTable({
        "paging":   false,
        "info":     false,
        "searching": false
    });

    jQuery('#upload_image_button').click(function(e) {

        e . preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if ( custom_uploader ) {
            custom_uploader . open();

            return;
        }

        //Extend the wp.media object
        custom_uploader = wp . media . frames . file_frame = wp . media({
            allowLocalEdits: true,
            displaySettings: true,
            displayUserSettings: true,
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader . on( 'select', function () {
            console . log( custom_uploader . state() . get( 'selection').toJSON());
            attachment = custom_uploader . state() . get( 'selection').first() . toJSON();
            jQuery('#upload_image').val( attachment . url );
            jQuery('#preimg').attr({src:attachment . url});
        });

        //Open the uploader dialog
        custom_uploader . open();

    } );
} );

function invalid(){

    if(jQuery('html').scrollTop() == 0){
        jQuery('#upload_image_button').effect('highlight',{color:'#900'},300,function(){});
    }else {
        jQuery('body, html').animate({
            scrollTop: 0
        }, 1400, function () {
            jQuery('#upload_image_button').effect('highlight', {color: '#900'}, 300, function () {
            });
        });
    }

    return false;
}