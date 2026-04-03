<?php
/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 10.10.2016
 * Time: 17:06
 */

function options_head(){
	echo'
	<div class="wrap">
	<h1>Allgemeine Optionen</h1>';
}

function show_options(){
	global $wpdb;
	$options = new Options($wpdb);
	$options->load();

	echo '
		<div class="notice notice-info" style="width: 300px; float: right;z-index: 1000">
			<strong>Info für die Fronseiten:</strong><br />
			[front_register] = Registrierungsformular<br />
			[front_kurse] = Auflistung der Kurse<br />
			[front_calendar] = Das Wochenprogramm
			<hr style="margin: 8px 0" />
			<strong>Platzhalter:</strong><br />
			<span style="font-size: 9pt">
				<b>E-Mail Anmeldung:</b><br />
				<a class="ed">{{Name}}</a> = Nachname<br />
				<a class="ed">{{Vorname}}</a> = Vorname<br />
				<a class="ed">{{Fullname}}</a> = Vorname Nachname<br />
				<a class="ed">{{Betrag}}</a> = Teilnahmegebühr (ohne €)<br />
				<a class="ed">{{Kurs}}</a> = Gebuchter Kurs<br />
				<a class="ed">{{Gesamt}}</a> = Gesamtbetrag (ohne €)<br />
				<a class="ed">{{lezterTagShopBestellung}}</a> = 4 Wochen vor CW-Start<br />
				<br />
				<b>Text oberhalb Anmeldeformular:</b><br />
				<a class="ed">{{Betrag}}</a> = Teilnahmegebühr Schüler/Stud.<br />
				<a class="ed">{{BetragAlumni}}</a> = Teilnahmegebühr Alumni
			</span>
		</div>
		<form method="post" style="float: left">
			<table class="form-table">
				<tr>
					<th>Anmeldung geöffnet</th>
					<td>
						<div id="sw_back_1" style="" class="'.($options->getRegisterEnabled() ? "sw_on":"sw_off").'">
							<div id="sw_1" data="sw_ch_1" data-type="switch" style="" ></div>
						</div>
						<input type="checkbox" id="sw_ch_1" style="display: none" value="1" name="open_reg" '.($options->getRegisterEnabled() ? "checked=\"checked\"":"").'/>
					</td>
				</tr>
				<tr>
					<th>Nur für angemeldete Benutzer</th>
					<td>
						<div id="sw_back_3" style="" class="'.($options->getRegisterLoggedInOnly() ? "sw_on":"sw_off").'">
							<div id="sw_3" data="sw_ch_3" data-type="switch" style="" ></div>
						</div>
						<input type="checkbox" id="sw_ch_3" style="display: none" value="1" name="logged_in_only" '.($options->getRegisterLoggedInOnly() ? "checked=\"checked\"":"").'/>
						<p class="description" style="display:inline;margin-left:8px">Anmeldeformular nur für eingeloggte WordPress-Benutzer anzeigen</p>
					</td>
				</tr>
				<tr>
					<th>Teilnahmegebühr Schüler/Studenten</th>
					<td>
						<input data="number" value="'.$options->getTeilnahmePreis().'" style="width: 30px;height: 20px;" required="required" name="gebuehr"/>&euro;
					</td>
				</tr>
				<tr>
					<th>Teilnahmegebühr Alumni</th>
					<td>
						<input data="number" value="'.$options->get_teilnahme_preis_alumni().'" style="width: 30px;height: 20px;" required="required" name="gebuehr_alumni"/>&euro;
					</td>
				</tr>
				<tr>
					<th>Campuswoche Start</th>
					<td>
						<div class="button-group">
							<input type="text" value="'.date("d.m.Y",strtotime($options->getCwStart())).'" id="cw_start" style="border-radius: 3px 0 0 3px;margin: 0;float: left;width: 100px;height: 28px;" required="required" />
							<button id="dp" type="button" class="button dashicons dashicons-calendar" style="width: 35px;margin: 0;float: left;border-radius: 0 3px 3px 0;height: 28px;" />
						</div>
						<input type="hidden" id="tp_cw_start" name="cw_start" value="'.$options->getCwStart().'">
					</td>
				</tr>
				<tr>
					<th>Registrierung Start</th>
					<td>
						<div class="button-group">
							<input type="text" value="'.date("d.m.Y",strtotime($options->getRegisterStart())).'" id="reg_start" style="border-radius: 3px 0 0 3px;margin: 0;float: left;width: 100px;height: 28px;" required="required"/>
							<button id="dp_reg" type="button" class="button dashicons dashicons-calendar" style="width: 35px;margin: 0;float: left;border-radius: 0 3px 3px 0;height: 28px;" />
						</div>
						<input type="hidden" id="tp_reg_start" name="reg_start" value="'.$options->getRegisterStart().'">
					</td>
				</tr>
				<tr>
					<th>Text Registrierung geschlossen</th>
					<td>
						<textarea class="wp-editor-area" cols="80" rows="3" name="close_text">'.$options->getTextClosed().'</textarea>
					</td>
				</tr>
				<tr>
					<th>Text oberhalb Anmeldeformular</th>
					<td>
						<textarea class="wp-editor-area" cols="80" rows="3" name="register_text">'.$options->get_text_register().'</textarea>
						<div style="border: 1px solid #333333; padding: 0.8rem; background: rgba(255,84,0,0.15)">Beispieltext mit farblichem Hintergrund wie er dann über der Registirerung steht</div>
					</td>
				</tr>
				<tr>
					<th>Text Anmeldung Email</th>
					<td>
						<textarea class="wp-editor-area" cols="80" rows="5" id="mailtext" name="email_text">'.$options->getTextEmail().'</textarea>
					</td>
				</tr>
			</table>
			<div>
			<br />
				<button type="submit" name="speichern" class="button button-primary" >
					<span class="dashicons dashicons-yes" style="margin-top: 5px;"></span>Speichern
				</button>
				<button type="reset" name="speichern" class="button button-secondary delete" style="color: #900" >
					<span class="dashicons dashicons-no" style="margin-top: 5px;"></span>Abbrechen
				</button>
			</div>
		</form>


';
}

/**
 * @param $post $_POST
 */
function save_options($post){
	global $wpdb;
	$options = new Options($wpdb);

	$options->setRegisterEnabled((isset($post["open_reg"])? 1 : 0));
	$options->setRegisterLoggedInOnly((isset($post["logged_in_only"])? 1 : 0));
	$options->setTeilnahmePreis((isset($post["gebuehr"])? $post["gebuehr"] : 0));
	$options->set_teilnahme_preis_alumni((isset($post["gebuehr_alumni"])? $post["gebuehr_alumni"] : 0));
	$options->setCwStart((isset($post["cw_start"])? $post["cw_start"] : ""));
	$options->setRegisterStart((isset($post["reg_start"])? $post["reg_start"] : ""));
	$options->setTextClosed((isset($post["close_text"])? $post["close_text"] : ""));
	$options->setTextEmail((isset($post["email_text"])? $post["email_text"] : ""));
	$options->set_text_register((isset($post["register_text"])? $post["register_text"] : ""));

	if($options->save()){
		echo'
			<div class="updated notice">
                <p>Die &Auml;nderungen wurden gespeichert</p>
			</div>
		';
	}

}

?>
