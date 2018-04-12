<?php
/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 13.10.2016
 * Time: 11:16
 */

function register()
{
    global $wpdb;
    $kurse = get_all_kurse();
    $shirts = get_all_tshirts();
    $options = new Options($wpdb);
    $options->load();
    $month = array("", "Januar", "Februar", "M&auml;rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
    $ret = "";
    $error = false;
    $error_msg = '<ul style="list-style-type: none!important;margin: 0">';

    /**
     * Wir testen, ob die Anmeldung geöffnet ist.
     * Falls nicht kommt die geschlossen Meldung
     *
     * Wenn doch, dass prüfen wir, ob das Registierungsdatum auch passt.
     * Dies dient der automatischen Öffnung der Anmeldung....
     */
    if ($options->getRegisterEnabled() == 0) {
        $ret .= '
			<div>
			' . nl2br($options->getTextClosed()) . '
			</div>
		';
        return $ret;
    } else {
        if (strtotime($options->getRegisterStart()) > strtotime('now')) {
            $ret .= '
				<div>
				' . nl2br($options->getTextClosed()) . '
				</div>
			';
            return $ret;
        }
    }

    /**
     * Hier checken wir nun die Anmeldung
     */

    if (isset($_POST["registerme"])) {

        if (!isset($_POST["vorname"])) {
            $error = true;
            $error_msg .= '<li>Es wurde kein Vorname angegeben</li>';
        } else {
            if (strlen(trim($_POST["vorname"])) == 0) {
                $error = true;
                $error_msg .= '<li>Es wurde kein Vorname angegeben</li>';
            }
        }

        if (!isset($_POST["nachname"])) {
            $error = true;
            $error_msg .= '<li>Es wurde kein Nachname angegeben</li>';
        } else {
            if (strlen(trim($_POST["nachname"])) == 0) {
                $error = true;
                $error_msg .= '<li>Es wurde kein Nachname angegeben</li>';
            }
        }

        if (!isset($_POST["email"])) {
            $error = true;
            $error_msg .= '<li>Es wurde keine Email angegeben</li>';
        } else {
            if (strlen(trim($_POST["email"])) == 0) {
                $error = true;
                $error_msg .= '<li>Es wurde keine Email angegeben</li>';
            }

            if (strtolower($_POST["email"]) != strtolower($_POST["emailw"])) {
                $error = true;
                $error_msg .= '<li>Die EMailadressen stimmen nicht &uuml;berein</li>';
            }
        }

        if (!isset($_POST["strasse"])) {
            $error = true;
            $error_msg .= '<li>Es wurde keine Strasse angegeben</li>';
        } else {
            if (strlen(trim($_POST["strasse"])) == 0) {
                $error = true;
                $error_msg .= '<li>Es wurde keine Strasse angegeben</li>';
            }
        }

        if (!isset($_POST["plz"])) {
            $error = true;
            $error_msg .= '<li>Es wurde keine PLZ angegeben</li>';
        } else {
            if (strlen(trim($_POST["plz"])) == 0) {
                $error = true;
                $error_msg .= '<li>Es wurde kein PLZ angegeben</li>';
            }
        }

        if (!isset($_POST["ort"])) {
            $error = true;
            $error_msg .= '<li>Es wurde kein Ort angegeben</li>';
        } else {
            if (strlen(trim($_POST["ort"])) == 0) {
                $error = true;
                $error_msg .= '<li>Es wurde kein Ort angegeben</li>';
            }
        }

        if (!isset($_POST["gbd"]) || !isset($_POST["gbm"]) || !isset($_POST["gby"])) {
            $error = true;
            $error_msg .= '<li>Dein Geburtsdatum ist ung&uuml;tig</li>';
        } else {
            if (!checkdate($_POST["gbm"], $_POST["gbd"], $_POST["gby"])) {
                $error = true;
                $error_msg .= '<li>Dein Geburtsdatum ist ung&uuml;tig</li>';
            }
        }

        if (!isset($_POST["schule"])) {
            $error = true;
            $error_msg .= '<li>Es wurde keine (Hoch-)Schule angegeben</li>';
        } else {
            if (strlen(trim($_POST["schule"])) == 0) {
                $error = true;
                $error_msg .= '<li>Es wurde keine (Hoch-)Schule angegeben</li>';
            }
        }

        if (!isset($_POST["kurs"])) {
            $error = true;
            $error_msg .= '<li>Es wurde kein Kurs angegeben</li>';
        } else {
            $is_in = false;
            $full = false;
            foreach ($kurse as $kurs) {
                if ($_POST["kurs"] == $kurs->getId()) {
                    $is_in = true;

                    if ($kurs->getTeilnehmer() >= $kurs->getMaxTeilnehmer()) {
                        $full = true;
                    }
                    break;
                }
            }

            if (!$is_in) {
                $error = true;
                $error_msg .= '<li>Der gew&auml;hlte Kurs existiert nicht</li>';
            }

            if ($full) {
                $error = true;
                $error_msg .= '<li>Der gew&auml;hlte Kurs ist bereits voll</li>';
            }
        }

        if ($options->getShirtEnabled() == 1) {
            if (isset($_POST["shirt"])) {
                $is_in = false;
                foreach ($shirts as $shirt) {
                    if ($_POST["shirt"] == $shirt->getId()) {
                        $is_in = true;
                        break;
                    }
                }

                if (!$is_in && $_POST["shirt"] != "-1") {
                    $error = true;
                    $error_msg .= '<li>Das gew&auml;hlte T-Shirt existiert nicht</li>';
                }
            }
        } else {
            unset($_POST["shirt"]);
        }

        if (!isset($_POST["food"])) {
            $error = true;
            $error_msg .= '<li>Bitte w&auml;hle dein Essen</li>';
        } else {
            if ($_POST["food"] == 2) {
                if (strlen(trim($_POST["food_sonst"])) == 0) {
                    $error = true;
                    $error_msg .= '<li>Bitte gebe bei Essen bei "Sonstiges" etwas ins Feld ein</li>';
                }
            }
        }

        if (!isset($_POST["gotit"])) {
            $error = true;
            $error_msg .= '<li>Bitte w&auml;hle woher Du die Campuswoche kennst</li>';
        } else {
            if ($_POST["gotit"] == 2) {
                if (strlen(trim($_POST["gotit_sonst"])) == 0) {
                    $error = true;
                    $error_msg .= '<li>Bitte gebe bei "...von anderer Quelle" etwas ins Feld ein</li>';
                }
            }
        }

        if (!isset($_POST["check1"]) || !isset($_POST["check3"]) || !isset($_POST["check3"])) {
            $error = true;
            $error_msg .= '<li>Du musst die drei Erkl&auml;rungen noch best&auml;tigen</li>';
        }


        $error_msg .= '</ul>';

        if ($error) {
            $ret .= '
				<div style="padding: 3px;background: #fcc;border: 2px solid #900;margin: 0 15px;">
					<span style="font-weight: bold">Es sind Fehler bei der Anmeldung aufgetreten:</span>
					' . $error_msg . '
				</div>
			';
        } else {

            /**
             * Scheiß Datumsangaben :(
             * Erst einmal den Scheiß formatieren
             */

            $geb = $_POST["gby"] . '-' . ($_POST["gbm"] < 10 ? '0' . $_POST["gbm"] : $_POST["gbm"]) . '-' . ($_POST["gbd"] < 10 ? '0' . $_POST["gbd"] : $_POST["gbd"]);

            $teilnehmer = new Teilnehmer($wpdb);
            $teilnehmer->setVorname($_POST["vorname"]);
            $teilnehmer->setNachname($_POST["nachname"]);
            $teilnehmer->setEmail($_POST["email"]);
            $teilnehmer->setStr($_POST["strasse"]);
            $teilnehmer->setPlz($_POST["plz"]);
            $teilnehmer->setOrt($_POST["ort"]);
            $teilnehmer->setGeb($geb);
            $teilnehmer->setSchule($_POST["schule"]);
            $teilnehmer->setEssen(($_POST["food"] != "2" ? $_POST["food"] : $_POST["food_sonst"]));
            $teilnehmer->setGotit(($_POST["gotit"]) != "6" ? $_POST["gotit"] : $_POST["gotit_sonst"]);
            $teilnehmer->setSonstiges($_POST["sonstiges"]);
            $teilnehmer->setUuid(sha1(uniqid("CW", true)));
            $teilnehmer->save();
            $teilnehmer->get_id_from_uuid();

            $regshirt = new Shirt($wpdb);
            if ($regshirt->load($_POST["shirt"])) {
                $teilnehmer->setTshirt($regshirt);
            }

            $regkurs = new Kurs($wpdb);
            $regkurs->load($_POST["kurs"]);
            $teilnehmer->setKurs($regkurs);
            $teilnehmer->setPayed(0);
            $teilnehmer->setShirtPayed(0);

            $teilnehmer->save();

            /**
             * So der Teilnehmer ist gespeichert Jetzt muss noch die Mail rein
             * FIXME: ES FEHLT DIE MAIL!!!
             */

            $ret = '
				<div>
					<h4 style="font-weight: bold">Vielen Dank f&uuml;r deine Anmeldung</h4>
					<p>
						Die Gesamtkosten f&uuml;r die Teilnahme betragen <b>' . ($options->getTeilnahmePreis() + $teilnehmer->getTshirt()->getPreis()) . '</b>&euro;<br />
						Du erh&auml;lst von uns eine EMail mit allen weiteren Informationen.<br />
						Falls Du keine EMail von uns bekommen hast, gucke bitte in deinen SPAM-Ordner oder schreibe uns!<br />
					</p>
				</div>
			';

	        $to = $teilnehmer->getEmail();
	        $subject = 'Deine Anmeldung Campuswoche '.date("Y");
	        $body = nl2br(substitue_email_text($wpdb,$options->getTextEmail(),$teilnehmer));
	        $headers = array('Content-Type: text/html; charset=UTF-8','From: Campuswochenteam <info@campuswoche.de>');

	        wp_mail( $to, $subject, $body, $headers );

            return $ret;

        }

    } else {

        /**
         * Nur eine Definition, falls noch nichts gesendet wurde und damit das WP-Debug nicht so rumschreit.
         */

        $_POST["vorname"] = null;
        $_POST["nachname"] = null;
        $_POST["email"] = null;
        $_POST["emailw"] = null;
        $_POST["strasse"] = null;
        $_POST["plz"] = null;
        $_POST["ort"] = null;
        $_POST["gbd"] = null;
        $_POST["gbm"] = null;
        $_POST["gby"] = null;
        $_POST["schule"] = null;
        $_POST["kurs"] = null;
        $_POST["shirt"] = null;
        $_POST["food"] = null;
        $_POST["gotit"] = null;
        $_POST["food_sonst"] = null;
        $_POST["gotit_sonst"] = null;
        $_POST["sonstiges"] = null;
    }

    /**
     *
     */
    $kurse = get_all_kurse();
    $shirts = get_all_tshirts();

    $ret .= '
	<form action="" method="post">
		<div class="register col-md-7 col-sm-12 col-xs-12" style="height: auto;overflow: auto;clear: both">
		
			<div>
				<span>Vorname:</span><br />
				<input type="text" name="vorname" value="' . $_POST["vorname"] . '" required="required"/>
			</div>
			
			<div>
				<span>Nachname:</span><br />
				<input type="text" name="nachname" value="' . $_POST["nachname"] . '" required="required"/>
			</div>
			
			<div>
				<span>EMail:</span><br />
				<input type="email" name="email" value="' . $_POST["email"] . '" required="required"/>
			</div>
			
			<div>
				<span>EMail wiederholen:</span><br />
				<input type="email" name="emailw" value="' . $_POST["emailw"] . '" required="required"/>
			</div>
			
			<div>
				<span>Stra&szlig;e &amp; Hausnummer:</span><br />
				<input type="text" name="strasse" value="' . $_POST["strasse"] . '" required="required"/>
			</div>
			
			<div>
				<span>PLZ / Ort:</span><br />
				<div style="width: 100%;">
					<div style="clear: both"></div>
					<div style="width: 100px;">
						<input type="text" style="width: 75px;float: left;" required="required" name="plz" value="' . $_POST["plz"] . '"/>
					</div>
					<div style="margin-left: 80px;width: auto;">
						<input type="text" style="width: 100%" required="required" name="ort" value="' . $_POST["ort"] . '"/>
					</div>
				</div>
			</div>
		
			<div>
				<span>Geburtstag:</span><br />
				<select name="gbd">';
    for ($d = 1; $d <= 31; $d++) {
        $ret .= '<option value="' . $d . '" ' . ($d == $_POST["gbd"] ? 'selected="selected"' : '') . '>' . $d . '</option>"';
    }
    $ret .= '
				</select>
				<select name="gbm">';
    for ($m = 1; $m <= 12; $m++) {
        $ret .= '<option value="' . $m . '" ' . ($m == $_POST["gbm"] ? 'selected="selected"' : '') . '>' . $month[$m] . '</option>"';
    }
    $ret .= '
				</select>
				<select name="gby">';
    for ($y = date("Y"); $y >= 1900; $y--) {
        $ret .= '<option value="' . $y . '" ' . ($y == $_POST["gby"] ? 'selected="selected"' : '') . '>' . $y . '</option>"';
    }
    $ret .= '
				</select>
			</div>
			
			<div>
				<span>(Hoch-)Schule:</span><br />
				<input type="text" required="required" name="schule" value="' . $_POST["schule"] . '"/>
			</div>
			
			<div>
				<span>W&auml;hle deinen Kurs:</span><br />
				<select style="width: 100%" name="kurs">';

    foreach ($kurse as $kurs) {
        if ($kurs->getIs_open() == 1 && $kurs->getTeilnehmer() < $kurs->getMaxTeilnehmer()) {
            $ret .= '<option value="' . $kurs->getId() . '" ' . ($kurs->getId() == $_POST["kurs"] ? 'selected="selected"' : '') . ' >' . $kurs->getName() . ' ( ' . max(($kurs->getMaxTeilnehmer() - $kurs->getTeilnehmer()),0) . ' Pl&auml;tze frei )</option>';
        }
    }

    $ret .= '</select>	
			</div>';

    if ($options->getShirtEnabled() == 1) {
        $ret .= '
			<div>
				<span>W&auml;hle dein T-Shirt:</span>
				<p style="color: #0076aa;padding: 2px;">' . $options->getTextShirt() . '</p>
				<select style="width: 100%" title="' . $options->getTextShirt() . '" name="shirt">
				<option value="-1">Kein T-Shirt</option>
				';


        foreach ($shirts as $shirt) {
            $ret .= '<option value="' . $shirt->getId() . '" ' . ($shirt->getId() == $_POST["shirt"] ? 'selected="selected"' : '') . ' >' . $shirt->getName() . ' ' . $shirt->getSize() . '  ( + ' . $shirt->getPreis() . '&euro; )</option>';
        }

        $ret .= '</select>	
			</div>';
    } else {
        $ret .= '
				<div>
					<span>W&auml;hle dein T-Shirt:</span><br />
					<i style="color: #0076aa;">Es ist k&ouml;nnen keine T-Shirts mehr mitbestellt werden</i>
				</div>
			';

    }

    $ret .= '			
			<div>
				<span>Ich bin:</span><br />
				<input type="radio" name="food" value="Kein Vegetarier" ' . ($_POST["food"] == "Kein Vegetarier" ? 'checked="checked"' : '') . ' required="required"/> Kein Vegetarier <br />
				<input type="radio" name="food" value="Vegetarier" ' . ($_POST["food"] == "Vegetarier" ? 'checked="checked"' : '') . '/> Vegetarier <br />
				<input type="radio" name="food" value="2" ' . ($_POST["food"] == "2" ? 'checked="checked"' : '') . '/> Sonstiges:
				<input type="text" name="food_sonst" value="' . $_POST["food_sonst"] . '"/>
			</div>	
						
			<div>
				<span>Ich kenne die Campuswoche:</span><br />
				<input type="radio" name="gotit" ' . ($_POST["gotit"] == "Flyer/Plakate" ? 'checked="checked"' : '') . ' value="Flyer/Plakate" required="required"/>&nbsp;...von Flyern / Plakaten<br />
				<input type="radio" name="gotit" ' . ($_POST["gotit"] == "Freunde" ? 'checked="checked"' : '') . 'value="Freunde"/>&nbsp;...von Freunden<br />
				<input type="radio" name="gotit" ' . ($_POST["gotit"] == "Zeitung" ? 'checked="checked"' : '') . 'value="Zeitung"/>&nbsp;...von der Zeitung<br />
				<input type="radio" name="gotit" ' . ($_POST["gotit"] == "Lehrer/Dozenten" ? 'checked="checked"' : '') . 'value="Lehrer/Dozenten"/>&nbsp;...von Lehrern / Dozenten<br />
				<input type="radio" name="gotit" ' . ($_POST["gotit"] == "Messen" ? 'checked="checked"' : '') . 'value="Messen"/>&nbsp;...von Messen<br />
				<input type="radio" name="gotit" ' . ($_POST["gotit"] == "RT-Labor" ? 'checked="checked"' : '') . 'value="RT-Labor"/>&nbsp;...vom RT-Labor<br />
				<input type="radio" name="gotit" ' . ($_POST["gotit"] == "6" ? 'checked="checked"' : '') . 'value="6"/>&nbsp;...von anderer Quelle<br />
				<input type="text" name="gotit_sonst" value="' . $_POST["gotit_sonst"] . '"/>
			</div>	
			
			<div>
				<span>Sonstiges und Anmerkungen:</span>
				<textarea style="width: 100%" name="sonstiges">' . $_POST["sonstiges"] . '</textarea>
			</div>
						
		</div>
			<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
				<p>&nbsp;</p>
			</div>
			<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
				<div style="background: #fcc;padding: 10px;font-weight: bold;border: 1px solid #900">
					<input type="checkbox" required="required" name="check1" value="ok"/>&nbsp;
					Ich habe die <a target="_blank" href="http://campuswoche.de/wp-content/uploads/2016/04/Datenschutzerklaerung_Campuswoche.pdf">Datenschutzerkl&auml;rung</a> gelesen, verstanden und akzeptiere diese!
				</div>
				<br />
				<div style="background: #fcc;padding: 10px;font-weight: bold;border: 1px solid #900">
					<input type="checkbox" required="required" name="check2" value="ok"/>&nbsp;
					Wenn ich das 18. Lebensjahr noch nicht erreicht habe, werde ich unverz&uuml;glich nach dieser Anmeldung die unterschriebene <a target="_blank" href="http://campuswoche.de/wp-content/uploads/2017/07/erklaerung_der_eltern_cw2017.pdf">Einverst&auml;dniserkl&auml;rung</a> zusenden bzw. zufaxen.<br />
					Au&szlig;erdem erkl&auml;re ich, dass ich zum Beginn der Campuswoche das 16. Lebensjahr erreicht haben werde.
				</div>
				<br />
				<div style="background: #fcc;padding: 10px;font-weight: bold;border: 1px solid #900">
					<input type="checkbox" required="required" name="check3" value="ok"/>&nbsp;
					Den Teilnahmebetrag von ' . $options->getTeilnahmePreis() . '&euro; (+ Extrabetr&auml;ge f&uuml;r gew&auml;hlte Shirts) werde ich umgehend &uuml;berweisen.
				</div>
			</div>
			<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
				<p>&nbsp;</p>
			</div>
			<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
				<input type="submit" name="registerme" value="Anmelden-&gt;" style="float: right"/>
			</div>
	</form>
	';

    //$ret = str_replace('required="required"','',$ret);

    return $ret;

}

?>