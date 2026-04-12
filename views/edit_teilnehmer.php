<?php
/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 20.10.2016
 * Time: 16:40
 */

function edit_teilnehmer( $id ) {

	global $wpdb;
	$kurse   = get_all_kurse();
	$options = new Options( $wpdb );
	$options->load();
	$month = array(
		"",
		"Januar",
		"Februar",
		"M&auml;rz",
		"April",
		"Mai",
		"Juni",
		"Juli",
		"August",
		"September",
		"Oktober",
		"November",
		"Dezember"
	);
	$ret   = "";

	$gotit = array(
		"Flyer/Plakate",
		"Freunde",
		"Zeitung",
		"Lehrer/Dozenten",
		"Messen",
		"RT-Labor"
	);

	$teilnehmer = new Teilnehmer( $wpdb );

	if ( $teilnehmer->load( $id ) ) {

		$ret .= '
	<form action="" method="post">
		<div class="register col-md-7 col-sm-12 col-xs-12" style="overflow: auto;clear: both">
		
			<div>
				<span>Vorname:</span><br />
				<input type="text" name="vorname" id="reg_vorname"  value="'.esc_attr($teilnehmer->getVorname()).'" required="required"/>
			</div>

			<div>
				<span>Nachname:</span><br />
				<input type="text" name="nachname" id="reg_nachname"  value="'.esc_attr($teilnehmer->getNachname()).'" required="required"/>
			</div>

			<div>
				<span>EMail:</span><br />
				<input type="email" name="email" id="reg_email"  value="'.esc_attr($teilnehmer->getEmail()).'" required="required"/>
			</div>

			<div>
				<span>Stra&szlig;e &amp; Hausnummer:</span><br />
				<input type="text" name="strasse" id="reg_strasse"  value="'.esc_attr($teilnehmer->getStr()).'" required="required"/>
			</div>

			<div>
				<span>PLZ / Ort:</span><br />
				<div style="width: 100%;">
					<div style="clear: both"></div>
					<div style="width: 100px;">
						<input type="text" style="width: 75px;float: left;" required="required" name="plz" id="reg_plz"  value="'.esc_attr($teilnehmer->getPlz()).'"/>
					</div>
					<div style="margin-left: 80px;width: auto;">
						<input type="text" style="width: 100%" required="required" name="ort" id="reg_ort"  value="'.esc_attr($teilnehmer->getOrt()).'"/>
					</div>
				</div>
			</div>
		
			<div>
				<span>Geburtstag:</span><br />
				<select name="gbd" id="reg_gbd" >';
		for ( $d = 1; $d<=31; $d ++ ) {
			$ret .= '<option value="'.$d.'" '.( $d == date( 'd', strtotime( $teilnehmer->getGeb() ) ) ? 'selected="selected"' : '' ).'>'.$d.'</option>"';
		}
		$ret .= '
				</select>
				<select name="gbm" id="reg_gbm" >';
		for ( $m = 1; $m<=12; $m ++ ) {
			$ret .= '<option value="'.$m.'" '.( $m == date( 'm', strtotime( $teilnehmer->getGeb() ) ) ? 'selected="selected"' : '' ).'>'.$month[ $m ].'</option>"';
		}
		$ret .= '
				</select>
				<select name="gby" id="reg_gby" >';
		for ( $y = date( "Y" ); $y>=1900; $y -- ) {
			$ret .= '<option value="'.$y.'" '.( $y == date( 'Y', strtotime( $teilnehmer->getGeb() ) ) ? 'selected="selected"' : '' ).'>'.$y.'</option>"';
		}
		$ret .= '
				</select>
			</div>
			
			<div>
				<span>Ich bin:</span><br />			
				<input type="radio" name="paytype" value="1" '.( $teilnehmer->get_paytype() == 1 ? 'checked="checked"' : '' ).' required="required" /> Schüler:in/Student:in (<b>Teilnahmebeitrag:&nbsp;'.esc_html($options->getTeilnahmePreis()).'€</b>)<br />
				<input type="radio" name="paytype" value="2" '.( $teilnehmer->get_paytype() == 2 ? 'checked="checked"' : '' ).' /> Alumni (<b>Teilnahmebeitrag:&nbsp;'.esc_html($options->get_teilnahme_preis_alumni()).'€</b>)
			</div>
			
			<div>
				<span>(Hoch-)Schule/Arbeitsstätte:</span><br />
				<input type="text" required="required" name="schule" id="reg_schule"  value="'.esc_attr($teilnehmer->getSchule()).'"/>
			</div>
			
			<div>
				<span>W&auml;hle deinen Kurs:</span><br />
				<select style="width: 100%" name="kurs" id="reg_kurs" >';

		foreach ( $kurse as $kurs ) {
			$ret .= '<option value="'.esc_attr($kurs->getId()).'" '.( $kurs->getId() == $teilnehmer->getKurs()->getId() ? 'selected="selected"' : '' ).' >'.esc_html($kurs->getName()).' ( '.esc_html( $kurs->getMaxTeilnehmer() - $kurs->getTeilnehmer() ).' Pl&auml;tze frei )</option>';
		}

		$ret .= '</select>	
			</div>';

		$ret .= '
			<div>
				<span>Ich bin:</span><br />
				<input type="radio" name="food" value="Kein Vegetarier" '.( $teilnehmer->getEssen() == "Kein Vegetarier" ? 'checked="checked"' : '' ).' required="required"/> Kein:e Vegetarier:in / Veganer:in <br />
				<input type="radio" name="food" value="Vegetarier" '.( $teilnehmer->getEssen() == "Vegetarier" ? 'checked="checked"' : '' ).'/> Vegetarier:in <br />
				<input type="radio" name="food" value="Veganer" '.( $teilnehmer->getEssen() == "Veganer" ? 'checked="checked"' : '' ).'/> Veganer:in <br />
				<input type="radio" name="food" value="2" '.( $teilnehmer->getEssen() != "Kein Vegetarier" && $teilnehmer->getEssen() != "Vegetarier" && $teilnehmer->getEssen() != "Veganer" ? 'checked="checked"' : '' ).'/> Sonstiges:
				<input type="text" name="food_sonst" id="reg_food_sonst"  value="'.esc_attr( $teilnehmer->getEssen() != "Kein Vegetarier" && $teilnehmer->getEssen() != "Vegetarier" && $teilnehmer->getEssen() != "Veganer" ? $teilnehmer->getEssen() : "" ).'"/>
			</div>	
						
			<div>
				<span>Ich kenne die Campuswoche:</span><br />
				<input type="radio" name="gotit" '.( $teilnehmer->getGotit() == "Flyer/Plakate" ? 'checked="checked"' : '' ).' value="Flyer/Plakate" required="required"/>&nbsp;...von Flyern / Plakaten<br />
				<input type="radio" name="gotit" '.( $teilnehmer->getGotit() == "Freunde" ? 'checked="checked"' : '' ).'value="Freunde"/>&nbsp;...von Freund:innen<br />
				<input type="radio" name="gotit" '.( $teilnehmer->getGotit() == "Zeitung" ? 'checked="checked"' : '' ).'value="Zeitung"/>&nbsp;...von der Zeitung<br />
				<input type="radio" name="gotit" '.( $teilnehmer->getGotit() == "Lehrer/Dozenten" ? 'checked="checked"' : '' ).'value="Lehrer/Dozenten"/>&nbsp;...von Lehrer:innen / Dozent:innen<br />
				<input type="radio" name="gotit" '.( $teilnehmer->getGotit() == "Messen" ? 'checked="checked"' : '' ).'value="Messen"/>&nbsp;...von Messen<br />
				<input type="radio" name="gotit" '.( $teilnehmer->getGotit() == "RT-Labor" ? 'checked="checked"' : '' ).'value="RT-Labor"/>&nbsp;...vom RT-Labor<br />
				<input type="radio" name="gotit" '.( !in_array( $teilnehmer->getGotit(), $gotit ) ? 'checked="checked"' : '' ).'value="6"/>&nbsp;...von anderer Quelle<br />
				<input type="text" name="gotit_sonst" id="reg_gotit_sonst"  value="'.esc_attr( !in_array( $teilnehmer->getGotit(), $gotit ) ? $teilnehmer->getGotit() : '' ).'"/>
			</div>	
			
			<div>
				<span>Sonstiges und Anmerkungen:</span>
				<textarea style="width: 100%" name="sonstiges" id="reg_sonstiges" >'.esc_textarea($teilnehmer->getSonstiges()).'</textarea>
			</div>
			
			<div style="display:flex;align-items:center;gap:10px;margin-top:4px;">
			    <span>Teilnahme bezahlt?</span>
				<input type="checkbox" name="payed" value="1" id="reg_payed" '.( $teilnehmer->getPayed() ? 'checked="checked"' : '' ).'/>
			</div>

			<div style="display:flex;align-items:center;gap:10px;margin-top:4px;">
			    <span>Kursleiter:in?</span>
				<input type="checkbox" name="is_course_leader" value="1" id="reg_is_course_leader" '.( $teilnehmer->getIsCourseLeader() ? 'checked="checked"' : '' ).'/>
			</div>
			
			<input type="hidden" name="id" id="reg_id" value="'.$teilnehmer->getId().'" />
			<input type="hidden" name="save" value="done" id="reg_save" />
						
		</div>
	</form>
	';

		return $ret;

	} else {
		return "";
	}

}

