<?php
/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 14.10.2016
 * Time: 13:24
 */


function teilnehmer(){
	session_start();
	global $wpdb;
	//".$wpdb->prefix."cw_kurse
	
	echo'<div class="wrap">';
	
	if(isset($_POST['delete_all_participants'])){
		if(isset($_POST['delete_all'])){
			if($_POST['delete_all'] == 1){
				if(isset($_POST['del_csrf'])){
					if($_POST['del_csrf'] === $_SESSION['csrf']){
						try{
							$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'cw_user_shirt');
							$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'cw_user_kurs');
							$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'cw_user');
							echo '<div class="notice notice-success inline">
							        <p><strong>Erledigt:</strong>Alle Teilnehmer wurden erfolgreich entfernt</p>
						  		  </div>';
						}catch(Exception $e){
							echo '<div class="notice notice-error inline">
								     <p><strong>Datenbankfehler:</strong>Fehler beim Ausführen des Datenbankbefehls</p>
						  		  </div>';
						}
						
					}else{
						echo '<div class="notice notice-error inline">
							<p><strong>Sicherheitswarnung:</strong> Der CSRF-Token stimmt nicht überein. Befehl abgebrochen.</p>
						  </div>';
					}
				}else{
					echo '<div class="notice notice-error inline">
							<p><strong>Sicherheitswarnung:</strong> Der CSRF-Token fehlt. Befehl abgebrochen.</p>
						  </div>';
				}
			}else{
				echo '<div class="notice notice-error inline">
						  <p><strong>Sicherheitswarnung:</strong> Die Checkbox zum löschen aller Teilnehmer wurde nicht bestätigt.</p>
					  </div>';
			}
		}else{
			echo '<div class="notice notice-error inline">
					  <p><strong>Sicherheitswarnung:</strong> Die Checkbox zum löschen aller Teilnehmer wurde nicht bestätigt.</p>
				  </div>';
		}
	}
	
	$teilnehmer = get_all_teilnehmer();
	
	$_SESSION['csrf'] = sha1(uniqid());
	
	if($teilnehmer == null){
		echo '<h1>Es sind aktuell keine Teilnehmer vorhanden</h1>';
	}else{
		echo'
				<h1>Teilnehmer (aktuell '.count($teilnehmer).')
					<a class="page-title-action" id="export" href="' . menu_page_url( 'teilnehmer', false ) . '&action=export">Tabelle exportieren</a>
					<form method="post" style="display:inline-flex;align-items:center;gap:6px;margin-left:8px;vertical-align:middle" onsubmit="return confirm(\'Wirklich alle Teilnehmer löschen?\');">
						<input type="checkbox" style="margin:0" value="1" name="delete_all" />
						<span style="font-size:9pt;font-weight:normal">Alle Teilnehmer entfernen</span>
						<input type="hidden" name="del_csrf" value="'.$_SESSION['csrf'].'"/>
						<button type="submit" name="delete_all_participants" class="button button-secondary delete" style="background:#900;color:#fff;font-size:13px;height:30px;line-height:28px;padding:0 8px">
							<span class="dashicons dashicons-trash" style="margin-top:5px"></span>Alle löschen
						</button>
					</form>
				</h1>

				<br />
				
				<div style="overflow-x:auto;-webkit-overflow-scrolling:touch">
				<table id="sorttable" class="widefat striped">
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
							<th>Zu Bezahlen</th>
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
							<td style="width: 75px">';

							if($t->get_paytype() == 2){
								echo '<i title="Alumni" class="dashicons dashicons-welcome-learn-more" style="color: goldenrod"></i>';
							}else{
								echo '<i title="Schüler/Student" class="dashicons dashicons-welcome-learn-more"></i>';
							}

                            if($t->getPayed() == 1){
                                echo '<img title="Bezahlt" src="'.plugin_dir_url(__FILE__).'../img/money.png'.'"/>';
                            }else{
                                echo '<img title="Noch nicht Bezahlt" src="'.plugin_dir_url(__FILE__).'../img/money.png'.'" style="-webkit-filter: grayscale(100%); filter: grayscale(100%);"/>';
                            }

                            if($t->getTshirt()->getName() != "") {
                                if ($t->getShirtPayed() == 1) {
                                    echo '<img title="T-Shirt bezahlt" src="' . plugin_dir_url(__FILE__) . '../img/shirt.png' . '"/>';
                                } else {
                                    echo '<img title="T-Shirt noch nicht bezahlt" src="' . plugin_dir_url(__FILE__) . '../img/shirt.png' . '" style="-webkit-filter: grayscale(100%); filter: grayscale(100%);"/>';
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
							<td>'.$t->get_to_pay().'&euro;</td>
							<td data-order="'.strtotime($t->getRegdate()).'">'.date("d.m.Y H:i:s",strtotime($t->getRegdate())).'</td>
							
						</tr>
					';
				}
					
					
				echo'
					</tbody>
				</table>
				</div>
			</div>
		';
	}
}

session_write_close()
?>