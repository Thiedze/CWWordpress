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

    /* ----------------------------------------------------------------
       Historie-Dialog
       ---------------------------------------------------------------- */
    jQuery(document).on('click', '#cw-history-btn', function () {
        var $dialog = jQuery('#cw-history-dialog');

        if ($dialog.length === 0) {
            jQuery('body').append(
                '<div id="cw-history-dialog" title="&Auml;nderungshistorie (letzte 250)">' +
                '<div id="cw-history-content" style="overflow:auto;max-height:460px">' +
                '<p>Wird geladen&hellip;</p></div></div>'
            );
            $dialog = jQuery('#cw-history-dialog');
            $dialog.dialog({
                modal: true,
                width: Math.min(960, jQuery(window).width() - 40),
                buttons: {
                    'Schlie\u00dfen': function () { jQuery(this).dialog('close'); }
                }
            });
        } else {
            jQuery('#cw-history-content').html('<p>Wird geladen&hellip;</p>');
            $dialog.dialog('open');
        }

        jQuery.post(
            cw_history.ajaxurl,
            { action: 'cw_get_history', nonce: cw_history.nonce },
            function (response) {
                if (!response.success) {
                    jQuery('#cw-history-content').html('<p>Fehler beim Laden der Historie.</p>');
                    return;
                }
                var rows = response.data;
                if (rows.length === 0) {
                    jQuery('#cw-history-content').html('<p>Noch keine Eintr\u00e4ge vorhanden.</p>');
                    return;
                }
                var actionLabel = { create: 'Erstellt', update: 'Ge\u00e4ndert', delete: 'Gel\u00f6scht' };
                var entityLabel = { kurs: 'Kurs', teilnehmer: 'Teilnehmer:in' };
                var html = '<table class="widefat striped" style="width:100%;border-collapse:collapse">' +
                    '<thead><tr>' +
                    '<th style="white-space:nowrap">Zeitpunkt</th>' +
                    '<th>Aktion</th>' +
                    '<th>Typ</th>' +
                    '<th>Name</th>' +
                    '<th>Benutzer:in</th>' +
                    '<th>\u00c4nderungen</th>' +
                    '</tr></thead><tbody>';
                jQuery.each(rows, function (i, row) {
                    var al = actionLabel[row.action] || row.action;
                    var el = entityLabel[row.entity_type] || row.entity_type;
                    var actionStyle = row.action === 'delete' ? 'color:#b00;font-weight:bold' :
                                      row.action === 'create' ? 'color:#0a0;font-weight:bold' : '';
                    html += '<tr>' +
                        '<td style="white-space:nowrap">' + row.ts + '</td>' +
                        '<td><span style="' + actionStyle + '">' + al + '</span></td>' +
                        '<td>' + el + '</td>' +
                        '<td>' + row.entity_name + '</td>' +
                        '<td>' + row.user_name + '</td>' +
                        '<td style="font-size:0.9em;color:#555">' + (row.changes || '&ndash;') + '</td>' +
                        '</tr>';
                });
                html += '</tbody></table>';
                jQuery('#cw-history-content').html(html);
            }
        );
    });

});
