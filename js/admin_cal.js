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

    // Integer-Zeit (z.B. 830) → Slot-Index ab 07:00
    function timeToSlot(t) {
        var h = Math.floor(t / 100);
        var m = t % 100;
        return (h * 60 + m - 420) / 30;
    }

    // Slot-Index → Integer-Zeit (z.B. slot 3 → 830)
    function slotToTime(s) {
        var total = 420 + s * 30;
        var h = Math.floor(total / 60);
        var m = total % 60;
        return h * 100 + m;
    }

    // Integer-Zeit → Anzeigestring "HH:MM"
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
       AJAX: Neues Event anlegen
       ================================================================ */
    function createEvent(day, start, end, name, color) {
        $.post(cwCal.ajax, {
            action : 'cw_cal_create',
            nonce  : cwCal.nonce,
            day    : day,
            start  : start,
            end    : end,
            name   : name,
            color  : color
        }, function (res) {
            if (res.success) {
                location.reload();
            }
        });
    }

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

                // Neue Position berechnen
                var day  = Math.round((ui.position.left - T) / C);
                var slot = Math.round(ui.position.top / S);
                day  = Math.max(0, Math.min(5, day));
                slot = Math.max(0, Math.min(31 - dur, slot));

                var newStart = slotToTime(slot);
                var newEnd   = slotToTime(slot + dur);

                // Auf Grid einrasten
                $self.css({
                    left : (T + day * C + 2) + 'px',
                    top  : (slot * S + 1) + 'px'
                });

                // Data-Attribute + Zeitanzeige aktualisieren
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

                // Höhe exakt einrasten
                $self.css({ height: (slots * S - 2) + 'px', width: (C - 4) + 'px' });
                $self.data('end', newEnd);
                $self.find('.cw-event-time').text(fmtTime(start) + ' – ' + fmtTime(newEnd));

                saveMove(id, day, start, newEnd);
            }
        });

        // jQuery UI Resizable setzt intern position:relative – das überschreiben wir zurück
        $ev.css('position', 'absolute');
    }

    // Alle vorhandenen Events initialisieren
    $('.cw-event').each(function () {
        initDraggable($(this));
        initResizable($(this));
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
       Klick auf leere Spalte → neues Event Dialog öffnen
       ================================================================ */
    $(document).on('click', '.cw-col', function (e) {
        if ($(e.target).closest('.cw-event').length) return;

        var $col  = $(this);
        var day   = parseInt($col.data('day'));
        var relY  = e.pageY - $('#cw-cal-body').offset().top;
        var slot  = Math.floor(relY / S);
        slot = Math.max(0, Math.min(30, slot));   // max slot 30 → 22:00

        $('#cw-new-day').val(day);
        $('#cw-new-start').val(slotToTime(slot));
        $('#cw-new-end').val(slotToTime(slot + 2)); // Standard: 1 Stunde
        $('#cw-new-name').val('');

        $('#cw-new-dlg').dialog('open');
    });

    /* ================================================================
       Dialog: Neues Event
       ================================================================ */
    $('#cw-new-dlg').dialog({
        autoOpen  : false,
        modal     : true,
        width     : 340,
        resizable : false,
        open: function () {
            $('#cw-new-name').focus();
        },
        buttons: {
            'Erstellen': function () {
                var name = $.trim($('#cw-new-name').val());
                if (!name) {
                    $('#cw-new-name').css('border-color', '#b00').focus();
                    return;
                }
                $('#cw-new-name').css('border-color', '');

                createEvent(
                    parseInt($('#cw-new-day').val()),
                    parseInt($('#cw-new-start').val()),
                    parseInt($('#cw-new-end').val()),
                    name,
                    $('#cw-new-color').val() || '#d7e7a1'
                );
                $(this).dialog('close');
            },
            'Abbrechen': function () {
                $(this).dialog('close');
            }
        }
    });

    // Enter im Titel-Feld = Erstellen
    $('#cw-new-name').on('keydown', function (e) {
        if (e.key === 'Enter') {
            $('#cw-new-dlg').closest('.ui-dialog').find('button:first').click();
        }
    });

});
