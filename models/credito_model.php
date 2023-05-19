<?php Session::init(); ?>
<?php

class Credito_Model extends Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
    public function credito_compra_list()
    {
        try
        {
            $ifecha = date('Y-m-d',strtotime($_POST['ifecha']));
            $ffecha = date('Y-m-d',strtotime($_POST['ffecha']));
            $id_prov = $_POST['id_prov'];
            $stm = $this->db->prepare("SELECT cc.id_credito,cc.id_compra,cc.total,cc.interes,cc.fecha,vc.id_prov,CONCAT(vc.serie_doc,'-',vc.num_doc) AS numero,vc.desc_td,desc_prov FROM tm_compra_credito AS cc INNER JOIN v_compras AS vc ON cc.id_compra = vc.id_compra WHERE (cc.fecha >= ? AND cc.fecha <= ?) AND vc.id_prov like ? AND cc.estado = 'p' AND vc.estado = 'a' ORDER BY cc.fecha ASC");
            $stm->execute(array($ifecha,$ffecha,$id_prov));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Amortizado'} = $this->db->query("SELECT IFNULL(SUM(importe),0) AS total FROM tm_credito_detalle WHERE id_credito = ".$d->id_credito)
                    ->fetch(PDO::FETCH_OBJ);
            }
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json; 
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function Proveedores()
    {
        try
        {      
            return $this->db->selectAll('SELECT id_prov,ruc,razon_social FROM tm_proveedor');
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function credito_compra_det()
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_credito_detalle WHERE id_credito = ?");
            $stm->execute(array($_POST['id_credito']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Usuario'} = $this->db->query("SELECT CONCAT(ape_paterno,' ',ape_materno,' ',nombres) AS nombre FROM v_usuarios WHERE id_usu = ".$d->id_usu)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function credito_compra_cuota_list($data)
    {
        try
        {
            $stm = $this->db->prepare("SELECT cc.id_credito,cc.id_compra,cc.total,cc.interes,cc.fecha,vc.id_prov,CONCAT(vc.serie_doc,'-',vc.num_doc) AS numero,vc.desc_td,desc_prov FROM tm_compra_credito AS cc INNER JOIN v_compras AS vc ON cc.id_compra = vc.id_compra WHERE cc.id_credito like ? AND cc.estado = 'p'");
            $stm->execute(array($data['id_credito']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Amortizado'} = $this->db->query("SELECT IFNULL(SUM(importe),0) AS total FROM tm_credito_detalle WHERE id_credito = ".$d->id_credito)
                    ->fetch(PDO::FETCH_OBJ);
            }
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function credito_compra_cuota_pago($data)
    {
        try 
        {
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
            $fecha = date("Y-m-d H:i:s");
            $id_usu = Session::get('usuid');
            $id_apc = Session::get('apcid');
            $consulta = "call usp_comprasCreditoCuotas( :flag, :id_credito, :id_usu, :id_apc, :importe, :fecha, :egreso, :monto_egreso, :monto_amortizado, :total_credito);";
            $arrayParam =  array(
                ':flag' => 1,
                ':id_credito' =>  $data['id_credito'],
                ':id_usu' =>  $id_usu,
                ':id_apc' => $id_apc,
                ':importe' =>  $data['importe'],
                ':fecha' =>  $fecha,
                ':egreso' =>  $data['egreso'],
                ':monto_egreso' => $data['monto_egreso'],
                ':monto_amortizado' => $data['monto_amortizado'],
                ':total_credito' => $data['total_credito']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
        }
        catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

}