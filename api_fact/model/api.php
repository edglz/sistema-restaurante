<?php

class ApiModel
{
    public function __construct() {
        $this->db = new Database(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_CHARSET);
    }

    public function getEmpresa()
    {
        try
        {      
            $stm = $this->db->prepare("SELECT * FROM tm_empresa");
            $stm->execute();
            $c = $stm->fetch(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    /******************* INICIO FACTURA - BOLETA *******************/
    public function getCabecera($cod_ven)
    {
        try
        {      
            $stm = $this->db->prepare("SELECT
            tm_venta.enviado_sunat,
            IF(tm_venta.id_tipo_doc=1,'1',IF(tm_venta.id_tipo_doc=2,'6','0')) AS tipo_documento,
            IF(tm_venta.id_tipo_doc=1,tm_cliente.dni,tm_cliente.ruc) AS numero_documento,
            IF(tm_venta.id_tipo_doc=1,CONCAT(tm_cliente.nombres),tm_cliente.razon_social) AS razon_social,
            tm_cliente.direccion AS direccion,
            DATE(tm_venta.fecha_venta) AS fecha,
            (tm_venta.fecha_venta) AS fecha_imp,
            IF(tm_venta.id_tipo_doc='1','03','01') AS tipo_comprobante,
            (tm_venta.total + tm_venta.comision_delivery - tm_venta.descuento_monto) AS total_venta,
            (tm_venta.igv * 100) AS impuesto,
            /*CONCAT(CONCAT(IF(tm_venta.id_tipo_doc='1','B','F'),tm_venta.serie_doc),'-',CONCAT('0',tm_venta.nro_doc)) AS numero,*/
            tm_venta.serie_doc,
            tm_venta.nro_doc,
            tm_venta.descuento_monto,
            tm_venta.total
            FROM
            tm_venta
            INNER JOIN tm_cliente ON tm_venta.id_cliente = tm_cliente.id_cliente
            INNER JOIN tm_tipo_doc ON tm_venta.id_tipo_doc = tm_tipo_doc.id_tipo_doc
            INNER JOIN tm_usuario ON tm_venta.id_usu = tm_usuario.id_usu
            WHERE tm_venta.id_tipo_doc IN (1,2) AND tm_venta.id_venta = ?");
            $stm->execute(array($cod_ven));
            $c = $stm->fetch(PDO::FETCH_OBJ);

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
                WHERE tm_venta.id_tipo_doc  IN ('1','2') AND tm_detalle_venta.precio > 0 AND tm_detalle_venta.id_venta = ".$cod_ven)
                ->fetchAll(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    /******************* FIN FACTURA - BOLETA *******************/

    /****************** INCIO COMUNICACION DE BAJA ************/

    public function generar_numero_baja($tipo_doc)
    {
        $consulta = $this->db->query("CALL sp_generar_numerobaja('0".$tipo_doc."',@numero)");
        $query    = $this->db->query('SELECT @numero AS numero');
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function buscar_documento($cod_ven)
    {
        $consulta = $this->db->prepare("CALL sp_consultar_documento(?)");
        $consulta->bindValue(1, $cod_ven, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_OBJ);
    }

    public function registrar_baja($cabecera,$cod_ven)
    {
        $status = false;
        $rpta = new stdClass();

        try {

            $this->db->beginTransaction(); //Inicializamos la transacción
            $fieldNames = implode(', ', array_keys($cabecera));
            $fieldValues = "'" . implode("', '", $cabecera) . "'";
            $this->db->query("INSERT INTO comunicacion_baja ($fieldNames) VALUES ($fieldValues)");
            $idcomunicacion = $this->db->lastInsertId(); //Recuperamos el código del Resumen
            $status = true;
            $this->db->query("UPDATE tm_venta SET estado='i' WHERE id_venta='".$cod_ven."'");

            if ($status) {
                $this->db->commit(); //Confirmamos si se ha registrado correctamente
                $rpta->mensaje = "Registrado correctamente";
                $rpta->idcomunicacion = $idcomunicacion;
                $rpta->status = $status;
            }
            
        } catch (Exception $e) {
            $this->db->rollBack(); //Borra todo el proceso que realizó
            $rpta->mensaje = $e->getMessage();
            $rpta->status = false;
        }

        return $rpta;
    }

    public function actualizar_cdr_baja($data)
    {
        $rpta = new stdClass();

        try {

            $this->db->query("CALL sp_actualizar_cdr_baja('" . implode("', '", $data) . "',@Mensaje)");
            $result = $this->db->query('SELECT @Mensaje AS mensaje')->fetch(PDO::FETCH_OBJ);;
            $rpta->mensaje = $result->mensaje;

        } catch (Exception $e) {
            $rpta->mensaje = $e->getMessage();
        }

        return $rpta;
    }

    /************ FIN COMUNICACION DE BAJA ******************/

    
    /******************* INICIO RESUMEN DE BOLETAS *******************/

    public function generar_numero_resumen()
    {
        $consulta = $this->db->query("CALL sp_generar_numeroresumen(@numero)");
        $query    = $this->db->query('SELECT @numero AS numero');
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function buscar_boletas($fecha_resumen)
    {
        $consulta = $this->db->prepare("CALL sp_consultar_boletas_resumen(?)");
        $consulta->bindValue(1, $fecha_resumen, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public function registrar_resumen($cabecera , $detalle)
    {
        $status = false;
        $rpta = new stdClass();

        try {

            $this->db->beginTransaction(); //Inicializamos la transacción
            $fieldNames = implode(', ', array_keys($cabecera));
            $fieldValues = "'" . implode("', '", $cabecera) . "'";
            $this->db->query("INSERT INTO resumen_diario ($fieldNames) VALUES ($fieldValues)");
            $id_resumen = $this->db->lastInsertId(); //Recuperamos el código del Resumen

            foreach ($detalle as $clave => $valor)
            {
                $valor['id_resumen'] = $id_resumen;
                $fieldNames = implode(', ', array_keys($valor));
                $fieldValues = "'" . implode("', '", $valor) . "'";
                $result = $this->db->query("INSERT INTO resumen_diario_detalle ($fieldNames) VALUES ($fieldValues)");
                if ($result) {
                    $this->db->query("UPDATE tm_venta SET enviado_sunat=1 WHERE id_venta='".$valor['id_venta']."'");
                    $status = true;
                }else{
                    $status = false;
                    throw new Exception("Error al registrar detalle del resumen", 1);
                }
            }

            if ($status) {
                $this->db->commit(); //Confirmamos si se ha registrado correctamente
                $rpta->mensaje = "Registrado correctamente";
                $rpta->idresumen = $id_resumen;
                $rpta->status = $status;
            }
            
        } catch (Exception $e) {
            $this->db->rollBack(); //Borra todo el proceso que realizó
            $rpta->mensaje = $e->getMessage();
            $rpta->status = false;
        }

        return $rpta;
    }

    public function actualizar_cdr_resumen($data)
    {
        $rpta = new stdClass();

        try {

            $this->db->query("CALL sp_actualizar_cdr_resumen('" . implode("', '", $data) . "',@Mensaje)");
            $result = $this->db->query('SELECT @Mensaje AS mensaje')->fetch(PDO::FETCH_OBJ);;
            $rpta->mensaje = $result->mensaje;

        } catch (Exception $e) {
            $rpta->mensaje = $e->getMessage();
        }

        return $rpta;
    }

}

?>