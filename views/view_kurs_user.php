<?php
/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 13.10.2016
 * Time: 19:09
 */

function tkurs(){
    global $wpdb;
    $kurse = get_all_kurse();

    echo '
        <div class="wrap">
		    <h1>Kurse
		    <a class="page-title-action" id="export" href="' . menu_page_url( 'tkurs', false ) . '&action=export">Tabelle exportieren</a>
		    </h1>
    ';

    foreach($kurse as $kurs){
        echo '
            <div>
            <h3>'.$kurs->getName().'&nbsp;&nbsp;( '.$kurs->getTeilnehmer().'/'.$kurs->getMaxTeilnehmer().' )</h3>
            <table id="sorttable_'.$kurs->getId().'" class="widefat striped sortable display">
				<thead>
					<tr>
					    <th style="border-right: 1px solid #eaeaea;width: auto" data-orderable="false">
							&nbsp;
						</th>
						<th style="border-right: 1px solid #eaeaea;width: 20%">
							<a>Name</a>
						</th>
						<th style="border-right: 1px solid #eaeaea;width: 20%">
							<a>EMail</a>
						</th>
						<th style="border-right: 1px solid #eaeaea;width: 5%">
							<a>Alter</a>
						</th>
						<th style="border-right: 1px solid #eaeaea;width: 20%">
							<a>Schule</a>
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
                    <td>Keine Teilnehmer</td>
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