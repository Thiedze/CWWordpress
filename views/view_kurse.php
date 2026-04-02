<?php
/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 11.10.2016
 * Time: 11:29
 */

/**
 * @param bool $show_new
 */
function kurs_head($show_new = false){
	echo'
	<div class="wrap">
		<h1>Kurse';

	if($show_new) {
		echo '<a class="page-title-action" href="' . menu_page_url( 'kurse', false ) . '&action=new">Neu erstellen</a></h1>';
	}else{
		echo "</h1>";
	}
}

function show_kurse(){
	global $wpdb;

	$kurse = get_all_kurse();

	echo '<div class="wrap">';

	if($kurse === null){
		echo '
			<div class="notice error">
				<p>Es sind keine Kurse vorhanden!</p>
			</div>
		';
	}else{

		echo '
			<div class="notice notice-info">
				<p>Sichtbar gibt an, ob dieser Kurs in der Kurs&uuml;bersicht angezeigt werden soll!</p>
			</div>
			<table id="kurstable" style="width: 850px" class="widefat striped sortable display">
				<thead>
					<tr>
						<th style="width: 400px;border-right: 1px solid #eaeaea">
							<a>Kursname</a>
						</th>
						<th style="width: 80px;border-right: 1px solid #eaeaea">
							<a>Teilnehmer</a>
						</th>
						<th style="width: 80px;border-right: 1px solid #eaeaea">
							<a>Sichtbar</a>
						</th>
						<th style="width: 80px;border-right: 1px solid #eaeaea">
							<a>Status</a>
						</th>
						<th data-orderable="false">
							&nbsp;
						</th>
					</tr>
				</thead>
				<tbody>';

			foreach ($kurse as $kurs){
				echo'
					<tr>
						<td>'.$kurs->getName().'</td>
						<td data-order="'.$kurs->getTeilnehmer().'">'.$kurs->getTeilnehmer().' / '.$kurs->getMaxTeilnehmer().'</td>
						<td>'.($kurs->getShowFront()? '<span style="color: #6a6">Ja</span>': '<span style="color: #a66">Nein</span>').'</td>
						<td>'.($kurs->getIs_open()? '<span style="color: #6a6">offen</span>': '<span style="color: #a66">geschlossen</span>').'</td>
						<td>
							<form action="'.menu_page_url('kurse',false).'&action=edit" method="post">
								<button name="edit" style="border: none; background: transparent;color: #0073aa">
									<i class="dashicons dashicons-welcome-write-blog"></i>
								</button>';
							if($kurs->getId() != 1) {
								echo '<button name="delete" type="submit" style="margin-left: 10px;border: none; background: transparent;color: #b00" onclick="return confirm(\'Den Kurs \\\'' . $kurs->getName() . '\\\' wirklich l&ouml;schen?\');">
										<i class="dashicons dashicons-trash"></i>
									</button>';
							}
	                      echo'
								    <input type="hidden" name="kid" value="'.$kurs->getId().'"/>
							</form>
						</td>
					</tr>
				';
			}

		echo'</tbody>
			</table>
		';

	}

	echo '</div>';
}

