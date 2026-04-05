/**
 * Created by JoetheJunkie on 20.10.2016.
 */

var $module;
var $errdialog;

jQuery(document).ready(function(){

    var table = jQuery('#sorttable').DataTable({
        autoWidth: false,
        responsive: false,
        order: [[15, 'desc']],
        destroy: true,
        lengthChange: false,
        paging: false,
        info: false,
        searching: false
    });

    var resizeTimer;
    jQuery(window).on('resize', function(){
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function(){
            jQuery('#sorttable').css('width', '100%');
            table.columns.adjust().draw(false);
        }, 100);
    });

    $module = jQuery('<div id="modal" style="width:100%;font-size:10pt;overflow:auto">ROFL</div>').dialog({
        autoOpen: false,
        width: Math.min(jQuery(window).width() * 0.95, 800),
        height: Math.min(jQuery(window).height() * 0.9, 800),
        position: { my: "center center", at: "center center", of: window },
        modal: true,
        title: "Benutzer:in bearbeiten",
        buttons: {
            'Speichern' : function () {

                jQuery.post(
                    ajax_object.ajaxurl,
                    jQuery('*[id^="reg_"]').serialize()+'&action=ajax_action&nonce='+encodeURIComponent(ajax_object.nonce)+'&'+jQuery('input[type="radio"]:checked').serialize()
                    , function(data){

                        $loading.dialog('close');

                        if(data == "ok"){
                            location.reload(true);
                        }else{
                            jQuery('#modalerr').html('<span style="color: #900">Fehler beim Speichern</span>');
                            $errdialog.dialog('open');
                        }
                    }
                )

            },
            'Abbrechen' : function () {
                jQuery(this).dialog('close')
            }
        }
    });

    $errdialog = jQuery('<div id="modalerr" style="font-size: 10pt;">ROFL</div>').dialog({
        autoOpen: false,
        width: "auto",
        height: "auto",
        modal: true,
        title: "Fehler",
        buttons: {
            'Schließen' : function () {
                jQuery(this).dialog('close')
            }
        }
    });

    $loading = jQuery('<div id="loading" style="font-size: 10pt;text-align: center">Daten werden geladen<br /><img src="../wp-content/plugins/campuswoche/img/loading.gif" /></div>').dialog({
        autoOpen: false,
        width: "auto",
        height: "auto",
        modal: true,
        title: "Bitte warten..."
    });

    jQuery('button[name="teil_delete"]').click(function(){

        var ret = confirm("Die Benutzer:in \""+jQuery(this).attr("data-name")+"\" wirklich löschen?");

        if(ret == true){
            jQuery.post(
                ajax_object.ajaxurl,
                {
                    'action': 'ajax_action',
                    'nonce': ajax_object.nonce,
                    'value': jQuery(this).attr("data-id"),
                    'do':'delete'
                }, function(data){
                    if(data == "no"){
                        jQuery('#modalerr').html('<span style="color: #900">Fehler beim Löschen</span>');
                        $errdialog.dialog('open');
                    }else {
                        location.reload(true);
                    }
                }
            )
        }else{

        }
    });

    jQuery('button[name="edit_teil"]').click(function(){

        $loading.dialog('open');

        jQuery.post(
            ajax_object.ajaxurl,
            {
                'action': 'ajax_action',
                'nonce': ajax_object.nonce,
                'value': jQuery(this).attr("data-id")
            }, function(data){

                $loading.dialog('close');

                if(data.length == 0){
                    jQuery('#modalerr').html('<span style="color: #900">ID kann nicht gefunden werden</span>');
                    $errdialog.dialog('open');
                }else{
                    jQuery('#modal').html(data);
                    $module.dialog('open');
                    jQuery('#modal').scrollTop(0);
                }
            }
        )

    });

});