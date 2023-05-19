<?php

class Comprobante_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
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
            // $c->{'Detalle'} = $this->db->query("SELECT id_prod,SUM(cantidad) AS cantidad, precio FROM tm_detalle_venta WHERE id_venta = " . $c->id_ven." GROUP BY id_prod, precio")
            //     ->fetchAll(PDO::FETCH_OBJ);
            // foreach($c->Detalle as $k => $d)
            // {
            //     $c->Detalle[$k]->{'Producto'} = $this->db->query("SELECT pro_nom, pro_pre FROM v_productos WHERE id_pres = " . $d->id_prod)
            //         ->fetch(PDO::FETCH_OBJ);
            // }
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
    
}