<?php

/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 10.10.2016
 * Time: 15:43
 */
class Shirt {

	private $id;

	private $name;

	private $size;

	private $preis;

	/**
	 * @var wpdb
	 */
	private $db;

	public static function getsizes(){
		return array("XS","S","M","L","XL","XXL","XXXL","XXXXL");
	}

	public function __construct($_db) {
		$this->id = -1;
		$this->preis = 0;
		$this->db = $_db;
	}

	public function load($id){
		$query = $this->db->prepare("SELECT * FROM ".$this->db->prefix."cw_shirt WHERE id=%d",$id);

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
		if($this->id < 0){
			$query = $this->db->prepare(
				"INSERT INTO ".$this->db->prefix."cw_shirt (name, size, preis) VALUES (%s,%s,%d)",
				$this->name,
				$this->size,
				$this->preis
			);
		}else{
			$query = $this->db->prepare(
				"UPDATE ".$this->db->prefix."cw_shirt SET name=%s,size=%s,preis=%d WHERE id=%d",
				$this->name,
				$this->size,
				$this->preis,
				$this->id
			);
		}

		if($this->db->query($query) === false){
			wp_die();
		}

		return true;

	}

	public function getId(): int {
		return $this->id;
	}

	public function getName() {
		return stripslashes($this->name);
	}

	public function getSize() {
		return stripslashes($this->size);
	}

	public function getPreis() {
		return $this->preis;
	}

	/**
	 * @param mixed $name
	 */
	public function setName( $name ) {
		$this->name = $name;
	}

	/**
	 * @param mixed $size
	 */
	public function setSize( $size ) {
		$this->size = $size;
	}

	/**
	 * @param mixed $preis
	 */
	public function setPreis( $preis ) {
		$this->preis = $preis;
	}



}