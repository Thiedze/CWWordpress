<?php
/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 23.01.2017
 * Time: 16:53
 */

function program_head($show_new = false){
	echo'
	<div class="wrap">
		<h1>Programm';

	if($show_new) {
		echo '
			<a class="page-title-action" href="' . menu_page_url( 'program', false ) . '&action=new">Neu erstellen</a>		
		</h1>';
	}else{
		echo "</h1>";
	}
}

function show_program(){

	echo '<div class="wrap">';

	$events = get_all_events();

	if($events == null){
		echo '
			<div class="notice error">
				<p>Es ist kein Programm vorhanden!</p>
			</div>
		';
	}else{
		echo'
			<table id="eventtable" style="width: auto" class="widefat striped sortable display">
				<thead>
					<tr>
						<th style="width: 100px;border-right: 1px solid #eaeaea">
							<a>Tag</a>
						</th>
						<th style="width: 60px;border-right: 1px solid #eaeaea">
							<a>Start</a>
						</th>
						<th style="width: 60px;border-right: 1px solid #eaeaea">
							<a>Ende</a>
						</th>
						<th style="width: 200px;border-right: 1px solid #eaeaea">
							<a>Titel</a>
						</th>
						<th style="width: 300px;border-right: 1px solid #eaeaea">
							<a>Untertitel</a>
						</th>
						<th style="width: 400px;border-right: 1px solid #eaeaea">
							<a>Beschreibung</a>
						</th>
						<th data-orderable="false">
							&nbsp;
						</th>
					</tr>
				</thead>
				<tbody>';

		foreach ($events as $event){
			echo'
					<tr>
						<td>'.$event->getEventDay(true).'</td>
						<td>'.$event->getEventStart(true).'</td>
						<td>'.$event->getEventEnd(true).'</td>
						<td>'.$event->getEventName().'</td>
						<td>'.$event->getEventSubtext().'</td>
						<td>'.$event->getEventDescription(true).'</td>
						<td>
							<form action="'.menu_page_url('program',false).'&action=edit" method="post">
								<button name="edit" style="border: none; background: transparent;color: #0073aa">
									<i class="dashicons dashicons-welcome-write-blog"></i>
								</button>
								<button name="delete" type="submit" style="margin-left: 10px;border: none; background: transparent;color: #b00" onclick="return confirm(\'Das Event \\\'' . $event->getEventName() . '\\\' wirklich l&ouml;schen?\');">
									<i class="dashicons dashicons-trash"></i>
								</button>
								    <input type="hidden" name="eid" value="'.$event->getId().'"/>
							</form>
						</td>
					</tr>
				';
		}

		echo'
					</tbody>
				</table>
		';

	}

	echo '</div>';

}

