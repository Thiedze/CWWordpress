/**
 * Campuswoche – Admin-Kalender Drag & Drop
 */
jQuery(document).ready(function ($) {

    if ($('#cw-cal-body').length === 0) return;

    var S = cwCal.slotH;   // px pro 30-Minuten-Slot
    var C = cwCal.colW;    // px pro Tag-Spalte
    var T = cwCal.timeW;   // px Zeitbeschriftungs-Breite

    /* ================================================================
       Hilfsfunktionen Zeitkonvertierung
       ================================================================ */

    function timeToSlot(t) {
        var h = Math.floor(t / 100);
        var m = t % 100;
        return (h * 60 + m - 420) / 30;
    }

    function slotToTime(s) {
        var total = 420 + s * 30;
        var h = Math.floor(total / 60);
        var m = total % 60;
        return h * 100 + m;
    }

    function fmtTime(t) {
        var h = Math.floor(t / 100);
        var m = t % 100;
        return (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m;
    }

    /* ================================================================
       AJAX: Event verschieben / strecken
       ================================================================ */
    function saveMove(id, day, start, end) {
        $.post(cwCal.ajax, {
            action : 'cw_cal_move',
            nonce  : cwCal.nonce,
            id     : id,
            day    : day,
            start  : start,
            end    : end
        }).fail(function () {
            alert('Fehler beim Speichern – bitte Seite neu laden.');
        });
    }

    /* ================================================================
       AJAX: Event löschen
       ================================================================ */
    function deleteEvent(id, $el) {
        $.post(cwCal.ajax, {
            action : 'cw_cal_delete',
            nonce  : cwCal.nonce,
            id     : id
        }, function (res) {
            if (res.success) {
                $el.fadeOut(200, function () { $(this).remove(); });
            }
        });
    }

    /* ================================================================
       AJAX: Event speichern (neu + bearbeiten)
       ================================================================ */
    function saveEvent() {
        var eid   = parseInt($('#cw-dlg-eid').val()) || 0;
        var name  = $.trim($('#cw-dlg-name').val());

        if (!name) {
            $('#cw-dlg-name').css('border-color', '#b00').focus();
            showDlgError('Bitte einen Titel angeben.');
            return;
        }
        $('#cw-dlg-name').css('border-color', '');

        var start = parseInt($('#cw-dlg-start').val());
        var end   = parseInt($('#cw-dlg-end').val());
        if (end <= start) {
            showDlgError('Ende muss nach Start liegen.');
            return;
        }

        $.post(cwCal.ajax, {
            action             : 'cw_event_save',
            nonce              : cwCal.nonce,
            eid                : eid,
            event_day          : $('#cw-dlg-day').val(),
            event_start        : start,
            event_end          : end,
            event_name         : name,
            event_subtext      : $('#cw-dlg-subtext').val(),
            event_description  : $('#cw-dlg-desc').html(),
            event_color        : $('#cw-dlg-color').val() || '#d7e7a1'
        }, function (res) {
            if (res.success) {
                location.reload();
            } else {
                showDlgError(res.data || 'Fehler beim Speichern.');
            }
        });
    }

    function showDlgError(msg) {
        $('#cw-dlg-error p').text(msg);
        $('#cw-dlg-error').show();
    }

    /* ================================================================
       Dialog initialisieren
       ================================================================ */
    var $dlg = $('#cw-event-dlg').dialog({
        autoOpen  : false,
        modal     : true,
        width     : 520,
        resizable : false,
        open: function () {
            $('#cw-dlg-error').hide();
            $('#cw-dlg-name').focus();
        },
        buttons: {
            'Speichern': function () { saveEvent(); },
            'Abbrechen': function () { $(this).dialog('close'); }
        }
    });

    function openDlgNew(day, start, end) {
        $dlg.dialog('option', 'title', 'Neues Event erstellen');
        $('#cw-dlg-eid').val(0);
        $('#cw-dlg-day').val(day !== undefined ? day : 0);
        $('#cw-dlg-start').val(start !== undefined ? start : 800);
        $('#cw-dlg-end').val(end !== undefined ? end : 900);
        $('#cw-dlg-name').val('');
        $('#cw-dlg-subtext').val('');
        $('#cw-dlg-color').val('#d7e7a1');
        $('#cw-dlg-desc').html('');
        $dlg.dialog('open');
    }

    /* ================================================================
       "Neu erstellen"-Button
       ================================================================ */
    $('#cw-new-event-btn').on('click', function () {
        openDlgNew();
    });

    /* ================================================================
       Klick auf leere Spalte → neues Event Dialog öffnen
       ================================================================ */
    $(document).on('click', '.cw-col', function (e) {
        if ($(e.target).closest('.cw-event').length) return;

        var $col  = $(this);
        var day   = parseInt($col.data('day'));
        var relY  = e.pageY - $('#cw-cal-body').offset().top;
        var slot  = Math.floor(relY / S);
        slot = Math.max(0, Math.min(30, slot));

        openDlgNew(day, slotToTime(slot), slotToTime(slot + 2));
    });

    /* ================================================================
       Bearbeiten-Button auf Event
       ================================================================ */
    $(document).on('click', '.cw-btn-edit', function (e) {
        e.stopPropagation();
        var id = parseInt($(this).data('id'));
        $.post(cwCal.ajax, {
            action : 'cw_event_load',
            nonce  : cwCal.nonce,
            eid    : id
        }, function (res) {
            if (!res.success) return;
            var ev = res.data;
            $dlg.dialog('option', 'title', 'Event bearbeiten');
            $('#cw-dlg-eid').val(ev.id);
            $('#cw-dlg-day').val(ev.event_day);
            $('#cw-dlg-start').val(ev.event_start);
            $('#cw-dlg-end').val(ev.event_end);
            $('#cw-dlg-name').val(ev.event_name);
            $('#cw-dlg-subtext').val(ev.event_subtext);
            $('#cw-dlg-color').val(ev.event_color || '#d7e7a1');
            $('#cw-dlg-desc').html(ev.event_description);
            $dlg.dialog('open');
        });
    });

    /* ================================================================
       Löschen-Button
       ================================================================ */
    $(document).on('click', '.cw-btn-del', function (e) {
        e.stopPropagation();
        var $btn = $(this);
        if (!confirm('Event wirklich löschen?')) return;
        deleteEvent(parseInt($btn.data('id')), $btn.closest('.cw-event'));
    });

    /* ================================================================
       Draggable initialisieren
       ================================================================ */
    function initDraggable($ev) {
        $ev.draggable({
            containment : '#cw-cal-body',
            grid        : [C, S],
            zIndex      : 100,
            cancel      : '.cw-resize-handle, .cw-event-btns',
            start: function () {
                $(this).addClass('cw-dragging');
            },
            stop: function (e, ui) {
                $(this).removeClass('cw-dragging');
                var $self    = $(this);
                var id       = parseInt($self.data('id'));
                var oldStart = parseInt($self.data('start'));
                var oldEnd   = parseInt($self.data('end'));
                var dur      = timeToSlot(oldEnd) - timeToSlot(oldStart);

                var day  = Math.round((ui.position.left - T) / C);
                var slot = Math.round(ui.position.top / S);
                day  = Math.max(0, Math.min(5, day));
                slot = Math.max(0, Math.min(31 - dur, slot));

                var newStart = slotToTime(slot);
                var newEnd   = slotToTime(slot + dur);

                $self.css({
                    left : (T + day * C + 2) + 'px',
                    top  : (slot * S + 1) + 'px'
                });

                $self.data('day', day).data('start', newStart).data('end', newEnd);
                $self.find('.cw-event-time').text(fmtTime(newStart) + ' – ' + fmtTime(newEnd));

                saveMove(id, day, newStart, newEnd);
            }
        });
    }

    /* ================================================================
       Resizable initialisieren (nur untere Kante)
       ================================================================ */
    function initResizable($ev) {
        $ev.resizable({
            handles   : { s: '.cw-resize-handle' },
            grid      : [C, S],
            minHeight : S,
            containment: '#cw-cal-body',
            start: function () {
                $(this).addClass('cw-resizing');
            },
            stop: function (e, ui) {
                $(this).removeClass('cw-resizing');
                var $self = $(this);
                var id    = parseInt($self.data('id'));
                var start = parseInt($self.data('start'));
                var day   = parseInt($self.data('day'));

                var slots  = Math.max(1, Math.round(ui.size.height / S));
                var newEnd = slotToTime(timeToSlot(start) + slots);

                $self.css({ height: (slots * S - 2) + 'px', width: (C - 4) + 'px' });
                $self.data('end', newEnd);
                $self.find('.cw-event-time').text(fmtTime(start) + ' – ' + fmtTime(newEnd));

                saveMove(id, day, start, newEnd);
            }
        });

        $ev.css('position', 'absolute');
    }

    // Alle vorhandenen Events initialisieren
    $('.cw-event').each(function () {
        initDraggable($(this));
        initResizable($(this));
    });

});
