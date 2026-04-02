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
require_once(CW_PLUGIN_DIR.'classes/Shirt.php');
require_once(CW_PLUGIN_DIR.'classes/Kurs.php');
require_once(CW_PLUGIN_DIR.'classes/Options.php');
require_once(CW_PLUGIN_DIR.'classes/Event.php');
require_once(CW_PLUGIN_DIR.'classes/functions.php');
require_once(CW_PLUGIN_DIR.'classes/Autoloader.php');
require_once(CW_PLUGIN_DIR.'classes/Export_XLS.php');
require_once(CW_PLUGIN_DIR.'views/view_options.php');
require_once(CW_PLUGIN_DIR.'views/view_kurse.php');
require_once(CW_PLUGIN_DIR.'views/view_shirts.php');
require_once(CW_PLUGIN_DIR.'views/view_kurs_user.php');
require_once(CW_PLUGIN_DIR.'views/view_teilnehmer.php');
require_once(CW_PLUGIN_DIR.'views/view_front_register.php');
require_once(CW_PLUGIN_DIR.'views/edit_teilnehmer.php');
require_once(CW_PLUGIN_DIR.'views/view_kurse_front.php');
require_once(CW_PLUGIN_DIR.'views/view_front_calendar.php');
require_once(CW_PLUGIN_DIR.'views/view_calendar.php');


add_action('admin_init','ad_init');
add_action('activate_plugin','plugin_activate');
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
	//wp_register_script('js_datatable','https://cdn.datatables.net/v/ju/jq-3.6.0/dt-1.13.4/date-1.4.0/r-2.4.1/datatables.min.js');
	wp_register_style('cw-admin.css',plugin_dir_url( __FILE__ ) . '/css/cw-admin.css', array());
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
	wp_enqueue_script( 'jquery-effects-highlight' );
	wp_enqueue_script( 'js_datatable' );
	wp_enqueue_script( 'kurs.js', plugins_url( "/js/kurs.js", __FILE__ ) );
	wp_enqueue_script( 'cw-admin.js', plugins_url( "/js/cw-admin.js", __FILE__ ) );
	wp_enqueue_script( 'cal.js', plugins_url( "/js/cal.js", __FILE__ ) );
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
	add_submenu_page('cwmain','T-Shirts','T-Shirts','cw_allow','tshirts','tshirts');
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

	if(isset($_GET["action"])){
		switch($_GET["action"]){

			case 'new':
					new_kurs();
				break;

			case 'edit':
					if(isset($_POST["edit"]) || isset($_POST["edit_done"])){ edit_kurs(); }
					if(isset($_POST["delete"])){ delete_kurs(); }
				break;

			default:
				kurs_head(true);
				show_kurse();
		}
	}else{
		kurs_head(true);
		show_kurse();
	}

	echo '</div>
	';
}

function tshirts(){

	if(isset($_GET["action"])){
		switch($_GET["action"]){

			case 'new':
				new_tshirt();
				break;

			case 'edit':
				if(isset($_POST["edit"]) || isset($_POST["edit_done"])){ edit_tshirt(); }
				if(isset($_POST["delete"])){ delete_tshirt(); }
				break;

			default:
				tshirt_head(true);
				show_tshirts();
		}
	}else{
		tshirt_head(true);
		show_tshirts();
	}

	echo '</div>
	';
}

function program(){

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'script_handle', plugins_url('/js/cp.js', __FILE__ ), array( 'wp-color-picker' ), false, true );

	if(isset($_GET["action"])){
		switch($_GET["action"]){

			case 'new':
				new_program();
				break;

			case 'edit':
				if(isset($_POST["edit"]) || isset($_POST["edit_done"])){ edit_program(); }
				if(isset($_POST["delete"])){ delete_program(); }
				break;

			default:
				program_head(true);
				show_program();
		}
	}else{
		program_head(true);
		show_program();
	}

	echo '</div>
	';
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

					case 'tshirts':
						$export = new Export_XLS($wpdb);
						$export->export_shirts();
					break;

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