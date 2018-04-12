<?php
/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 20.10.2016
 * Time: 16:40
 */

function edit_teilnehmer($id){

	global $wpdb;
	$kurse = get_all_kurse();
	$shirts = get_all_tshirts();
	$options = new Options($wpdb);
	$options->load();
	$month = array("", "Januar", "Februar", "M&auml;rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
	$ret = "";

    $gotit = array("Flyer/Plakate",
                    "Freunde",
                    "Zeitung",
                    "Lehrer/Dozenten",
                    "Messen",
                    "RT-Labor"
    );

	$teilnehmer = new Teilnehmer($wpdb);

	if($teilnehmer->load($id)){

		$ret .= '
	<form action="" method="post">
		<div class="register col-md-7 col-sm-12 col-xs-12" style="overflow: auto;clear: both">
		
			<div>
				<span>Vorname:</span><br />
				<input type="text" name="vorname" id="reg_vorname"  value="' . $teilnehmer->getVorname() . '" required="required"/>
			</div>
			
			<div>
				<span>Nachname:</span><br />
				<input type="text" name="nachname" id="reg_nachname"  value="' . $teilnehmer->getNachname() . '" required="required"/>
			</div>
			
			<div>
				<span>EMail:</span><br />
				<input type="email" name="email" id="reg_email"  value="' . $teilnehmer->getEmail() . '" required="required"/>
			</div>
						
			<div>
				<span>Stra&szlig;e &amp; Hausnummer:</span><br />
				<input type="text" name="strasse" id="reg_strasse"  value="' . $teilnehmer->getStr() . '" required="required"/>
			</div>
			
			<div>
				<span>PLZ / Ort:</span><br />
				<div style="width: 100%;">
					<div style="clear: both"></div>
					<div style="width: 100px;">
						<input type="text" style="width: 75px;float: left;" required="required" name="plz" id="reg_plz"  value="' . $teilnehmer->getPlz() . '"/>
					</div>
					<div style="margin-left: 80px;width: auto;">
						<input type="text" style="width: 100%" required="required" name="ort" id="reg_ort"  value="' . $teilnehmer->getOrt() . '"/>
					</div>
				</div>
			</div>
		
			<div>
				<span>Geburtstag:</span><br />
				<select name="gbd" id="reg_gbd" >';
		for ($d = 1; $d <= 31; $d++) {
			$ret .= '<option value="' . $d . '" ' . ($d == date('d',strtotime($teilnehmer->getGeb())) ? 'selected="selected"' : '') . '>' . $d . '</option>"';
		}
		$ret .= '
				</select>
				<select name="gbm" id="reg_gbm" >';
		for ($m = 1; $m <= 12; $m++) {
			$ret .= '<option value="' . $m . '" ' . ($m == date('m',strtotime($teilnehmer->getGeb())) ? 'selected="selected"' : '') . '>' . $month[$m] . '</option>"';
		}
		$ret .= '
				</select>
				<select name="gby" id="reg_gby" >';
		for ($y = date("Y"); $y >= 1900; $y--) {
			$ret .= '<option value="' . $y . '" ' . ($y == date('Y',strtotime($teilnehmer->getGeb())) ? 'selected="selected"' : '') . '>' . $y . '</option>"';
		}
		$ret .= '
				</select>
			</div>
			
			<div>
				<span>(Hoch-)Schule:</span><br />
				<input type="text" required="required" name="schule" id="reg_schule"  value="' . $teilnehmer->getSchule() . '"/>
			</div>
			
			<div>
				<span>W&auml;hle deinen Kurs:</span><br />
				<select style="width: 100%" name="kurs" id="reg_kurs" >';

		foreach ($kurse as $kurs) {
				$ret .= '<option value="' . $kurs->getId() . '" ' . ($kurs->getId() == $teilnehmer->getKurs()->getId() ? 'selected="selected"' : '') . ' >' . $kurs->getName() . ' ( ' . ($kurs->getMaxTeilnehmer() - $kurs->getTeilnehmer()) . ' Pl&auml;tze frei )</option>';
		}

		$ret .= '</select>	
			</div>';

			$ret .= '
			<div>
				<span>W&auml;hle dein T-Shirt:</span>
				<!--<p style="color: #0076aa;padding: 2px;">' . $options->getTextShirt() . '</p>-->
				<select style="width: 100%" title="' . $options->getTextShirt() . '" name="shirt" id="reg_shirt" >
				<option value="-1">Kein T-Shirt</option>
				';

			foreach ($shirts as $shirt) {
				$ret .= '<option value="' . $shirt->getId() . '" ' . ($shirt->getId() == $teilnehmer->getTshirt()->getId() ? 'selected="selected"' : '') . ' >' . $shirt->getName() . ' ' . $shirt->getSize() . '  ( + ' . $shirt->getPreis() . '&euro; )</option>';
			}

			$ret .= '</select>	
			</div>';

		$ret .= '			
			<div>
				<span>Ich bin:</span><br />
				<input type="radio" name="food" value="Kein Vegetarier" ' . ($teilnehmer->getEssen() == "Kein Vegetarier" ? 'checked="checked"' : '') . ' required="required"/> Kein Vegetarier <br />
				<input type="radio" name="food" value="Vegetarier" ' . ($teilnehmer->getEssen() == "Vegetarier" ? 'checked="checked"' : '') . '/> Vegetarier <br />
				<input type="radio" name="food" value="2" ' . ($teilnehmer->getEssen() != "Kein Vegetarier" && $teilnehmer->getEssen() != "Vegetarier" ? 'checked="checked"' : '') . '/> Sonstiges:
				<input type="text" name="food_sonst" id="reg_food_sonst"  value="' . ($teilnehmer->getEssen() != "Kein Vegetarier" && $teilnehmer->getEssen() != "Vegetarier" ? $teilnehmer->getEssen(): ""). '"/>
			</div>	
						
			<div>
				<span>Ich kenne die Campuswoche:</span><br />
				<input type="radio" name="gotit" ' . ($teilnehmer->getGotit() == "Flyer/Plakate" ? 'checked="checked"' : '') . ' value="Flyer/Plakate" required="required"/>&nbsp;...von Flyern / Plakaten<br />
				<input type="radio" name="gotit" ' . ($teilnehmer->getGotit() == "Freunde" ? 'checked="checked"' : '') . 'value="Freunde"/>&nbsp;...von Freunden<br />
				<input type="radio" name="gotit" ' . ($teilnehmer->getGotit() == "Zeitung" ? 'checked="checked"' : '') . 'value="Zeitung"/>&nbsp;...von der Zeitung<br />
				<input type="radio" name="gotit" ' . ($teilnehmer->getGotit() == "Lehrer/Dozenten" ? 'checked="checked"' : '') . 'value="Lehrer/Dozenten"/>&nbsp;...von Lehrern / Dozenten<br />
				<input type="radio" name="gotit" ' . ($teilnehmer->getGotit() == "Messen" ? 'checked="checked"' : '') . 'value="Messen"/>&nbsp;...von Messen<br />
				<input type="radio" name="gotit" ' . ($teilnehmer->getGotit() == "RT-Labor" ? 'checked="checked"' : '') . 'value="RT-Labor"/>&nbsp;...vom RT-Labor<br />
				<input type="radio" name="gotit" ' . (!in_array($teilnehmer->getGotit(),$gotit) ? 'checked="checked"' : '') . 'value="6"/>&nbsp;...von anderer Quelle<br />
				<input type="text" name="gotit_sonst" id="reg_gotit_sonst"  value="' . (!in_array($teilnehmer->getGotit(),$gotit) ? $teilnehmer->getGotit() : '') . '"/>
			</div>	
			
			<div>
				<span>Sonstiges und Anmerkungen:</span>
				<textarea style="width: 100%" name="sonstiges" id="reg_sonstiges" >' . $teilnehmer->getSonstiges() . '</textarea>
			</div>
			
			<div>
				<input type="checkbox" name="payed" value="1" id="reg_payed" '.($teilnehmer->getPayed() ? 'checked="checked"':'').' style="float: right; margin-right: 200px;margin-top: 3px;"/>
			    <span style="margin-right: 15px;float: left">Teilnahme bezahlt?</span>
			</div>
			
			<div>
				<input type="checkbox" name="shirt_payed" id="reg_shirt_payed" value="1" '.($teilnehmer->getShirtPayed() ? 'checked="checked"':'').' style="float: right; margin-right: 200px;margin-top: 3px;"/>
			    <span style="margin-right: 15px;float: left">TShirt bezahlt?</span>			    
			</div>
			
			<input type="hidden" name="id" id="reg_id" value="'.$teilnehmer->getId().'" />
			<input type="hidden" name="save" value="done" id="reg_save" />
						
		</div>
	</form>
	';

		return $ret;

	}else{
		return "";
	}

}

function save_edit(){
    global $wpdb;

    $teilnehmer = new Teilnehmer($wpdb);

    if($teilnehmer->load($_POST["id"])) {

        $geb = $_POST["gby"] . '-' . ($_POST["gbm"] < 10 ? '0' . $_POST["gbm"] : $_POST["gbm"]) . '-' . ($_POST["gbd"] < 10 ? '0' . $_POST["gbd"] : $_POST["gbd"]);

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
        $teilnehmer->setPayed((isset($_POST["payed"]) ? $_POST["payed"]: 0));
        $teilnehmer->setShirtPayed((isset($_POST["shirt_payed"]) ? $_POST['shirt_payed']: 0));


        $regshirt = new Shirt($wpdb);
        if ($regshirt->load($_POST["shirt"])) {
            $teilnehmer->setTshirt($regshirt);
        }else{
            $teilnehmer->setTshirt(null);
        }


        $regkurs = new Kurs($wpdb);
        $regkurs->load($_POST["kurs"]);
        $teilnehmer->setKurs($regkurs);

        $teilnehmer->save();

        return "ok";
    }

    return "no";

}

function delete_user(){
    global $wpdb;

    $teilnehmer = new Teilnehmer($wpdb);

    if($teilnehmer->load($_POST["value"])){
        $teilnehmer->delete();
    }else{
        return "no";
    }

}

?>