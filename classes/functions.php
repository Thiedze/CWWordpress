<?php
/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 13.10.2016
 * Time: 09:48
 */

/**
 * @return Kurs[]|null
 */
function get_all_kurse(){
	global $wpdb;
	$ret = array();
	$i = -1;

	if($obj = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."cw_kurse ORDER BY id DESC")){

		foreach ( $obj as $res ) {
			$i++;
			$ret[$i] = new Kurs($wpdb);
			$ret[$i]->load($res->id);
		}

		return $ret;

	}else{
		return null;
	}

}

/**
 * @return Shirt[]|null
 */
function get_all_tshirts(){
	global $wpdb;
	$ret = array();
	$i = -1;

	if($obj = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."cw_shirt ORDER BY name ASC")){

		foreach ( $obj as $res ) {
			$i++;
			$ret[$i] = new Shirt($wpdb);
			$ret[$i]->load($res->id);
		}

		return $ret;

	}else{
		return null;
	}

}

/**
 * @return Event[]|null
 */
function get_all_events(){
	global $wpdb;
	$ret = array();
	$i = -1;

	if($obj = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."cw_events ORDER BY event_day,event_start,event_end ASC")){

		foreach ( $obj as $res ) {
			$i++;
			$ret[$i] = new Event($wpdb);
			$ret[$i]->load($res->id);
		}

		return $ret;

	}else{
		return null;
	}
}

/**
 * @return Event[]|null
 */
function get_all_events_by_day($day_id){
	global $wpdb;
	$ret = array();
	$i = -1;

	if($obj = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."cw_events WHERE event_day=".$day_id." ORDER BY event_start,event_end ASC")){

		foreach ( $obj as $res ) {
			$i++;
			$ret[$i] = new Event($wpdb);
			$ret[$i]->load($res->id);
		}

		return $ret;

	}else{
		return null;
	}
}

/**
 * @param $kursid
 * @return Teilnehmer[]
 */
function get_all_teilnehmer_by_kurs($kursid){
    global $wpdb;
    $res = array();

    $results = $wpdb->get_results("SELECT u.id FROM ".$wpdb->prefix."cw_user_kurs uk LEFT JOIN ".$wpdb->prefix."cw_user u ON uk.user_id=u.id WHERE uk.kurs_id=".$kursid." ORDER BY u.vorname ASC");

    if($results){
        $i = -1;
        foreach ($results as $result){
            $i++;
            $res[$i] = new Teilnehmer($wpdb);
            $res[$i]->load($result->id);
        }
    }else{
        return null;
    }

    return $res;
}

function calc_age($datum){
    global $wpdb;
    $options = new Options($wpdb);
    $options->load();

    return date_diff(date_create($datum), date_create($options->getCwStart()))->y;
}

/**
 * @return Teilnehmer[]|null
 */
function get_all_teilnehmer(){
	global $wpdb;
	$res = array();

	$results = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."cw_user ORDER BY nachname ASC");

	if($results){
		$i = -1;
		foreach ($results as $result){
			$i++;
			$res[$i] = new Teilnehmer($wpdb);
			$res[$i]->load($result->id);
		}
	}else{
		return null;
	}

	return $res;
}

function get_kurs_by_beauty($bname){
    $bname = trim(strtolower($bname));
    $result = get_all_kurse();

    if(is_array($result)){
        foreach ($result as $item){
            if($bname == strtolower($item->getBeautyName())){
                return $item;
            }
        }
    }

    return null;

}

/**
 * @param $db wpdb
 * @param $text
 * @param $teil Teilnehmer
 *
 * @return mixed
 */
function substitue_email_text($db,$text,$teil){

	$opt = new Options($db);
	$opt->load();

	return preg_replace_callback('/{{(\w+)}}/i',
		function($match) use ($teil,$opt){
			switch (strtolower($match[1])){
				case 'name':
					return $teil->getNachname();
					break;

				case 'vorname':
					return $teil->getVorname();
					break;

				case 'fullname':
					return $teil->getVorname().' '.$teil->getNachname();
					break;

				case 'betrag':
					return $opt->getTeilnahmePreis();
					break;

				case 'betragshirt':
					return $teil->getTshirt()->getPreis();
					break;

				case 'kurs':
					return $teil->getKurs()->getName();
					break;

				case 'shirt':
					return (strlen($teil->getTshirt()->getName()) > 0 ? $teil->getTshirt()->getName().' '.$teil->getTshirt()->getSize(): 'Kein T-Shirt');
					break;

				case 'gesamt':
					return $teil->getTshirt()->getPreis() + $opt->getTeilnahmePreis();
					break;
			}
		}
		,$text);
}

?>