<?php

/*
Plugin Name: Campuswoche
Plugin URI: http://campuswoche.de
Description: Anmelde- und Verwaltungssystem für die Campuswoche
Version: 3.0
Author: Joachim Ernsten
Author URI: https://www.i-sec.ninja
License: GPL
*/

define( 'CW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once(CW_PLUGIN_DIR.'db/db.php');
require_once(CW_PLUGIN_DIR.'classes/Teilnehmer.php');
require_once(CW_PLUGIN_DIR.'classes/Kurs.php');
require_once(CW_PLUGIN_DIR.'classes/Options.php');
require_once(CW_PLUGIN_DIR.'classes/Event.php');
require_once(CW_PLUGIN_DIR.'classes/functions.php');
require_once(CW_PLUGIN_DIR.'classes/Autoloader.php');
require_once(CW_PLUGIN_DIR.'classes/Export_XLS.php');
require_once(CW_PLUGIN_DIR.'views/view_options.php');
require_once(CW_PLUGIN_DIR.'views/view_kurse.php');
require_once(CW_PLUGIN_DIR.'views/view_kurs_user.php');
require_once(CW_PLUGIN_DIR.'views/view_teilnehmer.php');
require_once(CW_PLUGIN_DIR.'views/view_front_register.php');
require_once(CW_PLUGIN_DIR.'views/edit_teilnehmer.php');
require_once(CW_PLUGIN_DIR.'views/view_kurse_front.php');
require_once(CW_PLUGIN_DIR.'views/view_front_calendar.php');
require_once(CW_PLUGIN_DIR.'views/view_calendar.php');
require_once(CW_PLUGIN_DIR.'views/view_calendar_admin.php');


add_action('admin_init','ad_init');
add_action('activate_plugin','plugin_activate');
add_action('wp_ajax_cw_cal_move',    'cw_cal_move');
add_action('wp_ajax_cw_cal_delete',  'cw_cal_delete');
add_action('wp_ajax_cw_cal_create',  'cw_cal_create');
add_action('wp_ajax_cw_kurs_load',   'cw_kurs_load_handler');
add_action('wp_ajax_cw_kurs_save',   'cw_kurs_save_handler');
add_action('wp_ajax_cw_kurs_delete', 'cw_kurs_delete_handler');
add_action('wp_ajax_cw_event_load',  'cw_event_load_handler');
add_action('wp_ajax_cw_event_save',  'cw_event_save_handler');
add_action('deactivate_plugin','plugin_deactivate');
add_action('admin_menu', 'cwplugin');
add_action('wp_ajax_ajax_action','do_ajax');
add_action('wp_loaded','load_scripts');
add_shortcode('front_register','reg_front');
add_shortcode('front_kurse','kurse_front');
add_shortcode('front_calendar','calendar_front');
add_filter('query_vars','qu_vars');

/* Wordpress Plugin "members" */

add_action('members_register_cap_groups', 'th_register_cap_groups');
add_action( 'members_register_caps', 'th_register_caps' );

function th_register_cap_groups() {

	members_register_cap_group(
		'cw_group',
		array(
			'label'    => __( 'Campuswoche', 'cw' ),
			'caps'     => array(),
			'icon'     => 'dashicons-tickets-alt',
			'priority' => 8
		)
	);
}

function th_register_caps() {

	members_register_cap('cw_allow',array('label' => __( 'Campuswoche Zugriff', 'cw' ),'group' => 'cw_group'));
//	members_register_cap('options_write',array('label' => __( 'Optionen bearbeiten', 'cw' ),'group' => 'cw_group'));
//	members_register_cap('course_write',array('label' => __( 'Kurse bearbeiten', 'cw' ),'group' => 'cw_group'));
//	members_register_cap('shirt_write',array('label' => __( 'T-Shirts bearbeiten', 'cw' ),'group' => 'cw_group'));
//	members_register_cap('participants_write',array('label' => __( 'Teilnehmer bearbeiten', 'cw' ),'group' => 'cw_group'));
//	members_register_cap('program_write',array('label' => __( 'Programm bearbeiten', 'cw' ),'group' => 'cw_group'));
}

/******************************/

function load_scripts() {



	wp_register_style('jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
	wp_register_script('js_datatable','https://cdn.datatables.net/v/dt/dt-1.13.4/r-2.4.1/datatables.min.js');
	wp_register_style('css_datatable','https://cdn.datatables.net/v/dt/dt-1.13.4/r-2.4.1/datatables.min.css');
	//wp_register_script('js_datatable','https://cdn.datatables.net/v/ju/jq-3.6.0/dt-1.13.4/date-1.4.0/r-2.4.1/datatables.min.js');
	wp_register_style('cw-admin.css',plugin_dir_url( __FILE__ ) . '/css/cw-admin.css', array());
	wp_register_style('admin-cal.css',plugin_dir_url( __FILE__ ) . '/css/admin_cal.css', array());
	wp_enqueue_style('admin-cal.css');
	wp_register_style('cal_reset.css',plugin_dir_url( __FILE__ ) . '/css/reset.css', array());
	wp_register_style('cal_style.css',plugin_dir_url( __FILE__ ) . '/css/style.css', array());

	wp_enqueue_style( 'cw-admin.css' );
	wp_enqueue_style( 'jquery-ui' );
	wp_enqueue_style( 'css_datatable' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-tooltip' );
	wp_enqueue_script( 'jquery-ui-dialog' );
	wp_enqueue_script( 'jquery-ui-effects' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'jquery-ui-spinner' );
	wp_enqueue_script( 'jquery-ui-draggable' );
	wp_enqueue_script( 'jquery-ui-resizable' );
	wp_enqueue_script( 'jquery-effects-highlight' );
	wp_enqueue_script( 'js_datatable' );
	wp_enqueue_script( 'kurs.js', plugins_url( "/js/kurs.js", __FILE__ ) );
	wp_localize_script( 'kurs.js', 'cw_kurs', array(
		'ajaxurl' => admin_url('admin-ajax.php'),
		'nonce'   => wp_create_nonce('cw_kurs_nonce'),
	) );
	wp_enqueue_script( 'cw-admin.js', plugins_url( "/js/cw-admin.js", __FILE__ ) );
	wp_enqueue_script( 'cal.js', plugins_url( "/js/cal.js", __FILE__ ) );
	wp_enqueue_script( 'admin-cal.js', plugins_url( "/js/admin_cal.js", __FILE__ ), array('jquery','jquery-ui-draggable','jquery-ui-resizable','jquery-ui-dialog') );
	//wp_enqueue_script( 'teilnehmer.js', plugins_url( "/js/teilnehmer.js", __FILE__ ) );
	wp_enqueue_media();

	wp_enqueue_script('ajax-script',plugins_url("/js/teilnehmer.js",__FILE__),array('jquery'));
	wp_localize_script('ajax-script','ajax_object',array('ajaxurl'=>admin_url('admin-ajax.php')));
}

function do_ajax(){

    if(isset($_POST["save"])){
        if($_POST["save"] == "done"){
            echo save_edit();
        }
    }else {

        if(isset($_POST["do"])){
            if($_POST["do"] == "delete"){
                echo delete_user();
            }
        }

        if (preg_match("/\d+/", $_POST["value"])) {
            echo edit_teilnehmer($_POST["value"]);
        }
    }
	wp_die();
}

function plugin_activate(){
    global $wpdb;
    init_database($wpdb);
}

function plugin_deactivate(){
    //flush_rewrite_rules();
}

function cwplugin(){
	add_menu_page('Campuswoche','Campuswoche','cw_allow','cwmain','cw_start',plugin_dir_url(__FILE__).'img/cicon.png');
	add_submenu_page('cwmain','Kurse','Kurse','cw_allow','kurse','kurse');
    add_submenu_page('cwmain','Teilnehmer Kurse','Teilnehmer Kurse','cw_allow','tkurs','tkurs');
	add_submenu_page('cwmain','Teilnehmerliste','Teilnehmerliste','cw_allow','teilnehmer','teilnehmer');
	add_submenu_page('cwmain','Programm','Programm','cw_allow','program','program');
}

function cw_start(){

	options_head();

	if(isset($_POST["speichern"])){
		save_options($_POST);
	}

	show_options();

	echo '</div>';

}

function kurse(){
	kurs_head(true);
	show_kurse();
	echo '</div>';
}

function cw_kurs_load_handler() {
	if (!current_user_can('cw_allow')) wp_die('forbidden');
	check_ajax_referer('cw_kurs_nonce', 'nonce');
	global $wpdb;
	$kurs = new Kurs($wpdb);
	$kurs->load(intval($_POST['kid'] ?? 0));
	if ($kurs->getId() < 0) wp_send_json_error('Kurs nicht gefunden.');
	wp_send_json_success(array(
		'id'             => $kurs->getId(),
		'name'           => $kurs->getName(),
		'max_teilnehmer' => $kurs->getMaxTeilnehmer(),
		'show_front'     => $kurs->getShowFront(),
		'is_open'        => $kurs->getIs_open(),
		'bild'           => $kurs->getBild(),
		'beschreibung'   => $kurs->getBeschreibung(),
	));
}

function cw_kurs_save_handler() {
	if (!current_user_can('cw_allow')) wp_die('forbidden');
	check_ajax_referer('cw_kurs_nonce', 'nonce');
	global $wpdb;
	$kid          = intval($_POST['kid'] ?? 0);
	$name         = sanitize_text_field($_POST['name'] ?? '');
	$mteil        = intval($_POST['mteil'] ?? 0);
	$show_front   = empty($_POST['show_front']) ? 0 : 1;
	$is_open      = empty($_POST['is_open'])    ? 0 : 1;
	$bild         = esc_url_raw($_POST['bild'] ?? '');
	$beschreibung = wp_kses_post($_POST['beschreibung'] ?? '');
	if (empty($name)) wp_send_json_error('Bitte einen Kursnamen angeben.');
	$kurs = new Kurs($wpdb);
	if ($kid > 0) $kurs->load($kid);
	$kurs->setName($name);
	$kurs->setMaxTeilnehmer($mteil);
	$kurs->setShowFront($show_front);
	$kurs->setIs_open($is_open);
	$kurs->setBild($bild);
	$kurs->setBeschreibung($beschreibung);
	if ($kurs->save()) wp_send_json_success();
	else wp_send_json_error('Fehler beim Speichern.');
}

function cw_kurs_delete_handler() {
	if (!current_user_can('cw_allow')) wp_die('forbidden');
	check_ajax_referer('cw_kurs_nonce', 'nonce');
	global $wpdb;
	$kid = intval($_POST['kid'] ?? 0);
	if ($kid <= 1) wp_send_json_error('Der Kurs "Sonstiges" kann nicht gel&ouml;scht werden.');
	$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."cw_kurse WHERE id=%d", $kid));
	$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."cw_user_kurs SET kurs_id=0 WHERE kurs_id=%d OR kurs_id IS NULL", $kid));
	wp_send_json_success();
}


