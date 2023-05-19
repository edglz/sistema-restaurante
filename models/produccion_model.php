<?php Session::init(); ?>
<?php

class Produccion_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function mesas_list()
    {
        try
        {   
            $id_areap = Session::get('areaid');
            $stm = $this->db->prepare("SELECT * FROM v_pedidos_agrupados WHERE id_areap = ? AND tipo_atencion = 1 AND estado_pedido <> 'd' AND estado_pedido <> 'z' AND (estado = 'a' OR estado = 'b') ORDER BY fecha_pedido ASC");
            $stm->execute(array($id_areap));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Total'} = $this->db->query("SELECT IF(COUNT(id_pedido) > 0, COUNT(id_pedido), '') AS nro_p FROM v_pedidos_agrupados WHERE tipo_atencion = 1 AND estado_pedido <> 'd' AND estado_pedido <> 'z' AND (estado = 'a' OR estado = 'b') AND id_areap = ".$d->id_areap)
                    ->fetch(PDO::FETCH_OBJ);

                $c[$k]->{'Producto'} = $this->db->query("SELECT pro_cat FROM v_productos WHERE id_pres = ".$d->id_pres."")
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;   
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function mostrador_list()
    {
        try
        {        
            $id_areap = Session::get('areaid');
            $stm = $this->db->prepare("SELECT * FROM v_pedidos_agrupados WHERE id_areap = ? AND tipo_atencion = 2 AND estado_pedido <> 'd' AND estado_pedido <> 'z' AND (estado = 'a' OR estado = 'b') ORDER BY fecha_pedido ASC");
            $stm->execute(array($id_areap));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Total'} = $this->db->query("SELECT IF(COUNT(id_pedido) > 0, COUNT(id_pedido), '') AS nro_p FROM v_pedidos_agrupados WHERE tipo_atencion = 2 AND estado_pedido <> 'd' AND estado_pedido <> 'z' AND (estado = 'a' OR estado = 'b') AND id_areap = ".$d->id_areap)
                    ->fetch(PDO::FETCH_OBJ);

                $c[$k]->{'Producto'} = $this->db->query("SELECT pro_cat FROM v_productos WHERE id_pres = ".$d->id_pres."")
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;     
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function delivery_list()
    {
        try
        {        
            $id_areap = Session::get('areaid');
            $stm = $this->db->prepare("SELECT * FROM v_pedidos_agrupados WHERE id_areap = ? AND tipo_atencion = 3 AND estado_pedido <> 'd' AND estado_pedido <> 'z' AND (estado = 'a' OR estado = 'b') ORDER BY fecha_pedido ASC");
            $stm->execute(array($id_areap));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Total'} = $this->db->query("SELECT IF(COUNT(id_pedido) > 0, COUNT(id_pedido), '') AS nro_p FROM v_pedidos_agrupados WHERE tipo_atencion = 3 AND estado_pedido <> 'd' AND estado_pedido <> 'z' AND (estado = 'a' OR estado = 'b') AND id_areap = ".$d->id_areap)
                    ->fetch(PDO::FETCH_OBJ);

                $c[$k]->{'Producto'} = $this->db->query("SELECT pro_cat FROM v_productos WHERE id_pres = ".$d->id_pres."")
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;        
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function agrupacion_platos_list()
    {
        try
        {        
            $id_areap = Session::get('areaid');
            $stm = $this->db->prepare("SELECT nombre_prod, pres_prod, MIN(fecha_pedido) FROM v_pedidos_agrupados WHERE id_areap = ? AND estado_pedido <> 'd' AND estado_pedido <> 'z' AND (estado = 'a' OR estado = 'b') GROUP BY nombre_prod,pres_prod ORDER BY fecha_pedido ASC");
            $stm->execute(array($id_areap));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function agrupacion_platos_detalle($data)
    {
        try
        {        
            $id_areap = Session::get('areaid');
            $stm = $this->db->prepare("SELECT * FROM v_pedidos_agrupados WHERE id_areap = ? AND nombre_prod = ? AND pres_prod = ? AND estado_pedido <> 'd' AND estado_pedido <> 'z' AND (estado = 'a' OR estado = 'b') ORDER BY fecha_pedido ASC");
            $stm->execute(array($id_areap,$data['nombre_prod'],$data['pres_prod']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Producto'} = $this->db->query("SELECT pro_cat FROM v_productos WHERE id_pres = ".$d->id_pres."")
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function agrupacion_pedidos_list()
    {
        try
        {        
            $id_areap = Session::get('areaid');
            $stm = $this->db->prepare("SELECT id_pedido,tipo_atencion,nro_mesa,desc_salon,MIN(fecha_pedido) FROM v_pedidos_agrupados WHERE id_areap = ? AND estado_pedido <> 'd' AND estado_pedido <> 'z' AND (estado = 'a' OR estado = 'b') GROUP BY id_pedido ORDER BY fecha_pedido ASC");
            $stm->execute(array($id_areap));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function agrupacion_pedidos_detalle($data)
    {
        try
        {        
            $id_areap = Session::get('areaid');
            $stm = $this->db->prepare("SELECT * FROM v_pedidos_agrupados WHERE id_areap = ? AND id_pedido = ? AND estado_pedido <> 'd' AND estado_pedido <> 'z' AND (estado = 'a' OR estado = 'b') ORDER BY fecha_pedido ASC");
            $stm->execute(array($id_areap,$data['id_pedido']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Producto'} = $this->db->query("SELECT pro_cat FROM v_productos WHERE id_pres = ".$d->id_pres."")
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function preparacion($data)
    {
        try
        {   
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
            $fecha = date("Y-m-d H:i:s");
            $sql = "UPDATE tm_detalle_pedido SET estado = 'b', fecha_envio = ? WHERE id_pedido = ? AND id_pres = ? AND fecha_pedido = ?";
            $this->db->prepare($sql)
              ->execute(array(
                $fecha,
                $data['cod_ped'],
                $data['cod_prod'],
                $data['fecha_p']
                ));
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function atendido($data)
    {
        try
        {   
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
            $fecha = date("Y-m-d H:i:s");
            $sql = "UPDATE tm_detalle_pedido SET estado = 'c', fecha_envio = ? WHERE id_pedido = ? AND id_pres = ? AND fecha_pedido = ?";
            $this->db->prepare($sql)
              ->execute(array(
                $fecha,
                $data['cod_ped'],
                $data['cod_prod'],
                $data['fecha_p']
                ));
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }
}