function new_kurs(){
	global $wpdb;
	$error = false;
	$errmsg = array();

	if(isset($_POST["create"])){

		if(isset($_POST["name"])){
			if(strlen(trim($_POST["name"])) === 0){
				$error = true;
				$errmsg[] = "<li>Es wurde kein Name angegeben</li>";
			}
		}

		if(isset($_POST["beschreibung"])){
			if(strlen(trim($_POST["beschreibung"])) === 0){
				$error = true;
				$errmsg[] = "<li>Es wurde keine Beschreibung angegeben</li>";
			}
		}

		if(isset($_POST["ad_image"])){
			if(strlen(trim($_POST["ad_image"])) === 0){
				$error = true;
				$errmsg[] = "<li>Es wurde kein Bild angegeben</li>";
			}
		}

		if(isset($_POST["mteil"])){
			if(!preg_match("/[0-9]+/",$_POST["mteil"])){
				$error = true;
				$errmsg[] = "<li>Da Anzahl der Teilnehmer ist ung&uuml;ltig</li>";
			}
		}

		if(!$error) {
			$kurs = new Kurs( $wpdb );
			$kurs->setName( $_POST["name"] );
			$kurs->setBeschreibung( $_POST["beschreibung"] );
			$kurs->setBild( $_POST["ad_image"] );
			$kurs->setMaxTeilnehmer( $_POST["mteil"] );
			$kurs->setShowFront( ( isset( $_POST["show_front"] ) ? 1 : 0 ) );
			$kurs->setIs_open( ( isset( $_POST["is_open"] ) ? 1 : 0 ) );

			if($kurs->save()){
				echo '
					<div class="notice updated">
						<p>Der neue Kurs wurde erstellt</p>
					</div>
				';

				kurs_head(true);
				show_kurse();
				return;
			}else{
				kurs_head();
				echo '
					<div class="notice error">
						<p>Es ist ein Fehler beim erstellen des Kurses aufgetreten</p>
					</div>
				';
			}

		}else{
			kurs_head();
			echo '
				<div class="notice error">
					<span style="font-weight: bold">Es fehlen Angaben, um den Kurs erstellen zu können:</span><br />
					<ul>';

				foreach ($errmsg as $err){
					echo $err;
				}

			echo'	</ul>
				</div>
			';

		}

	}

	if(!isset($_POST["create"])){
		kurs_head();
	}

	echo'
		<form action="" method="post">
				<table class="form-table">
					<tbody>
						<tr>
							<th>Kursname:</th>
							<td>
								<input type="text" name="name" required="required" size="50" value="'.(isset($_POST["name"]) ? $_POST["name"]: "").'" />
							</td>
						</tr>
						<tr>
							<th>Max. Teilnehmer:</th>
							<td>
								<input id="mteil" min="0" pattern="[0-9]+" required="required" name="mteil" value="'.(isset($_POST["mteil"]) ? $_POST["mteil"]: "0").'" style="width: 30px;height: 20px;"/>
							</td>
						</tr>
						<tr>
							<th>Kurs in der &Uuml;bersicht anzeigen</th>
							<td>
								<div id="sw_back_1" style="" class="'.(isset($_POST["show_front"]) ? "sw_on": "sw_off").'">
									<div id="sw_1" data="sw_ch_1" data-type="switch" style="" ></div>
								</div>
								<input type="checkbox" id="sw_ch_1" style="display: none" value="1" name="show_front" '.(isset($_POST["show_front"]) ? 'checked="checked"':'').'/>
							</td>
						</tr>
						<tr>
							<th>Anmeldung m&ouml;glich</th>
							<td>
								<div id="sw_back_2" style="" class="'.(isset($_POST["is_open"]) ? "sw_on": "sw_off").'">
									<div id="sw_2" data="sw_ch_2" data-type="switch" style="" ></div>
								</div>
								<input type="checkbox" id="sw_ch_2" style="display: none" value="1" name="is_open" '.(isset($_POST["is_open"]) ? 'checked="checked"':'').'/>
							</td>
						</tr>
						<tr>
							<th>Abbildung Kurs:</th>
							<td>
								<label for="upload_image" style="float: left">
									<input id="upload_image" type="text" required="required" size="50" name="ad_image" value="'.(isset($_POST["ad_image"]) ? $_POST["ad_image"]: "").'" oninvalid="invalid()" style="display:none"/>
									<input id="upload_image_button" class="button" type="button" value="Bild aussuchen" />
								</label>&nbsp;
								<img id="preimg" src="'.(isset($_POST["ad_image"]) ? $_POST["ad_image"]: "").'" height="50px"/>
							</td>
						</tr>
					</tbody>
				</table>
						
			 <h3>Beschreibung:</h3>
			 <div style="width:650px">';

				wp_editor((isset($_POST["beschreibung"]) ? $_POST["beschreibung"]: ""),"beschreibung",array('editor_height' => 300));

		echo'</div>
				<p>&nbsp;</p>
				<button type="submit" name="create" class="button button-primary" >
					Erstellen
			  	</button >&nbsp;&nbsp;
				<a class="button" style="color: #900" href="'.menu_page_url('kurse',false).'">
					<span class="dashicons dashicons-no" style="margin-top: 4px;"></span>Abbrechen
				</a>
		</form>
	';

}

