<?php
/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 13.10.2016
 * Time: 09:59
 */

/**
 * @param bool $show_new
 */
function tshirt_head($show_new = false){
	echo'
	<div class="wrap">
		<h1>T-Shirts';

	if($show_new) {
		echo '
			<a class="page-title-action" href="' . menu_page_url( 'tshirts', false ) . '&action=new">Neu erstellen</a>
			<a class="page-title-action" href="' . menu_page_url( 'tshirts', false ) . '&action=export">Liste exportieren</a>
		
		</h1>';
	}else{
		echo "</h1>";
	}
}

function show_tshirts(){
	global $wpdb;

	$tshirts = get_all_tshirts();

	echo '<div class="wrap">';

	if($tshirts === null){
		echo '
			<div class="notice error">
				<p>Es sind keine T-Shirts vorhanden!</p>
			</div>
		';
	}else{

		echo '
			<table id="tshirttable" style="width: 850px;float: left; margin-right: 20px;margin-bottom: 30px;" class="widefat striped sortable display">
				<thead>
					<tr>
						<th style="width: 400px;border-right: 1px solid #eaeaea">
							<a>Bezeichnung</a>
						</th>
						<th style="width: 80px;border-right: 1px solid #eaeaea">
							<a>Gr&ouml;&szlig;e</a>
						</th>
						<th style="width: 80px;border-right: 1px solid #eaeaea">
							<a>Preis</a>
						</th>
						<th data-orderable="false">
							&nbsp;
						</th>
					</tr>
				</thead>
				<tbody>';

		foreach ($tshirts as $tshirt){
			echo'
					<tr>
						<td>'.$tshirt->getName().'</td>
						<td data-order="'.array_search($tshirt->getSize(),Shirt::getsizes()).'">'.$tshirt->getSize().'</td>
						<td data-order="'.$tshirt->getPreis().'">'.$tshirt->getPreis().' &euro;</td>
						<td>
							<form action="'.menu_page_url('tshirts',false).'&action=edit" method="post">
								<button name="edit" style="border: none; background: transparent;color: #0073aa">
									<i class="dashicons dashicons-welcome-write-blog"></i>
								</button>								
								<button name="delete" type="submit" style="margin-left: 10px;border: none; background: transparent;color: #b00" onclick="return confirm(\'Das Tshirt \\\''.$tshirt->getName().' ('.$tshirt->getSize().')\\\' wirklich l&ouml;schen?\');">
									<i class="dashicons dashicons-trash"></i>
								</button>
								<input type="hidden" name="kid" value="'.$tshirt->getId().'"/>
							</form>
						</td>
					</tr>
				';
		}

		$shirtlist = $wpdb->get_results("SELECT wcs.name,wcs.size,count(wcus.user_id) as anzahl 
								FROM wp_cw_user_shirt wcus 
							    LEFT JOIN wp_cw_shirt wcs ON wcus.shirt_id=wcs.id 
							GROUP BY wcs.name,wcs.size
							ORDER BY wcs.name,wcs.size");

		echo'</tbody>
			</table>
			
			<div style="width: 300px;float: left;clear: none;">
			<table class="widefat striped sortable display">
				<thead>
					<tr>
						<td>Shirt</td>
						<td>Gr&ouml;&szlig;e</td>
						<td>Anzahl</td>
					</tr>
				</thead>
				<tbody>';
			$i = 1;
			if($shirtlist){
				foreach ($shirtlist as $shirt){
					echo'
						<tr '.($i%2 ? 'style="background: #eaeaea"':'').'>
							<td>'.$shirt->name.'</td>
							<td>'.$shirt->size.'</td>
							<td>'.$shirt->anzahl.'</td>
						</tr>
					';
					$i++;
				}
			}

		echo'
				</tbody>
			</table>
			</div>
		';

	}

	echo '</div>';
}

function new_tshirt(){
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

		if(isset($_POST["size"])){
			if(strlen(trim($_POST["size"])) === 0){
				$error = true;
				$errmsg[] = "<li>Es wurde keine Gr&ouml;&szlig;e angegeben</li>";
			}else {
				if (!in_array($_POST["size"],Shirt::getsizes())){
					$error = true;
					$errmsg[] = "<li>Die angegebene Gr&ouml;&szlig;e existiert nicht</li>";
				}
			}

		}

		if(isset($_POST["preis"])){
			if(!preg_match("/[0-9]+/",$_POST["preis"])){
				$error = true;
				$errmsg[] = "<li>Der angegebene Preis ist ung&uuml;ltig</li>";
			}
		}

		if(!$error) {
			$tshirt = new Shirt( $wpdb );
			$tshirt->setName( $_POST["name"] );
			$tshirt->setSize( $_POST["size"] );
			$tshirt->setPreis( $_POST["preis"] );

			if($tshirt->save()){
				tshirt_head(true);
				echo '
					<div class="notice updated">
						<p>Das neue T-Shirt wurde erstellt</p>
					</div>
				';

				show_tshirts();
				return;
			}else{
				tshirt_head();
				echo '
					<div class="notice error">
						<p>Es ist ein Fehler beim erstellen des T-Shirts aufgetreten</p>
					</div>
				';
			}

		}else{
			tshirt_head();
			echo '
				<div class="notice error">
					<span style="font-weight: bold">Es fehlen Angaben, um das T-Shirt erstellen zu können:</span><br />
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
		tshirt_head();
	}

	echo'
		<form action="" method="post">
				<table class="form-table">
					<tbody>
						<tr>
							<th>Tshirtbezeichnung:</th>
							<td>
								<input type="text" name="name" required="required" size="50" value="'.(isset($_POST["name"]) ? $_POST["name"]: "").'" />
							</td>
						</tr>
						<tr>
							<th>Gr&ouml;&szlig;e:</th>
							<td>
								<select name="size" required="required">';

								foreach (Shirt::getsizes() as $size){
									echo '<option value="'.$size.'" '.(isset($_POST["size"]) && $size == $_POST["size"] ? 'selected="selected"':'' ).' >'.$size.'</option>';
								}

						echo'
								</select>
							</td>
						</tr>
						<tr>
							<th>Preis:</th>
							<td>
								<input id="mteil" min="0" pattern="[0-9]+" required="required" name="preis" value="'.(isset($_POST["preis"]) ? $_POST["preis"]: "0").'" style="width: 30px;height: 20px;"/>&euro;
							</td>
						</tr>
					</tbody>
				</table>
						
				<p>&nbsp;</p>
				<button type="submit" name="create" class="button button-primary" >
					Erstellen
			  	</button >&nbsp;&nbsp;
				<a class="button" style="color: #900" href="'.menu_page_url('tshirts',false).'">
					<span class="dashicons dashicons-no" style="margin-top: 4px;"></span>Abbrechen
				</a>
		</form>
	';

}

function edit_tshirt(){
	global $wpdb;
	$error = false;
	$errmsg = array();

	$tshirt = new Shirt( $wpdb );

	if(isset($_POST["kid"])) {

		$tshirt->load( $_POST["kid"] );

		if ( $tshirt->getId() == - 1 ) {
			tshirt_not_exist();
			return;
		}
	}else{
		tshirt_not_exist();
	}

	if(isset($_POST["edit_done"])){

		if(isset($_POST["name"])){
			if(strlen(trim($_POST["name"])) === 0){
				$error = true;
				$errmsg[] = "<li>Es wurde kein Name angegeben</li>";
			}
		}

		if(isset($_POST["size"])){
			if(strlen(trim($_POST["size"])) === 0){
				$error = true;
				$errmsg[] = "<li>Es wurde keine Gr&ouml;&szlig;e angegeben</li>";
			}else {
				if (!in_array($_POST["size"],Shirt::getsizes())){
					$error = true;
					$errmsg[] = "<li>Die angegebene Gr&ouml;&szlig;e existiert nicht</li>";
				}
			}

		}

		if(isset($_POST["preis"])){
			if(!preg_match("/[0-9]+/",$_POST["preis"])){
				$error = true;
				$errmsg[] = "<li>Der angegebene Preis ist ung&uuml;ltig</li>";
			}
		}

		if(!$error) {
			$tshirt->setName( $_POST["name"] );
			$tshirt->setSize( $_POST["size"] );
			$tshirt->setPreis( $_POST["preis"] );

			if($tshirt->save()){
				echo '
					<div class="notice updated">
						<p>Die &Auml;nderungen wurden gespeichert</p>
					</div>
				';

				tshirt_head(true);
				show_tshirts();
				return;
			}else{
				tshirt_head();
				echo '
					<div class="notice error">
						Es ist ein Fehler beim bearbeiten des T-Shirts aufgetreten
					</div>
				';
			}

		}else{
			tshirt_head();
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
		$_POST["name"] = $tshirt->getName();
		$_POST["size"] = $tshirt->getSize();
		$_POST["preis"] = $tshirt->getPreis();
	}

	if(!isset($_POST["edit_done"])){
		tshirt_head();
	}

	echo'
		<form action="" method="post">
				<table class="form-table">
					<tbody>
						<tr>
							<th>Tshirtbezeichnung:</th>
							<td>
								<input type="text" name="name" required="required" size="50" value="'.(isset($_POST["name"]) ? $_POST["name"]: "").'" />
							</td>
						</tr>
						<tr>
							<th>Gr&ouml;&szlig;e:</th>
							<td>
								<select name="size" required="required">';

						foreach (Shirt::getsizes() as $size){
							echo '<option value="'.$size.'" '.(isset($_POST["size"]) && $size == $_POST["size"] ? 'selected="selected"':'' ).' >'.$size.'</option>';
						}

						echo'
								</select>
							</td>
						</tr>
						<tr>
							<th>Preis:</th>
							<td>
								<input id="mteil" min="0" pattern="[0-9]+" required="required" name="preis" value="'.(isset($_POST["preis"]) ? $_POST["preis"]: "0").'" style="width: 30px;height: 20px;"/>&euro;
							</td>
						</tr>
					</tbody>
				</table>
						
				<p>&nbsp;</p>
				<button type="submit" name="edit_done" class="button button-primary" >
					Speichern
			  	</button >&nbsp;&nbsp;
				<a class="button" style="color: #900" href="'.menu_page_url('tshirts',false).'">
					<span class="dashicons dashicons-no" style="margin-top: 4px;"></span>Abbrechen
				</a>
				<input type="hidden" name="kid" value="'.$_POST["kid"].'" />
		</form>
	';

}

function delete_tshirt(){
	global $wpdb;

	if(isset($_POST["kid"])){

		if(preg_match("/[0-9]+/",$_POST["kid"])){

			$query = $wpdb->prepare("DELETE FROM ".$wpdb->prefix."cw_shirt WHERE id=%d",$_POST["kid"]);
			$wpdb->query($query);

			$query = $wpdb->prepare("DELETE FROM ".$wpdb->prefix."cw_user_shirt WHERE shirt_id=%d ",$_POST["kid"]);
			$wpdb->query($query);

			tshirt_head(true);
			echo '
				<div class="notice updated">
					<p>Das angegebene T-Shirt wurde entfernt<br />
					Alle Teilnehmer mit diesem Shirt besitzen nun keins mehr</p>
				</div>
			';
			show_tshirts();
			return;

		}else{
			tshirt_not_exist();
			return;
		}
	}else{
		tshirt_not_exist();
		return;
	}

}

function tshirt_not_exist(){
	tshirt_head(true);
	echo '
				<div class="notice error">
					<p>Das angegebene T-Shirt existiert nicht!</p>
				</div>
			';
	show_tshirts();
}

?>