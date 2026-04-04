<?php
/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 13.10.2016
 * Time: 11:16
 */
session_start();

function register() {
	global $wpdb;
	$kurse   = get_all_kurse();
	$options = new Options( $wpdb );
	$options->load();
	$month     = array(
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
	$ret       = "";
	$error     = false;
	$error_msg = '<ul style="list-style-type: none!important;margin: 0">';

	/**
	 * Wir testen, ob die Anmeldung geöffnet ist.
	 * Falls nicht kommt die geschlossen Meldung
	 *
	 * Wenn doch, dass prüfen wir, ob das Registierungsdatum auch passt.
	 * Dies dient der automatischen Öffnung der Anmeldung....
	 */
	if ( $options->getRegisterEnabled() == 0 ) {
		$ret .= '
			<div>
			'.nl2br( $options->getTextClosed() ).'
			</div>
		';

		return $ret;
	}

	$start_reached = strtotime( $options->getRegisterStart() ) <= strtotime( 'now' );

	// Startdatum erreicht → Formular für alle anzeigen
	if ( $start_reached ) {
		// weiter zum Formular
	} elseif ( $options->getRegisterLoggedInOnly() == 1 && is_user_logged_in() ) {
		// Startdatum noch nicht erreicht, aber eingeloggt + Option aktiv → trotzdem Formular
	} else {
		// Startdatum noch nicht erreicht → geschlossene Meldung
		$ret .= '
			<div>
			'.nl2br( $options->getTextClosed() ).'
			</div>
		';
		return $ret;
	}

	/**
	 * Hier checken wir nun die Anmeldung
	 */

	if ( isset( $_POST["registerme"] ) ) {

		if ( !isset( $_POST["captcha"] ) ) {
			$error     = true;
			$error_msg .= '<li>Es wurde kein Captcha angegeben</li>';
		} else {
			if ( $_POST["captcha"] != $_SESSION["captcha"] ) {
				$error     = true;
				$error_msg .= '<li>Das Captcha wurde nicht richtig gelöst</li>';
			}
		}

		if ( !isset( $_POST["vorname"] ) ) {
			$error     = true;
			$error_msg .= '<li>Es wurde kein Vorname angegeben</li>';
		} else {
			if ( strlen( trim( $_POST["vorname"] ) ) == 0 ) {
				$error     = true;
				$error_msg .= '<li>Es wurde kein Vorname angegeben</li>';
			}
		}

		if ( !isset( $_POST["nachname"] ) ) {
			$error     = true;
			$error_msg .= '<li>Es wurde kein Nachname angegeben</li>';
		} else {
			if ( strlen( trim( $_POST["nachname"] ) ) == 0 ) {
				$error     = true;
				$error_msg .= '<li>Es wurde kein Nachname angegeben</li>';
			}
		}

		if ( !isset( $_POST["email"] ) ) {
			$error     = true;
			$error_msg .= '<li>Es wurde keine Email angegeben</li>';
		} else {
			if ( strlen( trim( $_POST["email"] ) ) == 0 ) {
				$error     = true;
				$error_msg .= '<li>Es wurde keine Email angegeben</li>';
			}

			if ( strtolower( $_POST["email"] ) != strtolower( $_POST["emailw"] ) ) {
				$error     = true;
				$error_msg .= '<li>Die EMailadressen stimmen nicht &uuml;berein</li>';
			}
		}

		if ( !isset( $_POST["strasse"] ) ) {
			$error     = true;
			$error_msg .= '<li>Es wurde keine Strasse angegeben</li>';
		} else {
			if ( strlen( trim( $_POST["strasse"] ) ) == 0 ) {
				$error     = true;
				$error_msg .= '<li>Es wurde keine Strasse angegeben</li>';
			}
		}

		if ( !isset( $_POST["plz"] ) ) {
			$error     = true;
			$error_msg .= '<li>Es wurde keine PLZ angegeben</li>';
		} else {
			if ( strlen( trim( $_POST["plz"] ) ) == 0 ) {
				$error     = true;
				$error_msg .= '<li>Es wurde kein PLZ angegeben</li>';
			}
		}

		if ( !isset( $_POST["ort"] ) ) {
			$error     = true;
			$error_msg .= '<li>Es wurde kein Ort angegeben</li>';
		} else {
			if ( strlen( trim( $_POST["ort"] ) ) == 0 ) {
				$error     = true;
				$error_msg .= '<li>Es wurde kein Ort angegeben</li>';
			}
		}

		if ( !isset( $_POST["gbd"] ) || !isset( $_POST["gbm"] ) || !isset( $_POST["gby"] ) ) {
			$error     = true;
			$error_msg .= '<li>Dein Geburtsdatum ist ung&uuml;tig</li>';
		} else {
			if ( !checkdate( $_POST["gbm"], $_POST["gbd"], $_POST["gby"] ) ) {
				$error     = true;
				$error_msg .= '<li>Dein Geburtsdatum ist ung&uuml;tig</li>';
			}
		}

		if ( !isset( $_POST["schule"] ) ) {
			$error     = true;
			$error_msg .= '<li>Es wurde keine (Hoch-)Schule angegeben</li>';
		} else {
			if ( strlen( trim( $_POST["schule"] ) ) == 0 ) {
				$error     = true;
				$error_msg .= '<li>Es wurde keine (Hoch-)Schule angegeben</li>';
			}
		}

		if ( !isset( $_POST["kurs"] ) ) {
			$error     = true;
			$error_msg .= '<li>Es wurde kein Kurs angegeben</li>';
		} else {
			$is_in = false;
			$full  = false;
			foreach ( $kurse as $kurs ) {
				if ( $_POST["kurs"] == $kurs->getId() ) {
					$is_in = true;

					if ( $kurs->getTeilnehmer()>=$kurs->getMaxTeilnehmer() ) {
						$full = true;
					}
					break;
				}
			}

			if ( !$is_in ) {
				$error     = true;
				$error_msg .= '<li>Der gew&auml;hlte Kurs existiert nicht</li>';
			}

			if ( $full ) {
				$error     = true;
				$error_msg .= '<li>Der gew&auml;hlte Kurs ist bereits voll</li>';
			}
		}

		if ( !isset( $_POST["food"] ) ) {
			$error     = true;
			$error_msg .= '<li>Bitte w&auml;hle dein Essen</li>';
		} else {
			if ( $_POST["food"] == 2 ) {
				if ( strlen( trim( $_POST["food_sonst"] ) ) == 0 ) {
					$error     = true;
					$error_msg .= '<li>Bitte gebe bei Essen bei "Sonstiges" etwas ins Feld ein</li>';
				}
			}
		}

		if ( !isset( $_POST["gotit"] ) ) {
			$error     = true;
			$error_msg .= '<li>Bitte w&auml;hle woher Du die Campuswoche kennst</li>';
		} else {
			if ( $_POST["gotit"] == 2 ) {
				if ( strlen( trim( $_POST["gotit_sonst"] ) ) == 0 ) {
					$error     = true;
					$error_msg .= '<li>Bitte gebe bei "...von anderer Quelle" etwas ins Feld ein</li>';
				}
			}
		}

		if ( !isset( $_POST["paytype"] ) ) {
			$error     = true;
			$error_msg .= '<li>Bitte w&auml;hle wer Du bist</li>';
		} else {
			if ( $_POST["paytype"] != 1 && $_POST["paytype"] != 2 ) {
				$error     = true;
				$error_msg .= '<li>Bitte w&auml;hle bei "Ich bin" ein gültiges Feld</li>';
			}
		}

		if ( !isset( $_POST["check1"] ) || !isset( $_POST["check3"] ) || !isset( $_POST["check3"] ) ) {
			$error     = true;
			$error_msg .= '<li>Du musst die drei Erkl&auml;rungen noch best&auml;tigen</li>';
		}

		if ( isset( $_POST["gbd"] ) && isset( $_POST["gbm"] ) && isset( $_POST["gby"] ) ) {
			$geb_check  = $_POST["gby"] . '-' . ( $_POST["gbm"] < 10 ? '0' . $_POST["gbm"] : $_POST["gbm"] ) . '-' . ( $_POST["gbd"] < 10 ? '0' . $_POST["gbd"] : $_POST["gbd"] );
			$ref_date   = $options->getCwStart() ? $options->getCwStart() : 'today';
			$age        = date_diff( date_create( $geb_check ), date_create( $ref_date ) )->y;
			if ( $age < 16 ) {
				$error     = true;
				$error_msg .= '<li>Du musst zum Start der Campuswoche mindestens 16 Jahre alt sein</li>';
			}
		}


		$error_msg .= '</ul>';

		if ( $error ) {
			$ret .= '
				<div style="padding: 3px;background: #fcc;border: 2px solid #900;margin: 0 15px;">
					<span style="font-weight: bold">Es sind Fehler bei der Anmeldung aufgetreten:</span>
					'.$error_msg.'
				</div>
			';
		} else {

			$geb = $_POST["gby"].'-'.( $_POST["gbm"]<10 ? '0'.$_POST["gbm"] : $_POST["gbm"] ).'-'.( $_POST["gbd"]<10 ? '0'.$_POST["gbd"] : $_POST["gbd"] );

			$teilnehmer = new Teilnehmer( $wpdb );

			$teilnehmer->setVorname( $_POST["vorname"] );
			$teilnehmer->setNachname( $_POST["nachname"] );
			$teilnehmer->setEmail( $_POST["email"] );
			$teilnehmer->setStr( $_POST["strasse"] );
			$teilnehmer->setPlz( $_POST["plz"] );
			$teilnehmer->setOrt( $_POST["ort"] );
			$teilnehmer->setGeb( $geb );
			$teilnehmer->setSchule( $_POST["schule"] );
			$teilnehmer->setEssen( ( $_POST["food"] != "2" ? $_POST["food"] : $_POST["food_sonst"] ) );
			$teilnehmer->setGotit( ( $_POST["gotit"] ) != "6" ? $_POST["gotit"] : $_POST["gotit_sonst"] );
			$teilnehmer->setSonstiges( $_POST["sonstiges"] );
			$teilnehmer->setUuid( sha1( uniqid( "CW", true ) ) );
			$teilnehmer->set_paytype( $_POST["paytype"] );
			$tnp = ( $teilnehmer->get_paytype() == 1 ? $options->getTeilnahmePreis() : $options->get_teilnahme_preis_alumni() );
			$teilnehmer->set_to_pay( $tnp );
			$teilnehmer->save();
			$teilnehmer->get_id_from_uuid();

			$regkurs = new Kurs( $wpdb );
			$regkurs->load( $_POST["kurs"] );
			$teilnehmer->setKurs( $regkurs );
			$teilnehmer->setPayed( 0 );

			$teilnehmer->save();

			$ret = '
				<div>
					<h4 style="font-weight: bold">Vielen Dank f&uuml;r deine Anmeldung</h4>
					<p>
						Die Gesamtkosten f&uuml;r die Teilnahme betragen <b>'.$tnp.'</b>&euro;<br />
						Du erh&auml;ltst von uns eine EMail mit allen weiteren Informationen.<br />
						Falls Du keine EMail von uns bekommen hast, gucke bitte in deinen SPAM-Ordner oder schreibe uns!<br />
					</p>
				</div>
			';

			$to      = $teilnehmer->getEmail();
			$subject = 'Deine Anmeldung Campuswoche '.date( "Y" );
			$body    = nl2br( substitue_email_text( $wpdb, $options->getTextEmail(), $teilnehmer ) );
			$headers = array(
				'Content-Type: text/html; charset=UTF-8',
				'From: Campuswochenteam <info@campuswoche.de>'
			);

			wp_mail( $to, $subject, $body, $headers );

			return $ret;

		}

	} else {

		$_POST["vorname"]     = null;
		$_POST["nachname"]    = null;
		$_POST["email"]       = null;
		$_POST["emailw"]      = null;
		$_POST["strasse"]     = null;
		$_POST["plz"]         = null;
		$_POST["ort"]         = null;
		$_POST["gbd"]         = null;
		$_POST["gbm"]         = null;
		$_POST["gby"]         = null;
		$_POST["schule"]      = null;
		$_POST["kurs"]        = null;
		$_POST["food"]        = null;
		$_POST["gotit"]       = null;
		$_POST["food_sonst"]  = null;
		$_POST["gotit_sonst"] = null;
		$_POST["sonstiges"]   = null;
		$_POST["paytype"]     = null;
	}

	$kurse = get_all_kurse();

	if(strlen(trim($options->get_text_register())) > 0){
		$register_text = str_ireplace(
			array('{{Betrag}}', '{{BetragAlumni}}'),
			array($options->getTeilnahmePreis(), $options->get_teilnahme_preis_alumni()),
			$options->get_text_register()
		);
		$ret.= '<div style="border: 1px solid #333333; padding: 0.8rem; background: rgba(255,84,0,0.15)">'.nl2br($register_text).'</div><br />';
	}

	$ret .= '
	<form action="" method="post">
		<div class="register col-md-7 col-sm-12 col-xs-12" style="height: auto;overflow: auto;clear: both">

			<div>
				<span>Vorname:</span><br />
				<input type="text" name="vorname" value="'.$_POST["vorname"].'" required="required"/>
			</div>

			<div>
				<span>Nachname:</span><br />
				<input type="text" name="nachname" value="'.$_POST["nachname"].'" required="required"/>
			</div>

			<div>
				<span>EMail:</span><br />
				<input type="email" name="email" value="'.$_POST["email"].'" required="required"/>
			</div>

			<div>
				<span>EMail wiederholen:</span><br />
				<input type="email" name="emailw" value="'.$_POST["emailw"].'" required="required"/>
			</div>

			<div>
				<span>Stra&szlig;e &amp; Hausnummer:</span><br />
				<input type="text" name="strasse" value="'.$_POST["strasse"].'" required="required"/>
			</div>

			<div>
				<span>PLZ / Ort:</span><br />
				<div style="width: 100%;">
					<div style="clear: both"></div>
					<div style="width: 100px;">
						<input type="text" style="width: 75px;float: left;" required="required" name="plz" value="'.$_POST["plz"].'"/>
					</div>
					<div style="margin-left: 80px;width: auto;">
						<input type="text" style="width: 100%" required="required" name="ort" value="'.$_POST["ort"].'"/>
					</div>
				</div>
			</div>

			<div>
				<span>Geburtstag:</span><br />
				<select name="gbd">';
	for ( $d = 1; $d<=31; $d ++ ) {
		$ret .= '<option value="'.$d.'" '.( $d == $_POST["gbd"] ? 'selected="selected"' : '' ).'>'.$d.'</option>"';
	}
	$ret .= '
				</select>
				<select name="gbm">';
	for ( $m = 1; $m<=12; $m ++ ) {
		$ret .= '<option value="'.$m.'" '.( $m == $_POST["gbm"] ? 'selected="selected"' : '' ).'>'.$month[ $m ].'</option>"';
	}
	$ret .= '
				</select>
				<select name="gby">';
	for ( $y = date( "Y" ); $y>=1900; $y -- ) {
		$ret .= '<option value="'.$y.'" '.( $y == $_POST["gby"] ? 'selected="selected"' : '' ).'>'.$y.'</option>"';
	}
	$ret .= '
				</select>
			<div id="age-warning" style="display:none;color:#900;font-weight:bold;margin-top:5px">
				Du musst zum Start der Campuswoche mindestens 16 Jahre alt sein.
			</div>
			</div>

			<div>
				<span>Ich bin:</span><br />
				<input type="radio" name="paytype" value="1" '.( $_POST["paytype"] == 1 ? 'checked="checked"' : '' ).' required="required" /> Schüler/Student (<b>Teilnahmebeitrag:&nbsp;'.$options->getTeilnahmePreis().'€</b>)<br />
				<input type="radio" name="paytype" value="2" '.( $_POST["paytype"] == 2 ? 'checked="checked"' : '' ).' /> Alumni (<b>Teilnahmebeitrag:&nbsp;'.$options->get_teilnahme_preis_alumni().'€</b>)
				<div id="paytype-warning" style="display:none;color:#900;font-weight:bold;margin-top:5px">
					Bitte w&auml;hle aus, ob du Sch&uuml;ler/Student oder Alumni bist.
				</div>
			</div>

			<div>
				<span>(Hoch-)Schule:</span><br />
				<input type="text" required="required" name="schule" value="'.$_POST["schule"].'"/>
				<div style="font-size: 0.8rem !important; font-style: normal">Falls Du kein Schüler/Student bist trage hier z.B. deine Arbeitsst&auml;tte ein</div>
			</div>

			<div>
				<span>W&auml;hle deinen Kurs:</span><br />
				<select style="width: 100%" name="kurs">';

	foreach ( $kurse as $kurs ) {
		if ( $kurs->getIs_open() == 1 && $kurs->getTeilnehmer()<$kurs->getMaxTeilnehmer() ) {
			$ret .= '<option value="'.$kurs->getId().'" '.( $kurs->getId() == $_POST["kurs"] ? 'selected="selected"' : '' ).' >'.$kurs->getName().' ( '.max( ( $kurs->getMaxTeilnehmer() - $kurs->getTeilnehmer() ), 0 ).' Pl&auml;tze frei )</option>';
		}
	}

	$ret .= '</select>
			</div>';

	$ret .= '
			<div>
				<span>Ich bin:</span><br />
				<input type="radio" name="food" value="Kein Vegetarier" '.( $_POST["food"] == "Kein Vegetarier" ? 'checked="checked"' : '' ).' required="required"/> Kein Vegetarier <br />
				<input type="radio" name="food" value="Vegetarier" '.( $_POST["food"] == "Vegetarier" ? 'checked="checked"' : '' ).'/> Vegetarier <br />
				<input type="radio" name="food" value="Veganer" '.( $_POST["food"] == "Veganer" ? 'checked="checked"' : '' ).'/> Veganer <br />
				<input type="radio" name="food" value="2"'.( $_POST["food"] == "2" ? 'checked="checked"' : '' ).'/> Sonstiges:
				<input type="text" name="food_sonst" value="'.$_POST["food_sonst"].'"/>
			</div>

			<div>
				<span>Ich kenne die Campuswoche:</span><br />
				<input type="radio" name="gotit" '.( $_POST["gotit"] == "Flyer/Plakate" ? 'checked="checked"' : '' ).' value="Flyer/Plakate" required="required"/>&nbsp;...von Flyern / Plakaten<br />
				<input type="radio" name="gotit" '.( $_POST["gotit"] == "Freunde" ? 'checked="checked"' : '' ).'value="Freunde"/>&nbsp;...von Freunden<br />
				<input type="radio" name="gotit" '.( $_POST["gotit"] == "Zeitung" ? 'checked="checked"' : '' ).'value="Zeitung"/>&nbsp;...von der Zeitung<br />
				<input type="radio" name="gotit" '.( $_POST["gotit"] == "Lehrer/Dozenten" ? 'checked="checked"' : '' ).'value="Lehrer/Dozenten"/>&nbsp;...von Lehrern / Dozenten<br />
				<input type="radio" name="gotit" '.( $_POST["gotit"] == "Messen" ? 'checked="checked"' : '' ).'value="Messen"/>&nbsp;...von Messen<br />
				<input type="radio" name="gotit" '.( $_POST["gotit"] == "RT-Labor" ? 'checked="checked"' : '' ).'value="RT-Labor"/>&nbsp;...vom RT-Labor<br />
				<input type="radio" name="gotit" '.( $_POST["gotit"] == "6" ? 'checked="checked"' : '' ).'value="6"/>&nbsp;...von anderer Quelle<br />
				<input type="text" name="gotit_sonst" value="'.$_POST["gotit_sonst"].'"/>
			</div>

			<div>
				<span>Sonstiges und Anmerkungen:</span>
				<textarea style="width: 100%" name="sonstiges">'.$_POST["sonstiges"].'</textarea>
			</div>

			<div>
				<span>Bitte löse die Rechnung im Captcha:</span>
				<div style="width: 100%;">
					<div style="clear: both"></div>
						<div style="width: 150px">
						<img style="float: left" src="'.plugin_dir_url( __FILE__ ).'/cimg.php" />
						</div>
						<div style="margin-left: 140px;width: 100px;">
							<input type="text" name="captcha" value="" required="required"/>
						</div>
				</div>
			</div>

		</div>
			<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
				<p>&nbsp;</p>
			</div>
			<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
				<div style="background: #fcc;padding: 10px;font-weight: bold;border: 1px solid #900">
					<input type="checkbox" required="required" name="check1" value="ok"/>&nbsp;
					Ich habe die <a target="_blank" href="https://campuswoche.de/wp-content/uploads/2016/04/DSEVorlageCampuswoche.pdf">Datenschutzerkl&auml;rung</a> gelesen, verstanden und akzeptiere diese!
				</div>
				<br />
				<div id="check2-container" style="background: #fcc;padding: 10px;font-weight: bold;border: 1px solid #900;display:none">
					<input type="checkbox" name="check2" value="ok"/>&nbsp;
					Ich werde unverz&uuml;glich nach dieser Anmeldung die unterschriebene <a target="_blank" href="https://campuswoche.de/wp-content/uploads/2024/03/Einverstaendniserklaerung-einer_eines-Erziehungsberechtigten-1.pdf">Einverst&auml;ndniserkl&auml;rung</a> dem Campuswochen Orga Team zusenden.				</div>
				<br />
				<div style="background: #fcc;padding: 10px;font-weight: bold;border: 1px solid #900">
					<input type="checkbox" required="required" name="check3" value="ok"/>&nbsp;
					Den Teilnahmebetrag von <span id="tnb"></span>&euro; werde ich umgehend &uuml;berweisen.
				</div>
			</div>
			<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
				<p>&nbsp;</p>
			</div>
			<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
				<input type="submit" id="btn-registerme" name="registerme" value="Anmelden-&gt;" style="float: right"/>
			</div>

			<input type="hidden" id="pt1" value="'.$options->getTeilnahmePreis().'" />
			<input type="hidden" id="pt2" value="'.$options->get_teilnahme_preis_alumni().'" />
			<input type="hidden" id="cw-start" value="'.$options->getCwStart().'" />
	</form>
	';

	return $ret;

}

session_write_close()
?>
