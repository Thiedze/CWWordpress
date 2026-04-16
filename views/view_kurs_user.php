<?php
/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 13.10.2016
 * Time: 19:09
 */

function tkurs(){
    global $wpdb;
    $kurse = get_all_kurse(true);

    echo '
        <div class="wrap">
		    <h1>Kurse
		    <a class="page-title-action" href="' . menu_page_url( 'tkurs', false ) . '&action=export&format=xlsx">Als XLSX exportieren</a>
		    <a class="page-title-action" href="' . menu_page_url( 'tkurs', false ) . '&action=export&format=csv">Als CSV exportieren</a>
		    <button id="cw-history-btn" class="page-title-action">Historie</button>
		    </h1>
    ';

    $kursleiter_map = [];
    $kl_rows = $wpdb->get_results(
        "SELECT ku.kurs_id FROM {$wpdb->prefix}cw_user_kurs ku
         JOIN {$wpdb->prefix}cw_user u ON ku.user_id = u.id
         WHERE u.is_course_leader = 1"
    );
    foreach ($kl_rows as $row) {
        $kursleiter_map[$row->kurs_id] = true;
    }

    foreach($kurse as $kurs){
        $no_leader_badge = $kurs->getNeedsCourseLeader() && empty($kursleiter_map[$kurs->getId()])
            ? ' <span style="color:#b00;font-weight:bold" title="Keine Kursleiter:in zugewiesen">&#9888; Keine Kursleiter:in</span>'
            : '';
        echo '
            <div>
            <h3>'.$kurs->getName().$no_leader_badge.'&nbsp;&nbsp;( '.$kurs->getTeilnehmer().'/'.$kurs->getMaxTeilnehmer().' )</h3>
            <table id="sorttable_'.$kurs->getId().'" class="widefat striped sortable display">
				<thead>
					<tr>
					    <th style="border-right: 1px solid #eaeaea;width: auto" data-orderable="false">
							&nbsp;
						</th>
						<th style="border-right: 1px solid #eaeaea;width: 20%">
							<a>Name</a>
						</th>
						<th style="border-right: 1px solid #eaeaea;width: 5%">
							<a>Kursleiter:in</a>
						</th>
						<th style="border-right: 1px solid #eaeaea;width: 20%">
							<a>EMail</a>
						</th>
						<th style="border-right: 1px solid #eaeaea;width: 5%">
							<a>Alter</a>
						</th>
						<th style="border-right: 1px solid #eaeaea;width: 20%">
							<a>(Hoch-)Schule/Arbeitsstätte</a>
						</th>
						<th style="border-right: 1px solid #eaeaea;width: 20%">
							<a>Sonstiges</a>
						</th>	
						<th style="border-right: 1px solid #eaeaea;width: 20%">
							<a>Registrierdatum</a>
						</th>
					</tr>
				</thead>
				<tbody>
        ';

        $teilnehmer = get_all_teilnehmer_by_kurs($kurs->getId());

        if($teilnehmer != null){
            foreach ($teilnehmer as $teil){
                $alter = calc_age($teil->getGeb());
                echo '
                    <tr>
                            <td>';
                                if($teil->getPayed() == 1){
                                echo '<img src="'.plugin_dir_url(__FILE__).'../img/money.png'.'"/>';
                            }else{
                                echo '<img src="'.plugin_dir_url(__FILE__).'../img/money.png'.'" style="-webkit-filter: grayscale(100%); filter: grayscale(100%);"/>';
                            }

                       echo'</td>
                            <td style="border-right: 1px solid #eaeaea">
                                ' . $teil->getVorname() . '&nbsp;' . $teil->getNachname() . '
                            </td>
                            <td style="border-right: 1px solid #eaeaea">
                                ' . ($teil->getIsCourseLeader() ? '<span style="color:#6a6">Ja</span>' : 'Nein') . '
                            </td>
                            <td style="border-right: 1px solid #eaeaea">
                                ' . $teil->getEmail() . '
                            </td>
                            <td data-order="'.$alter.'" style="border-right: 1px solid #eaeaea">
                                ' . $alter . '
                            </td>
                            <td style="border-right: 1px solid #eaeaea">
                                ' . $teil->getSchule() . '
                            </td>
                            <td style="border-right: 1px solid #eaeaea">
                                ' . $teil->getSonstiges() . '
                            </td>
                            <td data-order="'.strtotime($teil->getRegdate()).'">
                                '.date("d.m.Y H:i:s",strtotime($teil->getRegdate())).'
                            </td>
                                                    
                        </tr>
                ';
            }
        }else{
            echo'<tr>
                    <td>Keine Teilnehmer:innen</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
					<td>&nbsp;</td>
                 </tr>';
            
        }

        echo '</tbody>
            </table>';

    }

}

?>