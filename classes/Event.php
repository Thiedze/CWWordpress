<?php

/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 23.01.2017
 * Time: 17:01
 */
class Event {

	private $id;

	private $event_start;

	private $event_end;

	private $event_day;

	private $event_name;

	private $event_subtext;

	private $event_description;

	private $event_color;

	private $day = array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag");

	/**
	 * @var wpdb $db;
	 */
	private $db;

	public function __construct($_db) {
		$this->db = $_db;
		$this->id = -1;
	}

	public function load($id){
		$query = $this->db->prepare("SELECT * FROM ".$this->db->prefix."cw_events WHERE id=%d",$id);

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
				"INSERT INTO ".$this->db->prefix."cw_events (event_start, event_end, event_day, event_name, event_subtext, event_description, event_color) VALUES (%d,%d,%d,%s,%s,%s,%s)",
				$this->event_start,
				$this->event_end,
				$this->event_day,
				$this->event_name,
				$this->event_subtext,
				$this->event_description,
				$this->event_color
			);
		}else{
			$query = $this->db->prepare(
				"UPDATE ".$this->db->prefix."cw_events SET event_start=%d, event_end=%d, event_day=%d, event_name=%s, event_subtext=%s, event_description=%s, event_color=%s WHERE id=%d",
				$this->event_start,
				$this->event_end,
				$this->event_day,
				$this->event_name,
				$this->event_subtext,
				$this->event_description,
				$this->event_color,
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

	public function getEventStart($display=false) {
		if($display == true){
			$ret = floor($this->event_start/100).':'.(strlen($this->event_start % 100) == 1 ? '0'.($this->event_start % 100) : ($this->event_start % 100));
			if(strlen($ret) == 4){
				return '0'.$ret;
			}else{
				return $ret;
			}
		}
		return $this->event_start;
	}

	/**
	 * @param mixed $event_start
	 */
	public function setEventStart( $event_start ) {
		$this->event_start = $event_start;
	}

	public function getEventEnd($display=false) {
		if($display == true){
			$ret = floor($this->event_end/100).':'.(strlen($this->event_end % 100) == 1 ? '0'.($this->event_end % 100) : ($this->event_end % 100));
			if(strlen($ret) == 4){
				return '0'.$ret;
			}else{
				return $ret;
			}
		}
		return $this->event_end;
	}

	/**
	 * @param mixed $event_end
	 */
	public function setEventEnd( $event_end ) {
		$this->event_end = $event_end;
	}

	public function getEventDay($display=false) {
		if($display == true){
			return $this->day[$this->event_day];
		}
		return $this->event_day;
	}

	/**
	 * @param mixed $event_day
	 */
	public function setEventDay( $event_day ) {
		$this->event_day = $event_day;
	}

	public function getEventName() {
		return stripslashes($this->event_name);
	}

	/**
	 * @param mixed $event_name
	 */
	public function setEventName( $event_name ) {
		$this->event_name = $event_name;
	}

	public function getEventSubtext() {
		return stripslashes($this->event_subtext);
	}

	/**
	 * @param mixed $event_subtext
	 */
	public function setEventSubtext( $event_subtext ) {
		$this->event_subtext = $event_subtext;
	}

	public function getEventDescription($cutted= false) {
		if($cutted){
			return substr(strip_tags($this->event_description),0,50).(strlen(strip_tags($this->event_description)) > 50 ? '...':'');
		}
		return stripslashes($this->event_description);
	}

	/**
	 * @param mixed $event_description
	 */
	public function setEventDescription( $event_description ) {
		$this->event_description = $event_description;
	}

	public static function get_days(){
		return array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag");
	}

	public function getEventColor() {
		if(strlen($this->event_color)) {
			return $this->event_color;
		}else{
			return "#D7E7A1";
		}
	}

	/**
	 * @param mixed $event_color
	 */
	public function setEventColor( $event_color ) {
		$this->event_color = $event_color;
	}



}