/**
 * Kurse – Dialog-basierte Verwaltung
 */
jQuery(document).ready(function ($) {

    var custom_uploader_new, custom_uploader_edit;

    // ---- DataTables ----
    $('#kurstable').DataTable({
        paging:    false,
        info:      false,
        searching: false
    });

    $('#tshirttable').DataTable({
        paging:    false,
        info:      false,
        searching: false
    });

    // ---- Dialog: Neuer Kurs ----
    var $dialogNew = $('#cw-dialog-new').dialog({
        autoOpen: false,
        modal:    true,
        width:    640,
        buttons: {
            'Erstellen': function () { saveKurs('new'); },
            'Abbrechen': function () { $(this).dialog('close'); }
        }
    });

    $('#cw-new-btn').on('click', function (e) {
        e.preventDefault();
        $('#cw-new-error').hide();
        $('#new-name').val('');
        $('#new-mteil').val(0);
        $('#new-show-front').prop('checked', false);
        $('#new-is-open').prop('checked', false);
        $('#new-bild').val('');
        $('#new-preimg').attr('src', '').hide();
        $('#new-beschreibung').html('');
        $dialogNew.dialog('open');
    });

    // ---- Dialog: Kurs bearbeiten ----
    var $dialogEdit = $('#cw-dialog-edit').dialog({
        autoOpen: false,
        modal:    true,
        width:    640,
        buttons: {
            'Speichern': function () { saveKurs('edit'); },
            'Abbrechen': function () { $(this).dialog('close'); }
        }
    });

    $(document).on('click', '.cw-edit-btn', function () {
        var kid = $(this).data('kid');
        $('#cw-edit-error').hide();
        $.post(cw_kurs.ajaxurl, {
            action: 'cw_kurs_load',
            nonce:  cw_kurs.nonce,
            kid:    kid
        }, function (response) {
            if (!response.success) return;
            var k = response.data;
            $('#edit-kid').val(k.id);
            $('#edit-name').val(k.name);
            $('#edit-mteil').val(k.max_teilnehmer);
            $('#edit-show-front').prop('checked', k.show_front == 1);
            $('#edit-is-open').prop('checked', k.is_open == 1);
            $('#edit-bild').val(k.bild);
            $('#edit-preimg').attr('src', k.bild || '').toggle(!!k.bild);
            $('#edit-beschreibung').html(k.beschreibung);
            $dialogEdit.dialog('open');
        });
    });

    // ---- Speichern (Neu + Bearbeiten) ----
    function saveKurs(mode) {
        var p    = (mode === 'edit') ? 'edit' : 'new';
        var name = $('#' + p + '-name').val().trim();

        if (!name) {
            $('#cw-' + p + '-error p').text('Bitte einen Kursnamen angeben.');
            $('#cw-' + p + '-error').show();
            return;
        }

        $.post(cw_kurs.ajaxurl, {
            action:      'cw_kurs_save',
            nonce:       cw_kurs.nonce,
            kid:         (mode === 'edit') ? $('#edit-kid').val() : 0,
            name:        name,
            mteil:       $('#' + p + '-mteil').val(),
            show_front:  $('#' + p + '-show-front').is(':checked') ? 1 : '',
            is_open:     $('#' + p + '-is-open').is(':checked')    ? 1 : '',
            bild:        $('#' + p + '-bild').val(),
            beschreibung: $('#' + p + '-beschreibung').html()
        }, function (response) {
            if (response.success) {
                location.reload();
            } else {
                var msg = (response.data) ? response.data : 'Fehler beim Speichern.';
                $('#cw-' + p + '-error p').text(msg);
                $('#cw-' + p + '-error').show();
            }
        });
    }

    // ---- Löschen ----
    $(document).on('click', '.cw-delete-btn', function () {
        var kid  = $(this).data('kid');
        var name = $(this).data('name');
        if (!confirm('Den Kurs "' + name + '" wirklich löschen?')) return;
        $.post(cw_kurs.ajaxurl, {
            action: 'cw_kurs_delete',
            nonce:  cw_kurs.nonce,
            kid:    kid
        }, function (response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.data || 'Fehler beim Löschen.');
            }
        });
    });

    // ---- Bild-Upload: Neuer Kurs ----
    $('#new-bild-btn').on('click', function (e) {
        e.preventDefault();
        if (custom_uploader_new) { custom_uploader_new.open(); return; }
        custom_uploader_new = wp.media({ multiple: false });
        custom_uploader_new.on('select', function () {
            var attachment = custom_uploader_new.state().get('selection').first().toJSON();
            $('#new-bild').val(attachment.url);
            $('#new-preimg').attr('src', attachment.url).show();
        });
        custom_uploader_new.open();
    });

    // ---- Bild-Upload: Kurs bearbeiten ----
    $('#edit-bild-btn').on('click', function (e) {
        e.preventDefault();
        if (custom_uploader_edit) { custom_uploader_edit.open(); return; }
        custom_uploader_edit = wp.media({ multiple: false });
        custom_uploader_edit.on('select', function () {
            var attachment = custom_uploader_edit.state().get('selection').first().toJSON();
            $('#edit-bild').val(attachment.url);
            $('#edit-preimg').attr('src', attachment.url).show();
        });
        custom_uploader_edit.open();
    });

});

function invalid() {
    if (jQuery('html').scrollTop() == 0) {
        jQuery('#upload_image_button').effect('highlight', { color: '#900' }, 300, function () {});
    } else {
        jQuery('body, html').animate({ scrollTop: 0 }, 1400, function () {
            jQuery('#upload_image_button').effect('highlight', { color: '#900' }, 300, function () {});
        });
    }
    return false;
}
