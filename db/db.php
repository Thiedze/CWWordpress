<?php
/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 10.10.2016
 * Time: 13:08
 */

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

/* (!!!)HINWEIS:
 * DIE SQL-ABFRAGEN SOLLTEN SO FORMATIERT BLEIBEN WIE SIE SIND, DAMIT DBDELTA DAS RICHTIG AUSFÜHREN KANN!!!!!
 * ALSO NICHT ÄNDERN !!!
 */

/**
 * @param wpdb $db
 */
function init_database($db){

	$userdb = " CREATE TABLE ".$db->prefix."cw_user (
		id int(11) NOT NULL AUTO_INCREMENT,
		vorname VARCHAR(255) NOT NULL,
		nachname VARCHAR(255) NOT NULL,
		email VARCHAR(255) NOT NULL,
		str VARCHAR(255) NOT NULL,
		plz VARCHAR(20) NOT NULL,
		ort VARCHAR(255) NOT NULL,
		geb DATE NOT NULL,
		schule VARCHAR(255) NOT NULL,
		essen VARCHAR(255) NOT NULL,
		sonstiges TEXT,
		gotit VARCHAR(255),
		uuid VARCHAR(255),
		regdate DATETIME,
		payed int(1),
		shirt_payed int(1),
		PRIMARY KEY  (id)
	) ".$db->get_charset_collate();

	$kursdb = "CREATE TABLE ".$db->prefix."cw_kurse (
		id int(11) NOT NULL AUTO_INCREMENT,
		name VARCHAR(255) NOT NULL,
		beauty_name VARCHAR(255) NOT NULL,
		beschreibung TEXT,
		max_teilnehmer int(11),
		bild VARCHAR(255),
		show_front int(1),
		is_open int(1),
		PRIMARY KEY  (id)
	) ".$db->get_charset_collate();

	$tshirt = "CREATE TABLE ".$db->prefix."cw_shirt (
		id int(11) NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL,
		size varchar(10) NOT NULL,
		preis int(5),
		PRIMARY KEY  (id)
	) ".$db->get_charset_collate();

	$shirt_to_user = "CREATE TABLE ".$db->prefix."cw_user_shirt (
		user_id int(11) NOT NULL,
		shirt_id int(11) NOT NULL,
		PRIMARY KEY  (user_id)
	) ".$db->get_charset_collate();

	$kurs_to_user = "CREATE TABLE ".$db->prefix."cw_user_kurs (
		user_id int(11) NOT NULL,
		kurs_id int(11) NOT NULL,
		PRIMARY KEY  (user_id)
	) ".$db->get_charset_collate();

	$cw_options = "CREATE TABLE ".$db->prefix."cw_options (
		register_enabled int(1) NOT NULL,
		shirt_enabled int(1) NOT NULL,
		teilnahme_preis int(3) NOT NULL,
		text_closed TEXT,
		text_shirt TEXT,
		text_email TEXT,
		cw_start DATE,
		register_start DATE
	) ".$db->get_charset_collate();

	$cw_events = "CREATE TABLE ".$db->prefix."cw_events (
        id int(11) NOT NULL AUTO_INCREMENT,
        event_start int(11) NOT NULL,
        event_end int(11) NOT NULL,
        event_day int(11) NOT NULL,
        event_name TEXT NOT NULL,
        event_subtext TEXT,
        event_description TEXT,
        event_color TEXT,
        PRIMARY KEY  (id)
	) ".$db->get_charset_collate();

	dbDelta($userdb);
	dbDelta($kursdb);
	dbDelta($tshirt);
	dbDelta($shirt_to_user);
	dbDelta($kurs_to_user);
	dbDelta($cw_options);
	dbDelta($cw_events);

	/**
	 * Lase alle Kurse, wenn das NULL ist, wurde das Plugin noch nie aktiviert.
	 * Demnach erstellen wir einen Kurs Sonstiges mit der ID 1
	 */
	$kurse = get_all_kurse();

	if($kurse == null) {
		$db->insert(
			$db->prefix . 'cw_kurse',
			array(
				'id'             => 1,
				'name'           => 'Sonstiges',
				'beschreibung'   => 'Sonstiges',
				'max_teilnehmer' => 10,
				'bild'           => 'null',
				'show_front'     => 0,
				'is_open'        => 1
			)
		);
	}

	$res = $db->get_row("SELECT count(*) as c FROM ".$db->prefix."cw_options");

	if($res->c == 0){
		//$db->insert($db->prefix."cw_options",array(0,0,50,"closed","","",date("Y-m-d",time()),date("Y-m-d",time())));
		$db->query("INSERT INTO ".$db->prefix."cw_options VALUES(0,0,50,'closed','','','".date("Y-m-d",time())."','".date("Y-m-d",time())."')");
	}


}


?>