function program(){
	program_head(false);
	show_program_calendar();
	echo '</div>';
}

/* ================================================================
   AJAX-Handler: Event verschieben / strecken
   ================================================================ */
function cw_cal_move(){
	if(!current_user_can('cw_allow')) wp_die('forbidden');
	check_ajax_referer('cw_cal_nonce','nonce');

	global $wpdb;
	$id    = intval($_POST['id']);
	$day   = intval($_POST['day']);
	$start = intval($_POST['start']);
	$end   = intval($_POST['end']);

	if($day < 0 || $day > 5)           wp_send_json_error('invalid day');
	if($start < 700 || $start > 2300)  wp_send_json_error('invalid start');
	if($end   < 700 || $end   > 2300)  wp_send_json_error('invalid end');
	if($end <= $start)                  wp_send_json_error('invalid range');

	$event = new Event($wpdb);
	$event->load($id);
	if($event->getId() < 0) wp_send_json_error('not found');

	$event->setEventDay($day);
	$event->setEventStart($start);
	$event->setEventEnd($end);
	$event->save();

	wp_send_json_success();
}

/* ================================================================
   AJAX-Handler: Event löschen
   ================================================================ */
function cw_cal_delete(){
	if(!current_user_can('cw_allow')) wp_die('forbidden');
	check_ajax_referer('cw_cal_nonce','nonce');

	global $wpdb;
	$id = intval($_POST['id']);
	$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."cw_events WHERE id=%d", $id));

	wp_send_json_success();
}

