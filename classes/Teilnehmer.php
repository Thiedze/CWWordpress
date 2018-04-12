<?php

/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 08.10.2016
 * Time: 16:04
 */
class Teilnehmer {

	private $id;

	private $vorname;

	private $nachname;

	private $email;

	private $str;

	private $plz;

	private $ort;

	private $geb;

	private $schule;

	private $essen;

	private $sonstiges;

	private $gotit;

	private $uuid;

    private $regdate;

    private $payed;

    private $shirt_payed;

	/**
	 * @var Shirt
	 */
	private $tshirt;

	/**
	 * @var Kurs
	 */
	private $kurs;

	/**
	 * @var wpdb
	 */
	private $db;

	/**
	 * Teilnehmer constructor.
	 *
	 * @param wpdb $_db
	 */
	public function __construct(wpdb $_db) {
		$this->db = $_db;
		$this->id = -1;
	}

	/**
	 * Speichert den Benutzer, falls dieser existiert wird UPDATE durchgeführt, wenn nicht wird ein neuer Eintrag erstellt.
	 * @return bool
	 */
	public function save(){
		if($this->id < 0){
			$query = $this->db->prepare(
				"INSERT INTO ".$this->db->prefix."cw_user (vorname,nachname,email,str,plz,ort,geb,schule,essen,sonstiges,gotit,uuid,regdate,payed,shirt_payed) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,NOW(),%d,%d)",
				$this->vorname,
				$this->nachname,
				$this->email,
				$this->str,
				$this->plz,
				$this->ort,
				$this->geb,
				$this->schule,
				$this->essen,
				$this->sonstiges,
				$this->gotit,
				$this->uuid,
                $this->payed,
                $this->shirt_payed
			);
		}else{
			$query = $this->db->prepare(
				"UPDATE ".$this->db->prefix."cw_user SET vorname=%s,nachname=%s,email=%s,str=%s,plz=%s,ort=%s,geb=%s,schule=%s,essen=%s,sonstiges=%s,gotit=%s,payed=%d,shirt_payed=%d WHERE id=%d",
				$this->vorname,
				$this->nachname,
				$this->email,
				$this->str,
				$this->plz,
				$this->ort,
				$this->geb,
				$this->schule,
				$this->essen,
				$this->sonstiges,
				$this->gotit,
                $this->payed,
                $this->shirt_payed,
				$this->id
			);
		}

		if($this->db->query($query) === false){
			wp_die();
		}

		if($this->tshirt){
			if($this->tshirt->getId() > 0) {
				$query = $this->db->prepare(
					"INSERT INTO ".$this->db->prefix."cw_user_shirt (user_id, shirt_id) VALUES (%d,%d) ON DUPLICATE KEY UPDATE shirt_id=%d",
					$this->id,
					$this->tshirt->getId(),
					$this->tshirt->getId()
				);

				if ( $this->db->query( $query ) === false ) {
					wp_die();
				}
			}
		}else{
			$query = $this->db->prepare(
				"DELETE FROM ".$this->db->prefix."cw_user_shirt WHERE user_id=%d",
				$this->id
			);
			if($this->db->query($query) === false){
				wp_die();
			}
		}
		
		if($this->kurs){
			if($this->kurs->getId() > 0){
				$query = $this->db->prepare(
					"INSERT INTO ".$this->db->prefix."cw_user_kurs (user_id, kurs_id) VALUES (%d,%d) ON DUPLICATE KEY UPDATE kurs_id=%d",
					$this->id,
					$this->kurs->getId(),
					$this->kurs->getId()
				);

				if ( $this->db->query( $query ) === false ) {
					wp_die();
				}
			}
		}else{
			$query = $this->db->prepare(
				"DELETE FROM ".$this->db->prefix."cw_user_kurs WHERE user_id=%d",
				$this->id
			);
			if($this->db->query($query) === false){
				wp_die();
			}
		}

		return true;
	}

