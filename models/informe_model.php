<?php Session::init(); ?>
<?php

class Informe_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function TipoPedido()
    {
        try
        {      
            return $this->db->selectAll('SELECT * FROM tm_tipo_pedido');
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function Caja()
    {
        try
        {      
            return $this->db->selectAll('SELECT * FROM tm_caja');
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function Cliente()
    {
        try
        {      
            return $this->db->selectAll('SELECT id_cliente,nombre FROM v_clientes');
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function Categoria()
    {
        try
        {      
            return $this->db->selectAll('SELECT * FROM tm_producto_catg');
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function Producto()
    {
        try
        {      
            return $this->db->selectAll('SELECT * FROM tm_producto');
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function Presentacion()
    {
        try
        {      
            return $this->db->selectAll('SELECT * FROM tm_producto_pres');
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function Mozo()
    {
        try
        {      
            return $this->db->selectAll("SELECT id_usu, CONCAT(nombres,' ',ape_paterno,' ',ape_materno) AS nombre FROM v_usuarios WHERE id_rol = 5");
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function Proveedor()
    {
        try
        {      
            return $this->db->selectAll("SELECT * FROM tm_proveedor");
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function Cajero()
    {
        try
        {    
            return $this->db->selectAll("SELECT id_usu,ape_paterno,ape_materno,nombres FROM tm_usuario WHERE (id_rol = 1 OR id_rol = 2 OR id_rol = 3) AND id_usu <> 1 AND estado = 'a'");
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function Personal()
    {
        try
        {    
            return $this->db->selectAll("SELECT * FROM tm_usuario WHERE id_usu <> 1 AND estado = 'a' GROUP BY dni");
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function Repartidor()
    {
        try
        {    
            return $this->db->selectAll("SELECT * FROM tm_usuario WHERE id_rol = 6");
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function TipoDocumento()
    {
        try
        {   
            return $this->db->selectAll('SELECT * FROM tm_tipo_doc WHERE estado = "a"');
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function TipoPago()
    {
        try
        {   
            return $this->db->selectAll('SELECT * FROM tm_tipo_pago');
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function Empresa()
    {
        try
        {      
            return $this->db->selectOne("SELECT * FROM tm_empresa");
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    /* INICIO VENTAS */

    public function venta_all_list()
    {
        try
        {
            $ifecha = date('Y-m-d H:i:s',strtotime($_POST['ifecha']));
            $ffecha = date('Y-m-d H:i:s',strtotime($_POST['ffecha']));
            $stm = $this->db->prepare("SELECT v.id_ven,v.id_ped,v.id_tped,v.id_tpag,v.pago_efe,v.pago_tar,v.desc_monto,v.comis_tar,v.comis_del,v.total AS stotal,v.fec_ven,v.desc_td,v.ser_doc,v.nro_doc,v.estado,IFNULL((v.pago_efe + v.pago_tar),0) AS total,v.id_cli,v.igv,v.id_usu,v.desc_tipo,v.desc_personal,c.desc_caja FROM v_ventas_con AS v INNER JOIN v_caja_aper AS c ON v.id_apc = c.id_apc WHERE (v.fec_ven >= ? AND v.fec_ven <= ?) AND v.id_tped like ? AND v.id_tdoc like ? AND v.id_cli like ? AND v.estado like ?  GROUP BY v.id_ven");
            $stm->execute(array($ifecha,$ffecha,$_POST['tped'],$_POST['tdoc'],$_POST['cliente'],$_POST['estado']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
                       
            foreach($c as $k => $d)
            {
                $c[$k]->{'Pedido'} = $this->db->query("SELECT vm.desc_salon, vm.nro_mesa FROM tm_pedido_mesa AS pm INNER JOIN v_mesas AS vm ON pm.id_mesa = vm.id_mesa WHERE pm.id_pedido = ".$d->id_ped)
                    ->fetch(PDO::FETCH_OBJ);
            }
        
            foreach($c as $k => $d)
            {
                $c[$k]->{'Cliente'} = $this->db->query("SELECT nombre FROM v_clientes WHERE id_cliente = ".$d->id_cli)
                    ->fetch(PDO::FETCH_OBJ);
            }
            foreach($c as $k => $d)
            {
                $c[$k]->{'Personal'} = $this->db->query("SELECT CONCAT(nombres,' ',ape_paterno,' ',ape_materno) AS nombres FROM tm_usuario WHERE id_usu = ".$d->desc_personal)
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

    public function venta_all_det($data)
    {
        try
        {
            $stm = $this->db->prepare("SELECT id_prod,SUM(cantidad) AS cantidad,precio FROM tm_detalle_venta WHERE id_venta = ? GROUP BY id_prod, precio");
            $stm->execute(array($data['id_venta']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Comision'} = $this->db->query("SELECT comision_delivery AS total FROM tm_venta WHERE id_venta = ".$data['id_venta'])
                    ->fetch(PDO::FETCH_OBJ);
           
                $c[$k]->{'Descuento'} = $this->db->query("SELECT descuento_monto AS total FROM tm_venta WHERE id_venta = ".$data['id_venta'])
                    ->fetch(PDO::FETCH_OBJ);
        
                $c[$k]->{'Producto'} = $this->db->query("SELECT pro_nom,pro_pre FROM v_productos WHERE id_pres = ".$d->id_prod)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function venta_delivery_list()
    {
        try
        {
            $ifecha = date('Y-m-d',strtotime($_POST['ifecha']));
            $ffecha = date('Y-m-d',strtotime($_POST['ffecha']));
            $stm = $this->db->prepare("SELECT v.id_ven,v.id_ped,v.id_cli,v.id_apc,v.desc_td,v.ser_doc,v.nro_doc,v.pago_efe,v.pago_tar,IFNULL((v.pago_efe + v.pago_tar),0) AS total,v.fec_ven,d.tipo_entrega,d.id_repartidor,d.desc_repartidor FROM v_ventas_con AS v INNER JOIN v_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE (DATE(v.fec_ven) >= ? AND DATE(v.fec_ven) <= ?) AND d.id_repartidor LIKE ? AND d.tipo_entrega LIKE ? AND d.id_repartidor <> 1");
            $stm->execute(array($ifecha,$ffecha,$_POST['id_repartidor'],$_POST['tipo_entrega']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);           
            foreach($c as $k => $d)
            {
                $c[$k]->{'Cliente'} = $this->db->query("SELECT nombre FROM v_clientes WHERE id_cliente = ".$d->id_cli)
                    ->fetch(PDO::FETCH_OBJ);

                $c[$k]->{'Caja'} = $this->db->query("SELECT desc_caja FROM v_caja_aper WHERE id_apc = ".$d->id_apc)
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

    public function venta_culqi_list()
    {
        try
        {
            $ifecha = date('Y-m-d',strtotime($_POST['ifecha']));
            $ffecha = date('Y-m-d',strtotime($_POST['ffecha']));
            $stm = $this->db->prepare("SELECT v.desc_td,v.ser_doc,v.nro_doc,v.total,v.igv,d.tipo_entrega,d.nombre_cliente,d.email_cliente,d.fecha_pedido FROM v_ventas_con AS v INNER JOIN v_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE d.tipo_pago = 4 AND (DATE(v.fec_ven) >= ? AND DATE(v.fec_ven) <= ?) AND d.tipo_entrega LIKE ?");
            $stm->execute(array($ifecha,$ffecha,$_POST['tipo_entrega']));
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

    public function venta_prod_list($data)
    {
        try
        {
            $ifecha = date('Y-m-d H:i:s',strtotime($data['ifecha']));
            $ffecha = date('Y-m-d H:i:s',strtotime($data['ffecha']));
            $stm = $this->db->prepare("SELECT dp.id_prod, vp.id_pres AS id_pres,
            SUM(CASE WHEN v.id_tipo_pedido = 1 THEN dp.cantidad ELSE 0 END) AS cantidad_salon,
            SUM(CASE WHEN v.id_tipo_pedido = 2 THEN dp.cantidad ELSE 0 END) AS cantidad_mostrador,
            SUM(CASE WHEN v.id_tipo_pedido = 3 THEN dp.cantidad ELSE 0 END) AS cantidad_delivery,
            SUM(CASE WHEN v.id_tipo_pedido = 4 THEN dp.cantidad ELSE 0 END) AS cantidad_portero,
            SUM(dp.cantidad) AS cantidad_total,dp.precio,IFNULL((SUM(dp.cantidad)*dp.precio),0) AS total,v.fecha_venta
            FROM tm_detalle_venta AS dp 
            INNER JOIN tm_venta AS v ON dp.id_venta = v.id_venta 
            INNER JOIN v_productos AS vp ON vp.id_pres = dp.id_prod
            WHERE (v.fecha_venta >= ? AND v.fecha_venta <= ?) AND vp.id_catg LIKE ?
            AND vp.id_prod LIKE ? AND vp.id_pres LIKE ? AND v.estado = 'a' GROUP BY dp.id_prod, dp.precio
            ORDER BY v.fecha_venta DESC, SUM(dp.cantidad) DESC;");
           
            /*
            $stm = $this->db->prepare("SELECT dp.id_prod,SUM(dp.cantidad) AS cantidad,dp.precio,IFNULL((SUM(dp.cantidad)*dp.precio),0) AS total,v.fecha_venta FROM tm_detalle_venta AS dp INNER JOIN tm_venta AS v ON dp.id_venta = v.id_venta INNER JOIN v_productos AS vp ON vp.id_pres = dp.id_prod WHERE (DATE(v.fecha_venta) >= ? AND DATE(v.fecha_venta) <= ?) AND vp.id_catg like ? AND vp.id_prod like ? AND vp.id_pres like ? AND v.estado = 'a' GROUP BY dp.id_prod, dp.precio , DATE(v.fecha_venta) ORDER BY v.fecha_venta DESC, SUM(dp.cantidad) DESC");
            */
            $stm->execute(array($ifecha,$ffecha,$data['id_catg'],$data['id_prod'],$data['id_pres']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Producto'} = $this->db->query("SELECT pro_nom,pro_pre,pro_cat FROM v_productos WHERE id_pres = ".$d->id_prod)
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

    public function combPro($data)
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_producto WHERE id_catg = ?");
            $stm->execute(array($data['cod']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function combPre($data)
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_producto_pres WHERE id_prod = ?");
            $stm->execute(array($data['cod']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function venta_mozo_list($data)
    {
        try
        {
            $ifecha = date('Y-m-d H:i:s',strtotime($data['ifecha']));
            $ffecha = date('Y-m-d H:i:s',strtotime($data['ffecha']));
            $stm = $this->db->prepare("SELECT v.fec_ven,v.desc_td,CONCAT(v.ser_doc,'-',v.nro_doc) AS numero,IFNULL(SUM(v.pago_efe+v.pago_tar),0) AS total,v.id_cli,pm.id_mozo FROM v_ventas_con AS v INNER JOIN tm_pedido_mesa AS pm ON v.id_ped = pm.id_pedido WHERE (v.fec_ven >= ? AND v.fec_ven <= ?) AND pm.id_mozo like ? AND v.estado = 'a' GROUP BY v.id_ven");
            $stm->execute(array($ifecha,$ffecha,$data['id_mozo']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);        
            foreach($c as $k => $d)
            {
                $c[$k]->{'Mozo'} = $this->db->query("SELECT CONCAT(nombres,' ',ape_paterno,' ',ape_materno) AS nombre FROM v_usuarios WHERE id_usu = ".$d->id_mozo)
                    ->fetch(PDO::FETCH_OBJ);

                $c[$k]->{'Cliente'} = $this->db->query("SELECT nombre FROM v_clientes WHERE id_cliente = ".$d->id_cli)
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


    public function venta_fpago_list($data)
    {
        try
        {
            $ifecha = date('Y-m-d H:i:s',strtotime($data['ifecha']));
            $ffecha = date('Y-m-d H:i:s',strtotime($data['ffecha']));
            /*
            if($data['id_tpag'] == 1){
                $tipo_pago = 'AND (v.id_tpag = 1 OR v.id_tpag = 3) ';
            } else if($data['id_tpag'] == 2){
                $tipo_pago = 'AND (v.id_tpag = 2 OR v.id_tpag = 3) ';
            } else if($data['id_tpag'] == 3){
                $tipo_pago = 'AND (v.id_tpag = 1 OR v.id_tpag = 2 OR v.id_tpag = 3) ';
            } else {
                $tipo_pago = '';
            }
            */
            $stm = $this->db->prepare("SELECT v.id_ven,v.id_ped,v.id_tpag,v.pago_efe,v.pago_tar,v.desc_monto,v.comis_tar,v.comis_del,v.total AS stotal,v.fec_ven,v.desc_td,CONCAT(v.ser_doc,'-',v.nro_doc) AS numero,IFNULL(SUM(v.pago_efe+v.pago_tar),0) AS total,v.id_cli,v.igv,v.id_usu,c.desc_caja,v.estado,v.codigo_operacion FROM v_ventas_con AS v INNER JOIN v_caja_aper AS c ON v.id_apc = c.id_apc WHERE (v.fec_ven >= ? AND v.fec_ven <= ?) AND v.id_tpag LIKE ? AND v.estado = 'a' GROUP BY v.id_ven ORDER BY DATE(v.fec_ven) ASC");
            $stm->execute(array($ifecha,$ffecha,$data['id_tpag']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);           
            foreach($c as $k => $d)
            {
                $c[$k]->{'Cliente'} = $this->db->query("SELECT nombre FROM v_clientes WHERE id_cliente = ".$d->id_cli)
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

    public function venta_desc_list($data)
    {
        try
        {
            $ifecha = date('Y-m-d H:i:s',strtotime($data['ifecha']));
            $ffecha = date('Y-m-d H:i:s',strtotime($data['ffecha']));
            $stm = $this->db->prepare("SELECT v.id_ven,v.id_ped,v.id_tpag,v.pago_efe,v.pago_tar,v.desc_monto,v.desc_tipo,v.desc_motivo,v.comis_tar,v.comis_del,v.total AS stotal,v.fec_ven,v.desc_td,CONCAT(v.ser_doc,'-',v.nro_doc) AS numero,IFNULL(SUM(v.pago_efe+v.pago_tar),0) AS total,v.id_cli,v.igv,v.id_usu,c.desc_caja,v.desc_usu FROM v_ventas_con AS v INNER JOIN v_caja_aper AS c ON v.id_apc = c.id_apc WHERE (v.fec_ven >= ? AND v.fec_ven <= ?) AND v.desc_tipo LIKE ? AND v.estado = 'a' AND v.desc_monto > 0 GROUP BY v.id_ven ORDER BY DATE(v.fec_ven) ASC");
            $stm->execute(array($ifecha,$ffecha,$data['desc_tipo']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            /*        
            foreach($c as $k => $d)
            {
                $c[$k]->{'Cliente'} = $this->db->query("SELECT nombre FROM v_clientes WHERE id_cliente = ".$d->id_cli)
                    ->fetch(PDO::FETCH_OBJ);
            }
            */
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;       
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function venta_all_imp($data)
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
                WHERE tm_venta.id_tipo_doc  IN ('1','2','3') AND tm_detalle_venta.precio > 0 AND tm_detalle_venta.id_venta = ".$data)
                ->fetchAll(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function venta_all_imp_($data)
    {
        try
        {      
            $stm = $this->db->prepare("SELECT * FROM v_ventas_con WHERE id_ven = ?");
            $stm->execute(array($data));
            $c = $stm->fetch(PDO::FETCH_OBJ);
            $c->{'Cliente'} = $this->db->query("SELECT * FROM v_clientes WHERE id_cliente = " . $c->id_cli)
                ->fetch(PDO::FETCH_OBJ);
            $c->{'Pedido'} = $this->db->query("SELECT vm.desc_salon, vm.nro_mesa  FROM tm_pedido_mesa AS pm INNER JOIN v_mesas AS vm ON pm.id_mesa = vm.id_mesa WHERE pm.id_pedido = " . $c->id_ped)
                ->fetch(PDO::FETCH_OBJ);

            $c->{'Detalle'} = $this->db->query("SELECT v_productos.pro_cod AS codigo_producto, 
                v_productos.id_areap AS area_id,
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
                WHERE tm_venta.id_tipo_doc  IN ('1','2','3') AND tm_detalle_venta.precio > 0 AND tm_detalle_venta.id_venta = ".$data)
                ->fetchAll(PDO::FETCH_OBJ);
            /*
            $c->{'Detalle'} = $this->db->query("SELECT id_prod,SUM(cantidad) AS cantidad, precio FROM tm_detalle_venta WHERE id_venta = " . $c->id_ven." GROUP BY id_prod, precio")
                ->fetchAll(PDO::FETCH_OBJ);
            foreach($c->Detalle as $k => $d)
            {
                $c->Detalle[$k]->{'Producto'} = $this->db->query("SELECT pro_nom, pro_pre FROM v_productos WHERE id_pres = " . $d->id_prod)
                    ->fetch(PDO::FETCH_OBJ);
            }
            */
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    /* FIN MODULO VENTAS */

    /* INICIO MODULO COMPRAS */

    public function compra_all_list($data)
    {
        try
        {
            $ifecha = date('Y-m-d',strtotime($data['ifecha']));
            $ffecha = date('Y-m-d',strtotime($data['ffecha']));
            $stm = $this->db->prepare("SELECT * FROM v_compras WHERE (fecha_c >= ? AND fecha_c <= ?) AND id_prov LIKE ? AND id_tipo_compra LIKE ? AND id_tipo_doc LIKE ? AND estado LIKE ? GROUP BY id_compra");
            $stm->execute(array($ifecha,$ffecha,$data['id_prov'],$data['id_tipo_compra'],$data['id_tipo_doc'],$data['estado']));
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

    public function compra_all_det($data)
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_compra_detalle WHERE id_compra = ?");
            $stm->execute(array($data['id_compra']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Producto'} = $this->db->query("SELECT ins_cod,ins_nom,ins_med,ins_cat FROM v_insprod WHERE id_tipo_ins = ".$d->id_tp."  AND id_ins = ".$d->id_pres)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function compra_all_det_cuota($data)
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_compra_credito WHERE id_compra = ?");
            $stm->execute(array($data['id_compra']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function compra_all_det_subcuota($data)
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_credito_detalle WHERE id_credito = ?");
            $stm->execute(array($data['id_credito']));
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

    /* FIN MODULO COMPRAS */

    /* INICIO MODULO FINANZAS */

    public function finanza_arq_list($data)
    {
        try
        {
            $ifecha = date('Y-m-d H:i:s',strtotime($data['ifecha']));
            $ffecha = date('Y-m-d H:i:s',strtotime($data['ffecha']));
            $stm = $this->db->prepare("SELECT * FROM v_caja_aper WHERE fecha_aper >= ? AND fecha_aper <= ? AND id_usu like ? ORDER BY id_apc DESC");
            $stm->execute(array($ifecha,$ffecha,$data['id_usu']));
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

    public function finanza_arq_resumen_default($data)
    {
        try
        {    
            $stm = $this->db->prepare("SELECT v.id_apc,v.id_ped,IFNULL(SUM(v.pago_efe),0) AS pago_efe, IFNULL(SUM(v.pago_tar),0) AS pago_tar, IFNULL(SUM(v.desc_monto),0) AS descu, IFNULL(SUM(v.comis_tar),0) AS comis_tar, IFNULL(SUM(v.comis_del),0) AS comis_del, IFNULL(SUM(v.pago_efe+v.pago_tar),0) AS total, v.estado FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = ? AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000)");
            $stm->execute(array($data['cod_ape']));
            $c = $stm->fetch(PDO::FETCH_OBJ);
            $c->{'Apertura'} = $this->db->query("SELECT * FROM v_caja_aper WHERE id_apc = ".$data['cod_ape'])
            ->fetch(PDO::FETCH_OBJ);
            $c->{'Ingresos'} = $this->db->query("SELECT IFNULL(SUM(importe),0) AS total FROM tm_ingresos_adm WHERE id_apc = {$data['cod_ape']} AND estado='a'")
            ->fetch(PDO::FETCH_OBJ);
            $c->{'EgresosA'} = $this->db->query("SELECT IFNULL(SUM(importe),0) AS total FROM v_gastosadm WHERE id_apc = {$data['cod_ape']} AND (id_tg = 1 OR id_tg = 2 OR id_tg = 3) AND estado='a'")
            ->fetch(PDO::FETCH_OBJ);
            $c->{'EgresosB'} = $this->db->query("SELECT IFNULL(SUM(importe),0) AS total FROM v_gastosadm WHERE id_apc = {$data['cod_ape']} AND id_tg = 4 AND estado='a'")
            ->fetch(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function finanza_arq_resumen_venta_list($data)
    {
        try
        {   
            if($data['cod_filtro'] == 1){
                $stm = $this->db->prepare("SELECT IFNULL((v.pago_efe+v.pago_tar),0) AS monto_total,v.estado,v.ser_doc,v.nro_doc,v.desc_td,v.desc_monto FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = ? AND v.estado = ? AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000)");
            } else {
                $stm = $this->db->prepare("SELECT IFNULL((v.pago_efe+v.pago_tar),0) AS monto_total,v.estado,v.ser_doc,v.nro_doc,v.desc_td,v.desc_monto FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = ? AND v.estado = ? AND v.desc_monto <> '0.00' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000)");
            }
            $stm->execute(array($data['cod_ape'],$data['estado']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function finanza_arq_resumen_venta_delivery_list($data)
    {
        try
        {   
            $stm = $this->db->prepare("SELECT IFNULL((v.pago_efe+v.pago_tar),0) AS monto_total,v.estado,v.ser_doc,v.nro_doc,v.desc_td FROM v_ventas_con AS v INNER JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = ? AND v.estado = ?");
            $stm->execute(array($data['cod_ape'],$data['estado']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function finanza_arq_resumen_caja_list_i($data)
    {
        try
        {   
            $stm = $this->db->prepare("SELECT * FROM tm_ingresos_adm WHERE id_apc = ? AND estado = ?");
            $stm->execute(array($data['id_apc'],$data['estado']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function finanza_arq_resumen_caja_list_e($data)
    {
        try
        {   
            $stm = $this->db->prepare("SELECT * FROM v_gastosadm WHERE id_apc = ? AND estado = ?");
            $stm->execute(array($data['id_apc'],$data['estado']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function finanza_arq_resumen_productos($data)
    {
        try
        {
            $stm = $this->db->prepare("SELECT d.id_prod,SUM(d.cantidad) AS cantidad, d.precio FROM tm_venta AS v INNER JOIN tm_detalle_venta AS d ON v.id_venta = d.id_venta WHERE v.id_apc = ? AND v.estado = 'a' GROUP BY d.id_prod, d.precio ORDER BY cantidad DESC");
            $stm->execute(array($data['id_apc']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Producto'} = $this->db->query("SELECT pro_nom,pro_pre FROM v_productos WHERE id_pres = ".$d->id_prod)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function finanza_arq_resumen_anulaciones($data)
    {
        try
        {
            $stm = $this->db->prepare("SELECT dp.cant, dp.precio, dp.id_pres FROM tm_detalle_pedido AS dp INNER JOIN tm_pedido AS p ON dp.id_pedido = p.id_pedido WHERE dp.estado = 'z' AND p.id_apc = ?");
            $stm->execute(array($data['cod_ape']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Producto'} = $this->db->query("SELECT pro_nom,pro_pre FROM v_productos WHERE id_pres = ".$d->id_pres)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function finanza_ing_list($data)
    {
        try
        {
            $ifecha = date('Y-m-d',strtotime($data['ifecha']));
            $ffecha = date('Y-m-d',strtotime($data['ffecha']));
            $stm = $this->db->prepare("SELECT * FROM tm_ingresos_adm WHERE (DATE(fecha_reg) >= ? AND DATE(fecha_reg) <= ?) AND id_usu LIKE ? AND estado LIKE ?");
            $stm->execute(array($ifecha,$ffecha,$data['id_usu'],$data['estado']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Caja'} = $this->db->query("SELECT desc_caja FROM v_caja_aper WHERE id_apc = ".$d->id_apc)
                    ->fetch(PDO::FETCH_OBJ);
            }
            foreach($c as $k => $d)
            {
                $c[$k]->{'Cajero'} = $this->db->query("SELECT CONCAT(nombres,' ',ape_paterno,' ',ape_materno) AS desc_usu FROM tm_usuario WHERE id_usu = ".$d->id_usu)
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

    public function finanza_egr_list($data)
    {
        try
        {
            $ifecha = date('Y-m-d',strtotime($data['ifecha']));
            $ffecha = date('Y-m-d',strtotime($data['ffecha']));
            $stm = $this->db->prepare("SELECT * FROM v_gastosadm WHERE (DATE(fecha_re) >= ? AND DATE(fecha_re) <= ?) AND id_tg LIKE ? AND id_usu LIKE ? AND estado LIKE ?");
            $stm->execute(array($ifecha,$ffecha,$data['tipo_gasto'],$data['id_usu'],$data['estado']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Caja'} = $this->db->query("SELECT desc_caja FROM v_caja_aper WHERE id_apc = ".$d->id_apc)
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

    public function finanza_rem_list($data)
    {
        try
        {
            $ifecha = date('Y-m-d',strtotime($data['ifecha']));
            $ffecha = date('Y-m-d',strtotime($data['ffecha']));
            $stm = $this->db->prepare("SELECT id_usu,fecha_re,id_apc,des_tg,desc_usu,desc_per,motivo,importe,estado FROM v_gastosadm WHERE id_tg = 3 AND (DATE(fecha_re) >= ? AND DATE(fecha_re) <= ?) AND id_per LIKE ? AND estado LIKE ?");
            $stm->execute(array($ifecha,$ffecha,$data['id_per'],$data['estado']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Caja'} = $this->db->query("SELECT desc_caja FROM v_caja_aper WHERE id_apc = ".$d->id_apc)
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

    public function oper_anul_list($data)
    {
        try
        {
            $ifecha = date('Y-m-d H:i:s',strtotime($data['ifecha']));
            $ffecha = date('Y-m-d H:i:s',strtotime($data['ffecha']));
            $stm = $this->db->prepare("SELECT * FROM tm_detalle_pedido WHERE (fecha_pedido >= ? AND fecha_pedido <= ?) AND id_usu like ? AND estado = 'z'");
            $stm->execute(array($ifecha,$ffecha,$data['id_usu']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Personal'} = $this->db->query("SELECT id_usu,CONCAT(ape_paterno,' ',ape_materno,' ',nombres) AS nombres FROM tm_usuario WHERE id_usu = ".$d->id_usu)
                    ->fetch(PDO::FETCH_OBJ);
            }
            foreach($c as $k => $d)
            {
                $c[$k]->{'TipoPedido'} = $this->db->query("SELECT id_tipo_pedido FROM tm_pedido WHERE id_pedido = ".$d->id_pedido)
                    ->fetch(PDO::FETCH_OBJ);
            }
            foreach($c as $k => $d)
            {
                $c[$k]->{'Producto'} = $this->db->query("SELECT * FROM v_productos WHERE id_pres = ".$d->id_pres)
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

    public function finanza_arq_imp($data)
    {
        try
        {      
            $stm = $this->db->prepare("SELECT * FROM v_caja_aper WHERE id_apc = ?");
            $stm->execute(array($data));
            $c = $stm->fetch(PDO::FETCH_OBJ);

            $c->{'Principal'} = $this->db->query("SELECT v.id_apc,v.id_ped,IFNULL(SUM(v.pago_efe),0) AS pago_efe, IFNULL(SUM(v.pago_tar),0) AS pago_tar, IFNULL(SUM(v.desc_monto),0) AS descu, IFNULL(SUM(v.comis_tar),0) AS comis_tar, IFNULL(SUM(v.comis_del),0) AS comis_del, IFNULL(SUM(v.pago_efe+v.pago_tar),0) AS total, v.estado FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$data} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000)")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'Efectivo'} = $this->db->query("SELECT IF(v.id_tpag = 1 OR v.id_tpag = 3,SUM(v.pago_efe),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$data} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND (v.id_tpag = 1 OR v.id_tpag = 3)")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'Tarjeta'} = $this->db->query("SELECT IF(v.id_tpag = 2 OR v.id_tpag = 3,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$data} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND (v.id_tpag = 2 OR v.id_tpag = 3)")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'Culqi'} = $this->db->query("SELECT IF(v.id_tpag = 4,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$data} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 4")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'Yape'} = $this->db->query("SELECT IF(v.id_tpag = 5,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$data} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 5")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'Lukita'} = $this->db->query("SELECT IF(v.id_tpag = 6,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$data} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 6")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'Transferencias'} = $this->db->query("SELECT IF(v.id_tpag = 7,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$data} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 7")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'Estilos'} = $this->db->query("SELECT IF(v.id_tpag = 8,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$data} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 8")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'Credishop'} = $this->db->query("SELECT IF(v.id_tpag = 9,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$data} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 9")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'Tasa'} = $this->db->query("SELECT IF(v.id_tpag = 10,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$data} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 10")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'Plin'} = $this->db->query("SELECT IF(v.id_tpag = 11,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$data} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 11")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'Tunki'} = $this->db->query("SELECT IF(v.id_tpag = 12,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$data} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 12")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'Credito'} = $this->db->query("SELECT IF(v.id_tpag = 13,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$data} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 13")
            ->fetch(PDO::FETCH_OBJ);

            /*
            $c->{'Izipay'} = $this->db->query("SELECT IF(v.id_tpag = 7,SUM(v.pago_tar),0) AS total FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$data} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor > 4) AND v.id_tpag = 7")
            ->fetch(PDO::FETCH_OBJ);
            */

            $c->{'Glovo'} = $this->db->query("SELECT IFNULL(SUM(v.pago_efe + v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$data} AND v.estado <> 'i' AND d.id_repartidor = 4444")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'Rappi'} = $this->db->query("SELECT IFNULL(SUM(v.pago_efe + v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$data} AND v.estado <> 'i' AND d.id_repartidor = 2222")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'Ingresos'} = $this->db->query("SELECT IFNULL(SUM(importe),0) AS total FROM tm_ingresos_adm WHERE id_apc = {$data} AND estado='a'")
            ->fetch(PDO::FETCH_OBJ);
            
            $c->{'Egresos'} = $this->db->query("SELECT IFNULL(SUM(importe),0) AS total FROM v_gastosadm WHERE id_apc = {$data} AND (id_tg = 1 OR id_tg = 2 OR id_tg = 3 OR id_tg = 4) AND estado='a'")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'EgresosA'} = $this->db->query("SELECT IFNULL(SUM(importe),0) AS total FROM v_gastosadm WHERE id_apc = {$data} AND id_tg = 1 AND estado='a'")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'EgresosB'} = $this->db->query("SELECT IFNULL(SUM(importe),0) AS total FROM v_gastosadm WHERE id_apc = {$data} AND id_tg = 2 AND estado='a'")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'EgresosC'} = $this->db->query("SELECT IFNULL(SUM(importe),0) AS total FROM v_gastosadm WHERE id_apc = {$data} AND id_tg = 3 AND estado='a'")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'EgresosD'} = $this->db->query("SELECT IFNULL(SUM(importe),0) AS total FROM v_gastosadm WHERE id_apc = {$data} AND id_tg = 4 AND estado='a'")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'Descuentos'} = $this->db->query("SELECT COUNT(id_ven) AS cant FROM v_ventas_con WHERE id_apc = {$data} AND desc_monto > '0.00' AND estado <> 'i'")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'ComisionDelivery'} = $this->db->query("SELECT COUNT(id_ven) AS cant FROM v_ventas_con WHERE id_apc = {$data} AND id_tped = 3 AND estado <> 'i'")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'Anulaciones'} = $this->db->query("SELECT COUNT(*) AS cant, SUM(pago_efe + pago_tar) total FROM tm_venta WHERE estado = 'i' AND id_apc = {$data}")
            ->fetch(PDO::FETCH_OBJ);

            /*
            $c->{'Anulaciones'} = $this->db->query("SELECT COUNT(dp.cant) AS cant, SUM(dp.precio * dp.cant) AS total FROM tm_detalle_pedido AS dp INNER JOIN tm_pedido AS p ON dp.id_pedido = p.id_pedido WHERE dp.estado = 'z' AND p.id_apc = {$data}")
            ->fetch(PDO::FETCH_OBJ);
            */

            $c->{'Deliverys'} = $this->db->query("SELECT SUM(IFNULL((v.pago_efe+v.pago_tar),0)) AS total, COUNT(*) AS cant FROM v_ventas_con AS v INNER JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$data} AND v.estado = 'a'")
            ->fetch(PDO::FETCH_OBJ);

            $c->{'PollosVendidos'} = $this->db->query("SELECT p.id_pres,p.pro_nom,p.pro_pre,dv.precio,SUM(dv.cantidad) AS cantidad, i.cant FROM tm_detalle_venta AS dv INNER JOIN tm_venta AS v ON dv.id_venta = v.id_venta INNER JOIN v_productos AS p ON dv.id_prod = p.id_pres INNER JOIN tm_producto_ingr AS i ON dv.id_prod = i.id_pres WHERE v.id_apc = {$data} AND v.estado = 'a' AND i.id_ins = 1 AND p.pro_mar = 1 GROUP BY dv.id_prod, dv.precio ORDER BY total DESC")
                ->fetchAll(PDO::FETCH_OBJ);

            $c->{'PolloStock'} = $this->db->query("SELECT (ent-sal) AS total FROM v_stock WHERE id_tipo_ins = 1 AND id_ins = 1")
            ->fetch(PDO::FETCH_OBJ);
                   
            $c->{'Detalle'} = $this->db->query("SELECT d.id_prod,SUM(d.cantidad) AS cantidad, d.precio FROM tm_venta AS v INNER JOIN tm_detalle_venta AS d ON v.id_venta = d.id_venta WHERE v.id_apc = {$data} AND v.estado = 'a' GROUP BY d.id_prod, d.precio ORDER BY 2 DESC")
                ->fetchAll(PDO::FETCH_OBJ);
            foreach($c->Detalle as $k => $d)
            {
                $c->Detalle[$k]->{'Producto'} = $this->db->query("SELECT pro_nom, pro_pre, id_areap FROM v_productos WHERE id_pres = " . $d->id_prod)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function finanza_adel_list_a($data)
    {
        try
        {
            $ifecha = date('Y-m-d H:i:s',strtotime($data['ifecha']));
            $ffecha = date('Y-m-d H:i:s',strtotime($data['ffecha']));
            $stm = $this->db->prepare("SELECT * FROM v_gastosadm WHERE (fecha_re >= ? AND fecha_re <= ?) AND id_per = ? AND id_tg = 3 AND estado = 'a'");
            $stm->execute(array($ifecha,$ffecha,$data['id_personal']));
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

    public function finanza_adel_list_b($data)
    {
        try
        {
            $ifecha = date('Y-m-d H:i:s',strtotime($data['ifecha']));
            $ffecha = date('Y-m-d H:i:s',strtotime($data['ffecha']));
            $stm = $this->db->prepare("SELECT *, (total + comis_del - desc_monto) AS total_venta FROM v_ventas_con WHERE (fec_ven >= ? AND fec_ven <= ?) AND desc_personal = ? AND desc_tipo = 3 AND estado = 'a'");
            $stm->execute(array($ifecha,$ffecha,$data['id_personal']));
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

     /* FIN MODULO FINANZAS */
    // MODULO PORTERO 
    public function portero_list_ventas($data){
       try{
        $ifecha = date('Y-m-d H:i:s',strtotime($data['ifecha']));
        $ffecha = date('Y-m-d H:i:s',strtotime($data['ffecha']));
        $estado = $data["estado"];
        $stm = $this->db->prepare("SELECT *FROM portero_ventas WHERE (fecha >= ? AND fecha <= ?) AND estado LIKE ?");
        $stm->execute(array($ifecha, $ffecha, $estado));
        $c = $stm->fetchAll(PDO::FETCH_OBJ);
        $data = array("data" => $c);
        $json = json_encode($data);
        echo $json;
       }catch(PDOException $e){
           echo "Error: " . $e->getMessage();
       }
    }
    public function get_portero_pedido($data){
        try{
            $pst = $this->db->prepare("SELECT *FROM portero_ventas_detalle INNER JOIN tm_producto_pres ON portero_ventas_detalle.id_pres = tm_producto_pres.id_pres 
            INNER JOIN tm_producto ON tm_producto_pres.id_prod = tm_producto.id_prod WHERE portero_ventas_detalle.id_venta = ?");
            $pst->execute(array($data["id_venta"]));
            $c = $pst->fetchAll(PDO::FETCH_OBJ);
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;
        }catch(PDOException $e){
            echo "Error: " . $e->getMessage();
        }
    }
    public function inf_venta_portero($token){
    $st = $this->db->prepare("SELECT v.id_usuario AS id_usuario, v.personas AS personas, v.monto_ingresado AS importe, v.monto_devuelto AS vuelto,
     v.total AS total, v.nro_tar AS cod_vaucher, v.fecha AS fecha, v.estado AS estado FROM portero_ventas AS v WHERE v.id_venta = {$token}");
    $st->execute();
    $c = $st->fetch(PDO::FETCH_OBJ);

    $c->{'Detalle'} = $this->db->query("SELECT v.id_pres, v.cantidad, v.total, v.estado FROM portero_ventas_detalle AS v
    WHERE v.id_venta = {$token}")->fetchAll(PDO::FETCH_OBJ);
    foreach($c->Detalle as $k => $d){
        $c->Detalle[$k]->{'Producto'} = $this->db->query("SELECT pro_nom, pro_pre, id_areap FROM v_productos WHERE id_pres = {$d->id_pres}")
        ->fetch(PDO::FETCH_OBJ);
    }
    $c->{'Portero'} = $this->db->query("SELECT id_usu, CONCAT(nombres,' ',ape_paterno,' ',ape_materno) AS nombre FROM v_usuarios WHERE id_usu = {$c->id_usuario}")->fetch(PDO::FETCH_OBJ);
    return $c;
    }
    public function Areas(){
        $st = $this->db->prepare("SELECT *FROM tm_area_prod WHERE estado LIKE '%a' ");
        $st->execute();
        $c = $st->fetchAll(PDO::FETCH_OBJ);
        return $c;
    }
    public function inf_aperturas_portero($data){
        try{
            $ifecha = date('Y-m-d H:i:s',strtotime($data['ifecha']));
            $ffecha = date('Y-m-d H:i:s',strtotime($data['ffecha']));
            $estado = $data["estado"];
            $stm = $this->db->prepare("SELECT v.id_apertura AS id_ap, v.id_usuario AS id_usu, v.fecha_apertura AS fecha_ap, v.monto_inicial AS monto_inicial,
            v.monto_final AS monto_final, v.fecha_cierre AS cierre, v.estado AS estado FROM portero_apertura AS v WHERE (dia >= ? AND dia <= ?) AND estado LIKE ?");
            $stm->execute(array($ifecha, $ffecha, $estado));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach ($c as $k => $d){
                $c[$k]->{'Portero'} = $this->db->query("SELECT id_usu, CONCAT(nombres,' ',ape_paterno,' ',ape_materno) AS nombre FROM v_usuarios WHERE id_usu = {$d->id_usu}")->fetch(PDO::FETCH_OBJ);
            }
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;
           }catch(PDOException $e){
               echo "Error: " . $e->getMessage();
           }
    }
    public function rep_ventas_portero($data){
        try
        {
           $c = $this->db->query("SELECT d.id_pres, SUM(d.cantidad) AS cantidad, SUM(d.total) AS total, p.precio  AS precio_unitario
           FROM portero_ventas AS v 
           INNER JOIN portero_ventas_detalle AS d  ON v.id_venta = d.id_venta 
           INNER JOIN tm_producto_pres AS p ON d.id_pres = p.id_pres 
           WHERE v.id_apertura = {$data} AND v.estado = 'Pagado' GROUP BY d.id_pres, d.total  ORDER BY 2 DESC")->fetchAll(PDO::FETCH_OBJ);
            foreach ($c as $k => $d){
                $c[$k]->{'Producto'} = $this->db->query("SELECT pro_nom, pro_pre, id_areap FROM v_productos WHERE  id_pres = " . $d->id_pres)
                ->fetch(PDO::FETCH_OBJ);
            }
           return $c;
        }catch(PDOException $ex){
            echo "Error: " . $ex->getMessage();
        }
    }
    public function rep_apertura_portero($data){
        $c = $this->db->query("SELECT v.id_apertura AS id_apc, v.id_usuario AS id_usuario, v.fecha_apertura AS apertura, v.monto_inicial AS monto_inicial, v.monto_final AS monto_acumulado, v.fecha_cierre AS cierre, v.estado AS estado FROM portero_apertura AS v WHERE v.id_apertura = {$data}")->fetchAll(PDO::FETCH_OBJ);
        foreach($c as $k => $d){
            $c[$k]->{'Portero'} = $this->db->query("SELECT id_usu, CONCAT(nombres,' ',ape_paterno,' ',ape_materno) AS nombre FROM v_usuarios WHERE id_usu = {$d->id_usuario}")->fetchAll(PDO::FETCH_OBJ);
        }
        return $c;
    }
    public function rep_ingresos($data){
        $ifecha = date('Y-m-d H:i:s', strtotime($data['ifecha']));
        $ffecha = date('Y-m-d H:i:s', strtotime($data['ffecha']));
        $stm  = $this->db->prepare("SELECT v.motivo, v.monto, v.fecha, v.id_apertura FROM portero_ingreso AS v WHERE (v.fecha >= ? AND v.fecha <= ?)");
        $stm->execute(array($ifecha, $ffecha));
        $c = $stm->fetchAll(PDO::FETCH_OBJ);
        foreach($c as $k => $d){
            $c[$k]->{'Apertura'} = $this->db->query("SELECT v.id_usuario FROM portero_apertura AS v WHERE v.id_apertura = {$d->id_apertura}")->fetch(PDO::FETCH_OBJ);
        }
        foreach($c as $k => $d){
            $c[$k]->{'Portero'} = $this->db->query("SELECT id_usu, CONCAT(nombres,' ',ape_paterno,' ',ape_materno) AS nombre FROM v_usuarios WHERE id_usu = {$d->Apertura->id_usuario}")->fetch(PDO::FETCH_OBJ);
        }
        $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;
     }
     public function rep_egresos($data){
        $ifecha = date('Y-m-d H:i:s', strtotime($data['ifecha']));
        $ffecha = date('Y-m-d H:i:s', strtotime($data['ffecha']));
        $stm = $this->db->prepare("SELECT v.motivo, v.monto, v.receptor, v.fecha, v.estado, v.id_apertura AS id_ap FROM portero_egresos AS v WHERE (v.fecha >= ? AND v.fecha <= ?)");
        $stm->execute(array($ifecha, $ffecha));
        $c = $stm->fetchAll(PDO::FETCH_OBJ);
        foreach($c as $k => $d){
            $c[$k]->{'Apertura'} = $this->db->query("SELECT v.id_usuario FROM portero_apertura AS v WHERE v.id_apertura = {$d->id_ap}")->fetch(PDO::FETCH_OBJ);
        }
        foreach($c as $k => $d){
            $c[$k]->{'Portero'} = $this->db->query("SELECT id_usu, CONCAT(nombres,' ',ape_paterno,' ',ape_materno) AS nombre FROM v_usuarios WHERE id_usu = {$d->Apertura->id_usuario}")->fetch(PDO::FETCH_OBJ);
        }
        $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;
     }
     function venta_portero_list($data){
         
        // $stm = $this->db->prepare("SELECT dp.id_prod, vp.id_pres AS id_pres,
        // SUM(CASE WHEN v.id_tipo_pedido = 1 THEN dp.cantidad ELSE 0 END) AS cantidad_salon,
        // SUM(CASE WHEN v.id_tipo_pedido = 2 THEN dp.cantidad ELSE 0 END) AS cantidad_mostrador,
        // SUM(CASE WHEN v.id_tipo_pedido = 3 THEN dp.cantidad ELSE 0 END) AS cantidad_delivery,
        // SUM(dp.cantidad) AS cantidad_total,dp.precio,IFNULL((SUM(dp.cantidad)*dp.precio),0) AS total,v.fecha_venta
        // FROM tm_detalle_venta AS dp 
        // INNER JOIN tm_venta AS v ON dp.id_venta = v.id_venta 
        // INNER JOIN v_productos AS vp ON vp.id_pres = dp.id_prod
        // WHERE (v.fecha_venta >= ? AND v.fecha_venta <= ?) AND vp.id_catg LIKE ?
        // AND vp.id_prod LIKE ? AND vp.id_pres LIKE ? AND v.estado = 'a' GROUP BY dp.id_prod, dp.precio
        // ORDER BY v.fecha_venta DESC, SUM(dp.cantidad) DESC;");

        $ifecha = date('Y-m-d H:i:s',strtotime($data['ifecha']));
        $ffecha = date('Y-m-d H:i:s',strtotime($data['ffecha']));
        $stm = $this->db->prepare("SELECT d.id_pres,
        IFNULL((SUM(d.cantidad)*p.precio),0) AS total, p.precio  AS precio_unitario,
        SUM(CASE WHEN v.estado = 'Pagado' THEN d.cantidad ELSE 0 END) AS cantidad
        FROM portero_ventas AS v 
        INNER JOIN portero_ventas_detalle AS d  ON v.id_venta = d.id_venta 
        INNER JOIN tm_producto_pres AS p ON d.id_pres = p.id_pres
        INNER JOIN v_productos AS vp ON p.id_pres = vp.id_pres
        WHERE(v.fecha >= ? AND v.fecha <= ?) 
        AND vp.id_catg LIKE ? AND vp.id_prod LIKE ? AND d.id_pres LIKE ?
        GROUP BY vp.id_prod 
        ORDER BY v.fecha DESC, SUM(d.cantidad) DESC;");

        // $stm = $this->db->prepare("SELECT dp.id_pres AS id_pres,
        // SUM(dp.cantidad) AS cantidad_portero,
        // SUM(dp.cantidad) AS cantidad_total, pr.precio, IFNULL((SUM(dp.cantidad)*pr.precio),0) AS total,v.fecha
        // FROM portero_ventas_detalle AS dp 
        // INNER JOIN portero_ventas AS v ON dp.id_venta = v.id_venta 
        // INNER JOIN tm_producto_pres AS pr ON dp.id_pres = pr.id_pres
        // INNER JOIN v_productos AS vp ON vp.id_pres = vp.id_pres
        // WHERE (v.fecha >= ? AND v.fecha <= ?) AND v.estado = 'Pagado' GROUP BY dp.id_pres");
        $stm->execute(array($ifecha,$ffecha, $data['id_catg'],$data['id_prod'],$data['id_pres']));
        $c = $stm->fetchAll(PDO::FETCH_OBJ);
        foreach($c as $k => $d)
        {
            $c[$k]->{'Producto'} = $this->db->query("SELECT pro_nom,pro_pre,pro_cat FROM v_productos WHERE id_pres = ".$d->id_pres)
                ->fetch(PDO::FETCH_OBJ);
        }
        
        $data = array("data" => $c);
        $json = json_encode($data);
       
        echo $json;

     }
     function informe_general($data){
        $cod = trim($data['rep_cod']);
        $stm = $this->db->query("SELECT COUNT(*)  as registros from tm_aper_cierre WHERE cod_reporte = '{$cod}'")->fetch(PDO::FETCH_OBJ);
        $d = array("data" => $stm);
        echo json_encode($d);
     }
     function informe_pdf($cod){
         $data = trim($cod);
         $c = $this->db->query("SELECT COUNT(*)  as registros from tm_aper_cierre WHERE cod_reporte = '{$data}'")->fetch(PDO::FETCH_OBJ);
         if($c->registros > 0):
             $c->{'aperturas'} = $this->db->query("SELECT * FROM v_caja_aper WHERE codigo = {$data}")->fetchAll(PDO::FETCH_OBJ);
             foreach($c->aperturas as $k => $d):
                $c->aperturas[$k]->{'Principal'} = $this->db->query("SELECT v.id_apc,v.id_ped,IFNULL(SUM(v.pago_efe),0) AS pago_efe, IFNULL(SUM(v.pago_tar),0) AS pago_tar, IFNULL(SUM(v.desc_monto),0) AS descu, IFNULL(SUM(v.comis_tar),0) AS comis_tar, IFNULL(SUM(v.comis_del),0) AS comis_del, IFNULL(SUM(v.pago_efe+v.pago_tar),0) AS total, v.estado FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$d->id_apc} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000)")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Efectivo'} = $this->db->query("SELECT IF(v.id_tpag = 1 OR v.id_tpag = 3,SUM(v.pago_efe),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$d->id_apc} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND (v.id_tpag = 1 OR v.id_tpag = 3)")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Tarjeta'} = $this->db->query("SELECT IF(v.id_tpag = 2 OR v.id_tpag = 3,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$d->id_apc} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND (v.id_tpag = 2 OR v.id_tpag = 3)")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Culqi'} = $this->db->query("SELECT IF(v.id_tpag = 4,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$d->id_apc} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 4")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Yape'} = $this->db->query("SELECT IF(v.id_tpag = 5,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$d->id_apc} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 5")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Lukita'} = $this->db->query("SELECT IF(v.id_tpag = 6,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$d->id_apc} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 6")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Transferencias'} = $this->db->query("SELECT IF(v.id_tpag = 7,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$d->id_apc} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 7")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Estilos'} = $this->db->query("SELECT IF(v.id_tpag = 8,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$d->id_apc} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 8")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Credishop'} = $this->db->query("SELECT IF(v.id_tpag = 9,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$d->id_apc} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 9")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Tasa'} = $this->db->query("SELECT IF(v.id_tpag = 10,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$d->id_apc} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 10")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Plin'} = $this->db->query("SELECT IF(v.id_tpag = 11,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$d->id_apc} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 11")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Tunki'} = $this->db->query("SELECT IF(v.id_tpag = 12,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$d->id_apc} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 12")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Credito'} = $this->db->query("SELECT IF(v.id_tpag = 13,SUM(v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$d->id_apc} AND v.estado <> 'i' AND (d.id_repartidor IS NULL OR d.id_repartidor = 1 OR d.id_repartidor < 2000) AND v.id_tpag = 13")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Glovo'} = $this->db->query("SELECT IFNULL(SUM(v.pago_efe + v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$d->id_apc} AND v.estado <> 'i' AND d.id_repartidor = 4444")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Rappi'} = $this->db->query("SELECT IFNULL(SUM(v.pago_efe + v.pago_tar),0) AS total, COUNT(*) AS cant FROM v_ventas_con AS v LEFT JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$d->id_apc} AND v.estado <> 'i' AND d.id_repartidor = 2222")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Ingresos'} = $this->db->query("SELECT IFNULL(SUM(importe),0) AS total FROM tm_ingresos_adm WHERE id_apc = {$d->id_apc} AND estado='a'")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Egresos'} = $this->db->query("SELECT IFNULL(SUM(importe),0) AS total FROM v_gastosadm WHERE id_apc = {$d->id_apc} AND (id_tg = 1 OR id_tg = 2 OR id_tg = 3 OR id_tg = 4) AND estado='a'")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'EgresosA'} = $this->db->query("SELECT IFNULL(SUM(importe),0) AS total FROM v_gastosadm WHERE id_apc = {$d->id_apc} AND id_tg = 1 AND estado='a'")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'EgresosB'} = $this->db->query("SELECT IFNULL(SUM(importe),0) AS total FROM v_gastosadm WHERE id_apc = {$d->id_apc} AND id_tg = 2 AND estado='a'")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'EgresosC'} = $this->db->query("SELECT IFNULL(SUM(importe),0) AS total FROM v_gastosadm WHERE id_apc = {$d->id_apc} AND id_tg = 3 AND estado='a'")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'EgresosD'} = $this->db->query("SELECT IFNULL(SUM(importe),0) AS total FROM v_gastosadm WHERE id_apc = {$d->id_apc} AND id_tg = 4 AND estado='a'")
                ->fetch(PDO::FETCH_OBJ);

                $c->aperturas[$k]->{'Descuentos'} = $this->db->query("SELECT COUNT(id_ven) AS cant FROM v_ventas_con WHERE id_apc = {$d->id_apc} AND desc_monto > '0.00' AND estado <> 'i'")
                ->fetch(PDO::FETCH_OBJ);

                $c->aperturas[$k]->{'ComisionDelivery'} = $this->db->query("SELECT COUNT(id_ven) AS cant FROM v_ventas_con WHERE id_apc = {$d->id_apc} AND id_tped = 3 AND estado <> 'i'")
                ->fetch(PDO::FETCH_OBJ);

                $c->aperturas[$k]->{'Anulaciones'} = $this->db->query("SELECT COUNT(*) AS cant, SUM(pago_efe + pago_tar) total FROM tm_venta WHERE estado = 'i' AND id_apc = {$d->id_apc}")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Deliverys'} = $this->db->query("SELECT SUM(IFNULL((v.pago_efe+v.pago_tar),0)) AS total, COUNT(*) AS cant FROM v_ventas_con AS v INNER JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$d->id_apc} AND v.estado = 'a'")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'Portero'} = $this->db->query("SELECT SUM(IFNULL((v.pago_efe+v.pago_tar),0)) AS total, COUNT(*) AS cant FROM v_ventas_con AS v INNER JOIN tm_pedido_portero AS d ON v.id_ped = d.id_pedido WHERE v.id_apc = {$d->id_apc} AND v.estado = 'a'")
                ->fetch(PDO::FETCH_OBJ);
                $c->aperturas[$k]->{'PollosVendidos'} = $this->db->query("SELECT p.id_pres,p.pro_nom,p.pro_pre,dv.precio,SUM(dv.cantidad) AS cantidad, i.cant FROM tm_detalle_venta AS dv INNER JOIN tm_venta AS v ON dv.id_venta = v.id_venta INNER JOIN v_productos AS p ON dv.id_prod = p.id_pres INNER JOIN tm_producto_ingr AS i ON dv.id_prod = i.id_pres WHERE v.id_apc = {$d->id_apc} AND v.estado = 'a' AND i.id_ins = 1 AND p.pro_mar = 1 GROUP BY dv.id_prod, dv.precio ORDER BY total DESC")
                    ->fetchAll(PDO::FETCH_OBJ);
    
                $c->aperturas[$k]->{'PolloStock'} = $this->db->query("SELECT (ent-sal) AS total FROM v_stock WHERE id_tipo_ins = 1 AND id_ins = 1")
                ->fetch(PDO::FETCH_OBJ);
                       
                $c->aperturas[$k]->{'Detalle'} = $this->db->query("SELECT d.id_prod,SUM(d.cantidad) AS cantidad, v.id_tipo_pedido AS tipo_pedido, d.precio FROM tm_venta AS v INNER JOIN tm_detalle_venta AS d ON v.id_venta = d.id_venta WHERE v.id_apc = {$d->id_apc} AND v.estado = 'a' GROUP BY d.id_prod, d.precio ORDER BY 2 DESC")
                    ->fetchAll(PDO::FETCH_OBJ);
                foreach($c->aperturas[$k]->Detalle as $x => $l)
                {
                    $c->aperturas[$k]->Detalle[$x]->{'Producto'} = $this->db->query("SELECT pro_nom, pro_pre, id_areap, id_catg, pro_cat FROM v_productos WHERE id_pres = " . $l->id_prod)
                        ->fetch(PDO::FETCH_OBJ);
                }
             endforeach;
            endif;
        return $c;
     }
} 