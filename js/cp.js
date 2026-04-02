/**
 * Created by JoetheJunkie on 24.01.2017.
 */
(function( $ ) {

    // Add Color Picker to all inputs that have 'color-field' class
    $(function() {
        $('.color-field').wpColorPicker({
            palettes: ['#D7E7A1','#BBDAFF','#FFFF84','#FFA8A8','#CEA8F4','#FFD062']
        });
    });

})( jQuery );
