<?php

class Client_Model extends Model {
    public function __construct()
	{
		parent::__construct();
	}
    public function Carta(){
        
        $stm = $this->db->query("SELECT * FROM tm_cartilla")->fetch(PDO::FETCH_OBJ);
        return $stm;
    }
}