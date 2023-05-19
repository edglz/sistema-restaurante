<?php

class Tablero_Model extends Model {

	public function __construct() {
		parent::__construct();
	}

    public function Caja()
    {
        try
        {      
            return $this->db->selectAll("SELECT id_apc,id_caja,id_turno,desc_caja,desc_turno FROM v_caja_aper WHERE estado = 'a'");
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }
	public function tablero_datos()
    {
        try
        {
            /*
            $ifecha = date('Y-m-d H:i:s',strtotime($_POST['ifecha']));
            $ffecha = date('Y-m-d H:i:s',strtotime($_POST['ffecha']));
            */
            $id_apc = $_POST['id_apc'];

            $sql_ventas = $this->db->prepare("SELECT IFNULL(SUM(pago_efe),0) AS pago_efe, IFNULL(SUM(pago_tar),0) AS pago_tar, IFNULL(SUM(desc_monto),0) AS descuento, IFNULL(SUM(comis_tar),0) AS comis_tar, IFNULL(SUM(comis_del),0) AS comis_delivery, IFNULL(SUM(total),0) AS total FROM v_ventas_con WHERE /*(fec_ven >= ? AND fec_ven < ?) AND*/ id_apc = ? AND estado <> 'i'");
            $sql_ventas->execute(array(/*$ifecha,$ffecha,*/$id_apc));
            $ventas= $sql_ventas->fetch(PDO::FETCH_OBJ); 

            $sql_egresos = $this->db->prepare("SELECT IFNULL(SUM(importe),0) AS total FROM v_gastosadm WHERE /*(fecha_re >= ? AND fecha_re < ?) AND*/ id_apc = ? AND estado = 'a'");
            $sql_egresos->execute(array(/*$ifecha,$ffecha,*/$id_apc));
            $egresos = $sql_egresos->fetch(PDO::FETCH_OBJ);

            $sql_ingresos = $this->db->prepare("SELECT IFNULL(SUM(importe),0) AS total FROM tm_ingresos_adm WHERE /*(fecha_reg >= ? AND fecha_reg < ?) AND */id_apc = ? AND estado = 'a'");
            $sql_ingresos->execute(array(/*$ifecha,$ffecha,*/$id_apc));
            $ingresos = $sql_ingresos->fetch(PDO::FETCH_OBJ);

            $sql_pollos_vendidos = $this->db->prepare("SELECT p.id_pres,p.pro_nom,p.pro_pre,dv.precio,SUM(dv.cantidad) AS cantidad, i.cant FROM tm_detalle_venta AS dv INNER JOIN tm_venta AS v ON dv.id_venta = v.id_venta INNER JOIN v_productos AS p ON dv.id_prod = p.id_pres INNER JOIN tm_producto_ingr AS i ON dv.id_prod = i.id_pres WHERE v.id_apc = ? AND i.id_ins = 1 AND p.pro_mar = 1 AND v.estado = 'a' GROUP BY dv.id_prod, dv.precio ORDER BY total DESC");
            $sql_pollos_vendidos->execute(array($id_apc));
            $pollos_vendidos = $sql_pollos_vendidos->fetchAll(PDO::FETCH_OBJ);
            //59 al 66

            $sql_pollos_stock = $this->db->prepare("SELECT (ent-sal) AS total FROM v_stock WHERE id_tipo_ins = 1 AND id_ins = 1");
            $sql_pollos_stock->execute();
            $pollos_stock = $sql_pollos_stock->fetch(PDO::FETCH_OBJ);

            $sql_platos = $this->db->prepare("SELECT vp.pro_nom,vp.pro_pre,dv.precio,SUM(dv.cantidad) AS cantidad,COUNT(dv.id_venta) AS total FROM tm_detalle_venta AS dv INNER JOIN tm_venta AS v ON dv.id_venta = v.id_venta INNER JOIN v_productos AS vp ON dv.id_prod = vp.id_pres WHERE vp.id_tipo = 1 AND v.estado = 'a' AND v.id_apc = ? GROUP BY dv.id_prod, dv.precio ORDER BY total DESC LIMIT 10");
            $sql_platos->execute(array(/*$ifecha,$ffecha,*/$id_apc));
            $platos = $sql_platos->fetchAll(PDO::FETCH_OBJ);

            $sql_productos = $this->db->prepare("SELECT vp.pro_nom,vp.pro_pre,dv.precio,SUM(dv.cantidad) AS cantidad,COUNT(dv.id_venta) AS total FROM tm_detalle_venta AS dv INNER JOIN tm_venta AS v ON dv.id_venta = v.id_venta INNER JOIN v_productos AS vp ON dv.id_prod = vp.id_pres WHERE v.estado = 'a' AND v.id_apc = ? GROUP BY dv.id_prod, dv.precio ORDER BY total DESC LIMIT 10");
            $sql_productos->execute(array(/*$ifecha,$ffecha,*/$id_apc));
            $productos = $sql_productos->fetchAll(PDO::FETCH_OBJ);

            $sql_canal_salon = $this->db->prepare("SELECT COUNT(*) AS cantidad_ventas, SUM(pago_efe + pago_tar) total_ventas FROM tm_venta WHERE id_tipo_pedido = 1 AND estado = 'a' AND id_apc = ?");
            $sql_canal_salon->execute(array(/*$ifecha,$ffecha,*/$id_apc));
            $canal_salon = $sql_canal_salon->fetch(PDO::FETCH_OBJ);

            $sql_canal_mostrador = $this->db->prepare("SELECT COUNT(*) AS cantidad_ventas, SUM(pago_efe + pago_tar) total_ventas FROM tm_venta WHERE id_tipo_pedido = 2 AND estado = 'a' AND id_apc = ?");
            $sql_canal_mostrador->execute(array(/*$ifecha,$ffecha,*/$id_apc));
            $canal_mostrador = $sql_canal_mostrador->fetch(PDO::FETCH_OBJ);

            $sql_canal_delivery = $this->db->prepare("SELECT COUNT(*) AS cantidad_ventas, SUM(pago_efe + pago_tar) total_ventas FROM tm_venta WHERE id_tipo_pedido = 3 AND estado = 'a' AND id_apc = ?");
            $sql_canal_delivery->execute(array(/*$ifecha,$ffecha,*/$id_apc));
            $canal_delivery = $sql_canal_delivery->fetch(PDO::FETCH_OBJ);

            $sql_canal_salon_i = $this->db->prepare("SELECT COUNT(*) AS cantidad_ventas, SUM(pago_efe + pago_tar) total_ventas FROM tm_venta WHERE id_tipo_pedido = 1 AND estado = 'i' AND id_apc = ?");
            $sql_canal_salon_i->execute(array(/*$ifecha,$ffecha,*/$id_apc));
            $canal_salon_i = $sql_canal_salon_i->fetch(PDO::FETCH_OBJ);

            $sql_canal_mostrador_i = $this->db->prepare("SELECT COUNT(*) AS cantidad_ventas, SUM(pago_efe + pago_tar) total_ventas FROM tm_venta WHERE id_tipo_pedido = 2 AND estado = 'i' AND id_apc = ?");
            $sql_canal_mostrador_i->execute(array(/*$ifecha,$ffecha,*/$id_apc));
            $canal_mostrador_i = $sql_canal_mostrador_i->fetch(PDO::FETCH_OBJ);

            $sql_canal_delivery_i = $this->db->prepare("SELECT COUNT(*) AS cantidad_ventas, SUM(pago_efe + pago_tar) total_ventas FROM tm_venta WHERE id_tipo_pedido = 3 AND estado = 'i' AND id_apc = ?");
            $sql_canal_delivery_i->execute(array(/*$ifecha,$ffecha,*/$id_apc));
            $canal_delivery_i = $sql_canal_delivery_i->fetch(PDO::FETCH_OBJ);

            /*
            $mozo = $this->db->prepare("SELECT IFNULL(COUNT(dp.id_pedido),0) AS tped,u.nombres,u.ape_paterno,u.ape_materno FROM tm_detalle_pedido AS dp INNER JOIN tm_pedido_mesa AS pm ON dp.id_pedido = pm.id_pedido INNER JOIN tm_pedido AS p ON dp.id_pedido = p.id_pedido INNER JOIN tm_usuario AS u ON pm.id_mozo = u.id_usu WHERE dp.estado <> 'i' AND p.estado = 'c' AND (p.fecha_pedido >= ? AND p.fecha_pedido < ?) GROUP BY pm.id_mozo ORDER BY tped DESC LIMIT 1");
            $mozo->execute(array($ifecha,$ffecha));
            $m = $mozo->fetch(PDO::FETCH_OBJ);

            $t_ped = $this->db->prepare("SELECT IFNULL(COUNT(dp.id_pedido),0) AS toped FROM tm_detalle_pedido AS dp INNER JOIN tm_pedido AS p ON dp.id_pedido = p.id_pedido WHERE dp.estado <> 'i' AND p.estado = 'c' AND (p.fecha_pedido >= ? AND p.fecha_pedido < ?)");
            $t_ped->execute(array($ifecha,$ffecha));
            $tp = $t_ped->fetch(PDO::FETCH_OBJ);

            $mesas = $this->db->prepare("SELECT IFNULL(COUNT(pm.id_pedido),0) AS total FROM tm_pedido_mesa AS pm INNER JOIN tm_pedido as p ON pm.id_pedido = p.id_pedido WHERE p.estado = 'c' AND (p.fecha_pedido >= ? AND p.fecha_pedido < ?)");
            $mesas->execute(array($ifecha,$ffecha));
            $me = $mesas->fetch(PDO::FETCH_OBJ);

            $v_mesa = $this->db->prepare("SELECT IFNULL(SUM(v.total - v.descu),0) AS total_v FROM v_ventas_con AS v INNER JOIN tm_pedido_mesa AS pm ON v.id_ped = pm.id_pedido WHERE (v.fec_ven >= ? AND v.fec_ven < ?)");
            $v_mesa->execute(array($ifecha,$ffecha));
            $vm = $v_mesa->fetch(PDO::FETCH_OBJ);

            $v_mos = $this->db->prepare("SELECT IFNULL(SUM(v.total - v.descu),0) AS total_v FROM v_ventas_con AS v INNER JOIN tm_pedido_llevar AS pm ON v.id_ped = pm.id_pedido WHERE (v.fec_ven >= ? AND v.fec_ven < ?)");
            $v_mos->execute(array($ifecha,$ffecha));
            $vmo = $v_mos->fetch(PDO::FETCH_OBJ);

            $mostrador = $this->db->prepare("SELECT IFNULL(COUNT(pm.id_pedido),0) AS total FROM tm_pedido_llevar AS pm INNER JOIN tm_pedido as p ON pm.id_pedido = p.id_pedido WHERE p.estado = 'c' AND (p.fecha_pedido >= ? AND p.fecha_pedido < ?)");
            $mostrador->execute(array($ifecha,$ffecha));
            $mo = $mostrador->fetch(PDO::FETCH_OBJ);


            $mesa_a = $this->db->prepare("SELECT COUNT(id_pedido) as total FROM tm_pedido WHERE (fecha_pedido >= ? AND fecha_pedido < ?) AND id_tipo_pedido = 1 AND estado ='i'");
            $mesa_a->execute(array($ifecha,$ffecha));
            $ma = $mesa_a->fetch(PDO::FETCH_OBJ);

            $mos_a = $this->db->prepare("SELECT COUNT(id_pedido) as total FROM tm_pedido WHERE (fecha_pedido >= ? AND fecha_pedido < ?) AND id_tipo_pedido = 2 AND estado ='i'");
            $mos_a->execute(array($ifecha,$ffecha));
            $moa = $mos_a->fetch(PDO::FETCH_OBJ);
            */

            $data = array("Ventas" => $ventas,"Egresos" => $egresos,"Ingresos" => $ingresos,"Platos" => $platos,"Productos" => $productos, "Pollostock" => $pollos_stock, "Pollosvendidos" => $pollos_vendidos, "CanalSalon" => $canal_salon, "CanalMostrador" => $canal_mostrador, "CanalDelivery" => $canal_delivery, "CanalSalonAnulados" => $canal_salon_i, "CanalMostradorAnulados" => $canal_mostrador_i,"CanalDeliveryAnulados" => $canal_delivery_i/*"data3" => $m,"data4" => $tp,"data5" => $me,"data6" => $vm,"data7" => $vmo,"data8" => $mo,*//*,"data11" => $ma,"data12" => $moa*/);
            $json = json_encode($data);
            echo $json;  

        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }
    public function Puertas(){
        $c  = $this->db->query("SELECT * FROM portero_apertura WHERE estado = 'a'")->fetchAll(PDO::FETCH_OBJ);
        return $c;
    }
    public function ventas_portero(){
        $c = $this->db->query("SELECT SUM(v.monto_final) AS monto FROM portero_apertura as v WHERE estado = 'a'")->fetch(PDO::FETCH_OBJ);
        echo $c->monto;
    }
    public function count_ventas(){
        //fin
        $fecha = date("Y-m-d h:i A");
        //inicio
        $fechaa = date("Y-m-d 07:00");

        $fecha_inicio = $fechaa;

        $ifecha = date('Y-m-d H:i:s',strtotime($fechaa));
        $ffecha = date('Y-m-d H:i:s',strtotime($fecha));




        $stm = $this->db->prepare("SELECT * FROM portero_ventas AS v WHERE (fecha >= ? AND fecha <= ?)");
        $stm->execute(array($fecha_inicio, $ffecha));
        echo $stm->rowCount();
    }
}