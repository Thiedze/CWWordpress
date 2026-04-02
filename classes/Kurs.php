<?php

/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 10.10.2016
 * Time: 15:34
 */
class Kurs {

	private $id;

	private $name;

	private $beschreibung;

	private $max_teilnehmer;

	private $bild;

	private $show_front;

	private $is_open;

	private $teilnehmer;

    private $beauty_name;

	/**
	 * @var wpdb
	 */
	private $db;

	public function __construct($_db) {
		$this->db = $_db;
		$this->id = -1;
	}

	public function load($id){
		$query = $this->db->prepare("SELECT k.*,count(ku.user_id) AS teilnehmer FROM ".$this->db->prefix."cw_kurse k LEFT JOIN ".$this->db->prefix."cw_user_kurs ku ON k.id = ku.kurs_id WHERE k.id=%d",$id);

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
				"INSERT INTO ".$this->db->prefix."cw_kurse (name, beschreibung, max_teilnehmer, bild, show_front, is_open, beauty_name) VALUES (%s,%s,%d,%s,%d,%d,%s)",
				$this->name,
				$this->beschreibung,
				$this->max_teilnehmer,
				$this->bild,
				$this->show_front,
				$this->is_open,
                $this->beauty_name
			);
		}else{
			$query = $this->db->prepare(
				"UPDATE ".$this->db->prefix."cw_kurse SET name=%s,beschreibung=%s,max_teilnehmer=%d,bild=%s,show_front=%d,is_open=%d,beauty_name=%s WHERE id=%d",
				$this->name,
				$this->beschreibung,
				$this->max_teilnehmer,
				$this->bild,
				$this->show_front,
				$this->is_open,
                $this->beauty_name,
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

	public function getBeschreibung() {
		return stripslashes($this->beschreibung);
	}

	public function getMaxTeilnehmer() {
		return $this->max_teilnehmer;
	}

	public function getTeilnehmer() {
		return $this->teilnehmer;
	}

	public function getBild() {
		return stripslashes($this->bild);
	}

	public function getShowFront() {
		return $this->show_front;
	}

	public function getIs_open() {
		return $this->is_open;
	}

    /**
     * @return mixed
     */
    public function getBeautyName()
    {
        return $this->beauty_name;
    }


	/**
	 * @param mixed $name
	 */
	public function setName( $name ) {
		$this->name = $name;
        $this->setBeautyName($name);
	}

	/**
	 * @param mixed $beschreibung
	 */
	public function setBeschreibung( $beschreibung ) {
		$this->beschreibung = $beschreibung;
	}

	/**
	 * @param mixed $max_teilnehmer
	 */
	public function setMaxTeilnehmer( $max_teilnehmer ) {
		$this->max_teilnehmer = $max_teilnehmer;
	}

	/**
	 * @param mixed $bild
	 */
	public function setBild( $bild ) {
		$this->bild = $bild;
	}

	/**
	 * @param mixed $show_front
	 */
	public function setShowFront( $show_front ) {
		$this->show_front = $show_front;
	}

	/**
	 * @param mixed $is_open
	 */
	public function setIs_open( $is_open ) {
		$this->is_open = $is_open;
	}

    /**
     * @param mixed $beauty_name
     */
    private function setBeautyName($beauty_name)
    {
        $this->beauty_name = preg_replace('/([^A-Za-z0-9äöüÄÖÜ-])+/','_',$this->name);
    }




}