function edit_kurs(){
	global $wpdb;
	$error = false;
	$errmsg = array();

	$kurs = new Kurs( $wpdb );

	if(isset($_POST["kid"])) {

		$kurs->load( $_POST["kid"] );

		if ( $kurs->getId() == - 1 ) {
			kurs_not_exist();
			return;
		}
	}else{
		kurs_not_exist();
	}

	if(isset($_POST["edit_done"])){

		if(isset($_POST["name"])){
			if(strlen(trim($_POST["name"])) === 0){
				$error = true;
				$errmsg[] = "<li>Es wurde kein Name angegeben</li>";
			}
		}

		if(isset($_POST["beschreibung"])){
			if(strlen(trim($_POST["beschreibung"])) === 0){
				$error = true;
				$errmsg[] = "<li>Es wurde keine Beschreibung angegeben</li>";
			}
		}

		if(isset($_POST["ad_image"])){
			if(strlen(trim($_POST["ad_image"])) === 0){
				$error = true;
				$errmsg[] = "<li>Es wurde kein Bild angegeben</li>";
			}
		}

		if(isset($_POST["mteil"])){
			if(!preg_match("/[0-9]+/",$_POST["mteil"])){
				$error = true;
				$errmsg[] = "<li>Da Anzahl der Teilnehmer ist ung&uuml;ltig</li>";
			}
		}

		if(!$error) {
			$kurs->setName( $_POST["name"] );
			$kurs->setBeschreibung( $_POST["beschreibung"] );
			$kurs->setBild( $_POST["ad_image"] );
			$kurs->setMaxTeilnehmer( $_POST["mteil"] );
			$kurs->setShowFront( ( isset( $_POST["show_front"] ) ? 1 : 0 ) );
			$kurs->setIs_open( ( isset( $_POST["is_open"] ) ? 1 : 0 ) );

			if($kurs->save()){
				echo '
					<div class="notice updated">
						<p>Die &Auml;nderungen wurden gespeichert</p>
					</div>
				';

				kurs_head(true);
				show_kurse();
				return;
			}else{
				kurs_head();
				echo '
					<div class="notice error">
						Es ist ein Fehler beim bearbeiten des Kurses aufgetreten
					</div>
				';
			}

		}else{
			kurs_head();
			echo '
				<div class="notice error">
					<span style="font-weight: bold">Es fehlen Angaben, um die &Auml;nderungen zu speichern:</span><br />
					<ul>';

			foreach ($errmsg as $err){
				echo $err;
			}

			echo'	</ul>
				</div>
			';

		}

	}else{
		$_POST["name"] = $kurs->getName();
		$_POST["beschreibung"] = $kurs->getBeschreibung();
		$_POST["ad_image"] = $kurs->getBild();
		$_POST["mteil"] = $kurs->getMaxTeilnehmer();
		($kurs->getShowFront() ? $_POST["show_front"] = 1 : "");
		($kurs->getIs_open() ? $_POST["is_open"] = 1 : "");
	}

	if(!isset($_POST["edit_done"])){
		kurs_head();
	}

	echo'
		<form action="" method="post">
				<table class="form-table">
					<tbody>
						<tr>
							<th>Kursname:</th>
							<td>
								<input type="text" name="name" required="required" size="50" value="'.(isset($_POST["name"]) ? $_POST["name"]: "").'" />
							</td>
						</tr>
						<tr>
							<th>Max. Teilnehmer:</th>
							<td>
								<input id="mteil" min="0" pattern="[0-9]+" required="required" name="mteil" value="'.(isset($_POST["mteil"]) ? $_POST["mteil"]: "0").'" style="width: 30px;height: 20px;"/>
							</td>
						</tr>
						<tr>
							<th>Kurs in der &Uuml;bersicht anzeigen</th>
							<td>
								<div id="sw_back_1" style="" class="'.(isset($_POST["show_front"]) ? "sw_on": "sw_off").'">
									<div id="sw_1" data="sw_ch_1" data-type="switch" style="" ></div>
								</div>
								<input type="checkbox" id="sw_ch_1" style="display: none" value="1" name="show_front" '.(isset($_POST["show_front"]) ? 'checked="checked"':'').'/>
							</td>
						</tr>
						<tr>
							<th>Anmeldung m&ouml;glich</th>
							<td>
								<div id="sw_back_2" style="" class="'.(isset($_POST["is_open"]) ? "sw_on": "sw_off").'">
									<div id="sw_2" data="sw_ch_2" data-type="switch" style="" ></div>
								</div>
								<input type="checkbox" id="sw_ch_2" style="display: none" value="1" name="is_open" '.(isset($_POST["is_open"]) ? 'checked="checked"':'').'/>
							</td>
						</tr>
						<tr>
							<th>Abbildung Kurs:</th>
							<td>
								<label for="upload_image" style="float: left">
									<input id="upload_image" type="text" size="50" required="required" name="ad_image" value="'.(isset($_POST["ad_image"]) ? $_POST["ad_image"]: "").'" oninvalid="invalid()" style="display:none"/>
									<input id="upload_image_button" class="button" type="button" value="Bild aussuchen" />
								</label>&nbsp;
								<img id="preimg" src="'.(isset($_POST["ad_image"]) ? $_POST["ad_image"]: "").'" height="50px"/>
							</td>
						</tr>
					</tbody>
				</table>
						
			 <h3>Beschreibung:</h3>
			 <div style="width:650px">';

	wp_editor((isset($_POST["beschreibung"]) ? $_POST["beschreibung"]: ""),"beschreibung",array('editor_height' => 300));

	echo'</div>
				<p>&nbsp;</p>
				<button type="submit" name="edit_done" class="button button-primary" >
					Speichern
			  	</button >&nbsp;&nbsp;
				<a class="button" style="color: #900" href="'.menu_page_url('kurse',false).'">
					<span class="dashicons dashicons-no" style="margin-top: 4px;"></span>Abbrechen
				</a>
				<input type="hidden" name="kid" value="'.$_POST["kid"].'" />
		</form>
	';

}

