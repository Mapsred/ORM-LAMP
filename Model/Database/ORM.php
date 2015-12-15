<?php 
namespace Model\Database;	
 class ORM { 
	protected $sql_serveur= '5.135.191.187';
	protected $sql_utilisateur= 'maps_red';
	protected $sql_password= '9aEuULSDQtyJMbK2';
	protected $sql_bd= 'orm';
	public $tablename= 'action';
	private $id;
	private $ok;
	private $ko;
	private $test;
	protected $database;

	function __construct() {
		$this->database = array($this->sql_serveur, $this->sql_utilisateur, $this->sql_password, $this->sql_bd);
	}
	public function setId($item) { 
		$this->id = $item;
	}
	public function setOk($item) { 
		$this->ok = $item;
	}
	public function setKo($item) { 
		$this->ko = $item;
	}
	public function setTest($item) { 
		$this->test = $item;
	}
	public function getId() { 
		return $this->id;
	}
	public function getOk() { 
		return $this->ok;
	}
	public function getKo() { 
		return $this->ko;
	}
	public function getTest() { 
		return $this->test;
	}
	public function getDatabase() {
		return $this->database;
	}

 }