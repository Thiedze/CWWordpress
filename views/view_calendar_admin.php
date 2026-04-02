<?php
/**
 * Admin-Kalender mit Drag & Drop
 */

function show_program_calendar() {
    global $wpdb;
    $events = get_all_events();
    $days   = Event::get_days();

    $SLOT_H  = 40;          // px pro 30-Minuten-Slot
    $COL_W   = 160;         // px pro Tag-Spalte
    $TIME_W  = 55;          // px für Zeitbeschriftung
    $SLOTS   = 32;          // 07:00–23:00 = 16h = 32 Halbstunden
    $TOTAL_H = $SLOTS * $SLOT_H;
    $TOTAL_W = $TIME_W + count( $days ) * $COL_W;

    $nonce = wp_create_nonce( 'cw_cal_nonce' );
    ?>

    <div id="cw-cal-outer">

        <p>
            <a href="<?php echo esc_url( menu_page_url( 'program', false ) . '&action=new' ); ?>"
               class="button button-primary">
                <span class="dashicons dashicons-plus" style="margin-top:3px"></span> Neu erstellen
            </a>
            <span style="margin-left:12px;color:#777;font-size:12px">
                Drag &amp; Drop zum Verschieben &nbsp;·&nbsp; Untere Kante zum Strecken &nbsp;·&nbsp; Klick auf freie Fläche = neues Event
            </span>
        </p>

        <!-- Tagesnamen-Kopfzeile -->
        <div id="cw-cal-head">
            <div style="display:inline-block;width:<?php echo $TIME_W; ?>px"></div>
            <?php foreach ( $days as $day ) : ?>
                <div class="cw-day-head" style="width:<?php echo $COL_W; ?>px">
                    <?php echo esc_html( $day ); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Scrollbarer Grid-Körper -->
        <div id="cw-cal-scroll">
            <div id="cw-cal-body"
                 style="width:<?php echo $TOTAL_W; ?>px;height:<?php echo $TOTAL_H; ?>px">

                <?php
                /* ---- Zeitbeschriftungen + Rasterlinien ---- */
                for ( $s = 0; $s < $SLOTS; $s++ ) :
                    $total_min = 420 + $s * 30;
                    $h         = (int) floor( $total_min / 60 );
                    $m         = $total_min % 60;
                    $top       = $s * $SLOT_H;
                    $is_hour   = ( $m === 0 );
                ?>
                    <div class="cw-time-label" style="top:<?php echo $top; ?>px">
                        <?php printf( '%02d:%02d', $h, $m ); ?>
                    </div>
                    <div class="cw-grid-line <?php echo $is_hour ? 'cw-hour' : 'cw-half'; ?>"
                         style="top:<?php echo $top; ?>px;left:<?php echo $TIME_W; ?>px;width:<?php echo $TOTAL_W - $TIME_W; ?>px">
                    </div>
                <?php endfor; ?>

                <?php
                /* ---- Tages-Spalten (Klickziele für neue Events) ---- */
                foreach ( $days as $i => $day_name ) :
                    $left = $TIME_W + $i * $COL_W;
                ?>
                    <div class="cw-col <?php echo $i % 2 === 0 ? 'cw-col-even' : ''; ?>"
                         data-day="<?php echo $i; ?>"
                         style="left:<?php echo $left; ?>px;width:<?php echo $COL_W; ?>px;height:<?php echo $TOTAL_H; ?>px">
                    </div>
                <?php endforeach; ?>

                <?php
                /* ---- Events ---- */
                if ( $events ) :
                    foreach ( $events as $event ) :
                        $sh = (int) floor( $event->getEventStart() / 100 );
                        $sm = $event->getEventStart() % 100;
                        $eh = (int) floor( $event->getEventEnd() / 100 );
                        $em = $event->getEventEnd() % 100;

                        $start_slot = ( $sh * 60 + $sm - 420 ) / 30;
                        $end_slot   = ( $eh * 60 + $em - 420 ) / 30;
                        $ev_top     = (int) $start_slot * $SLOT_H + 1;
                        $ev_height  = (int) ( $end_slot - $start_slot ) * $SLOT_H - 2;
                        $ev_left    = $TIME_W + $event->getEventDay() * $COL_W + 2;
                        $edit_url   = menu_page_url( 'program', false ) . '&action=edit&eid=' . $event->getId();
                ?>
                        <div class="cw-event"
                             data-id="<?php echo esc_attr( $event->getId() ); ?>"
                             data-day="<?php echo esc_attr( $event->getEventDay() ); ?>"
                             data-start="<?php echo esc_attr( $event->getEventStart() ); ?>"
                             data-end="<?php echo esc_attr( $event->getEventEnd() ); ?>"
                             style="top:<?php echo $ev_top; ?>px;
                                    left:<?php echo $ev_left; ?>px;
                                    height:<?php echo $ev_height; ?>px;
                                    width:<?php echo $COL_W - 4; ?>px;
                                    background:<?php echo esc_attr( $event->getEventColor() ); ?>">

                            <div class="cw-event-btns">
                                <a href="<?php echo esc_url( $edit_url ); ?>"
                                   class="dashicons dashicons-welcome-write-blog"
                                   title="Bearbeiten"></a>
                                <span class="cw-btn-del dashicons dashicons-trash"
                                      data-id="<?php echo esc_attr( $event->getId() ); ?>"
                                      title="Löschen"></span>
                            </div>

                            <div class="cw-event-time">
                                <?php echo esc_html( $event->getEventStart( true ) . ' – ' . $event->getEventEnd( true ) ); ?>
                            </div>
                            <div class="cw-event-name">
                                <?php echo esc_html( $event->getEventName() ); ?>
                            </div>
                            <?php if ( $event->getEventSubtext() ) : ?>
                                <div class="cw-event-sub">
                                    <?php echo esc_html( $event->getEventSubtext() ); ?>
                                </div>
                            <?php endif; ?>

                            <div class="cw-resize-handle"></div>
                        </div>

                    <?php endforeach;
                endif; ?>

            </div><!-- #cw-cal-body -->
        </div><!-- #cw-cal-scroll -->

    </div><!-- #cw-cal-outer -->

    <!-- Dialog: Neues Event erzeugen -->
    <div id="cw-new-dlg" style="display:none" title="Neues Event erstellen">
        <table class="form-table" style="margin:0">
            <tr>
                <th style="width:60px">Titel</th>
                <td><input type="text" id="cw-new-name" style="width:100%" placeholder="Pflichtfeld" /></td>
            </tr>
            <tr>
                <th>Farbe</th>
                <td><input type="color" id="cw-new-color" value="#d7e7a1" /></td>
            </tr>
        </table>
        <input type="hidden" id="cw-new-day" />
        <input type="hidden" id="cw-new-start" />
        <input type="hidden" id="cw-new-end" />
    </div>

    <!-- Konfiguration für admin_cal.js -->
    <script>
    var cwCal = {
        slotH : <?php echo $SLOT_H; ?>,
        colW  : <?php echo $COL_W; ?>,
        timeW : <?php echo $TIME_W; ?>,
        nonce : '<?php echo esc_js( $nonce ); ?>',
        ajax  : '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>'
    };
    </script>

    <?php
}
