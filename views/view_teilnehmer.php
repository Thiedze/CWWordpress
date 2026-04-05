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
							$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'cw_user_kurs');
							$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'cw_user');
							echo '<div class="notice notice-success inline">
							        <p><strong>Erledigt:</strong>Alle Teilnehmer:innen wurden erfolgreich entfernt</p>
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
						  <p><strong>Sicherheitswarnung:</strong> Die Checkbox zum löschen aller Teilnehmer:innen wurde nicht bestätigt.</p>
					  </div>';
			}
		}else{
			echo '<div class="notice notice-error inline">
					  <p><strong>Sicherheitswarnung:</strong> Die Checkbox zum löschen aller Teilnehmer:innen wurde nicht bestätigt.</p>
				  </div>';
		}
	}
	
	$teilnehmer = get_all_teilnehmer();
	
	$_SESSION['csrf'] = sha1(uniqid());
	
	if($teilnehmer == null){
		echo '<h1>Es sind aktuell keine Teilnehmer:innen vorhanden</h1>';
	}else{
		$count_total      = count($teilnehmer);
		$count_kursleiter = count(array_filter($teilnehmer, function($t){ return $t->getIsCourseLeader(); }));
		$count_regular    = $count_total - $count_kursleiter;

		echo'
				<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
					<h1 style="margin:0;">Teilnehmer:innen
						<a class="page-title-action" id="export" href="' . menu_page_url( 'teilnehmer', false ) . '&action=export">Tabelle exportieren</a>
					</h1>
					<form method="post" style="display:inline-flex;align-items:center;gap:6px;" onsubmit="return confirm(\'Wirklich alle Teilnehmer:innen löschen?\');">
						<input type="checkbox" style="margin:0" value="1" name="delete_all" />
						<span style="font-size:9pt;">Alle Teilnehmer:innen entfernen</span>
						<input type="hidden" name="del_csrf" value="'.$_SESSION['csrf'].'"/>
						<button type="submit" name="delete_all_participants" class="button button-secondary delete" style="background:#900;color:#fff;font-size:13px;height:30px;line-height:28px;padding:0 8px">
							<span class="dashicons dashicons-trash" style="margin-top:5px"></span>Alle löschen
						</button>
					</form>
				</div>

				<div style="display:inline-block;width:180px;padding:8px 16px;margin:0 0 16px 0;background:#fff;border-left:4px solid #72aee6;">
					<strong>Teilnehmer:innen gesamt:</strong><br />
					<span style="font-size:1.4em;font-weight:bold">'.$count_total.'</span>
					<span style="color:#888"> gesamt</span><br />
					<span style="font-size:1.4em;font-weight:bold">'.$count_regular.'</span>
					<span style="color:#888"> regulär</span><br />
					<span style="font-size:1.4em;font-weight:bold">'.$count_kursleiter.'</span>
					<span style="color:#888"> Kursleiter:innen</span>
				</div>

				<div style="overflow-x:auto;-webkit-overflow-scrolling:touch">
				<table id="sorttable" class="widefat striped">
					<thead>
						<tr>
						    <th data-orderable="false">
								&nbsp;
							</th>
							<th>Typ</th>
							<th>Bezahlt</th>
							<th>Nachname</th>
							<th>Vorname</th>
							<th>EMail</th>
							<th>Adresse</th>
							<th>PLZ/Ort</th>
							<th>Geburtsdatum</th>
							<th>(Hoch-)Schule</th>
							<th>Kursleiter:in</th>
							<th>Kurs</th>
							<th>Essen</th>
							<th>Sonstiges</th>
							<th>Zu Bezahlen</th>
							<th>Registrierdatum</th>
							
						</tr>
					</thead>
					<tbody>';
		
				foreach ($teilnehmer as $t){
					echo'
						<tr>
						    <td>
								<button name="edit_teil" style="border: none; background: transparent;color: #0073aa" data-id="'.esc_attr($t->getId()).'">
										<i class="dashicons dashicons-welcome-write-blog"></i>
									</button>
								<button name="teil_delete" data-id="'.esc_attr($t->getId()).'" style="margin-left: 1px;border: none; background: transparent;color: #b00" data-name="'.esc_attr($t->getNachname().' '.$t->getVorname()).'">
										<i class="dashicons dashicons-trash"></i>
								</button>
							</td>
							<td data-order="'.esc_attr($t->get_paytype()).'">';

							if($t->get_paytype() == 2){
								echo '<i title="Alumni" class="dashicons dashicons-welcome-learn-more" style="color: goldenrod"></i>';
							}else{
								echo '<i title="Schüler:in/Student:in" class="dashicons dashicons-welcome-learn-more"></i>';
							}

					echo'   </td>
							<td data-order="'.esc_attr($t->getPayed()).'">';

                            if($t->getPayed() == 1){
                                echo '<img title="Bezahlt" src="'.plugin_dir_url(__FILE__).'../img/money.png'.'"/>';
                            }else{
                                echo '<img title="Noch nicht Bezahlt" src="'.plugin_dir_url(__FILE__).'../img/money.png'.'" style="-webkit-filter: grayscale(100%); filter: grayscale(100%);"/>';
                            }

					echo'   </td>
							<td>'.esc_html($t->getNachname()).'</td>
							<td>'.esc_html($t->getVorname()).'</td>
							<td>'.esc_html($t->getEmail()).'</td>
							<td>'.esc_html($t->getStr()).'</td>
							<td>'.esc_html($t->getPlz()).' '.esc_html($t->getOrt()).'</td>
							<td data-order="'.esc_attr(strtotime($t->getGeb())).'">'.esc_html(date("d.m.Y",strtotime($t->getGeb()))).'</td>
							<td>'.esc_html($t->getSchule()).'</td>
							<td>'.($t->getIsCourseLeader() ? '<span style="color:#6a6">Ja</span>' : 'Nein').'</td>
							<td>'.($t->getKurs() !== null ? esc_html($t->getKurs()->getName()) : "Kein Kurs").'</td>
							<td>'.esc_html(array('Kein Vegetarier'=>'Kein:e Vegetarier:in','Vegetarier'=>'Vegetarier:in','Veganer'=>'Veganer:in')[$t->getEssen()] ?? $t->getEssen()).'</td>
							<td '.(strlen(trim($t->getSonstiges())) > 16 ? 'datatype="tooltip" title="'.esc_attr($t->getSonstiges()).'"' : '').'>'.esc_html(strlen(trim($t->getSonstiges())) > 16 ? substr($t->getSonstiges(),0,15).'...' : $t->getSonstiges()).'</td>
							<td>'.esc_html($t->get_to_pay()).'&euro;</td>
							<td data-order="'.esc_attr(strtotime($t->getRegdate())).'">'.esc_html(date("d.m.Y H:i:s",strtotime($t->getRegdate()))).'</td>
							
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