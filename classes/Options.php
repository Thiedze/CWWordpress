<?php

/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 10.10.2016
 * Time: 16:24
 */
class Options {

	private $register_enabled;

	private $register_logged_in_only;

	private $teilnahme_preis;

	private $teilnahme_preis_alumni;

	private $text_closed;

	private $cw_start;

	private $register_start;

	private $text_email;

	private $text_register;

	/**
	 * @var wpdb
	 */
	private $db;

	public function __construct($_db) {
		$this->db = $_db;
	}

	public function load(){
		$query = "SELECT * FROM ".$this->db->prefix."cw_options";

		try {
			if ( $obj = $this->db->get_results( $query ) ) {
				$values = get_object_vars( $obj[0] );
				foreach ( $values as $key => $val ) {
					$this->$key = $val;
				}

				return true;
			} else {
				return false;
			}
		}catch (Exception $E){
			wp_die();
		}
	}

	public function save(){
		$query = $this->db->prepare(
			"UPDATE ".$this->db->prefix."cw_options SET register_enabled=%d,register_logged_in_only=%d,teilnahme_preis=%d,teilnahme_preis_alumni=%d,text_closed=%s,cw_start=%s,register_start=%s,text_email=%s,text_register=%s",
			$this->register_enabled,
			$this->register_logged_in_only,
			$this->teilnahme_preis,
			$this->teilnahme_preis_alumni,
			$this->text_closed,
			$this->cw_start,
			$this->register_start,
			$this->text_email,
			$this->text_register
		);

		if($this->db->query($query) === false){
			wp_die();
		}

		return true;
	}

	public function getRegisterEnabled() {
		return $this->register_enabled;
	}

	public function getRegisterLoggedInOnly() {
		return $this->register_logged_in_only;
	}

	public function setRegisterLoggedInOnly( $val ) {
		$this->register_logged_in_only = $val;
	}

	public function getTeilnahmePreis() {
		return $this->teilnahme_preis;
	}

	public function getTextClosed() {
		return stripslashes($this->text_closed);
	}

	public function getCwStart() {
		return $this->cw_start;
	}

	public function getRegisterStart() {
		return $this->register_start;
	}

	public function getTextEmail() {
		return stripslashes($this->text_email);
	}

	/**
	 * @param mixed $text_email
	 */
	public function setTextEmail( $text_email ) {
		$this->text_email = $text_email;
	}

	/**
	 * @param mixed $register_enabled
	 */
	public function setRegisterEnabled( $register_enabled ) {
		$this->register_enabled = $register_enabled;
	}

	/**
	 * @param mixed $teilnahme_preis
	 */
	public function setTeilnahmePreis( $teilnahme_preis ) {

		$this->teilnahme_preis = intval($teilnahme_preis);

		if($this->teilnahme_preis > 999){
			$this->teilnahme_preis = 999;
		}

	}

	/**
	 * @param mixed $text_closed
	 */
	public function setTextClosed( $text_closed ) {
		$this->text_closed = $text_closed;
	}

	/**
	 * @param mixed $cw_start
	 */
	public function setCwStart( $cw_start ) {
		$this->cw_start = $cw_start;
	}

	/**
	 * @param mixed $register_start
	 */
	public function setRegisterStart( $register_start ) {
		$this->register_start = $register_start;
	}

	/**
	 * @return mixed
	 */
	public function get_teilnahme_preis_alumni() {
		return $this->teilnahme_preis_alumni;
	}

	/**
	 * @param mixed $teilnahme_preis_alumni
	 */
	public function set_teilnahme_preis_alumni( $teilnahme_preis_alumni ): void {
		$this->teilnahme_preis_alumni = $teilnahme_preis_alumni;
	}

	/**
	 * @return mixed
	 */
	public function get_text_register() {
		return $this->text_register;
	}

	/**
	 * @param mixed $text_register
	 */
	public function set_text_register( $text_register ): void {
		$this->text_register = $text_register;
	}




}