/* ================================================================
   AJAX-Handler: Neues Event anlegen
   ================================================================ */
function cw_cal_create(){
	if(!current_user_can('cw_allow')) wp_die('forbidden');
	check_ajax_referer('cw_cal_nonce','nonce');

	global $wpdb;
	$day   = intval($_POST['day']);
	$start = intval($_POST['start']);
	$end   = intval($_POST['end']);
	$name  = sanitize_text_field($_POST['name']);
	$color = sanitize_hex_color($_POST['color']);
	if(!$color) $color = '#d7e7a1';

	if($day < 0 || $day > 5)          wp_send_json_error('invalid day');
	if($start < 700 || $start > 2300) wp_send_json_error('invalid start');
	if($end   < 700 || $end   > 2300) wp_send_json_error('invalid end');
	if($end <= $start)                 wp_send_json_error('invalid range');
	if(!$name)                         wp_send_json_error('no name');

	$event = new Event($wpdb);
	$event->setEventDay($day);
	$event->setEventStart($start);
	$event->setEventEnd($end);
	$event->setEventName($name);
	$event->setEventSubtext('');
	$event->setEventDescription('');
	$event->setEventColor($color);
	$event->save();

	wp_send_json_success(array('id' => $wpdb->insert_id));
}

function cw_event_load_handler() {
	if (!current_user_can('cw_allow')) wp_die('forbidden');
	check_ajax_referer('cw_cal_nonce', 'nonce');
	global $wpdb;
	$event = new Event($wpdb);
	$event->load(intval($_POST['eid'] ?? 0));
	if ($event->getId() < 0) wp_send_json_error('Event nicht gefunden.');
	wp_send_json_success(array(
		'id'                => $event->getId(),
		'event_day'         => $event->getEventDay(),
		'event_start'       => $event->getEventStart(),
		'event_end'         => $event->getEventEnd(),
		'event_name'        => $event->getEventName(),
		'event_subtext'     => $event->getEventSubtext(),
		'event_description' => $event->getEventDescription(),
		'event_color'       => $event->getEventColor(),
	));
}