function new_program(){
	global $wpdb;
	$error = false;
	$errmsg = array();

	if(isset($_POST["create"])){

		if(isset($_POST["event_day"])){
			if(trim($_POST["event_day"]) < 0 && trim($_POST["event_day"]) > 5){
				$error = true;
				$errmsg[] = "<li>Es wurde kein Tag angegeben</li>";
			}
		}

		if(isset($_POST["event_start"])){
			if($_POST["event_start"] < 700 || $_POST["event_start"] > 2300){
				$error = true;
				$errmsg[] = "<li>Die angegebene Startzeit ist ung&uuml;tig</li>";
			}
		}

		if(isset($_POST["event_end"])){
			if($_POST["event_end"] < 700 || $_POST["event_end"] > 2300){
				$error = true;
				$errmsg[] = "<li>Die angegebene Startzeit ist ung&uuml;tig</li>";
			}
		}

		if($error == false) {

			$res = $wpdb->get_row( "SELECT count(*) AS c FROM " . $wpdb->prefix . "cw_events WHERE event_day=" . $_POST["event_day"] . " AND (" . $_POST["event_start"] . " BETWEEN event_start AND event_end-1 OR " . $_POST["event_end"] . " BETWEEN event_start+1 AND event_end)" );

			if ( $res->c >= 1 ) {
				$error    = true;
				$errmsg[] = "<li>Das Event &uuml;berschneidet sich mit einem anderen Event</li>";
			}
		}

		if(isset($_POST["event_name"])){
			if(strlen(trim($_POST["event_end"])) === 0){
				$error = true;
				$errmsg[] = "<li>Es wurde kein Eventname angegeben</li>";
			}
		}

		if(!$error) {
			$event = new Event($wpdb);

			$event->setEventDay($_POST["event_day"]);
			$event->setEventStart($_POST["event_start"]);
			$event->setEventEnd($_POST["event_end"]);
			$event->setEventName($_POST["event_name"]);
			$event->setEventSubtext(($_POST["event_subtext"] ? $_POST["event_subtext"]: ""));
			$event->setEventDescription(($_POST["event_description"] ? $_POST["event_description"]: ""));
			$event->setEventColor((isset($_POST["event_color"]) ? $_POST["event_color"]: "#D7E7A1"));

			if($event->save()){
				program_head(true);
				echo '
					<div class="notice updated">
						<p>Das neue Event wurde erstellt</p>
					</div>
				';

				show_program();
				return;
			}else{
				program_head();
				echo '
					<div class="notice error">
						<p>Es ist ein Fehler beim erstellen des Events aufgetreten</p>
					</div>
				';
			}

		}else{
			program_head();
			echo '
				<div class="notice error">
					<span style="font-weight: bold">Es fehlen Angaben, um das Event erstellen zu können:</span><br />
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
		program_head();
	}
	
	for($z = 7;$z <= 23; $z++){
		$zeiten[] = $z * 100;
		$diszeiten[] = ($z < 10?'0'.$z:$z).':00';
		if($z < 23){
			$zeiten[] = ($z*100)+30;
			$diszeiten[] = ($z < 10?'0'.$z:$z).':30';
		}
	}

	echo'
		<form action="" method="post">
				<div style="height: auto;overflow: auto;width: 100%">
				<table class="form-table" style="float: left;width: 600px;">
					<tbody>
						<tr>
							<th>Tag:</th>
							<td>
								<select name="event_day" required="required">';
								
								$i = 0;
								foreach (Event::get_days() as $d){
									echo '<option value="'.$i.'" '.(isset($_POST["event_day"]) && $_POST["event_day"] == $i ? 'selected="selected"':'').' >'.$d.'</option>';
									$i++;
								}	
	echo'							
								</select>
							</td>
						</tr>
						<tr>
							<th>Start:</th>
							<td>
								<select name="event_start" required="required">';

								for($i = 0; $i < count($zeiten); $i++){
									echo '<option value="'.$zeiten[$i].'" '.(isset($_POST["event_start"]) && $_POST["event_start"] == $zeiten[$i] ? 'selected="selected"':'').'>'.$diszeiten[$i].'</option>';
								}

	echo'
								</select>
							</td>
						</tr>
						<tr>
							<th>Ende:</th>
							<td>
								<select name="event_end" required="required">';

								for($i = 0; $i < count($zeiten); $i++){
									echo '<option value="'.$zeiten[$i].'" '.(isset($_POST["event_end"]) && $_POST["event_end"] == $zeiten[$i] ? 'selected="selected"':'').'>'.$diszeiten[$i].'</option>';
								}

	echo'
								</select>
							</td>
						</tr>
						<tr>
							<th>Titel:</th>
							<td>
								<input type="text" name="event_name" required="required" size="50" value="'.(isset($_POST["event_name"]) ? $_POST["event_name"]: "").'" />
							</td>
						</tr>
						<tr>
							<th>Untertitel:</th>
							<td>
								<input type="text" name="event_subtext" size="50" value="'.(isset($_POST["event_subtext"]) ? $_POST["event_subtext"]: "").'" />
							</td>
						</tr>
						<tr>
							<th>Farbe:</th>
							<td>
								<input type="text" name="event_color" required="required" class="color-field" value="'.(isset($_POST["event_color"]) ? $_POST["event_color"]: "#D7E7A1").'" />
							</td>
						</tr>
					</tbody>
				</table>
					<div>
						<span style="padding-left: 75px;">Ansicht im Kalendar</span><br /><br />
						<div style="margin-left: 75px;float: left;width: 200px;border: 1px solid #000;clear: none;background: rgb(215, 231, 161);padding: 5px;">
							<span style="font-size: 9pt">07:00 - 09:00</span><br />
							<span style="font-weight: bold;font-size: 10pt;">Titel</span><br />
							<span style="font-weight: bold;font-size: 9pt;">Untertitel</span><br />
						</div>
					</div>
				
				</div>
				<h3>Beschreibung:</h3>
			    <div style="width:650px">';

				wp_editor((isset($_POST["event_description"]) ? $_POST["event_description"]: ""),"event_description",array('editor_height' => 300));

				echo'</div>
						
				<p>&nbsp;</p>
				<button type="submit" name="create" class="button button-primary" >
					Erstellen
			  	</button >&nbsp;&nbsp;
				<a class="button" style="color: #900" href="'.menu_page_url('program',false).'">
					<span class="dashicons dashicons-no" style="margin-top: 4px;"></span>Abbrechen
				</a>
		</form>
	';
}

function edit_program(){
	global $wpdb;
	$error = false;
	$errmsg = array();

	$event = new Event( $wpdb );

	if(isset($_POST["eid"])) {

		$event->load( $_POST["eid"] );

		if ( $event->getId() == - 1 ) {
			program_not_exist();
			return;
		}
	}else{
		program_not_exist();
	}

	if(isset($_POST["edit_done"])){

		if(isset($_POST["event_day"])){
			if(trim($_POST["event_day"]) < 0 && trim($_POST["event_day"]) > 5){
				$error = true;
				$errmsg[] = "<li>Es wurde kein Tag angegeben</li>";
			}
		}

		if(isset($_POST["event_start"])){
			if($_POST["event_start"] < 700 || $_POST["event_start"] > 2300){
				$error = true;
				$errmsg[] = "<li>Die angegebene Startzeit ist ung&uuml;tig</li>";
			}
		}

		if(isset($_POST["event_end"])){
			if($_POST["event_end"] < 700 || $_POST["event_end"] > 2300){
				$error = true;
				$errmsg[] = "<li>Die angegebene Startzeit ist ung&uuml;tig</li>";
			}
		}

		if($error == false) {

			$res = $wpdb->get_row( "SELECT count(*) AS c FROM " . $wpdb->prefix . "cw_events WHERE event_day=" . $_POST["event_day"] . " AND (" . $_POST["event_start"] . " BETWEEN event_start AND event_end-1 OR " . $_POST["event_end"] . " BETWEEN event_start+1 AND event_end) AND id <> ".$_POST["eid"] );

			if ( $res->c >= 1 ) {
				$error    = true;
				$errmsg[] = "<li>Das Event &uuml;berschneidet sich mit einem anderen Event</li>";
			}
		}

		if(isset($_POST["event_name"])){
			if(strlen(trim($_POST["event_end"])) === 0){
				$error = true;
				$errmsg[] = "<li>Es wurde kein Eventname angegeben</li>";
			}
		}

		if(!$error) {

			$event->setEventDay($_POST["event_day"]);
			$event->setEventStart($_POST["event_start"]);
			$event->setEventEnd($_POST["event_end"]);
			$event->setEventName($_POST["event_name"]);
			$event->setEventSubtext(($_POST["event_subtext"] ? $_POST["event_subtext"]: ""));
			$event->setEventDescription(($_POST["event_description"] ? $_POST["event_description"]: ""));
			$event->setEventColor((isset($_POST["event_color"]) ? $_POST["event_color"]: "#D7E7A1"));


			if($event->save()){
				program_head(true);
				echo '
					<div class="notice updated">
						<p>Die &Auml;nderungen wurden gesprichert</p>
					</div>
				';

				show_program();
				return;
			}else{
				program_head();
				echo '
					<div class="notice error">
						<p>Es ist ein Fehler beim bearbiten des Events aufgetreten</p>
					</div>
				';
			}

		}else{
			program_head();
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
		$_POST["event_day"] = $event->getEventDay();
		$_POST["event_start"] = $event->getEventStart();
		$_POST["event_end"] = $event->getEventEnd();
		$_POST["event_name"] = $event->getEventName();
		$_POST["event_subtext"] = $event->getEventSubtext();
		$_POST["event_description"] = $event->getEventDescription();
		$_POST["event_color"] = $event->getEventColor();
	}

	if(!isset($_POST["edit_done"])){
		program_head();
	}

	for($z = 7;$z <= 23; $z++){
		$zeiten[] = $z * 100;
		$diszeiten[] = ($z < 10?'0'.$z:$z).':00';
		if($z < 23){
			$zeiten[] = ($z*100)+30;
			$diszeiten[] = ($z < 10?'0'.$z:$z).':30';
		}
	}

	echo'
		<form action="" method="post">
				<div style="height: auto;overflow: auto;width: 100%">
				<table class="form-table" style="float: left;width: 600px;">
					<tbody>
						<tr>
							<th>Tag:</th>
							<td>
								<select name="event_day" required="required">';

	$i = 0;
	foreach (Event::get_days() as $d){
		echo '<option value="'.$i.'" '.(isset($_POST["event_day"]) && $_POST["event_day"] == $i ? 'selected="selected"':'').' >'.$d.'</option>';
		$i++;
	}
	echo'							
								</select>
							</td>
						</tr>
						<tr>
							<th>Start:</th>
							<td>
								<select name="event_start" required="required">';

	for($i = 0; $i < count($zeiten); $i++){
		echo '<option value="'.$zeiten[$i].'" '.(isset($_POST["event_start"]) && $_POST["event_start"] == $zeiten[$i] ? 'selected="selected"':'').'>'.$diszeiten[$i].'</option>';
	}

	echo'
								</select>
							</td>
						</tr>
						<tr>
							<th>Ende:</th>
							<td>
								<select name="event_end" required="required">';

	for($i = 0; $i < count($zeiten); $i++){
		echo '<option value="'.$zeiten[$i].'" '.(isset($_POST["event_end"]) && $_POST["event_end"] == $zeiten[$i] ? 'selected="selected"':'').'>'.$diszeiten[$i].'</option>';
	}

	echo'
								</select>
							</td>
						</tr>
						<tr>
							<th>Titel:</th>
							<td>
								<input type="text" name="event_name" required="required" size="50" value="'.(isset($_POST["event_name"]) ? $_POST["event_name"]: "").'" />
							</td>
						</tr>
						<tr>
							<th>Untertitel:</th>
							<td>
								<input type="text" name="event_subtext" size="50" value="'.(isset($_POST["event_subtext"]) ? $_POST["event_subtext"]: "").'" />
							</td>
						</tr>
						<tr>
							<th>Farbe:</th>
							<td>
								<input type="text" name="event_color" required="required" class="color-field" value="'.(isset($_POST["event_color"]) ? $_POST["event_color"]: "#D7E7A1").'" />
							</td>
						</tr>
					</tbody>
				</table>
					<div>
						<span style="padding-left: 75px;">Ansicht im Kalendar</span><br /><br />
						<div style="margin-left: 75px;float: left;width: 200px;border: 1px solid #000;clear: none;background: rgb(215, 231, 161);padding: 5px;">
							<span style="font-size: 9pt">07:00 - 09:00</span><br />
							<span style="font-weight: bold;font-size: 10pt;">Titel</span><br />
							<span style="font-weight: bold;font-size: 9pt;">Untertitel</span><br />
						</div>
					</div>
				
				</div>
				<h3>Beschreibung:</h3>
			    <div style="width:650px">';

	wp_editor((isset($_POST["event_description"]) ? $_POST["event_description"]: ""),"event_description",array('editor_height' => 300));

	echo'</div>
						
				<p>&nbsp;</p>
				<button type="submit" name="edit_done" class="button button-primary" >
					Speichern
			  	</button >&nbsp;&nbsp;
				<a class="button" style="color: #900" href="'.menu_page_url('program',false).'">
					<span class="dashicons dashicons-no" style="margin-top: 4px;"></span>Abbrechen
				</a>
				<input type="hidden" name="eid" value="'.$_POST["eid"].'" />
		</form>
	';
}


function delete_program(){
	global $wpdb;

	if(isset($_POST["eid"])){

		if(preg_match("/[0-9]+/",$_POST["eid"])){

			$query = $wpdb->prepare("DELETE FROM ".$wpdb->prefix."cw_events WHERE id=%d",$_POST["eid"]);
			$wpdb->query($query);

			program_head(true);
			echo '
				<div class="notice updated">
					<p>Das angegebene Event wurde entfernt</p>
				</div>
			';
			show_program();
			return;

		}
	}else{
		program_not_exist();
		return;
	}

}

function program_not_exist(){
	program_head(true);
	echo '
				<div class="notice error">
					<p>Das angegebene Event existiert nicht!</p>
				</div>
			';
	show_program();
}

?>