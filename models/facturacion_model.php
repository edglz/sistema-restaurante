<?php Session::init();?>

<?php

class Facturacion_Model extends Model
{
    public function __construct()
	{
		parent::__construct();
	}

	public function Datos1()
    {
        try
        {
            if($_POST['estado'] == 1){
                $estado = "AND v.estado = 'a' AND v.enviado_sunat = '1'";
            } else if ($_POST['estado'] == 2){
                $estado = "AND v.estado = 'a' AND (v.enviado_sunat = '' OR v.enviado_sunat = '0' OR v.enviado_sunat IS NULL)";
            } else if ($_POST['estado'] == 3) {
                $estado = "AND v.estado = 'i'";
            } else {
                $estado = "";
            }

            $ifecha = date('Y-m-d',strtotime($_POST['ifecha']));
            $ffecha = date('Y-m-d',strtotime($_POST['ffecha']));
            $stm = $this->db->prepare("SELECT v.*,IFNULL((v.total+v.comis_del-v.desc_monto),0) AS total FROM v_ventas_con AS v INNER JOIN v_caja_aper AS c ON v.id_apc = c.id_apc WHERE (DATE_FORMAT(v.fec_ven,'%Y-%m-%d') >= ? AND DATE_FORMAT(v.fec_ven,'%Y-%m-%d') <= ?) AND v.id_tdoc like ? AND v.id_tdoc <> 3 ".$estado." GROUP BY v.id_ven");
            $stm->execute(array($ifecha,$ffecha,$_POST['tdoc']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);           
            foreach($c as $k => $d)
            {
                $c[$k]->{'Cliente'} = $this->db->query("SELECT dni,ruc,nombre FROM v_clientes WHERE id_cliente = ".$d->id_cli)
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

    public function Datos2()
    {
        try
        {
            $ifecha = date('Y-m-d',strtotime($_POST['ifecha']));
            $ffecha = date('Y-m-d',strtotime($_POST['ffecha']));
            $stm = $this->db->prepare("SELECT * FROM comunicacion_baja WHERE (fecha_baja >= ? AND fecha_baja <= ?) AND tipo_doc = '01'");
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

    public function Datos3()
    {
        try
        {
            $ifecha = date('Y-m-d',strtotime($_POST['ifecha']));
            $ffecha = date('Y-m-d',strtotime($_POST['ffecha']));
            $stm = $this->db->prepare("SELECT * FROM comunicacion_baja WHERE (fecha_baja >= ? AND fecha_baja <= ?) AND tipo_doc = '03'");
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

    public function Datos4()
    {
        try
        {
            $ifecha = date('Y-m-d',strtotime($_POST['ifecha']));
            $ffecha = date('Y-m-d',strtotime($_POST['ffecha']));
            $stm = $this->db->prepare("SELECT * FROM resumen_diario WHERE (fecha_resumen >= ? AND fecha_resumen <= ?)");
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

    public function Datos5()
    {
        try
        {
            $ifecha = date('Y-m-d',strtotime($_POST['ifecha']));
            $stm = $this->db->prepare("SELECT fec_ven,desc_td,ser_doc,nro_doc,(total-desc_monto) AS total FROM v_ventas_con WHERE id_tdoc = 1 AND (enviado_sunat IS NULL OR enviado_sunat = '') AND DATE_FORMAT(fec_ven,'%Y-%m-%d') = ?");
            $stm->execute(array($ifecha));
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

    public function Detalle()
    {
        try
        {      
            $stm = $this->db->prepare("SELECT v.id_cli,v.fec_ven,v.desc_td,v.ser_doc,v.nro_doc,(v.total-v.desc_monto) AS total FROM v_ventas_con AS v INNER JOIN resumen_diario_detalle AS rd ON v.id_ven = rd.id_venta WHERE rd.id_resumen = ?");
            $stm->execute(array($_POST['cod']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Cliente'} = $this->db->query("SELECT dni,nombre FROM v_clientes WHERE id_cliente = ".$d->id_cli)
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

    public function reenvio($data)
    {
        try {
            $stm = $this->db->prepare("UPDATE tm_venta SET enviado_sunat = 1, code_respuesta_sunat = 0 WHERE id_venta = ?");
            $stm->execute(array($data['cod_ven']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            return $c;
        } catch (Exception $e) {
            return false;
        }
    }

    public function pdf_factura($data)
    {
        try
        {      
            $stm = $this->db->prepare("SELECT * FROM v_ventas_con WHERE id_ven = ?");
            $stm->execute(array($data));
            $c = $stm->fetch(PDO::FETCH_OBJ);
            $c->{'Empresa'} = $this->db->query("SELECT * FROM tm_empresa")
                ->fetch(PDO::FETCH_OBJ);
            $c->{'Cliente'} = $this->db->query("SELECT * FROM v_clientes WHERE id_cliente = " . $c->id_cli)
                ->fetch(PDO::FETCH_OBJ);
            $c->{'Pedido'} = $this->db->query("SELECT vm.desc_salon, vm.nro_mesa  FROM tm_pedido_mesa AS pm INNER JOIN v_mesas AS vm ON pm.id_mesa = vm.id_mesa WHERE pm.id_pedido = " . $c->id_ped)
                ->fetch(PDO::FETCH_OBJ);
            /* Traemos el detalle */
            $c->{'Detalle'} = $this->db->query("SELECT v_productos.pro_cod AS codigo_producto, 
                CONCAT(v_productos.pro_nom,' ',v_productos.pro_pre) AS nombre_producto, 
                IF(v_productos.pro_imp='1','10','20') AS codigo_afectacion, 
                CAST(tm_detalle_venta.cantidad AS DECIMAL(7,2)) AS cantidad, 
                IF(v_productos.pro_imp='1',ROUND((tm_detalle_venta.precio/(1 + 0.18)),2),tm_detalle_venta.precio) AS valor_unitario,
                tm_detalle_venta.precio AS precio_unitario,
                IF(v_productos.pro_imp='1',ROUND((tm_detalle_venta.precio/(1 + 0.18))*tm_detalle_venta.cantidad,2),
                ROUND(tm_detalle_venta.precio*tm_detalle_venta.cantidad,2)) AS valor_venta,
                IF(v_productos.pro_imp='1',ROUND((tm_detalle_venta.precio/(1 + 0.18)*tm_detalle_venta.cantidad)*0.18,2),0) AS total_igv 
                FROM tm_detalle_venta 
                INNER JOIN tm_venta ON tm_detalle_venta.id_venta = tm_venta.id_venta 
                INNER JOIN v_productos ON tm_detalle_venta.id_prod = v_productos.id_pres 
                WHERE tm_venta.id_tipo_doc  IN ('1','2') AND tm_detalle_venta.precio > 0 AND tm_detalle_venta.id_venta = ".$data)
                ->fetchAll(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    /*
    public function ObtenerDatosImp($data)
    {
        try
        {      
            $stm = $this->db->prepare("SELECT * FROM v_ventas_con WHERE id_ven = ?");
            $stm->execute(array($data));
            $c = $stm->fetch(PDO::FETCH_OBJ);
            $c->{'Cliente'} = $this->db->query("SELECT * FROM v_clientes WHERE id_cliente = " . $c->id_cli)
                ->fetch(PDO::FETCH_OBJ);
            $c->{'Detalle'} = $this->db->query("SELECT id_prod,SUM(cantidad) AS cantidad, precio FROM tm_detalle_venta WHERE id_venta = " . $c->id_ven." GROUP BY id_prod")
                ->fetchAll(PDO::FETCH_OBJ);
            foreach($c->Detalle as $k => $d)
            {
                $c->Detalle[$k]->{'Producto'} = $this->db->query("SELECT nombre_prod, pres_prod FROM v_productos WHERE id_pres = " . $d->id_prod)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }
    */
}