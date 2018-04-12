<?php
/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 14.10.2016
 * Time: 13:24
 */


function teilnehmer(){
	$teilnehmer = get_all_teilnehmer();
	if($teilnehmer == null){

	}else{
		echo'
			<div class="wrap">
				<h1>Teilnehmer (aktuell '.count($teilnehmer).')
					<a class="page-title-action" id="export" href="' . menu_page_url( 'teilnehmer', false ) . '&action=export">Tabelle exportieren</a>
				</h1>
				
				<br />
				
				<table id="sorttable" class="widefat striped sortable display">
					<thead>
						<tr>
						    <th data-orderable="false">
								&nbsp;
							</th>
							<th data-orderable="false">
							    &nbsp;
							</th>
							<th>Nachname</th>
							<th>Vorname</th>
							<th>EMail</th>
							<th>Adresse</th>
							<th>PLZ/Ort</th>
							<th>Geburtsdatum</th>
							<th>(Hoch-)Schule</th>
							<th>Kurs</th>
							<th>Essen</th>
							<th>Sonstiges</th>
							<th>Shirt</th>
							<th>Registrierdatum</th>
							
						</tr>
					</thead>
					<tbody>';
		
				foreach ($teilnehmer as $t){
					echo'
						<tr>
						    <td>
								<button name="edit_teil" style="border: none; background: transparent;color: #0073aa" data-id="'.$t->getId().'">
										<i class="dashicons dashicons-welcome-write-blog"></i>
									</button>
								<button name="teil_delete" data-id="'.$t->getId().'" style="margin-left: 1px;border: none; background: transparent;color: #b00" data-name="' . $t->getNachname() .' '.$t->getVorname().'">
										<i class="dashicons dashicons-trash"></i>
								</button>
							</td>
							<td>';

                            if($t->getPayed() == 1){
                                echo '<img src="'.plugin_dir_url(__FILE__).'../img/money.png'.'"/>';
                            }else{
                                echo '<img src="'.plugin_dir_url(__FILE__).'../img/money.png'.'" style="-webkit-filter: grayscale(100%); filter: grayscale(100%);"/>';
                            }

                            if($t->getTshirt()->getName() != "") {
                                if ($t->getShirtPayed() == 1) {
                                    echo '<img src="' . plugin_dir_url(__FILE__) . '../img/shirt.png' . '"/>';
                                } else {
                                    echo '<img src="' . plugin_dir_url(__FILE__) . '../img/shirt.png' . '" style="-webkit-filter: grayscale(100%); filter: grayscale(100%);"/>';
                                }
                            }

					echo'   </td>
							<td>'.$t->getNachname().'</td>
							<td>'.$t->getVorname().'</td>
							<td>'.$t->getEmail().'</td>
							<td>'.$t->getStr().'</td>
							<td>'.$t->getPlz().' '.$t->getOrt().'</td>
							<td data-order="'.strtotime($t->getGeb()).'">'.date("d.m.Y",strtotime($t->getGeb())).'</td>
							<td>'.$t->getSchule().'</td>
							<td>'.($t->getKurs() !== null ? $t->getKurs()->getName() : "Kein Kurs").'</td>
							<td>'.$t->getEssen().'</td>
							<td '.(strlen(trim($t->getSonstiges())) > 16 ? 'datatype="tooltip" title="'.htmlentities($t->getSonstiges()).'"' : '').'>'.(strlen(trim($t->getSonstiges())) > 16 ? htmlentities(substr($t->getSonstiges(),0,15)).'...' : htmlentities($t->getSonstiges())).'</td>
							<td>'.$t->getTshirt()->getName().' '.$t->getTshirt()->getSize().'</td>
							<td data-order="'.strtotime($t->getRegdate()).'">'.date("d.m.Y H:i:s",strtotime($t->getRegdate())).'</td>
							
						</tr>
					';
				}
					
					
				echo'
					</tbody>
				</table>
				
			</div>
		';
	}
}

?>