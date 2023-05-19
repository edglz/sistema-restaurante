<?php Session::init(); ?>
<?php

class Carta_Model extends Model {
    public function __construct()
	{
		parent::__construct();
	}
    public function Carta(){
        
        $stm = $this->db->query("SELECT * FROM tm_cartilla")->fetch(PDO::FETCH_OBJ);
        return $stm;
    }
    public function ActualizaCarta($route){
        $stm = $this->db->query("SELECT * FROM tm_cartilla")->fetchAll(PDO::FETCH_OBJ);
        if(count($stm) > 0){
            $stm = $this->db->query("UPDATE tm_cartilla SET route = '$route' ");
            echo '1';
        }else{
            $stm = $this->db->query("INSERT INTO tm_cartilla (route)VALUES('$route')");
            echo '1';
        }
        
    }
}