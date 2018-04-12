<?php

/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 10.10.2016
 * Time: 16:24
 */
class Options {

	private $register_enabled;

	private $shirt_enabled;

	private $teilnahme_preis;

	private $text_closed;

	private $text_shirt;

	private $cw_start;

	private $register_start;

	private $text_email;

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
			"UPDATE ".$this->db->prefix."cw_options SET register_enabled=%d,shirt_enabled=%d,teilnahme_preis=%d,text_closed=%s,text_shirt=%s,cw_start=%s,register_start=%s,text_email=%s",
			$this->register_enabled,
			$this->shirt_enabled,
			$this->teilnahme_preis,
			$this->text_closed,
			$this->text_shirt,
			$this->cw_start,
			$this->register_start,
			$this->text_email
		);

		if($this->db->query($query) === false){
			wp_die();
		}

		return true;
	}

	public function getRegisterEnabled() {
		return $this->register_enabled;
	}

	public function getShirtEnabled() {
		return $this->shirt_enabled;
	}

	public function getTeilnahmePreis() {
		return $this->teilnahme_preis;
	}

	public function getTextClosed() {
		return stripslashes($this->text_closed);
	}

	public function getTextShirt() {
		return stripslashes($this->text_shirt);
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
	 * @param mixed $shirt_enabled
	 */
	public function setShirtEnabled( $shirt_enabled ) {
		$this->shirt_enabled = $shirt_enabled;
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
	 * @param mixed $text_shirt
	 */
	public function setTextShirt( $text_shirt ) {
		$this->text_shirt = $text_shirt;
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



}