function cw_event_save_handler() {
	if (!current_user_can('cw_allow')) wp_die('forbidden');
	check_ajax_referer('cw_cal_nonce', 'nonce');
	global $wpdb;
	$eid   = intval($_POST['eid']         ?? 0);
	$day   = intval($_POST['event_day']   ?? 0);
	$start = intval($_POST['event_start'] ?? 0);
	$end   = intval($_POST['event_end']   ?? 0);
	$name  = sanitize_text_field($_POST['event_name']    ?? '');
	$sub   = sanitize_text_field($_POST['event_subtext'] ?? '');
	$desc  = wp_kses_post($_POST['event_description']    ?? '');
	$color = sanitize_hex_color($_POST['event_color']    ?? '') ?: '#d7e7a1';
	if (empty($name))              wp_send_json_error('Bitte einen Titel angeben.');
	if ($day < 0 || $day > 5)     wp_send_json_error('Ung&uuml;ltiger Tag.');
	if ($start < 700 || $start > 2300) wp_send_json_error('Ung&uuml;ltige Startzeit.');
	if ($end   < 700 || $end   > 2300) wp_send_json_error('Ung&uuml;ltige Endzeit.');
	if ($end <= $start)            wp_send_json_error('Ende muss nach Start liegen.');
	$excl = $eid > 0 ? $wpdb->prepare(' AND id <> %d', $eid) : '';
	$res  = $wpdb->get_row($wpdb->prepare(
		"SELECT count(*) AS c FROM ".$wpdb->prefix."cw_events
		 WHERE event_day=%d AND (%d BETWEEN event_start AND event_end-1 OR %d BETWEEN event_start+1 AND event_end)".$excl,
		$day, $start, $end
	));
	if ($res->c >= 1) wp_send_json_error('Das Event &uuml;berschneidet sich mit einem anderen Event.');
	$event = new Event($wpdb);
	if ($eid > 0) $event->load($eid);
	$event->setEventDay($day);
	$event->setEventStart($start);
	$event->setEventEnd($end);
	$event->setEventName($name);
	$event->setEventSubtext($sub);
	$event->setEventDescription($desc);
	$event->setEventColor($color);
	if ($event->save()) wp_send_json_success();
	else wp_send_json_error('Fehler beim Speichern.');
}

function reg_front(){
	wp_enqueue_script( 'front_reg.js', plugins_url( "/js/front_register.js", __FILE__ ) );
	return register();
}

function ad_init(){

	global $wpdb;

	if(current_user_can('cw_allow')){
		if(isset($_GET["action"])){
			if($_GET["action"] == "export"){
				switch($_GET["page"]){

					case 'teilnehmer':
						$export = new Export_XLS($wpdb);
						$export->export_teilnehmer();
						break;

					case 'tkurs':
						$export = new Export_XLS($wpdb);
						$export->export_kurs_teilnehmer();
						break;

					default:
						break;
				}
			}
		}
	}


}

?>