	/**
	 * @param $id
	 *  Lädt einen Benutzer mit der angegebenen ID
	 * @return bool
	 */
	public function load($id){

		$query = $this->db->prepare("SELECT * FROM ".$this->db->prefix."cw_user u 
				  	LEFT JOIN ".$this->db->prefix."cw_user_shirt cws ON u.id = cws.user_id
				  	LEFT JOIN ".$this->db->prefix."cw_user_kurs cwk ON u.id = cwk.user_id
				  WHERE u.id=%d",$id);

		try {
			if ( $obj = $this->db->get_results( $query ) ) {
				$values = get_object_vars( $obj[0] );
				foreach ( $values as $key => $val ) {
					$this->$key = $val;
				}

				if($obj[0]->kurs_id > 0){
					$this->kurs = new Kurs($this->db);
					$this->kurs->load($obj[0]->kurs_id);
				}

				if($obj[0]->shirt_id > 0){
					$this->tshirt = new Shirt($this->db);
					$this->tshirt->load($obj[0]->shirt_id);
				}

				return true;
			} else {
				return false;
			}
		}catch (Exception $E){
			wp_die();
		}
	}

	/**
	 * @return null
	 */
	public function get_id_from_uuid(){
		$query = $this->db->prepare("SELECT id FROM ".$this->db->prefix."cw_user WHERE uuid=%s",$this->uuid);

		if($obj = $this->db->get_results($query)){
			$this->id = $obj[0]->id;
			return $obj[0]->id;
		}

		return null;
	}

	public function delete(){
	    if($this->id > 0){
	        $this->db->query("DELETE FROM ".$this->db->prefix."cw_user_shirt WHERE user_id=".$this->id);
            $this->db->query("DELETE FROM ".$this->db->prefix."cw_user_kurs WHERE user_id=".$this->id);
            $this->db->query("DELETE FROM ".$this->db->prefix."cw_user WHERE id=".$this->id);
        }
    }

	/*
	 ******* GETTERS AND SETTERS
	 */

	public function getId(): int {
		return $this->id;
	}

	public function getVorname() {
		return stripslashes($this->vorname);
	}

	public function getNachname() {
		return stripslashes($this->nachname);
	}

	public function getEmail() {
		return stripslashes($this->email);
	}

	public function getStr() {
		return stripslashes($this->str);
	}

	public function getPlz() {
		return stripslashes($this->plz);
	}

	public function getOrt() {
		return stripslashes($this->ort);
	}

	public function getGeb() {
		return $this->geb;
	}

	public function getSchule() {
		return stripslashes($this->schule);
	}

	public function getEssen() {
		return stripslashes($this->essen);
	}

	public function getSonstiges() {
		return stripslashes($this->sonstiges);
	}

	public function getGotit(){
		return stripslashes($this->gotit);
	}

	public function getUuid() {
		return $this->uuid;
	}

    public function getRegdate()
    {
        return $this->regdate;
    }

	/**
	 * @return Shirt
	 */
	public function getTshirt(){
		global $wpdb;
		if(!is_a($this->tshirt,"Shirt")){
			return new Shirt($wpdb);
		}
		return $this->tshirt;
	}

	/**
	 * @return Kurs
	 */
	public function getKurs(){
		return $this->kurs;
	}

    /**
     * @return mixed
     */
    public function getPayed()
    {
        return $this->payed;
    }

    /**
     * @param mixed $payed
     */
    public function setPayed($payed)
    {
        $this->payed = $payed;
    }

    /**
     * @return mixed
     */
    public function getShirtPayed()
    {
        return $this->shirt_payed;
    }

    /**
     * @param mixed $shirt_payed
     */
    public function setShirtPayed($shirt_payed)
    {
        $this->shirt_payed = $shirt_payed;
    }

	/**
	 * @param mixed $vorname
	 */
	public function setVorname( $vorname ) {
		$this->vorname = $vorname;
	}

	/**
	 * @param mixed $nachname
	 */
	public function setNachname( $nachname ) {
		$this->nachname = $nachname;
	}

	/**
	 * @param mixed $email
	 */
	public function setEmail( $email ) {
		$this->email = $email;
	}

	/**
	 * @param mixed $str
	 */
	public function setStr( $str ) {
		$this->str = $str;
	}

	/**
	 * @param mixed $plz
	 */
	public function setPlz( $plz ) {
		$this->plz = $plz;
	}

	/**
	 * @param mixed $ort
	 */
	public function setOrt( $ort ) {
		$this->ort = $ort;
	}

	/**
	 * @param mixed $geb
	 */
	public function setGeb( $geb ) {
		$this->geb = $geb;
	}

	/**
	 * @param mixed $schule
	 */
	public function setSchule( $schule ) {
		$this->schule = $schule;
	}

	/**
	 * @param mixed $essen
	 */
	public function setEssen( $essen ) {
		$this->essen = $essen;
	}

	/**
	 * @param mixed $sonstiges
	 */
	public function setSonstiges( $sonstiges ) {
		$this->sonstiges = $sonstiges;
	}

	/**
	 * @param mixed $gotit
	 */
	public function setGotit( $gotit ) {
		$this->gotit = $gotit;
	}

	/**
	 * @param mixed $uuid
	 */
	public function setUuid( $uuid ) {
		$this->uuid = $uuid;
	}

	/**
	 * @param Shirt $tshirt
	 */
	public function setTshirt( $tshirt ) {
		$this->tshirt = $tshirt;
	}

	/**
	 * @param Kurs $kurs
	 */
	public function setKurs( Kurs $kurs ) {
		$this->kurs = $kurs;
	}

}