function delete_kurs(){
	global $wpdb;

	if(isset($_POST["kid"])){

		if(preg_match("/[0-9]+/",$_POST["kid"]) && trim($_POST["kid"]) != 1){

			$query = $wpdb->prepare("DELETE FROM ".$wpdb->prefix."cw_kurse WHERE id=%d",$_POST["kid"]);
			$wpdb->query($query);

			$query = $wpdb->prepare("UPDATE ".$wpdb->prefix."cw_user_kurs SET kurs_id=0 WHERE kurs_id=%d OR kurs_id IS NULL",$_POST["kid"]);
			$wpdb->query($query);

			kurs_head(true);
			echo '
				<div class="notice updated">
					<p>Der angegeben Kurs wurde entfernt und die Teilnehmer in &quot;Sonstiges&quot; verschoben</p>
				</div>
			';
			show_kurse();
			return;

		}else{

			if(trim($_POST["kid"]) != 1){
				kurs_not_exist();
			}else{
				kurs_head(true);
				echo '
					<div class="notice error">
						<p>Der Kurs "Sonstiges" kann nicht gel&ouml;scht werden!</p>
					</div>
				';
				show_kurse();
			}
			return;
		}
	}else{
		kurs_not_exist();
		return;
	}

}

function kurs_not_exist(){
	kurs_head(true);
	echo '
				<div class="notice error">
					<p>Der angegeben Kurs existiert nicht!</p>
				</div>
			';
	show_kurse();
}

?>