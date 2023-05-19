<?php

use Illuminate\Database\Capsule\Manager as DB;

 Session::init(); ?>
<?php

class Venta_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Salon()
    {
        try {
            return $this->db->selectAll('SELECT * FROM tm_salon WHERE estado <> "i"');
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function getInfoMesa()
    {
        $id_mesa = Session::get('id_mesa');
        return $this->db->query("SELECT * FROM v_mesas WHERE id_mesa = {$id_mesa}")->fetch(PDO::FETCH_OBJ);
    }
    public function Mozo()
    {
        try {
            return $this->db->selectAll('SELECT id_usu,nombres,ape_paterno,ape_materno FROM v_usuarios WHERE id_rol = 5 AND estado = "a"');
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Repartidor()
    {
        try {
            return $this->db->selectAll('SELECT * FROM tm_usuario WHERE id_rol = 6 AND estado = "a"');
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TipoDocumento()
    {
        try {
            return $this->db->selectAll('SELECT * FROM tm_tipo_doc WHERE estado = "a"');
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TipoPago()
    {
        try {
            return $this->db->selectAll('SELECT * FROM tm_tipo_pago WHERE estado = "a"');
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Personal()
    {
        try {
            return $this->db->selectAll("SELECT * FROM tm_usuario WHERE id_usu <> 1 AND estado = 'a' GROUP BY dni");
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function mesa_list()
    {
        try {
            $mesa = $this->db->prepare("SELECT * FROM v_listar_mesas ORDER BY id_mesa ASC");
            $mesa->execute();
            $m = $mesa->fetchAll(PDO::FETCH_OBJ);
            if(Session::get('rol') == -1){
                foreach ($m as $k => $d) {
                    $m[$k]->{'pedido'} = $d->id_pedido ? DB::table('tm_pedido')->where('id_pedido', $d->id_pedido)->first() : null;
                }
            }
            $data = array("mesa" => $m);
            return $data;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function mostrador_list($data)
    {
        try {
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp");
            $fecha = date('Y-m-d');

            if ($data['estado'] == 'd') {
                $filtro_fecha = " AND DATE_FORMAT(p.fecha_pedido,'%Y-%m-%d') = '" . $fecha . "' ORDER BY p.fecha_pedido DESC";
            } else {
                $filtro_fecha = "";
            }

            $stm = $this->db->prepare("SELECT tp.*,p.fecha_pedido,p.estado,DATE(p.fecha_pedido) AS fecha FROM tm_pedido AS p INNER JOIN tm_pedido_llevar AS tp ON p.id_pedido = tp.id_pedido WHERE p.estado = ? " . $filtro_fecha);
            $stm->execute(array($data['estado']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach ($c as $k => $d) {
                $c[$k]->{'Total'} = $this->db->query("SELECT IFNULL(SUM(precio*cantidad),0) AS total FROM v_det_llevar WHERE estado <> 'z' AND id_pedido = " . $d->id_pedido)
                    ->fetch(PDO::FETCH_OBJ);
            }
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function mostrador_list_c($data)
    {
        try {
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp");
            $fecha = date('Y-m-d');

            if ($data['estado'] == 'd') {
                $filtro_fecha = " AND DATE_FORMAT(p.fecha_pedido,'%Y-%m-%d') = '" . $fecha . "' ORDER BY p.fecha_pedido DESC";
            } else {
                $filtro_fecha = "";
            }

            if (Session::get('rol') == 5) {

                $stm = $this->db->prepare("SELECT tp.*,p.fecha_pedido,p.estado,DATE(p.fecha_pedido) AS fecha, v.id_venta, v.id_tipo_pago, IFNULL((v.total+v.comision_delivery-v.descuento_monto),0) AS total FROM tm_pedido AS p INNER JOIN tm_pedido_llevar AS tp ON p.id_pedido = tp.id_pedido INNER JOIN tm_venta AS v ON p.id_pedido = v.id_pedido WHERE p.estado = ? " . $filtro_fecha);
                $stm->execute(array($data['estado']));
                $c = $stm->fetchAll(PDO::FETCH_OBJ);
            } else {

                $stm = $this->db->prepare("SELECT tp.*,p.fecha_pedido,p.estado,DATE(p.fecha_pedido) AS fecha, v.id_venta, v.id_tipo_pago, IFNULL((v.total+v.comision_delivery-v.descuento_monto),0) AS total FROM tm_pedido AS p INNER JOIN tm_pedido_llevar AS tp ON p.id_pedido = tp.id_pedido INNER JOIN tm_venta AS v ON p.id_pedido = v.id_pedido WHERE v.id_apc = ? AND p.estado = ? " . $filtro_fecha);
                $stm->execute(array(Session::get('apcid'), $data['estado']));
                $c = $stm->fetchAll(PDO::FETCH_OBJ);
            }

            foreach ($c as $k => $d) {
                $c[$k]->{'Tipopago'} = $this->db->query("SELECT descripcion AS nombre FROM tm_tipo_pago WHERE id_tipo_pago = " . $d->id_tipo_pago)
                    ->fetch(PDO::FETCH_OBJ);
            }

            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function delivery_list($data)
    {
        try {
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp");
            $fecha = date('Y-m-d');

            if ($data['estado'] == 'd') {
                $filtro_fecha = " AND DATE_FORMAT(p.fecha_pedido,'%Y-%m-%d') = '" . $fecha . "' ORDER BY p.fecha_pedido DESC";
            } else {
                $filtro_fecha = "";
            }

            $stm = $this->db->prepare("SELECT tp.*,p.fecha_pedido,p.estado,DATE(p.fecha_pedido) AS fecha FROM tm_pedido AS p INNER JOIN tm_pedido_delivery AS tp ON p.id_pedido = tp.id_pedido WHERE p.estado = ? " . $filtro_fecha);
            $stm->execute(array($data['estado']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach ($c as $k => $d) {
                $c[$k]->{'Tipopago'} = $this->db->query("SELECT descripcion AS nombre FROM tm_tipo_pago WHERE id_tipo_pago = " . $d->tipo_pago)
                    ->fetch(PDO::FETCH_OBJ);
            }
            foreach ($c as $k => $d) {
                $c[$k]->{'Total'} = $this->db->query("SELECT IFNULL(SUM(precio*cantidad),0) AS total FROM v_det_delivery WHERE estado <> 'z' AND id_pedido = " . $d->id_pedido)
                    ->fetch(PDO::FETCH_OBJ);
            }
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function delivery_list_c($data)
    {
        try {
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp");
            $fecha = date('Y-m-d');

            if ($data['estado'] == 'd') {
                $filtro_fecha = " AND DATE_FORMAT(p.fecha_pedido,'%Y-%m-%d') = '" . $fecha . "' ORDER BY p.fecha_pedido DESC";
            } else {
                $filtro_fecha = "";
            }

            $stm = $this->db->prepare("SELECT v.id_venta,tp.*,p.fecha_pedido,p.estado,DATE(p.fecha_pedido) AS fecha, v.id_tipo_pago AS tipo_pago_new, IFNULL((v.total+v.comision_delivery-v.descuento_monto),0) AS total FROM tm_pedido AS p INNER JOIN tm_pedido_delivery AS tp ON p.id_pedido = tp.id_pedido INNER JOIN tm_venta AS v ON p.id_pedido = v.id_pedido WHERE v.id_apc = ? AND p.estado = ? " . $filtro_fecha);
            $stm->execute(array(Session::get('apcid'), $data['estado']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach ($c as $k => $d) {
                $c[$k]->{'Tipopago'} = $this->db->query("SELECT descripcion AS nombre FROM tm_tipo_pago WHERE id_tipo_pago = " . $d->tipo_pago_new)
                    ->fetch(PDO::FETCH_OBJ);
            }
            /*
            foreach($c as $k => $d)
            {
                $c[$k]->{'Total'} = $this->db->query("SELECT IFNULL(SUM(precio*cantidad),0) AS total FROM v_det_delivery WHERE estado <> 'z' AND id_pedido = " . $d->id_pedido)
                    ->fetch(PDO::FETCH_OBJ);
            }            
            */
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    /*
    public function delivery_list_b()
    {
        try
        {   
            $stm = $this->db->prepare("SELECT tp.*,p.fecha_pedido,p.estado,DATE(p.fecha_pedido) AS fecha FROM tm_pedido AS p INNER JOIN tm_pedido_delivery AS tp ON p.id_pedido = tp.id_pedido WHERE p.estado = 'x'");
            $stm->execute();
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Total'} = $this->db->query("SELECT IFNULL(SUM(precio*cantidad),0) AS total FROM v_det_delivery WHERE estado <> 'i' AND id_pedido = " . $d->id_pedido)
                    ->fetch(PDO::FETCH_OBJ);
            }
            foreach($c as $k => $d)
            {
                $c[$k]->{'Repartidor'} = $this->db->query("SELECT descripcion AS nombre FROM tm_repartidor WHERE id_repartidor = " . $d->id_repartidor)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function delivery_list_c()
    {
        try
        {   
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
            $fecha = date('Y-m-d');
            $stm = $this->db->prepare("SELECT tp.*,p.fecha_pedido,p.estado,DATE(p.fecha_pedido) AS fecha FROM tm_pedido AS p INNER JOIN tm_pedido_delivery AS tp ON p.id_pedido = tp.id_pedido WHERE p.estado = 'c' AND DATE_FORMAT(p.fecha_pedido,'%Y-%m-%d') = ?");
            $stm->execute(array($fecha));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Total'} = $this->db->query("SELECT IFNULL(SUM(precio*cantidad),0) AS total FROM v_det_delivery WHERE estado <> 'i' AND id_pedido = " . $d->id_pedido)
                    ->fetch(PDO::FETCH_OBJ);
            }
            foreach($c as $k => $d)
            {
                $c[$k]->{'Repartidor'} = $this->db->query("SELECT descripcion AS nombre FROM tm_repartidor WHERE id_repartidor = " . $d->id_repartidor)
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

    public function listarPedidos($data)
    {
        try {
            if ($data['codpagina'] == '1') {
                $stm = $this->db->prepare("SELECT dp.id_pedido, dp.id_pres, SUM(dp.cantidad) AS cantidad, dp.precio, dp.comentario, dp.estado, p.nombre_mozo FROM tm_detalle_pedido AS dp INNER JOIN v_pedido_mesa AS p ON p.id_pedido = dp.id_pedido WHERE dp.id_pedido = ? AND dp.estado <> 'z' AND dp.cantidad > 0 GROUP BY dp.id_pres, dp.precio ORDER BY dp.fecha_pedido DESC");
            } else {
                $stm = $this->db->prepare("SELECT dp.id_pedido, dp.id_pres, SUM(dp.cantidad) AS cantidad, dp.precio, dp.comentario, dp.estado FROM tm_detalle_pedido AS dp WHERE dp.id_pedido = ? AND dp.estado <> 'z' AND dp.cantidad > 0 GROUP BY dp.id_pres, dp.precio ORDER BY dp.fecha_pedido DESC");
            }
            $stm->execute(array($data['id_pedido']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach ($c as $k => $d) {
                $c[$k]->{'Producto'} = $this->db->query("SELECT pro_nom,pro_pre FROM v_productos WHERE id_pres = " . $d->id_pres)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function listarPedidosDetalle($data)
    {
        try {
            if ($data['cod_atencion'] == 2) {
                $tabla = 'v_pedido_llevar';
            } elseif ($data['cod_atencion'] == 3) {
                $tabla = 'v_pedido_delivery';
            }
            $stm = $this->db->prepare("SELECT * FROM " . $tabla . " WHERE id_pedido = ?");
            $stm->execute(array($data['id_pedido']));
            $c = $stm->fetch(PDO::FETCH_OBJ);
            /* Traemos el detalle */
            $c->{'Detalle'} = $this->db->query("SELECT id_pedido,id_pres,SUM(cant) AS cant, precio, comentario, estado FROM tm_detalle_pedido WHERE id_pedido = " . $c->id_pedido . " AND estado <> 'z' AND cant > 0 GROUP BY id_pres, precio ORDER BY fecha_pedido DESC")
                ->fetchAll(PDO::FETCH_OBJ);
            foreach ($c->Detalle as $k => $d) {
                $c->Detalle[$k]->{'Producto'} = $this->db->query("SELECT pro_nom,pro_pre FROM v_productos WHERE id_pres = " . $d->id_pres)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function listarUpdatePedidos($data)
    {
        try {
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp");
            $fecha = date("Y-m-d H:i:s");

            $stm = $this->db->prepare("SELECT p.pro_nom AS producto, p.pro_pre AS presentacion, SUM(d.cant) AS cantidad, d.precio, d.comentario, a.id_areap, i.nombre AS nombre_imp FROM tm_detalle_pedido AS d INNER JOIN v_productos AS p ON d.id_pres = p.id_pres INNER JOIN tm_area_prod AS a ON a.id_areap = p.id_areap INNER JOIN tm_impresora AS i ON i.id_imp = a.id_imp WHERE d.id_pedido = ? AND d.estado = 'y' AND d.cant > 0 GROUP BY d.id_pres ORDER BY d.fecha_pedido DESC");
            $stm->execute(array($data['id_pedido']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);

            $sql2 = "UPDATE tm_detalle_pedido SET estado = 'a', fecha_pedido = ? WHERE id_pedido = ? AND estado = 'y'";
            $this->db->prepare($sql2)->execute(array($fecha, $data['id_pedido']));

            if ($data['estado_pedido'] == 'a') {

                $sql3 = "UPDATE tm_pedido SET id_apc = ?, id_usu = ?, estado = 'b' WHERE id_pedido = ?";
                $this->db->prepare($sql3)->execute(array(Session::get('apcid'), Session::get('usuid'), $data['id_pedido']));

                $sql4 = "UPDATE tm_pedido_delivery SET fecha_preparacion = ? WHERE id_pedido = ?";
                $this->db->prepare($sql4)->execute(array($fecha, $data['id_pedido']));
                /*
                UPDATE tm_pedido SET id_apc = _id_apc, id_usu = _id_usu, estado = 'b' WHERE id_pedido = _id_pedido;
                UPDATE tm_pedido_delivery SET fecha_preparacion = _fecha_venta WHERE id_pedido = _id_pedido;
                */
            }

            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function listarPedidosTicket($data)
    {
        try {
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp");
            $fecha = date("Y-m-d H:i:s");

            $stm = $this->db->prepare("SELECT p.pro_nom AS producto, p.pro_pre AS presentacion, SUM(d.cant) AS cantidad, d.precio, d.comentario, 1 AS id_areap, 'CAJA' AS nombre_imp FROM tm_detalle_pedido AS d INNER JOIN v_productos AS p ON d.id_pres = p.id_pres WHERE d.id_pedido = ? AND d.estado = 'a' AND d.cant > 0 GROUP BY d.id_pres ORDER BY d.fecha_pedido DESC");
            $stm->execute(array($data['id_pedido']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);

            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function pedidoAccion($data)
    {
        try {
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp");
            $fecha = date("Y-m-d H:i:s");

            if ($data['cod_accion'] == 1) {
                $estado = 'c';
                $tabla = 'tm_pedido_delivery';
                $fecha_campo = 'fecha_envio';
            } else if ($data['cod_accion'] == 2) {
                $estado = 'd';
                $tabla = 'tm_pedido_delivery';
                $fecha_campo = 'fecha_entrega';
            } else if ($data['cod_accion'] == 3) {
                $estado = 'd';
                $tabla = 'tm_pedido_llevar';
                $fecha_campo = 'fecha_entrega';
            }

            $sql = "UPDATE tm_pedido SET estado = '" . $estado . "' WHERE id_pedido = ?";
            $this->db->prepare($sql)->execute(array($data['id_pedido']));
            $sql2 = "UPDATE " . $tabla . " SET " . $fecha_campo . " = ? WHERE id_pedido = ?";
            $this->db->prepare($sql2)->execute(array($fecha, $data['id_pedido']));
            $sql3 = "UPDATE tm_venta SET codigo_operacion = ? WHERE id_pedido = ?";
            $this->db->prepare($sql3)->execute(array($data['codigo_operacion'], $data['id_pedido']));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ComboMesaOri($data)
    {
        try {
            $stmm = $this->db->prepare("SELECT * FROM tm_mesa WHERE id_salon = ? AND estado = 'i' ORDER BY nro_mesa ASC");
            $stmm->execute(array($data['cod_salon_origen']));
            $var = $stmm->fetchAll(PDO::FETCH_ASSOC);
            foreach ($var as $v) {
                echo '<option value="' . $v['id_mesa'] . '">' . $v['nro_mesa'] . '</option>';
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ComboMesaDes($data)
    {
        try {
            $stmm = $this->db->prepare("SELECT * FROM tm_mesa WHERE id_salon = ? AND estado = ? ORDER BY nro_mesa ASC");
            $stmm->execute(array($data['cod_salon_destino'], $data['estado']));
            $var = $stmm->fetchAll(PDO::FETCH_ASSOC);
            foreach ($var as $v) {
                echo '<option value="' . $v['id_mesa'] . '">' . $v['nro_mesa'] . '</option>';
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function CambiarMesa($data)
    {
        try {
            $consulta = "call usp_restOpcionesMesa( :flag, :cod_mesa_origen, :cod_mesa_destino);";
            $arrayParam =  array(
                ':flag' => 1,
                ':cod_mesa_origen' =>  $data['cod_mesa_origen_opc01'],
                ':cod_mesa_destino' => $data['cod_mesa_destino_opc01']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function MoverPedidos($data)
    {
        try {
            $consulta = "call usp_restOpcionesMesa( :flag, :cod_mesa_origen, :cod_mesa_destino);";
            $arrayParam =  array(
                ':flag' => 2,
                ':cod_mesa_origen' =>  $data['cod_mesa_origen_opc02'],
                ':cod_mesa_destino' => $data['cod_mesa_destino_opc02']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function subPedido($data)
    {
        try {
            if ($data['tipo_pedido'] == 1) {
                $tabla = 'v_pedido_mesa';
            } elseif ($data['tipo_pedido'] == 2) {
                $tabla = 'v_pedido_llevar';
            } elseif ($data['tipo_pedido'] == 3) {
                $tabla = 'v_pedido_delivery';
            } elseif ($data['tipo_pedido'] == 4) {
                $tabla = 'v_pedido_portero';
            }
            $stm = $this->db->prepare("SELECT id_pedido, id_tipo_pedido, estado_pedido FROM " . $tabla . " WHERE id_pedido = ?");
            $stm->execute(array($data['id_pedido']));
            $c = $stm->fetch(PDO::FETCH_OBJ);
            /* Traemos el detalle */
            $c->{'Detalle'} = $this->db->query("SELECT id_pres, cantidad, cant, precio, estado, fecha_pedido FROM tm_detalle_pedido WHERE id_pedido = " . $c->id_pedido . " AND id_pres = " . $data['id_pres'] . " AND precio = " . $data['precio'] . " ORDER BY fecha_pedido DESC")
                ->fetchAll(PDO::FETCH_OBJ);
            foreach ($c->Detalle as $k => $d) {
                $c->Detalle[$k]->{'Producto'} = $this->db->query("SELECT pro_nom,pro_pre FROM v_productos WHERE id_pres = " . $d->id_pres)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function refrescar_mesas()
    {
        try {
            $stm = $this->db->prepare("UPDATE tm_mesa AS m INNER JOIN v_listar_mesas AS v ON m.id_mesa = v.id_mesa SET m.estado = 'a' WHERE v.estado <> 'a' AND v.estado <> 'm' AND v.id_pedido IS NULL");
            $stm->execute();
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            return $c;
        } catch (Exception $e) {
            return false;
        }
    }

    public function ValidarEstadoPedido($id_pedido)
    {
        try {
            $consulta = "SELECT count(*) AS cod, id_tipo_pedido AS tipo_pedido FROM tm_pedido WHERE id_pedido = :id_pedido AND (estado = 'a' OR estado = 'b' OR estado ='c')";
            $result = $this->db->prepare($consulta);
            $result->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
            $result->execute();
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row;
        } catch (Exception $e) {
            return false;
        }
    }

    public function pc1($data)
    {
        try {
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp");
            $fecha = date("Y-m-d H:i:s");
            $id_usu = Session::get('usuid');
            if (Session::get('rol') == 5) {
                $id_mozo = $id_usu;
            } else {
                $id_mozo = $data['id_mozo'];
            };
            $consulta = "call usp_restRegMesa( :flag, :id_tipo_pedido, :id_apc, :id_usu, :fecha_pedido, :id_mesa, :id_mozo, :nomb_cliente, :nro_personas, :cliente);";
            $arrayParam =  array(
                ':flag' => 1,
                ':id_tipo_pedido' => 1,
                ':id_apc' => Session::get('apcid'),
                ':id_usu' => Session::get('rol') == -1 ? $id_mozo : $id_usu,
                ':fecha_pedido' => $fecha,
                ':id_mesa' => $data['id_mesa'],
                ':id_mozo' => $id_mozo,
                ':nomb_cliente' => $data['nomb_cliente'],
                ':nro_personas' => $data['nro_personas'],
                ':cliente' => Session::get('rol') == -1 ? Session::get('usuid') : 0
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function pc4($data)
    {
        try {
            try {
                date_default_timezone_set($_SESSION["zona_horaria"]);
                setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp");
                $fecha = date("Y-m-d H:i:s");
                $id_usu = Session::get('usuid');
                if (Session::get('rol') == 7) {
                    $id_mozo = 54;
                } else {
                    $id_mozo = $data['id_mozo'];
                };
                $consulta = "call usp_restRegMesa( :flag, :id_tipo_pedido, :id_apc, :id_usu, :fecha_pedido, :id_mesa, :cliente, :id_mozo, :nro_personas);";
                $arrayParam =  array(
                    ':flag' => 1,
                    ':id_tipo_pedido' => 1,
                    ':id_apc' => null,
                    ':id_usu' => $id_usu,
                    ':fecha_pedido' => $fecha,
                    ':id_mesa' => Session::get('id_mesa'),
                    ':id_mozo' => $id_mozo,
                    ':nomb_cliente' => "MESA " . Session::get('id_mesa'),
                    ':nro_personas' => $data['personas']
                );
                $st = $this->db->prepare($consulta);
                $st->execute($arrayParam);
                $row = $st->fetch(PDO::FETCH_ASSOC);
                return $row;
            } catch (Exception $e) {
                die($e->getMessage());
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function pc2($data)
    {
        try {
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp");
            $fecha = date("Y-m-d H:i:s");
            $id_usu = Session::get('usuid');
            $consulta = "call usp_restRegMostrador( :flag, :id_tipo_pedido, :id_apc, :id_usu, :fecha_pedido, :nomb_cliente);";
            $arrayParam =  array(
                ':flag' => 1,
                ':id_tipo_pedido' => 2,
                ':id_apc' => Session::get('apcid'),
                ':id_usu' =>  $id_usu,
                ':fecha_pedido' => $fecha,
                ':nomb_cliente' => $data['nomb_cliente']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function pc3($data)
    {
        try {
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp");
            $fecha = date("Y-m-d H:i:s");
            $id_usu = Session::get('usuid');
            if ($data['tipo_entrega'] == 1) {
                $id_repartidor = $data['id_repartidor'];
                $direccion_cliente = $data['direccion_cliente'];
                $referencia_cliente = $data['referencia_cliente'];
            } else {
                $id_repartidor = 1;
                $direccion_cliente = '';
                $referencia_cliente = '';
            };
            $consulta = "call usp_restRegDelivery( :flag, :tipo_canal, :id_tipo_pedido, :id_apc, :id_usu, :fecha_pedido, :id_cliente, :id_repartidor, :tipo_entrega, :tipo_pago, :pedido_programado, :hora_entrega, :nombre_cliente, :telefono_cliente, :direccion_cliente, :referencia_cliente, :email_cliente);";
            $arrayParam =  array(
                ':flag' => 1,
                ':tipo_canal' => 1,
                ':id_tipo_pedido' => 3,
                ':id_apc' => Session::get('apcid'),
                ':id_usu' =>  $id_usu,
                ':fecha_pedido' => $fecha,
                ':id_cliente' => $data['cliente_id'],
                ':id_repartidor' => $id_repartidor,
                ':tipo_entrega' => $data['tipo_entrega'],
                ':tipo_pago' => 1,
                ':pedido_programado' => $data['pedido_programado'],
                ':hora_entrega' => $data['hora_entrega'],
                ':nombre_cliente' => $data['nomb_cliente'],
                ':telefono_cliente' => $data['telefono_cliente'],
                ':direccion_cliente' => $direccion_cliente,
                ':referencia_cliente' => $referencia_cliente,
                ':email_cliente' => 'example@email.com'
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function defaultdata($data)
    {
        try {
            if ($data['tipo_pedido'] == 1) {
                $tabla = 'v_pedido_mesa';
            } elseif ($data['tipo_pedido'] == 2) {
                $tabla = 'v_pedido_llevar';
            } elseif ($data['tipo_pedido'] == 3) {
                $tabla = 'v_pedido_delivery';
            } elseif ($data['tipo_pedido'] == 4) {
                $tabla = 'v_pedido_portero';
            }
            $id_pedido = $data['id_pedido'];
            $id_pedido = intval($id_pedido);
            $stm = $this->db->prepare("SELECT * FROM " . $tabla . " WHERE id_pedido = ?");
            $stm->execute(array($id_pedido));
            $c = $stm->fetch(PDO::FETCH_OBJ);
            /* Traemos el detalle */
            $c->{'Detalle'} = $this->db->query("SELECT SUM(cantidad) AS cantidad, precio, comentario, estado FROM tm_detalle_pedido WHERE id_pedido = " . $c->id_pedido . " AND estado <> 'z' GROUP BY id_pres,precio ORDER BY fecha_pedido DESC")
                ->fetchAll(PDO::FETCH_OBJ);
            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function listarCategorias($data)
    {
        try {
            if ($data['codtipoped'] == 3) {
                $variable = ' ORDER BY orden ASC';
                //$variable = 'AND delivery = 1';
            } else {
                $variable = ' ORDER BY orden ASC';
            }
            $stm = $this->db->prepare("SELECT * FROM tm_producto_catg WHERE estado = 'a' " . $variable);
            $stm->execute();
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function listarProductos($data)
    {
        try {
            if ($data['codtipoped'] == 3) {
                if ($data['codrepartidor'] == 1) {
                    $campo = ',pro_cos';
                    $variable = '';
                } else {
                    $campo = ',pro_cos_del AS pro_cos';
                    $variable = ' AND pro_cos_del > 0';
                }
            } else {
                $campo = ',pro_cos';
                $variable = '';
            }
            $stm = $this->db->prepare("SELECT id_areap, is_combo,id_pres,pro_nom,pro_pre" . $campo . ",pro_img FROM v_productos WHERE id_catg = ? AND est_a = 'a'  AND est_b = 'a' AND est_c = 'a' " . $variable);
            $stm->execute(array($data['id_catg']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach ($c as $k => $d) {
                $c[$k]->{'Impresora'} = $this->db->query("SELECT i.nombre FROM tm_area_prod AS ap INNER JOIN tm_impresora AS i ON ap.id_imp = i.id_imp WHERE ap.id_areap = " . $d->id_areap)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function listarProdsMasVend($data)
    {
        try {
            if ($data['codtipoped'] == 3) {
                if ($data['codrepartidor'] == 1) {
                    $campo = ',p.pro_cos';
                    $variable = 'GROUP BY dv.id_prod ORDER BY SUM(cantidad) DESC';
                } else {
                    $campo = ',p.pro_cos_del AS pro_cos';
                    $variable = ' AND p.pro_cos_del > 0 GROUP BY dv.id_prod ORDER BY SUM(cantidad) DESC';
                }
            } else {
                $campo = ',p.pro_cos';
                $variable = 'GROUP BY dv.id_prod ORDER BY SUM(cantidad) DESC';
            }
            $stm = $this->db->prepare("SELECT p.id_areap, p.is_combo, p.id_pres,p.pro_nom,p.pro_pre" . $campo . ",p.pro_img FROM tm_detalle_venta AS dv INNER JOIN v_productos AS p ON dv.id_prod = p.id_pres WHERE p.est_a = 'a' AND p.est_b = 'a' AND p.est_c = 'a' " . $variable);
            $stm->execute();
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach ($c as $k => $d) {
                $c[$k]->{'Impresora'} = $this->db->query("SELECT i.nombre FROM tm_area_prod AS ap INNER JOIN tm_impresora AS i ON ap.id_imp = i.id_imp WHERE ap.id_areap = " . $d->id_areap)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function RegistrarPedido($data)
    {
        try {
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp");
            $fecha = date("Y-m-d H:i:s");
            if ($data['codtipoped'] == 3) {
                $estado = 'y';
            } else {
                $estado = 'a';
            }
            foreach ($data['items'] as $d) {
                DB::table('tm_detalle_pedido')->insert([
                    'id_pedido' => $data['cod_ped'],
                    'id_usu' => Session::get('rol') == -1 ? 1 : Session::get('usuid'),
                    'id_pres' => $d['producto_id'],
                    'cantidad' => $d['cantidad'],
                    'cant' => $d['cantidad'],
                    'precio' => $d['precio'],
                    'comentario' => $d['comentario'],
                    'fecha_pedido' => $fecha,
                    'estado' => $estado,
                ]);
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function buscar_producto($data)
    {
        try {
            if ($data['codtipoped'] == 3) {
                if ($data['codrepartidor'] == 1) {
                    $campo = ',pro_cos';
                } else {
                    $campo = ',pro_cos_del AS pro_cos';
                }
                $variable = 'AND del_a = 1 AND del_b = 1 AND del_c = 1';
            } else {
                $variable = '';
                $campo = ',pro_cos';
            }
            $cadena = $data['cadena'];
            $stm = $this->db->prepare("SELECT id_areap,is_combo,id_pres,pro_nom,pro_pre" . $campo . ",pro_img FROM v_productos WHERE (pro_cod LIKE '%$cadena%' OR pro_nom LIKE '%$cadena%' OR pro_cat LIKE '%$cadena%') AND est_a = 'a' AND est_b = 'a' AND est_c = 'a' " . $variable);
            $stm->execute();
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach ($c as $k => $d) {
                $c[$k]->{'Impresora'} = $this->db->query("SELECT i.nombre FROM tm_area_prod AS ap INNER JOIN tm_impresora AS i ON ap.id_imp = i.id_imp WHERE ap.id_areap = " . $d->id_areap)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ListarDetallePed($data)
    {
        try {
            if ($data['tipo_pedido'] == 1) {
                $tabla = 'v_pedido_mesa';
            } elseif ($data['tipo_pedido'] == 2) {
                $tabla = 'v_pedido_llevar';
            } elseif ($data['tipo_pedido'] == 3) {
                $tabla = 'v_pedido_delivery';
            } elseif ($data['tipo_pedido'] == 4) {
                $tabla = 'v_pedido_portero';
            }
            $stm = $this->db->prepare("SELECT id_pedido FROM " . $tabla . " WHERE id_pedido = ?");
            $stm->execute(array($data['id_pedido']));
            $c = $stm->fetch(PDO::FETCH_OBJ);
            /* Traemos el detalle */
            $c->{'Detalle'} = $this->db->query("SELECT id_pres,SUM(cantidad) AS cantidad, precio, estado FROM tm_detalle_pedido WHERE id_pedido = " . $c->id_pedido . " AND estado <> 'z' GROUP BY id_pres, precio")
                ->fetchAll(PDO::FETCH_OBJ);
            foreach ($c->Detalle as $k => $d) {
                $c->Detalle[$k]->{'Producto'} = $this->db->query("SELECT pro_nom, pro_pre FROM v_productos WHERE id_pres = " . $d->id_pres)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function cliente_crud_create($data)
    {
        try {
            $consulta = "call usp_restRegCliente( :flag, @a, :tipo_cliente, :dni, :ruc, :nombres, :razon_social, :telefono, :fecha_nac, :correo, :direccion, :referencia);";
            $arrayParam =  array(
                ':flag' => 1,
                ':tipo_cliente' => $data['tipo_cliente'],
                ':dni' => $data['dni'],
                ':ruc' => $data['ruc'],
                ':nombres' => $data['nombres'],
                ':razon_social' => $data['razon_social'],
                ':telefono' => $data['telefono'],
                ':fecha_nac' => date('Y-m-d', strtotime($data['fecha_nac'])),
                ':correo' => $data['correo'],
                ':direccion' => $data['direccion'],
                ':referencia' => $data['referencia']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            $c = $st->fetch(PDO::FETCH_OBJ);
            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function cliente_crud_update($data)
    {
        try {
            $consulta = "call usp_restRegCliente( :flag, :id_cliente, :tipo_cliente, :dni, :ruc, :nombres, :razon_social, :telefono, :fecha_nac, :correo, :direccion, :referencia);";
            $arrayParam =  array(
                ':flag' => 2,
                ':id_cliente' => $data['id_cliente'],
                ':tipo_cliente' => $data['tipo_cliente'],
                ':dni' => $data['dni'],
                ':ruc' => $data['ruc'],
                ':nombres' => $data['nombres'],
                ':razon_social' => $data['razon_social'],
                ':telefono' => $data['telefono'],
                ':fecha_nac' => date('Y-m-d', strtotime($data['fecha_nac'])),
                ':correo' => $data['correo'],
                ':direccion' => $data['direccion'],
                ':referencia' => $data['referencia']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            $c = $st->fetch(PDO::FETCH_OBJ);
            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function buscar_cliente($data)
    {
        try {
            /*nota: para kerlyn quitar el tipo_cliente de la consulta*/
            if ($data['tipo_cliente'] == 1 or $data['tipo_cliente'] == 2) {
                $stm = $this->db->prepare("SELECT * FROM v_clientes WHERE tipo_cliente = " . $data['tipo_cliente'] . " AND estado <> 'i' AND (dni LIKE '%" . $data['cadena'] . "%' OR ruc LIKE '%" . $data['cadena'] . "%' OR nombre LIKE '%" . $data['cadena'] . "%') ORDER BY dni LIMIT 5");
            } else {
                $stm = $this->db->prepare("SELECT * FROM v_clientes WHERE estado <> 'i' AND (dni LIKE '%" . $data['cadena'] . "%' OR ruc LIKE '%" . $data['cadena'] . "%' OR nombre LIKE '%" . $data['cadena'] . "%') ORDER BY dni LIMIT 5");
            }
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function buscar_cliente_telefono($data)
    {
        try {
            /*nota: para kerlyn quitar el tipo_cliente de la consulta*/
            $stm = $this->db->prepare("SELECT * FROM v_clientes WHERE estado <> 'i' AND (dni LIKE '%" . $data['cadena'] . "%' OR ruc LIKE '%" . $data['cadena'] . "%' OR nombre LIKE '%" . $data['cadena'] . "%' OR telefono LIKE '%" . $data['cadena'] . "%') ORDER BY dni LIMIT 5");
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /*
    public function buscar_cliente_telefono($data)
    {
        try
        {   
            

            $stm = $this->db->prepare("SELECT p.id_pedido, pd.telefono_cliente, pd.nombre_cliente, pd.direccion_cliente, pd.referencia_cliente FROM tm_pedido_delivery AS pd INNER JOIN tm_pedido AS p ON pd.id_pedido = p.id_pedido WHERE pd.telefono_cliente LIKE '%".$data['cadena']."%' AND pd.id_pedido = (SELECT MAX(id_pedido) FROM tm_pedido_delivery WHERE telefono_cliente LIKE '%".$data['cadena']."%') GROUP BY pd.telefono_cliente LIMIT 5");
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ); 
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }
    */

    public function tags_list($data)
    {
        try {
            $stm = $this->db->prepare("SELECT p.id_prod,p.notas FROM tm_producto AS p INNER JOIN tm_producto_pres AS pp ON p.id_prod = pp.id_prod WHERE pp.id_pres = ?");
            $stm->execute(array($data['id_pres']));
            return $stm->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function tags_crud($data)
    {
        try {
            $sql = "UPDATE tm_producto SET notas = ? WHERE id_prod = ?";
            $this->db->prepare($sql)->execute(array($data['notas'], $data['id_prod']));
        } catch (Exception $e) {
            return false;
        }
    }
    public function pedidos_portero()
    {
        $id_mesa = Session::get('id_mesa');
        $stm = $this->db->query("SELECT e.id_pedido, e.fecha_pedido, p.nro_personas as personas FROM tm_pedido AS e INNER JOIN tm_pedido_mesa AS p ON e.id_pedido = p.id_pedido WHERE e.estado = 'a' AND e.id_tipo_pedido = 1 AND p.id_mozo =54 AND p.id_mesa = {$id_mesa}")->fetchAll(PDO::FETCH_OBJ);
        $c = array("data" => $stm);
        echo json_encode($c);
    }
    public function RegistrarVenta($data)
    {
        try {
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp");
            $fecha = date("Y-m-d H:i:s");

            $consulta = "call usp_restEmitirVenta(:flag, :dividir_cuenta, :id_pedido, :tipo_pedido, :tipo_entrega, :id_cliente, :id_tipo_doc, :id_tipo_pago, :id_usu, :id_apc, :pago_efe_none, :pago_tar, :descuento_tipo, :descuento_personal, :descuento_monto, :descuento_motivo, :comision_tarjeta, :comision_delivery, :igv, :total, :codigo_operacion, :fecha_venta);";
            $arrayParam = array(
                ':flag' => 1,
                ':dividir_cuenta' =>  $data['dividir_cuenta'],
                ':id_pedido' =>  $data['id_pedido'],
                ':tipo_pedido' =>  $data['tipo_pedido'],
                ':tipo_entrega' =>  $data['tipo_entrega'],
                ':id_cliente' =>  $data['cliente_id'],
                ':id_tipo_doc' =>  $data['tipo_doc'],
                ':id_tipo_pago' =>  $data['tipo_pago'],
                ':id_usu' =>  Session::get('usuid'),
                ':id_apc' =>  Session::get('apcid'),
                ':pago_efe_none' => $data['pago_efe'],
                ':pago_tar' => $data['pago_tar'],
                ':descuento_tipo' => $data['descuento_tipo'],
                ':descuento_personal' => $data['descuento_personal'],
                ':descuento_monto' => $data['descuento_monto'],
                ':descuento_motivo' => $data['descuento_motivo'],
                ':comision_tarjeta' => $data['comision_tarjeta'],
                ':comision_delivery' => $data['comision_delivery'],
                ':igv' => Session::get('igv'),
                ':total' =>  $data['total'],
                ':codigo_operacion' =>  $data['codigo_operacion'],
                ':fecha_venta' =>  $fecha
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);

            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                $id_venta = $row['id_venta'];
            }

            $this->db = new Database(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_CHARSET);
            $a = $data['idProd'];
            $b = $data['cantProd'];
            $c = $data['precProd'];

            for ($x = 0; $x < sizeof($a); ++$x) {
                if ($b[$x] > 0) {
                    $sql = "INSERT INTO tm_detalle_venta (id_venta,id_prod,cantidad,precio) VALUES (?,?,?,?);";
                    $this->db->prepare($sql)->execute(array($id_venta, $a[$x], $b[$x], $c[$x]));
                }
            }

            $this->db = new Database(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_CHARSET);
            $cons = "call usp_restEmitirVentaDet( :flag, :id_venta, :id_pedido, :fecha );";
            $arrayParam = array(
                ':flag' => 1,
                ':id_venta' =>  $id_venta,
                ':id_pedido' =>  $data['id_pedido'],
                ':fecha' =>  $fecha
            );
            $stm = $this->db->prepare($cons);
            $stm->execute($arrayParam);
            return $id_venta;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function anular_pedido($data)
    {
        try {
            if ($data['tipo_pedido'] == 1) {

                $consulta = "call usp_restDesocuparMesa( :flag, :id_pedido);";
                $arrayParam =  array(
                    ':flag' => 1,
                    ':id_pedido' =>  $data['id_pedido']
                );
                $st = $this->db->prepare($consulta);
                $st->execute($arrayParam);
            } elseif ($data['tipo_pedido'] == 2 or $data['tipo_pedido'] == 3 or $data['tipo_pedido'] == 4) {

                $sql = "UPDATE tm_pedido SET estado = 'z' WHERE id_pedido = ?";
                $this->db->prepare($sql)->execute(array($data['id_pedido']));
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function anular_venta($data)
    {
        try {
            $sql1 = "UPDATE tm_inventario SET estado = 'i' WHERE id_tipo_ope = 2 AND id_ope = ?";
            $this->db->prepare($sql1)->execute(array($data['id_venta']));
            $sql2 = "UPDATE tm_venta SET estado = 'i' WHERE id_venta = ?";
            $this->db->prepare($sql2)->execute(array($data['id_venta']));
            $sql3 = "UPDATE tm_pedido SET estado = 'z' WHERE id_pedido = ?";
            $this->db->prepare($sql3)->execute(array($data['id_pedido']));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function pedido_edit($data)
    {
        try {
            $stm = $this->db->prepare("SELECT * FROM tm_pedido_delivery WHERE id_pedido = ?");
            $stm->execute(array($data['id_pedido']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function pedido_delete($data)
    {
        try {
            //Seleccionar producto
            $stm = $this->db->prepare("SELECT p.pro_nom AS producto, p.pro_pre AS presentacion, d.cant AS cantidad, d.precio, d.comentario, i.nombre AS nombre_imp FROM tm_detalle_pedido AS d INNER JOIN v_productos AS p ON d.id_pres = p.id_pres INNER JOIN tm_area_prod AS a ON a.id_areap = p.id_areap INNER JOIN tm_impresora AS i ON i.id_imp = a.id_imp WHERE d.id_pedido = ? AND d.id_pres = ? AND d.estado <> 'z' AND d.cant > 0 GROUP BY d.id_pres ORDER BY d.fecha_pedido DESC");
            $stm->execute(array($data['id_pedido'], $data['id_pres']));
            $data_producto = $stm->fetchAll(PDO::FETCH_OBJ);

            //Cancelar pedido
            date_default_timezone_set($_SESSION["zona_horaria"]);
            setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp");
            $filtro_seguridad = '9' . date('dm') . '20';
            $fecha_envio = date("Y-m-d H:i:s");
            $consulta = "call usp_restCancelarPedido( :flag, :id_usu, :id_pres, :id_pedido, :estado_pedido, :fecha_pedido, :fecha_envio, :codigo_seguridad, :filtro_seguridad);";
            $arrayParam =  array(
                ':flag' => 1,
                ':id_usu' => Session::get('usuid'),
                ':id_pres' => $data['id_pres'],
                ':id_pedido' =>  $data['id_pedido'],
                ':estado_pedido' =>  $data['estado_pedido'],
                ':fecha_pedido' => $data['fecha_pedido'],
                ':fecha_envio' => $fecha_envio,
                ':codigo_seguridad' => $data['codigo_seguridad'],
                ':filtro_seguridad' => $filtro_seguridad
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            $codigo_respuesta = $st->fetchAll(PDO::FETCH_OBJ);

            $datos = array("Producto" => $data_producto, "Codigo" => $codigo_respuesta);
            $c = json_encode($datos);
            echo $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function pedido_crud_update($data)
    {
        try {
            $sql = "UPDATE tm_pedido_delivery SET id_repartidor = ?, hora_entrega = ?, amortizacion = ?, tipo_pago = ?, paga_con = ?, comision_delivery = ? WHERE id_pedido = ?";
            $this->db->prepare($sql)->execute(array(
                $data['id_repartidor'], $data['hora_entrega'], $data['amortizacion'],
                $data['tipo_pago'], $data['paga_con'], $data['comision_delivery'], $data['id_pedido']
            ));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function venta_edit($data)
    {
        try {
            $stm = $this->db->prepare("SELECT id_tipo_pago,id_pedido,id_venta FROM tm_venta WHERE id_venta = ?");
            $stm->execute(array($data['id_venta']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function venta_edit_pago($data)
    {
        try {
            if ($data['tipo_pago'] <> $data['id_tipo_pago']) {
                if ($data['tipo_pago'] == 1) {
                    $sql = "UPDATE tm_venta SET id_tipo_pago = ?, pago_tar = pago_efe, pago_efe = '0.00' WHERE id_venta = ?";
                    $this->db->prepare($sql)->execute(array($data['id_tipo_pago'], $data['id_venta']));
                } else {

                    if ($data['id_tipo_pago'] == 1) {
                        $sql = "UPDATE tm_venta SET id_tipo_pago = ?, pago_efe = pago_tar, pago_tar = '0.00' WHERE id_venta = ?";
                        $this->db->prepare($sql)->execute(array($data['id_tipo_pago'], $data['id_venta']));
                    } else {
                        $sql = "UPDATE tm_venta SET id_tipo_pago = ? WHERE id_venta = ?";
                        $this->db->prepare($sql)->execute(array($data['id_tipo_pago'], $data['id_venta']));
                    }
                }
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function venta_edit_documento($data)
    {
        try {
            $consulta = "call usp_restEditarVentaDocumento( :flag, :id_venta, :id_cliente, :id_tipo_documento);";
            $arrayParam =  array(
                ':flag' => 1,
                ':id_venta' =>  $data['id_venta'],
                ':id_cliente' => $data['id_cliente'],
                ':id_tipo_documento' => $data['id_tipo_documento']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /* INICIO COMPROBANTE SIN ENVIAR SUNAT */

    public function contadorSunatSinEnviar()
    {
        try {
            $stm = $this->db->prepare("SELECT COUNT(v.id_ven) AS total FROM v_ventas_con AS v INNER JOIN v_caja_aper AS c ON v.id_apc = c.id_apc INNER JOIN tm_tipo_doc AS d ON v.id_tdoc = d.id_tipo_doc WHERE v.ser_doc = d.serie AND v.id_tdoc <> 3 AND v.estado = 'a' AND (v.enviado_sunat = '' OR v.enviado_sunat = '0' OR v.enviado_sunat IS NULL)");
            $stm->execute();
            $c = $stm->fetch(PDO::FETCH_OBJ);
            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /* FIN COMPROBANTE SIN ENVIAR SUNAT */

    /* INICIO PEDIDOS PREPARADOS */

    public function contadorPedidosPreparados()
    {
        try {
            if (Session::get('rol') == 1 or Session::get('rol') == 2 or Session::get('rol') == 3) {
                $stm = $this->db->prepare("SELECT COUNT(id_pedido) AS cantidad FROM v_cocina_me WHERE id_tipo = 1 AND cantidad > 0 AND estado = 'c'");
                $stm->execute();
            } elseif (Session::get('rol') == 5) {
                $stm = $this->db->prepare("SELECT COUNT(id_pedido) AS cantidad FROM v_cocina_me WHERE id_tipo = 1 AND id_mozo = ? AND cantidad > 0 AND estado = 'c'");
                $stm->execute(array(Session::get('usuid')));
            }
            $stm->execute();
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function listarPedidosPreparados()
    {
        try {
            if (Session::get('rol') == 1 or Session::get('rol') == 2 or Session::get('rol') == 3) {
                $stm = $this->db->prepare("SELECT * FROM v_cocina_me WHERE id_tipo = 1 AND cantidad > 0 AND estado = 'c'");
                $stm->execute();
            } elseif (Session::get('rol') == 5) {
                $stm = $this->db->prepare("SELECT * FROM v_cocina_me WHERE id_tipo = 1 AND id_mozo = ? AND cantidad > 0 AND estado = 'c'");
                $stm->execute(array(Session::get('usuid')));
            }
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function pedidoEntregado($data)
    {
        try {
            $sql = "UPDATE tm_detalle_pedido SET estado = 'd' WHERE id_pedido = ? AND id_pres = ? AND fecha_pedido = ?";
            $this->db->prepare($sql)
                ->execute(array(
                    $data['id_pedido'],
                    $data['id_pres'],
                    $data['fecha_pedido']
                ));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function pedido_estado_update($data)
    {
        try {
            if ($data['estado'] == 'i') {
                $estado = 'p';
            } elseif ($data['estado'] == 'p') {
                $estado = 'i';
            };
            $sql = "UPDATE tm_mesa SET estado = ? WHERE id_mesa = ?";
            $this->db->prepare($sql)->execute(array($estado, $data['id_mesa']));
        } catch (Exception $e) {
            return false;
        }
    }

    /* FIN PEDIDOS PREPARADOS */

    public function menu_categoria_list()
    {
        try {
            $stm = $this->db->prepare("SELECT * FROM tm_producto_catg WHERE delivery = 1");
            $stm->execute();
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function menu_plato_list($data)
    {
        try {
            $stm = $this->db->prepare("SELECT * FROM v_productos WHERE del_a = 1 AND del_b = 1 AND del_c = 1 AND id_catg = ?");
            $stm->execute(array($data['id_catg']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function menu_plato_estado($data)
    {
        try {
            if ($data['estado'] == 'a') {
                $estado = 'i';
            } elseif ($data['estado'] == 'i') {
                $estado = 'a';
            };
            $sql = "UPDATE tm_producto_pres SET estado = ? WHERE id_pres = ?";
            $this->db->prepare($sql)->execute(array($estado, $data['id_pres']));
        } catch (Exception $e) {
            return false;
        }
    }

    /* INICIO IMPRESION */

    public function impresion_precuenta($id_pedido)
    {
        try {
            $stm = $this->db->prepare("SELECT * FROM v_pedido_mesa WHERE id_pedido = ?");
            $stm->execute(array($id_pedido));
            $c = $stm->fetch(PDO::FETCH_OBJ);
            /* Traemos el detalle */
            $c->{'Detalle'} = $this->db->query("SELECT id_pres, SUM(cantidad) AS cantidad, precio FROM tm_detalle_pedido WHERE id_pedido = " . $c->id_pedido . " AND estado <> 'z' GROUP BY id_pres")
                ->fetchAll(PDO::FETCH_OBJ);
            foreach ($c->Detalle as $k => $d) {
                $c->Detalle[$k]->{'Producto'} = $this->db->query("SELECT pro_nom, pro_pre FROM v_productos WHERE id_pres = " . $d->id_pres)
                    ->fetch(PDO::FETCH_OBJ);
            }
            $c->{'host_pc'} = Session::get('host_pc');
            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function impresion_reparto($id_venta)
    {
        try {
            $stm = $this->db->prepare("SELECT v.id_ven, v.id_cli, v.desc_tp, v.id_tpag, v.fec_ven, v.pago_efe, v.pago_efe_none, v.pago_tar,IFNULL((v.total+v.comis_del-v.desc_monto),0) AS total, d.nro_pedido FROM v_ventas_con AS v INNER JOIN tm_pedido_delivery AS d ON v.id_ped = d.id_pedido WHERE id_ven = ?");
            $stm->execute(array($id_venta));
            $c = $stm->fetch(PDO::FETCH_OBJ);
            $c->{'Cliente'} = $this->db->query("SELECT * FROM v_clientes WHERE id_cliente = " . $c->id_cli)
                ->fetch(PDO::FETCH_OBJ);
            /* Traemos el detalle */
            $c->{'Detalle'} = $this->db->query("SELECT id_prod,SUM(cantidad) AS cantidad, precio FROM tm_detalle_venta WHERE id_venta = " . $c->id_ven . " GROUP BY id_prod, precio")
                ->fetchAll(PDO::FETCH_OBJ);
            foreach ($c->Detalle as $k => $d) {
                $c->Detalle[$k]->{'Producto'} = $this->db->query("SELECT pro_nom, pro_pre FROM v_productos WHERE id_pres = " . $d->id_prod)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function contador_comanda()
    {
        try {
            $stm = $this->db->prepare("SELECT COUNT(*) AS correlativo FROM tm_detalle_pedido GROUP BY id_pedido,fecha_pedido");
            $stm->execute();
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function alert_pedidos_programados()
    {
        try {
            $stm = $this->db->prepare("SELECT MIN(pd.hora_entrega) AS hora_entrega, pd.id_pedido, pd.nombre_cliente, pd.nro_pedido FROM tm_pedido_delivery AS pd INNER JOIN tm_pedido AS p ON pd.id_pedido = p.id_pedido WHERE pd.pedido_programado = 1 AND p.estado = 'a'");
            $stm->execute();
            $c = $stm->fetch(PDO::FETCH_OBJ);
            return $c;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    /* FIN IMPRESION */
    ##NUEVAS FUNCIONESSS

    public function deleteSeleccion($data)
    {
        $i = 0;
        try {
            $pedidos = json_decode($data["pedidos"], true);
            for ($x = 0; $x < count($pedidos); $x++) {
                $key = $pedidos[$x]["id_p"];
                $stm = $this->db->prepare("UPDATE tm_detalle_pedido SET estado = 'z' WHERE id_pedido = '" . $data["id_pedido"] . "' AND  id_pres = '" . $key . "'");
                if ($stm->execute()) {
                    $i++;
                }
            }
            if ($i == count($pedidos)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    public function Usuarios_rol($id)
    {
        $c = $this->db->query("SELECT id_salon AS salon FROM tm_areas_rel WHERE id_usu = $id")->fetchAll(PDO::FETCH_OBJ);
        return $c;
    }
    //FUNCIONES PARA EL MODULO DE PORTERO
    public function verifica_porteria()
    {
        try {
            $id_usu = Session::get('usuid');
            //$SQL = 'SELECT *FROM portero_apertura WHERE id_usuario = "'.Session::get('usuid').'" AND estado = "a"';
            $st = $this->db->query("SELECT *FROM portero_apertura WHERE id_usuario = {$id_usu} AND estado = 'a' LIMIT 1")->fetch(PDO::FETCH_OBJ);
            if (($st)) {
                $c = array("data" => $st);
                echo json_encode($c);
            } else {
                $array = array(
                    "error" => "Ninguna apertura"
                );
                echo json_encode($array);
            }
        } catch (PDOexception $th) {
            //throw $th;
            echo "Error" . $th->getMessage();
        }
    }
    public function registra_impresion($data)
    {
        date_default_timezone_set('America/Lima');
        $nombre = $data['nombre_imp'];
        $id_pedido = $data['id_ped'];
        $status = "a";
        $url = $data['url'];
        $tipo_imp = $data['tipo_imp'];
        $usu = Session::get('usuid');
        $json = $data['json'];
        $fecha = date('Y-m-d H:i:s');
        if ($json == '') {
            $x = $this->db->query("INSERT INTO tm_registro_impresiones (id_registro, nombre_impresora, tipo_impresion, id_usuario,  fecha, id_pedido, status, url, json) 
            VALUES(null, '{$nombre}', '{$tipo_imp}',{$usu}, '{$fecha}' ,  {$id_pedido},  '{$status}', '{$url}', null)");
        } else {
            $x = $this->db->query("INSERT INTO tm_registro_impresiones (id_registro, nombre_impresora, tipo_impresion, id_usuario,  fecha, id_pedido, status, url, json) 
            VALUES(null, '{$nombre}', '{$tipo_imp}',{$usu}, '{$fecha}' ,  {$id_pedido},  '{$status}', '{$url}', '{$json}')");
        }
        if ($x) {
            $return = array("msj" => 1);
        } else {
            $return = array("msj" => -1);
        }
        echo json_encode($return);
    }
    public function info_product($id)
    {
        $c = $this->db->query("SELECT * FROM tm_producto_ingr WHERE id_pres = {$id}")->fetchAll(PDO::FETCH_OBJ);
        if ($c) {
            foreach ($c as $k => $d) {
                $c[$k]->{'presentacion'} = $this->db->query("SELECT * FROM tm_producto_pres WHERE id_pres = {$d->id_ins}")->fetch(PDO::FETCH_OBJ);
                $c[$k]->{'producto'} = $this->db->query("SELECT * FROM tm_producto WHERE id_prod = {$c[$k]->{'presentacion'}->id_prod}")->fetch(PDO::FETCH_OBJ);
                $c[$k]->{'area'} = $this->db->query("SELECT id_imp, id_areap, nombre FROM tm_area_prod WHERE id_areap = {$c[$k]->{'producto'}->id_areap}")->fetch(PDO::FETCH_OBJ);
                $c[$k]->{'impresora'} = $this->db->query("SELECT * FROM tm_impresora WHERE id_imp = {$c[$k]->{'area'}->id_imp}")->fetch(PDO::FETCH_OBJ);
            }
        }
        $data = array("data" => $c);
        $json = json_encode($data);
        echo $json;
    }
}