function save_edit() {
	global $wpdb;

	$teilnehmer = new Teilnehmer( $wpdb );
	$options    = new Options( $wpdb );
	$options->load();

	if ( $teilnehmer->load( absint( $_POST["id"] ) ) ) {

		$old_name   = $teilnehmer->getVorname() . ' ' . $teilnehmer->getNachname();
		$old_values = array(
			'Vorname'       => $teilnehmer->getVorname(),
			'Nachname'      => $teilnehmer->getNachname(),
			'Email'         => $teilnehmer->getEmail(),
			'Kurs'          => ( $teilnehmer->getKurs() ? $teilnehmer->getKurs()->getName() : '' ),
			'Bezahlt'       => $teilnehmer->getPayed(),
			'Kursleiter:in' => $teilnehmer->getIsCourseLeader(),
			'Typ'           => $teilnehmer->get_paytype(),
		);

		$gby = absint( $_POST["gby"] );
		$gbm = absint( $_POST["gbm"] );
		$gbd = absint( $_POST["gbd"] );
		$geb = $gby . '-' . ( $gbm < 10 ? '0' . $gbm : $gbm ) . '-' . ( $gbd < 10 ? '0' . $gbd : $gbd );

		$allowed_food  = array( 'Kein Vegetarier', 'Vegetarier', 'Veganer' );
		$allowed_gotit = array( 'Flyer/Plakate', 'Freunde', 'Zeitung', 'Lehrer/Dozenten', 'Messen', 'RT-Labor' );

		$food  = sanitize_text_field( $_POST["food"] ?? '' );
		$gotit = sanitize_text_field( $_POST["gotit"] ?? '' );

		$new_vorname  = sanitize_text_field( $_POST["vorname"] ?? '' );
		$new_nachname = sanitize_text_field( $_POST["nachname"] ?? '' );
		$new_paytype  = absint( $_POST["paytype"] ?? 1 );
		$new_payed    = isset( $_POST["payed"] ) ? 1 : 0;
		$new_is_kl    = isset( $_POST["is_course_leader"] ) ? 1 : 0;
		$new_kurs_id  = absint( $_POST["kurs"] ?? 0 );

		$teilnehmer->setVorname( $new_vorname );
		$teilnehmer->setNachname( $new_nachname );
		$teilnehmer->setEmail( sanitize_email( $_POST["email"] ?? '' ) );
		$teilnehmer->setStr( sanitize_text_field( $_POST["strasse"] ?? '' ) );
		$teilnehmer->setPlz( sanitize_text_field( $_POST["plz"] ?? '' ) );
		$teilnehmer->setOrt( sanitize_text_field( $_POST["ort"] ?? '' ) );
		$teilnehmer->setGeb( $geb );
		$teilnehmer->setSchule( sanitize_text_field( $_POST["schule"] ?? '' ) );
		$teilnehmer->setEssen( in_array( $food, $allowed_food ) ? $food : sanitize_text_field( $_POST["food_sonst"] ?? '' ) );
		$teilnehmer->setGotit( in_array( $gotit, $allowed_gotit ) ? $gotit : sanitize_text_field( $_POST["gotit_sonst"] ?? '' ) );
		$teilnehmer->setSonstiges( sanitize_textarea_field( $_POST["sonstiges"] ?? '' ) );
		$teilnehmer->setPayed( $new_payed );
		$teilnehmer->setIsCourseLeader( $new_is_kl );
		$teilnehmer->set_paytype( $new_paytype );

		$tnp = ( $teilnehmer->get_paytype() == 1 ? $options->getTeilnahmePreis() : $options->get_teilnahme_preis_alumni() );
		$teilnehmer->set_to_pay( $tnp );

		$regkurs = new Kurs( $wpdb );
		$regkurs->load( $new_kurs_id );
		$teilnehmer->setKurs( $regkurs );

		$teilnehmer->save();

		$new_name   = $new_vorname . ' ' . $new_nachname;
		$new_values = array(
			'Vorname'       => $new_vorname,
			'Nachname'      => $new_nachname,
			'Email'         => sanitize_email( $_POST["email"] ?? '' ),
			'Kurs'          => $regkurs->getName(),
			'Bezahlt'       => $new_payed,
			'Kursleiter:in' => $new_is_kl,
			'Typ'           => $new_paytype,
		);
		$diff = [];
		foreach ( $old_values as $k => $v ) {
			if ( (string)$v !== (string)$new_values[$k] ) {
				$diff[] = $k . ': ' . $v . ' → ' . $new_values[$k];
			}
		}
		cw_log_history( 'teilnehmer', $teilnehmer->getId(), $new_name, 'update', implode( ', ', $diff ) );

		return "ok";
	}

	return "no";

}

function delete_user() {
	global $wpdb;

	$teilnehmer = new Teilnehmer( $wpdb );

	if ( $teilnehmer->load( $_POST["value"] ) ) {
		$name = $teilnehmer->getVorname() . ' ' . $teilnehmer->getNachname();
		$id   = $teilnehmer->getId();
		$teilnehmer->delete();
		cw_log_history( 'teilnehmer', $id, $name, 'delete', '' );
	} else {
		return "no";
	}

}

?>