<?php Session::init(); ?>
<?php

class Inventario_Model extends Model
{
	public function __construct()
	{
		parent::__construct();
	}

    public function Responsable()
    {
        try
        {      
            return $this->db->selectAll('SELECT * FROM tm_usuario WHERE id_usu <> 1 GROUP BY dni');
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }
	
    /* AJUSTE DE STOCK */
    public function ajuste_list()
    {
        try
        {
            $ifecha = date('Y-m-d',strtotime($_POST['ifecha']));
            $ffecha = date('Y-m-d',strtotime($_POST['ffecha']));

            $stm = $this->db->prepare("SELECT i.*,IF(i.id_tipo=3,'ENTRADA','SALIDA') AS tipo, CONCAT(u.nombres,' ',u.ape_paterno,' ',u.ape_materno) AS responsable FROM tm_inventario_entsal AS i INNER JOIN tm_usuario AS u ON i.id_responsable = u.id_usu WHERE ((DATE_FORMAT(i.fecha,'%Y-%m-%d')) >= ? AND (DATE_FORMAT(i.fecha,'%Y-%m-%d')) <= ?)");
            $stm->execute(array($ifecha,$ffecha));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json; 
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function ajuste_crud_create($data)
    {
        try 
        {
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
            $fecha = date("Y-m-d H:i:s");
            /* Registramos */
            $sql = "INSERT INTO tm_inventario_entsal(id_tipo,id_usu,id_responsable,motivo,fecha) VALUES (?,?,?,?,?);";
            $this->db->prepare($sql)
                      ->execute(
                        array(
                            $data['id_tipo'],
                            Session::get('usuid'),
                            $data['id_responsable'],
                            $data["motivo"],
                            $fecha
                        ));

            /* El ultimo ID que se ha generado */
            $id = $this->db->lastInsertId();
            
            /* Recorremos el detalle para insertar */
            foreach($data['items'] as $d)
            {
                $sql = "INSERT INTO tm_inventario (id_tipo_ope,id_ope,id_tipo_ins,id_ins,cos_uni,cant,fecha_r) 
                        VALUES (?,?,?,?,?,?,?)";
                
                $this->db->prepare($sql)
                ->execute(
                    array(
                        $data['id_tipo'],
                        $id,
                        $d['id_tipo_ins_insumo'],
                        $d['id_ins_insumo'],
                        $d['precio_insumo'],
                        $d['cantidad_insumo'],
                        $fecha
                    ));
            }
            
            return true;
        }
        catch (Exception $e) 
        {
            return false;
        }
    }

    public function ajuste_det($data)
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_inventario WHERE id_tipo_ope = ? AND id_ope = ?");
            $stm->execute(array($data['id_tipo'], $data['id_es']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Producto'} = $this->db->query("SELECT * FROM v_insprod WHERE id_tipo_ins = ".$d->id_tipo_ins."  AND id_ins = ".$d->id_ins)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function ajuste_delete($data)
    {
        try 
        {
            $consulta = "call usp_invESAnular( :flag, :id_es, :id_tipo);";
            $arrayParam =  array(
                ':flag' => 1,
                ':id_es' => $data['id_es'],
                ':id_tipo' => $data['id_tipo']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            return $row;
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function ajuste_insumo_buscar($data)
    {
        try
        {        
            $cadena = $data['cadena'];
            $stm = $this->db->prepare("SELECT * FROM v_insprod WHERE (ins_cod LIKE '%$cadena%' OR ins_nom LIKE '%$cadena%') AND est_b = 'a' AND est_c = 'a' AND id_tipo_ins <> 3 ORDER BY ins_nom LIMIT 5");
            $stm->execute();
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function combomedida($data)
    {
        try
        {   
            $stmm = $this->db->prepare("SELECT * FROM tm_tipo_medida WHERE grupo = ? OR grupo = ?");
            $stmm->execute(array($data['va1'],$data['va2']));
            $var = $stmm->fetchAll(PDO::FETCH_ASSOC);
            foreach($var as $v){
                echo '<option value="'.$v['id_med'].'">'.$v['descripcion'].'</option>';
            }
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    /* STOCK */
    public function stock_list($data)
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM v_stock WHERE id_tipo_ins LIKE ? AND debajo_stock LIKE ?");
            $stm->execute(array($data['tipo_ins'],$data['stock_min']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Producto'} = $this->db->query("SELECT * FROM v_insprod WHERE id_tipo_ins = ".$d->id_tipo_ins." AND id_ins = ".$d->id_ins)
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

    /* KARDEX VALORIZADO */
    public function kardex_list()
    {
        try
        {
            $tipo_ip = $_POST['tipo_ip'];
            $id_ip = $_POST['id_ip'];
            $ifecha = date('Y-m-d',strtotime($_POST['ifecha']));
            $ffecha = date('Y-m-d',strtotime($_POST['ffecha']));

            $stm = $this->db->prepare("SELECT id_inv,id_tipo_ope,id_ope,id_tipo_ins,id_ins,cos_uni,cant,fecha_r,estado,
                    IF(id_tipo_ope = 1 OR id_tipo_ope = 3,FORMAT(cant,6),0) AS cantidad_entrada, 
                    IF(id_tipo_ope = 1 OR id_tipo_ope = 3,cos_uni,0) AS costo_entrada, 
                    IF(id_tipo_ope = 1 OR id_tipo_ope = 3,(cant*cos_uni),0) AS total_entrada, 
                    IF(id_tipo_ope = 2 OR id_tipo_ope = 4,FORMAT(cant,6),0) AS cantidad_salida, 
                    IF(id_tipo_ope = 2 OR id_tipo_ope = 4,cos_uni,'-') AS costo_salida, 
                    IF(id_tipo_ope = 2 OR id_tipo_ope = 4,(cant*cos_uni),0) AS total_salida
                FROM tm_inventario WHERE id_tipo_ins = ? AND id_ins = ? AND (date(fecha_r) >= ? AND date(fecha_r) <= ?)");
            $stm->execute(array($tipo_ip,$id_ip,$ifecha,$ffecha));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Precio'} = $this->db->query("SELECT ROUND(AVG(cos_uni),2) AS cos_pro FROM tm_inventario WHERE id_tipo_ins = ".$d->id_tipo_ins." AND id_ins = ".$d->id_ins)
                    ->fetch(PDO::FETCH_OBJ);

                $c[$k]->{'Medida'} = $this->db->query("SELECT ins_med FROM v_insprod WHERE id_tipo_ins = ".$d->id_tipo_ins." AND id_ins = ".$d->id_ins)
                    ->fetch(PDO::FETCH_OBJ);

                $c[$k]->{'Stock'} = $this->db->query("SELECT SUM(ent - sal) AS total FROM v_stock WHERE id_tipo_ins = ".$tipo_ip." AND id_ins = ".$id_ip)
                    ->fetch(PDO::FETCH_OBJ);

                if($d->id_tipo_ope == 1){
                    $c[$k]->{'Comp'} = $this->db->query("SELECT serie_doc AS ser_doc,num_doc AS nro_doc,desc_td FROM v_compras WHERE id_compra = ".$d->id_ope)
                    ->fetch(PDO::FETCH_OBJ);
                } else if($d->id_tipo_ope == 2){
                    $c[$k]->{'Comp'} = $this->db->query("SELECT ser_doc,nro_doc,desc_td FROM v_ventas_con WHERE id_ven = ".$d->id_ope)
                    ->fetch(PDO::FETCH_OBJ);
                } else if($d->id_tipo_ope == 3 OR $d->id_tipo_ope == 4){
                    $c[$k]->{'Comp'} = $this->db->query("SELECT i.motivo, CONCAT(u.nombres,' ',u.ape_paterno,' ',u.ape_materno) AS responsable FROM tm_inventario_entsal AS i INNER JOIN tm_usuario AS u ON i.id_responsable = u.id_usu WHERE i.id_es = ".$d->id_ope)
                    ->fetch(PDO::FETCH_OBJ);
                }
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

    public function ComboInsumoProducto($data)
    {
        try
        {   
            $stmm = $this->db->prepare("SELECT id_ins,ins_cod,ins_nom,ins_cat FROM v_insprod WHERE id_tipo_ins = ? AND est_b = 'a' AND est_c = 'a'");
            $stmm->execute(array($data['id_tipo_ins']));
            $var = $stmm->fetchAll(PDO::FETCH_ASSOC);
            foreach($var as $v){
                echo '<option value="'.$v['id_ins'].'">'.$v['ins_cod'].' | '.$v['ins_cat'].' | '.$v['ins_nom'].'</option>';
            }
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }
}