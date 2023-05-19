-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 23-03-2023 a las 22:16:59
-- Versión del servidor: 10.5.15-MariaDB-cll-lve
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u407783947_restobar`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `sp_actualizar_cdr_baja` (`p_id_comunicacion` INT, `p_hash_cpe` VARCHAR(100), `p_hash_cdr` VARCHAR(100), `p_code_respuesta_sunat` VARCHAR(5), `p_descripcion_sunat_cdr` VARCHAR(300), `p_name_file_sunat` VARCHAR(80), OUT `mensaje` VARCHAR(100))  BEGIN
	IF(NOT EXISTS(SELECT * FROM comunicacion_baja WHERE id_comunicacion=p_id_comunicacion))THEN
		SET mensaje='No existe la comunicación de baja';
	ELSE
		UPDATE comunicacion_baja SET enviado_sunat=1,hash_cpe=p_hash_cpe,hash_cdr=p_hash_cdr,code_respuesta_sunat=p_code_respuesta_sunat,descripcion_sunat_cdr=p_descripcion_sunat_cdr,name_file_sunat=p_name_file_sunat WHERE id_comunicacion=p_id_comunicacion;
		SET mensaje='Actualizado correctamente';
	END IF;
END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `sp_actualizar_cdr_resumen` (`p_id_resumen` INT, `p_hash_cpe` VARCHAR(100), `p_hash_cdr` VARCHAR(100), `p_code_respuesta_sunat` VARCHAR(5), `p_descripcion_sunat_cdr` VARCHAR(300), `p_name_file_sunat` VARCHAR(80), OUT `mensaje` VARCHAR(100))  BEGIN
	IF(NOT EXISTS(SELECT * FROM resumen_diario WHERE id_resumen=p_id_resumen))THEN
		SET mensaje='No existe el resumen diario';
	ELSE
		UPDATE resumen_diario SET enviado_sunat=1,hash_cpe=p_hash_cpe,hash_cdr=p_hash_cdr,code_respuesta_sunat=p_code_respuesta_sunat,descripcion_sunat_cdr=p_descripcion_sunat_cdr,name_file_sunat=p_name_file_sunat WHERE id_resumen=p_id_resumen;
		SET mensaje='Actualizado correctamente';
		
		block:BEGIN
		DECLARE done INT DEFAULT FALSE;
		DECLARE idven BIGINT;
		DECLARE venta CURSOR FOR SELECT dr.id_venta FROM resumen_diario AS rd INNER JOIN resumen_diario_detalle AS dr ON rd.id_resumen = dr.id_resumen WHERE dr.id_resumen = p_id_resumen;
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done=TRUE;
		OPEN venta;
		
			read_loop: LOOP
			FETCH venta INTO idven;
				IF done THEN
					LEAVE read_loop;
				END IF;
				UPDATE tm_venta SET code_respuesta_sunat=p_code_respuesta_sunat,descripcion_sunat_cdr=p_descripcion_sunat_cdr,name_file_sunat=p_name_file_sunat,hash_cpe=p_hash_cpe,hash_cdr=p_hash_cdr WHERE id_venta = idven;
			END LOOP;
			
		CLOSE venta;
		END block;
	END IF;
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `sp_consultar_boletas_resumen` (`p_fecha_resumen` DATE)  BEGIN
	SELECT
		'03' AS 'tipo_comprobante',DATE_FORMAT(v.fecha_venta,'%Y-%m-%d') AS 'fecha_resumen',IF(c.dni="" OR c.dni="-",0,1) AS 'tipo_documento',
		IF(c.dni="" OR c.dni="-","00000000",c.dni) AS "dni",CONCAT(c.nombres," ",c.ape_paterno," ",c.ape_materno) AS 'cliente',v.serie_doc AS 'serie_doc',
		v.nro_doc AS 'nro_doc',"PEN" AS 'tipo_moneda',ROUND((v.total/(1 + v.igv)) *(v.igv),2) AS 'total_igv',
		ROUND((v.total/(1 + v.igv)),2) AS 'total_gravadas',ROUND(v.total,2) AS 'total_facturado',IF(v.estado="a",1,3) AS 'status_code',v.id_venta
	FROM tm_venta v INNER JOIN tm_cliente c ON c.id_cliente=v.id_cliente
	WHERE v.id_tipo_doc=1 AND v.code_respuesta_sunat="" AND DATE_FORMAT(v.fecha_venta,"%Y-%m-%d") = p_fecha_resumen
	ORDER BY v.fecha_venta ASC;
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `sp_consultar_documento` (`p_id_venta` INT)  BEGIN
	SELECT
		IF(id_tipo_doc='1','03','01') AS tipo_comprobante, IF(c.dni="" OR c.dni="-",0,1) AS 'tipo_documento',
		IF(c.dni="" OR c.dni="-","00000000",c.dni) AS "dni",v.serie_doc AS 'serie_doc', v.nro_doc AS 'nro_doc',"PEN" AS 'tipo_moneda',ROUND((v.total/(1 + v.igv)) *(v.igv),2) AS 'total_igv',
		ROUND((v.total/(1 + v.igv)),2) AS 'total_gravadas',ROUND(v.total,2) AS 'total_facturado',v.id_venta, v.estado
	FROM tm_venta v INNER JOIN tm_cliente c ON c.id_cliente=v.id_cliente
	WHERE v.id_venta = p_id_venta;
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `sp_generar_numerobaja` (`p_tipo_doc` CHAR(3), OUT `numerobaja` CHAR(5))  BEGIN
	DECLARE contador INT;
	IF(NOT EXISTS(SELECT * FROM comunicacion_baja WHERE tipo_doc = p_tipo_doc))THEN
		SET contador:= (SELECT IFNULL(MAX(correlativo), 0)+1 AS 'codigo' FROM comunicacion_baja WHERE tipo_doc = p_tipo_doc);
		SET numerobaja:= (SELECT LPAD(contador,5,'0') AS 'correlativo');
	ELSE		
		SET contador:= (SELECT IFNULL(MAX(correlativo), 0)+1 AS 'codigo' FROM comunicacion_baja WHERE tipo_doc = p_tipo_doc);
		SET numerobaja:= (SELECT LPAD(contador,5,'0') AS 'correlativo');
	END IF;
END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `sp_generar_numeroresumen` (OUT `numeroresumen` CHAR(5))  BEGIN
	DECLARE contador INT;
	IF(NOT EXISTS(SELECT * FROM resumen_diario))THEN
		SET contador:= (SELECT IFNULL(MAX(correlativo), 0)+1 AS 'codigo' FROM resumen_diario);
		SET numeroresumen:= (SELECT LPAD(contador,5,'0') AS 'correlativo');
	ELSE		
		SET contador:= (SELECT IFNULL(MAX(correlativo), 0)+1 AS 'codigo' FROM resumen_diario);
		SET numeroresumen:= (SELECT LPAD(contador,5,'0') AS 'correlativo');
	END IF;
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_cajaAperturar` (IN `_flag` INT(11), IN `_id_usu` INT(11), IN `_id_caja` INT(11), IN `_id_turno` INT(11), IN `_fecha_aper` DATETIME, IN `_monto_aper` DECIMAL(10,2), IN `_cod_rep` VARCHAR(10))  BEGIN
	DECLARE _filtro INT DEFAULT 1;
	
	IF _flag = 1 THEN
	
		SELECT COUNT(*) INTO _filtro FROM tm_aper_cierre WHERE (id_usu = _id_usu or id_caja = _id_caja) AND estado = 'a';
		
		IF _filtro = 0 THEN
			INSERT INTO tm_aper_cierre (id_usu,id_caja,id_turno,fecha_aper,monto_aper, cod_reporte) VALUES (_id_usu, _id_caja, _id_turno, _fecha_aper, _monto_aper, _cod_rep);
			
			SELECT @@IDENTITY INTO @id;
			
			SELECT @id AS id_apc, _filtro AS cod;
		ELSE
			SELECT _filtro AS cod;
		END IF;
		
	END IF;
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_cajaCerrar` (IN `_flag` INT(11), IN `_id_apc` INT(11), IN `_fecha_cierre` DATETIME, IN `_monto_cierre` DECIMAL(10,2), IN `_monto_sistema` DECIMAL(10,2), IN `_stock_pollo` VARCHAR(11))  BEGIN
		DECLARE _filtro INT DEFAULT 0;
		DECLARE _id_usu INT DEFAULT 0;
		
		IF _flag = 1 THEN
		
			SELECT COUNT(*) INTO _filtro FROM tm_aper_cierre WHERE id_apc = _id_apc AND estado = 'a';
			SELECT id_usu INTO _id_usu FROM tm_aper_cierre WHERE id_apc = _id_apc AND estado = 'a';
			
			IF _filtro = 1 THEN
			
				UPDATE tm_aper_cierre SET fecha_cierre = _fecha_cierre, monto_cierre = _monto_cierre, monto_sistema = _monto_sistema, stock_pollo = _stock_pollo, estado = 'c' 
				WHERE id_apc = _id_apc;
				
				SELECT _filtro AS cod, _id_usu AS id_usu;
			ELSE
				SELECT _filtro AS cod, _id_usu AS id_usu;
			END IF;
		END IF;
	END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_comprasAnular` (IN `_flag` INT(11), IN `_id_compra` INT(11))  BEGIN
	DECLARE _filtro INT DEFAULT 0;
	if _flag = 1 then
	
		SELECT COUNT(*) INTO _filtro FROM tm_compra WHERE estado = 'a' AND id_compra = _id_compra;
		
		IF _filtro = 1 THEN
			UPDATE tm_compra SET estado = 'i' WHERE id_compra = _id_compra;
			DELETE FROM tm_inventario WHERE id_tipo_ope = 1 AND id_ope = _id_compra;
			SELECT _filtro AS cod;
		ELSE
			SELECT _filtro AS cod;
		END IF;
	end if;
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_comprasCreditoCuotas` (IN `_flag` INT(11), IN `_id_credito` INT(11), IN `_id_usu` INT(11), IN `_id_apc` INT(11), IN `_importe` DECIMAL(10,2), IN `_fecha` DATETIME, IN `_egreso` INT(11), IN `_monto_egreso` DECIMAL(10,2), IN `_monto_amortizado` DECIMAL(10,2), IN `_total_credito` DECIMAL(10,2))  BEGIN
	DECLARE tcuota DECIMAL(10,2) DEFAULT 0;
	DECLARE motivo VARCHAR(100);
	
	IF _flag = 1 THEN
	
		INSERT INTO tm_credito_detalle (id_credito,id_usu,importe,fecha,egreso)
		VALUES (_id_credito, _id_usu, _importe, _fecha, _egreso);
	
			IF (_egreso = 1) THEN
	
				SELECT v.desc_prov INTO @descP
				FROM v_compras AS v INNER JOIN tm_compra_credito AS c ON v.id_compra = c.id_compra
				WHERE c.id_credito = _id_credito;
		
			SET motivo = @descP;
		
				INSERT INTO tm_gastos_adm (id_tipo_gasto,id_usu,id_apc,importe,motivo,fecha_registro)
				VALUES (4,_id_usu,_id_apc,_monto_egreso,motivo,_fecha);
	
			END IF;
	
		SET tcuota = _monto_amortizado + _importe;
	
		IF ( _total_credito <= tcuota ) THEN
	
			UPDATE tm_compra_credito SET estado = 'a' WHERE id_credito = _id_credito;
	
		END IF;
	
	END IF;
	
END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_comprasRegProveedor` (IN `_flag` INT(11), IN `_id_prov` INT(11), IN `_ruc` VARCHAR(13), IN `_razon_social` VARCHAR(100), IN `_direccion` VARCHAR(100), IN `_telefono` INT(9), IN `_email` VARCHAR(45), IN `_contacto` VARCHAR(45))  BEGIN
		DECLARE _filtro INT DEFAULT 1;
		
		IF _flag = 1 THEN
		
			SELECT count(*) INTO _filtro FROM tm_proveedor WHERE ruc = _ruc;
		
			IF _filtro = 0 THEN
			
				INSERT INTO tm_proveedor (ruc,razon_social,direccion,telefono,email,contacto) 
				VALUES (_ruc, _razon_social, _direccion, _telefono, _email, _contacto);
				
				SELECT @@IDENTITY INTO @id;
			
				SELECT _filtro AS cod,@id AS id_prov;
			ELSE
				SELECT _filtro AS cod;
			END IF;	
			
		END IF;
		
		if _flag = 2 then
		
			UPDATE tm_proveedor SET ruc = _ruc, razon_social = _razon_social, direccion = _direccion, telefono = _telefono, email = _email, contacto = _contacto
			WHERE id_prov = _id_prov;
			
		end if;
	END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_configAlmacenes` (IN `_flag` INT(11), IN `_nombre` VARCHAR(45), IN `_estado` VARCHAR(5), IN `_idAlm` INT(11))  BEGIN
	DECLARE _cont INT DEFAULT 0;
	DECLARE _cod0 INT DEFAULT 0;
	DECLARE	_cod1 INT DEFAULT 1;
	DECLARE	_cod2 INT DEFAULT 2;
	
	IF _flag = 1 THEN
		SELECT COUNT(*) INTO _cont FROM tm_almacen WHERE nombre = _nombre;
	
		IF _cont = 0 THEN
			INSERT INTO tm_almacen (nombre,estado) VALUES (_nombre, _estado);
			SELECT _cod1 AS cod;
		ELSE
			SELECT _cod0 AS cod;
		END IF;
	END IF;
	
	IF _flag = 2 THEN
		SELECT COUNT(*) INTO _cont FROM tm_almacen WHERE nombre = _nombre AND estado = _estado;
	
		IF _cont = 0 THEN
			UPDATE tm_almacen SET nombre = _nombre, estado = _estado WHERE id_alm = _idAlm;
			SELECT _cod2 AS cod;
		ELSE
			SELECT _cod2 AS cod;
		END IF;
	END IF;
	END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_configAreasProd` (IN `_flag` INT(11), IN `_id_areap` INT(11), IN `_id_imp` INT(11), IN `_nombre` VARCHAR(45), IN `_estado` VARCHAR(5))  BEGIN
	DECLARE _cont INT DEFAULT 0;
	DECLARE _cod0 INT DEFAULT 0;
	DECLARE	_cod1 INT DEFAULT 1;
	DECLARE	_cod2 INT DEFAULT 2;
	
	IF _flag = 1 THEN
		SELECT COUNT(*) INTO _cont FROM tm_area_prod WHERE nombre = _nombre;
	
		IF _cont = 0 THEN
			INSERT INTO tm_area_prod (id_imp,nombre,estado) VALUES (_id_imp, _nombre, _estado);
			SELECT _cod1 AS cod;
		ELSE
			SELECT _cod0 AS cod;
		END IF;
	END IF;
	
	IF _flag = 2 THEN
		SELECT COUNT(*) INTO _cont FROM tm_area_prod WHERE id_imp = _id_imp AND nombre = _nombre AND estado = _estado;
	
		IF _cont = 0 THEN
			UPDATE tm_area_prod SET id_imp = _id_imp, nombre = _nombre, estado = _estado WHERE id_areap = _id_areap;
			SELECT _cod2 AS cod;
		ELSE
			SELECT _cod2 AS cod;
		END IF;
	END IF;
	END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_configCajas` (IN `_flag` INT(11), IN `_id_caja` INT(11), IN `_descripcion` VARCHAR(45), IN `_estado` VARCHAR(5))  BEGIN
	DECLARE _filtro INT DEFAULT 0;
	DECLARE _cod0 INT DEFAULT 0;
	DECLARE	_cod1 INT DEFAULT 1;
	DECLARE	_cod2 INT DEFAULT 2;
	
	IF _flag = 1 THEN
		SELECT COUNT(*) INTO _filtro FROM tm_caja WHERE descripcion = _descripcion;
	
		IF _filtro = 0 THEN
			INSERT INTO tm_caja (descripcion,estado) VALUES (_descripcion, _estado);
			SELECT _cod1 AS cod;
		ELSE
			SELECT _cod0 AS cod;
		END IF;
	END IF;
	
	IF _flag = 2 THEN
	
		SELECT COUNT(*) INTO _filtro FROM tm_caja WHERE descripcion = _descripcion AND estado = _estado;
	
		IF _filtro = 0 THEN
			UPDATE tm_caja SET descripcion = _descripcion, estado = _estado WHERE id_caja = _id_caja;
			SELECT _cod2 AS cod;
		ELSE
			SELECT _cod2 AS cod;
		END IF;
	END IF;
	END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_configEliminarCategoriaIns` (IN `_id_catg` INT(11))  BEGIN
	DECLARE _filtro INT DEFAULT 0;
	DECLARE _cod0 INT DEFAULT 0;
	DECLARE	_cod1 INT DEFAULT 1;
	
	SELECT COUNT(*) INTO _filtro FROM tm_insumo WHERE id_catg = _id_catg;
	IF _filtro = 0 THEN
		DELETE FROM tm_insumo_catg WHERE id_catg = _id_catg;
		SELECT _cod1 AS cod;
	ELSE
		SELECT _cod0 AS cod;
	END IF;
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_configEliminarCategoriaProd` (IN `_id_catg` INT(11))  BEGIN
	DECLARE _filtro INT DEFAULT 0;
	DECLARE _cod0 INT DEFAULT 0;
	DECLARE	_cod1 INT DEFAULT 1;
	
	SELECT COUNT(*) INTO _filtro FROM tm_producto WHERE id_catg = _id_catg;
	IF _filtro = 0 THEN
		DELETE FROM tm_producto_catg WHERE id_catg = _id_catg;
		SELECT _cod1 AS cod;
	ELSE
		SELECT _cod0 AS cod;
	END IF;
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_configImpresoras` (IN `_flag` INT(11), IN `_id_imp` INT(11), IN `_nombre` VARCHAR(50), IN `_estado` VARCHAR(5))  BEGIN
	DECLARE _filtro INT DEFAULT 0;
	DECLARE _cod0 INT DEFAULT 0;
	DECLARE	_cod1 INT DEFAULT 1;
	DECLARE	_cod2 INT DEFAULT 2;
	
	IF _flag = 1 THEN
		SELECT COUNT(*) INTO _filtro FROM tm_impresora WHERE nombre = _nombre;
	
		IF _filtro = 0 THEN
			INSERT INTO tm_impresora (nombre,estado) VALUES (_nombre,_estado);
			SELECT _cod1 AS cod;
		ELSE
			SELECT _cod0 AS cod;
		END IF;
	END IF;
	
	IF _flag = 2 THEN
		SELECT COUNT(*) INTO _filtro FROM tm_impresora WHERE nombre = _nombre AND estado = _estado;
	
		IF _filtro = 0 THEN
			UPDATE tm_impresora SET nombre = _nombre, estado = _estado WHERE id_imp = _id_imp;
			SELECT _cod2 AS cod;
		ELSE
			SELECT _cod2 AS cod;
		END IF;
	END IF;
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_configInsumo` (IN `_flag` INT(11), IN `_idCatg` INT(11), IN `_idMed` INT(11), IN `_cod` VARCHAR(10), IN `_nombre` VARCHAR(45), IN `_stock` INT(11), IN `_costo` DECIMAL(10,2), IN `_estado` VARCHAR(5), IN `_idIns` INT(11))  BEGIN
	DECLARE _cont INT DEFAULT 0;
	DECLARE _cod0 INT DEFAULT 0;
	DECLARE	_cod1 INT DEFAULT 1;
	DECLARE	_cod2 INT DEFAULT 2;
	
	IF _flag = 1 THEN
	
		SELECT COUNT(*) INTO _cont FROM tm_insumo WHERE nomb_ins = _nombre and cod_ins = _cod and id_catg = _idCatg;
	
		IF _cont = 0 THEN
			INSERT INTO tm_insumo (id_catg,id_med,cod_ins,nomb_ins,stock_min,cos_uni) VALUES ( _idCatg, _idMed, _cod, _nombre, _stock, _costo);
			SELECT _cod1 AS cod;
		ELSE
			SELECT _cod0 AS cod;
		END IF;
		
	END IF;
	
	IF _flag = 2 THEN
	
		SELECT COUNT(*) INTO _cont FROM tm_insumo WHERE id_catg = _idCatg AND id_med = _idMed AND cod_ins = _cod AND nomb_ins = _nombre AND stock_min = _stock AND cos_uni = _costo AND estado = _estado;
	
		IF _cont = 0 THEN
			UPDATE tm_insumo SET id_catg = _idCatg, id_med = _idMed, cod_ins = _cod, nomb_ins = _nombre, stock_min = _stock, cos_uni = _costo, estado = _estado WHERE id_ins = _idIns;
			SELECT _cod2 AS cod;
		ELSE
			SELECT _cod2 AS cod;
		END IF;
	END IF;
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_configInsumoCatgs` (IN `_flag` INT(11), IN `_descC` VARCHAR(45), IN `_idCatg` INT(11))  BEGIN
	DECLARE _cont INT DEFAULT 0;
	DECLARE _cod0 INT DEFAULT 0;
	DECLARE	_cod1 INT DEFAULT 1;
	DECLARE	_cod2 INT DEFAULT 2;
	
	IF _flag = 1 THEN
	
		SELECT COUNT(*) INTO _cont FROM tm_insumo_catg WHERE descripcion = _descC;
		
		IF _cont = 0 THEN
			INSERT INTO tm_insumo_catg (descripcion) VALUES (_descC);
			SELECT _cod1 AS cod;
		ELSE
			SELECT _cod0 AS cod;
		END IF;
	
	END IF;
	
	IF _flag = 2 THEN
	
		SELECT COUNT(*) INTO _cont FROM tm_insumo_catg WHERE descripcion = _descC;
		
		IF _cont = 0 THEN
			UPDATE tm_insumo_catg SET descripcion = _descC WHERE id_catg = _idCatg;
			SELECT _cod2 AS cod;
		ELSE
			SELECT _cod2 AS cod;
		END IF;
	
	END IF;
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_configMesas` (IN `_flag` INT(11), IN `_id_mesa` INT(11), IN `_id_salon` INT(11), IN `_nro_mesa` VARCHAR(5), IN `_estado` VARCHAR(45))  BEGIN
	DECLARE _filtro INT DEFAULT 0;
	DECLARE _cod0 INT DEFAULT 0;
	DECLARE	_cod1 INT DEFAULT 1;
	DECLARE	_cod2 INT DEFAULT 2;
	
	IF _flag = 1 THEN
	
		SELECT COUNT(*) INTO _filtro FROM tm_mesa WHERE id_salon = _id_salon AND nro_mesa = _nro_mesa;
	
		IF _filtro = 0 THEN
			INSERT INTO tm_mesa (id_salon,nro_mesa) VALUES (_id_salon, _nro_mesa);
			SELECT _cod1 AS cod;
		ELSE
			SELECT _cod0 AS cod;
		END IF;
	
	end if;
	
	IF _flag = 2 THEN
	
		SELECT COUNT(*) INTO _filtro FROM tm_mesa WHERE id_salon = _id_salon AND nro_mesa = _nro_mesa AND estado = _estado;
	
		IF _filtro = 0 THEN
			UPDATE tm_mesa SET nro_mesa = _nro_mesa, estado = _estado WHERE id_mesa = _id_mesa;
			SELECT _cod2 AS cod;
		ELSE
			SELECT _cod2 AS cod;
		END IF;
	
	END IF;
	
	IF _flag = 3 THEN
	
		SELECT count(*) INTO _filtro FROM tm_pedido_mesa WHERE id_mesa = _id_mesa;
	
		IF _filtro = 0 THEN
			DELETE FROM tm_mesa WHERE id_mesa = _id_mesa;
			SELECT _cod1 AS cod;
		ELSE
			SELECT _cod0 AS cod;
		END IF;
	
	END IF;
	END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_configProducto` (IN `_flag` INT(11), IN `_id_prod` INT(11), IN `_id_tipo` INT(11), IN `_id_catg` INT(11), IN `_id_areap` INT(11), IN `_nombre` VARCHAR(45), IN `_notas` VARCHAR(200), IN `_delivery` INT(1), IN `_estado` VARCHAR(1), IN `_combo` VARCHAR(2))  BEGIN
	DECLARE _filtro INT DEFAULT 0;
	DECLARE _cod0 INT DEFAULT 0;
	DECLARE	_cod1 INT DEFAULT 1;
	DECLARE	_cod2 INT DEFAULT 2;
	
	IF _flag = 1 THEN
		SELECT COUNT(*) INTO _filtro FROM tm_producto WHERE id_tipo = _id_tipo AND id_catg = _id_catg AND id_areap = _id_areap AND nombre = _nombre;
		IF _filtro = 0 THEN
			INSERT INTO tm_producto (id_tipo,id_catg,id_areap,nombre,notas,delivery, combo) 
			VALUES ( _id_tipo, _id_catg, _id_areap, _nombre, _notas, _delivery, _combo);
			SELECT _cod1 AS cod;
		else
			SELECT _cod0 AS cod;
		end if;
	end if;
	
	if _flag = 2 then
		SELECT COUNT(*) INTO _filtro FROM tm_producto WHERE id_tipo = _id_tipo AND id_catg = _id_catg AND id_areap = _id_areap AND nombre = _nombre AND notas = _notas AND delivery = _delivery and estado = _estado;
		IF _filtro = 0 THEN
			UPDATE tm_producto SET id_tipo = _id_tipo, id_catg = _id_catg, id_areap = _id_areap, nombre = _nombre, notas = _notas, delivery = _delivery, estado = _estado 
			WHERE id_prod = _id_prod;
			SELECT _cod2 AS cod;
		ELSE
			SELECT _cod2 AS cod;
		END IF;
	end if;
	
	END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_configProductoCatgs` (IN `_flag` INT(11), IN `_id_catg` INT(11), IN `_descripcion` VARCHAR(45), IN `_delivery` INT(1), IN `_orden` INT(11), IN `_imagen` VARCHAR(200), IN `_estado` VARCHAR(1))  BEGIN	
	DECLARE _filtro INT DEFAULT 0;
	DECLARE _cod0 INT DEFAULT 0;
	DECLARE	_cod1 INT DEFAULT 1;
	DECLARE	_cod2 INT DEFAULT 2;
	
	IF _flag = 1 THEN	
		
		SELECT COUNT(*) INTO _filtro FROM tm_producto_catg WHERE descripcion = _descripcion;
		IF _filtro = 0 THEN
			INSERT INTO tm_producto_catg (descripcion,delivery,orden,imagen,estado) VALUES (_descripcion,_delivery,100,_imagen,_estado);
			SELECT _cod1 AS cod;
		ELSE
			SELECT _cod0 AS cod;
		END IF;
	end if;
		
	IF _flag = 2 THEN
		SELECT COUNT(*) INTO _filtro FROM tm_producto_catg WHERE descripcion = _descripcion and delivery = _delivery and orden = _orden AND imagen = _imagen AND estado = _estado;
		IF _filtro = 0 THEN
			UPDATE tm_producto_catg SET descripcion = _descripcion, delivery = _delivery, orden =_orden, imagen = _imagen, estado = _estado WHERE id_catg = _id_catg;
			SELECT _cod2 AS cod;
		ELSE
			SELECT _cod2 AS cod;
		END IF;
	END IF;
	
	END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_configProductoIngrs` (IN `_flag` INT(11), IN `_id_pi` INT(11), IN `_id_pres` INT(11), IN `_id_tipo_ins` INT(11), IN `_id_ins` INT(11), IN `_id_med` INT(11), IN `_cant` FLOAT)  BEGIN
	if _flag = 1 then
		INSERT INTO tm_producto_ingr (id_pres,id_tipo_ins,id_ins,id_med,cant) VALUES (_id_pres, _id_tipo_ins, _id_ins, _id_med, _cant);
	end if;
	if _flag = 2 then
		UPDATE tm_producto_ingr SET cant = _cant WHERE id_pi = _id_pi;
	end if;
	if _flag = 3 then
		DELETE FROM tm_producto_ingr WHERE id_pi = _id_pi;
	end if;
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_configProductoPres` (IN `_flag` INT(11), IN `_id_pres` INT(11), IN `_id_prod` INT(11), IN `_cod_prod` VARCHAR(45), IN `_presentacion` VARCHAR(45), IN `_descripcion` VARCHAR(200), IN `_precio` DECIMAL(10,2), IN `_precio_delivery` DECIMAL(10,2), IN `_receta` INT(1), IN `_stock_min` INT(11), IN `_impuesto` INT(1), IN `_delivery` INT(1), IN `_margen` INT(1), IN `_igv` DECIMAL(10,2), IN `_imagen` VARCHAR(200), IN `_estado` VARCHAR(1))  BEGIN
		
	DECLARE _cont INT DEFAULT 0;
	DECLARE _cod0 INT DEFAULT 0;
	DECLARE	_cod1 INT DEFAULT 1;
	DECLARE	_cod2 INT DEFAULT 2;
	IF _flag = 1 THEN
	
		SELECT COUNT(*) INTO _cont FROM tm_producto_pres WHERE presentacion = _presentacion AND id_prod = _id_prod;
		
		IF _cont = 0 THEN
			INSERT INTO tm_producto_pres (id_prod,cod_prod,presentacion,descripcion,precio,precio_delivery,receta,stock_min,impuesto,delivery,margen,igv,imagen,estado) 
			VALUES (_id_prod, _cod_prod, _presentacion, _descripcion, _precio, _precio_delivery, _receta, _stock_min, _impuesto, _delivery, _margen, _igv, _imagen, _estado);
			SELECT _cod1 AS cod;
		ELSE
			SELECT _cod0 AS cod;
		END IF;
		
	end if;
	
	IF _flag = 2 THEN
	
		UPDATE tm_producto_pres SET cod_prod = _cod_prod, presentacion = _presentacion, descripcion = _descripcion, precio = _precio, precio_delivery = _precio_delivery, receta = _receta, stock_min = _stock_min, impuesto = _impuesto, delivery = _delivery, margen = _margen, igv = _igv, imagen = _imagen, estado = _estado 
		WHERE id_pres = _id_pres;
		SELECT _cod2 AS cod;
		
	END IF;
	END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_configRol` (IN `_flag` INT(11), IN `_desc` VARCHAR(45), IN `_idRol` INT(11))  BEGIN
		DECLARE _duplicado INT DEFAULT 1;
		
		IF _flag = 1 THEN
		
				SELECT count(*) INTO _duplicado FROM tm_rol WHERE descripcion = _desc;
			
			IF _duplicado = 0 THEN
				INSERT INTO tm_rol (descripcion) VALUES (_desc);
				
				SELECT _duplicado AS dup;
			ELSE
				SELECT _duplicado AS dup;
			END IF;
		
		end if;
		
		IF _flag = 2 THEN
		
				SELECT COUNT(*) INTO _duplicado FROM tm_rol WHERE descripcion = _desc;
			
			IF _duplicado = 0 THEN
				UPDATE tm_rol SET descripcion = _desc WHERE id_rol = _idRol;
				
				SELECT _duplicado AS dup;
			ELSE
				SELECT _duplicado AS dup;
			END IF;
		
		END IF;
	END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_configSalones` (IN `_flag` INT(11), IN `_id_salon` INT(11), IN `_descripcion` VARCHAR(45), IN `_estado` VARCHAR(5))  BEGIN
	DECLARE _filtro INT DEFAULT 0;
	DECLARE _filtro2 INT DEFAULT 0;
	DECLARE _cod0 INT DEFAULT 0;
	DECLARE	_cod1 INT DEFAULT 1;
	DECLARE	_cod2 INT DEFAULT 2;
	
	IF _flag = 1 THEN
	
		SELECT COUNT(*) INTO _filtro FROM tm_salon WHERE descripcion = _descripcion AND estado = _estado;
	
		IF _filtro = 0 THEN
			INSERT INTO tm_salon (descripcion,estado) VALUES (_descripcion,_estado);
			SELECT _cod1 AS cod;
		ELSE
			SELECT _cod0 AS cod;
		END IF;
	
	end if;
	
	IF _flag = 2 THEN
	
		SELECT COUNT(*) INTO _filtro FROM tm_salon WHERE descripcion = _descripcion AND estado = _estado;
	
		IF _filtro = 0 THEN
			UPDATE tm_salon SET descripcion = _descripcion, estado = _estado WHERE id_salon = _id_salon;
			SELECT _cod2 AS cod;
		ELSE
			SELECT _cod2 AS cod;
		END IF;
	
	END IF;
	
	IF _flag = 3 THEN
	
		SELECT count(*) INTO _filtro FROM tm_mesa WHERE id_salon = _id_salon;
	
		IF _filtro = 0 THEn
			
			SELECT COUNT(*) AS _filtro2 FROM tm_salon;
			
			if _filtro2 = 1 then
			
				DELETE FROM tm_salon WHERE id_salon = _id_salon;
				ALTER TABLE tm_salon AUTO_INCREMENT = 1;
				SELECT _cod1 AS cod;
			
			else 
		
				DELETE FROM tm_salon WHERE id_salon = _id_salon;
				SELECT _cod1 AS cod;
	
			end if;		
			
		ELSE
			SELECT _cod0 AS cod;
		END IF;
	
	END IF;
	END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_configUsuario` (IN `_flag` INT(11), IN `_id_usu` INT(11), IN `_id_rol` INT(11), IN `_id_areap` INT(11), IN `_id_mesa` INT(11), IN `_dni` VARCHAR(10), IN `_ape_paterno` VARCHAR(45), IN `_ape_materno` VARCHAR(45), IN `_nombres` VARCHAR(45), IN `_email` VARCHAR(100), IN `_usuario` VARCHAR(45), IN `_contrasena` VARCHAR(45), IN `_imagen` VARCHAR(45))  BEGIN
		DECLARE _filtro INT DEFAULT 1;
		
		IF _flag = 1 THEN
		
			SELECT count(*) INTO _filtro FROM tm_usuario WHERE usuario = _usuario;
		
			IF _filtro = 0 THEN
			
				INSERT INTO tm_usuario (id_rol,id_areap,id_mesa,dni,ape_paterno,ape_materno,nombres,email,usuario,contrasena,imagen) 
				VALUES (_id_rol,_id_areap,_id_mesa,_dni,_ape_paterno,_ape_materno,_nombres,_email,_usuario,_contrasena,_imagen);
				
				SELECT _filtro AS cod;
			ELSE
				SELECT _filtro AS cod;
			END IF;
		
		end if;
		
		IF _flag = 2 THEN
			UPDATE tm_usuario SET id_rol = _id_rol, id_areap = _id_areap,id_mesa = _id_mesa, dni = _dni, ape_paterno = _ape_paterno, ape_materno = _ape_materno, nombres = _nombres, email = _email, usuario = _usuario, contrasena = _contrasena, imagen = _imagen
			WHERE id_usu = _id_usu;
		END IF;
	END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_invESAnular` (IN `_flag` INT(11), IN `_id_es` INT(11), IN `_id_tipo` INT(11))  BEGIN
	DECLARE _filtro INT DEFAULT 0;
	IF _flag = 1 THEN
	
		SELECT COUNT(*) INTO _filtro FROM tm_inventario_entsal WHERE estado = 'a' AND id_es = _id_es;
		
		IF _filtro = 1 THEN
			UPDATE tm_inventario_entsal SET estado = 'i' WHERE id_es = _id_es;
			UPDATE tm_inventario SET estado = 'i' WHERE id_tipo_ope = _id_tipo AND id_ope = _id_es;
			SELECT _filtro AS cod;
		ELSE
			SELECT _filtro AS cod;
		END IF;
	END IF;
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_optPedidos` (IN `_flag` INT(11))  BEGIN
	DECLARE _cont INT DEFAULT 0;
	DECLARE _cod0 INT DEFAULT 0;
	DECLARE	_cod1 INT DEFAULT 1;
	
	IF _flag = 1 THEN
	
		SELECT COUNT(*) FROM tm_aper_cierre WHERE estado = 'a';
		
		IF _cont = 0 THEN
			DELETE FROM tm_detalle_pedido;
			UPDATE tm_pedido SET estado = 'z' WHERE estado = 'a';
			/*mostrador*/
			UPDATE tm_pedido SET estado = 'd' WHERE estado = 'b' AND id_tipo_pedido = 2;
			/*delivery*/
			UPDATE tm_pedido SET estado = 'd' WHERE estado = 'c' AND id_tipo_pedido = 3;
			UPDATE tm_pedido SET estado = 'z' WHERE estado = 'b' AND id_tipo_pedido = 3;
			UPDATE tm_mesa SET estado = 'a';
			SELECT _cod1 AS cod;
		ELSE
			SELECT _cod0 AS cod;
		END IF;
	
	END IF;
	
	IF _flag = 2 THEN
	
		DELETE FROM tm_detalle_pedido;
		DELETE FROM tm_pedido_mesa;
		DELETE FROM tm_pedido_llevar;
		DELETE FROM tm_pedido_delivery;
		DELETE FROM tm_pedido;
		ALTER TABLE tm_pedido AUTO_INCREMENT = 1;
		DELETE FROM tm_compra_detalle;
		DELETE FROM tm_credito_detalle;
		DELETE FROM tm_compra_credito;
		ALTER TABLE tm_compra_credito AUTO_INCREMENT = 1;
		DELETE FROM tm_compra;
		ALTER TABLE tm_compra AUTO_INCREMENT = 1;
		DELETE FROM tm_gastos_adm;
		ALTER TABLE tm_gastos_adm AUTO_INCREMENT = 1;
		DELETE FROM tm_ingresos_adm;
		ALTER TABLE tm_ingresos_adm AUTO_INCREMENT = 1;
		DELETE FROM tm_detalle_venta;
		DELETE FROM comunicacion_baja;
		ALTER TABLE comunicacion_baja AUTO_INCREMENT = 1;
		DELETE FROM resumen_diario_detalle;
		ALTER TABLE resumen_diario_detalle AUTO_INCREMENT = 1;
		DELETE FROM resumen_diario;
		ALTER TABLE resumen_diario AUTO_INCREMENT = 1;			
		DELETE FROM tm_venta;
		ALTER TABLE tm_venta AUTO_INCREMENT = 1;
		DELETE FROM tm_aper_cierre;
		ALTER TABLE tm_aper_cierre AUTO_INCREMENT = 1;
		DELETE FROM tm_inventario_entsal;
		ALTER TABLE tm_inventario_entsal AUTO_INCREMENT = 1;
		DELETE FROM tm_inventario;
		ALTER TABLE tm_inventario AUTO_INCREMENT = 1;
		UPDATE tm_mesa SET estado = 'a' WHERE estado <> 'm';
		SELECT _cod1 AS cod;
		
	END IF;
	
	IF _flag = 3 THEN
	
		SELECT COUNT(*) INTO _cont FROM tm_detalle_venta;
		
		IF _cont = 0 THEN
			DELETE FROM tm_producto_ingr;
			ALTER TABLE tm_producto_ingr AUTO_INCREMENT = 1;
			DELETE FROM tm_producto_pres;
			ALTER TABLE tm_producto_pres AUTO_INCREMENT = 1;
			DELETE FROM tm_producto;
			ALTER TABLE tm_producto AUTO_INCREMENT = 1;
			DELETE FROM tm_producto_catg WHERE id_catg <> 1;
			ALTER TABLE tm_producto_catg AUTO_INCREMENT = 1;
			SELECT _cod1 AS cod;
		ELSE
			SELECT _cod0 AS cod;
		END IF;
		
	END IF;
	
	IF _flag = 4 THEN
	
		SELECT COUNT(*) INTO _cont FROM tm_producto_ingr;
		
		IF _cont = 0 THEN
			DELETE FROM tm_insumo;
			ALTER TABLE tm_insumo AUTO_INCREMENT = 1;
			DELETE FROM tm_insumo_catg;
			ALTER TABLE tm_insumo_catg AUTO_INCREMENT = 1;
			SELECT _cod1 AS cod;
		ELSE
			SELECT _cod0 AS cod;
		END IF;
		
	END IF;
	
	IF _flag = 5 THEN
	
		DELETE FROM tm_cliente where id_cliente <> 1;
		ALTER TABLE tm_cliente AUTO_INCREMENT = 2;
		SELECT _cod1 AS cod;
		
	END IF;
	
	IF _flag = 6 THEN
	
		DELETE FROM tm_proveedor;
		ALTER TABLE tm_proveedor AUTO_INCREMENT = 1;
		SELECT _cod1 AS cod;
		
	END IF;
	
	IF _flag = 7 THEN
	
		DELETE FROM tm_mesa;
		ALTER TABLE tm_mesa AUTO_INCREMENT = 1;
		DELETE FROM tm_salon;
		ALTER TABLE tm_salon AUTO_INCREMENT = 1;
		SELECT _cod1 AS cod;
		
	END IF;
			
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_restCancelarPedido` (IN `_flag` INT(11), IN `_id_usu` INT(11), IN `_id_pres` INT(11), IN `_id_pedido` INT(11), IN `_estado_pedido` VARCHAR(5), IN `_fecha_pedido` DATETIME, IN `_fecha_envio` DATETIME, IN `_codigo_seguridad` VARCHAR(50), IN `_filtro_seguridad` VARCHAR(50))  BEGIN
	DECLARE _filtro INT DEFAULT 0;
	DECLARE _cod0 INT DEFAULT 0;
	DECLARE	_cod1 INT DEFAULT 1;
	DECLARE	_cod2 INT DEFAULT 2;
	
	IF _flag = 1 THEN
		/*
		SELECT COUNT(*) INTO _filtro FROM tm_detalle_pedido WHERE id_pedido = _id_pedido AND id_pres = _id_pres AND fecha_pedido = _fecha_pedido AND (_estado_pedido = 'a' OR _estado_pedido = 'y');
		*/
		iF _estado_pedido = 'a' or _estado_pedido = 'y' THEN		
			if _codigo_seguridad = _filtro_seguridad then
				UPDATE tm_detalle_pedido SET estado = 'z', id_usu = _id_usu, fecha_envio = _fecha_envio WHERE id_pedido = _id_pedido AND id_pres = _id_pres AND fecha_pedido = _fecha_pedido AND estado = _estado_pedido LIMIT 1;
				SELECT _cod1 AS cod;			
			else
				SELECT _cod0 AS cod;
			end if;			
		ELSE
			SELECT _cod2 AS cod;
		END IF;	
	END IF;
	
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_restDesocuparMesa` (`_flag` INT(11), `_id_pedido` INT(11))  BEGIN
	DECLARE result INT DEFAULT 1;
	IF _flag = 1 THEN
		SELECT id_mesa INTO @codmesa FROM tm_pedido_mesa WHERE id_pedido = _id_pedido;
		UPDATE tm_mesa SET estado = 'a' WHERE id_mesa = @codmesa;
		UPDATE tm_pedido SET estado = 'z' WHERE id_pedido = _id_pedido;
		SELECT result AS resultado;
	END IF;
END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_restEditarVentaDocumento` (`_flag` INT(11), `_id_venta` INT(11), `_id_cliente` INT(11), `_id_tipo_documento` INT(11))  BEGIN
	DECLARE _cod INT DEFAULT 1;
	
	IF _flag = 1 THEN
		SELECT td.serie,CONCAT(LPAD(COUNT(id_venta)+(td.numero),8,'0')) AS numero INTO @serie, @numero
		FROM tm_venta AS v INNER JOIN tm_tipo_doc AS td ON v.id_tipo_doc = td.id_tipo_doc
		WHERE v.id_tipo_doc = _id_tipo_documento AND v.serie_doc = td.serie;
		UPDATE tm_venta SET id_cliente = _id_cliente, id_tipo_doc = _id_tipo_documento, serie_doc = @serie, nro_doc = @numero WHERE id_venta = _id_venta;
	END IF;
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_restEmitirVenta` (IN `_flag` INT(11), IN `_dividir_cuenta` INT(11), IN `_id_pedido` INT(11), IN `_tipo_pedido` INT(11), IN `_tipo_entrega` VARCHAR(1), IN `_id_cliente` INT(11), IN `_id_tipo_doc` INT(11), IN `_id_tipo_pago` INT(11), IN `_id_usu` INT(11), IN `_id_apc` INT(11), IN `_pago_efe_none` DECIMAL(10,2), IN `_pago_tar` DECIMAL(10,2), IN `_descuento_tipo` CHAR(1), IN `_descuento_personal` INT(11), IN `_descuento_monto` DECIMAL(10,2), IN `_descuento_motivo` VARCHAR(200), IN `_comision_tarjeta` DECIMAL(10,2), IN `_comision_delivery` DECIMAL(10,2), IN `_igv` DECIMAL(10,2), IN `_total` DECIMAL(10,2), IN `_codigo_operacion` VARCHAR(20), IN `_fecha_venta` DATETIME)  BEGIN
	DECLARE pago_efe DECIMAL(10,2) DEFAULT 0;
	DECLARE pago_tar DECIMAL(10,2) DEFAULT 0;
	
	if (_descuento_tipo = 1 or _descuento_tipo = 3) then
		SET pago_efe = 0;
		SET pago_tar = 0;
	else 
		IF _id_tipo_pago = 1 THEN
			SET pago_efe = ( _total + _comision_delivery - _descuento_monto);
			SET pago_tar = 0;
		ELSEIF _id_tipo_pago = 2 THEN
			SET pago_efe = 0;
			SET pago_tar = ( _total + _comision_delivery - _descuento_monto);
		ELSEIF _id_tipo_pago = 3 THEN
			SET pago_efe = ( _total + _comision_delivery - _descuento_monto) - _pago_tar;
			SET pago_tar = _pago_tar;
		ELSE
			SET pago_efe = 0;
			SET pago_tar = ( _total + _comision_delivery - _descuento_monto);
		END IF;
	end if;
	
	IF _flag = 1 THEN
	
		SELECT td.serie,CONCAT(LPAD(COUNT(id_venta)+(td.numero),8,'0')) AS numero INTO @serie, @numero
		FROM tm_venta AS v INNER JOIN tm_tipo_doc AS td ON v.id_tipo_doc = td.id_tipo_doc
		WHERE v.id_tipo_doc = _id_tipo_doc AND v.serie_doc = td.serie;
		INSERT INTO tm_venta (id_pedido, id_tipo_pedido, id_cliente, id_tipo_doc, id_tipo_pago, id_usu, id_apc, serie_doc, nro_doc, pago_efe, pago_efe_none, pago_tar, descuento_tipo, descuento_personal, descuento_monto, descuento_motivo, comision_tarjeta, comision_delivery, igv, total, codigo_operacion, fecha_venta)
		VALUES (_id_pedido, _tipo_pedido, _id_cliente, _id_tipo_doc, _id_tipo_pago,_id_usu,_id_apc, @serie,@numero, pago_efe, _pago_efe_none, pago_tar, _descuento_tipo, _descuento_personal, _descuento_monto, _descuento_motivo, _comision_tarjeta, _comision_delivery, _igv, _total, _codigo_operacion, _fecha_venta );
		
		SELECT @@IDENTITY INTO @id;
		
		/* DIVIDIR CUENTA 1 = FALSE, 2 = TRUE */
		IF _dividir_cuenta = 1 THEN
		
			IF _tipo_pedido = 1 THEN	
				SELECT id_mesa INTO @idMesa FROM tm_pedido_mesa WHERE id_pedido = _id_pedido;
				UPDATE tm_mesa SET estado = 'a' WHERE id_mesa = @idMesa;
				UPDATE tm_pedido SET estado = 'd' WHERE id_pedido = _id_pedido;
			elseIF _tipo_pedido = 2 then
				UPDATE tm_pedido SET estado = 'b' WHERE id_pedido = _id_pedido;
				UPDATE tm_pedido_llevar SET fecha_entrega = _fecha_venta WHERE id_pedido = _id_pedido;
			ELSEIF _tipo_pedido = 3 THEN
			
				UPDATE tm_pedido SET id_apc = _id_apc, id_usu = _id_usu, estado = _tipo_entrega WHERE id_pedido = _id_pedido;
				
				if _tipo_entrega = 'c' then
					UPDATE tm_pedido_delivery SET fecha_envio = _fecha_venta WHERE id_pedido = _id_pedido;
				elseif _tipo_entrega = 'd' then
					UPDATE tm_pedido_delivery SET fecha_entrega = _fecha_venta WHERE id_pedido = _id_pedido;
				end if;
			END IF;
			    if(_tipo_pedido = 4) THEN
                UPDATE tm_pedido SET estado = 'd' WHERE id_pedido = _id_pedido;
                end if;
		END IF;
			
		SELECT @id AS id_venta;
			
	END IF;
	
	END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_restEmitirVentaDet` (`_flag` INT(11), `_id_venta` INT(11), `_id_pedido` INT(11), `_fecha` DATETIME)  BEGIN
    
	DECLARE _idprod INT; 
	DECLARE _cantidad1 INT;
	DECLARE _precio1 FLOAT;
	DECLARE _receta INT;
	DECLARE _tipopedido INT;
	DECLARE done INT DEFAULT 0;
	DECLARE primera CURSOR FOR SELECT dv.id_prod, SUM(dv.cantidad) AS cantidad, dv.precio, pp.receta, p.id_tipo FROM tm_detalle_venta AS dv INNER JOIN tm_producto_pres AS pp
	ON dv.id_prod = pp.id_pres LEFT JOIN tm_producto AS p ON pp.id_prod = p.id_prod WHERE dv.id_venta = _id_venta GROUP BY dv.id_prod;
	DECLARE segunda CURSOR FOR SELECT i.id_tipo_ins,i.id_ins,i.cant,v.ins_cos FROM tm_producto_ingr AS i INNER JOIN v_insprod AS v ON i.id_ins = v.id_ins AND i.id_tipo_ins = v.id_tipo_ins WHERE i.id_pres = _idprod;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
	
	OPEN primera;
	REPEAT
	
	FETCH primera INTO _idprod, _cantidad1, _precio1, _receta, _tipopedido;
	IF NOT done THEN
			
		UPDATE tm_detalle_pedido SET cantidad = (cantidad - _cantidad1) WHERE id_pedido = _id_pedido AND id_pres = _idprod AND estado <> 'i' LIMIT 1;
	
		IF _receta = 1 THEN
			
			IF _tipopedido = 2 THEN
				
				INSERT INTO tm_inventario (id_tipo_ope,id_ope,id_tipo_ins,id_ins,cos_uni,cant,fecha_r) VALUES (2,_id_venta,2,_idprod,_precio1,_cantidad1,_fecha);
			
			ELSEIF _tipopedido = 1 THEN
				
				block2: BEGIN
				
						DECLARE donesegunda INT DEFAULT 0;
						DECLARE _tipoinsumo2 INT;
						DECLARE _idinsumo2 INT;
						DECLARE xx FLOAT;
						DECLARE _cantidad2 FLOAT;
						DECLARE _precio2 FLOAT;
						DECLARE tercera CURSOR FOR SELECT i.id_tipo_ins,i.id_ins,i.cant,v.ins_cos FROM tm_producto_ingr AS i INNER JOIN v_insprod AS v ON i.id_ins = v.id_ins AND i.id_tipo_ins = v.id_tipo_ins WHERE i.id_pres = _idinsumo2;
						DECLARE CONTINUE HANDLER FOR NOT FOUND SET donesegunda = 1;
					
					OPEN segunda;
					REPEAT
			
					FETCH segunda INTO _tipoinsumo2,_idinsumo2,_cantidad2, _precio2;
						IF NOT donesegunda THEN
						
							IF _tipoinsumo2 = 1 OR _tipoinsumo2 = 2 THEN
							
								SET xx = _cantidad2 * _cantidad1;
								INSERT INTO tm_inventario (id_tipo_ope,id_ope,id_tipo_ins,id_ins,cos_uni,cant,fecha_r) VALUES (2,_id_venta,_tipoinsumo2,_idinsumo2,_precio2,xx,_fecha);
							
							ELSEIF _tipoinsumo2 = 3 then
							
								block3: BEGIN
										DECLARE donetercera INT DEFAULT 0;
										DECLARE _tipoinsumo3 INT;
										DECLARE _idinsumo3 INT;
										DECLARE yy FLOAT;
										DECLARE _cantidad3 FLOAT;
										DECLARE _precio3 FLOAT;
										DECLARE CONTINUE HANDLER FOR NOT FOUND SET donetercera = 1;
							
									OPEN tercera;
									REPEAT
							
									FETCH tercera INTO _tipoinsumo3,_idinsumo3,_cantidad3,_precio3;
										IF NOT donetercera THEN
											
										SET yy = _cantidad1 * _cantidad2 * _cantidad3;
										INSERT INTO tm_inventario (id_tipo_ope,id_ope,id_tipo_ins,id_ins,cos_uni,cant,fecha_r) VALUES (2,_id_venta,_tipoinsumo3,_idinsumo3,_precio3,yy,_fecha);
									
										END IF;
									UNTIL donetercera END REPEAT;
									CLOSE tercera;
									
								END block3;
								
							end if;
							
						END IF;
							
					UNTIL donesegunda END REPEAT;
					CLOSE segunda;
					
				END block2;
				
			END IF;
		END IF;	
	END IF;
	UNTIL done END REPEAT;
	CLOSE primera;
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_restOpcionesMesa` (IN `_flag` INT(11), IN `_cod_mesa_origen` INT(11), IN `_cod_mesa_destino` INT(11))  BEGIN
	DECLARE _filtro INT DEFAULT 0;
	if _flag = 1 then
			
			SELECT COUNT(*) INTO _filtro FROM tm_mesa WHERE id_mesa = _cod_mesa_origen AND estado = 'i';
		
		if _filtro = 1 then 
			SELECT id_pedido INTO @cod FROM v_listar_mesas WHERE id_mesa = _cod_mesa_origen;
			UPDATE tm_mesa SET estado = 'a' WHERE id_mesa = _cod_mesa_origen;
			UPDATE tm_mesa SET estado = 'i' WHERE id_mesa = _cod_mesa_destino;
			UPDATE tm_pedido_mesa SET id_mesa = _cod_mesa_destino WHERE id_pedido = @cod;
			
			SELECT _filtro AS cod;
		ELSE
			SELECT _filtro AS cod;
		end if;
	end if;
	
	IF _flag = 2 THEN
			
			SELECT COUNT(*) INTO _filtro FROM tm_mesa WHERE id_mesa = _cod_mesa_origen AND estado = 'i';
		
		IF _filtro = 1 THEN 
			SELECT id_pedido INTO @cod_1 FROM v_listar_mesas WHERE id_mesa = _cod_mesa_origen;
			SELECT id_pedido INTO @cod_2 FROM v_listar_mesas WHERE id_mesa = _cod_mesa_destino;
			UPDATE tm_detalle_pedido SET id_pedido = @cod_2 WHERE id_pedido = @cod_1;
			
				if _cod_mesa_origen = _cod_mesa_destino then
					UPDATE tm_mesa SET estado = 'i' WHERE id_mesa = _cod_mesa_origen;
				else
					UPDATE tm_mesa SET estado = 'a' WHERE id_mesa = _cod_mesa_origen;
					UPDATE tm_pedido SET estado = 'z' WHERE id_pedido = @cod_1;
				end if;
			
			SELECT _filtro AS cod;
		ELSE
			SELECT _filtro AS cod;
		END IF;
	END IF;
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_restRegCliente` (IN `_flag` INT(11), IN `_id_cliente` INT(11), IN `_tipo_cliente` INT(11), IN `_dni` VARCHAR(10), IN `_ruc` VARCHAR(13), IN `_nombres` VARCHAR(200), IN `_razon_social` VARCHAR(100), IN `_telefono` INT(11), IN `_fecha_nac` DATE, IN `_correo` VARCHAR(100), IN `_direccion` VARCHAR(100), IN `_referencia` VARCHAR(100))  BEGIN
	DECLARE _filtro INT DEFAULT 1;
	DECLARE _numero_documento INT DEFAULT 0;
	
	IF _flag = 1 THEN
	
		IF _tipo_cliente = 1 THEN
			SELECT COUNT(*) INTO _filtro FROM tm_cliente WHERE dni = _dni;
			SET _numero_documento = _dni;
		ELSEIF _tipo_cliente = 2 THEN
			SELECT COUNT(*) INTO _filtro FROM tm_cliente WHERE ruc = _ruc;
			SET _numero_documento = '2';
		END IF;
	
		IF _filtro = 0 OR _numero_documento = '00000000' THEN
		
			INSERT INTO tm_cliente (tipo_cliente,dni,ruc,nombres,razon_social,telefono,fecha_nac,correo,direccion,referencia) 
			VALUES (_tipo_cliente, _dni, _ruc, _nombres, _razon_social, _telefono, _fecha_nac, _correo, _direccion, _referencia);
			
			SELECT @@IDENTITY INTO @id;
			
			SELECT _filtro AS cod,@id AS id_cliente;
		ELSE
			SELECT _filtro AS cod;
		END IF;
	END IF;
	
	IF _flag = 2 THEN
	
		UPDATE tm_cliente SET tipo_cliente = _tipo_cliente, dni = _dni, ruc = _ruc, nombres = _nombres, 
		razon_social = _razon_social, telefono = _telefono, fecha_nac = _fecha_nac, correo = _correo, direccion = _direccion, referencia = _referencia
		WHERE id_cliente = _id_cliente;
		
		SELECT _id_cliente AS id_cliente;
		
	END IF;
END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_restRegDelivery` (IN `_flag` INT(11), IN `_tipo_canal` INT(11), IN `_id_tipo_pedido` INT(11), IN `_id_apc` INT(11), IN `_id_usu` INT(11), IN `_fecha_pedido` DATETIME, IN `_id_cliente` INT(11), IN `_id_repartidor` INT(11), IN `_tipo_entrega` INT(11), IN `_tipo_pago` INT(11), IN `_pedido_programado` INT(11), IN `_hora_entrega` TIME, IN `_nombre_cliente` VARCHAR(100), IN `_telefono_cliente` VARCHAR(20), IN `_direccion_cliente` VARCHAR(100), IN `_referencia_cliente` VARCHAR(100), IN `_email_cliente` VARCHAR(200))  BEGIN
	DECLARE _filtro INT DEFAULT 1;
	
	IF _flag = 1 THEN
		
		INSERT INTO tm_pedido (id_tipo_pedido,id_apc,id_usu,fecha_pedido) VALUES (_id_tipo_pedido, _id_apc, _id_usu, _fecha_pedido);
		
		SELECT @@IDENTITY INTO @id;
		
		SELECT CONCAT(LPAD(count(t.nro_pedido)+1,5,'0')) AS codigo INTO @nro_pedido FROM tm_pedido_delivery AS t INNER JOIN tm_pedido AS p ON t.id_pedido = p.id_pedido WHERE p.id_tipo_pedido = 3 AND p.estado <> 'z'; 
		
			IF _id_cliente = 1 THEN
				INSERT INTO tm_cliente (tipo_cliente,nombres,telefono,direccion,referencia) VALUES (1,_nombre_cliente,_telefono_cliente,_direccion_cliente,_referencia_cliente);
				SELECT @@IDENTITY INTO @id_cliente;
				INSERT INTO tm_pedido_delivery (id_pedido,tipo_canal,id_cliente,id_repartidor,tipo_entrega,tipo_pago,pedido_programado,hora_entrega,nro_pedido,nombre_cliente,telefono_cliente,direccion_cliente,referencia_cliente,email_cliente) VALUES (@id, _tipo_canal, @id_cliente, _id_repartidor, _tipo_entrega, _tipo_pago, _pedido_programado, _hora_entrega, @nro_pedido, _nombre_cliente, _telefono_cliente, _direccion_cliente, _referencia_cliente, _email_cliente);
			ELSE
				UPDATE tm_cliente SET nombres = _nombre_cliente, telefono = _telefono_cliente, direccion = _direccion_cliente, referencia = _referencia_cliente WHERE id_cliente = _id_cliente; 		
				INSERT INTO tm_pedido_delivery (id_pedido,tipo_canal,id_cliente,id_repartidor,tipo_entrega,tipo_pago,pedido_programado,hora_entrega,nro_pedido,nombre_cliente,telefono_cliente,direccion_cliente,referencia_cliente,email_cliente) VALUES (@id, _tipo_canal, _id_cliente, _id_repartidor, _tipo_entrega, _tipo_pago, _pedido_programado, _hora_entrega, @nro_pedido, _nombre_cliente, _telefono_cliente, _direccion_cliente, _referencia_cliente, _email_cliente);
			END IF;
			
		SELECT _filtro AS fil, @id AS id_pedido;
	
	END IF;
    END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_restRegMesa` (IN `_flag` INT(11), IN `_id_tipo_pedido` INT(11), IN `_id_apc` INT(11), IN `_id_usu` INT(11), IN `_fecha_pedido` DATETIME, IN `_id_mesa` INT(11), IN `_id_mozo` INT(11), IN `_nomb_cliente` VARCHAR(45), IN `_nro_personas` INT(11))  BEGIN
	DECLARE _filtro INT DEFAULT 0;
	
		IF _flag = 1 THEN
		
			SELECT COUNT(*) INTO _filtro FROM tm_mesa WHERE id_mesa = _id_mesa AND estado = 'a';
			
			if _filtro = 1 THEN
				
				INSERT INTO tm_pedido (id_tipo_pedido,id_apc,id_usu,fecha_pedido) VALUES (_id_tipo_pedido, _id_apc, _id_usu, _fecha_pedido);
				
				SELECT @@IDENTITY INTO @id;
				
				INSERT INTO tm_pedido_mesa (id_pedido,id_mesa,id_mozo,nomb_cliente,nro_personas) VALUES (@id, _id_mesa, _id_mozo, _nomb_cliente, _nro_personas);
				
				SELECT _filtro AS fil, @id AS id_pedido;
				
				UPDATE tm_mesa SET estado = 'i' WHERE id_mesa = _id_mesa;
			ELSE
				SELECT _filtro AS fil;
			END IF;
		END IF;
	END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_restRegMostrador` (IN `_flag` INT(11), IN `_id_tipo_pedido` INT(11), IN `_id_apc` INT(11), IN `_id_usu` INT(11), IN `_fecha_pedido` DATETIME, IN `_nomb_cliente` VARCHAR(45))  BEGIN
	DECLARE _filtro INT DEFAULT 1;
	
	IF _flag = 1 THEN
		
		INSERT INTO tm_pedido (id_tipo_pedido,id_apc,id_usu,fecha_pedido) VALUES (_id_tipo_pedido, _id_apc, _id_usu, _fecha_pedido);
		
		SELECT @@IDENTITY INTO @id;
		
		SELECT CONCAT(LPAD(count(t.nro_pedido)+1,5,'0')) AS codigo INTO @nro_pedido FROM tm_pedido_llevar AS t INNER JOIN tm_pedido AS p ON t.id_pedido = p.id_pedido WHERE p.id_tipo_pedido = 2 and p.estado <> 'z'; 
		
		INSERT INTO tm_pedido_llevar (id_pedido,nro_pedido,nomb_cliente) VALUES (@id, @nro_pedido, _nomb_cliente);
		
		SELECT _filtro AS fil, @id AS id_pedido;
	
	END IF;
	END$$

CREATE DEFINER=`u407783947_restobar`@`127.0.0.1` PROCEDURE `usp_tableroControl` (IN `_flag` INT(11), IN `_codDia` INT(11), IN `_fecha` DATE, IN `_feSei` DATE, IN `_feCin` DATE, IN `_feCua` DATE, IN `_feTre` DATE, IN `_feDos` DATE, IN `_feUno` DATE)  BEGIN
	if _flag = 1 then
				SELECT dia,margen into @dia,@margen FROM tm_margen_venta WHERE cod_dia = _codDia;
				SELECT IFNULL(SUM(total-descuento),0) into @siete FROM tm_venta WHERE DATE(fecha_venta) = _fecha;
				SELECT IFNULL(SUM(total-descuento),0) into @seis FROM tm_venta WHERE DATE(fecha_venta) = _feSei;
				SELECT IFNULL(SUM(total-descuento),0) into @cinco FROM tm_venta WHERE DATE(fecha_venta) = _feCin;
				SELECT IFNULL(SUM(total-descuento),0) into @cuatro FROM tm_venta WHERE DATE(fecha_venta) = _feCua;
				SELECT IFNULL(SUM(total-descuento),0) into @tres FROM tm_venta WHERE DATE(fecha_venta) = _feTre;
				SELECT IFNULL(SUM(total-descuento),0) into @dos FROM tm_venta WHERE DATE(fecha_venta) = _feDos;
				SELECT IFNULL(SUM(total-descuento),0) into @uno FROM tm_venta WHERE DATE(fecha_venta) = _feUno;
		
		select @dia as dia,@margen as margen,@siete as siete,@seis as seis,@cinco as cinco,@cuatro as cuatro,@tres as tres,@dos as dos,@uno as uno;	
	end if;
    END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunicacion_baja`
--

CREATE TABLE `comunicacion_baja` (
  `id_comunicacion` int(11) NOT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  `fecha_baja` date DEFAULT NULL,
  `fecha_referencia` date DEFAULT NULL,
  `tipo_doc` char(2) DEFAULT NULL,
  `serie_doc` char(4) DEFAULT NULL,
  `num_doc` varchar(8) DEFAULT NULL,
  `nombre_baja` varchar(200) DEFAULT NULL,
  `correlativo` varchar(5) DEFAULT NULL,
  `enviado_sunat` char(1) DEFAULT NULL,
  `hash_cpe` varchar(100) DEFAULT NULL,
  `hash_cdr` varchar(100) DEFAULT NULL,
  `code_respuesta_sunat` varchar(5) DEFAULT NULL,
  `descripcion_sunat_cdr` varchar(300) DEFAULT NULL,
  `name_file_sunat` varchar(80) DEFAULT NULL,
  `estado` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resumen_diario`
--

CREATE TABLE `resumen_diario` (
  `id_resumen` int(11) NOT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  `fecha_resumen` date DEFAULT NULL,
  `fecha_referencia` date DEFAULT NULL,
  `correlativo` varchar(5) CHARACTER SET utf8mb4 DEFAULT NULL,
  `enviado_sunat` char(1) CHARACTER SET utf8mb4 DEFAULT NULL,
  `hash_cpe` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `hash_cdr` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `code_respuesta_sunat` varchar(5) CHARACTER SET utf8mb4 DEFAULT NULL,
  `descripcion_sunat_cdr` varchar(300) CHARACTER SET utf8mb4 DEFAULT NULL,
  `name_file_sunat` varchar(80) CHARACTER SET utf8mb4 DEFAULT NULL,
  `estado` varchar(12) CHARACTER SET utf8mb4 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resumen_diario_detalle`
--

CREATE TABLE `resumen_diario_detalle` (
  `id_detalle` int(11) NOT NULL,
  `id_resumen` int(11) DEFAULT NULL,
  `id_venta` int(11) DEFAULT NULL,
  `status_code` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sa_asistencia_registro`
--

CREATE TABLE `sa_asistencia_registro` (
  `id_registro` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `hora_entrada` datetime NOT NULL,
  `hora_salida` datetime DEFAULT NULL,
  `descuento` decimal(11,2) NOT NULL,
  `bonificacion` decimal(11,2) NOT NULL,
  `foto_entrada` varchar(255) NOT NULL,
  `foto_salida` varchar(255) DEFAULT NULL,
  `latitud` varchar(255) NOT NULL,
  `longitud` varchar(255) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `pago` decimal(11,2) DEFAULT NULL,
  `hrs` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sa_configuracion`
--

CREATE TABLE `sa_configuracion` (
  `id_conf` int(11) NOT NULL,
  `lat` varchar(255) NOT NULL,
  `lon` varchar(255) NOT NULL,
  `radio` varchar(255) NOT NULL,
  `moneda` varchar(20) NOT NULL,
  `id_impresora` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sa_configuracion`
--

INSERT INTO `sa_configuracion` (`id_conf`, `lat`, `lon`, `radio`, `moneda`, `id_impresora`) VALUES
(1, '-12.069585', '-75.210914', '100', 'S/', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sa_usuario_asistencia`
--

CREATE TABLE `sa_usuario_asistencia` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `hora_entrada` time NOT NULL,
  `bonificacion` decimal(11,2) NOT NULL,
  `descuento` decimal(11,2) NOT NULL,
  `pago_p_hora` decimal(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sa_usuario_asistencia`
--

INSERT INTO `sa_usuario_asistencia` (`id`, `id_usuario`, `hora_entrada`, `bonificacion`, `descuento`, `pago_p_hora`) VALUES
(23, 40, '16:00:00', '4.00', '5.00', '2.00'),
(24, 45, '14:41:00', '10.00', '12.00', '12.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_almacen`
--

CREATE TABLE `tm_almacen` (
  `id_alm` int(11) NOT NULL,
  `nombre` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `estado` varchar(5) CHARACTER SET latin1 DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_almacen`
--

INSERT INTO `tm_almacen` (`id_alm`, `nombre`, `estado`) VALUES
(1, 'ABARROTES E INSUMOS', 'a'),
(2, 'BEBIDAS, GASEOSAS Y CERVEZAS', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_aper_cierre`
--

CREATE TABLE `tm_aper_cierre` (
  `id_apc` int(11) NOT NULL,
  `id_usu` int(11) NOT NULL,
  `id_caja` int(11) NOT NULL,
  `id_turno` int(11) NOT NULL,
  `fecha_aper` datetime DEFAULT NULL,
  `monto_aper` decimal(10,2) DEFAULT 0.00,
  `fecha_cierre` datetime DEFAULT NULL,
  `monto_cierre` decimal(10,2) DEFAULT 0.00,
  `monto_sistema` decimal(10,2) DEFAULT 0.00,
  `stock_pollo` varchar(11) NOT NULL DEFAULT '0',
  `estado` varchar(5) DEFAULT 'a',
  `cod_reporte` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_aper_cierre`
--

INSERT INTO `tm_aper_cierre` (`id_apc`, `id_usu`, `id_caja`, `id_turno`, `fecha_aper`, `monto_aper`, `fecha_cierre`, `monto_cierre`, `monto_sistema`, `stock_pollo`, `estado`, `cod_reporte`) VALUES
(1, 50, 6, 1, '2023-03-22 12:19:53', '1000.00', NULL, '0.00', '0.00', '0', 'a', '422032397');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_areas_rel`
--

CREATE TABLE `tm_areas_rel` (
  `id_rel` int(11) NOT NULL,
  `id_usu` int(11) NOT NULL,
  `id_salon` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tm_areas_rel`
--

INSERT INTO `tm_areas_rel` (`id_rel`, `id_usu`, `id_salon`) VALUES
(1, 54, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_area_prod`
--

CREATE TABLE `tm_area_prod` (
  `id_areap` int(11) NOT NULL,
  `id_imp` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `estado` varchar(5) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_area_prod`
--

INSERT INTO `tm_area_prod` (`id_areap`, `id_imp`, `nombre`, `estado`) VALUES
(1, 2, 'COCINA', 'a'),
(2, 3, 'BARRA', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_caja`
--

CREATE TABLE `tm_caja` (
  `id_caja` int(11) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `estado` varchar(5) DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_caja`
--

INSERT INTO `tm_caja` (`id_caja`, `descripcion`, `estado`) VALUES
(6, 'CAJA1', 'a'),
(7, 'MESAS', 'a'),
(8, 'CAJA2', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_cartilla`
--

CREATE TABLE `tm_cartilla` (
  `route` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tm_cartilla`
--

INSERT INTO `tm_cartilla` (`route`) VALUES
('public/pdf/f0f4175afc216a721bbfb51e19f8ecf1.pdf');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_cliente`
--

CREATE TABLE `tm_cliente` (
  `id_cliente` int(11) NOT NULL,
  `tipo_cliente` int(11) NOT NULL,
  `dni` varchar(10) NOT NULL DEFAULT '00000000',
  `ruc` varchar(13) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `razon_social` varchar(100) NOT NULL,
  `telefono` int(11) NOT NULL,
  `fecha_nac` date NOT NULL,
  `correo` varchar(100) NOT NULL,
  `direccion` varchar(100) NOT NULL DEFAULT 'S/DIRECCION',
  `referencia` varchar(100) NOT NULL,
  `estado` varchar(5) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_cliente`
--

INSERT INTO `tm_cliente` (`id_cliente`, `tipo_cliente`, `dni`, `ruc`, `nombres`, `razon_social`, `telefono`, `fecha_nac`, `correo`, `direccion`, `referencia`, `estado`) VALUES
(1, 1, '00000000', '', 'PUBLICO EN GENERAL', '', 0, '0000-00-00', '', 'SIN DIRECCION', '', 'a'),
(2, 1, '46574756', '', 'IVAN PAUL SUASNABAR LOPEZ', '', 0, '1969-12-31', '', 'sin direccion', '', 'a'),
(3, 1, '70399622', '', 'JHASON ESPINOZA NUÑEZ', '', 0, '1969-12-31', '', 'sin direccion', '', 'a'),
(4, 1, '42288331', '', 'ROCIO TITO POZO', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(5, 1, '72150706', '', 'ALCIDES CHULLUNCUY SAMANIEGO', '', 0, '1969-12-31', '', 'J MAREATEGUI', '', 'a'),
(6, 1, '43776629', '', 'ROSANA GUTIERREZ ARAUJO', '', 0, '1969-12-31', '', 'LOS JAZMINES', '', 'a'),
(7, 1, '23274350', '', 'JUAN CARLOS SALAS FLORES', '', 0, '1969-12-31', '', 'xxx', '', 'a'),
(8, 1, '61375023', '', 'WITHNEY DALESHCA LIÑAN RICSE', '', 0, '1969-12-31', '', 'EN TU CORAZON', '', 'a'),
(9, 1, '19868313', '', 'LUIS ALFREDO ACOSTA REYMUNDO', '', 0, '1969-12-31', '', 'sin direecion ', '', 'a'),
(10, 1, '45670374', '', 'GUSTAVO ANDRE ROLDAN RAMOS', '', 0, '1969-12-31', '', 'coop.America MZ Y1 LT. 35 ', '', 'a'),
(11, 1, '08144719', '', 'JORGE SAUL SORIA GONZALES', '', 0, '1969-12-31', '', 'NS', '', 'a'),
(12, 1, '73640299', '', 'PEDRO LUIS FUENTES TUNQUE', '', 0, '1969-12-31', '', 'nd', '', 'a'),
(13, 1, '48177494', '', 'CRISTHIAN BLIMER LEON QUINCHO', '', 0, '1969-12-31', '', '****************', '', 'a'),
(14, 1, '48258642', '', 'DANNY HERMINIO MAURY HURTADO', '', 0, '1969-12-31', '', 'ffffffffffffffffffff', '', 'a'),
(15, 1, '09892145', '', 'CARLOS GRIMALDO CHAHUAYO DURAN', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(16, 1, '76975607', '', 'VICTOR A SAICO JUST', '', 0, '1969-12-31', '', 'micaela bastidas', '', 'a'),
(17, 1, '40940363', '', 'ABRAHAM BENITO CASTILLA SOLORZANO', '', 0, '1969-12-31', '', 'VTA. ALEGRE 128', '', 'a'),
(18, 1, '73470380', '', 'JUAN CARLOS MACEDO APAZA', '', 0, '1969-12-31', '', 'ggggggg', '', 'a'),
(19, 1, '73470980', '', 'ANDERSON FRANK BLAS SOTO', '', 0, '1969-12-31', '', 'fff', '', 'a'),
(20, 1, '70149866', '', 'MARY EVELYN QUISPE LOPEZ', '', 0, '1969-12-31', '', 'jjj', '', 'a'),
(21, 2, '', '20609204762', 'INVERSIONES ROMAN JR S.R.L.', 'INVERSIONES ROMAN JR S.R.L.', 0, '1969-12-31', '', 'JR. PUNO NRO. 365 HUANCAYO CERCADO JUNIN HUANCAYO HUANCAYO', 'ENTREGAR A X LUGAR', 'a'),
(22, 1, '41243575', '', 'ANTONIO ENRIQUE HUAMAN MONTES', '', 0, '1969-12-31', '', 'fff', '', 'a'),
(23, 1, '77235739', '', 'YIMI ROLDAN MELCHOR QUISPE', '', 0, '1969-12-31', '', 'FFFF', '', 'a'),
(24, 1, '70139379', '', 'JEAN CARLOS POMA SANCHEZ', '', 0, '1969-12-31', '', 'montecarlo', '', 'a'),
(25, 1, '46568703', '', 'YOSEP ROLANDO SANABRIA LAZO', '', 0, '1969-12-31', '', 'residencial', '', 'a'),
(26, 1, '44384434', '', 'NADHIA NARDHA ESQUIVEL LLALLICO', '', 0, '1969-12-31', '', 'ROSEMBERG', '', 'a'),
(27, 1, '45486261', '', 'JEANCARLO JOSE ORE LAZO', '', 0, '1969-12-31', '', 'FF', '', 'a'),
(28, 1, '46839799', '', 'SUSANA ANDREA RIVERA VEGA', '', 0, '1969-12-31', '', 'proceres', '', 'a'),
(29, 1, '76835987', '', 'YHESSENIA SAENZ HUAMAN', '', 0, '1969-12-31', '', 'progreso', '', 'a'),
(30, 1, '46532023', '', 'LUIS EDUARDO PALACIOS MARMANILLO', '', 0, '1969-12-31', '', 'simon bolivar', '', 'a'),
(31, 1, '73190724', '', 'HELKIN SAMMIR ARAUJO VENTURA', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(32, 2, '', '10475084409', '', 'MACHACA CACHICATARI DIANA', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(33, 1, '73014977', '', 'JUAN JOSE HERNANDEZ GARAGATTI', '', 0, '1969-12-31', '', 'santa anita', '', 'a'),
(34, 1, '41595387', '', 'WILLIAM CAMPOS CARDENAS', '', 0, '1969-12-31', '', 'fff', '', 'a'),
(35, 2, '', '10751000346', '', 'VALERO MARAVI MELISA GRACIELA', 938254636, '1969-12-31', '', 'EN TU CORAZON <3', '', 'a'),
(36, 1, '73089799', '', 'WILLIAN DAVID ARGUME QUISPE', '', 0, '1969-12-31', '', 'FF', '', 'a'),
(37, 2, '', '20263322496', '', 'NESTLE PERU S A', 0, '1969-12-31', '', 'CAL. LUIS GALVANI NRO. 493 URB. LOTIZACION INDUSTRIAL SAN LIMA LIMA ATE', '', 'a'),
(38, 1, '48893782', '', 'EVELIN MAGALY COLONIO ÑAHUI', '', 0, '1969-12-31', '', 'fff', '', 'a'),
(39, 1, '76547879', '', 'ADAMARITH MARYCIELO DAMIAN MORATILLO', '', 0, '1969-12-31', '', 'ff', '', 'a'),
(40, 1, '23275167', '', 'EDGAR TOMAS CASTILLO PEÑARES', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(41, 1, '72569197', '', 'FELIPE MANUEL OLIVERA ROJAS', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(42, 1, '44363027', '', 'MIRKO JUAN CARLOS MARAVI CHIPANA', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(43, 1, '48014853', '', 'IRVING JUAN CARLOS QUINTANA PORRAS', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(44, 1, '20064152', '', 'JOSE GODOFREDO GOMERO QUINTO', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(45, 1, '72785284', '', 'CHRISTIAN OMAR DELGADILLO CARHUALLANQUI', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(46, 1, '47042091', '', 'KARINA MARIBEL PALACIOS VIVAS', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(47, 1, '45963389', '', 'MELVIN HANS SURICHAQUI QUISPE', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(48, 1, '43735480', '', 'OSCAR CRISTOBAL QUISPE', '', 0, '1969-12-31', '', 'JR.TRUJILLO 898', '', 'a'),
(49, 1, '20051173', '', 'MARISSA EVELYN SANABRIA MENDOZA', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(50, 1, '70034842', '', 'DIANA KAREN HUARCAYA RODRIGUEZ', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(51, 1, '80044045', '', 'BEDWER ERNESTO HERNANDEZ MIRANDA', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(52, 2, '', '20600194195', '', 'CONSTRUCTORA Y CONSULTORA FELVAR S.A.C.', 0, '1969-12-31', '', 'PJ. LA CANTUTA MZA. F INT. 11 A.H. LA PRIMAVERA JUNIN HUANCAYO EL TAMBO', '', 'a'),
(53, 2, '', '10425940312', '', 'CASTRO QUISPE WILLIAM JACKSON', 0, '1969-12-31', '', '.......', '', 'a'),
(54, 2, '', '20503644968', '', 'BODEGA SAN ISIDRO', 0, '1969-12-31', '', '------', '', 'a'),
(55, 1, '75513136', '', 'CLARA PORRAS', '', 0, '1969-12-31', '', 'FFF', '', 'a'),
(56, 2, '', '20600631978', '', 'INVERSIONES RAPIDFOOD SAC', 0, '1969-12-31', '', '.....', '', 'a'),
(57, 2, '', '20604856532', '', 'REPRESENTACIONES BOLE E.I.R.L', 0, '1969-12-31', '', 'Av.Republica de Uruguay Nro 701', '', 'a'),
(58, 1, '28384011', '', 'FRANK', '', 0, '1969-12-31', '', 'EN TU CORAZON <3 ', '', 'a'),
(59, 1, '47465344', '', 'KELLY ESTHER LOPEZ SOVERO', '', 0, '1969-12-31', '', '....', '', 'a'),
(60, 2, '', '10734709803', '', 'ANDERZON  FRANK BLAS SOTO', 0, '1969-12-31', '', '.....', '', 'a'),
(61, 1, '70234794', '', 'CALEB KEVIN CARHUAZ LAZARO', '', 0, '1969-12-31', '', '....', '', 'a'),
(62, 1, '70042502', '', 'STEPHANY MARJORIE VALENTIN ARCE', '', 0, '1969-12-31', '', '......', '', 'a'),
(63, 1, '20055743', '', 'JESSICA JUDITH SALDAÑA FLORES', '', 0, '1969-12-31', '', '.....', '', 'a'),
(64, 2, '', '20487182649', '', 'SERVICIO INTEGRAL PARA EL DESARROLLO DE LA ECOLOGIA PERU E.I.R.L.', 0, '1969-12-31', '', 'NRO. . DPTO. 402 C.H. JUAN PARRA DEL RIEGO JUNIN HUANCAYO EL TAMBO', '', 'a'),
(65, 1, '46485842', '', 'EDUARDO PRADO MELENDEZ', '', 0, '1969-12-31', '', '....', '', 'a'),
(66, 1, '72794022', '', 'DANA ROCIO VASQUEZ BUSTAMANTE', '', 0, '1969-12-31', '', '...', '', 'a'),
(67, 2, '', '20608359851', '', 'MAYISA E.I.R.L.', 0, '1969-12-31', '', 'JR. HUAMACHUCO NRO. A-11 JUNIN HUANCAYO PILCOMAYO', '', 'a'),
(68, 2, '', '10755577991', '', 'SAENZ CHUMBES FRECIA MARIPAZ', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(69, 2, '', '10749038662', '', 'OBREGON MIRANDA ARTURO JESUS', 0, '1969-12-31', '', 'g', '', 'a'),
(70, 1, '46021265', '', 'PAUL ANDERSON MAURICIO ALTAMIRANO', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(71, 1, '47991466', '', 'HENRRY RONALD TALAVERA ARISTE', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(72, 1, '72580196', '', 'LORENA KATHERINE FERNANDEZ LADERA', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(73, 1, '46334808', '', 'LUIS DANIEL ALCANTARA SUEÑO', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(74, 1, '46230068', '', 'MAYUMI SANCHES  HURRADO', '', 0, '1969-12-31', '', 'j', '', 'a'),
(75, 2, '', '20554166117', '', 'INDUSTRIA LA PIRCA SAC', 0, '1969-12-31', '', 'j', '', 'a'),
(76, 1, '43704140', '', 'RACHEL ZOELI QUIÑONEZ MARTINEZ', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(77, 1, '70769262', '', 'GIANFRANCO RICARDO FLORES RODRIGUEZ', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(78, 1, '73384437', '', 'KEVIN LUIS  TAZA TERREROS', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(79, 1, '75222617', '', 'RAUL ALEJANDRO ROJAS DORREGARAY', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(80, 1, '77335195', '', 'MARIELLA GUISEL MATEO JACAY', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(81, 1, '46363889', '', 'JUAN CARLOS RAMOS ALEJO', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(82, 2, '', '10707577784', '', 'MARGOT PEÑARES PEÑALOZA', 0, '1969-12-31', '', 'ica-ica', 'PUENTE BLANCO', 'a'),
(83, 2, '', '10763109114', '', 'JHON KEVIN CHURA JESUS', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(84, 2, '', '20606603933', '', 'CONSTRUCTORA Y CONSULTORA ARAUCO FLORES SAC', 0, '1969-12-31', '', 'JR. MANUEL PRADO NRO. 253 JUNIN SATIPO SATIPO', '', 'a'),
(85, 1, '72807712', '', 'JUAN CARLOS TORRES AGUILAR', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(86, 2, '', '10716450100', '', 'SANCHEZ JESUS CARLA CHABELI', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(87, 1, '43000452', '', 'ISABELA ELEONORA JUMPA RAMOS', '', 0, '1969-12-31', '', 'sin direccion', '', 'a'),
(88, 1, '47270790', '', 'STEVE JHONNATAN SOVERO MAURATE', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(89, 1, '70134758', '', 'EDUARDO MODESTO POMA ORDOÑEZ', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(90, 1, '48230487', '', 'JOSE ANTONIO ROMERO SULLCA', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(91, 1, '70219418', '', 'DAVID JOSE BORDA LOPEZ', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(92, 1, '70297213', '', 'LHESLY ZONYA MADUEÑO POMA', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(93, 1, '46599027', '', 'JEANN CARLOS ROJAS CARHUANCHO', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(94, 1, '48052727', '', 'RUTH KAREN CAMPOS PALACIOS', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(95, 2, '', '10759818194', '', 'LOZANO LAZO LESLY GERALDYNE', 0, '1969-12-31', '', 'FERROCARRIL 414', '', 'a'),
(96, 1, '46444377', '', 'MARIELA VERENISSE HILARIO YUCRA', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(97, 1, '41995915', '', 'EVERT PIER GOMEZ ORGA', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(98, 1, '42324056', '', 'DAVID DARWIN PEREZ BERAUN', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(99, 1, '20063076', '', 'NIKO GERSON TEJADA ÑAVINCOPA', '', 0, '1969-12-31', '', 'f', '', 'a'),
(100, 1, '71500080', '', 'ENRIQUE JUNIOR DIAZ RUEDAS', '', 0, '1969-12-31', '', '}l', '', 'a'),
(101, 2, '', '20607876267', '', 'COSEIM SOCIEDAD ANONIMA CERRADA', 0, '1969-12-31', '', 'AV. ANDRES A. CACERES NRO. 1495 BAR. YANANACO HUANCAVELICA HUANCAVELICA HUANCAVELICA', '', 'a'),
(102, 1, '73041817', '', 'JEISON KEVIN ROSALES ZACARIAS', '', 0, '1969-12-31', '', 's/n', '', 'a'),
(103, 1, '71733732', '', 'DEISY BLAS VICENTE', '', 0, '1969-12-31', '', 's/n', '', 'a'),
(104, 1, '45907752', '', 'MEDALY CRISTINA CASTRO BLANCAS', '', 0, '1969-12-31', '', 's/n', '', 'a'),
(105, 1, '70435193', '', 'FRANKLIN LEONCIO HUAMAN ARAUJO', '', 0, '1969-12-31', '', 's/n', '', 'a'),
(106, 2, '', '20549309501', '', 'CONSORCIO HERCOSAYFA S.A.C.', 0, '1969-12-31', '', 'MZA. O LOTE. 5 ROSARIO DEL NORTE LIMA LIMA SAN MARTIN DE PORRES', '', 'a'),
(107, 1, '47452715', '', 'MIGUEL ARTURO TOLENTINO DIAZ', '', 0, '1969-12-31', '', 's/n', '', 'a'),
(108, 1, '47785181', '', 'JHONATAN EDUARDO CARACUZMA RAMON', '', 0, '1969-12-31', '', 's/n', '', 'a'),
(109, 1, '46782818', '', 'CARLOS LEOPOLDO HUAROC CARRION', '', 0, '1969-12-31', '', 's/n', '', 'a'),
(110, 1, '76370545', '', 'DIEGO ARMANDO VEGA RODRIGUEZ', '', 0, '1969-12-31', '', 's/n', '', 'a'),
(111, 1, '73329888', '', 'ANTTONY KEVIN LUQUILLAS VILCAPOMA', '', 0, '1969-12-31', '', 's/*n', '', 'a'),
(112, 2, '', '10721748109', '', 'DIONISIO ISLA RICARDO', 0, '1969-12-31', '', 'JR. SAN MARTIN 864 -SATIPO', '', 'a'),
(113, 2, '', '10482861828', '', 'FLORES CCANTO MICHAEL ALEXANDER', 0, '1969-12-31', '', 's/n', '', 'a'),
(114, 1, '75535712', '', 'FRANCES YANELA LUIS MEZA', '', 0, '1969-12-31', '', 's/n', '', 'a'),
(115, 1, '47985514', '', 'MIRIAM TITO AVILA', '', 0, '1969-12-31', '', 's/n', '', 'a'),
(116, 1, '42848372', '', 'ANA MARIA GONZALES YAMASHIRO', '', 0, '1969-12-31', '', 'S/D', '', 'a'),
(117, 1, '40726517', '', 'RAFAEL EDILIO CASTELLANOS GOMEZ', '', 0, '1969-12-31', '', 'SIN DIRECCION', '', 'a'),
(118, 1, '18048282', '', 'FREDI EDUARDO PEREZ CERQUERA', '', 986986344, '1970-01-01', '', 'Ambato', 'AMBATO', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_compra`
--

CREATE TABLE `tm_compra` (
  `id_compra` int(11) NOT NULL,
  `id_prov` int(11) NOT NULL,
  `id_tipo_compra` int(11) NOT NULL,
  `id_tipo_doc` int(11) NOT NULL,
  `id_usu` int(11) DEFAULT NULL,
  `fecha_c` date DEFAULT NULL,
  `hora_c` varchar(45) DEFAULT NULL,
  `serie_doc` varchar(45) DEFAULT NULL,
  `num_doc` varchar(45) DEFAULT NULL,
  `igv` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `descuento` decimal(10,2) DEFAULT NULL,
  `estado` varchar(1) DEFAULT 'a',
  `observaciones` varchar(100) DEFAULT NULL,
  `fecha_reg` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_compra_credito`
--

CREATE TABLE `tm_compra_credito` (
  `id_credito` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `interes` decimal(10,2) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado` varchar(5) DEFAULT 'p'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_compra_detalle`
--

CREATE TABLE `tm_compra_detalle` (
  `id_compra` int(11) NOT NULL,
  `id_tp` int(11) NOT NULL,
  `id_pres` int(11) NOT NULL,
  `cant` decimal(10,2) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_configuracion`
--

CREATE TABLE `tm_configuracion` (
  `id_cfg` int(11) NOT NULL,
  `zona_hora` varchar(100) DEFAULT NULL,
  `trib_acr` varchar(20) DEFAULT NULL,
  `trib_car` int(5) DEFAULT NULL,
  `di_acr` varchar(20) DEFAULT NULL,
  `di_car` int(5) DEFAULT NULL,
  `imp_acr` varchar(20) DEFAULT NULL,
  `imp_val` decimal(10,2) DEFAULT NULL,
  `mon_acr` varchar(20) DEFAULT NULL,
  `mon_val` varchar(5) DEFAULT NULL,
  `pc_name` varchar(50) DEFAULT NULL,
  `pc_ip` varchar(20) DEFAULT NULL,
  `print_com` int(1) DEFAULT NULL,
  `print_pre` int(1) DEFAULT NULL,
  `print_cpe` int(1) DEFAULT NULL,
  `opc_01` int(1) DEFAULT NULL,
  `opc_02` int(1) DEFAULT NULL,
  `opc_03` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_configuracion`
--

INSERT INTO `tm_configuracion` (`id_cfg`, `zona_hora`, `trib_acr`, `trib_car`, `di_acr`, `di_car`, `imp_acr`, `imp_val`, `mon_acr`, `mon_val`, `pc_name`, `pc_ip`, `print_com`, `print_pre`, `print_cpe`, `opc_01`, `opc_02`, `opc_03`) VALUES
(1, 'America/Lima', 'RUC', 11, 'DNI', 8, 'IVA', '18.00', 'MXN', '$', 'SISTEMAS', '192.168.1.9', 1, 1, 1, 0, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_credito_detalle`
--

CREATE TABLE `tm_credito_detalle` (
  `id_credito` int(11) DEFAULT NULL,
  `id_usu` int(11) DEFAULT NULL,
  `importe` decimal(10,2) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `egreso` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_detalle_combo`
--

CREATE TABLE `tm_detalle_combo` (
  `id_pres` int(11) NOT NULL,
  `id_ing` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_detalle_pedido`
--

CREATE TABLE `tm_detalle_pedido` (
  `id_pedido` int(11) NOT NULL,
  `id_usu` int(11) NOT NULL,
  `id_pres` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `cant` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `comentario` varchar(100) NOT NULL,
  `fecha_pedido` datetime NOT NULL,
  `fecha_envio` datetime NOT NULL,
  `estado` varchar(5) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_detalle_pedido`
--

INSERT INTO `tm_detalle_pedido` (`id_pedido`, `id_usu`, `id_pres`, `cantidad`, `cant`, `precio`, `comentario`, `fecha_pedido`, `fecha_envio`, `estado`) VALUES
(3, 53, 2, 1, 1, '23.00', '', '2023-03-20 15:44:51', '0000-00-00 00:00:00', 'a'),
(3, 53, 3, 1, 1, '23.00', '', '2023-03-20 15:44:51', '0000-00-00 00:00:00', 'a'),
(3, 53, 5, 1, 1, '23.00', '', '2023-03-20 15:44:51', '0000-00-00 00:00:00', 'a'),
(3, 1, 308, 1, 1, '86.00', '', '2023-03-20 18:47:30', '0000-00-00 00:00:00', 'a'),
(3, 1, 5, 1, 1, '23.00', '', '2023-03-20 18:47:30', '0000-00-00 00:00:00', 'a'),
(3, 1, 4, 1, 1, '23.00', '', '2023-03-20 18:47:30', '0000-00-00 00:00:00', 'a'),
(3, 1, 3, 1, 1, '23.00', '', '2023-03-20 18:47:30', '0000-00-00 00:00:00', 'a'),
(3, 1, 1, 2, 2, '29.00', '', '2023-03-20 18:47:30', '0000-00-00 00:00:00', 'a'),
(5, 55, 157, 1, 1, '50.90', '', '2023-03-21 16:23:05', '2023-03-21 16:47:20', 'c'),
(5, 55, 154, 1, 1, '33.90', '', '2023-03-21 16:23:05', '2023-03-21 16:46:26', 'c'),
(6, 55, 157, 1, 1, '50.90', '', '2023-03-21 16:34:09', '2023-03-21 16:47:41', 'c'),
(6, 55, 156, 1, 1, '45.90', '', '2023-03-21 16:34:20', '2023-03-21 16:48:00', 'c'),
(7, 55, 10, 1, 1, '30.00', '', '2023-03-21 16:41:01', '0000-00-00 00:00:00', 'a'),
(3, 53, 18, 2, 2, '28.00', '', '2023-03-22 12:07:06', '0000-00-00 00:00:00', 'a'),
(3, 53, 16, 3, 3, '28.00', '', '2023-03-22 12:07:06', '0000-00-00 00:00:00', 'a'),
(3, 53, 157, 2, 2, '50.90', '', '2023-03-22 12:08:57', '2023-03-22 12:10:29', 'c'),
(3, 53, 154, 2, 2, '33.90', '', '2023-03-22 12:09:02', '2023-03-22 12:10:16', 'c'),
(8, 44, 156, 2, 2, '45.90', '', '2023-03-22 12:14:00', '0000-00-00 00:00:00', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_detalle_venta`
--

CREATE TABLE `tm_detalle_venta` (
  `id_venta` int(11) NOT NULL,
  `id_prod` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_empresa`
--

CREATE TABLE `tm_empresa` (
  `id_de` int(11) NOT NULL,
  `ruc` varchar(20) DEFAULT NULL,
  `razon_social` varchar(200) DEFAULT NULL,
  `nombre_comercial` varchar(200) DEFAULT NULL,
  `direccion_comercial` varchar(200) DEFAULT NULL,
  `direccion_fiscal` varchar(200) DEFAULT NULL,
  `ubigeo` varchar(8) DEFAULT NULL,
  `departamento` varchar(50) DEFAULT NULL,
  `provincia` varchar(50) DEFAULT NULL,
  `distrito` varchar(50) DEFAULT NULL,
  `sunat` int(1) NOT NULL,
  `modo` int(1) DEFAULT NULL,
  `usuariosol` varchar(50) DEFAULT NULL,
  `clavesol` varchar(50) DEFAULT NULL,
  `clavecertificado` varchar(50) DEFAULT NULL,
  `logo` varchar(45) DEFAULT NULL,
  `celular` varchar(50) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_empresa`
--

INSERT INTO `tm_empresa` (`id_de`, `ruc`, `razon_social`, `nombre_comercial`, `direccion_comercial`, `direccion_fiscal`, `ubigeo`, `departamento`, `provincia`, `distrito`, `sunat`, `modo`, `usuariosol`, `clavesol`, `clavecertificado`, `logo`, `celular`, `email`) VALUES
(1, '20605905367', 'RESTAURANT AMAZONICO', 'RESTAURANTE AMAZONICO', '-', '-', '1', '-', '-', '-', 0, 3, '-', '-', '-', 'logoprint.png', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_gastos_adm`
--

CREATE TABLE `tm_gastos_adm` (
  `id_ga` int(11) NOT NULL,
  `id_tipo_gasto` int(11) NOT NULL,
  `id_usu` int(11) NOT NULL,
  `id_apc` int(11) NOT NULL,
  `id_per` int(11) DEFAULT NULL,
  `importe` decimal(10,2) DEFAULT NULL,
  `responsable` varchar(100) DEFAULT NULL,
  `motivo` varchar(100) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  `estado` varchar(5) DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_gastos_adm`
--

INSERT INTO `tm_gastos_adm` (`id_ga`, `id_tipo_gasto`, `id_usu`, `id_apc`, `id_per`, `importe`, `responsable`, `motivo`, `fecha_registro`, `estado`) VALUES
(1, 1, 1, 1, 0, '300.00', 'FSJASDI', 'fdsfdfs', '2023-03-12 23:13:53', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_impresora`
--

CREATE TABLE `tm_impresora` (
  `id_imp` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `estado` varchar(5) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tm_impresora`
--

INSERT INTO `tm_impresora` (`id_imp`, `nombre`, `estado`) VALUES
(1, 'NINGUNO', 'a'),
(2, 'COCINA', 'a'),
(3, 'BARRA', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_ingresos_adm`
--

CREATE TABLE `tm_ingresos_adm` (
  `id_ing` int(11) NOT NULL,
  `id_usu` int(11) NOT NULL,
  `id_apc` int(11) NOT NULL,
  `importe` decimal(10,2) DEFAULT NULL,
  `responsable` varchar(100) DEFAULT NULL,
  `motivo` varchar(200) DEFAULT NULL,
  `fecha_reg` datetime DEFAULT NULL,
  `estado` varchar(5) DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_ingresos_adm`
--

INSERT INTO `tm_ingresos_adm` (`id_ing`, `id_usu`, `id_apc`, `importe`, `responsable`, `motivo`, `fecha_reg`, `estado`) VALUES
(1, 1, 1, '1000.00', 'SBDJDASN', 'snsad', '2023-03-12 23:13:29', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_insumo`
--

CREATE TABLE `tm_insumo` (
  `id_ins` int(11) NOT NULL,
  `id_catg` int(11) NOT NULL,
  `id_med` int(11) NOT NULL,
  `cod_ins` varchar(10) DEFAULT NULL,
  `nomb_ins` varchar(45) DEFAULT NULL,
  `stock_min` int(11) DEFAULT NULL,
  `cos_uni` decimal(10,2) DEFAULT NULL,
  `estado` varchar(5) DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_insumo`
--

INSERT INTO `tm_insumo` (`id_ins`, `id_catg`, `id_med`, `cod_ins`, `nomb_ins`, `stock_min`, `cos_uni`, `estado`) VALUES
(2, 2, 2, 'P001', 'PAPAS', 0, '12.00', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_insumo_catg`
--

CREATE TABLE `tm_insumo_catg` (
  `id_catg` int(11) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `estado` varchar(5) DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_insumo_catg`
--

INSERT INTO `tm_insumo_catg` (`id_catg`, `descripcion`, `estado`) VALUES
(2, 'TUBERCULOS', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_inventario`
--

CREATE TABLE `tm_inventario` (
  `id_inv` int(11) NOT NULL,
  `id_tipo_ope` int(11) NOT NULL,
  `id_ope` int(11) NOT NULL,
  `id_tipo_ins` int(11) NOT NULL,
  `id_ins` int(11) NOT NULL,
  `cos_uni` decimal(10,2) NOT NULL,
  `cant` float NOT NULL,
  `fecha_r` datetime NOT NULL,
  `estado` varchar(5) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_inventario_entsal`
--

CREATE TABLE `tm_inventario_entsal` (
  `id_es` int(11) NOT NULL,
  `id_usu` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `id_responsable` int(11) NOT NULL,
  `motivo` varchar(200) NOT NULL,
  `fecha` datetime NOT NULL,
  `estado` varchar(5) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_margen_venta`
--

CREATE TABLE `tm_margen_venta` (
  `id` int(11) NOT NULL,
  `cod_dia` int(11) NOT NULL,
  `dia` varchar(45) NOT NULL,
  `margen` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tm_margen_venta`
--

INSERT INTO `tm_margen_venta` (`id`, `cod_dia`, `dia`, `margen`) VALUES
(1, 1, 'Lunes', '150.00'),
(2, 2, 'Martes', '750.00'),
(3, 3, 'Miércoles', '750.00'),
(4, 4, 'Jueves', '850.00'),
(5, 5, 'Viernes', '1200.00'),
(6, 6, 'Sábado', '1800.00'),
(7, 0, 'Domingo', '2500.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_mesa`
--

CREATE TABLE `tm_mesa` (
  `id_mesa` int(11) NOT NULL,
  `id_salon` int(11) NOT NULL,
  `nro_mesa` varchar(5) NOT NULL,
  `estado` varchar(45) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_mesa`
--

INSERT INTO `tm_mesa` (`id_mesa`, `id_salon`, `nro_mesa`, `estado`) VALUES
(1, 1, '1', 'i'),
(2, 1, '2', 'i'),
(3, 1, '3', 'a'),
(4, 1, '4', 'a'),
(5, 1, '5', 'a'),
(6, 1, '6', 'a'),
(7, 1, '7', 'a'),
(8, 1, '8', 'i'),
(9, 1, '9', 'i'),
(10, 2, '1', 'a'),
(11, 2, '2', 'a'),
(12, 2, '3', 'a'),
(13, 2, '4', 'a'),
(14, 2, '5', 'a'),
(15, 2, '6', 'a'),
(16, 2, '7', 'a'),
(17, 3, '1', 'a'),
(18, 3, '2', 'a'),
(19, 3, '3', 'a'),
(20, 3, '4', 'a'),
(21, 3, '5', 'a'),
(22, 3, '6', 'a'),
(23, 3, '7', 'a'),
(24, 3, '8', 'a'),
(25, 4, '1', 'a'),
(26, 4, '2', 'a'),
(27, 4, '3', 'a'),
(28, 4, '4', 'a'),
(29, 4, '5', 'a'),
(30, 4, '6', 'a'),
(31, 5, '1', 'a'),
(32, 5, '2', 'a'),
(33, 5, '3', 'a'),
(34, 5, '4', 'a'),
(35, 5, '5', 'a'),
(36, 5, '6', 'a'),
(37, 5, '7', 'a'),
(38, 5, '8', 'a'),
(39, 5, '9', 'a'),
(40, 6, '1', 'a'),
(41, 6, '2', 'a'),
(42, 6, '3', 'a'),
(43, 6, '4', 'a'),
(44, 6, '5', 'a'),
(45, 6, '6', 'a'),
(46, 6, '7', 'a'),
(47, 6, '8', 'a'),
(48, 6, '9', 'a'),
(49, 2, '8', 'a'),
(50, 2, '9', 'a'),
(51, 4, '7', 'a'),
(52, 4, '8', 'a'),
(53, 4, '9', 'a'),
(54, 3, '9', 'a'),
(55, 1, '10', 'a'),
(56, 1, '11', 'a'),
(57, 1, '12', 'a'),
(58, 1, '13', 'a'),
(59, 1, '14', 'a'),
(60, 1, '15', 'a'),
(61, 1, '16', 'a'),
(62, 1, '17', 'a'),
(63, 1, '18', 'a'),
(64, 1, '19', 'a'),
(65, 1, '20', 'a'),
(66, 2, '10', 'a'),
(67, 3, '10', 'a'),
(68, 3, '11', 'a'),
(69, 3, '12', 'a'),
(70, 3, '13', 'a'),
(71, 3, '14', 'a'),
(72, 3, '15', 'a'),
(73, 3, '16', 'a'),
(74, 3, '17', 'a'),
(75, 3, '18', 'a'),
(76, 3, '19', 'a'),
(77, 3, '20', 'a'),
(78, 4, '10', 'a'),
(79, 4, '11', 'a'),
(80, 4, '12', 'a'),
(81, 4, '13', 'a'),
(82, 4, '14', 'a'),
(83, 4, '15', 'a'),
(84, 5, '10', 'a'),
(85, 5, '11', 'a'),
(86, 5, '12', 'a'),
(87, 6, '10', 'a'),
(88, 6, '11', 'a'),
(89, 6, '12', 'a'),
(90, 6, '13', 'a'),
(91, 6, '14', 'a'),
(92, 6, '15', 'a'),
(93, 5, '13', 'a'),
(94, 5, '14', 'a'),
(95, 5, '15', 'a'),
(96, 2, '11', 'a'),
(97, 2, '12', 'a'),
(98, 2, '13', 'a'),
(99, 2, '14', 'a'),
(100, 2, '15', 'a'),
(101, 1, '21', 'a'),
(102, 1, '22', 'a'),
(103, 1, '23', 'a'),
(104, 1, '24', 'a'),
(105, 1, '25', 'a'),
(106, 1, '26', 'a'),
(107, 1, '27', 'a'),
(108, 1, '28', 'a'),
(109, 1, '29', 'a'),
(110, 1, '30', 'a'),
(111, 2, '16', 'a'),
(112, 2, '17', 'a'),
(113, 2, '18', 'a'),
(114, 2, '19', 'a'),
(115, 2, '20', 'a'),
(116, 2, '21', 'a'),
(117, 2, '22', 'a'),
(118, 2, '23', 'a'),
(119, 2, '24', 'a'),
(120, 2, '25', 'a'),
(121, 2, '26', 'a'),
(122, 2, '27', 'a'),
(123, 2, '28', 'a'),
(124, 2, '29', 'a'),
(125, 2, '30', 'a'),
(126, 3, '21', 'a'),
(127, 3, '22', 'a'),
(128, 3, '23', 'a'),
(129, 3, '24', 'a'),
(130, 3, '25', 'a'),
(131, 3, '26', 'a'),
(132, 3, '27', 'a'),
(133, 3, '28', 'a'),
(134, 3, '29', 'a'),
(135, 3, '30', 'a'),
(136, 4, '16', 'a'),
(137, 4, '17', 'a'),
(138, 4, '18', 'a'),
(139, 4, '19', 'a'),
(140, 4, '20', 'a'),
(141, 4, '21', 'a'),
(142, 6, '16', 'a'),
(143, 6, '17', 'a'),
(144, 6, '18', 'a'),
(145, 6, '19', 'a'),
(146, 6, '20', 'a'),
(147, 6, '21', 'a'),
(148, 10, '1', 'a'),
(149, 10, '2', 'a'),
(150, 10, '3', 'a'),
(151, 10, '4', 'a'),
(152, 10, '5', 'a'),
(153, 10, '6', 'a'),
(154, 10, '7', 'a'),
(155, 10, '8', 'a'),
(156, 10, '9', 'a'),
(157, 10, '10', 'a'),
(158, 10, '11', 'a'),
(159, 10, '12', 'a'),
(160, 10, '13', 'i'),
(161, 10, '14', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_pago`
--

CREATE TABLE `tm_pago` (
  `id_pago` int(2) NOT NULL,
  `descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_pedido`
--

CREATE TABLE `tm_pedido` (
  `id_pedido` int(11) NOT NULL,
  `id_tipo_pedido` int(11) NOT NULL,
  `id_apc` int(11) DEFAULT NULL,
  `id_usu` int(11) NOT NULL,
  `fecha_pedido` datetime NOT NULL,
  `estado` varchar(5) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_pedido`
--

INSERT INTO `tm_pedido` (`id_pedido`, `id_tipo_pedido`, `id_apc`, `id_usu`, `fecha_pedido`, `estado`) VALUES
(3, 1, NULL, 53, '2023-03-20 15:44:43', 'a'),
(4, 1, NULL, 55, '2023-03-21 16:22:10', 'z'),
(5, 1, NULL, 55, '2023-03-21 16:22:50', 'a'),
(6, 1, NULL, 55, '2023-03-21 16:33:58', 'a'),
(7, 1, NULL, 55, '2023-03-21 16:40:56', 'a'),
(8, 2, NULL, 44, '2023-03-22 12:13:38', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_pedido_delivery`
--

CREATE TABLE `tm_pedido_delivery` (
  `id_pedido` int(11) NOT NULL,
  `tipo_canal` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_repartidor` int(11) NOT NULL,
  `tipo_pago` int(11) NOT NULL,
  `tipo_entrega` int(11) NOT NULL,
  `pedido_programado` int(11) DEFAULT 0,
  `hora_entrega` time DEFAULT '00:00:00',
  `paga_con` decimal(10,2) NOT NULL,
  `comision_delivery` decimal(10,2) NOT NULL,
  `amortizacion` decimal(10,2) NOT NULL,
  `nro_pedido` varchar(10) NOT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `telefono_cliente` varchar(20) NOT NULL,
  `direccion_cliente` varchar(100) NOT NULL,
  `referencia_cliente` varchar(100) NOT NULL,
  `email_cliente` varchar(200) NOT NULL,
  `fecha_preparacion` datetime NOT NULL,
  `fecha_envio` datetime NOT NULL,
  `fecha_entrega` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_pedido_llevar`
--

CREATE TABLE `tm_pedido_llevar` (
  `id_pedido` int(11) NOT NULL,
  `nro_pedido` varchar(10) NOT NULL,
  `nomb_cliente` varchar(100) NOT NULL,
  `fecha_entrega` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_pedido_llevar`
--

INSERT INTO `tm_pedido_llevar` (`id_pedido`, `nro_pedido`, `nomb_cliente`, `fecha_entrega`) VALUES
(8, '00001', '1804750113', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_pedido_mesa`
--

CREATE TABLE `tm_pedido_mesa` (
  `id_pedido` int(11) NOT NULL,
  `id_mesa` int(11) NOT NULL,
  `id_mozo` int(11) NOT NULL,
  `nomb_cliente` varchar(45) NOT NULL,
  `nro_personas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_pedido_mesa`
--

INSERT INTO `tm_pedido_mesa` (`id_pedido`, `id_mesa`, `id_mozo`, `nomb_cliente`, `nro_personas`) VALUES
(3, 8, 54, 'MESA 8', 1),
(4, 1, 54, 'MESA 1', 2),
(5, 9, 54, 'Mesa: 9', 2),
(6, 1, 54, 'MESA 1', 2),
(7, 2, 54, 'MESA 2', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_pedido_portero`
--

CREATE TABLE `tm_pedido_portero` (
  `id_pedido` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `personas` int(11) NOT NULL,
  `id_mesa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_precios`
--

CREATE TABLE `tm_precios` (
  `id_precio` int(11) NOT NULL,
  `id_pres` int(11) NOT NULL,
  `precio` decimal(11,2) NOT NULL,
  `dia` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tm_precios`
--

INSERT INTO `tm_precios` (`id_precio`, `id_pres`, `precio`, `dia`) VALUES
(3, 90, '13.00', 'Lunes'),
(4, 91, '10.00', 'Lunes'),
(5, 89, '12.00', 'Martes'),
(6, 219, '15.00', 'Martes'),
(7, 219, '10.00', 'Lunes'),
(8, 19, '15.00', 'Sabado'),
(10, 1, '29.00', 'Martes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_preparados`
--

CREATE TABLE `tm_preparados` (
  `id_preparado` int(11) NOT NULL,
  `id_catg` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `estado` varchar(2) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_preparados_catg`
--

CREATE TABLE `tm_preparados_catg` (
  `id_catg` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `estado` varchar(1) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tm_preparados_catg`
--

INSERT INTO `tm_preparados_catg` (`id_catg`, `nombre`, `estado`) VALUES
(1, 'PRUEBA DE UPDATE', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_producto`
--

CREATE TABLE `tm_producto` (
  `id_prod` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `id_catg` int(11) NOT NULL DEFAULT 0,
  `id_areap` int(11) NOT NULL,
  `nombre` varchar(45) DEFAULT NULL,
  `notas` varchar(200) DEFAULT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `delivery` int(1) DEFAULT 0,
  `estado` varchar(1) DEFAULT 'a',
  `combo` varchar(2) NOT NULL DEFAULT 'b'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tm_producto`
--

INSERT INTO `tm_producto` (`id_prod`, `id_tipo`, `id_catg`, `id_areap`, `nombre`, `notas`, `descripcion`, `delivery`, `estado`, `combo`) VALUES
(1, 1, 2, 2, 'EL SENSUAL', '', NULL, 0, 'a', 'b'),
(2, 1, 2, 2, 'LA DIVA', 'SUAVE,POCO HIELO ', NULL, 0, 'a', 'b'),
(3, 1, 2, 2, 'EL AGRESIVO', 'FUERTE', NULL, 0, 'a', 'b'),
(4, 1, 2, 2, 'EL ORGULLOSO', 'VODKA', NULL, 0, 'a', 'b'),
(5, 1, 2, 2, 'EL PITUCO', '', NULL, 0, 'a', 'b'),
(6, 1, 3, 2, 'YOLO', '', NULL, 0, 'a', 'b'),
(7, 1, 3, 2, 'SUKHA', '', NULL, 0, 'a', 'b'),
(8, 1, 3, 2, 'STRONGER', '', NULL, 0, 'a', 'b'),
(9, 1, 3, 2, 'EL CATRÍN', 'BAJO EN ALCOHOL', NULL, 0, 'a', 'b'),
(10, 1, 4, 2, 'WANCATEC', '', NULL, 0, 'a', 'b'),
(11, 1, 4, 2, 'CHILCANO FLORAL', '', NULL, 0, 'a', 'b'),
(12, 1, 4, 2, 'FLAT WHITE BARSOL', '', NULL, 0, 'a', 'b'),
(13, 1, 5, 2, 'MAI TAI', '', NULL, 0, 'a', 'b'),
(14, 1, 5, 2, 'ZOMBIE', '', NULL, 0, 'a', 'b'),
(15, 1, 5, 2, 'MAORI', 'SHARK BITE', NULL, 0, 'a', 'b'),
(16, 1, 5, 2, 'SCORPION', 'SHRUREKEN SKULL,SCORPION,MAORI', NULL, 0, 'a', 'b'),
(17, 1, 5, 2, 'AMAZONICO', '', NULL, 0, 'a', 'b'),
(18, 1, 5, 2, 'SHARK BITE', '3X2,SCORPION,POCO HIELO', NULL, 0, 'a', 'b'),
(19, 1, 6, 2, 'CALIENTE DE VINO', 'CON PISCO,RON,WHISKY,VINO,3X2 ,BORGOÑA', NULL, 0, 'a', 'b'),
(20, 1, 6, 2, 'NARANJITA PISQUERA', 'TORONTEL,ACHOLADO,CARGADO,2 ILATIA ,2 TORONTEL,PISCO ITALIA ,ITALIA', NULL, 0, 'a', 'b'),
(21, 1, 6, 2, 'NARANJITA VIP', 'CON RON FLOR DE CAÑA,ABUELO AÑEJO,APPLETON STATE,GRANT\'S,JACK DANIEL\'S,MIEL DE ABEJA,REDUCCIÓN DE KION,JAGERMEISTER ,2 APPLETON STATE', NULL, 0, 'a', 'b'),
(22, 1, 19, 2, 'CALIENTE DE VINO', 'BORGOÑA,PISCO QUEBRANTA BARSOL,CASILLERO DEL DIABLO', NULL, 0, 'a', 'b'),
(23, 1, 7, 2, 'CALIENTE TROPICO DE FRUTOS ROJOS', 'RON ABUELO AÑEJO,PISCO PURO QUEBRANTA BARSOL', NULL, 0, 'a', 'b'),
(24, 1, 7, 2, 'MATACOVID', 'PARA AFECCIONES RESPIRATORIAS,PISCO PURO QUEBRANTA BARSOL,EUCALIPTO,NARANJA,MIEL DE ABEJA,REDUCCIÓN DE KION', NULL, 0, 'a', 'b'),
(25, 1, 7, 2, 'ELIXIR LONGEVO', 'ZUMO DE PIÑA GOLDEN,INFUSIÓN DE HIERBA LUIS, CON SUTILES NOTAS DE MANZANILLA,ZUMO DE NARANJA,ALMIBAR DE KION,RALLADURA DE CARDAMOMO PARA INCREMENTAR SUS DEFENSAS,BIENESTAR DEL VIENTRE,APORTAR ANTIOXID', NULL, 0, 'a', 'b'),
(26, 1, 8, 2, 'ZUMO DE PIÑA', '', NULL, 0, 'a', 'b'),
(27, 1, 8, 2, 'ZUMO DE LIMÓN (EL VIRGO)', 'LA PITA ', NULL, 0, 'a', 'b'),
(28, 1, 8, 2, 'SYRUP DE CANELA', '', NULL, 0, 'a', 'b'),
(29, 1, 8, 2, 'MERMELADA DE MELOCOTON', '', NULL, 0, 'a', 'b'),
(30, 1, 8, 2, 'AGUA CON GAS (EL VIRGO)', '', NULL, 0, 'a', 'b'),
(31, 1, 8, 2, 'EMULICION DE PIÑA', '', NULL, 0, 'a', 'b'),
(32, 1, 8, 2, 'INFUSIÓN SODA DE HIERBA LUISA', '', NULL, 0, 'a', 'b'),
(33, 1, 8, 2, 'ZUMO DE NARANJA', '', NULL, 0, 'a', 'b'),
(34, 1, 8, 2, 'ZUMO DE MARACUYA', '', NULL, 0, 'a', 'b'),
(35, 1, 8, 2, 'CAFÉ EXPRESSO', '', NULL, 0, 'a', 'b'),
(36, 1, 8, 2, 'ZUMO NARANJAS', '', NULL, 0, 'a', 'b'),
(37, 1, 8, 2, 'ALNIVAR DE VAINILLA', '', NULL, 0, 'a', 'b'),
(38, 1, 8, 2, 'AGUA CON GAS (EL MORFEO)', '', NULL, 0, 'a', 'b'),
(39, 1, 8, 2, 'CREMA DE COCO', '', NULL, 0, 'a', 'b'),
(40, 1, 8, 2, 'ZUMO DE LIMÓN (EL MANUEL)', '', NULL, 0, 'a', 'b'),
(41, 1, 8, 2, 'AGUA CON GAS (EL MANUEL)', '', NULL, 0, 'a', 'b'),
(42, 1, 9, 2, 'COCA COLA', '', NULL, 0, 'a', 'b'),
(43, 1, 9, 2, 'INKA COLA', '', NULL, 0, 'a', 'b'),
(44, 1, 9, 2, 'GUARANA', '', NULL, 0, 'a', 'b'),
(45, 1, 9, 2, 'GINGER BEER MR PERKINS', '', NULL, 0, 'a', 'b'),
(46, 1, 9, 2, 'NARANJA', '', NULL, 0, 'a', 'b'),
(47, 1, 9, 2, 'PIÑA', '', NULL, 0, 'a', 'b'),
(48, 1, 9, 2, 'TONICA ORIGINAL MR. PERKINS', '', NULL, 0, 'a', 'b'),
(49, 1, 9, 2, 'TONICA BLOSSOM PERKINS', '', NULL, 0, 'a', 'b'),
(50, 1, 9, 2, 'TONICA AMAZONICA MR. PERKINS', '', NULL, 0, 'a', 'b'),
(51, 1, 9, 2, 'SAN MATEO', 'AGUA CON GAS', NULL, 0, 'a', 'b'),
(52, 1, 9, 2, 'RED BULL CLASICO (BEB ENV)', '', NULL, 0, 'a', 'b'),
(53, 2, 13, 2, 'RICCADONNA', '', NULL, 0, 'a', 'b'),
(54, 2, 13, 2, 'PILSEN', 'HELADA,A TIEMPO', NULL, 0, 'a', 'b'),
(55, 2, 13, 2, 'CUSQUEÑA TRIGO', 'HELADA', NULL, 0, 'a', 'b'),
(56, 2, 13, 2, 'CUSQUEÑA DORADA', '', NULL, 0, 'a', 'b'),
(57, 2, 13, 2, 'CUSQUEÑA NEGRA', '', NULL, 0, 'a', 'b'),
(58, 2, 13, 2, 'CORONA', 'HIELO, HELADA', NULL, 0, 'a', 'b'),
(59, 2, 13, 2, 'PALM (SIN ALCOHOL)', '', NULL, 0, 'a', 'b'),
(60, 2, 13, 2, 'LA TRAPPE DUBBEL', '', NULL, 0, 'a', 'b'),
(61, 2, 13, 2, 'CORNET SMOKED', 'HELADA', NULL, 0, 'a', 'b'),
(62, 2, 13, 2, 'QUEIROLO BORGOÑA', '', NULL, 0, 'a', 'b'),
(63, 2, 13, 2, 'QUEIROLO ROSE', '', NULL, 0, 'a', 'b'),
(64, 2, 13, 2, 'VIÑA VIEJA BORGOÑA', '', NULL, 0, 'a', 'b'),
(65, 2, 13, 2, 'VIÑA VIEJA ROSE', '', NULL, 0, 'a', 'b'),
(66, 2, 13, 2, 'TABERNERO BORGOÑA', '', NULL, 0, 'a', 'b'),
(67, 2, 13, 2, 'TABERNERO ROSE', '', NULL, 0, 'a', 'b'),
(68, 2, 13, 2, 'TACAMA TINTO', '', NULL, 0, 'a', 'b'),
(69, 2, 13, 2, 'TACAMA ROSE', '', NULL, 0, 'a', 'b'),
(70, 2, 13, 2, 'CASILLERO DEL DIABLO', 'MALBEC', NULL, 0, 'a', 'b'),
(71, 2, 13, 2, 'NAVARRO CORREA', '', NULL, 0, 'a', 'b'),
(72, 2, 13, 2, 'MARTIN MILLER', '', NULL, 0, 'a', 'b'),
(73, 2, 12, 2, 'SILENT POOL (GIN)', '', NULL, 0, 'a', 'b'),
(74, 2, 19, 2, 'JAGEMEISTER 750ML', '', NULL, 0, 'a', 'b'),
(75, 2, 19, 2, 'BAILEYS', '', NULL, 0, 'a', 'b'),
(76, 2, 13, 2, 'MARACUY', '', NULL, 0, 'a', 'b'),
(77, 2, 13, 2, 'AQARA AGAVE', '', NULL, 0, 'a', 'b'),
(78, 2, 12, 2, 'JW. RED LABEL', 'EN LAS ROCAS ', NULL, 0, 'a', 'b'),
(79, 2, 12, 2, 'JW. BLACK LABEL', '', NULL, 0, 'a', 'b'),
(80, 2, 12, 2, 'JW. DOUBLE LABEL', '', NULL, 0, 'a', 'b'),
(81, 2, 12, 2, 'JW. BLUE LABEL', '', NULL, 0, 'a', 'b'),
(82, 2, 12, 2, 'JW. GOLD LABEL', '', NULL, 0, 'a', 'b'),
(83, 2, 12, 2, 'JW. GREEEN LABEL', '', NULL, 0, 'a', 'b'),
(84, 2, 12, 2, 'JW. BLUE LABEL GHOST AND RARE', '', NULL, 0, 'a', 'b'),
(85, 2, 12, 2, 'MAKERS MARK', '', NULL, 0, 'a', 'b'),
(86, 2, 12, 2, 'CHIVAS REGAL 12 AÑOS', '', NULL, 0, 'a', 'b'),
(87, 2, 12, 2, 'CHIVAS REGAL EXTRA', '', NULL, 0, 'a', 'b'),
(88, 2, 12, 2, 'JACK DANIELS', '', NULL, 0, 'a', 'b'),
(89, 2, 19, 2, 'JIM BEAM WHITE', '', NULL, 0, 'i', 'b'),
(90, 2, 12, 2, 'JIM BEAM BLACK', '', NULL, 0, 'a', 'b'),
(91, 2, 12, 2, 'GLENLIVET 15 AÑOS', '', NULL, 0, 'a', 'b'),
(92, 2, 13, 2, 'JAMESON', '', NULL, 0, 'a', 'b'),
(93, 2, 12, 2, 'GLENFIDDICH 15 AÑOS', '', NULL, 0, 'a', 'b'),
(94, 2, 12, 2, 'GLENDDICH 18 AÑOS', '', NULL, 0, 'a', 'b'),
(95, 2, 12, 2, 'MACALLAN 15 AÑOS', '', NULL, 0, 'a', 'b'),
(96, 2, 12, 2, 'WOODFORD RESERVE', '', NULL, 0, 'a', 'b'),
(97, 2, 12, 2, 'DALMORE KING ALEXANDER III', '', NULL, 0, 'a', 'b'),
(98, 2, 19, 2, 'JOSE CUERVO RUBIO (TEQUILA)', '', NULL, 0, 'i', 'b'),
(99, 2, 12, 2, 'JOSE CUERVO REPOSADO (TEQUILA)', '', NULL, 0, 'a', 'b'),
(100, 2, 12, 2, 'HERRADURA (TEQUILA)', '', NULL, 0, 'a', 'b'),
(101, 2, 12, 2, 'CUERVO 18DCI (TEQUILA)', '', NULL, 0, 'a', 'b'),
(102, 2, 12, 2, 'BARSOL PURO MOSCATEL (PISCO)', '', NULL, 0, 'a', 'b'),
(103, 2, 12, 2, 'BARSOL PURO ITALIA (PISCO)', '', NULL, 0, 'a', 'b'),
(104, 2, 12, 2, 'BARSOL PURO TORENTEL (PISCO)', '', NULL, 0, 'a', 'b'),
(105, 2, 12, 2, 'BARASOL ACHOLADO (PISCO)', '', NULL, 0, 'a', 'b'),
(106, 2, 12, 2, 'BARSOL MOSTO VERDE ITALIA (PISCO)', '', NULL, 0, 'a', 'b'),
(107, 2, 12, 2, 'BARSOL MOSTO VERDE TORENTEL (PISCO)', '', NULL, 0, 'a', 'b'),
(108, 2, 12, 2, 'TABERNERO BOTIJA (PISCO)', '', NULL, 0, 'a', 'b'),
(109, 2, 12, 2, 'CUATRO GALLOS QUEBRANTA ACHOLADO', 'ACHOLADO', NULL, 0, 'a', 'b'),
(110, 2, 12, 2, 'PORTON (PISCO)', '', NULL, 0, 'a', 'b'),
(111, 2, 12, 2, 'ABUELO AÑEJO (RON)', '', NULL, 0, 'a', 'b'),
(112, 2, 12, 2, 'ABUELO 7 AÑOS (RON)', '', NULL, 0, 'a', 'b'),
(113, 2, 12, 2, 'ABUELO 12 AÑOS (RON)', '', NULL, 0, 'a', 'b'),
(114, 2, 12, 2, 'ABUELO 15 AÑOS (RON)', '', NULL, 0, 'a', 'b'),
(115, 2, 12, 2, 'FLOR DE CAÑA 4 AÑOS (RON)', '', NULL, 0, 'a', 'b'),
(116, 2, 12, 2, 'FLOR DE CAÑA 7 AÑOS (RON)', '', NULL, 0, 'a', 'b'),
(117, 2, 12, 2, 'FLOR DE CAÑA 12 AÑOS (RON)', '', NULL, 0, 'a', 'b'),
(118, 2, 12, 2, 'HAVAN CLUB 7 AÑOS (RON)', '', NULL, 0, 'a', 'b'),
(119, 2, 12, 2, 'KRAKEN (RON)', '', NULL, 0, 'a', 'b'),
(120, 2, 12, 2, 'MILLONARIO 15 AÑOS (RON)', '', NULL, 0, 'a', 'b'),
(121, 2, 12, 2, 'XACAPA XO (RON)', '', NULL, 0, 'a', 'b'),
(122, 2, 12, 2, 'PLANTATION 3 STAR (RON)', '', NULL, 0, 'a', 'b'),
(123, 2, 12, 2, 'PLANTATION DARK (RON)', '', NULL, 0, 'a', 'b'),
(124, 2, 12, 2, 'PLANTATION XO (RON)', '', NULL, 0, 'a', 'b'),
(125, 2, 12, 2, 'APPLETON STATE SIGNATURE (RON)', '', NULL, 0, 'a', 'b'),
(126, 2, 12, 2, 'APPLETON STATTE 8 AÑOS (RON)', '', NULL, 0, 'a', 'b'),
(127, 2, 12, 2, 'SKYY (VODKA)', '', NULL, 0, 'a', 'b'),
(128, 2, 12, 2, 'SMIRNOFF NEUTRO (VODKA)', '', NULL, 0, 'a', 'b'),
(129, 2, 12, 2, 'SMIRNOFF MANZANA/RASPBERRY (VODKA)', 'MANZANA', NULL, 0, 'a', 'b'),
(130, 2, 12, 2, 'CIROC NEUTRO (VODKA)', '', NULL, 0, 'a', 'b'),
(131, 2, 12, 2, 'CIROC COCO (VODKA)', '', NULL, 0, 'a', 'b'),
(132, 2, 12, 2, 'ABSOLUTE (VODKA)', '', NULL, 0, 'a', 'b'),
(133, 2, 21, 2, 'GREENALS GIN', '', NULL, 0, 'a', 'b'),
(134, 2, 12, 2, 'TANQUERAY (GIN)', '', NULL, 0, 'a', 'b'),
(135, 2, 21, 2, 'LONDON DRY N 3', '', NULL, 0, 'a', 'b'),
(136, 2, 12, 2, 'DEEFEATER (GIN)', '', NULL, 0, 'a', 'b'),
(137, 2, 21, 2, 'HENDRICKS (GIN)', '', NULL, 0, 'a', 'b'),
(138, 2, 12, 2, 'BROKMANS (GIN)', '', NULL, 0, 'a', 'b'),
(139, 2, 21, 2, 'NORDES GIN', '', NULL, 0, 'a', 'b'),
(140, 1, 10, 2, 'HOJA DE COCA', '3X2,MANGO MARACUYA', NULL, 0, 'a', 'b'),
(141, 1, 10, 2, 'MANGO DE MARACUYA', '3X2,COCA CON MARACUYA,TUNA ROJA,MANGO MARACUYA,COMANDA DE PRUEBA,EUCALIPTO,MUÑA CON TUMBO,HOJA DE COCA', NULL, 0, 'a', 'b'),
(142, 1, 10, 2, 'QARAY', '', NULL, 0, 'a', 'b'),
(143, 1, 10, 2, 'MUÑA CON TUMBO', 'EUCALIPTO ,3X2', NULL, 0, 'a', 'b'),
(144, 1, 10, 2, 'EUCALIPTO', 'NARANJA,3X2, EUCALIPTO ,MARACUYA,POCO HIELO ', NULL, 0, 'a', 'b'),
(145, 1, 11, 2, 'MOJITO DE CRISTAL', 'MÁS ALCOHOL', NULL, 0, 'a', 'b'),
(146, 1, 11, 2, 'VIOLET CLUB', '', NULL, 0, 'a', 'b'),
(147, 1, 1, 1, 'COMBO ORIGINAL', 'CÓCTEL VIRGEN ', NULL, 0, 'a', 'a'),
(148, 1, 1, 1, 'COMBO MIX', 'CON NUGGETS,CON 4ALITAS CÓCTEL VIRGE', NULL, 0, 'a', 'a'),
(149, 1, 1, 1, 'COMBO DUO MIX', 'NAGGETS,ALITAS,NUGGETS,2 ALITAS Y 2 NUGGETS', NULL, 0, 'a', 'a'),
(150, 1, 1, 1, 'COMBO FESTIVAL', 'PARA LLEVAR ', NULL, 0, 'a', 'a'),
(151, 1, 1, 1, 'COMBO SUPER MIX ', '', NULL, 0, 'a', 'a'),
(152, 1, 1, 1, 'COMBO POP CORN', 'PRUEBA DE COMANDA', NULL, 0, 'a', 'a'),
(153, 1, 14, 1, 'AMERICANA - 2 VASOS DE GASEOSA', 'MIXTA', NULL, 0, 'a', 'b'),
(154, 1, 14, 1, 'HAWAINA - 2 VASOS DE GASEOSA', '½ AMÉRICANA ', NULL, 0, 'a', 'b'),
(155, 1, 14, 1, 'VEGGIE - 2 VASOS DE GASEOSOSA', '', NULL, 0, 'a', 'b'),
(156, 1, 15, 1, 'ALITAS CRIOLLAS', 'PICANTES,PARA LLEVAR,MITAD GLASEADO ,MITAD BBQ,MITAD PIERNITAS ,MITAD PICANTES', NULL, 0, 'a', 'b'),
(157, 1, 15, 1, 'ALITAS GLASEADAS', '', NULL, 0, 'a', 'b'),
(158, 1, 15, 1, 'ALITAS BBQ', 'MIXTO ,LLEVAR ,MITAD CRIOLLAS ,PICANTE', NULL, 0, 'a', 'b'),
(159, 1, 15, 1, 'PIERNITAS DE POLLO', 'BBQ,SALSA APARTE DE BBQ,PICANTES,PARA LLEVAR ,MITAD BBQ', NULL, 0, 'a', 'b'),
(160, 1, 15, 1, '15 NUGGETS POLLO', '', NULL, 0, 'a', 'b'),
(161, 1, 15, 1, '15 TEQUEÑOS', 'PARA LLEVAR', NULL, 0, 'a', 'b'),
(162, 1, 15, 1, '7 NUGGETS DE POLLO', 'PARA LLEVAR ', NULL, 0, 'a', 'b'),
(163, 1, 16, 1, 'SALCHIPAPA CLASICA', '', NULL, 0, 'a', 'b'),
(164, 1, 16, 1, 'SLACHIPAPA REVUELTA', 'PARA LLEVAR', NULL, 0, 'a', 'b'),
(165, 1, 16, 1, 'SALCHIPOLLO', '', NULL, 0, 'a', 'b'),
(166, 1, 16, 1, 'SALCHIPOLLO REVUELTO', '', NULL, 0, 'a', 'b'),
(167, 1, 16, 1, 'SALCHIALITAS', 'PARA LLEVAR,PICANTES', NULL, 0, 'a', 'b'),
(168, 1, 17, 1, 'HOT DOG', '', NULL, 0, 'a', 'b'),
(169, 1, 17, 1, 'POLLO', '', NULL, 0, 'a', 'b'),
(170, 1, 17, 1, 'MIXTO', 'LLEVAR ', NULL, 0, 'a', 'b'),
(171, 1, 17, 1, 'CHORIZO', '', NULL, 0, 'a', 'b'),
(172, 1, 17, 1, 'HAWAIANO', '', NULL, 0, 'a', 'b'),
(173, 1, 18, 2, 'DILUVIO', '', NULL, 0, 'a', 'b'),
(174, 1, 18, 2, 'SEDUCCIÓN', '', NULL, 0, 'a', 'b'),
(175, 1, 18, 2, 'CUCARACHA', '', NULL, 0, 'a', 'b'),
(176, 1, 18, 2, 'HEMORRAGIA ZOMBIE', '', NULL, 0, 'a', 'b'),
(177, 1, 18, 2, 'MEDUSA', '', NULL, 0, 'a', 'b'),
(178, 1, 18, 2, 'TEQUILA', '', NULL, 0, 'a', 'b'),
(179, 1, 13, 2, 'CORONA URBAN BLUE', '', NULL, 0, 'i', 'b'),
(180, 1, 13, 2, 'CORONA URBAN RED', '', NULL, 0, 'i', 'b'),
(181, 1, 13, 2, 'CORONA URBAN BLACK', '', NULL, 0, 'i', 'b'),
(182, 1, 6, 2, 'NARANJITA TRADICIONAL', 'WISKY ,VINO,RON,PISCO,PRUEBA,CARGADO,SIN ALCOHOL,2 PISCO,2 WISKY,2 RON,2 VINO,4WHISKY,BAJO ,3 RON,1 BAJO ,BIEN CALIENTE ', NULL, 0, 'a', 'b'),
(183, 1, 19, 2, 'CALIENTE TROPICO DE FRUTOS ROJOS', 'RON ABUELO AÑEJO,PISCO PURO QUEBRANTA BARSOL', NULL, 0, 'a', 'b'),
(184, 1, 19, 2, 'CALIENTE DINAMO', '3X2', NULL, 0, 'a', 'b'),
(185, 1, 19, 2, 'CALIENTE MARACUYA', 'PISCO', NULL, 0, 'a', 'b'),
(186, 1, 19, 2, 'NARANJITA CON VINO', '3X2', NULL, 0, 'a', 'b'),
(187, 1, 19, 2, 'CALIENTE CON MAZERADOS', 'MUÑA,MENTA,EUCALIPTO', NULL, 0, 'a', 'b'),
(188, 1, 19, 2, 'PISCO SOUR Y VARIACIONES', 'SOLO MARACUYÁ,MUÑA,CLÁSICO,CAFE', NULL, 0, 'a', 'b'),
(189, 1, 18, 2, 'NEGRONI', '', NULL, 0, 'a', 'b'),
(190, 1, 19, 2, 'SMIRNOFF ROJO NEUTRO', '', NULL, 0, 'a', 'b'),
(191, 1, 19, 2, 'MARTINI', '', NULL, 0, 'a', 'b'),
(192, 1, 19, 2, 'MANHATAN', '', NULL, 0, 'a', 'b'),
(193, 1, 19, 2, 'OLD MASHINED', '', NULL, 0, 'a', 'b'),
(194, 1, 19, 2, 'MARGARITA', '', NULL, 0, 'a', 'b'),
(195, 1, 19, 2, 'HANKY PANKY 7', '', NULL, 0, 'a', 'b'),
(196, 1, 18, 2, 'MACHU PICCHU (LOS DE SIEMPRE)', '', NULL, 0, 'a', 'b'),
(197, 1, 19, 2, 'PISCO PUNCH', '', NULL, 0, 'a', 'b'),
(198, 1, 19, 2, 'PANKILLER', '', NULL, 0, 'a', 'b'),
(199, 1, 18, 2, 'CAIPIRINHA (LOS DE SIEMPRE)', '51', NULL, 0, 'a', 'b'),
(200, 1, 18, 2, 'PIÑA COLADA (LOS DE SIEMPRE)', 'SIN ALCOHOL,POCO ALCOHOL', NULL, 0, 'a', 'b'),
(201, 1, 18, 2, 'DAIKIRI CLASICO(LOS DE SIEMPRE)', 'DURASNO ,FRESA,CLASICO', NULL, 0, 'a', 'b'),
(202, 1, 19, 2, 'CAPITAN', '', NULL, 0, 'a', 'b'),
(203, 1, 19, 2, 'APPLE MARTINI', '', NULL, 0, 'a', 'b'),
(204, 1, 18, 2, 'AMOR EN LLAMAS (LOS DE SIEMPRE)', '', NULL, 0, 'a', 'b'),
(205, 1, 19, 2, 'ALEXANDER', '', NULL, 0, 'a', 'b'),
(206, 1, 19, 2, 'SUBMARINO', '', NULL, 0, 'a', 'b'),
(207, 1, 20, 1, 'HAMBURGUESA CLASICA', 'PARTELO DE LA MITAD', NULL, 0, 'a', 'b'),
(208, 1, 19, 2, 'MARGARITA BULDOG', 'PRUEBA DE COMANDA (IGNORA)', NULL, 0, 'a', 'b'),
(209, 1, 19, 2, 'BLUEBERRY TONIC', '', NULL, 0, 'a', 'b'),
(210, 1, 19, 2, 'INKA SOUL', '', NULL, 0, 'a', 'b'),
(211, 1, 19, 2, 'CHILCANO Y VARIACIONES', 'MARACUYÁ ,MUÑA,CLASICO,3×2,EUCALIPTO,COCA CON MARACUYA,EUCALIPTO C NARANJA', NULL, 0, 'a', 'b'),
(212, 1, 18, 2, 'CUBA LIBRE (LOS DE SIEMPRE)', '', NULL, 0, 'a', 'b'),
(213, 1, 18, 2, 'MOJITO CLASICO/MARACUYA (LOS DE SIEMPRE)', 'CLASICO,MARACUYA,POCO HIELO ', NULL, 0, 'a', 'b'),
(214, 1, 19, 2, 'SOLSTICIO', '', NULL, 0, 'a', 'b'),
(215, 1, 19, 2, 'GIN TONIC', '', NULL, 0, 'a', 'b'),
(216, 1, 18, 2, 'KAMIKASE (LOS DE SIEMPRE)', '', NULL, 0, 'a', 'b'),
(217, 1, 19, 2, 'MOJITO MARTINI', '', NULL, 0, 'a', 'b'),
(218, 1, 19, 2, 'MOSCOW BLUE', '', NULL, 0, 'a', 'b'),
(219, 1, 19, 2, 'DARK & STORMY', '', NULL, 0, 'a', 'b'),
(220, 1, 19, 2, 'BESTIA NOCTURNA', '', NULL, 0, 'a', 'b'),
(221, 1, 19, 2, 'FLOWER TONIC', '', NULL, 0, 'a', 'b'),
(222, 1, 19, 2, 'CITRONIC', '', NULL, 0, 'a', 'b'),
(223, 1, 19, 2, 'TONICA AMAZONICA', '', NULL, 0, 'a', 'b'),
(224, 1, 19, 1, 'PORCION DE PAPAS', '', NULL, 0, 'a', 'b'),
(225, 1, 19, 1, 'ENSALADA', '', NULL, 0, 'a', 'b'),
(226, 1, 19, 2, 'SATANAS', '', NULL, 0, 'a', 'b'),
(227, 1, 19, 2, 'BLACK RUSIAN', '', NULL, 0, 'a', 'b'),
(228, 1, 19, 2, 'ALGARROBINA', '', NULL, 0, 'a', 'b'),
(229, 1, 19, 2, 'JAGER BOOM', '', NULL, 0, 'a', 'b'),
(230, 1, 19, 2, 'PERFUME DE SAVIA', '', NULL, 0, 'a', 'b'),
(231, 1, 19, 2, 'PISCINA', 'MORFEO,SHOTS BAYLES,LAGUNA AZUL', NULL, 0, 'a', 'b'),
(232, 1, 19, 2, 'SHRUNKEN SKULL', '', NULL, 0, 'i', 'b'),
(233, 1, 19, 2, 'JUNGLE BIRD', '', NULL, 0, 'a', 'b'),
(234, 1, 11, 2, 'SEROTONINA', '', NULL, 0, 'a', 'b'),
(235, 1, 21, 2, 'AMAZONIAN (GIN)', '', NULL, 0, 'a', 'b'),
(236, 1, 21, 2, 'MARTIN MILLERS GIN', '', NULL, 0, 'a', 'b'),
(237, 1, 21, 2, 'BROCKMANS (GIN)', '', NULL, 0, 'a', 'b'),
(238, 1, 21, 2, 'SILENT POOL GIN', '', NULL, 0, 'a', 'b'),
(239, 1, 21, 2, 'HENKES (GIN)', '', NULL, 0, 'a', 'b'),
(240, 1, 21, 2, 'GREENLAS WILD BERRY', '', NULL, 0, 'a', 'b'),
(241, 1, 6, 2, 'NARANJITA HERBAL', 'EUCALIPTO,MUÑA ,CARGADO ,MENTA,BAJO DE ALCOHOL,2 EUCALIPTO ,MAS CALIENTE,1 SIN ALCOHOL MUÑA', NULL, 0, 'a', 'b'),
(242, 1, 6, 2, 'MULLED WINE', 'VINO TINTO ,COMIDA EVENTO,PISCO QUEBRANTA', NULL, 0, 'a', 'b'),
(243, 1, 20, 1, 'HAMBUERGUESAS CRISPY', 'PARA LLEVAR', NULL, 0, 'a', 'b'),
(244, 1, 20, 1, 'HAMBURGUESAS ROYAL KING', '', NULL, 0, 'a', 'b'),
(245, 1, 12, 2, 'GREENALLS (GIN)', '', NULL, 0, 'a', 'b'),
(246, 1, 12, 2, 'LONDON Nº3 (GIN)', '', NULL, 0, 'a', 'b'),
(247, 1, 12, 2, 'BEEFEATER (GIN)', '', NULL, 0, 'a', 'b'),
(248, 1, 12, 2, 'HENDRICHS (GIN)', '', NULL, 0, 'a', 'b'),
(249, 1, 12, 2, 'NORDES(GIN)', '', NULL, 0, 'a', 'b'),
(250, 1, 12, 2, 'MARTIN MILLER (GIN)', '', NULL, 0, 'a', 'b'),
(251, 1, 12, 2, 'OLD FORESTER', '', NULL, 0, 'a', 'b'),
(252, 1, 12, 2, 'OLD PAR 12 AÑOS', '', NULL, 0, 'a', 'b'),
(253, 1, 12, 2, 'CLASE AZUL (TEQUILA)', '', NULL, 0, 'a', 'b'),
(254, 1, 12, 2, 'LA DAMA (TEQUILA)', '', NULL, 0, 'a', 'b'),
(255, 1, 19, 2, 'MATACUY', '', NULL, 0, 'a', 'b'),
(256, 1, 9, 2, 'PINK SODA MR PERKINS', '', NULL, 0, 'a', 'b'),
(257, 1, 8, 2, 'VIRGO (INFUSIÓN DE TE NEGRO)', '', NULL, 0, 'a', 'b'),
(258, 1, 22, 2, 'ORGASMO MULTIPLE', '', NULL, 0, 'a', 'b'),
(259, 1, 22, 2, 'COSMO PERÚ', '', NULL, 0, 'a', 'b'),
(260, 1, 22, 2, 'PERFUME DE SABIA', '', NULL, 0, 'a', 'b'),
(261, 1, 22, 2, 'CITRONIC', '', NULL, 0, 'a', 'b'),
(262, 1, 22, 2, 'EXPRESO MARTINI', '', NULL, 0, 'a', 'b'),
(263, 1, 22, 2, 'SATANÁS', '', NULL, 0, 'a', 'b'),
(264, 1, 22, 2, 'MANHATAN', '', NULL, 0, 'a', 'b'),
(265, 1, 22, 2, 'NEGRONI', '', NULL, 0, 'a', 'b'),
(266, 1, 22, 2, 'MARGARITA BULL DOG', '', NULL, 0, 'i', 'b'),
(267, 1, 22, 2, 'MARGARITA', '', NULL, 0, 'a', 'b'),
(268, 1, 22, 2, 'NEW YOUR SOUR', '', NULL, 0, 'a', 'b'),
(269, 1, 22, 2, 'SOLSTICIO', '', NULL, 0, 'a', 'b'),
(270, 1, 22, 2, 'KENTUKEY MULE', '', NULL, 0, 'a', 'b'),
(271, 1, 22, 2, 'PISCO PUNCH', '', NULL, 0, 'a', 'b'),
(272, 1, 22, 2, 'BLUEBERRY TONIC', '', NULL, 0, 'a', 'b'),
(273, 1, 22, 2, 'BESTIA NOCTURNA', '', NULL, 0, 'a', 'b'),
(274, 1, 22, 2, 'MOJITO MARTINI', '', NULL, 0, 'a', 'b'),
(275, 1, 18, 2, 'RUSO BLACO/NEGRO (LOS DE SIEMPRE)', 'BLANCO ,NEGRO', NULL, 0, 'a', 'b'),
(276, 1, 10, 2, 'OTROS SABORES(MARACUYA, TUNA ROJA,CLASICO,CAN', 'MARACUYA,EUCALIPTO CON NARANJA,COCA CON MARACUYA,3X2,CLASICO,MUÑA,TUNA ROJA,MUÑA CON TUMBO,MANGO MARACUYA ,HOJA DE COCA,CANELA,EUCALIPTO ', NULL, 0, 'a', 'b'),
(277, 1, 19, 2, 'CORONA URBAN ', 'BLUE ,RED,BLACK', NULL, 0, 'a', 'b'),
(278, 2, 19, 2, 'EVERES', '', NULL, 0, 'a', 'b'),
(279, 1, 19, 2, 'LIMONADA FROZEN', '', NULL, 0, 'a', 'b'),
(280, 1, 23, 1, '12 ALITAS O PIERNITAS 2 CUSQUEÑAS DORADAS', 'COMBINADO ', NULL, 0, 'a', 'b'),
(281, 1, 2, 2, 'COCTELES CON PISCO', '', NULL, 0, 'a', 'b'),
(282, 1, 2, 2, 'BITTER RICANTI', '', NULL, 0, 'i', 'b'),
(283, 1, 23, 2, 'CHILCANO 3X2', 'MANGO MARACUYA,HOJA DE COCA,TUNA ROJA ,CLASICO,EUCALIPTO ,MARACUYA,MUÑA TUMBO,2 DE MANGO C MARACUYA ', NULL, 0, 'a', 'b'),
(284, 1, 23, 2, 'NARANJITA 3X2', 'PISCO,RON,WHISKY,2 PISCO,2 VINO,2 RON,3 WHISKY ,3 PISCO,2 WHISKY,VINO,3 RON,CARGADO', NULL, 0, 'a', 'b'),
(285, 1, 23, 2, 'PUNCH AMAZONICO', '', NULL, 0, 'a', 'b'),
(286, 1, 23, 2, 'PREVIA FASHIONED', '', NULL, 0, 'a', 'b'),
(287, 1, 23, 2, 'POSIMA BITTER', '', NULL, 0, 'a', 'b'),
(288, 1, 13, 2, 'BUDWEISER', '', NULL, 0, 'a', 'b'),
(289, 1, 23, 2, '6×5 PILSEN', '', NULL, 0, 'a', 'b'),
(290, 1, 23, 2, '6X5 BUDWEISER', '', NULL, 0, 'a', 'b');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_producto_catg`
--

CREATE TABLE `tm_producto_catg` (
  `id_catg` int(11) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `delivery` int(1) NOT NULL DEFAULT 0,
  `orden` int(11) NOT NULL DEFAULT 100,
  `imagen` varchar(200) NOT NULL DEFAULT 'default.png',
  `estado` varchar(1) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tm_producto_catg`
--

INSERT INTO `tm_producto_catg` (`id_catg`, `descripcion`, `delivery`, `orden`, `imagen`, `estado`) VALUES
(1, 'COMBOS', 0, 0, 'default.png', 'a'),
(2, 'ALQUIMIA CON PERSONALIDAD', 0, 1, 'default.png', 'a'),
(3, 'NEO TIKI', 0, 2, 'default.png', 'a'),
(4, 'MIXOLOGÍA CON BARSOL', 0, 3, 'default.png', 'a'),
(5, 'VINTAGE TIKI', 0, 4, 'default.png', 'a'),
(6, 'CALIENTES DE NARANJA CON NOTAS ESPECIADAS', 0, 5, 'default.png', 'a'),
(7, 'LOS ERMITAÑOS', 0, 6, 'default.png', 'a'),
(8, 'ALQUIMIA VIRGEN', 0, 7, 'default.png', 'a'),
(9, 'SIDERS/MIXERS', 0, 8, 'default.png', 'a'),
(10, 'CHILCANOS Y SOURS', 0, 8, 'default.png', 'a'),
(11, 'MIXOLOGÍA EVOLUTIVO', 0, 10, 'default.png', 'a'),
(12, 'COLECCION PRIVADA BOTELLAS Y LAS ROCAS', 0, 11, 'default.png', 'a'),
(13, 'BURBUJAS Y VINOS', 0, 12, 'default.png', 'a'),
(14, 'PIZZAS', 0, 13, 'default.png', 'a'),
(15, 'PIQUEOS', 0, 14, 'default.png', 'a'),
(16, 'SALCHIPAPAS', 0, 15, 'default.png', 'a'),
(17, 'SANDWICHS', 0, 16, 'default.png', 'a'),
(18, 'EL MITICO', 0, 100, 'default.png', 'a'),
(19, 'OTROS', 0, 100, 'default.png', 'a'),
(20, 'HAMBURGUESAS', 0, 100, 'default.png', 'a'),
(21, 'PREVIA TONIC', 0, 100, 'default.png', 'a'),
(22, 'LOS CLÁSICOS', 0, 100, 'default.png', 'a'),
(23, 'PROMOCIÓN ', 0, 100, 'default.png', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_producto_ingr`
--

CREATE TABLE `tm_producto_ingr` (
  `id_pi` int(11) NOT NULL,
  `id_pres` int(11) NOT NULL,
  `id_tipo_ins` int(11) NOT NULL,
  `id_ins` int(11) NOT NULL,
  `id_med` int(11) NOT NULL,
  `cant` float(10,6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_producto_ingr`
--

INSERT INTO `tm_producto_ingr` (`id_pi`, `id_pres`, `id_tipo_ins`, `id_ins`, `id_med`, `cant`) VALUES
(4, 158, 3, 43, 1, 1.000000),
(5, 158, 3, 237, 1, 1.000000),
(6, 158, 3, 307, 1, 1.000000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_producto_pres`
--

CREATE TABLE `tm_producto_pres` (
  `id_pres` int(11) NOT NULL,
  `id_prod` int(11) NOT NULL,
  `cod_prod` varchar(45) NOT NULL,
  `presentacion` varchar(45) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `precio_delivery` decimal(10,2) NOT NULL,
  `receta` int(1) NOT NULL,
  `stock_min` int(11) NOT NULL,
  `impuesto` int(1) NOT NULL,
  `delivery` int(1) NOT NULL DEFAULT 0,
  `margen` int(1) NOT NULL DEFAULT 0,
  `igv` decimal(10,2) NOT NULL,
  `imagen` varchar(200) NOT NULL DEFAULT 'default.png',
  `estado` varchar(1) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tm_producto_pres`
--

INSERT INTO `tm_producto_pres` (`id_pres`, `id_prod`, `cod_prod`, `presentacion`, `descripcion`, `precio`, `precio_delivery`, `receta`, `stock_min`, `impuesto`, `delivery`, `margen`, `igv`, `imagen`, `estado`) VALUES
(1, 5, 'ELCOC0', 'COCTEL', '', '29.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(2, 4, 'ELCOC0', 'COCTEL', '', '23.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(3, 3, 'ELCOC0', 'COCTEL', '', '23.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(4, 2, 'LACOC0', 'COCTEL', '', '23.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(5, 1, 'ELCOC0', 'COCTEL', '', '23.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(6, 9, 'ELCOC0', 'COCTEL', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(7, 8, 'STCOC0', 'COCTEL', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(8, 7, 'SUCOC0', 'COCTEL', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(9, 6, 'YOCOC0', 'COCTEL', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(10, 12, 'FLCOC0', 'COCTEL', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(11, 11, 'CHCOC0', 'COCTEL', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(12, 10, 'WACOC0', 'COCTEL', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(13, 18, 'SHCOC0', 'COCTEL', '', '28.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(14, 17, 'AMCOC0', 'COCTEL', '', '28.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(15, 16, 'SCCOC0', 'COCTEL', '', '28.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(16, 15, 'MACOC0', 'COCTEL', '', '28.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(17, 14, 'ZOCOC0', 'COCTEL', '', '28.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(18, 13, 'MACOC0', 'COCTEL', '', '28.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(19, 19, 'NACAL0', 'CALIENTE', '', '20.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(20, 20, 'NACAL20', 'CALIENTE', '', '20.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(21, 21, 'NACAL0', 'CALIENTE', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(22, 22, 'CACAL0', 'CALIENTE', '', '20.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(23, 23, 'CACAL0', 'CALIENTE', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(24, 24, 'MACAL0', 'CALIENTE', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(25, 25, 'ELCAL0', 'CALIENTE', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(26, 30, 'AGEL 0', 'EL VIRGO', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(27, 29, 'MEEL 0', 'EL VIRGO', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(28, 28, 'SYEL 0', 'EL VIRGO', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(29, 27, 'ZUEL 0', 'EL VIRGO', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(30, 26, 'ZUEL 0', 'EL VIRGO', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(31, 31, 'EMLA 0', 'LA PITA', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(32, 32, 'INLA 0', 'LA PITA', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(33, 33, 'ZULA 0', 'LA PITA', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(34, 34, 'ZULA 0', 'LA PITA', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(35, 35, 'CAMOR0', 'MORFEO', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(36, 36, 'ZUMOR0', 'MORFEO', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(37, 37, 'ALMOR0', 'MORFEO', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(38, 38, 'AGMOR0', 'MORFEO', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(39, 39, 'CREL 0', 'EL MANUEL', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(40, 40, 'ZUEL 0', 'EL MANUEL', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(41, 41, 'AGEL 0', 'EL MANUEL', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(42, 42, 'COGAS5', 'GASEOSA BOTELLA PERSONAL', '', '5.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(43, 43, 'INGAS5', 'GASEOSA BOTELLA PERSONAL', '', '5.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(44, 44, 'GUGAS15', 'GASEOSA BOTELLA 1 LITRO', '', '15.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(45, 45, 'GIGAS15', 'GASEOSA BOTELLA 1.5 LITROS', '', '10.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(46, 46, 'NANEC15', 'NECTAR BOTELLA', '', '15.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(47, 47, 'PINEC15', 'NECTAR BOTELLA', '', '15.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(48, 48, 'TOAGU0', 'AGUA TONICA', '', '10.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(49, 49, 'TOAGU0', 'AGUA TONICA', '', '10.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(50, 50, 'TOAGU0', 'AGUA TONICA', '', '10.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(51, 51, 'SAAGU0', 'AGUA CON GAS', '', '5.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(52, 51, 'SAAGU0', 'AGUA SIN GAS', '', '5.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(53, 52, 'RELAT0', 'LATA', '', '12.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(54, 53, 'RICHA0', 'CHAMPAGNE', '', '170.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(55, 54, 'PICER0', 'CERVEZA', '', '15.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(56, 55, 'CUCER0', 'CERVEZA', '', '15.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(57, 56, 'CUCER0', 'CERVEZA', '', '9.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(58, 57, 'CUCER0', 'CERVEZA', '', '15.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(59, 58, 'COCER0', 'CERVEZA', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(60, 59, 'PACER0', 'CERVEZA', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(61, 60, 'LACER0', 'CERVEZA', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(62, 61, 'COCER0', 'CERVEZA', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(63, 62, 'QUVIN0', 'VINO', '', '70.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(64, 63, 'QUVIN0', 'VINO', '', '70.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(65, 64, 'VIVIN0', 'VINO 70', '', '70.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(66, 65, 'VIVIN0', 'VINO', '', '70.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(67, 66, 'TAVIN0', 'VINO', '', '75.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(68, 67, 'TAVIN0', 'VINO', '', '75.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(69, 68, 'TAVIN0', 'VINO', '', '75.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(70, 69, 'TAVIN0', 'VINO', '', '75.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(71, 70, 'CAVIN0', 'VINO', '', '100.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(72, 71, 'NAVIN0', 'VINO', '', '120.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(73, 72, 'MAVIN0', 'VINO', '', '360.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(74, 73, 'SIBOT400', 'BOTELLA', '', '400.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(75, 74, 'JAOTR0', 'OTROS', '', '170.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(76, 75, 'BAOTR0', 'OTROS', '', '190.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(77, 76, 'MAOTR0', 'OTROS', '', '220.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(78, 77, 'AQBOT220', 'BOTELLA', '', '220.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(79, 78, 'JWWHI0', 'WHISKY', '', '160.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(80, 79, 'JWWHI280', 'WHISKY', '', '280.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(81, 80, 'JWWHI0', 'WHISKY', '', '330.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(82, 81, 'JWWHI0', 'WHISKY', '', '1600.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(83, 82, 'JWWHI0', 'WHISKY', '', '600.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(84, 83, 'JWWHI0', 'WHISKY', '', '750.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(85, 84, 'JWWHI0', 'WHISKY', '', '2000.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(86, 85, 'MAWHI0', 'WHISKY', '', '350.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(87, 86, 'CHWHI0', 'WHISKY', '', '220.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(88, 87, 'CHWHI0', 'WHISKY', '', '280.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(89, 88, 'JAWHI0', 'WHISKY', '', '250.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(90, 89, 'JIWHI0', 'WHISKY', '', '150.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(91, 90, 'JIWHI0', 'WHISKY', '', '240.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(92, 91, 'GLWHI0', 'WHISKY', '', '500.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(93, 92, 'JAWHI0', 'WHISKY', '', '220.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(94, 93, 'GLWHI0', 'WHISKY', '', '900.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(95, 94, 'GLWHI0', 'WHISKY', '', '1000.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(96, 95, 'MAWHI0', 'WHISKY', '', '1500.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(97, 96, 'WOWHI0', 'WHISKY', '', '850.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(98, 98, 'JOBOT0', 'BOTELLA', '', '160.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(99, 98, 'JOONZ17', 'ONZAS', '', '17.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(100, 99, 'JOBOT160', 'BOTELLA', '', '160.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(101, 99, 'JOONZ17', 'ONZAS', '', '17.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(102, 100, 'HEBOT0', 'BOTELLA', '', '290.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(103, 100, 'HEONZ30', 'ONZAS', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(104, 101, 'CUBOT0', 'BOTELLA', '', '290.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(105, 101, 'CUONZ0', 'ONZAS', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(106, 102, 'BABOT0', 'BOTELLA', '', '190.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(107, 103, 'BABOT0', 'BOTELLA', '', '190.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(108, 104, 'BABOT0', 'BOTELLA', '', '190.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(109, 105, 'BABOT0', 'BOTELLA', '', '190.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(110, 106, 'BABOT0', 'BOTELLA', '', '210.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(111, 107, 'BABOT0', 'BOTELLA', '', '210.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(112, 108, 'TABOT0', 'BOTELLA', '', '90.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(113, 109, 'CUBOT0', 'BOTELLA', '', '120.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(114, 110, 'POBOT0', 'BOTELLA', '', '220.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(115, 111, 'ABBOT0', 'BOTELLA', '', '130.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(116, 111, 'ABONZ0', 'ONZAS', '', '15.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(117, 112, 'AB BO0', ' BOTELLA', '', '210.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(118, 112, 'ABONZ0', 'ONZAS', '', '22.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(119, 113, 'ABBOT0', 'BOTELLA', '', '360.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(120, 114, 'ABBOT0', 'BOTELLA', '', '800.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(121, 115, 'FLBOT0', 'BOTELLA', '', '130.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(122, 116, 'FLBOT0', 'BOTELLA', '', '230.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(123, 117, 'FLBOT0', 'BOTELLA', '', '300.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(124, 118, 'HABOT0', 'BOTELLA', '', '260.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(125, 119, 'KRBOT0', 'BOTELLA', '', '190.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(126, 120, 'MIBOT0', 'BOTELLA', '', '350.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(127, 121, 'XABOT0', 'BOTELLA', '', '600.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(128, 122, 'PLBOT0', 'BOTELLA', '', '200.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(129, 123, 'PLBOT0', 'BOTELLA', '', '250.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(130, 124, 'PLBOT0', 'BOTELLA', '', '600.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(131, 125, 'APBOT0', 'BOTELLA', '', '130.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(132, 126, 'APBOT0', 'BOTELLA', '', '220.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(133, 127, 'SKBOT0', 'BOTELLA', '', '120.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(134, 128, 'SMBOT0', 'BOTELLA', '', '100.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(135, 129, 'SMBOT0', 'BOTELLA', '', '110.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(136, 130, 'CIBOT0', 'BOTELLA', '', '220.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(137, 131, 'CIBOT0', 'BOTELLA', '', '240.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(138, 132, 'ABBOT0', 'BOTELLA', '', '130.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(139, 133, 'GRBOT0', 'BOTELLA', '', '150.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(140, 134, 'TABOT0', 'BOTELLA', '', '240.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(141, 135, 'LOBOT0', 'BOTELLA', '', '300.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(142, 136, 'DEBOT0', 'BOTELLA', '', '210.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(143, 137, 'HEBOT0', 'BOTELLA', '', '360.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(144, 138, 'DRBOT0', 'BOTELLA', '', '420.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(145, 139, 'NOBOT0', 'BOTELLA', '', '350.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(146, 144, 'EUCHI0', 'CHILCANO', '', '20.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(147, 143, 'MUCHI0', 'CHILCANO', '', '20.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(148, 142, 'QACHI0', 'CHILCANO', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(149, 141, 'MACHI0', 'CHILCANO', '', '20.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(150, 140, 'HOCHI0', 'CHILCANO', '', '20.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(151, 146, 'VICOC0', 'COCTEL', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(152, 145, 'MOCOC0', 'COCTEL', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(153, 147, 'CO2 P25', '2 PIEZAS DE POLLO, 1 PAPA REGULAR, CHILCANO O', '', '25.90', '0.00', 1, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(154, 148, 'CO2 P33', '2 PIEZAS DE POLLO, 4 ALITAS O NUGGETS, 1 PAPA', '', '33.90', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(155, 149, 'CO4 P42', '4 PIEZAS DE POLLO, 4 ALITAS O NAGGETS, 2 PAPA', '', '42.90', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(156, 150, 'CO3 P45', '3 PIEZAS DE POLLO, 4 NAGGETS, 2 PAPAS GRANDES', '', '45.90', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(157, 151, 'CO6 P50', '6 PIEZAS DE POLLO, 4 ALITAS O NUGGETS, 3 CHIL', '', '50.90', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(158, 152, 'COCHI18', 'CHICHARON POP, 1 PAPA REGULAR, 1 TARTARA', '', '18.90', '0.00', 1, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(159, 153, 'AMPIZ0', 'PIZZA', '', '35.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(160, 154, 'HAPIZ0', 'PIZZA', '', '36.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(161, 155, 'VEPIZ0', 'PIZZA', '', '36.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(162, 156, 'ALCOM0', 'COMPLETO', '', '34.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(163, 156, 'ALMIT0', 'MITAD', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(164, 157, 'ALCOM0', 'COMPELTO', '', '34.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(165, 157, 'ALMIT0', 'MITAD', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(166, 158, 'ALCOM0', 'COMPLETO', '', '34.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(167, 158, 'ALMIT0', 'MITAD', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(168, 159, 'PICOM0', 'COMPLETO', '', '34.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(169, 159, 'PIMIT0', 'MITAD', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(170, 160, '15COM0', 'COMPELTO', '', '32.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(171, 161, '15COM0', 'COMPLETO', '', '23.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(172, 162, '7 COM0', 'COMPLETO', '', '23.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(173, 163, 'CLSAL0', 'SALCHIPAPA', '', '20.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(174, 164, 'RESAL0', 'SALCHIPAPA', '', '23.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(175, 165, 'SASAL0', 'SALCHIPOLLO', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(176, 166, 'SASAL0', 'SALCHIPOLLO', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(177, 167, 'SASAL0', 'SALCHIALITAS', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(178, 168, 'HOSAN0', 'SANDWICHS', '', '10.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(179, 169, 'POSAN0', 'SANDWICHS', '', '12.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(180, 170, 'MISAN0', 'SANDWICHS', '', '12.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(181, 171, 'CHSAN0', 'SANDWICHS', '', '12.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(182, 172, 'HASAN0', 'SANDWICHS', '', '12.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(183, 173, 'DISHO0', 'SHOT', '', '15.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(184, 174, 'SESHO0', 'SHOTS', '', '15.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(185, 175, 'CUSHO0', 'SHOT', '', '15.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(186, 176, 'HESHO0', 'SHOTS', '', '15.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(187, 177, 'MESHO0', 'SHOTS', '', '15.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(188, 178, 'TESHO0', 'SHOTS', '', '16.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(189, 179, 'COCER0', 'CERVEZA', '', '17.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(190, 180, 'COCER0', 'CERVEZAS', '', '17.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(191, 181, 'COCER0', 'CERVEZA', '', '17.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(192, 182, 'CACAL0', 'CALIENTE', '', '15.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(193, 183, 'CACAL0', 'CALIENTE', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(194, 184, 'CACAL0', 'CALIENTE', '', '20.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(195, 185, 'CACAL0', 'CALIENTE', '', '15.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(196, 186, 'CACAL0', 'CALIENTE', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(197, 187, 'CACAL0', 'CALIENTE', '', '15.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(198, 188, 'PICOC0', 'COCTEL', '', '20.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(199, 190, 'SMBOT0', 'BOTELLA', '', '100.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(200, 191, 'MADRY0', 'DRY', '', '23.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(201, 191, 'MACLA0', 'CLASICO', '', '23.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(202, 191, 'MASWE0', 'SWEET', '', '23.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(203, 189, 'NECOC0', 'COCTEL', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'i'),
(204, 192, 'MACOC0', 'COCTEL', '', '23.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(205, 193, 'OLCOC0', 'COCTEL', '', '23.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(206, 194, 'MACOC0', 'COCTEL', '', '23.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(207, 195, 'HACOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(208, 196, 'MACOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(209, 197, 'PICOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(210, 198, 'PACOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(211, 199, 'CACOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(212, 200, 'PICOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(213, 201, 'DACOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(214, 202, 'CACOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(215, 203, 'APCOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(216, 204, 'AMCOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(217, 205, 'ALCOC0', 'COCTEL', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(218, 206, 'SUCOC0', 'COCTEL', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(219, 207, 'HACLA0', 'CLASICA', '', '16.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(220, 208, 'MACOC0', 'COCTEL', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(221, 209, 'BLCOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(222, 210, 'INBOT0', 'BOTELLA', '', '140.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(223, 210, 'INCOC0', 'COCTEL', '', '35.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(224, 211, 'CHCOC0', 'COCTEL', '', '20.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(225, 212, 'CUCOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(226, 213, 'MOCOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(227, 214, 'SOCOC0', 'COCTEL', '', '23.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(228, 215, 'GICOC0', 'COCTEL', '', '23.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(229, 216, 'KACOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(230, 217, 'MOCOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(231, 218, 'MOCOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(232, 219, 'DACOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(233, 220, 'BECOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(234, 221, 'FLCOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(235, 222, 'CICOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(236, 223, 'TOCOC0', 'COCTEL', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(237, 224, 'POPOR0', 'PORCION DE PAPAS', '', '9.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(238, 225, 'ENGUA0', 'GUARNICION', '', '7.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(239, 226, 'SACOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(240, 227, 'BLCOC0', 'COCTEL', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(241, 228, 'ALCOC0', 'COCTEL', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(242, 229, 'JACOC0', 'COCTEL', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(243, 230, 'PECOC0', 'COCTEL', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(244, 231, 'PICOC0', 'COCTEL', '', '22.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(245, 232, 'SHCOC0', 'COCTEL', '', '35.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(246, 233, 'JUCOC0', 'COCTEL', '', '32.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(247, 234, 'SECOC0', 'COCTEL', '', '70.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(248, 137, 'HEONZ0', 'ONZA', '', '35.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(249, 235, 'AMBOT0', 'BOTELLA', '', '320.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(250, 235, 'AMONZ0', 'ONZA', '', '35.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(251, 135, 'LOONZ0', 'ONZA', '', '47.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(252, 236, 'MABOT0', 'BOTELLA', '', '360.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(253, 236, 'MAONZ0', 'ONZA', '', '40.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(254, 237, 'BRBOT0', 'BOTELLA', '', '420.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(255, 237, 'BRONZ0', 'ONZA', '', '50.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(256, 238, 'SIBOT0', 'BOTELLA', '', '400.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(257, 238, 'SIONZ0', 'ONZA', '', '62.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(258, 139, 'NOONZ0', 'ONZA ', '', '35.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(259, 133, 'GRONZ0', 'ONZA', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(260, 239, 'HEBOT0', 'BOTELLA', '', '150.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(261, 239, 'HEONZ0', 'ONZAS', '', '23.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(262, 240, 'GRBOT0', 'BOTELLA', '', '180.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(263, 240, 'GRONZ0', 'ONZAS', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(264, 241, 'NACAL0', 'CALIENTE', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(265, 242, 'MUCAL0', 'CALIENTE', '', '20.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(266, 243, 'HACRI0', 'CRISPY', '', '16.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(267, 244, 'HAROY0', 'ROYAL KING', '', '17.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(268, 245, 'GRBOT0', 'BOTELLA', '', '150.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(269, 246, 'LOBOT0', 'BOTELLA', '', '300.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(270, 247, 'BEBOT210', 'BOTELLA', '', '210.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(271, 248, 'HEBOT0', 'BOTELLA', '', '360.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(272, 249, 'NOBOT350', 'BOTELLA', '', '350.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(273, 250, 'MABOT0', 'BOTELLA', '', '360.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(274, 251, 'OLWHI400', 'WHISKY', '', '400.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(275, 252, 'OLWHI210', 'WHISKY', '', '210.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(276, 97, 'DAWHI1700', 'WHISKY', '', '1700.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(277, 253, 'CLBOT3500', 'BOTELLA', '', '3500.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(278, 254, 'LABOT800', 'BOTELLA', '', '800.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(279, 255, 'MABOT210', 'BOTELLA', '', '210.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(280, 256, 'PIBOT10', 'BOTELLA 1.5 LIT', '', '10.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(281, 257, 'VIEL 22', 'EL MANUEL', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(282, 258, 'ORCOC0', 'COCTEL', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(283, 259, 'COCOC0', 'COCTEL', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(284, 260, 'PECOC0', 'COCTEL', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(285, 261, 'CICOC0', 'COCTEL', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(286, 262, 'EXCOC0', 'COCTEL', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(287, 263, 'SACOC0', 'COCTEL', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(288, 264, 'MACOC0', 'COCTEL', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(289, 265, 'NECOC0', 'COCTEL', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(290, 266, 'MACOC0', 'COCTEL', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'i'),
(291, 267, 'MACOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(292, 268, 'NECOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(293, 269, 'SOCOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(294, 270, 'KECOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(295, 271, 'PICOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(296, 272, 'BLCOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(297, 273, 'BECOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(298, 274, 'MOCOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(299, 189, 'NESHO35', 'SHOT (EL MITICO)', '', '35.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(300, 275, 'RUCOC0', 'COCTEL', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(301, 276, 'OTCHI0', 'CHILCANO', '', '20.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(302, 277, 'COCOC0', 'COCTEL', '', '20.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(303, 78, 'JW2 O0', '2 OZ', '', '17.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(304, 79, 'JW2 O0', '2 OZ', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(305, 278, 'EVBOT0', 'BOTELLA', '', '15.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(306, 279, 'LICOC0', 'COCTEL', '', '10.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(307, 280, '12PRO0', 'PROMOCIÓN ', '', '42.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(308, 281, 'COCOC0', 'COCTEL', '', '86.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(309, 282, 'BI.25', '.', '', '25.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'i'),
(310, 283, 'CHCHL0', 'CHLCANO', '', '40.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(311, 284, 'NANAR0', 'NARANJITA', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(312, 285, 'PUCOC0', 'COCTEL', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(313, 286, 'PRCOC0', 'COCTEL', '', '30.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(314, 287, 'POCOC0', 'COCTEL', '', '27.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(315, 288, 'BUCER0', 'CERVEZA', '', '18.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(316, 289, '6×CER0', 'CERVEZA', '', '75.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a'),
(317, 290, '6XCER0', 'CERVEZA', '', '90.00', '0.00', 0, 0, 1, 0, 0, '0.18', 'default.png', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_proveedor`
--

CREATE TABLE `tm_proveedor` (
  `id_prov` int(11) NOT NULL,
  `ruc` varchar(13) NOT NULL,
  `razon_social` varchar(100) NOT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `telefono` int(9) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `contacto` varchar(45) DEFAULT NULL,
  `estado` varchar(1) DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tm_proveedor`
--

INSERT INTO `tm_proveedor` (`id_prov`, `ruc`, `razon_social`, `direccion`, `telefono`, `email`, `contacto`, `estado`) VALUES
(1, 'ESPINOZA MIRA', '-san luis ', '978714410', 0, '', NULL, 'a'),
(2, '20602992111', 'DISTRIBUIDORA RAPID FOOD S.A.C.', 'CAL. SAMIRIA NRO. 121 URB. CALERA DE LA MERCED LIMA LIMA SURQUILLO', 957367240, 'ruben.navarro@rapifoodlat.com', 'RUBEN NAVARRO', 'a'),
(3, '20508783338', 'KAHAN LICORES S.A.', 'AV. EL DERBY NRO. 055 INT. A URB. LIMA POLO AND HUNT CLUB LIMA LIMA SANTIAGO DE SURCO', 988377053, 'lgarcia@kahanlicores.pe', 'LUIS GARCIA', 'a'),
(4, '20377838972', 'MISTROSANTI SAC', 'AV. MARISCAL A CACERES NRO. 146 LIMA LIMA MIRAFLORES', 913397112, '', 'RICARDO RODRIGUEZ', 'a'),
(5, '20601104165', 'VIGO GROUP SOCIEDAD ANONIMA CERRADA', 'CAL. LOS LIBERTADORES NRO. 215 LIMA LIMA SAN ISIDRO', 921447639, '', 'EDUARDO UBILLUS', 'a'),
(6, '20263512945', 'PANUTS VINOS MEMORABLES S.A.C.', 'CAL. SAMIRIA NRO. 139 LIMA LIMA SURQUILLO', 994731741, '', 'SOFIA', 'a'),
(7, '10735162069', 'DOMINGUEZ TAPULLIMA EDBER', 'lima', 982474801, '', 'EDBER DOMINGUZ', 'a'),
(8, '20512488219', 'COMERCIAL FGF SAC', 'AV. SAN MARCOS SUBLOTE 1-A MZA. O 01 LOTE. 01 URB. LOS HUERTOS DE VILLA LIMA LIMA CHORRILLOS', 920301227, '', 'GIANCARLO', 'a'),
(9, '20503644968', 'BODEGA SAN ISIDRO S.R.L.', 'AV. REDUCTO NRO. 1310 INT. P.7 URB. ARMENDARIZ LIMA LIMA MIRAFLORES', 992691555, '', 'ZOILA PINO', 'a'),
(10, '20563596563', 'DSICA S.A.C.', 'CAL. JOSÉ DEL LLANO ZAPATA NRO. 365 DPTO. 302 LIMA LIMA MIRAFLORES', 948029719, '', 'GEORGINA ', 'a'),
(11, '20601813301', 'RIOS Y VALLES E.I.R.L.', 'JR. CARABAYA NRO. 831 INT. 602 LIMA LIMA LIMA', 966230138, '', 'INGRID', 'a'),
(12, '20100052050', 'PERUFARMA S A', 'JR. STA FRANCISCA ROMANA NRO. 1092 URB. PANDO III ETAPA LIMA LIMA LIMA', 991958379, '', 'YOSSELYN', 'a'),
(13, '20511364117', 'ANDES LIFE SOCIEDAD ANONIMA CERRADA', 'AV. LOS SAUCES MZA. I LOTE. 10 INT. B URB. LOS SAUCES LIMA LIMA ATE', 962342872, '', 'VICKY QUINTO', 'a'),
(14, '20565904125', 'THE INCA DISTILLERY S.A.C.', 'CAL. LOS ROBLES NRO. 248 DPTO. 403 URB. ORRANTIA LIMA LIMA SAN ISIDRO', 985329639, '', 'GIAN FRANCO', 'a'),
(15, '10702377396', 'LAZO TAPIA WILSON ANTHONY', 'av, indeendencia', 968613222, 'wi@gmail.com', '12', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_recetas`
--

CREATE TABLE `tm_recetas` (
  `id_receta` int(11) NOT NULL,
  `id_catg_receta` int(11) NOT NULL,
  `id_pres` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `receta` longtext NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(2) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tm_recetas`
--

INSERT INTO `tm_recetas` (`id_receta`, `id_catg_receta`, `id_pres`, `nombre`, `receta`, `fecha_creacion`, `estado`) VALUES
(33, 7, 4, 'COMO HACER EL CALIENTE X', '<!DOCTYPE html>\n<html>\n<head>\n</head>\n<body>\n<h4>COMO PREPARAR LA RECETA<br />QUIERO QUE HAGAS ESTO, FINALMENTE HAGAS ESTO Y TERMINADO PUES NADA<br /><br /><br />INGREDIENTES ADICIONALES<br /><br /></h4>\n<table style=\"border-collapse: collapse; width: 100%; height: 117.563px; background-color: #CED4D9; border-color: #070707;\" border=\"1\">\n<tbody>\n<tr style=\"height: 19.5938px;\">\n<td style=\"width: 33.3603%; height: 19.5938px;\">INGREDIENTE</td>\n<td style=\"width: 33.3603%; height: 19.5938px;\">CANTIDAD</td>\n<td style=\"width: 33.3603%; height: 19.5938px;\">UNIDAD</td>\n</tr>\n<tr style=\"height: 19.5938px;\">\n<td style=\"width: 33.3603%; height: 19.5938px;\">AZUCAR</td>\n<td style=\"width: 33.3603%; height: 19.5938px;\">1</td>\n<td style=\"width: 33.3603%; height: 19.5938px;\">GM</td>\n</tr>\n</tbody>\n</table>\n<h4><br /><br /><img src=\"data:image/png;base64,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wkPpPPdQChHenPs3PIUX/sNE7Fsb2fTeRrLeAwgiduJs/rx4DCdePerwhjAmP7mCtQkbWbV5I0s/oj6+eU/zwuxRhHgYQsKcFawNe4sV72bxp3+3DKgdHNqXOc+uYEmnD9id1UQBjfIk3iCSFq5kbXQGqzZ/wC93fWBdbDnWf37cWPee1cOIB57mqdK3WPfeRrKAEQtW8OeZfevqP/q1t9jiWP/PjuHEq8fY3ZLdFxERERER6XA8vy8Nvms+ax85ypq3PmbZR9Z5cINcr+/Ne1RPy2rR8wTTGJ76j18R+24G697dyA5bmTHxzF+WzqK7NPyziIiIiLSOgNu3b9/2dxCtqeTKDQACAgLq/hl/d/zZyLjMVm0BAQEYq9D2s+P/4WF+SJp6Q00F5RVmgk1hBHsz6dkS5grKK2lgDl8XWrgP1RXXqcZEiKkVhr2uqaC8AoJNJrdj9XZ81RXXqe5kIqSrb/bXo3grr1Nubmi+5haU25Bm1L9Ie3btumXIO+Pnn/F/x5/d0aE/E0VERETEpUbvxy5v55e//BhsX8IFj+7rvXmP6nFZLXmeUGOmvKICuoYR0oqzaImIiIiI93j6/LShZ6mePmO9ZbZsNzfX80kqk5KSAPUAlqYEmggJ9XcQDoJMnt08tXAfgk1hDQ4X7XXNiNXb8fl6fz0qv2sYIW4ON+6VuNtiexcREREREWnjPL4f8+C+3pv3qB6X1ZJ7xMAgQkLDmvlmEREREZGW0RzAIiIiIiIiIiIiIiIiIiIdhBLAIiIiIiIiIiIiIiIiIiIdhIaAFhERERERERER3+gay+QZKTCg6fl+RURERETEO5QAFhERERERERER3wgdxSMLR/k7ChERERGRO4qGgBYRERERERERERERERER6SCUABYRERERERERERERERER6SCUABYRERERERERERERERER6SCUABYRERERERERERERERER6SCUABYRERERERERERERERER6SCUABYRERERERERERERERER6SCUABYRERERERERERERERER6SCUABYRERERERERERERERER6SCUABYRERERERERERERERER6SA6+6LQwkvfczz/LLW1tc0uo1OnToxMGEhs3z5ejExEREREREREREREREREpOPyegK49PoN9h35morKKq+U9T/umUBEWA8vRCYiIiIiIiIiIiIiIiIi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GxvqzWYZyjp1ZhrLrTHW/Q7APNIXNvBWL+udOoWpZNsn04lg7ptrKUiyH8LW1TDFfhETR/7MtLre1VNf/oh3kqE5cfd+YAHpSUuJ32hdz/A7wNSF8xoevtbnfN+mezexv07146Ktt86ww44SWb5zGU8Y2oHlmvJqE3M812tq31skeSnZLxuvd3hUT8nzl8HMh4h/ybpP6fOIr7v2pJK+0DbfbARDWG0YCnseW3J9eSwSm75WLZwCK9KItw4RXX9+Okh+jJU8VB/7wnmezZveGDc/q+ZGWZL1ezeeY+WLDp8n6w11PmkZW15OZa+117XT55nT623pOiIiIiIiIiKeCLh9+/ZtfwfRmkqu3AAgICCg7p/xd8efjYzLbNUWEBCAsQptPzv+Hx5m8vauiIiItMi16xUAdp9/xv8df3aHPz4Tjxw5wtixY71apoiISLtiG665LQ95LyIiIiIi4kO+eEbo6fPThp6levqM9ZbZst3c3FyP3geQlJQEqAewiIiIiIiISDtmHaJ6faaSvyIiIiIiIgIoASwiIiIiIiLSLuW8lsYjGxsZolpERERERETuSEoAi4iIiIiIiLRDyc9mUvCsv6MQERERERGRtqaTvwMQERERERERERERERERERHvUAJYRERERERERERERERERKSDUAJYRERERERERERERERERKSDUAJYRERERERERERERERERKSDUAJYRERERERERERERERERKSDUAJYRERERERERERERERERKSDUAJYRERERERERERERERERKSDUAJYRERERERERERERERERKSDUAJYRERERERERERERERERKSD6OzvAERERERa6siRI/4OQURERERERERERKRNUAJYRERE2r2xY8f6OwS5Ax05ckRtT9yituI51Zm0lNqQtHdqwyIiIiKeUQcRexoCWkRERERERERERERERESkg1APYBERERERET84W3iBkitXqampadXtBgYGEtmrJwNj+7fqdkVERERERESkdSgBLCIiIiIi0srOFl6g+PsSv2y7pqambttKAouIiIiIiIh0PBoCWkREREREpJWVXLnq7xDaRAwiIiIiIiIi4n1KAIuIiIiIiLSy1h72ua3GICIiIiIiIiLepwSwiIiIiIiIiIiIiIiIiEgHoQSwiIiIiIiIiIiIiIiIiEgHoQSwiIiIiIiIiIiIiIiI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o7h+xPWN8lVoHcKNsh+IjVEdiYjcCXqGhfJd0WV/hyFedif2fmitBEBbeUjsW6Vs/vlDLN9vWLRwLQXPJvotIhGRjqI9z/1r096S7uI9BbmZvPH2BrIP5wDOwzC3hpRxSS3u+d+ReqOLiIiI+IPPEsAAE5MSuVVTyz/PXcB86xaHT/yT3FMFhIV2p0tQEFVmM9fLfuBWTQ1gmRP4R3H9mZikB1dNqb19W8M+i4jcIQIDO2mudxFxEMHcNzOZ6+8wREREpM15+slFPI3/vshwp33JUURERKQt8mkCOCAggCljRxHZM4wjJ/5JZVU1t2pquHLthtO6XYKDGDt8CIkJAwkICPBlWCIiIiIiIiIiIiIiIiIiHZJPE8BgSQInxsfxo7h+nDxznjMXLnGt/Adu375NQEAA4SHdGdS/L8MGDSCos8/DERERERERERERERERERHpsFot4xrUuTOjhgxi1JBBrbVJEREREREREREREREREZE7iiaRFRERERERERERERERERHpIJQAFhERERERaWWBgYH+DqFNxCAiIiIiIiIi3qcEsIiIiIiISCuL7NXT3yG0iRhERERERERExPtabQ5gERERERERsRgY2x+AkitXqampadVtBwYGEtmrZ10MIiIiIiIiItKxKAEsIiIiIiLiBwNj+ysJKyIiIiIiIiJepyGgRUREREREREREREREREQ6CPUAvhPVlHMm92sOXSinAoAgImPiSE0aTGSQw7rmK+QdOUFe8U3LusHdGBw7gkmJvQi0rXPtNPu++p7g/smkxndxscFaCnO+5ERVHyalDibUk7IdXMvJ5OPLvXhwxkjCjS9cPs6HB64wYGIa43sbX7jB13/P5mh1b2bcl0yMq0LNV8g7eJycK1XcAgjsQuyAoYwfGU1ooGG7hVX27+sWY4nj8nE+PHCRG3UvBNEzKprUkcOJCnG1wXPs3nqKQoelsYkzmJ4A+Vm7yGIoiybHNVALBp4cS4CzR/jLV1eI/FEas4bbH6uGtmu33GlfAbqQODGN8RznwwNFmIZOZNYwU4Nl5GftIuuKi9is9Vni6vVerurjew7tzOVM+EjmphiObPHXfJh9lQEpaYyPdrEdEemQjhw54u8Q5A6ltifuUlvxnOpMWkptSNo7tWERERERaS4lgO805fns+PspCgN7kTxyNMN6BXH1u0K+Pv0NH/53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\" alt=\"\" /><img src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAB4AAAAQ4CAYAAADo08FDAAAgAElEQVR4nOzdeXwU9f348dfs5k5IuEK4whkDBAgqRjGIgIazasuhGFH8huNXGv3WA/ALVkFQC0XwaG1Ki5p6YFQQ22JVIBYUjdaISDjUmAghXCERSEIg1+78/pjdzey9uUgC7+fjEcjO8fl8ZnZmZzLv/bw/SlhYlIoDVQVFsU5WHGcLIVoN7TzVzllFzl0h2gw5d4Vom+TcFaJtsj93Q8Pa0aFTZwICgjAYjS3cNiGEO2aTierqSk6XFHO+4hxGox+BQYEYDEZQtOuu7eqrKCiAqiqAapmtWBawv0YrDr8oKNhdzV2s43qe9X8VRXF1H6CAomJrqaLVpa3h0Ban1tk/qnNcXkW1TFQt/ylO6wCoquqiTBXb6s5r2M1T6mrA4T+ndUBFVbW1FMXaLtX1OmrdVFV1uYRow1RVxWw2UV1VjdlsaunmCCGEEJctxTEArKrublyFEK2ZnLtCtE1y7grRNsm5K0TbFBIaRvfoPi3dDCFEPRUdP6IFdx0uvRc1AOy4YpMHgB3LUJ1+a7IAMLbYrHOdEgAWTajywnlMJgkCCyGEEC3B4DhBnmMJ0TbJuStE2yTnrhBtk5y7QrRNHTtHtnQThBAN0KGTnLtCtEX+AQEt3QQhhBDisuUUABZCCCGEEEIIIS5FAYFBLd0EIUQD+PkHtnQTLkInVdXN781clRDNyGCQoRaEEEKIluIiACzdGYRom+TcFaJtknNXiLZJzl0h2iJ5EC1E22QwtL7+C4riQxRVtb9fUJ1+cVrB/Ux3KZhV7/ckPrVViGYgQ6YIIYQQLaf13UELIYQQQgghhBBCCNEkfA9+NixM6i7A5TD+rYvCfanP3VC99vPclVT/4Fv99oEElkXT0satVi3/N6gE5LgUQgghNBIAFkIIIYQQQgghhBBtVz2CRS0TGlJt/6uAouC+R6+LtVC1dexDyhd/S+pVY4MDeOJypaqqpcew0oiew4rlR44/IYQQwq+xBYwYkUBy8h3ccMP19OzZHYDCwmN8/vmXvP56Bl9//U2jGymE8G7okL7s23+oXuvU1lZSU3Mek6kW7eZYwWgMwN8/GD+/VjDGkhCXgfqeu4YeA/AfPRO/IaOhU0/t1D1zlNr9n1CzcwPmYz80X2OFEDb1OndVy1Pbxi4jhGh2337bDgMGUPxQMaAYjSiKPwbFgFk1oKpgNPphxgAYMJtUFIMCqgEUhfi4/S29CUJc1hRFRVWtf902UANWrgtcuZqpgKKL9loWUxRLjFTxEqpyvEWwGyrYfUNVtSEbUr/FXa0q6aZFQ8mtsBBCCNG0GhwA9vf354knHmXu3HsJCQkBsKXnGDDgCgYMuIIZM6bx8suv8sQTv6empqZpWiyEaDRVVamqKqO2ttJxDiZTFSZTFf7+IQQEhAEyZosQrYLRn8A7n8A45l7dOWn5ZnPkFfiNvQK/MbOp3fkqVW89ASa57grRKlgCu/5+KkaD5aV1FmBQoLIaVMt5rSqA2cNDZCFEs1JQUBUDoIDBiLlGBVMlJgygBGAIDKCmogrtT2kDxtAgzGYFFAVFxgYXotUIDg1h8uQJ9O3Xh5DQUADOV1Rw6NBhPvxgG5UVF+pdZl04VfvNtw6ulmV1/2q9gLVPDFsQGJxjtZbp1lsCx96/3tM/64pyyEPdpCFaifeKJtN011H5bqUQQgjRgACwoij4+/vzzjuvcfPNY5zm6YWGhvDb3/6GwYMHcccds6ipqWnEGA5CiMaynn9VVaXU1lZZpjo/qlJRqak5D6gEBoZ7/jazEKL5Gf0J/N83MAxIRK1V7R732FHBcMMsAiNjqPrT3RIEFqKFKaqKqij8snMQyxb9TGDUBdQaQ10KR1XFz0/h5GmFe/4QTuEpo9ZByMdr7tzr4rlj2ECGdIsEYP+JYtb/dy8b90omACEaTNF69hr8jJhrVTp1bs+ji2/Dz9/Isy98TEHBWR5d+iu6d+/AW+/s4bOdPxLQLpjaGnnSLERLs4ZX468cyuRbJhEcEozJZOLMz6epNdUS1bUr8cPiueKKK/jo3x+Rk1PXY985Yayb3rMOk7Uex4rrZe3WU1AVVQsCW3oEq5Z13H502AV+Lb9pNxCW/7x95ljLV+2D1W4fy9nPaMwnmjz5E83lww83o6oqkydP87icPMMSQojLQ3n5SY/z27XrepFa4pvy8pPMn/8AGza87XL+zJkzWLfuhSZrd70DwKqq8sQTj3LzzWPYtetzMjI2kZJyD4MGDaCwsBBVhV69osnN/ZH16//OtGm/JClpLI899ghLlz7lWyWpK8gYFW15Uciu5KWkAUlL1jEnYjfJi9fXt9mti932Qd02zmNVxnBKX57PykzHleaxKiOaI5Z9YStq1ToiMl0t71DlqnR6HUlhcZrn5fTLj+pteVF5gJdT1uClCg/02+V6O0TzswZxq6vP64K/gPtQEjU1FzAaA/DzC/KhhrEsSZ8BG+yPx6Ql60gqne/zsXepSVqyjjnxzum0K3NeI2XljhZokWiL/Kc+jtJvBGqtavdV5gh/ldXX+3FzDwPBfvDUbhNpB0wo/UbgP/VxajYu9aH0sSxJn0W87TSvIsfxOmR33XIx37ZMF9fzkhaSPieGXJfXt4vD6VwsyGre+4kG7Q/tvYjI9ny9Tl2VTkJpU3yGuLsmOx4TULDL93sIb5KWrGMmbzfpZ2BzlNlYquVc/WWndvxxQCfCO/+M0tkMtYrtQa/BoKKqKj2iFa7uW0PhKSPtEztTtr8Uc5n7L3BEBAWwcdavOFpazhPbPifr8DEAEvv0YMHoBOZdN4zbX/sHpZXVHlpoeZ+LXJ8LqavSGUUznycNkbSQ9Jmwweu9aRPec7qrM2kh6XPCyfa1jvouL1qGqqV9NlWaQK3lqRXT+ezzfEaOjGHpY7ex59ujVFerPLt2O3984U7m5p/hZMHPGNqFopp9edi8iM3ZE+i8O40b5292ucT/vvIBs4cGcGjLTKauKHK5zF3r/sGi4WG211Xlefzrd/P5/RfO86g6ydebn2fes1/b6u+rL+zwVhYfT2BVYjU7fn03D9tGcYpi+cYN3BaWzdJJp0lx0e7hz7zBS2O6Uu5he7Q6R/Lz2l8x7y3P2+FLW6+6/RkSH17L724ZRvd22uTyfe9y4+ww5+UBOMfXlrrdtXf5xkxuQyvbrm2x+Txz0wLevHMtny4YRjvb3GqO797C0/P/QpauJnfv3fKNmdzWBw5tWcDUFXstU6ey/j+pDMhN48bjt7Dn1l7Ou658L8/cdJLpbvaDcBZ/5VCm3j6VmppaPvr3h/z3i69RUenTrw//M3sWAMEhwUy5fQoAOTn7df1xNa5Dv3fzTs5iro/QXpV++QeG3vmG9mL2y+xbmoA2q4wvVtzAnen6da15nh2CwKiW9M+WQK1Djc6BX+13++Cv67/mG5T+2UVptqcFXiO6+m7M9Q3/SrhY+O6GGxJbuglCCCFaGXfBUm/B4ZYwf/4DrFv3AoBTENga/J0//4Emq6/eAeDhw69i7tx7MZlM5Ocf5vXXM3jnnc2Eh7fjzJmzqKpK+/YRVFScp7Kykvj4wYwdeyO//vVsNm/+F99+m+O5gqSFpI+CXckp2oORpIUsSQXSIHPl/EYEIZtQ6goyehU26IFY0pJ1zInN4+XkFNu2JC1ZQUISkLmexcnWMl0H05rfPFZlJBKV8xrJyZaHqJb3ILPBT6r02+Wrltr+S5eiKKiqmZqaCts0A90ZwK0M5mf8/c+R0z6YAcXH2cx/bctUV5/HaAyUb082kNPnlvUh8kUNUsj51JYpUbEo192NucbsNO/pkQFM7We0vQ42qFoPYVVFue5ulE/fQC3K9aEWXVA3aSHpc1aQmqkFKFJXpTMq6oDuujWWJUvmQabz53plJcQmjIVM++M7NSkGHDPO+8rnYI93dV+80AJfq1LXN+uXU+q7P5KWzPAa/AVIW5zShK10x02gX/jMCCS3N/OnPgFUVZ3Fz68GQhTQ9RKsPqdSqxoIuWDCP8yfsFmD6HVrN35ancO57BK3ZW+6dwrrv9zLF4eP8cSEG3j2trEAHDj5Mw/+42PuuGogG2f9ivF/e8dLK6uojIrGcqtdJ2khCVFVVLqOO/mmEffLbUbmGlLqc47Ud3nRIgxGI6bqGmIH9OKpp5MxKLDhtc84W1bJ7dOuYfQNMezec4yYfp3Z8Wk+r6y/kxf/8gUfbPkeQ4h/E7TgN4wcCiUl1fQdOotuPMMJt8ueZMejL7G13TDuuv9Wbl+6kn2TltjPoysT7p/F2KlzWfjs16yxzC3f9y5PZ3ynvThXyNawjixMTGBA8jD4xhKg7DaLoX2gJGsHWxiG89VnDClDulJScprOsSO5i8282aBtrl9buWUly5OHEbhvC09n7KW8+0h+PToM2MyaR7NpBwxNfpCZfY6wYeVm9lFNyf6mae/xnU/xx23Qffxcfj3mVn778F/IetY61/t713f8b1i4fj5rHGe8lcbiL8KAQdy1ZBp9D1u2ufoMOYxnuqv90Ab9+19v8PXuvSxbbh+8Xr5sEdcMH8YvbrvbeyEOcU39y+DQECbfMomamhrS/riOM6dPAwrjJ41n6LAhTkVN/MVEfszP54JP6aAHUrhtCHcsBEV5iv8c/g1vz36DGa/AnPGwrU88CwBl9svkLP0na9J/yUJXDdcHgW2D/6p1v7raXt0LX4K/Gle9f5sh/bMPnOqTeG+bk/3VTrKy/ssDD/6f3fQXnv8DiYnXkXDtGDdrCiHE5ePQoQP07Tu40csI3+iDu64Cva2t56+VNejrGATWB3/d9Q5uiHoHgO+9dybBwcEAVFVVYjAYqKqqori4rkfhzz+fBsBgMFBTU4vRaCQkJISUlHt44IFFniuIDSeooLDuIVTmmkvn4WPSQmbG5jn1ps1cubR1BLaBpCXDiXLsmXgpvQeXOZOpBlWtCyKplNGPvow3XMPGnkU8+vMAYAeb+a82/hkqZnMNZnMtRmNTPNC63I1lycwYcje0ki+ziDZBSbgD1QSg2nryW9O5J/U0APC7z6p47WAtFbXWb9wrdeu+72P2DavM3eTOnEFEEsBCEqIcs0DsYKWbLzAEFZ2iIH4sqezQBZPm0Ssqj9yimPq1o1ntIDt3BjMjxgLN92WMeu+P7LdZLB8Ol4xOUbH0HjePPxoMVJlrOLN5NQb1KKqqDQQcGAAL7rhA+3ATB04EcmTMQLr37UZ1aZXHcudeF0/h2TK+OHyMrb++g/bBdV21e3WI4Po+3Znw13dI7N2DO4YN5J2933ssr6ioCwlLxoLuvE5KiKEoN4+oiMbtAyHaIrPJTEhoMI8vncaTyzfTrWcnHlgwiT05x6ioqCIgwJ/gYH+iunak8HgZ//1vIU8vn0Dh0XPs+8Z9qNZnDw9jQFUer7wPM/9nGL+9GpZ8427hasq372QrO8kZnsAH46O4BvjBbh5svX48e24N0PVeBaqL2Lp9p27CVn54OIGRfcbTjb2cALrd25++nObz7duAYc7Vj5vAgM4n2bc8nwHLEphwL7z5akM2up5tXRpFZ06zY/0LbPoCYCdbLfVmbc8HoNO0B4EajlvKbar2VpXvZOt2YHsCt2ZPIEDXednre1d+mpKAGG5bNpUN8zfbB4dzv2ZrLkBHpi9x3ObxrvdDG7TzkyzuunMqgC0IvHzZIm67ZQJvvuWuB7me516tkyePJyg4mA///RGnfz6NokCPnt1JvGGEy+WDQ4KZOGk87236pw9VPcYCa0RXfYw9+fu5Kk57+UrybFsvXvWVYxQv7ey5/XZBYHAf/dWvh8/BX4+9fz3V4yUw6zxb9baA19JklLi24aOPMpk3714AWxD4hef/wIwZU1m/vkEf/EIIccm55565fPjhe0yaNMXl/A8+2MysWfMucqsubW2p56+eYxDY+ntTB3+hAQHgxMTrUFWVqqoq1q17GbPZbPcwWs9sNrN+fTpz5txLSEgw11+f4L2CtEIKMhJJX5LnlMbPPr2f1lNVy1JcSE5OF2It87R0x1kwyjLflubRsRec/rUlTdwuGGVJc+kyRastDWY0GRnDLb1jHFIlukkrmZQQA7lvewj8WFPV7SDCWt6cdDKSskhe7H3XOaZ5dk77rNtnLtM6jyUhFnI3eHgYnrSQ9DmDsW2qLSWkp/3nIQWfy7Siuv1p2/5LuPfIRWRyGA9U5Rw7eIw9Sg9uOvEAPStrWGlLYqbarddUAWB36cW183s3ubGJxAdZjp/Ssbbjwy79qN1xWJcm3uncdyi/Lv1r3TqePksc29vYtM1JS2YQm/u2rvePu88O5/PJtv2W3pDZuTGMig/EsZeec3uR86mNU/qPtOv9q0/a3ilYe7Dz568taV7t478o/Uc2qm7v1y1HhRwpSKSXrjth0pLhROXupjRWV66b89FuekEWyZkRtnN9TkY6SZbzwOV5aTk3cosGE99bf4674nC985Di2m1dLj6DGro/9PXMmTPLVo9dqmdd6lj013cPn4dNN5SDtZEeUoHr59nqs/+Ms08jHaObZ78PPbXdl+3SjiNavAdzVXhv3us7Sxvmz6yy/4W3MJ47o8/iztIZ5ews7sDcg31Ro8LpXFWNyUvCjRlXDmLZ1s9YPvEGu+CvVfvgIJZPvIG1n2TzxPiRXgPApUfyiEoYThI7LPtyHknxpziyK5zeEaV1C7p8j3F9DLq8X/Zhp1k5XevcpCG31I3lnNF/hlTmHMBjB2ZXnxm+pLvX15k93GOGAu3eJo+i+MH0tn6mWZaP9XrfLlqKWmsmvGM4fXtHknh9P/pf0ZNevTtzzdW9KSuv4ty5KvbuP86Nif3Z9p+fMBgN/Jj3M1dd2Z192UcbWXsUj14dQ1VuBn/6cwXXJM8lfpquR269BNBu3Bgm0J/pY3rB8c/rAqEu7WRLbiojEwcxvxssOwEzh8RASTZb3ne9xq239Kfz8e/Y8P5mps8bydjRd8Krb7leuCnb+v53HBo/gbFPv8LKF59myeZ8n2ppivYGthvDhHEQO20kfTnJju3WOb68d4X8a9s5Zt96J7+7ZTP3u9mvl7Jn1qbRLiyM226ZYJt22y0T2PL+Np5Z2/gPwD79+mIymfjqi68A7bbY3z+A4lPFAHTo0AE/f/vHYH379rH97us4wCpPcVX/n9hzszZfu7ZbxgKe3YPIshIOum2lLggMoKjUJYF2vbT2i+JqqvPyuuBv/Xr/NnD8X4cM0YpinSCR3UvJ40ufIiKiHTNmTLVNmzFjKu+8s5nHfR3uTwghLnGfffYFmzf/k2efXcXDD9sHc559dhXvvfcvdu3KcrO2uNw4BoGbI/gLYKjvCj16dEdRFM6cOcuRI9ofuK6Cv1YFBYUcOXIEgOjoaLfL1VnP4uTXyI2dRUZGOqtSXS0zliXp1jTFKSQnFxLhMMZm71HRHElOITk5i4Lew1mS5MvWRTOqV6GlzCyK4mc4r5e2lORdhdqD6eS6YGVsrrUtKewikfQlY51Kj40IpKi0LniUuiqdjIx0MjLWOdSzg5Upr5FTWUXOyym6YE00ozKs62g/toegPqjbJynsKhrMTKc2xhARdIpSdw/obGMWptj2EaP0bfdh/+mlriDDku47OTmF5JfziJ2zglS32y8aS9/7V6Nwnlp6mO7kmsrhPMMbfMS32rIe13MnkPg59seofsxN23iClvf85dwY5qyq++ZTUPxw2GA9fmZpqSOTU0jeVUjvhIUkQV06WOtxswtG6cpwd5xnrpxfV2+OpbeTl88S68NYbd5r5MZ6OaY9sWQAsE/9vIOVKXXl50QNd30+6bcfIGgwCbxt2RaIT5rnob1yPrV15uCumGtMLn9sy9SYXf6oId3qX2HqWOLJIzvT+brli7TMA0TZjtexJMSeIntlnt0yrs9HLehkuyYsXq+lS335AJWVWgpqa/DX7XkZNJiIIykkuwnIBsXPsnw2OaRET1tqa0/yrlO2cyppyTq7zyx9oNndZ1BD9oe7bUpbnEVR/FhSrdkDXnbcrnmssrsu64K/Hj5rfaP/PLdc693sJ/3wHcnJKSTbgr8zLJ/p1nuGFVhv6+o+77VjwLoPPbXdp+1KXWEZaqPl01crpmrCq8/SvrqM8OoztG9Xi9Gg4u+nYDSodA4182J+F+46MICfTf6E+Jkxq4rXIRcGd+1M1uFjXN+7h9tl4qI6kXX4GEO6RXpvaO4asosGk2R9c1KjicrZYX+sWdM56/b9zCVj3Z8PTvfL3pvhRHetS375AFG640dTd/ynrNyhvfe6z5ANxOD1Nln/meH2vtRDnb5sRny4dl8i1982Qwnwo6SolMqqGm775XUEBfnz6Wc/smfvUdq1CyI8IoigQH8+zTrM0CFdyc0rARVy9heBMaBxlXe7k/gB1fyw+2XgLXbmVtN9yC0M97ba1XNYcX1XOJ7Pv2xTuzL294+x6vfJDDj8LovnL7Mbq7bd8FT2ZGeyJzuTzUu1aVu351NCLwbcGwX8hmsGQEnuVjfB2PFMiO3I8dzN7GYvm/afJDA20UWaaF/Us63fPMN9z+7gEL2YuOSvfPnPldzl9ZanadrbfcxjrPr9Y8yOPcKGRxfw8BeWGT6+dz+vSGPH8Y6MnPc76juKpav3rC1aunw1W97fxm23TLAFf5cuX90kZYeGhnLm59N2QdDDhw7x5xf+wp//+Be+/95+aJTq6hpCQkMB+y9Zaq9xjpau+ReFBfspLIhnT+/bWKBfXgWYydsPJlC8bQ4vewyAqnWFq5ZgsCUgbPdjmWff69f3nr9OTfDSpKYZ/1cyPF+qfvvA//HOO5uZMWOqLfj72wf+z/uKjfDhh5spLz9p92PlOP2DD95t1rYIIYQv1q//OwDz5v2PbZr1d+u81sbx89Tdj2ib6t0D2N/ybcmQkBCCggKprq72uHxQUBCdOnVCURTbut7tYGWKtedoOqtw/DZ8DBFBhWTbHrysJzNnODN1SxTssj4kXa/1vokF711fCtllezhjKdPF2H32YojggF1QJy3zAOkz9T0pNLmlVSTo0k2mLU6xPKhdh28Z9px7Gfm+rn6fuGtjHqWVw7W0n672VWw45Lyte4in30eW9rncf67bkxTRhYJd8+3SfWcnpdv1lBLNTaELDxDDnWxkNZ/zQSPLc+4tk7RknS3wERFVyK6UugegmSt3k5ShjT2YC1Taji/tWMyxjjGaW0alNYFAbDhBQdHMyUhnjrWgyjJbcNT9ca7v6QsUaOlX3X+WjCUiCnr3TidjVN32FDh+lvjUE3AsS2YOpig7xfl0sOttpC9fdz6lFVKQEY1tVmXdZ05mdh4zZ0aQ5Gt7RZuj1prBYHmMYumWUL6kvd0yZYu0K8H1L5ex/5QZ62MXc43Jx2/vW4J9c0A7ji292RyuWz6xpJBOSILM2LHEFxWSDCyxW8jV+ZhHaWUio9IXkuu2t6qH4zwXqDzgcbx6u561dtd3h974lWUkWXoJ52xwCNp4+Axy2Wav+8PTubuexbtWkJFh6RXsWEFqNL0LdpNsN93zZ63vl1dXvR9d7ScgIQZy3nYoO4aIoEB6244rrcxSy4d1pe5+oq59ntru/RpC7AwycB5qo6WoKJgUI4piQFVUTKpCrapg7fVzxmzk2bMDqTVfIMhgxmTX78jnfjcueQsiO0o7UkhGr3lAHksSumi942PrviiYFNEFekeTkaELVxTE1P98sHLXm1hPd62zu0fMBYhhZgbk6o5Rx/tK6/HhsT7dZ4bH+1I3dXrbJoBKx2C6aPVUs4p/iD8//1zO9z+c5NjxUsBA3vGzjLi2L7UmlbhBURw7Xk5UlzC6d2tPTP+Olr91G3fudps3jAEEwP9kssf2zGgQM6+G3S7TQPfituxMbgMoz2PjqqfZDQwA4Aj/Sniaw+vW8tvhI5nQ5y9s1eUc1o8nW15gmfj+Dn64L4GRQ6bCfYPow2m+ft9NyuE7JxDfGdqNWcuebOvEjoy5D9L/XN8tr39bT2x+mqmb3+LWxan89pYEFq37HT/8Utv+5mzvoS1JLC5Yy0v3D2Ps+GjWbNdyDfj+3n3Nw+uz2b5sJL99+DvKfa/a9XvWRumDrY6B18bq3CWSJ55eppWtqpw9c5a9e3L4ZOenfPjvDyk9e5aQ4BDy8vIZe9NoOnTq4LW1tnN74W1ELwR4ih0F+/nPpiHctNAyf/Yr7Fs6gIMr4rnpFdB6Bqtersn6bdcHet0t46Ekh+BvY3r/+raEhHkvR/ovFbS2Tt71vf8VQojm8vDDi/nww/f47jttYJapU3/pNi10a9Bax8q91OnH/AXnMYGbSr0DwD/9dIiBAwcQERFOQsJwPv54JwaDAbPZvoeg0WjEZDIxYkQCkZGdUVWVQ4cO17O29SzeFW15IKV7AJsUQVRlWat4uFcfmaWnmJPgHBhuPXZQWjTL4aG4uJQoirXTv/UhczzhzOMUB6jgHNP4BacpYAf73azXSrhJsx7rYlHNPFZlDKf05RQtUJK6goxe+PBZ4ib9o17mGuegjKPUscQXZZHs+AQ4dQUZCWW8nKwFhlNXpdPLS1Ge+dBe0eaop4+hdrKOF+t53LPDP5u0dNGWP34NZ475WIvrY6dh160drMweS0bSPFLpQk7mUkCfccLN+Wj98lfSQtIz0pnj5jx3n5bV5waStjiLVRnWsXm1oGZEdop2jlp6NFqzYhxxteFu2+aKt/3hYZtA+wLMKBfT0QJWlfXsod1w7vaTpaf4EVftcP2lmCQfRgRpiCCgMiic1vK9F0VRMBp1AeALJkuWR+2e2XTBTFSvCC5UB1FS+LP2ZQ8AFd2YgM4OnCwhsU8Pvig4xsSB/dwuM3FAXw6cLPGtsWk7yEmfwZLUCGKLdmvXNYeLqruhUep3PljrW+p8TayPIKAykAjdmx0bEQilbpZvbH1u6vRWR0OThogWVmumY+dwUKFnjw7k/VRCt67tCQkN5FRJOaGhQWPQJ7YAACAASURBVPSK7kivXh0xmxTeyfiKDu2DuPqq7mR/2pioXBTzh/aCw1tZ/DdrhHIYKctuZUCyuzTQJ9nx6EtsPVfI1i9cpUHOJ33+ayT+J5WxDy9i+O3P1AVIXY4nu42tuXMZOXwQL1bHEFiyly3bcemuMf1pV7KXPz67heMAhHHrww9yzfA5wMv13fgGtFVbZ8uqBRzv9AYvjenPbeA2AOxTezt15S7gTQCGcU23MCg/ZxlTuU7uqwtYd/0/WDQmleVXz2bZN/V8795/nvRb/sqiqVM57vn79PYugTGAoW7M3y3vbwOwpYO2jgnsE4dbYuvLiooKwsPDbdMVRaFDxw6MuXk0Z86cZe+3e9m+9WPbvKm3/4rzFRW6clQU3VVYxZKh2en2+zHGboyn8KqngMe04O+D8FyfkaQr6FbwJQis34qG8SX4W5/ev9rTAh/ao7r53dNk7xNEK2Ud8/edd7Txuq3poK1jAjeHSZOmOk2z9kKTgIUQojWbNGkKBQXfASq9e8e1dHMuS62517I++KsP+DZHELjeUZ2vvtL+pCovP8ef/rSG2NgYp+AvgMlkYuDAWFavftJ2w/vll197ryB1hV3a56SILs7LZO4mF12qOuaR5JAC2rUdlBYFEptgefCaNJxYu6HTorVv+dvKhNxsbw9W8yjFPp1yatJgyN3t/GwobSm7igY3IA2jb3JLq+jdy1r2PHo55L2rm+e+jWmZByB+ln3qbet7klsGdmmdHfdR/fZfZukpeuvT+SUtJKF3IUekm0SzcRzHVyGUaqqpJICh/D9GMhM/AizzFLfrNcwOSoui7VKlJi0ZTu+Cwvr1jMkto9JDWneXx3lSBFHUpTdP7WXpoePxs0T7vLClOG2weawaha53fJ2kiC5QVGobc9HxnK2fpmqvaG3U/M9Ra83aT42KWmsm5PESQh6vC+yELC0hZGkJZ8trUWtVzNUmzNUm1LzPG1d52g5ycLxuzWOVt+tYWiEFvRMZFaWlkrbj7ny0ylxDSnIWBb2jcR4FoqmO8/Vk2tIOxxARVEWpJSNgUkKMpUf/eo4URDund/byGeSSp/3hcZvmsWpOONluUtBnZuc5XJet5TXBZ60Td/tJ6z3a2yk9bx6lldGW9N7OgmKH24Jjde3z1Hbv21WZ+zYpu2BUhmNbWkZ1TTUlpacpKT1DydkSQq8JJeKGSMJv6Er4yC60H9sVs6IS1jGMTlEdMNSAMcgP1WT2+Cj07W+/Y8618Sz76DNKL1Q5zS+9UMWyjz5j3ohhrP/S13FDd5CdC/GjBlN0xPl6lVl6iqD4sc77tSHng6+CYkiwHSQO94iVeWxIyYJRdcPFpB2xHy4haclw7ymgdbzel7qoszG83beLlmMIDKDoWAkms0pYWBDnz1exN6eQHt0i6BPdgarKWv7zSS6vvv41X2YXcPjIWYKD/fn00wIIqMc9c0AUE8aN0X6u7w9Xz+WaPnBo32ts3b7T8vMCXx+G7kPuZILLQqop377TTfDXajPrdh6BPmP57X1RrusfPQxrBuUt2/MpCYxj5NAAD+mf72TCkDDKCz4n3dbW99mSe5rAoQks9H0vNKytS//Iv9ctImXcGCZMfYD5Q7pCSRHunzZ4b++63XnQbhjz31hEyrhb+N8XFpHYHY7v3+wyqPzm3z7nEL2YcP8cutX7vSvizeVb+YGudG9Xj93j5j1rSxYtSLVL+6xPB71oga8fru6vkod/OuR2Xi+He87rrr8Go9GodVTwJcXxmqdYa3t9N++M70fensdAhbUzB3Dw+dm8ArbxgOsoHodNayzPwd+65je4968P+6b+4/+qvi0mWo0nVzxml/ZZnw76yRWPtXTzhBCiVerde5AEf1tQu3ZdW+WXhdwFfzdseJv58x9g3boXmDlzRpPVV+8ewK++uoF7753Jn//8N/r06cXu3Z+xY8enLF++kt279wAQFzeQhQsfYMKEJMLD21FbW4uqqqSnv+69grQdlKank5FheV15gJdTHB9E7WDlhuGkz7GmSywkJ6eKKLxLyzxA+pxZZGTMgspCCir1cwuhV13dBbtSXPfGSSukICORjIzhWo+dlBhWZVjKBK0nhJtxwdIWp8CqdIcUelkO6Ru1bczOncGcOelkJGWRvNhxvjMt1V2ipexCChy+gF5ANBkZ6Z7bmLmGlEwt9bYtFWWBtf41pMSuIEOXztF+H/m4/6zSlvJyxDrmZKSjVaX1gEpzuf0yblpTMBr9tZ5Ilt5HKns5yWzOUkMOCv/kNGUcs8zT/hozGPwxGOr9UeFS2uLXiEifVXf8uzy/vch0Pg71vZJcH+c7yE5KZ5TlWCsoKLQU5vmzxKm9blM8u5e6Sktz29t2nOvb7PmcrS/37ZXzqS1T9r2LaUgyoGg9e7WnSnbLmKst4wErinZ+K5Ze/vsaOw6S1is31e66pR1Xnmnp1JNKXaR1taRVdT4f7dNCF+zShknQUijPYk5GOkm7UljcBOcl1F0zV6WmkJkzXDs/5kBlQSHWWwPHc0o7bz1/BtV7f7ioR9umHURYxihfzA7IHkvGnBWkZur2vdPnoWVfNMVnrZttcLWfnK7nlvS3TvdH+rS4ReHMtKYN1rXPU9t92q60pSSzgoyMdHrtchxC5OKqqq7meMkpUIwoVNHtV90I7BiIavKzncKmCzXUVtcSGtmOorfzUeNraH99NxSD+55CL/03hxlXDmL8gL6M/+vbLJ94A4O7dga0nr/LPvqMO64aSHhQIO/s/d7n9mau3E1SeoTrNOpO92zaObo4zcP54Hi/XN9u2ZWniJhpLVd/j2i1nsXJaPesvbJIXryUXavSbemoK3MOUEC4q5Jd83hf6qbORnQ193bfLlqQArU1Zh586HUeenASM+8cwaNL3+PjHd8TGRlGYJAfnTuF8ve/ZjFk4Tieevo21j7zKd/tO4kxLMh7+Rbthk5j1VDLi8NbKckdRHdO8/kXRXbLbSg4ycwB/bl1HGx10xvXm90r3ufrMalcc8uD3Prn0871l+/lmZsWaD1fLWmgO3c+zQ/u0j/fm8iAQDicu9lu8tYvCvld4jCueRh4thnb+moFAWMm8Nvfa6HVqvIjfPTi82xxV6gP7V2z6hn+2G05KYnWcs9xfPe7PP07N1+k+eYZNu0eyaLhE/jD/1Z7fe9+dlz/xF94ZlsCL93qe/4ft+9ZGzJ29EinMX+tv48dPZJn1jbuwv3Bv7cSOyCWoOBg27TdX39D0YmTFBdbvzyp0rFjR24el8SF8xf48INtTuXoB2Ww9QI+2IMJBfu5wzK99ItVDFkIcDc9O4dz/dJ9HNHdpuVtGspNCxvSE9h33oO/qpfgL07zfUtC7apEn/sNizZo0qQkpzF/rb9PmpTE40ufaqmmCSGEuIy15l6+7rgK/lpZp61b90KT9QJWwsKi6n1/tnr1k8yfP5drrx3D7bdP4aqr4lmwYAmHDmlPLvr06cW+fV8BcOHCBYKDg/nTn9bx6KNPNEmjXUldlU6vI4150DePVRnRHGnAQ2QBsv9a3tAhfdm33/03nq1/cFZXn6e62vfRpoKC2uPn50sP+5bX+M+BpilDiPrwdu4C1N6wBNNg529/Va/RvskWsFC74bE+rFIA44G3MX62solbKy4tcu1uDG/nrrH79YTcvBLVrKIYaulyTRr+7Y6jmvy1ALBS9/jUGOxHwaq9VHx3lq539+fsJyeo/KnCbdkRQQFsuncKpZWVrN2ZzRcFWkLTxD49eHj0NUQEBTH91fcoraxPbtFWxJJifEMrGc9ZXFpiBg7xOH9vThcUgx/mWqC2nCd/P5tTxecoLinnqmG9+fK/BTy1/DbW/PETJiYN4u6Ut6gpr8EvIhzVbGDoABlKx9kiNmeP5Oe1v2LeWy3dFtGWnTx2xPKbYvcfuqnxV8Yz7Y66tLFvv7mRgwcOoqCgKArXJSZw87gk/P392LzxPXL27q8rxlas4hwUtUvt7ExxLATrdzcd12l8INgx8KvV5SL4i7dets4BYrswrtvs0apunvV3RWuDrlL32Z6tbZNw8eWi4lx9Rjz3TFJACyGEEL6rd7c+RVFYvHgZV145jDfeeIkFC5bwwQdbbcFfgBMniiguLiEysjPBwcF8/PFOli17GkVpptQ3lhRt2T70khXicmU9//z9gzGbq6mttaaNNGD9A8zxO7v+/sH4+QU2y7eVWyX5LBGtlF/WGszt+mDu5nrwVLXG2qtfo5zIxpi15iK1TgjhiunsIWpP52EI645qOoepvAyDnxnVVGvrzA8qitFA9ckLVBddgBqVk+l5Xssuraxm3F/f5o5hA1kxcZRdD+D1X+6tV89fIYQzs2rGz98f1S+Cp//wb+77zc0MuKIrzzyXScnxMsI7hBLTrwtP/H47NZU1BLQPoabW5JigQwjRxOoS4bgcmBeAnG9zQIHJv5hEcEgwU6bfxtibR2NQFDp06ojRaOTC+Qts+ccWcvbux1Vp7scCdl+vq7bZrvV2gWDFNh3w+e/sumdpCrjo9dvkwV+7ktxNqWuTpH8WF8tnn2W1dBOEEEKINqPeAWBVVVFVlUmTpvDMM0/z/vubAOjePYby8nMA+Pv70alTR2pra/nLX15i6dKnqK2tbdKGp65KZ5RtrCxXKdqEEI6sf1wGBkYAZdTWVgJ1Y3jr/wbz9w8hICDskg/+ymeJaBPMNQRsvZ+aax6iduDtTj0JzJYAsIKK3w8b8f/6OTDXtERLhRBW509y/oP5KMYAwMz5f9WAwUUeRgXUGhVM1t4y2kNnX7yz93sJ9grR5FQUwGQyYVAMVF6oZu2q9wAF/NthCA3g1b/tBAzgH4YhMICamloUxQ9Vd18thGg5OXty+PHHfCZPHk/ffn3p1LkTAOcrKjj002E+/HAbFyouOK/oEN91mQrawyVaVa0dhfXBWvtxgfWBYOu8uhpcURz+t7xSXAVS67rs+jKsscfUz54KcBUD9rKKEE1l0qSp3heCS/5ZlhBCCOGLBqWA1rvuumsYPz6JP/zhWaqrtTRzAQEBLF68gI8/3sHnn3/ZJA0VQnjmSxpZR7W1ldTUnMdkqsX6167RGGDr+SuEaH71PXfN4f2ojfkVpm7XUvvWGACMM3ZiPPkV/vn/wFD6U3M1VQih4+vQC0KI1sVbCuicfZGoGFDwAxQUgxGD0Q8FA7VmBTBg9PMHxYjJbEA1GwADisEIGIkf8NlF2AohLk8njhborq3u00Dbfld0QVbFOk/RLeQQVHWY5JgKWivTRaWOy9gt4hi4xdKmht0juA78Qr2Cvzimi3bR+9dlOfqc0NZ0z/ptUh2XdFpV0j9fnnxPAe3lWxb10pRlCSGEEG1TowPAQojWoSEBYCFEy5NzV4i2Sc5dIdombwFgIUTrdeJoAVq6YesUxWMAGEXLqaEFW1X74K2bQK63ILAv4wG7XsRFXQ6THIPCtrTKtvmuavIWtHWxvLfUz27LUe0CuaqbsX8dWqWboA9ey6PIy4mvAeCm/QKlBICFEEKIeqeAFkIIIYQQQgghhBCitbEL+VgG5rWNkwuWcK7iamk3hbhLBe05uGTrHWstQDderr55LtbyMN/Nsj4FfusWbFjqZ/u8z7atcTH2r4R2RUMpimIJAltP34YEcJ3PNSGEEOJyZWjpBgghhBBCCCGEEEII4Z3q8aX7pRXHDqyel7dlO1ata9sv42MvVhVLbNSuYLsJ9eCwrootsO3Tum6Cv6quPN1/rqtHv+mK5+Ud1vMwQQgbLeirNKInsIIEf4UQQgiNBICFEEIIIYQQQgghRJugeoni2k21LKzY9dhV3S3tckpjg8DWpVSH+K19QNeXn7pffQ/8WlbyFvzVb5fL1jv+rtSz968+/bMQQgghhLgYJAW0EEIIIYQQQgghhGgjHNIvexnq0z4tNKiKJRW0qujSMyvO6+gmq6goKE7poLU0z3a5nr223PqLy6VdxajdT/Jem4tAscvgr9uAsn23X8eYt/T+FUIIIYRovaQHsBBCCCGEEEIIIYRoM+rbC1hF1wvYcwdgtwW56glsW6QevYH16zn96Hr3uuj/W7/S6xH89dpQu98t4yqD9P4VQgghhGjFJAAshBBCCCGEEEIIIdoQ1eNLd6xBYFW/kpvgrasYs9cgsNqwcG3TqYsg1yf46zaMrtsJ1i23pX72oSleJgghhBBCiGakXHnNzXIHJoQQQgghhBBCCCGEEEIIIYQQlwDpASyEEEIIIYQQQgghhBBCCCGEEJcICQALIYQQQgghhBBCCCGEEEIIIcQlQgLAQgghhBBCCCGEEEIIIYQQQghxifBr6QYIIYQQVlWVFZw7V0ptTRWqKkPUCyGEEEIIIYQQonVTFAU//0DCwiIIDApt6eYIIYQQgPQAFkII0UpUVlZw5nQRNdWVEvwVQgghhBBCCCFEm6CqKjXVlZw5XURlZUVLN0cIIYQAJAAshBCilagoP9vSTdCotn+EEEIIIYQQQojLlCp/GjdAq3m2IYQQ4rInAWAhhBCtQm1tTUs3AQBVUQGlpZshhBBCCCGEEEK0IMXy97Goj9bybEMIIYSQALAQQohWQVXNLd0EABQJ/gohhBBCCCGEEPL3cQO0lmcbQgghhF9LN0AIIYQQQgghhBBCCCHEJUpVQakLJk/rdI5VfUqoMakYjAYt27RqplY1MDc/iv+WB7ld92K0TwghhLgUSA9gIYQQQgghhBBCCCGEED752/q0+q2gKET61fKLjueY2bmUp3sUY0DFDzBcMGOsMmMAwv3M/DG6iCmdzjG5wzmCFfNFC/7eGhrMlcb2ddOEEEI0uZj+fYjp36elm3HZaNYewGGhIcy6+3amTf0FW7ftZPXaet4c+Ciyc0diYvpSWlZOft5hqqqrm6Ueb2L69yHx+mvo3087gPN/OkzWF1+Tl3+4RdojhBCNFRYawqIFqYSFhbqcf+5cBc+sTeNcxfmL3DIhhBBCCCGEEEK0BcNCqlgfe4roQBMV1SoXgttjOnMWc5AfypDhUFOFmreXmmqVkI7teTagmKBghW/L/Pl/P3ahsNq/eRpmCfRG+pl4vKuRjQVXsE/9GhMSABZCiKY2YdwYHlmYCsDqNWls3b6zhVt06Wu2AHBM/z4sevg3xMT05fOsr5gwfgyvvbGRk0XFTV5Xnz69qKg4T2hICPHxg8jPL+D0mbNNXo871gDJDSOvBSA//zDnKiq49547uPeeO8jLO8S5ivN8nvUVm//x4UVrlxBCNNYjC1O5ctgQ8vIPuZx/1ZVDuO83KfxhzZ8vcsvsBQUFMebmmxgUFwfAdwcP8tG/P2jRNgkhhBBCCCGEEALu61ZKdLiZqlNQ/Zu1hN16PzUnD2MICCSwW29UFSqP/ojRYETp3JPKvz2I4b2/cGW3Wu46W84fjnVslnYpgKooPNLFQJcAE5FKBImGnuxSCyUttBBCNLEJ48fYfh+ZmHBRAsD6OKU3585V8HlWNmnr/t6snZ0mjBvN+HFjPC6T9UU2777X+GfbzRIAnjZlMqnz/4eiomIWPLIcgJGJ19K1a5dmCQD7+Rk5cfIsxad+ZsCA/gwY0J8Tx4soPHYck8nc5PXphYWG8Ne01XTt2oXX3tjIq69vtJv3yMJURiZqgeErhw1GUZQmeeOEEOJiCAsLJS//EA8vWu5y/rPPLCMqqvNFbpWzsTffTLdu3Uhf/xIAU6dPZ9IvfsGH//53C7esvlJ49+DjRG7tx40PNX3p8zZ+y4ou2+g2+pGmL7zBVvPpsenwjg/b/Nx2TkwoZmncXay/KG273HXm5tmTYctrfNz0t29NLm76/cQdfJFNB+u5YuQYZs/swJfPv8dBBjL9wSTY1oBynBo0hYfivuO5Td8TN/1+JvS0zigjZ4N+n2p1RltfHs3kuU3fN7JyIYQQQgghWo99lUH8orSC80kzCJ7yEJXZH2E8X0rA4OvxU82YVYVAzFw48BVKVC8Cfv0CF/J3Y8z7igOVQd4raAhVRVUUBgVCcmcDP1eq1FDDWEM/9phOUk41qCqKBIGFEKJBYvr3YeqUyUR1iQSgf7/etnnD4uNYu3oZAEWnitn83gfNkkl31t3TadcujNfe2Oh9YWDalF9QUXGeP6/7e5O3xSosLPSifb+oSQPAYaEhLF+2iCuHDebzrK9YvUZLC3rlsMFNWY1bVdXV5Oz7jp49uxHdszvh4WHk/1RAxfkLzVbnIwtT6dq1C79OfcTpAD1Xcd4pberIxAQJAAshRBO78uqr+cuf/sTZs1r2h82bNjF73tyWDwDPfZPvl0fyUY9xPNiyLWnFHuHGHq0pIC1s4kYRzxHeuJjBX7tgbP3WG9HzGF9uamwDvmfT8/rg60CmP3gtZzbUNwjemZtH9KDwy/eAgcSRyXOWciNvmsXdt44h55WdFAORNw3izIYX2VRsWW/2ncy+qYRX/lPS2I0RQgghhBCiVZgYeg6lSxQB9/8Nf3M11UEhqF/+i9LXHyfs2S9Qiw9T/eSdqDdOx9jrCoL8jJge+DuGRVcyOeIc7/8c0uRtUgAVWNrNgNEIJoyYFJWOxhCS1H78w/yDjAUshBCNMG3KLxg/brTLeWFhoXZxQwWlWTJMhoWFEhUVyay7b/d5nalTJqOqKq+9sbFZegK/+94HFy1G2GQB4CuHDWb50oUoikLaur+3aJDz6NETlJWVMyA2hvj4OA4dPsLJk03/9DKmfx9GJl7La29sdPvthL05BxkWX3cgf56V3eTtEEKIy11wcJAt+Atw9uxZ2nfo0IItEqLti4vTAphtoPMvkUN7Qc4H9QsaN6fIIfRmP1sOAnzPJl1gunjfEUrjOxAJFAPF/3mPj21zS/j4y2M8NGIIkexsE/teCCGEEEIIb2oUoLaG2pJjGPsOIvzK0ahXjqb49Scx+hkxKUZM0x+i4y/vs61jLjkCqkqNamjy9iiW3r8T28GYCAOqyYyxuhKqTVT41zDC2JNs83GOKeWSCloIIRooKiqyWZatr/z8w6T99VXf29KlM48svI+iU8VtvjNnkwSAJ4wbzSML7yM//zCr16Y1S1ft+iorO8c3e3KI6d+Hvn16ERoSQv5PBU1ax8jEBAC7tM+OrPOGxcexN+dgmz9ghBCitfnN/fcDsOL3T7uYdx9/ebG+3x6zpGE+8CVdEkcQUfqlJeWwlqr4CstSpVlPMvD2dO3F3Df5fvkIIizzfnynHzeynRN39AdgxrGfmJG3yZJ62b4cyOdtpx7CWhsSI3Tl2dIjO6xvax+2FMlZxSNIjHFcz9m8jd+yIjHc9rpu+dV8emw8xcu2EbncWlcZWcuuZNpLnvaRfbuxbrOr1M22aduZaJf22sP2WQze+C0nrO227VdXKa4dU2p733dvH4hjhouyAaf32XoMuNuPz3/yExNP6Y4TtGkzqM+xgP26MS6Wfa7uWHNut+W9fOcgg+/Q2q61e5Cublf1DiSu5zEOWgKXkTfN4u6OR8gJH0J8uDWFsdZbNd666Y6pi+Om8ND4HraXhba0yvYpj0tz3tJ6u+qWn/Dg/UywludQjm15m87E94GCLbppdut4TrlcmLPfrqybZ99Jhy9fZFPxGGbPHEIEED3zfuLL9vPGKzspdkzZbJteJ3JoLzj8gcsAbtzoIZDzlttgdWTXdlD2nQR/hRBCCCHEJWPHuVCuPXcaw+PjqViZSWj0ACr37cKY9xWGdh1Q2nWA9Q9See0vCOzWh3N7P8H/6akoxhoyS9s3eXtUIFBRebyrAVVR4FwFAdeMJiS4DxU5hwgOa8ckQwwvmfc0ed1CCCEurnMVFXy794Db+WGhIfTv38f2+mRRMXl5hy6JbL5NEgC+95477FI+txYmk5kfcn+ypYQ+evQEVdXVTVZ+/369yfch2O0pQCyEEKJxunXv5mFe9waXe8VgWNqjnyXwWDdObTdbIPEB3p2bzrSXUnj34RGceqcfAy3znn8OeGgc3Q44poDWyumS9STdLEHBeRu/ZcXBNzmgC3JecccDFC/rR7eXsAQdv+XdA5bg63NXObRjOvc8B+utgd6IEURu7Uc31xlWdFKY2OUgS3to9c7b+C0rJrzJPKztCCdx+VW83aMfN4IWZFy+nedfqgsU2u8jLdg6+IB127TXnz73CDc+tIcfj41n4lxYbw0gT+jPj1vHsZ4UJuqb5cP2TeRJuvVIt+yb6ZY6fHhTG1P23Df5fnkcB5b1swTBV/PuRs/78cFv8jkxYRzzSLcdR1fHlJG1rC746+1YsLIFjntYg91vMtj6vtwRSZatXdp+/37jd7rAcziJEyzv1dw3+X7545w4lm95b7XlJ25MAV2gOvKma4k++hV2GZV79oINL/KcLlVx78Nv8dx/Smyvp8d9rwV546bw0HjY+vyLWqAzcgw3RwK6MXafswWDJ3Pzvtf4+OB7PFfsnAI6Lk5XTtwUHho/irj/6FJEO6aqdqw7bgoP2VIuO9avtTuaY87HS/FOXnn+pHMK6LhBDu1PYnTcTt2YwQMZHQ8FG1wHpAu3vcgrbrsqD2R0fDiF22QMYCGEEEIIcen44mwgSm+FsJKjnH98HBf+sAtj+86oBgNVL6ai1lahduqJISiYyh++xm/5rQSbzlOJgd3nmnYMYGvv37kdoV+YH6YaEzWKHyELV3BtfhWZU57iglpDnF8XhtREsp9i6QUshBANkLbu74SFhTL1V5NtnSnd6d+vN2tXL2Pr9p1s2/7JRWqhluV37eplTkO5AuzNcR80bowJ40YzftwYj8tkfZHdJMHnJsuhEdUl0uVOamlGo4HAgAAAak21TV6+KmNBCCHEJenHrfreqldxRemXvG4LMj7C61kweEKKbfkuPVNs8x50F4y0lLNWF2hbf/s2foyIY+JcXd3vWHvaAi/dxUd54XV1PTROF+x8hG/y9HUDdu30JJ1po+u2cf3Wg5RGRFI3aEEZWct0vUIfepOs0v5cmBAO7AAAIABJREFU/Zyunfp9NHccgyPy+ci2belM25rPFVevtrRTtw2WZb9x1U4fts+2/166i48c53vSiLKfv2cEZL1Q977wCNNuT8fjfnzoTbIYwT3WffbcVVxRepCPXsLnYwGAuW8yMSaft3W9kdfffhcPAs9f3Z9Su3Zp+z1i8Djm2ZYuI+tZSxtf2s6BUijNetPy3qbz0YEyIroM0lWo9ajN+cQhCHn0q7ogaOQQeocf40tbT1wtdXF03EBs499u0wVpi3fy8UEgbhDRZfv5xDbjez7Jgd5DO+POwU26cg5+RyHt6KbLDKSlqq7rgRsX14PSnF26dXaRQy/iI13VX8LHW/ZT6rZ2Vw16Txfs/Z6DRyG8q679cYOI1u8ryzrPPf8izz3/Igfj7ueh2WNwTm6kBZPDc97SlS+EEEIIIUTbt/98AKfOG1BCFAzlJZirKgmIHkTEsn9Sfa6U2vAoOvzfBgI6REHFGfwulKMGG/junD/Hq5tsBENQVVQgyk/lt1EGzKqCobyU6lvvwS86lpjRg+k++Wqqy85jMqhMNMTgb276FNRCCHE5yMs/zLd7D7B6bZrXZa1jAv/fwvuI0fXGbW6PLEilqKiYBY8st/vxpeNnQ4WFhaIoePxpKk1yBV26/BmefeYJ/pq2mtVr0vj8i9Yxzm1oSDCxsf0JCgokL+8QJpO5ScvP/6mAkYnX+rz8imUL2fyPDz12NxdCCNH6zOsZCRH9WXHsJ1boZ+QNAh5hWhy8e/BxThx73DltsGM5xXscend+R3HpdCIHu1wFgAOnynS9ZB3SLAOlp+q/TYBz6mDyPSycTmHx4y6CVhaDI4mgv5buWj+9tJh5wIOvf8n3D2u9YZkQB1kvuEl13ITb12RlpxAdCae+SXc92+1+TOejAw+wwhIEf/5qa6/neh4LgyOJKC3G+e7BTbsOFFN6hz6Yr2d5H4+62RawjF97hC2echBHdiCCHlqqZv30sjNEAh3CyzjjYv3Iru0gvAd3PzjEfsbRzkCJ8woAkXWpmC2VcMb2u32qauhMt3CI6HknD8XbF1MY2VTplR1SXwOlp+t+j4vrQeHB99yufXDTW3Sbfad9r+G4KTw0vh05G15kk+R+FkIIIYQQlxBFVTmPga8rApkUeJ7a4HCMnXpgBmr27YLjeVBxmqpD+wjsOxQ698Lk74efWkuWpfevQVUxN8ETcQVQFYXFXSA8yIjpQhVVkd3xv/cBDGYzKAauffR2tnzyHZW11fT0i+AGczQ71ALpBSyEEJeg/v378NobG51iducqKpqtznff++CipZZukgBwXv5h/t9vFvHIwlRWPLGIrdt2krbu7y2aDtqa9rmqsoqcnINUnL/Q5HV8npXNrLtv5957bvea5nnalMmMTLyW197Y5HE5IYQQrc/6o8WsyNvmNrAL6UyL0wJqz3/yEyc+weWy648Ws2JwT+aBQ+CvjGIP3w0a3CUcToF+TNtulp6sz3/yk30KZV9Zx+DtoQUktRTHbsO7WIONbh0oprS0mLUu0hcDWs/Thx9g4twUGAwHnnUVgGzC7WvSsrWg6eCeKYBDu73sx/VbD7Jg+VU8z2qujsnnG0tq7nodC24Duh7a5TJg7Ju40UPg8Fueg6TFZygtO8MWh7FvNZ05UxZOh0hwnFl8shw6fmU/VrAnkdaU0JZ0zgxk+oN1X75zTlVdwokytDF8XfaiLYc+XYnk+7qmRXbQBZe9qRsj+DlL+XHT72eErr0jeh7jy/rc7sVN4aERZ3jj+fdk3F8hhBBCCHHJURRtzN1dpcFMjrpAcEkRNb+fQllsIuaKMwTNeQb1/FnO/3sd1WGdCNi9Bf+aWpRAhV1ntQBwU+RftKZ+vjIY7uhspLZWQS0voyplMWGdo6itrkZFISquH/1m3sgPaVvx7+LPGGMfvqk5SalShaqqKBIEFkIIn8T070NoaAhTfzXZ67IVFef5du8BPs/KJq8Ze9862rb9E6b+arJTpt+oLpEUnWr7T2maLIfFyaJiHl60nNfe2MiE8WP4a9rqi9pV28poNBAXF0t0z+6cPn2GvfuaJ/gLWuD786yvmHX37R63NaZ/H2bdfTt7cw5c1INXCCGa0sjrE9jw6otsePVFRl7vedyGS85De/gxZjqfPudq5mo+/WS17dWBU2Wey4kYwYKNdWmH5218gEQsaYEtrpjwZl363ue2MyPGmlp5EJER+gDhaq6O8dTwFN49+BPfb3ROkaz1QD1qCz7OmxDnEAQLJ/Geuu3S2ukhvfRL2zmA/bbZS+ejAzD4nrsY7LC9deq7ffbWHy2GmKt4Xt9m20Y1ruwHv8knIvEB3rWlZ17NuxtTvO/Hl+7io7z+TDw4ni62tMt4Pxbmvsn3x77V6ntpOwdK+zPjE/378SbPu2yXNiY1B7a7DsR7pfWorUvt7EbxfgoYwq03uUrdXELO4TKix08hzjopcgw3x6GlcO6ZxPQ4F6u5EtmBiLIzdYHRuEFE22a6TlV98OAx+7rt2n2G0vAhjLbN1NJV+66zQ+/mgcT11DV3aC/Qp58GiJtit72RN00mPvwYB61jEI+wT2EthBBCCCHEpcRseab+RVkQpioVQ6iB4C8z8ft6C0HzniVk2I2EXn8bgcmP47dtPcG532IIUTh9QeHbikBA67XbVJZ1VcDoj9+FUvyvu472M3+NnwJ+gQH4B/pjUGDsk8l0HtyNmvMXCDeGMN7YD/4/e/cdZldZ7n38u9Zu02fSE9JJICG0EIVQgkgLAopKUxEBy7EgvnbRcxQO2LDA4SgiBwtVUYyCKCqIgBA6hIReEhJCQurMZNqe2W2t9489GRLSYZJJwvdzXdHsvZ/1rHuvK4Qr+XHfD+UOYknS5jn702dxyY/+m2mHbHqK7tx5Czjvgh9x2z/v3gaVveZnP7+K+x94lJNPfDdnfuTUnh9Dhw7epnVsLb14iELZNdf9gTlPPMPXvnw2/3f5D/nZz6/ipfkLe/s269W/XwPjxo0GAubOnc+KlU2bvObN+uGPL+eSHw3m/y7/Iddcd+M6Hb7HHH0YZ3/6LIIg4PIrrtnq9UhSb6upruZrXz6bY6a/kzlPlNOz1dMeaqqrt+pIjM2x5NUlDNtl2AY/6x1f4x3nj+C5C15iyamr32vl/vMnc9Ivn2XFoG+xZPHJ5bdbHuS8Sd3dv788jX985KXyWOTu0dDvGA73LO4eF92zfu2u2Refhi/3jJsu3+cL3XVcd/90LrzgJZZcADCPF+e+sW/0i1Nu5yOLT+6pu2XuvNedg9rK/cv3Y8nil7pfz+P3wzfQ3QuUu6D3WPu7AS/euGvPubvlbtgDWX7j0RvY501+vy8eze+nvNQzhrrl/hnc33Jy99jqN7/3MP7Jkp7ry9/tF1/cYxPPsRzSLjl1EPfftmaX7ub9Wii7ao0x42v8OttAXS33f5uJp2xkxPPGTNqDkYueZdMNrCv516/v4OQvrD1q+ZXby523K+68lus5g9N7RkS38sRvAJ5jxm+G8rEPn8MXp6++qpUnfnNt+czcFXfz4KJzyqOlF93B/8y4lycO/OBrI6MXLeaV1ZdtaFT1Mzdx/dA17w20PsX1v76bFSvu5te/YY37t/LE7U/RMr3fBr7nc/z7iQM4/cPnsE/3Hj2vAVjMK4tWry0H0i//5XXh+Ypm6tb6vou57dKb1gqJR05f8/PXPRNJkiRpR9Yd3s7tTPFSV4rdqgvk6xNUv/Q42c/sRcc3/0TcvJTED06jMruMXGWCTFhiVnsFLVGi10Yvx0HAu+sCDmxIQNTGq8U67o+PJ/N/D0OpCGG5TyqOIhKpFMtH7U684EEyUZYDEiN4MF7MK2zkP/iWJG3Uffc/wnkX/AiAC8//KoccvPUbjJYtW8muY0dt8PP2jiw/+PHP1nn/yst/yLJlm2iOeIOOOfowph/9zo2uuf+BR3plTHQw+e1H9sYUjXXUVFfxta+czSEHH8DSpcsZOnQw7z3xrK0yFnrK5L2IgYqKDB0dWZ5/fh65fL7X77Mha35XgLlz59PekWXyvuVhjcuWreC8C35k96+kHc4lPzqfffcp/1527fV/6Bl3f+ZHTuGM008BYM4TT/Olr17wpu+19NWXNr1I28gPuWfxdFacP5mT1tupqy2yekz0hsZjbzdeG2+8/vHJ25dJJ5/DgU2/49eb6lbeVlaPcl7vWGxJkiRp53HlLy7nk/9x9mavXz1++fiGDn65x3KIoBAFpHIxXXW1hMUu0p0FCumAVDKmoxhw8tPDmN2R7t7gzQfAlcTM3D1PfaaSW1ftw7+yB9LakSLIda6zfxzHpKorqEwVGNq1mN0KWeZFK7kiemKz7jV0l13fdL2StKM79yufZfrRh/W8/vLXLug5a3fyvnty8Q/P7/ns9n/+e71B7Ju1+j6rM7vNUVNdxfjxY9eqtzeVj4vdePh93/29EwD3egfwau0dWc674MccctD+nHTicVx7/R+22pnAc19awMgRw1ixspFFi3qr22vzrf6u48eN4ZCD92fcrqOpqanm2uv/wNy5C7jvgUe2eU2S1Bsuv+Iazv70mVx7/Yy1/oW3etrDGaef7HQDaaN+yD2njuPFDXY9b0cG7cVonuIvO0D42zOqesZ2Ev4CkyYN55UHPcdXkiRJer04CCCOuXVVNR96egjfG9vI2JoiURhQ0dlGHAAV5fD38VUZvjp/AE9nM0DvdP8CfHNYgtZwP3669GCWRsOpCgsMro8grF1/zaWIKA5ZVjuAlnwTE/P9mdbZzMz4lfWulySt7Y833QrAkCHl2Xzt7a9NkWxv72DOE+W/gFq2bAU/u+LqrVLD7DlP8+WvXcAxR7+TIUPWd4zZul6av5CfX3ntVgl/Af540996JdzdHFutA1iSpC1hB/D2xA7g3nDpv1/iA+Pf5EhmSZIkSdrObGkH8GqrO4HrwhLnjlrFmYNbSQRAAJ2lgJ8sbuCyV+spEvSs7S0n9ZvEC/ndCQmoCHNEhJu+KIaAmGKQJCAimVvEQ/nHN3mZHcCSpO2BAbAkabtgACxJkiRJ0s4tjGOi7mD3yPosF41tZFUh5KvzBzI7mwHo9fB3WzMAliRtD7baCGhJkiRJkiRJklaL1gh2/9VSxf6zq9ZZsyOHv5IkbS82Y9aFJElbXxBsH/9KinEwhiRJkiRJ/vl4y20vf7chSZL/RpIkbReSyVRflwBAEAfgH3IlSZIkSW9pcfefj7Ultpe/25AkyQBYkrRdqK5t6OsSyoKe/5EkSZIk6S0q8I/Gb8B283cbkqS3PANgSdJ2oaKimn79h5BKVxB43o8kSZIkSdoBBEFAKl1Bv/5DqKio7utyJEkCINnXBUiStFqmopqMf1iSJEmSJEmSJOkNswNYkiRJkiRJkiRJknYSBsCSJEmSJEmSJEmStJMwAJYkSZIkSZIkSZKknYQBsCRJkiRJkiRJkiTtJJLNweF9XcM2FSYyfV2CJEmSJEmSJEmSJK1j9m2ffNN72AEsSZIkSZIkSZIkSTsJA2BJkiRJkiRJkiRJ2kkYAEuSJEmSJEmSJEnSTsIAWJIkSZIkSZIkSZJ2EgbAkiRJkiRJkiRJkrSTMACWJEmSJEmSJEmSpJ2EAbAkSZIkSZIkSZIk7QBuueWWTa4xAJYkSZIkSZIkSZKk7dzq8HdTIbABsCRJkiRJkiRJkiRtx14f+m4sBDYAliRJkiRJkiRJkqTt1IbC3g29n9yaxUiSJEmSJEmSJEmS3rgTTjhhi9bbASxJkiRJkiRJkiRJOwkDYEmSJEmSJEmSJEnaSRgAS5IkSZIkSZIkSdJOwjOAJUmSJEmSJEmSdlB7TRzC5z56EIdOHUNFxtjnra4rV+Tehxbw06se4KnnlvV1Oeoj/k4gSZIkSZIkSZK0A9pz90H86kfvpiKTpJDvpJDv64p2brW1tX1dwiZVZJIc/Y7xHDp1DKd++gZD4LcoA2BJkiRJkiRJkqQd0Kc/8ja7frVeFZkkn/voQXzq3Jv7uhS9QbNnz97iayZPngx4BrAkSZIkSZIkSdIO6YDJw/u6BG3HDp06pq9LUB8xAJYkSZIkSZIkSdoB2f2rjfHXx1uXAbAkSZIkSZIkSZIk7SQMgCVJkiRJkiRJkiRpJ2EALEmSJEmSJEmSJEk7CQNgSZIkSZIkSZIkSVvs8EN25Y+/+DCf//jBfV2K1uDpz5IkSZIkSZIkSQIgtaSZilN/CKk02d9/mdKgur4uSduh6qo073vXJN591AT2njiEfvUVZNIJbvrHM7w4v7Gvy3vLMwDehLGj+nH6ifvRUFfxhvcolSL+ee9c/nnP3F6sTJIkSZIkSZIkqfekn11E5sj/gvYuAKomf4Gue75PYdyQPq5M25MPnziZcz56IIP6V9Pc0sncBY1UVab5jw/vz5mnTuHmfzzLf/3g9r4us8dBbx/F0EE1Pa+LxYhH5ixm6fI2AIYOrmXynsOY/fSSnvd2dFs1AK6KCwyIc7QFabIkKAQhMcEW73P0CQ9Q29DOP28+gLbW+q1Q6YYNHVzLsUfsvtYvjC1VKJRYsrzNAFiSJEmSJEmSJG2XKh6bR2r6t6BQeu3Ntg4qDvwK4d3fI7fnyL4rTtuND79/X776mUMpFEqcf/G/+M2fZvd8duCUkXzjnMM49T17kckk+MqFf+/DSmG3sQO46D/fxeQ9hxGsEU8WCiWuuO5h/ucX9wHwuY8exAffuw8vvLSS/7zoNh5/akkfVdx7tmoAHALTS4t5NqhnSNxJHMCioIaFYQ2NQYbSZhxBPKjUzop/7soDyQo6s9Vbs9z1KhYjsp15OrL5N7xHvlAily/2YlWSJEmSJEmSJEm9o2L2y6SOOW/t8He1XJ70O/8T7v4+uT1HbPvitN3Yb69hfPL0A+jsKvCN793G3Q/MX+vzB2e9wmnn3MjV/3MSxx0xgbkLmrji2of6qFr45ucPXyf8BYgpT+9dLZEICAKYMG4g3/v6MTtFCNzrAXAiDEgkQ1LJBIlEBUsTI1lYOYxZje2EuRwD4y5GxB1MiFqIYigECTqDBK2kaAnStAWptYLhz+bnMLWqmv9YPpb2xLafWP3I7EUc9YFfb/P7SpIkSZIkSZIkbW1hZ57U6T+GjTWy5fKkD/8G4e/OpfOIvTa556pVLfzt9n/x1DPPkUgkePt++3LM0YdTkcn0YuWbZ9GrS3js8Tkcc+Q7qah448d9bg1xHHPTX/7GDTf+if/62heZvM9e/O22O7jquhvWWVtfX8f53/gKI0cM74NKy953zCR2GVLL7255cp3wd7WObJ7r/jibC75yJMcdsXufBcBHThvHpN0HEwSw4JVmfnXDo2S7CgB0dhW596EF671uZwmB31SiOnXKKIYNrmPwwBqGDKxhQP9qBvSrol99FbU1GdLpBOlEQDouce1fnuZHP7+bhUENC1l7nHJ1XGBU1M7eURND405eCOq5JzkMgKtTk1i8+GWWVdSQTIQU10jkJUmSJEmSJEmS9CbkCtDRuel1XXmSJ32Pqos/TvZjR25wWXt7B5dd+Wt2HTOaL57zKVpaW7n+d39kybLlfPyMD5FKpXqx+E1btaqFhx99nMMPPWS7C4BnzX6Ce2Y+wJAhg3veO+6YozjumKPWWvfwo7O49/6HGLrGur4wfuwA2jryPPDowo2uu+X2Z/nEaW9n8IAaph0wmpkPv7yNKnxNXW2Gikw5Bn149iJ+c9Oczb52ZwiB31QAvM8ewwiCgI5snqeeX0pHtkBHNkdXZ56OriKdXQVyuSKHZBfyucvO4bKrUuwytI7qyjRBGDDn6Vc5dOpY6moquPO+ueQH70rH/KdZtcsYLvjIO0klQ4YOqeO/f/QP/vuMg4mimD/97Ukef2pxb31/SZIkSZIkSZKkt6yH8yGH7zUO/j1704tLJRJf/hW1jW20ffm9EAbrLFm4aDGdnV0cd8xRNNTXMWjgAD72kQ/y6Kw5tHd0UFlRwW3/upu37bcvI3YZ1nPdoleX8MKLczn04IMolYrc9q+72XvPPXj+hbk8/OjjpFIpjp1+BJP32Yuge6ZvHMc8+/wL/P32O1nV0sqUyXtz+KGHMGvOE+y+23gA7rrnPlpaWrnpL3+joaF+u+kEbmxq5o9//isfOOl93H3vfRtcVygUuPf+hzj04KnbPDx/vYH9q+nKFWlu3fR/MNDalmPsyH4MGrDtj3fdkFPevTdf+tQh1FSl13o/kVj3yNodPQR+UwHwL37zWtt2Io54T3EhTycHsW+pkb8lR3HckXtwx70vcEs8gI905Jk6ZRQfPnEKuVyRwYNqufiKuzn2iD0IAnhu3nJOG1niT6/U8I1vncR3//dfjB7Rj371lXzxU+/k6t8/wqvLWnnX4RO2aQC8/+QRfP8b0xkysGbTizcgXyjxqxse5fJr+m7OuSRJkiRJkiRJ0pr+5/FGZuUCDr/5v+DUH8Adj0Ecb/yiUgm++3tqO/O0n/t+4szaoWRFRYZCoUBLaysN9XUAjBo5glEjy+cHt7a28fCjjzNu7Ji1AuBVq1p44qlnmXbQVPL5Ag8/+jgPPvwYB019Ox/+4Ek8+9yL/Pq6G/jgSe/jkIMOII5j7vz3TG7+69+ZfsQ7GTliOK8sWsz/Xv4LiqUSgwcNYuSIXRg1YjjzX17I7uPHUV9fRyKR6N2H+AaUSiV+N+Mm9po0kcn77rXRAHjhosW0trYxcffdtmGFO6e37bPLFuV9O3IIvG6k/Qb0q69kBB0QwHsLC6iJi6TTCU57/35EUcxnzzqEl/75AO85ciKf/c8/8cyLy7j97uc574tH8+Of300YBoyrDXi2WEM4YQJLlrXy4vwVHDltPHOeeZU5T7/Kglea+NUlp/Ln257ujZI3WzIZUlWZprrqjf+oqUqTSW/784slSZIkSZIkSZJeL4pjvnLnEi78y0KmDqqChmq4/UJ49kr44ikwbV/Yf3d4224wfhfoX/e6DSL48R+p+fGf19l79MgRTJm8Dxdd/BMuu+JXzH7iKfKFwhuq88D938Z7jp3O+F3H8p7jpnPs0UfyyKzZ5PN5lq9Yye13/puPfeRDvOe46UzeZ0/ec9x0Tjj+GJavWAlAfV0d43YdQ2VFBXvuMYFJE3fv8y5agDvuuofW1jbe/57jCIN1u6hXK5VK3P6vu9lv372pq6vdhhVqtQnjBnL+lzY88nx71Sup5KUXvpevfv4aGoMM96eGQSLgy596J3+78znGjxnI6Se9jZ9e0cqkpSsoFSPGjxnI/Y8u4O93PsdHTn4bYS7H4bWd/K1Ux3FHjuaCS/7JkdN2I5EIufCr7+Ib37uVaQeM5Ze/fYjWtq7eKHmzLV3ext/vfIGGujc+DqBUinjq+WW9WJUkSZIkSZIkSdKWy5diTvzDfGa+0EoUwxONWWIgAJgwHC756PovvOZO+MSlUCyWXwcBtK+b2SQSCT5w0ns58rBpPPjILP7813/wq2t/y6knvpdpBx2wRbWO23VMz7hngFEjh3Pfgw/T1ZVjxcpG0qkU43cdu9Y143cdy+BBA7foPtvS/AULueOuezj7Pz5KZWUluVxug2uXLlvO/Pkvc8Jxx2zDCrWmUhSzorGjr8vYYr0SAFdVpnnf6YfRr76SY+sq6VdfydHv2J25C1by4ffvx+e/dTOnnTiF333nek44+u2EYcDZZx7Mb256nM7OPPtOGsbf+41jTGWKgf2rae/I8dEP7E8MTBg3iEm7D+FjHzyAn/xqJu86fCL/uOu53ih7s8xf2My3L71zm91PkiRJkiRJkiRpa/nATQu45/mWntc3/vMVDhpYxf/bd9DGLzzjCHhsHvz0JghD+MKJtH3zxPUuDYKAQYMG8p7jpvPuY49m1uwnufGmW9h9/K5U9uL5u6lUijBce9htGIbbRZfv+nR0dHD19b9j6JDBNDY109jUTKFQoLl5FS/OfYm6ulp2HTO6Z/0jj81m7NjRDB0yuA+rfp04pliMNrmsVNr0mm0tlysSRTHhes6uXp9SFHPPA/P5wvm3buXKel+vBMC//O1DjB87kKUr2pg7fyXHHrkHP7z8Lh587GVWNndQU5XhsTmLmBfU8rNz3sEf7niRG25+nEwCdt9tMN++9A7GjxnIGe/ak+/9pBy2PjhrIb/50ywOP2QcI3Zp4J/3vMCo4Q2s2oyDpSVJkiRJkiRJkrSuwdXptV5HxYjPX/csX//jizRUphhYmSAEqiqSpIZWccHBu/DOwVXlFuFxQyGRgEs+QdtZR3S3Da9t0atLeGzWHI4+4h1UVVURBAHjdh1DRSZDW3s7NdXVZDIZurrW7h5+dcnSLfoeNTXVtLW1saKxkerqqp73VzQ2snz5ii3aa1tpbmmlUCiw8JVFXHfDjQCUooiVKxtpaW2jWCr1BMCtrW08/NgsTjv1pO3i3OL9J4+gtjpNS1uOR2Yv2uT6RUtaOWC/EYwcVr8Nqts8f7/rBcaPGUC/hsq13q+vrWDYkLVHbK8Of//feX+lI5vflmX2il4JgG+7+3luu/t5AKoqU5xxytu58voHez6fOmUUf77tabqiBBeeez0PPL2cZclqvtXvVb7fNIhVUZL5C5v45z0v9Fxz6S/uAeDGW+b0RolvWHVVmkOnjqGy4o0/qlIp5tkXl/Pi/MZerEySJEmSJEmSJGnLXHz0LiQSAb95YBnxGu93Zot0ZossWXPx3FUcft8S9pjYj4fO2IPap16hcMNX6XrXfhvcP5lIcN+DD1NZWcGR7zwUgIcfnUUqlWTo4MFUVVWy5x6789d/3MGokSMYNHAACxa+wr/+PZNhW9DpOnzYUMbtOpYbbvwTZ374AwwbOoQlS5fxx5v/SrBGh2cqlSKKIto7Ovr8HN0Ruwzjexf811rvtbS0cv53f8hZp3+Qyfvs1fP+rNlPUFlRwbixo1+/TZ+Ytv9o+ver4pE5izdr/bNzl1Mq7cG+k4Zt5co2LZHh5Wg7AAAgAElEQVQod4k/OOsVHpz1yjqfX/Sfx3Dqe/bueb2jh7/QSwHwmj55+kHcdf/ctd57aNbCnp/PeCbPYaVVjI7a+PmKelYFvV5Cr9pn0lDO++IRDB1U84b3KBRKXHHdw/zPL+7rxcokSZIkSZIkSZK2TE0y4KdHD6M2FfB/9y4lijdxQRzz7LNN7HPdszz5tfcSD63b6PKhQwbzyY99hOt/90d+N+NmAEaNHMEnzvowNTXVABw7/UhWtbTyn//9PaIoYsJu4zn68Hfw1DObfwRoKpXijNNO4fd//DPnf/dHlEolBvTvx2mnnsi/Zz7Qs27s6JHsMWE3zv/ujxg8aCDnfvGcPg+CN6Wzs5N773+Qow4/jMrKyk1fsJXtNnYA7zl6Ivl8iXkLGnn/sZM2eU1nZ4FXl7UydcpIzjp1ClffOGsbVPqapSvaaevIU12V5qhDx3HZd99DLlcCIF8o8efbnllvGLwzhL8Awej9v7Opf7Q3WyIMuPHKMzjrC7+jrX3Dh1YfWFpGZ5BkTjigt2692cJEZovWH/T2UVx83nEGwJIkSZIkSZIkabsy5/ZPvanrf/DQCr5/26JNh8DAwOokj39uT+oz4aYXb6ZSqUQpiki/yTN7S6UShUKBil48X3h9amu37+B4fXY96Mdv6vrdxg7g0gvezcTxgwg27+jcdbS257j0F/dt8xD4x+cdy3uPmUTidWf+vj63W90BvL2Ev7Nv+2T5/2fP3uJrJ0+eDPRyB/D0d07g73c+t9HwF+DhxGB6LXXeyh54dCEHn3BFX5chSZIkSZIkSZLUq86dOoghDWm+OmMBuWK00bUDatOkk28wAdyARCLRK+fb9tY+WteL8xs5/oxr1vvZ6SdN5uufPYyObJ5+9ZX8/a4X+Px5f93GFW7Y+T/+F7lciXcfNYHamtcaRGOgVHrt13tTc5auXJEHHl3Y5+Fvb+nVDuAdwZZ2AEuSJEmSJEmSJG2P3mwH8Gp/WZTlrKteoLCBVuB0MuSOz+zB5P7pXrnfjuqt2AG8Kd845zAOOWA0i5a0cvEV9/Li/Mater+3gu2uA1iSJEmSJEmSJEk7lveMqGLGxydw8q+eXycETiVDbv+PCW/58Ffr9/3L/t3XJWg9em9QuyRJkiRJkiRJknZIh+9Sye2f3oOq1GvRUXUmwcxPT2TK4K17tq6k3mUALEmSJEmSJEmSJN42KMO9n5vE7kOr2HN4FY99bhJ7DPBoTWlH4whoSZIkSZIkSZIkAbBbbYpHPzWhr8uQ9CbYASxJkiRJkiRJkiRJOwkDYEmSJEmSJEmSJEnaSRgAS5IkSZIkSZIkSdJOwgBYkiRJkiRJkiRpB9SVK/Z1CdqO+evjrcsAWJIkSZIkSZIkaQf08OzFfV2CtmP3PrSgr0tQHzEAliRJkiRJkiRJ2gFdcd1jdnlqvbpyRX561QN9XYb6SLKvC5AkSZIkSZIkSdKWe/qFFXz8q3/lcx89iEOnjqEiY+zzVteVK3LvQwv46VUP8NRzy/q6HPURfyeQJEmSJEmSJEnaQT313DI+de7NfV2GpO2II6AlSZIkSZIkSZIkaSdhACxJkiRJkiRJkiRJOwkDYEmSJEmSJEmSJEnaSRgAS5IkSZIkSZIkSdJOwgBYkiRJkiRJkiRJknYSBsCSJEmSJEmSJEmStJMwAJYkSZIkSZIkSZKknYQBsCRJkiRJkiRJkiTtJAyAJUmSJEmSJEmSJGknYQAsSZIkSZIkSZIkSTsJA2BJkiRJkiRJkiRJ2kkYAEuSJEmSJEmSJEnSTsIAWJIkSZIkSZIkSZJ2EgbAkiRJkiRJkiRJkrSTMACWJEmSJEmSJEmSpJ2EAbAkSZIkSZIkSZIk7SSSYSLT1zVIkiRJkiRJkiRJknpBct79nyEmJAwyxHRBHBDEEYQJ4jgGos3aKIghDhMEUUwcRARBNc0v/5XGmR9i0GE3Ur/LsVBYwst/OxKCAv0O+gXFZQ9RaH6AjlULqd3t4wya9BlKzY/y8u1HM3Ta76gafixEHUQhEIcEROV64gxBmKRrxcO8+o/DqNnriwza7yKIOwiCAKKAOIy7a3mtPgjJdsXl10HQ82PN1+v7ec93XOPnG3tPkiRJkiRJkiRJkrZUa2vrm94jGQQBARk6W5+jIlUHmcEQxsRxabM36QlB4xh6AtEiNQ1jaEkmiNufIw6PhXQdw6b/jVIMzbMvJLfgOoIwAXGOdEWGmJCW5oUUSmkKyRrKYW9IGMcQR8QBBHFIHBSI4xRNz/0vyaoRNEw6hyDOQwzlRUH552EIUTnMjYPN/z6SJEmSJEmSJEmStCMKiZMUsi+w5I5jWTX/GgjTxHFMwOZ1tgZBAHFIeXkEQTmDjeICycpRxJVjaV92N0FcAiooZRfT+OCnyC+aQZiuokSJVP+DqRn+/vL12flkMhkqK4dSokAc0hMsByQAKAUVFJvn0LX4FqomfJJ0egRERSBJHBSJiQniiDguQJCHoAhEBOU2YEmSJEmSJEmSJEnaKYUEaZpf+C1x5wrSw44kigsEpIhiegLXjYnjuBzQ9rwuEZIgDGKCdB3VQw+i1PgU+dxKgiBBvnE2haX3USplKZGhYvC7GDj1MhKZegIg6nyFRLqedO0oEnEAlMdRR1E5ZI5CSJCgdfFtEFVQPfr9EOSIEiFQLH8lgp5u4SAoj7IOgoAo2rxx1pIkSZIkSZIkSZK0I0oWSx3kltxG7dBjqK5/O1AkCgqEcYqYAhBudIOge9xyTAQkiONSeQp0HBIHkO43lZYXrqHUNpe4YiD1Ez5OcuDbiDrmE1QMp2LwFJJUQlyiFOfJrnyCUs3uREAYl8qhLyXCoJwzhzEQlsituI+KQVOorBpVLiQqAgkCus8uDlMQRN01lusJAyiHxJIkSZIkSZIkSZK08wmLbQvoan2ZxKCpxGFIHAdADYQJ4mDj4S90dwADMeWzdsMg7ukKDihQNWRfEokk2cY53R3FEdX9307tqFOoGTyNJEmgE4KQqGs5Uduz9B/0dkIy3V283fcJAELisJI4ShAWVpCoHkYcZiDOEIZVEFZCkCQO08QxRHFAKYqI4piYEpDbOk9RkiRJkiRJkiRJkrYDyajYRhx1kOk3kYAQSjlaFlxPVf8ppOv37O4C3rCg3F5LEIfls3d7PomAElSMIFE9luLKByE+mygICegiCALimO6QNyYgSW7FoxTyWZL1+5UD5DCAclXldVFIrmkWzS9eR67jZeLscpY/8nmS6X4EdBESExGQ71xFrquNIJEk7mokLnWRiLIkoiwNRz6wNZ+nJEmSJEmSJEmSJPWZZII8FUCqZhegREfTIzTe+//g0CtIN+wH8cYD4DiOCWIIguQaYXFEHEQEUUg63UC6/560Nz1BbVcLAUmiKO4+azggCBKEQZIgkaP1lb9RUTmYzLD9ISiU96Y7JSagFORZ9uDZRM1PEtXtQapmNPnm2RRK7YRhikJcTxRAFIYkIoiKHZCqJQ6rSFcPJczUbeXHKUmSJEmSJEmSJEl9JxmQIAogSFZDnKB90d9I1gyicsg7iOnarE3K45nL4W9MSBzH5HJ5crl2isUETfkxrFp8C8U5vyVZN54wzFAKkgRAFHeSKnbS1fQUq576A5VjTqJyVUwq1Ug6XUEmk6J8zHBAGFdSMWQaHc1PkIxyJCtqyFfuQiozkGSigrhrFVGxhUTcSSLOEnXGREGBrlwznflGEokURsCSJEmSJEmSJEmSdlbJuPtcXgiAIvmmOaQG7E+majRx3LFFm3V2dtLZ2UVX1xrBcZChepfDaF1wAy2Pf4VSXEUpVU0ikSIoQanUThjniCmQHHAA9bueQT6fJ9/VRUfQBXFMuipNdaaWiqqIAft9m1S/yXQt+iuFxucp5VeRL+YIQoiDJEGygmSygkymjjBTT5zIkKkcQRykIV3Tm89OkiRJkiRJkiRJkrYrySAMIU4RBCH5/EoKLS9Qt/c3IIAgThBT2ugGURTR0ZGlo6ODKIrW+Tymi3Td7uxyyK+JuhaRbZpPIbeMiIAgLpFI1ZGpH0u6ahjp2nEEYS1xnIMg6j5fGPLZLvKdBYKWkJraKurGnErD6JMo5FuJC01ExU4IIggqCcMqEskKwnQlQVgJhEAJYgiCBNncxr+PJEmSJEmSJEmSJO2okoQJ4qBEggRRqYOo1EGmYTdiINhE+NvW1k57eztxHG9wTRAHEEWkqsdC7RjSAw7tbjgOKP8kJA4gDErl9+I8MSEhCSJiYmISQbp7HHWStpYW2lqz1NZVUVtTDxUDKIVx+Tzh7jLiICYEiErEUG5uLhfz5p6WJEmSJEmSJEmSJG3HkkEYEgYBURjT2fISYVBDnBwIFCFIQLxuCNzZ2Ulraxul0qa7aYM4QRTGBJTKTbpBiThMEIQRcVzuzg2CAOKImBTEAWEYE5dCgjAmpBwgB4kUUIIwSYkSra2ryGbbqKvtR2VFxRp3jAiI6U6VCaJyyExc6v4uwXrrlCRJkiRJkiRJkqQdXTKOIuISBGGGfPNc0hU1ZOpHEcQR5Q7d18RxTEtLK9lsdgtuEUNcIgjD8thnEgRhABEEqztyowIEGQKKRGGJIE5AWCIIYmJCCANiIsI4hCgmkQiI4yTFYkxzczO5qkrq62ohTADl0LlcbzlwBoiDoGektCRJkiRJkiRJkiTtjJJRvgsSAXEMYdcCCqkGgkQdBLnuUc3lhfl8nlWrWigWi1t4i4ggSEAEYVDuzI0pEQBxEJWnMgdJIIKoRBikiIgJgwiiRPn2YVzOkeMSQSJBHJUICIiC8tG/2c4s+UKe+vpa0ukUUQhBHBIEJSICQhIQx5QnVTsGWpIkSZIkSZIkSdLOKRlEnRCGxIUOOhqforJhTyDR3T2bAEp0dnbS3LzqDd0gTITkc0VWNDezZPGrPPnkMyxZspSWllZKpRJhGFBfX8eee07iwIMPoLaykoqqKqLuLuAwBqJk+fzeMAQCghCIY4I4hCAijmMKhRKNK1vp11BDRVUlAHEcEHSPf7b7V5IkSZIkSZIkSdLOLpltfZIgqCefXUah9Slqxr6XIAiI4pAgjunIdtDS0rrRTVZPciZOUUoUSCVCujpLPP/8i7zwwjyam5tpamoim83S0lLuIs5ms+TzeYrFInEc89JLC3j11WUcddRR/PSnFzBx4u4c864jGTtmBEEQEQQhcVyEOAFBOfgNiLq7eqHcrhzRvGoVdXFEdXX1a+8H8Frnr0GwJEmSJEmSJEmSpJ1TsuXVR4kqBtC66HbiKEt64FQgIqBER0eWltZNhb8JCCOiOCCTCojyIc88t4DFixezcOHCnuC3s7OTjo4OOjs7ieOYXC5HsVjs7gIOyeVyLFmyhBUrVhAEAfPmzef/rriK+vp63vWuo9l3v0nEhCSDCOIQgrUmVAOvRbyrA+vVIbBDnyVJkiRJkiRJkiS9FSTjpscIA+h8+QZqdjmeqrpJRHSR6+ikpbVjM7aICcOQVKKClStX8corr7B48WKamprI5/Ok02mSySS1tbUUCgXa2tpob28nk8kQRRGFQoE4jgmCgAEDBjB27FgSiQTFYpFisUhLSwv/+tfdPPPM87zjHQcxcuRwgiCGKCCMA+IwWm9VLS2thGFIZWVl7z4xSZIkSZIkSZIkSdpOJcPiSiKKJFMDqZ74WQhD8rkCzS2tbE7vbKYiRb6QYMmSFSxfvpy2tjYqKysZNWoUxWKRfD5PIpFgyJAh9OvXj1KpxPLly3s6f+M4JpVKlffKZLjnnnsolUokk0nCMCSRSFAqlchms9x//yPU1DzDkUcdRqYiQRhvfJxzc/MqEokE6XS6N57Vm7Zw5i+5Zd7a7/UfM5UD9p3I+IZU3xS1US3MnfUcTQMmcsDo+r4uRpIkSZIkSZIkSdImJCEiLnVROfIw0g17EMchrc3NREFM+PoZy69TXV1HU1M7jY1LaW1tpbOzkzAMCYKg53zflpYWCoUCtbW1DBs2jCAIqKiooFAoEEURYRhSVVVFTU0N1dXVvPzyy1RUVJDJZHr2Wj0yulQqkcvlufmmW9l//8mM33UsBOvvAF5t1aoWBg0aSBD0/dm/TS8+xC13pOg/pIqeSPqBh7j6Shg6/TNcfuYUavqywHXM4x8X/5JbjvoMt390Sl8XI0mSJEmSJEmSJGkTklFUIEwOoG3RLeSzC0mMPpeoYgIhRSABlNa5KAgCKitraW3tYsmSJXR1dbFq1SqKxWLPeb+tra0UCgVKpRKlUol58+ZRXV1NXV1dT5gbRVFPMJtKpQiCgHe/+908+eSTdHR0EMdxz5nBxWKxJxSO45g777yXpqYm9t9/v41+wXII3UpDw/bSwbo3X7/kM0zueV1g4d8v4uzrf85F437Md6ZtL3VKkiRJkiRJkiRJ2tEkd3nHjRSCaqLsCyx97Pu0LPkiQw6+imRmMMT5dS4od/DW0NVVYtmyZTQ1NdHW1kZnZyddXV10dHTQ2dlJLpcjiiISiQRBENDW1sYzzzzDkCFDyGQyBEFAMpkkjstjptPpNEEQEAQB+++/P3/5y196zgFOJBIkEgna29t7rguCgDlzniGKYg488G09+6xPNpslk0lvp+cBpxg1/Xje9fufc8ucJ8lPm/Zad3BpJQ/f8HMuuWshTV1AKsWovafxyTNO4YBBrxsZ3dtrm+7iwu/czMMAM6/hjCd/C8ARn/oxZ03o3qfpIa6+6mZueXIl7QVI1w7jiA+dyScPyzLjS9dw59QzufYDe3ffs4W5d1zDRTc9ycK27nvufzxfP+N4xtf27hOVJEmSJEmSJEmS3qqSyaFHkKJEGO3PyrYkpfvOpvOVf1K720fWe0FVVS25XEwQBHR2dtLc3ExLSwvZbLans7dYLFIoFIByYJxIJOjq6mLZsmUUCgXq6upIJpPU19dTXV1NqVSiUCgQBAFRFDFmzBiKxSK5XI44jonjmGw2S1NTE4VCgZqaGioqKkin07zyyhLq619k4sTxG/2ira1t22kADCSqSFcAjS20A/0BsrO47Nyfc0tbPQe8+xRO2LUe2uZxy0138c2vPslp37yQs8Z3h7VrrJ128pm8a5cUtM1jxozy2rMu+D6njWadtRvdt2oiJ5w+lfaL72L2blM5Z/o4APoP6d5n2a184dybeYbX9mlfMYsZ117EJ16awqRlLSzNFnq+4jM3nscX/lpg/LGn8J1J3WtvuJmzn1vJpZeeyaTEtnnUkiRJkiRJkiRJ0s4smaBEKYBVTQuJEv0J0g10tjxFLSEEAcQxATEESSorK3vCX4A4jmlpaaG5uZlsNkuxWOw5tzeXy5HJZCiVXhshnc/naWtrI51Ok06nKZVKhGFIGIZ0dXWRy+VIJBKMGjWKZDJJe3s7AF1dXXR2dlIoFEilUtTV1fWcE1xTU8OSJSsZNnQXGuqriEkQUIIwQRyXIEhAHBOVirS1t5FM1PXJg96ouQ9xZxuMmrJ3OfylwOzfX8MtbaM45wff4oTVoStTOWDaNGZc+G2u/N/fcsClZzIpsZG1+4/jki/9kqsv/wPTfnAKo7Zk34phTJ4ykZncxewhEzlgyppnAC/hlstv5hnW3eeIQ5/jygsuZsZaX/A5Zv47CwefyaWnr+5wnsoBtRdz4s9mcufzZzJpUm8/VEmSJEmSJEmSJOmtJ0lYSWHFPbzwj88SFDuI840kSu1ABFFMABAmqExXkMtBIhESRRFRFDFkyBBaW1tpbW0lny+Pi85kMkB5pPPqM4DDMKSmpob+/ftTWVlJGIakUikSiQQLFiwgCALiOKZ///6MHz+eVCrFXnvtxSOPPEJLSwsdHR0UCgUqKytJp9M0NDQAUFVVRX19PbW1tbS1dzJgYD+KhU4IE0SUCAgJ4iIECeIopL2ti4aGvg6AW3hm1kOUn1aBpc/MZMYd82jqP4WvHzGqvKTrIf5xR5aaY9+3RrjaLTGKE47bmyt/MouZ889k0ojy2vRRx6+7tmoqX7p0Ip8spEiXgMIW7LuxhupFM5kxF4aefNp67jmRT35sGv+4cCbtPW/WM3QI8NyTzG6bxgHdI59rDv4ytx+8yQcmSZIkSZIkSZIkaTMliSAXDqVi+PtIVtbROf83xPmVxHGOIEhCUCIRJihE5ZB29Tm9QRBQXV1NGIa0tbURRRH9+vWjf//+NDU1USwWSaVSZDIZ4jimWCyyatUqVq5cSalUIooiAObNm8fxxx/PHnvsQT6f59Zbb2XgwIHst99+7LPPPjz22GPcdNNNxHFMoVCgra2NtrY2UqlUzxjphoYG+vfvTzZboKoyQ7GYJwyTEJe6u5iBIII47NunDcBCbrn+D6Qp0LQsSz41kGknfoIfHj+VoavHILe1sBTgpbu47JqH1t1i5RIgS77ztbXjdxm2/ttV1FNT0f3zpi3Yd2OaV7IUOGH8uPV/PnocBzCTO3veGMYRp03jH9+fyTc/fTajpuzNEW8/nCMOnsjQ1Pq3kCRJkiRJkiRJkrTlkrm2p1n57J/IVA0kzjcS5VqJwlqCICQmIqDcrdvS2kk6nSaOY6Io6glx8/k8XV1dJJNJpk2bRjabZfDgwcyZMweAbDZLZWUlVVVVDB8+nEKhQKFQYNWqVXR0dJBIJKipqSEMQ5qamshmszz22GPsvvvuJJNJOjs7SaVSPYFyZWUlqVSKrq4umpubqa+vJwxD0uk0YRhSKhXLoW9UgjggDoLyCGtiINmnD7tsb75+yWeYTIHZ13yFr90O46esEf5ujoHjOGH6OCb16+XSemvfVKp7zPNraiacyeW/eh9zH7qdGXc9xIyrZnH1lSnGv/8z/PDkval5k7eUJEmSJEmSJEmSBMlVSx+n6dkrKIYhybCCuNRKkBxTHv8cBqRTKTq7ykFvMpnsGf+cy+UoFApkMhmiKGLQoEGsXLmSVCrFypUr2W233chmszQ2NlJVVUVtbS2VlZVUVlaSz+fp6OggiiIaGhrIZrOkUilKpRKNjY3U1dVRU1NDY2Mj7e3tPaOmq6qqGDRoEIMGDSKdTtPV1UVHRwfFYhGAIAhYsbKFwYPriUsQJ4oQhURhREgF+bYXod+APn7kq6WYfMopHHHXNVz9i5s54rz3vRYC19YzFGja+3jOef8GumxX6yqvfXjFSmA9XcBdLbQXUqSrqkhvyb4b028gQ4HZLy+EvUet+/nc55i5vusS9Yw/+BS+fvApUGrhmVt+wtdm/ISrJ/yCc/Z+4+VIkiRJkiRJkiRJKguTDdPov/8lNOxzPgPffinp2onEQQhhCohIpjLkcvmewDeKop5RzEuXLmX8+PJhsclkklwuRyqVorOzPEN48ODB7LHHHgwcOJCGhgYqKioIgoBMJsPYsWM58MADOfXUU3u6hDs6Oli5ciXHHXccXV1dDBo0iCAIGDlyJOPHj2fMmDEMHz6coUOHMnjwYOrqyuf5rg6AV1uyZAUEJWIiCCLCKCQmyap5v9ymD3eTqqZx1umjSM+9lcseaHnt/YqpTJsGS2+7nYezr7umtIQZ5/8H0z99Dc+UXlvbfsddzFxn7Tx+e/5XOPE7t7I0sYX7AjCMoaOBbLb7zOJuI6ZywghY+Ndb17vPLb9f8/xfIDuTS770Fc6+6bnX3kvUM+ngvRkFzH11yaaelCRJkiRJkiRJkqTNEHaW0lQMPIiaIUeSbNiNuBRBWElEgmQipFgsn73b3t5OZ2cnuVyOYrFINptl2bJlxHHMyJEjmTt3Lg0NDXR0dJDNZlm4cCGpVIr+/ftTWVlJW1sb7e3lWDCOY7q6unpe9+vXj5UrV7J06VJGjBhBoVBg5MiRdHR0UFlZyYABAxg9ejSjRo2irq6OyspK0ul0z2joYrFIHMc9ZxTPnTufIJkgjEPiIIYgJiquorj8sb581us19PDTOG0EPPzLa9YIcFNM+9AnOCI1i2+e+22uvn8eS9taWPryTK78729z5dwqTvjsKUxKdK89+X1M4kkuPPdiZswqr10493au/O+LuXpRFSecfjyjtnhfgCr6NwCP3MqV/36Ih2c9xNxVAKM4+ezpjO+axTc/dx5X/vtJ5q5YyDP338ol3zqPKwftzbQ1v2TVFI7Yu8DcGT/nwr8+xNwVLbS/Oourr7qduQzjiCkbOL9YkiRJkiRJkiRJ0hZJrnjgU9SP+yiZwQdQKrZRKHSQqh5JCKTTlXR2lSgWizQ3NwPlMcvFYrHnvN4wDJkwYQJz585l5cqV1NbWUl1dTUdHB42NjQC0trayYsUKEokE/fr1o6uri1Kp3Gba2dnJ8OHDaWtrY8WKFYwePZolS5ZQX1/P008/TbFYpLq6mpqaGlKpFKlUqmcMdTabpb29ncbGRoYNG0Ymk+k5mzjXkSNdkSQEIEVcaKVUzPXJQ96oxDhOPnMqt3z3IS77/Swmf3RK+Tzchql8/Xv1TLr2l1z5s4v4bffy9C7jOOu/PsNpk6pe22PQ8Vx6yUCuvOwarr74Iq7sWbs353z7E5yw6xprt2Rf6jniE59g7mXXcMuVv+QW4ISvT+WcBmD0KVz+vXou+p+bmXHlT5gBkEox6ahPcO2HUvx25pNr7FPF5DO+xXdSP+GiGb9k5g3d96wdxmn/9WVOGNRLz1KSJEmSpP/P3p1HyXnd553/3ner6urq6gW9AN3Y0SR2EiS4AxIsiqZIU3tkZeSRrThWlNiWnbEs53hixTlelNjjY1nSyPHEI9uS40jHHimRFelws0hRIimSIAgQJACC2BtL72tV1/bW+975o7qLAAhi7240+HzO6cNa3nrfW7ch3ffcp3/3ioiIiIiIvM2ZF/7hQduy/CMku95DVDzJySc+Sl3Xg7Tf9B9IZxJM5sqMjY3x+uuv4zgOzc3NFAoFTp48SbFYxPM8UqkUTz31FIVCgZ/5mZ9hcnIS3/c5ceIEqVSKvr4+xsfHWbVqFQsXLiSKotqS0k1NTXR0dNDT08MzzzxDV9vb2CQAACAASURBVFcXo6Oj3HnnnYyMjLB//34aGxtJp9O1qt9MJkOlUuHYsWMMDQ2xZMkS3v3ud5NOpxkdHaWnp4flK5ayYtkisA7WMRCHnPzhh9jw4e3VL25M7ef05+d6XOus0x6f77WrLgrJ5fOQbCTtz9Gx5xPmyRUh3ZC68LGElLN5yn6KdPJKLioiIiIiIiIiIiIiIiJyfZmYmABg165dl/zZTZs2AeC03vqfSHTcA9ZgcCAq4Sdb8XyPSmhrSysXCgVGRkbI5XKUSiUcx6FSqTAxMUFvby833ngjk5OTOI7DsmXLcF2XTCZDNpulv78fAN/3cRwH368Gf6VSiXK5TF9fH9u3b+fIkSPs37+frVu3Mjw8zKuvvkqpVGJycpJCoUC5/MZOtFEU1ZaiHhwcZHR0tHaMtZaB/iFc1wUTgzVAHX7QeoVdPkdcn3TDRYa0M3Xs+fipiwx/AXyChkaFvyIiIiIiIiIiIiIiIiIzwAuS7Uy8/iXKE8ewWIjHCeo78DyXUqlMHMdUKhVc12X58uUsXbqU+vp6HMchiiLy+TzPPfccBw4cYPPmzQwPD9PW1kYqlaKhoYE4jkkkEmcszzy9fHMYhgwMDPDII48wOjpKJpPB932eeeYZtm7dypEjR7DW4jhObX9f13XxfR9rLZ7nkUgkaG5upqGhgWQySaFQwBiDtZYojrGAsRHW8TDpVXPd3yIiIiIiIiIiIiIiIiIiM8bDVHBIEhV7CcMJsOAGLRhTrfCNoghjDK2trbS0tJBKpUgkEjhOdXfdZDLJtm3bWLZsGb7v88QTT2CtBWB8fJxDhw6Ry+VwHIeJiYnaZ6MowlpLX18fYRiyZMkSbrvtNtauXcuJEyeoVCosX76cVCqF53kYY3Ach0QiQX19PZ7n0d7ejud5dHZ2snDhQhzHIQgChoaGSCaTFIsVEkkPcDHWkmq9Yw67WkRERERERERERERERERkZnlReYi6FR+lbvEDTJx8nPF9X8K6DYRhuRbcuq5LY2MjnucRx3FtWehpruvS2dkJwLZt2ygWi0RRxNDQEKOjo/T19TE5OVmtyo0iPM/DdV3iOMb3fe68885aZXBHRwednZ0MDQ2xaNEi6uvra0tG+75fqwB2HOeMCuDpquDTA+pSqUQQeBhTBhIkW2+diz4WEREREREREREREREREZkVXvbId0i13kbQtB5DjOvV4aZamZzMMjk5SV1dHcYY6uvrMcYwNjY2FawGuK5b2wt4OpBtbGwkiqLq/rtAoVAgDEOGhoaIoohisUg6naa+vp5CocDRo0e55ZZbaGxs5ODBg/zoRz/i7rvvJpFIUCqVgGrwm0wmSSQSGGNqjZ8OgsMwrFUU53I5rLU0NDRgjIuDAWuInApuonNOOllEREREREREREREREREZDZ4ucNfpXT0b6m/5fOY0hBespkg2U5usky5XCaKIoIgIAgC4jgGYHJyknw+jzEGYwye5+F5Xu1Yz/PwfR/f91m0aBE9PT1EUUQikcD3fXK5HKVSiWw2y+joKIVCgQceeICenh4ARkdHcRyndpy1Ft/3a9erVCpUKhWKxSJxHDM2Noa1ljAMGRwcJJlMTu1dHIGpttmxFmvsW3aEiIiIiIiIiIiIiIiIiMh85zmmnoaN/45Ew0r6934Zv2E11gSUyzkKhQIAjuPg+35tL17XdWvVv47jTIWt1SrgQqGA7/sEQUAYhrS1tbFy5Up6e3spFov4vk+hUGB8fJx8Pk8URYRhyLe//e1a1W82m6Wjo6NWTWyMoa6ujkQiQRiGlMvlWggcRRFRFAFQLpcBsNYSxzHlcojFARNjYgeI5qqfRURERERERERERERERERmnBeVBvHTi8E4VCYO07D0/RgMmUwDcWyI47i2vPJ02FqpVGrVuNZajDE0NDRw8OBBuru7a0Gs4zhYa7n99ts5evRo7adSqZxx3kKhQLlcJpVKAbB48WJWrFhBsVisnSeKoto+wXEcE4ZhbR9ix3EAalXCURQxMTFBpRLStrARNwZrwKAKYBERERERERERERERERG5fnn4KSYOfwM3uRDHVkg0bwIc0uk0rpuohazAeR/39PSQSqVqVb3TS0KXy2Xq6+vZvHkzYRgyPDzMwMAAYRgCEMdxrYp4cnISay3JZJJKpVJbWtpxHLLZLJVKhXK5TC6Xq31ueu/h6arj0yuCU6k6XOsAcXXv4Hg+BcDj7Hr0edLvuJ/u1EUcXjzEE08Nsem+O2lxZ7xxIiIiIiIiIiIiIiIiInIN8tLL/zmTr/81xk2S6NiC33gj2CK+10SccM75IWNM7XG5XGZiYoJXX32VxYsXUy6Xz9ivN4oi6urq8DyP5cuX09LSwqFDh9i3bx/ZbJY4jmthchRFNDY2kslkOHLkCOl0mrq6Ovr7+xkdHWV8fJzW1lbS6TS+7+M4Dq7r4nkeiUQCz/Nqyz8bY0gEAVjAQESMe+6vM0uGeOTLX4eP/yYPtFzE4cUeXnjiSdI33E/3yos4PnuIJx5+jeCeO9nacKVtPd0ELzy1i/Eld/DTK5OXeY5htj+2gz2F6ec+zR0LuX3tajobZ/mXcuJVHhlq5YFNC2f3ugDl4zzx4+MkN97FPe2X+72P8cQ/9tD8zndwS/OZ7xx8+jF6Ft7Pvd1X3FIRERERERERERERERGZp7zGFR8jyh6gEk7StPpXME4ANsIYW1ta+WzW2toevv39/QCMjIwwOjrKQw89RBiG+L5PIpGoLd9cKpVwXZcFCxbQ1NTE3XffTalU4plnnmHdunVs2bKF4eFhHn74YV566SVWrFhBMpkkjmOKxSInTpygoaGBjo4OmpqaaiH0dIDsOM4ZS1IDpOqTWBNjrINrAeLZ6NO3EFIeHr/4bYiTG/nUH//niz992/384Rfvv6yWnV+GzZuX8cRPXuBxLj8ELpcdbrjrPrZ0AFGJsYM7+d4Pn+Ou+++hu+7qtvi8chP0jFxukH2FgiVs3ZDj+zuf49lbLjcEDsmHExx8/lVWPLCBptPeqZRD8pWr1VgRERERERERERERERGZjzwn2U7zxn+PjSxefRcmjsAY4riCtV4tTK1UKkRRhOu67Nmzh927d9PV1QVUK3eLxWJtaeb+/n7q6+ux1hKGISdPnsTzPFzXZWRkpFa5+/jjj7N48WJuu+02li1bRjKZ5Dd/8zcZGRmhv7+fZDKJ53k0NTWxceNG2tvb6ejooK7ujcTQWlurIJ5+PP2TaUxj4whLBWM8rLl2loDO7f0Oj7CVrcUn+drDz7O32MrW+z7Iz21dQ9oFGGfX956EbR9k03RFb7aHJ578Do/8sIdc1xoeuP+DvH9j69SbPTzx9UN0f+JdLJ2+yODzfOMfn+TpvXla1m3k/R/4IHe0+ZfcVje9jHvv5opD4DdOmKBp9R3ccuJhXjsG3WumXh/vY+e+AxzoL1BOZlix/AbuWr2AN1a0jsme2M/O1/o4PBmRaVrAunUbWHP6dwqHeW3XAfb2TzDh1rGy8wZu2bCQBpdq9e+xHJRDHnlqCBLtbLlrJQ1nXZv6BdywZjW3cPSMauFocD/PvHyKw5PQvGABt2y6iaXpS/vqQcdaHtq07wpD4Awr6k/x2K6lfHRT5pxHZPfvYCc38M7VZ75/atcOJpZvZs10cvyWfR7Ss3MH/a13cPuS09sY07PzBfpbNnP7Mh8IGdr/Ki8eHaYv9FnYsYTbNrUytOMArDntOiIiIiIiIiIiIiIiIjIrPEOMk2rHwQdbqVbMxg5RHDE+PkmhUCAMQ6y15HI5Dh06xMsvv8zmzZtr+++Oj48TxzF9fX0MDAywb98+brvtNqy1uK7L8PAwcRwTRVHtvz09PZw8eZJkMsn4+DhhGJJMJhkcHGTx4sV0dXWRyWRwXZdisUipVMJxHIIgeMvKZDhzb2Lf8wlLETgOxGCY0zWgz5A7/hLf+N5L9Hzsk3zmt3+WIOzhib/5Uz559JP87Sc2EpCnZ/tLcOdUADz4GP/uPz7PHZ/5Zf7wodbqks/f/CN+4eVP8NWPbyRgiL2PvUbLVACce+Uv+JVv+nz607/MRz7uUx55ha/92f/JCx/7fT698WI2FT7TVQ+Bz9a/m3/4yTCta9fy3s3teNk+Xn5pB98c3cDH7+oEYGjHU/zjQIZ7b7uTLS0u2RMHeOrZJzm6bhsP3JCAwmEeeewglaUbuPe+hTREAxzY+SrffnKcD963mqaOlWwZHqVnqJUtN3eBm6iGv727+Yfn+sjceBMPbszghhMc2bWdR2KHHqa+5+irfPuZYW64/U5+vh2G9u7g4Sd3cP/7NtN5iV/1ykNglyX3rIbv7WD7sndxe/ObjyhNjHKS8E2vT4yMMrxo6skF+nyhH/LI/gPcvGQ1wfQJCgfYeQzW3+QDeV774dM8V2pj26138c4U5PsO8KMn+wjKeZpLl/i1RERERERERERERERE5Io5TFfF2um1iV0wMWE5YnR0mJGREXK5HKOjo7z00ks899xzjI+PMz4+TlNTE8VikcnJSay1RFHEM888Q0tLC6lUCsdxap/N5/OUSiWy2SynTp1ibGyMIAjwPI/Dhw9z7NgxKpUKR44c4cSJEwwMDDA+Pk6pVCKRSNDU1ERzczOpVArXdXEcp1ZJfPqP53l4nofv+8RxdclniwMmhmsoAAbgzp/lM/csJXCB5FLu/dQn2Prk0+x60zLRIU9/6zu0fOI3+Uh3K4ELQdMqHvjUJ3lg/9O8kD/r8OLzfOWLIZ/63Ce5o7ORIJki3Xknn/7cB8l98as8Xby85lZD4GVw6AUeP3yZJ5kSnXyZ3dlmupcBlNj9yklSa+7h3tULSfkOQUsnt79rA0sH97N9FCjs55mjDre/YzMr2lK4boKmZRv4wIZmhnqOkgdOvXyQ0c7NvPeWTpqSDm79QtZsvYdb48NsPxiDn6Ih4YLxaWjK0NCQqF57z3ES3Vt5YP1CGupTpJoWsv6n7qSzknujwUMTjDV0cUtXCtdP0XHzzdzVBhNjl/f9qyFwM/07n+PZgctYmtxdxk9vqGPP869yeU24cJ8Ha5eyInuKl0+7QPlgH0PNi+h2gRP7eDG7gPvvu4UVbWlS9WlaV93Ch9cmGLuyfx4iIiIiIiIiIiIiIiJymTxigzEWLIDFGjDGJYxCmpoy9PcPks1mOXz4MDt27KBQKHDfffdRKBT4whe+wD333EMQBERRRBAE3HvvvSQSCTzPo1wuk81mGRsbo6Wlhfb2dhzHYe3atRSLRX7wgx+QSCQ4fvw4mUyG1atXk8lkOHz4MF1dXYRhSEtLC/l8HmMMmUyGTCZTC47jOK4tUQ1nVv96vqESFqvfLbZYx2DsxW7AOzuWtrWe+YK7iKXLeukbAdpOf+M1dj2/kXs/dVblrruGn/uDNbzJ4Vd4+tZb+czZhb6pO9l66//H3hOwtfvy2uym2uhIH+PAYD+5lcu4+BWQS7z2k8c4YCCqhCTS7dzyzrtYUwdwklNjabpuLZEdO71sNE1DKs/JfsAdpr+piw+cfcFVt/PxVQDD7B6GjjU+2bGJMw5paPDZMzoKLDhHu6rXXrHlTZ3FmkVpnhucerqyi+69e/nmU0Xu6l7J0q4Ma+7afNHf/lyCllbagn6G+keJ2k9f6voirbqDbaf+iadeWckHLrmq+yL6vHkZ6xft5+lDE9y+OQNM8PKJEjdsXAbAWN8oldZ1dJ7d8CWLWPrS6KV+GxEREREREREREREREbkKPEw1RLXEGMDUQlRLQzrBwMAABw4cYP/+/YyNjXH33Xdz/Phxtm/fzoYNG8jn81hrmZycpFQqUS6XyefztLe3k8/nKZfLrFq1is2bN9PU1MTQ0BDj4+P09/eTSCSA6v7Cvb297N27l1QqRRAEZLNZ1q1bx/79+2tLTC9atKi2NLQxhqamptpy0NbaM8Jg14OoDBgXQ0yMUw2D56UQQqqVwhcjCimnUm8s21vjk07lKRcusxlxnj0vvMQBZxk/ffulhL8ACdbcfR9bOvLsfuxJjixaz/qW6YrsmAoFjryyl/6zP+YtoCsN5ABz9ptnimxE/9G9PHPi7HfSdLW81d7HMRV8gro3vxP4p33GXca9711I/9ED7N/3PE9sD2ldvokPbGo/f6PeSjjIs8/sY2TBeh7YeBnhLwAOK27rZv9j29m9Yts5ft/ncxF9DnSuXghPH+DU5s10njjAfhby3sXV9yoWEl7iHOdO4F7eFxIREREREREREREREZEr5FneCH3fiH7feB7HEYcOHWJkZISbbrqJJUuWsGfPHtatW0d9fT3lcplUKlULe6MoorW1Wtna1dVFZ2cnQRDQ3NxcWya6XC6TSCRobGykXC5TKpWYnJykpaWFwcFBOjs7GR8fp7e3l/7+frLZLNZa8vk8cRzjeR4tLS2EYVirKjbG1CqAjTHEYQEwYAwYj7iSx3Evfe/ba8NSlq7+BntPwKbFp7+ep2fvIYKVG1l4+pa8y9dwx98coodbWXr64dEhdr2yinUfuYwmTIW/r5kl/PTty8hc9mraKW66eQl7ntvFwe576K4DaKa1ziFacxdbOk4/tkT/geNEDUAiTcP+cU7BmXvu5vp4rddh5Q3tdKRjRhfewQNrzmxc9th+hpLnSHiZvvZhTh2NWbP89M/F9AzngKmAt5gjT4qOVRvoWAXvDPv40aO7ea7rPu5qO9d5z2Mq/B1qWcsDN7VdYnB7lrqV3Luuj288u4+bvTdeziR98mM5zqx6HmC4tlz4RfQ5QPNq1tQ9xcHjwMlRGpa8g6apt1qbUmSPDJBnAWf8Lys3QH8RzjitiIiIiIiIiIiIiIiIzAonmUxiqYamp1fQQrUy96aNaxgdHWXdunV0d3djjKGrq4tUqhr5GGOIogjHcSiXy5TL5VpY297eTltbG42NjcRxXNsr+ODBg2zZsgUA13WJoohsNkscx9TX13Ps2DHy+Ty9vb0MDw/XqoZHRkbo6+tj79699Pb2cvToUfr6+qhUKrW2ALieQyUCMFhcJnr+J8M7/yOV4tBs9etV1sq9H1rFI3/9HXpOW8U69/LX+dy3eiB51uENW/nIzc/zhYd73ngtGufgd7/BEzc/xNYGLs1VC3+ndGxgW3uOp188NvXCAm5aleLAizs5FdYuSnbfDh7enyVoBNq6uaVhmKee76PWBVGOPS/sZue4SwB0r+4ku/859oy/sadudHIn39s5TCkzVc3bWEeqlD9t39wF3LQqTc+enfTkTrv2kZ3sPn0V46O7+bsfvsrY9MWd+I2/mLgUVzP8nRLcsJmtwXG2j5z22uIFNAz3nNYXMdl9hzlS69+L6HMAEty0PEPPoe3sHkyzfu1pFb8ru1kfHufxV07rqHCYnc/3kX+rgmsRERERERERERERERGZUV5dXZJisVh9dloFbfWpZenSJdxwQzeZTIZisVirsh0eHmbhwoVUKhWKxSJhGFIulxkaGiKdrq4fGwRBrTK3VCoRxzEHDhxg27ZtAERRhDEGz/Nq50yn01hr8TyPbDbLxMQEuVyOxsZG8vk8fX19OI7D5OQk2WyWSqVCc3PzGRXAxYEf4qRX4CW7KAz8hPE9XyLRtB7jzt9UKr3xl/li/HU+92v/llzShzgkWHk/v/vZh1j4pqN9Nn38t/m5b36Z/+1X8gRJoBjScs/P8cWPb7zE0DHPnudf4jXnKoW/ADh0bupm4aMHeKZ3CVsWOaTW3MUH7Qs8/P2HKRkXbETkt7Ltp26hWk+eYs2224me3sHXvrMb1wAWMl1r+cBtU1Wui27hAzfv5PEfPcr22AUiItLcdOeWqb2GgUVLWb9nB9/+Th9uqpMP3r+BpulrP/4wTzjVayeab+DBVSX+oXfqc2s28UB2O9/7Xw9TMW712ss38eFLqf4tD/Lss1c3/K1K0H1nNwcf3Udl+qXmdTy4fgffe+JRtjvV9ZhTC1azvvEA00XAF+7zKd2L6HjlVfoXbeaB05d2dtvZsm0tzzy7nb86RPV34iS4YWM3K17df9W+nYiIiIiIiIiIiIiIiFw8E8c529fXj43jWiUwdioAxmKIGRgY5a/++r/R3NxMIpGgVCpx5MgRgiCgpaUF13U5efIkJ06c4H3vex9r164lCALiOK4Fs8VikWw2y/DwMFu2bOHIkSN885vfJAxDXNclCAIaGxsxxjAyMkJnZyeFQoHjx4/j+z4tLS04jkMqlSKRSFBfX8+NN95IU1MTq1evrrbXWtzCywy9+O9p3fwHJNvuYuAnn6Iy2UPr7V/Gb+imuaW6MO10xfN02H2+x7XOMm/ehPZcr824Yh6SF7+cdbkYEiQvP/wuF/LEiRTJqxL+XoRiiSiZOM++uCH5SUjVn+c7hSXKTuLi902eEk3moT513j15o2IJN3muvW8vJKY4WcapT17F8PfC1yxn85BKn78vLtjnFxCVyIc+qVn7RyIiIiIiIiIiIiIiInL9mZiYAGDXrl2X/NlNmzYB4ADVqlumw0yDpbq6rcHB4rCoczErViyvBboTExMUCgVSqRRBEFAsFomiCM/zGB8fJ5/Pk8vlKBQKFItFisUilUoFay1bt24lCAImJyfxPI84jomiiCiKKJVKVCoVSqUS+XyeQqHA2NgYruvW9g+O45ixsTFKpRLHjh0jkUgQx3H1/HHEyCu/T9DYjZdZR37idQqDO0gt/jBe4xqMc9nx1rXlEsJf4IrCX4CgbhbDX4ALBpH++cNfAP/Sw18A9wLhL3CZ4S+AQ3JWw9/qNYOGC4S/cBF9fgFuQuGviIiIiIiIiIiIiIjINcABqE/VTVX+2tq+ptUw2GJIUMr18N4tSXwPxsbGyGazlEolwjAkjuNaEu26LqOjowwMDDA4OMj4+DiFQoFSqQTAunXr8H0fx3EYGxujWCzi+35tqehSqcTRo0drewZPTEzgeR6O4xCGIUEQkM/nKRaLuK5LfX091lqCICCZTJJy+ihnjxO03YMXNBMV+rFhHq++Eyd2wMzfJaBFRERERERERERERERERC7EATCOU92315jp/BespRJVyB77B4Z2/BZj+77Mu294gXy+uvduPp/nxIkThGGItRbf9/F9n2w2y+DgICMjI7WQN4oilixZgud5BEG1/rG3t7q5aqVSIQxDfN/HdV3K5TJxHJPNZhkYGCCbzTI0NEQ+nyeOY4aGhigWiwwMDNDb20tra+tUGFxHZDxcJ4CohDUOidQinESawuBPsMSk03Xn6gMRERERERERERERERERkeuCYzEYA5l0GtdxMMbDGI/i2B7Gdv4O4/v/bzABmRs/RefN/5YHti4hn8/T0NBAY2NjbWnnRCJBKpWiq6urtiRzqVSiWCzS3t5OMpnE9308z6O/v5+JiYnass5RFDE5OVnb33dycpJyuUwulyObzVIsFimXy/i+z9DQEFEUUV9fT2dnJ5VKhSBwKIc5vGQXyZZbKA8+iyHET60gtfS9FE49Sjj2LJmGxrnubxERERERERERERERERGRGeMYTHXZZ8envrGVOJxg4uDfMfLS5yhl99G47GMs2Pg5UovfC3GRxd4/cf+Go5TL5dp+v3V1dVQqFSqVCt3d3QAUi0XGxsZoamqqVekaYwjDsLaMs+NU9wyN45hyucyRI0dYvHgxk5OTtcrg6eri6crhZDKJ4zi1paUbGlJEUQETO+AkSHU9QHF8L8XsfqybILP8f8e4Sczgd4kxc9XPIiIiIiIiIiIiIiIiIiIzzsEkgASV4jB26FHye36H3JH/RtC0nrZNf0T9yp8DY5k4+FVGdv0eYf4E7/nAz/Nv/tUv4vs++XyeMAwplUq1it4tW7YwOjrKwoULa4FwFEUkk0l6e3uJoohcLndGCOw4DiMjIwwPD9PU1EQcx8RxjOd5WGspFAqMjo4SRRG+79Pc3Mwdt99KY2MKYgMGDBYvvQIbZokmjuBYQ5BeTLqhDa/cgyGew64WEREREREREREREREREZlZ3thr/4Uof4Jw4jXC/Aky6W4Siz6LSa/HRhPkDv8dk/1PEpfGSLTcQnrpe/Fabue2JT5NLc18/j/9CS0tLbWlnIMgIJ1O8+53v5vm5mbiuBq6GmOoVCoUCgUqlUptr9/p4BjAWsv4+Hht3+ByuVwLgB3HIZ/P4/s+yWSS9z50PytXriC2EcYYrDFEpUEmDvy/4Pi4yQVgLKX+p0lVjpFo+0UFwCIiIiIiIiIiIiIiIiJyXfPG9/0JQd1S/Ob1pJZ+mER6GflcP6cOfI3y8IvEYYmgeTX1K/8lfvNNOOl2TOyCienuXs5//a9/xpe//Jf09Q1QLpcpl8uUSqVaFS9ApVIhk8kwODhIJpOht7e3tnfwdEVvX18fyWQSay2lUokwDKlUKnieh+M4eJ7H8PAwqVQdn/mNX6W1rRlrKxjrYbGAgaiM8RI0rvk0fuMGsJak00/ccQeZ7l8C68xtb4uIiIiIiIiIiIiIiIiIzCCPKI9bv5T0DZ+k1P8EY0e/RaV4irooAU03k2i/m6BhDX6qAxwfiMFEQHXlZd9P8Fu/9eu8+so+vvLnf1mr7rXWYkx1z90oinAch2w2y6JFi+jt7a0t73z6Ma7r1oLh6XN4nkcymSSVSvGB9z/Igw8+UG2DtRgCIMQaixMbnGQTTTf+KibZjuMmaG6qx2/8EPHyB/EyyyEO56aXRURERERERERERERERERmgZfqfJDi0A4m9n2BcHQXXuNNNCz+RdqabqJg2siXPMADE1d/LIDBmjeWU7YxrF+3hv/y53/Kqb4BTp4YwHEcUqlULejN5/MEQYDjOPT29taWfnYch1KpRF1dHY7jEEXVcHl6ieggCPjIRz7Ee95zL77vgYmwscUYl+rGv0mqoTQ4JoHTsBowNDalqUulwGSwuEAFHIBolrtYRERERERERERERERERGR2eKnl/4z88f9FeaBI4/pfJ9lxP35qEcZNEhgHbzLH/T4vUgAAIABJREFUxMQIWAvWBWOnQmBw4ukg2APAGEtn5yKWdC0nDIsMDg0wOVmktbWV0dFRGhoaKJVKDA4OEscxYRhSX1/P4OAgqVSKQqGAtRZrLTffvIGPfvTD3LLpZlzXmQpxYyDGkIA4ohL1EmVPYXBwsEQGDJbGTAY3n6Kcj4hsiTjMY7DEcRlnwc/MVV+LiIiIiIiIiIiIiIiIiMwoL7XgLtz0MmwUkl72UUyiHRNXsCbC2Jh0fRLXaWZ0ZHQq/K2mv8Y6gMVYAybGGjAmgaVEYfB5SgNP40zsoaE8AiMd1DkdJJtvYqTYiO8UcYytVvRiWb9+HZtu3siNa26ke+Uy2trbcB2DxcEAFoshAmOmrm/BxEzs/yqlwRdwnSQYQ0xEY9qjmPAoYKt7/jpgohhrLLFxaNmqAFhERERERERERERERERErk+eE7RSt/BdTBz8OuVcD4mgA6bC1mrwCsm6BK1tbYyNjVKpVJdQtliMAayDxYIJqGSPkTv+9xR7f4w1Bi+9CqdlKU6ik/pEC06YoyF8kd/++Qzh+CBxXCC56J00LPnneKklxCbEsRZLBNZgjFNtC9PBrwFccCxERUoDT+Ml2qlb/BCGCi1N9fieh2Ni4hiM6+N4merSz06AnQqvRURERERERERERERERESuRx7GJdXxbiZe+38oDP6IRMsd2KlKX2MdrAGwBIkErW2tTIxnyecnq69bg3UiHJKUhnYw/vqXqOR6SLRvw7oegd9MatlHcL0UOCniUh/lngnSi99PlO9hsv+HlE4+TjjyGo3rfp2gaSNQqVYXO0yFvlMhsDFUF3iOMLhUwklsmMPpvJ/W1R+joaEezzFgq+E0ONV9gk2CKB6B0jhusolCec76WkRERERERERERERERERkRnlQpm7BJryGpRT7n8bc+BlwXOK4gsHBEuNANRA2Dk1NGYKET3YiRxRVMCQoj+xk7JXfw1pLZvWvkmzbSmHgh1QKJ/DqOojDLHFlCC9oJtX5Ttz6xXjpTtyGGyi1bGL89b9ibPfnab7pPxA0bcBSxliITYRjXYyB2Bocy1RlskMc5jDEtDQ307xgCSYugQPWGgwxGJ/K6D4m+x6jNLqbqDxMoq6Nuo1fmeMuFxERERERERERERERERGZGQ42xCTbqVt0H5WRV4jKvVhcHAcwMQYLxgELVKNV6urq6Ghvp6FhAVFphPF9XySOijRt+Cx1i9+HV9dKqm0b6a4PAi5R7giFwWexXoogtRJsCeIIr64DN9lBZtXHics5xvb+X1QKAxiCag2vdab2HXZxzFR7ppaGzmQaWJCB+iAx1bQK2BhDBXDJn/o+gzt+g9zRv4eogFu3hLgSzlU/i4iIiIiIiIiIiIiIiIjMOMcagzU+QfNmotIwYf44xvrV3X+trQbA1RpgwGIwGKovpTON1OcfxS+8StPqf0VUGJk61FKc2Ief6sQS4da1Ute4BoCJg18jf/y7YHwAosIpEs3raVjzq5RGd5M78t/BTF+X6jLQtnpOx7U0NDTQ0d5K84KVuEEDUb5nquJ3ei9iA6ZE7sDfUB57hYaVH6Nxw2/RvOaXSSy4Yzb7VkRERERERERERERERERkVnnGJoijMcqDz+AGTTiJFjAVjDVUk15DLYvF4lQ3BcYaj7h4gsmjf0frynfTcuu/YOLII5BIUChHxKUBIiq4RJigGSfRBjGYRAPGrwfjAZZE8604iQZSdUspDbyH/NFvU7fkPSTSa4EyFkMymSRVn6AumQRrqlXBToZE650Uen9AZfIAfv2qN6qATQK/YSXFgR9T7P8R5clT2FIfldHXaVr6S3PU1SIiIiIiIiIiIiIiIiIiM8sJc4cYffn3KPT+gPSNv4RftxSoYKv1tFTTXwPGYMzUcwOGgNLQDirZo6SW/yyu30LTsvtpaW1n0aKFLL7xfdRnmgmCJPlTT1ApDYKJqet8iETbVvIDTzJ59Bu49Ytw3DTGS5Pp/hdAibD3EerTGVpaWlm0aCELFrSQrJsKfzEwVfGbWfUL2KjM2Ct/RFQ4iSEA64CNqL/xX9O87jfA8amM76cS50l1vmuu+llEREREREREREREREREZMZ5wy/+H4TZg2RW/xvqV/4CBoeYGKda/lsNXR1bqwKeSoEBCLNHMG6CIL0S4jzGq8PGRYx1qG9ZRspxMSRIFhcTtC4jMo0QtxNXLNnCdqLSEJnGNJ6FqDJMXCli2hpg9H/ijt9OYsk/wxgDcQVjHDAR4E0tRB0StN5JZt2vMbH3S4xUimTW/hqJlruJTUSQXoHf/UmS+T7iSgG8JH6ynZJ9Ux+IiIiIiIiIiIiIiIiIiFwXPAs0rf8sqcUfwk00QRxS3U23FgGDjavBr2VqCeaph7YClTxjr/4xJkhjYgNEYDycqeWkfb+ZSmUCKmMYk6BawVumrtJLnDCEh/+CYrEPWzhBVBjCrVsIuIzt/s9EYZHMqp/HOE61DdXGVM8RxxjHJ73iE4BD7sDXGd7xW6SWfJhU5/0EmVXEiTaCoKn6MQMWF/Ll2etdEREREREREREREREREZFZ5LXc/AcEmW5wA6ytYBymllqOARdr4tP2A66yxmCoEBVO4fj1uF4DcVzB4mBtGWPLFLMDMB7iGgdrY2xUqp6SCtY1eDYgNg6xjXGDDE6yg/r2n8JrvhXHDRh/7UuM7/ljEo03kGzdgqWIYXoJaAvGYrC4QSPpVZ/AT68gd+xbZA//LcW+J0i0bCK1/KO4mfVYIgAMzmz3r4iIiIiIiIiIiIiIiIjIrPESzTdNFfWWsbFTDXzxMURUE1sLxp3aEbgavIIDlRylwWcJOrbRsPazRGEfjuNjnHrAElXyRKV+ymO78fwMfmolcSVXDYiDDC4pYiyOW4fjN0DQiLFlCuN7SAStZLo/ycCPP0rh5KMkW9+BsQZrLIYYg0tsIhxbDXYdP0Vd1/34DaspDj3DxGt/zsTwDvzWW0mk12BtliicgHIEftfc9baIiIiIiIiIiIiIiIiIyAzysJVqqGsdjLFYOxXwEgIuU4s9Y+10EXBcXR66UiSc7CWZuYHCqe9THNuF4yRIL3k/yYX34eRPMnn8W5T7t2MSdaSX/wINyz4E1lApn2Ly5FPEpX4SzZsIGjYSRyXGXvkT8gPP4iVaSTSugzjCGrfW2GoFbwxMVSVbB2ss2BhjHLxMN/WpVsb2/hlxeZTJI9+i1Pdj4jCHjQvElTyZ2/77rHeyiIiIiIiIiIiIiIiIiMhs8KoVvRaLwWCphr4VakstA1iLqS0BXV1G2Xop/Ka1lHt/TGVsH256OZWwn+FX/pDUqR9QLhylPPQiyY77iMr9jL3yh4STh/CCFgr9PyQcfx2wFE78I17jGgiLlMZ2Utf2UxT7Hmcye5hgwR3Udz0AVKaqf6tNqi5L7QIxxliIHHBttWrZ8Um2byFuXI/j+MSloWq+7dbhpFtmsWtFRERERERERERERERERGaXZ6cWd3YsYJzq/r9mKgg2cXXfXWvf+IQ1GBODV09iwa1M9P2IpjW/SmrhuyEuM3niuxSHdxCbCo03/Gvqln6UuJIlu+9PmTz2PzA2xiQXkF7xMbz6pYQT+wknXseYiKaNv4ObbKdw4n9Qt+TDpFf9S4Lm9VgbggE7tfxzNQC2U6+BcexUGy3G+DSu+Q1sXMKx1a9jMTgmwPoJRrIFrJ2udJ76StYCBjOVchtz5uNppz8+32siIiIiIiIiIiIiIiIiIhdjOps0xuC5Fz7+Qjxjp8NOU42CnWiq8NeZClWd6bWfqe4BbDDWYo1T3bsXiyXG9dO4qXa8+mUkc6+D8Uhm1uAkFhDj0LzhcxTHd2NLY3hN6wky63GCNHFplErhJI4tU4kKZA98nSjMUbdwG3Wtt4MtVat/bUy1Opnq8s8mAjywEdNJsDXV9gWZlVhjqjk21eWtMRZrLM5kfmrZ6No3OqNjz/X49M4/1y9ERERERERERERERERERORyvZFPxld+Lnt6KezbwMDgWO3x2eGuAmARERERERERERERERERmW3TmaONSwDs2rXrks+xadMmALyr16z54exg961CXwXAIiIiIiIiIiIiIiIiIjJbjDFEV14A/PYMgKeLns8X+ioAFhEREREREREREREREZH55m0XAMOZGykrABYRERERERERERERERGR64Uz1w0QEREREREREREREREREZGrQwGwiIiIiIiIiIiIiIiIiMh1QgGwiIiIiIiIiIiIiIiIiMh1QgGwiIiIiIiIiIiIiIiIiMh1QgGwiIiIiIiIiIiIiIiIiMh1QgGwiIiIiIiIiIiIiIiIiMh1QgGwiIiIiIiIiIiIiIiIiMh1QgGwiIiIiIiIiIiIiIiIiMh1QgGwiIiIiIiIiIiIiIiIiMh1QgGwiIiIiIiIiIiIiIiIiMh1QgGwiIiIiIiIiIiIiIiIiMh1wpvrBsjls9bOdRNERGSWGGPmugnXNI2JIiIiIiIiIiIib1+aPz2TAuBrjCawRUTkXC5lfLhebnY0JoqIiIiIiIiIiMjFeDvOn56PAuA5pIltERGZCecaX671mxqNiSIiIiIiIiIiIjIb5uP86aVSADzLNMEtIiJz4fTx51q5mdGYKCIiIiIiIiIiIteCa3H+9EooAJ4lmuQWEZFrxfSYNFc3MhoTRURERERERERE5Fo11/OnV4MC4BmkCW4REbmWzeZftWlMFBERERERERERkflkPlcFKwCeIVd7olsT5yIicraredNhrZ2xmxiNiSIiIiIiIiIiIjLb5sv86UxQADwDLndiWhPaIiJyKc43blzOzchM3MRoTBQREREREREREZG5MB/mT2eKAuCr6FInqzW5LSIiM+XsMeZib0yu1v4WGhNFRERERERERETkWjXX86czTQHwVXIpE9ezMcmtiXQRkflnJm8aLvXG5Er+mk1jooiIiIiIiIiIiFxt18v86WyYsQB4cHScZBAQ25jhsQlWdC3kyMk+GtP1ZNIp9h3uYe3KpUzk8oznJmvvL2jK4BiHYrlMW3PjTDXvqrrYieVrbUJcRESuLVf6//0Xc8NxKTcyl3MTozFRREREREREREREZsL1MH86W2YsAD7ZP8ShE6cIwwq5fIHO9gX0DY/iuS4NqTqGxiY4dPwU2XyBShTx2pEeTg0Mk07V4fseqxZ3zosA+GL+sV2tY0RERM7nUpYtudgbmUu5idGYKCIiIiIiIiIiItequZ4/nU0zugT08NhE7fGJ/iEAKpWIYqkMVKuEz35/YjIPwKrFnTPZtFlzvknsmaiSEhGRt5eLuUk533GzeYOiMVFERERERERERERm03yaP72atAfwFbjQL/3sSWrHMQSBi+c6M900ERGRc6pEMeVyRBy/MUZdzHh2MX/pdinva0wUERERERERERGRa81MzZ/OtlkJgAPfu+i1ssthZRZadOUuZ6I7VefPZJNEREQuyHMdvDqHfCG8ajcxGhNFRERERERERETkejAT86dzYcYDYM912XrrRrqXXHhJ54PHT/GjF3dTiaKZbtYVudSJboDAd2eqOSIiIpcs8F2KpTP/6OpybmI0JoqIiIiIiIiIiMj15mrNn86VGQ+AY2s53jfAeDZ3wWMnJvPE83xvv3NNdFtr8TwtcSkiItcOz3OwxXMHulfrJkVjooiIiIiIiIiIiMxHszF/OpNmPgCOYw4cOznTl7kmvNVEt4iIyLXqrap6r/QmRmOiiIiIiIiIiIiIzHczNX8602Y8AHZdhzs2rGFR24ILHts7OMwLr75GFMUz3azLdimT15roFhGR+eBSblhOP1ZjooiIiIiIiIiIiFzvLnf+dC7NeABsMNQlE7Q2ZS547Fg2h2HuO+VynD2xrYluERGZT86+MbmSGxWNiSIiIiIiIiIiInI9uZrzp7NhxgPgShTxxPM7eeL5nTN9qRl3uRPYmvgWEZFr0eXepFzJuKYxUUREREREREREROaDK5k/netweMYDYIDA9y7qi1prKYeVWWjR1XW+SidNdIuIyLXs7CWdr/Sv2DQmioiIiIiIiIiIyPXias+fzpYZD4A912XrrRvpXtJ5wWMPHj/Fj17cTSWKZrpZIiIiIiIiIiIiIiIiIiLXnRkPgGNrOd43wHg2d8FjJybzxNdoddBbpfiqdBIRkfnuUv+KbXp8e6vXz/VcY6KIiIiIiIiIiIjMR5dTBTzX1cEzHwDHMQeOnZzpy1zTNOktIiLXorm4CdGYKCIiIiIiIiIiIvPBXIe4V8KZ6wZcrzTBLSIi88lMjlsaE0VERERERERERGQ+m29znDMaAPueh+9Vi4xTyQRQ3RM4EfgAJAIfz3XPeP/0z8w35/rlz7d/ECIi8vYyU2OXxkQRERERERERERGZ7+brPOeMJa1NmXoefMcd5AtFevoGuOfmdby453UaG9Is7mhl+6v7uX3Dak70DzGezXHb+ht59uW9LF3YTqouSbFUmqmmXTXzufRbRETkYl3MeDcfbnpERERERERERERErrZrMS809m02Yzs0PAGAMab2c/rzcz2e7qKzf3nn2uj59P+e/rgxUzcTX0dEROSyjU8UgDPHt9PHv9NNPz/7tuGtXteYKCIiIiIiIiIiIvPZ5cyfnu1yguFKmAdg165dl/zZTZs2AdoD+KqZryXgIiIiV3sM05goIiIiIiIiIiIi14v5ON+pAHgWXOv/CERE5O1pLsYnjYkiIiIiIiIiIiIyH8znuUwFwCIiIiIiIiIiIiIiIiIi1wkFwCIiIiIiIiIiIiIiIiIi1wkFwFfZfC4HFxERuZrjmMZEERERERERERERuZ7MlzlPBcCXab78gkVERK6G8417GhNFRERERERERETk7examyNVACwiIiIiIiIiIiIiIiIicp1QADxDppP+ay3xf5NoiF2PfoM/+vxn+dr+uW7MXMhz8Kmv8oXvvkLuPEflDn+f3//y9+mbtXZNC+nb+zwv7O2lfLVOOXaIF156noNjV+uEIjKfzcZ4NW/GRBERERGRi5U/xCN/9xd8Y3/+rY+Jxjn46J/y+//UO3vtEhEREZGrar7ObXpz3YBr3bX8C+15+qt899Aq3v+Jd7H0ss7Qy3c//7t8Zb/P0ls3ckf9VW7gvPAaj/zl8zxCL91bN/L+lrPezr7Gd//mq/zlS3DHx3+Zs9+eeUO88Pdf5St8kL/9vYdYeDVOefwxPvenL/H+376TTzddjRPOByF9e1+ih6VsWreIYK6bIzJPnWtMvJbHSRERERE5hxNP8pUfHDrzteal3LFuI5tWLCJw56ZZ886hx/jCwy/B4aU88LsPvWm+IHf4+3zhy9/hBf5/9u4+rKo63///ExHULQgiCqKCCo43qIF3kJqop1H72mU1lZa/Od584/LXOE1NzYzl+c7Ulc05mmdmmjpmff3Spfadn03mVHrlSa1RcdAwb6AU0wm8QVNIxBsIgS34+2PvDWvfAHvD3mzA1+O6vIS11/6s9/qsz1qbtd778/kMY8mvI/0SooiIiIj43+3btwkICGj17SoB3I6VfnuQbZ+bmdzcBPCFLLacgqQlK1mdFubt8NqJMcz/Xw8SW5ninPzlGGt+8RYn7l1Ixv9JITrIH/GJd/ggkS4iIiIiItIeXT3Jtl1HCQ4LI6KrdVntUXa8/wH0TuH3/5FOksmvEbYPIx5l9YJYqsfMcP6y+IkMHvtjIXN+voIP71JSXURERERanxLAd7KrJRQBEyLu1OSvRcSI2cxx+Uo8i/5rLSGhrRyQiIiIiIiIiI/N+tkfeGqUYcHFj/nlv23nt2vieXfZND+MgNXOBEaSNHO269cGPspf14URosSviIiIiPiJEsAdSekeVvx+OzzwK5aE7mLVuixOlAEEETtxNi8smE2CNZl54v1fsyrTMk/Njrd+zZddYfr/+wcWDbWWVXaSHe9/wIYvCimtBIKCiB01mSULHmVC7yCX23wqfBcr3sriRMrP2LV4jGUbB8fw3MtjKHw3g3UHrlMNBMfE88hP01l0VyRUFLBtfQYbDpVQbobg0L5MX5DOcxNd9GkuPciG9R+z7Zhh3ccXsiStgi3PbWR3ykLenWe4ey07ybZ3N9aVTVcTSXfPZsm8GXX1ACXseGMVm5jNn5823uCaKTr6MWvezyL3gtkSd1gs0+c9ypLJwww3cfXvX/24iW1vbWTbKev6MaNY8rOFzBnsRoK9ooAdmzbV1XdwaCRJ9z7Icw811LfbTNHRTfwp4yC5180AhPSPZ868dBaNad7QUqVfbeJPm+r3N2ToGJb8dCGzzB+z4H8ftW8fTm9uuB00J97y09tZ87+3k9Vo3btb7jE2PLeRbaUA21n23B4AEh54gRfTrOs41D9dw5hw36M891A8X77pqn2IiIiIiIh0MDEPsmjadpbtOsaJymlMrvDSfV7d/eILvDiqgA3rP2DL0euGe735LEmLJ8QhHPfvUQ335T81sW3NRradGsXv/7+fkeRxWVbuPE8w7leaYX9rSvjyvzNY93kBhdZnMhEjRrHo8fnMMj4f8OAZjoiIiIiIp5QA9oI2M/9hTQWlxdcp3LKK9Iow5ixIZ34olJ7exYaPPmbpyUus/s90krpCdOqjPBW8h99uKWDEjx/lkTiIiLKWU7yHFb/fRFZZGBPuf5Q5g8Mov3yULZv38NvfHGXOr1bw1CiT3TZLMzNYevoSIaPGMGeI5canuuI6RcUHWfVve4gY9SDP/SqSkLICtn20h02rX6F8WTrR77/FtvDJLHk6nojKErI+3c6ON1+hsGIFf763b/2+FW/nl89/zAkcYnp3FemnxzCi+DpFFWbn9QP78sjidJJCsaz/3gcs/eokL77yNJPDAcxUX7lOERVU1725ghPvvcKyT0pg8BiW/HwM0V2vc+LzXWxZ90d2f/EgGb+ZTXSg4f3FH/PL35iJuPdBXrw/DMoK2LJlD2t+9yKF/+sPPDWikfGjK46y5vm32FYaRMK0B1kyxlZPGSw4MYbpZsDu7RXkrl/Oss8riL5rGs/dG08E1vj+uJwvH/oVf35kmEfz3BbtfIX0dwuhdzyPLJnGiFAzRUd3seZ3y8m/N5ai4uuUVzdSQCPtwNN462IZPJmnfjWMCMwUHd7OunV/5Mvj6az9eYo1EetuubFM/ukMSv/yATsYxfyfjiECCO4X5l79V16nKMjYPkTuHM2Zn6LNfCaKiIiIiMeCg01ABdVmvHefZy2n+tQH/HL9UUpHTOOpXxmeAaxbxZenf0bG4jF1SWDP7lGt9+VlWax6qZBCUzzTZwyr+wKvx/e77j5PsO4XlYZnERXHWPf7N9hyznJ/+cKY+ucgf/rdMXYv/h2rbc86PHiGIyIiIiJtk7/m93WHEsAdUHlZPC+ufZrJtjl7xqQwechbLFh9kE0HHyUpLYyIuBQm3DgKQGxCChPqOs5eZ/fGTWSVxfLUq79jji0pTArT77Emyt7cyOQ//8zuJqToNDz16lrD+jYVhEx/gbUPxdeVM2H8MEs5r71BxMR0Mpak1N3kTUgZQ+yKF1n3l4/JnfYzkgIBLrFt7cecwFVMJ1n38h/ZYrdN6/qhY1j96s8McxelMD3pY3753HZWvXeQD3+W4jpJmv8BKz4pIeRe+xvQCWNmMGvnK6S/+zGrPh/Dn2caEtRlFSQ9/TovpNg2lsKE8fH86bkMtm3bw/wRLuYEAsBM7vsb2VZqYs4LK+sT66QwYfIMdvx5OX86BSTUv6P62EZWfF7BiAUr7GKYMGYa0z95hfT33mJD0ussScA9l7ez6t1CGPogGf/LltgGxkxm+j0b+eWKLDcLct0OPIrXGotj3TNmMhMGvMiCv2SwLmUML4wL8qDcMBLGjCJh6wdALEljUgxzAHte/yIiIiIiIh1SxVF2H6iA/vGWnqeVlsUtvs+zys88yfSfr+TPE+t7y06YOI3p65ez7PONbBg3iqdGBTX/HrW4EBas4EPjvbrHZbXsecKJrRlsOefq/nIa2/79Rdasf4ttd61gTu/697jzDEdERERExFOd/B2AeF/wtGn1Nw5WIXdNZnoQ5J4uaPzNl7PY9hXEPrLQOZlrGsOShaMILjvKjq/Mdi8Fp812kfwF6MucyfH2i0xjmDM9DMxhzPkfKfbDPAX2Zfo9sWC+RGGpddmFLLbkQ/QD813ENIwl/3OyfRnW9Ufc/6DhZs0q6kFW/58/8Nefjmqwh2zuP7IoDRrFU4+PcRqCKnrmQhb1hxM7syg0vhCUwqwUh42ZUph+N3CswH5do8qD7Pi8AiY+ypJRDu8PjGTWE4/WDVllYebLfUcpD53MEmMPaUsQxN47g+lUsOPQyYa26KQwaw8nCGP+/zTcDFuFDF3Ic/c5VmLDnNuBZ/FaYunL/Ptd1P2M3/Hh23/gqZGel9sgj+tfRERERESkY8jdlcGajdZ/GatI/8VbbCuLZNHSRzFORtTS+7w6o2azZKLjFEAmkh5/lOlUsG3fQappwT1q0BgWOcTjcVkteZ5Qc5TdOysInjzfxf1lX+akzyCWS2zJsn8u06JnOCIiIiIiDVAP4A4oIdrVHLCRxMa58eaiQk4Ac+Jczz0bHBdPAscoulYC1N9YJcQ43vTZ9CW2t/PSYJPl7iaki/NrEVGRwFEKLwO9gaslFAFzEuKdVwaIi2cCWey2/W5df0IDMQWbwhoZHvkShWctZSa4HGYploRhwOcllEL9TXFcX0Ov0nrR0Q3Vi1XZdYqAEQnxrmMKjycpCnLrFpRQ+j1AITv+klG/z4bX84HyyorGt2tQeu06MIYR/V2/njAkHj495lZZzu3As3gtscS7bDMEBhESavvm8yXv1IPH9S8iIiIiItIxFBWc5MvvrL90jyTpgYW8OG0ysQ7zzrb0Ps8memi865GxusYzIgF2f3+dUlpwjxoX65Tk9bisljxPKL1EvhkSBse6XicmniRg27Xr9jG05BmOiIiIiEiBeZ6tAAAgAElEQVQDlACWZqmubYWN1Li5XlCQR/Pdekt1DRDY5GotZCLYo/l+IkmaEUlSvKsbyOYJ7trI/MUt5v14vVuup/UvIiIiIiLSPsz62R94alTT63nO9f1YSFf3R5dqjDfvUX17v9sAs7npdUREREREWkgJYLHXM5JoIP/iJRjl4huvpSUUAhMivJ2wazqm3HOFMMpFz+T8k2S5WL+w9DrgPFdOdcV1qmuCCAl1dfMZSUQf4FAhRTU4fXsYrlNUDERFunitGULDiAayThdSTV/nRPa1Y3x5DsMctNb4yobxyEL7YbmaKyI8DCgk/yIkxTi/fuKY+8NJO/MsXkssJRRdA8IdXqwxU15RAUEmQrp6qR48rn8REREREZE7XfPux/K/OkbpfX2dewFXFnLiHDA+jAig2ov3qB7f77bkeYLt/vLiJYwjptW5ZnmeEt27FZ+niIiIiMgdS3MAi73+KczpDyc++Zhcx9Fzay6xZVMW5UGjmHxXK35L1hpT4Sfb+dJFTNvez6Lcxfq5/72LQsdexBVZ/Gnpr3nsL8eodrmxICakjCLYfJQNn19yerX8q02sOwax01O8knylawqTJ0N11na2FTu+WEHuR9sdhh+2xlecxZavnIc3Lvr0RWb8P8+w7pT7IcSmpBBLCZs+OWhfjwDF21n3ufvDSTvzLF5LLIVs2eM8z1H54bd47Mlfs+a45+VCX6L7AWUV9sfd4/oXERERERG50zXzvvTYdjYdc1zfTOHn29lthukpKQTj3XtUj8tqyfME2/1lpuv7yy83byeXSOakNDC9lYiIiIiIFykBLA5ieeS5BxlRdpTf/tsqNmQeI/9iAbmZm/jtr15kXb6JOb9KZ3KrDosbyyNLZ5BQeZTf/uJF1mUeI/9yIScObOdPv3uRdb1HMdlh/TkLU4i4sIulv8tgR34h5WUl5B/9gBX/tpHdxLLkJykNDhsdPC6d399r4sS7r7A0Yzu78wspzM9iW8aLPLb6KNVDH+T393kl/QsEMfnxdKZHXGLd879mxSdZnLhYQv6JXWz49+X89vwwZjnM+xM8biEvTIQdq5ezbEsW+ZevU365gN1/eZH0v1wi4t6FzB/qQQgxj/LCvEiqMzN47Hcb2XGigKKLJ9m98y2WPr+d6JRhLdpDj+KNeZCnZpgo3LKKpRt3ceKiZd0vP/kjS986BkMfZFFyULPqISTUBMVZbPgkiy+PHiS32EyT9f9tPNM175KIiIiIiIid5tyXJtwVy5d/XM6yLbvIPVdI/okstryxnKXvXSJ43HyWjLN+0dyb96gel9WS5wlBTF74M+aEFrLm317kTzuzOHHR9uzi1/w2s4IRC57mERc9kUVEREREvE1DQIuzqNn8+T8jWffWRrase4NN1sUhQ8fw3NMLmTXYO/P2eCTuUdb+RxirXvuYLeveYAtAUBAj7k3n3ceD2JR1zG714BHpZLzSlzVvbedPLx3kT9blIUMn8/sXFzLBacwpIxNJC1awNmYjqzZ/zKo9H1sWdw1jwkPpPPdQChHenPs3PIUX/sNE7Fsb2fTeRrLeAwgiduJs/rx4DCdePerwhjAmP7mCtQkbWbV5I0s/oj6+eU/zwuxRhHgYQsKcFawNe4sV72bxp3+3DKgdHNqXOc+uYEmnD9id1UQBjfIk3iCSFq5kbXQGqzZ/wC93fWBdbDnWf37cWPee1cOIB57mqdK3WPfeRrKAEQtW8OeZfevqP/q1t9jiWP/PjuHEq8fY3ZLdFxERERER6XA8vy8Nvms+ax85ypq3PmbZR9Z5cINcr+/Ne1RPy2rR8wTTGJ76j18R+24G697dyA5bmTHxzF+WzqK7NPyziIiIiLSOgNu3b9/2dxCtqeTKDQACAgLq/hl/d/zZyLjMVm0BAQEYq9D2s+P/4WF+SJp6Q00F5RVmgk1hBHsz6dkS5grKK2lgDl8XWrgP1RXXqcZEiKkVhr2uqaC8AoJNJrdj9XZ81RXXqe5kIqSrb/bXo3grr1Nubmi+5haU25Bm1L9Ie3btumXIO+Pnn/F/x5/d0aE/E0VERETEpUbvxy5v55e//BhsX8IFj+7rvXmP6nFZLXmeUGOmvKICuoYR0oqzaImIiIiI93j6/LShZ6mePmO9ZbZsNzfX80kqk5KSAPUAlqYEmggJ9XcQDoJMnt08tXAfgk1hDQ4X7XXNiNXb8fl6fz0qv2sYIW4ON+6VuNtiexcREREREWnjPL4f8+C+3pv3qB6X1ZJ7xMAgQkLDmvlmEREREZGW0RzAIiIiIiIiIiIiIiIiIiIdhBLAIiIiIiIiIiIiIiIiIiIdhIaAFhERERERERER3+gay+QZKTCg6fl+RURERETEO5QAFhERERERERER3wgdxSMLR/k7ChERERGRO4qGgBYRERERERERERERERER6SCUABYRERERERERERERERER6SCUABYRERERERERERERERER6SCUABYRERERERERERERERER6SCUABYRERERERERERERERER6SCUABYRERERERERERERERER6SCUABYRERERERERERERERER6SCUABYRERERERERERERERER6SCUABYRERERERERERERERER6SA6+6LQwkvfczz/LLW1tc0uo1OnToxMGEhs3z5ejExEREREREREREREREREpOPyegK49PoN9h35morKKq+U9T/umUBEWA8vRCYiIiIiIiIiIiIiIiIi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GxvqzWYZyjp1ZhrLrTHW/Q7APNIXNvBWL+udOoWpZNsn04lg7ptrKUiyH8LW1TDFfhETR/7MtLre1VNf/oh3kqE5cfd+YAHpSUuJ32hdz/A7wNSF8xoevtbnfN+mezexv07146Ktt86ww44SWb5zGU8Y2oHlmvJqE3M812tq31skeSnZLxuvd3hUT8nzl8HMh4h/ybpP6fOIr7v2pJK+0DbfbARDWG0YCnseW3J9eSwSm75WLZwCK9KItw4RXX9+Okh+jJU8VB/7wnmezZveGDc/q+ZGWZL1ezeeY+WLDp8n6w11PmkZW15OZa+117XT55nT623pOiIiIiIiIiKeCLh9+/ZtfwfRmkqu3AAgICCg7p/xd8efjYzLbNUWEBCAsQptPzv+Hx5m8vauiIiItMi16xUAdp9/xv8df3aHPz4Tjxw5wtixY71apoiISLtiG665LQ95LyIiIiIi4kO+eEbo6fPThp6levqM9ZbZst3c3FyP3geQlJQEqAewiIiIiIiISDtmHaJ6faaSvyIiIiIiIgIoASwiIiIiIiLSLuW8lsYjGxsZolpERERERETuSEoAi4iIiIiIiLRDyc9mUvCsv6MQERERERGRtqaTvwMQERERERERERERERERERHvUAJYRERERERERERERERERKSDUAJYRERERERERERERERERKSDUAJYRERERERERERERERERKSDUAJYRERERERERERERERERKSDUAJYRERERERERERERERERKSDUAJYRERERERERERERERERKSDUAJYRERERERERERERERERKSDUAJYRERERERERERERERERKSD6OzvAERERERa6siRI/4OQURERERERERERKRNUAJYRERE2r2xY8f6OwS5Ax05ckRtT9yituI51Zm0lNqQtHdqwyIiIiKeUQcRexoCWkRERERERERERERERESkg1APYBERERERET84W3iBkitXqampadXtBgYGEtmrJwNj+7fqdkVERERERESkdSgBLCIiIiIi0srOFl6g+PsSv2y7pqambttKAouIiIiIiIh0PBoCWkREREREpJWVXLnq7xDaRAwiIiIiIiIi4n1KAIuIiIiIiLSy1h72ua3GICIiIiIiIiLepwSwiIiIiIiIiIiIiIiIiEgHoQSwiIiIiIiIiIiIiIiIiEgHoQSwiIiIiIiIiIiIiIiI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o7h+xPWN8lVoHcKNsh+IjVEdiYjcCXqGhfJd0WV/hyFedif2fmitBEBbeUjsW6Vs/vlDLN9vWLRwLQXPJvotIhGRjqI9z/1r096S7uI9BbmZvPH2BrIP5wDOwzC3hpRxSS3u+d+ReqOLiIiI+IPPEsAAE5MSuVVTyz/PXcB86xaHT/yT3FMFhIV2p0tQEFVmM9fLfuBWTQ1gmRP4R3H9mZikB1dNqb19W8M+i4jcIQIDO2mudxFxEMHcNzOZ6+8wREREpM15+slFPI3/vshwp33JUURERKQt8mkCOCAggCljRxHZM4wjJ/5JZVU1t2pquHLthtO6XYKDGDt8CIkJAwkICPBlWCIiIiIiIiIiIiIiIiIiHZJPE8BgSQInxsfxo7h+nDxznjMXLnGt/Adu375NQEAA4SHdGdS/L8MGDSCos8/DERERERERERERERERERHpsFot4xrUuTOjhgxi1JBBrbVJEREREREREREREREREZE7iiaRFRERERERERERERERERHpIJQAFhERERERaWWBgYH+DqFNxCAiIiIiIiIi3qcEsIiIiIiISCuL7NXT3yG0iRhERERERERExPtabQ5gERERERERsRgY2x+AkitXqampadVtBwYGEtmrZ10MIiIiIiIiItKxKAEsIiIiIiLiBwNj+ysJKyIiIiIiIiJepyGgRUREREREREREREREREQ6CPUAvhPVlHMm92sOXSinAoAgImPiSE0aTGSQw7rmK+QdOUFe8U3LusHdGBw7gkmJvQi0rXPtNPu++p7g/smkxndxscFaCnO+5ERVHyalDibUk7IdXMvJ5OPLvXhwxkjCjS9cPs6HB64wYGIa43sbX7jB13/P5mh1b2bcl0yMq0LNV8g7eJycK1XcAgjsQuyAoYwfGU1ooGG7hVX27+sWY4nj8nE+PHCRG3UvBNEzKprUkcOJCnG1wXPs3nqKQoelsYkzmJ4A+Vm7yGIoiybHNVALBp4cS4CzR/jLV1eI/FEas4bbH6uGtmu33GlfAbqQODGN8RznwwNFmIZOZNYwU4Nl5GftIuuKi9is9Vni6vVerurjew7tzOVM+EjmphiObPHXfJh9lQEpaYyPdrEdEemQjhw54u8Q5A6ltifuUlvxnOpMWkptSNo7tWERERERaS4lgO805fns+PspCgN7kTxyNMN6BXH1u0K+Pv0NH/53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\" alt=\"\" /></h4>\n</body>\n</html>', '2021-06-22 23:13:19', 'a');
INSERT INTO `tm_recetas` (`id_receta`, `id_catg_receta`, `id_pres`, `nombre`, `receta`, `fecha_creacion`, `estado`) VALUES
(34, 7, 208, 'machupichu', '<!DOCTYPE html>\n<html>\n<head>\n</head>\n<body>\n<strong>1 coctel</strong>\n</body>\n</html>', '2023-06-22 19:59:21', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_recetas_catg`
--

CREATE TABLE `tm_recetas_catg` (
  `id_catg` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `estado` varchar(2) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tm_recetas_catg`
--

INSERT INTO `tm_recetas_catg` (`id_catg`, `nombre`, `imagen`, `estado`) VALUES
(7, 'CALIENTES', 'default.png', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_receta_ingrediente`
--

CREATE TABLE `tm_receta_ingrediente` (
  `id_ingrediente` int(11) NOT NULL,
  `id_receta` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `unidad` varchar(30) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `oracion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tm_receta_ingrediente`
--

INSERT INTO `tm_receta_ingrediente` (`id_ingrediente`, `id_receta`, `cantidad`, `unidad`, `nombre`, `oracion`) VALUES
(52, 33, 12, 'C.C', '', '12  C.C   CALIENTE 1'),
(53, 33, 12, 'GRAMOS', '', '12  GRAMOS   RECETA DE PREVIA'),
(54, 34, 1, 'UNIDADES', 'MIEL', '1  UNIDADES   miel');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_registro_impresiones`
--

CREATE TABLE `tm_registro_impresiones` (
  `id_registro` int(11) NOT NULL,
  `nombre_impresora` varchar(40) NOT NULL,
  `tipo_impresion` varchar(30) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'a',
  `url` longtext NOT NULL,
  `json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tm_registro_impresiones`
--

INSERT INTO `tm_registro_impresiones` (`id_registro`, `nombre_impresora`, `tipo_impresion`, `id_usuario`, `fecha`, `id_pedido`, `status`, `url`, `json`) VALUES
(1, 'BARRA', 'COMANDA', 53, '2023-03-20 15:44:52', 3, 'a', 'http://192.168.1.9/imprimir/comanda.php?data=', '{\"pedido_tipo\":\"1\",\"pedido_numero\":\"WILFREDO\",\"pedido_cliente\":\"MESA: 8\",\"pedido_mozo\":\"KARINA JULIANA ALVAREZ\",\"correlativo_imp\":\"000001\",\"nombre_imp\":\"BARRA\",\"nombre_pc\":\"SISTEMAS\",\"codigo_anulacion\":0,\"id_pedido\":\"3\",\"items\":[{\"combo\":\"b\",\"combos_detalle\":[],\"area_id\":2,\"nombre_imp\":\"BARRA\",\"producto_id\":2,\"producto\":\"EL ORGULLOSO\",\"presentacion\":\"COCTEL\",\"cantidad\":1,\"precio\":\"23.00\",\"comentario\":\"\",\"total\":23,\"id\":0},{\"combo\":\"b\",\"combos_detalle\":[],\"area_id\":2,\"nombre_imp\":\"BARRA\",\"producto_id\":3,\"producto\":\"EL AGRESIVO\",\"presentacion\":\"COCTEL\",\"cantidad\":1,\"precio\":\"23.00\",\"comentario\":\"\",\"total\":23,\"id\":1},{\"combo\":\"b\",\"combos_detalle\":[],\"area_id\":2,\"nombre_imp\":\"BARRA\",\"producto_id\":5,\"producto\":\"EL SENSUAL\",\"presentacion\":\"COCTEL\",\"cantidad\":1,\"precio\":\"23.00\",\"comentario\":\"\",\"total\":23,\"id\":2}]}'),
(2, 'BARRA', 'COMANDA', 1, '2023-03-20 18:47:32', 3, 'a', 'http://192.168.1.9/imprimir/comanda.php?data=', '{\"pedido_tipo\":\"1\",\"pedido_numero\":\"WILFREDO\",\"pedido_cliente\":\"MESA: 8\",\"pedido_mozo\":\"KARINA JULIANA ALVAREZ\",\"correlativo_imp\":\"000002\",\"nombre_imp\":\"BARRA\",\"nombre_pc\":\"SISTEMAS\",\"codigo_anulacion\":0,\"id_pedido\":\"3\",\"items\":[{\"combo\":\"b\",\"combos_detalle\":[],\"area_id\":2,\"nombre_imp\":\"BARRA\",\"producto_id\":308,\"producto\":\"COCTELES CON PISCO\",\"presentacion\":\"COCTEL\",\"cantidad\":1,\"precio\":\"86.00\",\"comentario\":\"\",\"total\":86,\"id\":0},{\"combo\":\"b\",\"combos_detalle\":[],\"area_id\":2,\"nombre_imp\":\"BARRA\",\"producto_id\":5,\"producto\":\"EL SENSUAL\",\"presentacion\":\"COCTEL\",\"cantidad\":1,\"precio\":\"23.00\",\"comentario\":\"\",\"total\":23,\"id\":1},{\"combo\":\"b\",\"combos_detalle\":[],\"area_id\":2,\"nombre_imp\":\"BARRA\",\"producto_id\":4,\"producto\":\"LA DIVA\",\"presentacion\":\"COCTEL\",\"cantidad\":1,\"precio\":\"23.00\",\"comentario\":\"\",\"total\":23,\"id\":2},{\"combo\":\"b\",\"combos_detalle\":[],\"area_id\":2,\"nombre_imp\":\"BARRA\",\"producto_id\":3,\"producto\":\"EL AGRESIVO\",\"presentacion\":\"COCTEL\",\"cantidad\":1,\"precio\":\"23.00\",\"comentario\":\"\",\"total\":23,\"id\":3},{\"combo\":\"b\",\"combos_detalle\":[],\"area_id\":2,\"nombre_imp\":\"BARRA\",\"producto_id\":1,\"producto\":\"EL PITUCO\",\"presentacion\":\"COCTEL\",\"cantidad\":2,\"precio\":\"29.00\",\"comentario\":\"\",\"total\":58,\"id\":4}]}'),
(3, 'COCINA', 'COMANDA', 55, '2023-03-21 16:23:06', 5, 'a', 'http://192.168.1.9/imprimir/comanda.php?data=', '{\"pedido_tipo\":\"1\",\"pedido_numero\":\"WILFREDO\",\"pedido_cliente\":\"MESA: 9\",\"pedido_mozo\":\"KARINA JULIANA ALVAREZ\",\"correlativo_imp\":\"000003\",\"nombre_imp\":\"COCINA\",\"nombre_pc\":\"SISTEMAS\",\"codigo_anulacion\":0,\"id_pedido\":\"5\",\"items\":[{\"combo\":\"a\",\"combos_detalle\":[],\"area_id\":1,\"nombre_imp\":\"COCINA\",\"producto_id\":157,\"producto\":\"COMBO SUPER MIX \",\"presentacion\":\"6 PIEZAS DE POLLO, 4 ALITAS O NUGGETS, 3 CHIL\",\"cantidad\":1,\"precio\":\"50.90\",\"comentario\":\"\",\"total\":50.9,\"id\":0},{\"combo\":\"a\",\"combos_detalle\":[],\"area_id\":1,\"nombre_imp\":\"COCINA\",\"producto_id\":154,\"producto\":\"COMBO MIX\",\"presentacion\":\"2 PIEZAS DE POLLO, 4 ALITAS O NUGGETS, 1 PAPA\",\"cantidad\":1,\"precio\":\"33.90\",\"comentario\":\"\",\"total\":33.9,\"id\":1}]}'),
(4, 'COCINA', 'COMANDA', 55, '2023-03-21 16:34:09', 6, 'a', 'http://192.168.1.9/imprimir/comanda.php?data=', '{\"pedido_tipo\":\"1\",\"pedido_numero\":\"WILFREDO\",\"pedido_cliente\":\"MESA: 1\",\"pedido_mozo\":\"KARINA JULIANA ALVAREZ\",\"correlativo_imp\":\"000004\",\"nombre_imp\":\"COCINA\",\"nombre_pc\":\"SISTEMAS\",\"codigo_anulacion\":0,\"id_pedido\":\"6\",\"items\":[{\"combo\":\"a\",\"combos_detalle\":[],\"area_id\":1,\"nombre_imp\":\"COCINA\",\"producto_id\":157,\"producto\":\"COMBO SUPER MIX \",\"presentacion\":\"6 PIEZAS DE POLLO, 4 ALITAS O NUGGETS, 3 CHIL\",\"cantidad\":1,\"precio\":\"50.90\",\"comentario\":\"\",\"total\":50.9,\"id\":0}]}'),
(5, 'COCINA', 'COMANDA', 55, '2023-03-21 16:34:21', 6, 'a', 'http://192.168.1.9/imprimir/comanda.php?data=', '{\"pedido_tipo\":\"1\",\"pedido_numero\":\"WILFREDO\",\"pedido_cliente\":\"MESA: 1\",\"pedido_mozo\":\"KARINA JULIANA ALVAREZ\",\"correlativo_imp\":\"000005\",\"nombre_imp\":\"COCINA\",\"nombre_pc\":\"SISTEMAS\",\"codigo_anulacion\":0,\"id_pedido\":\"6\",\"items\":[{\"combo\":\"a\",\"combos_detalle\":[],\"area_id\":1,\"nombre_imp\":\"COCINA\",\"producto_id\":156,\"producto\":\"COMBO FESTIVAL\",\"presentacion\":\"3 PIEZAS DE POLLO, 4 NAGGETS, 2 PAPAS GRANDES\",\"cantidad\":1,\"precio\":\"45.90\",\"comentario\":\"\",\"total\":45.9,\"id\":0}]}'),
(6, 'BARRA', 'COMANDA', 55, '2023-03-21 16:41:02', 7, 'a', 'http://192.168.1.9/imprimir/comanda.php?data=', '{\"pedido_tipo\":\"1\",\"pedido_numero\":\"WILFREDO\",\"pedido_cliente\":\"MESA: 2\",\"pedido_mozo\":\"KARINA JULIANA ALVAREZ\",\"correlativo_imp\":\"000006\",\"nombre_imp\":\"BARRA\",\"nombre_pc\":\"SISTEMAS\",\"codigo_anulacion\":0,\"id_pedido\":\"7\",\"items\":[{\"combo\":\"b\",\"combos_detalle\":[],\"area_id\":2,\"nombre_imp\":\"BARRA\",\"producto_id\":10,\"producto\":\"FLAT WHITE BARSOL\",\"presentacion\":\"COCTEL\",\"cantidad\":1,\"precio\":\"30.00\",\"comentario\":\"\",\"total\":30,\"id\":0}]}');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_repartidor`
--

CREATE TABLE `tm_repartidor` (
  `id_repartidor` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `estado` varchar(5) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tm_repartidor`
--

INSERT INTO `tm_repartidor` (`id_repartidor`, `descripcion`, `estado`) VALUES
(1, 'INTERNO', 'a'),
(2222, 'RAPPI', 'a'),
(3333, 'UBER', 'a'),
(4444, 'GLOVO', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_rol`
--

CREATE TABLE `tm_rol` (
  `id_rol` int(11) NOT NULL,
  `descripcion` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_rol`
--

INSERT INTO `tm_rol` (`id_rol`, `descripcion`) VALUES
(1, 'ADMINISTRATOR'),
(2, 'ADMINISTRADOR'),
(3, 'CAJERO'),
(4, 'PRODUCCION'),
(5, 'MOZO'),
(6, 'REPARTIDOR'),
(7, 'MESAS');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_salon`
--

CREATE TABLE `tm_salon` (
  `id_salon` int(11) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `estado` varchar(5) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_salon`
--

INSERT INTO `tm_salon` (`id_salon`, `descripcion`, `estado`) VALUES
(1, 'WILFREDO', 'a'),
(2, 'CRISTIAN', 'a'),
(3, 'ABRAHAM', 'i'),
(4, 'GINO', 'a'),
(5, 'LEONARDO', 'a'),
(6, 'RULLY', 'a'),
(10, 'Jeancarlo', 'i');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_tipo_compra`
--

CREATE TABLE `tm_tipo_compra` (
  `id_tipo_compra` int(11) NOT NULL,
  `descripcion` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tm_tipo_compra`
--

INSERT INTO `tm_tipo_compra` (`id_tipo_compra`, `descripcion`) VALUES
(1, 'CONTADO'),
(2, 'CREDITO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_tipo_doc`
--

CREATE TABLE `tm_tipo_doc` (
  `id_tipo_doc` int(11) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `serie` char(4) NOT NULL,
  `numero` varchar(8) NOT NULL,
  `estado` varchar(5) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_tipo_doc`
--

INSERT INTO `tm_tipo_doc` (`id_tipo_doc`, `descripcion`, `serie`, `numero`, `estado`) VALUES
(1, 'BOLETA DE VENTA', 'B001', '00000407', 'a'),
(2, 'FACTURA', 'F003', '00000001', 'a'),
(3, 'NOTA DE VENTA', '0001', '00000001', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_tipo_gasto`
--

CREATE TABLE `tm_tipo_gasto` (
  `id_tipo_gasto` int(11) NOT NULL,
  `descripcion` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_tipo_gasto`
--

INSERT INTO `tm_tipo_gasto` (`id_tipo_gasto`, `descripcion`) VALUES
(1, 'POR COMPRAS'),
(2, 'POR SREVICIOS'),
(3, 'POR REMUNERACION'),
(4, 'POR CREDITO DE COMPRAS');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_tipo_medida`
--

CREATE TABLE `tm_tipo_medida` (
  `id_med` int(11) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `grupo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_tipo_medida`
--

INSERT INTO `tm_tipo_medida` (`id_med`, `descripcion`, `grupo`) VALUES
(1, 'UNIDAD', 1),
(2, 'KILOS', 2),
(3, 'GRAMOS', 2),
(4, 'MILIGRAMOS', 2),
(5, 'LITRO', 3),
(6, 'MILILITRO', 3),
(7, 'LIBRAS', 2),
(8, 'ONZAS', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_tipo_pago`
--

CREATE TABLE `tm_tipo_pago` (
  `id_tipo_pago` int(11) NOT NULL,
  `id_pago` int(11) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `estado` varchar(5) NOT NULL DEFAULT 'a'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_tipo_pago`
--

INSERT INTO `tm_tipo_pago` (`id_tipo_pago`, `id_pago`, `descripcion`, `estado`) VALUES
(1, 1, 'EFECTIVO', 'a'),
(2, 2, 'TARJETA', 'a'),
(3, 3, 'PAGO MIXTO', 'a'),
(4, 4, 'CULQI', 'a'),
(5, 5, 'YAPE', 'a'),
(6, 5, 'LUKITA', 'a'),
(7, 5, 'TRANSFERENCIA', 'a'),
(8, 2, 'ESTILOS', 'i'),
(9, 2, 'CREDISHOP', 'i'),
(10, 6, 'TASA', 'i'),
(11, 5, 'PLIN', 'i'),
(12, 5, 'TUNKI', 'i'),
(13, 2, 'CREDITO', 'i');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_tipo_pedido`
--

CREATE TABLE `tm_tipo_pedido` (
  `id_tipo_pedido` int(11) NOT NULL,
  `descripcion` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_tipo_pedido`
--

INSERT INTO `tm_tipo_pedido` (`id_tipo_pedido`, `descripcion`) VALUES
(1, 'MESA'),
(2, 'LLEVAR'),
(3, 'DELIVERY'),
(4, 'PORTERO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_tipo_venta`
--

CREATE TABLE `tm_tipo_venta` (
  `id_tipo_venta` int(11) NOT NULL,
  `descripcion` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_tipo_venta`
--

INSERT INTO `tm_tipo_venta` (`id_tipo_venta`, `descripcion`) VALUES
(1, 'CONTADO'),
(2, 'CREDITO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_turno`
--

CREATE TABLE `tm_turno` (
  `id_turno` int(11) NOT NULL,
  `descripcion` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_turno`
--

INSERT INTO `tm_turno` (`id_turno`, `descripcion`) VALUES
(1, 'PRIMER TURNO'),
(2, 'SEGUNDO TURNO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_usuario`
--

CREATE TABLE `tm_usuario` (
  `id_usu` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_areap` int(11) NOT NULL,
  `id_mesa` int(11) DEFAULT NULL,
  `dni` varchar(10) NOT NULL,
  `ape_paterno` varchar(45) DEFAULT NULL,
  `ape_materno` varchar(45) DEFAULT NULL,
  `nombres` varchar(45) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `usuario` varchar(45) DEFAULT NULL,
  `contrasena` varchar(45) DEFAULT 'cmVzdHBl',
  `estado` varchar(5) DEFAULT 'a',
  `imagen` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tm_usuario`
--

INSERT INTO `tm_usuario` (`id_usu`, `id_rol`, `id_areap`, `id_mesa`, `dni`, `ape_paterno`, `ape_materno`, `nombres`, `email`, `usuario`, `contrasena`, `estado`, `imagen`) VALUES
(1, 1, 0, NULL, '73674643', '.', 'W', 'DESARROLLO', 'dev@laprevia.com', 'administrador', 'd2lsc29uJGVkdWFyZG8=', 'a', '220226010308.png'),
(40, 5, 0, NULL, '73616324', 'JORGE', 'TICLLACURI', 'SABIO ALDERSON', 'jorge@gmail.com ', '73616324', 'NzM2MTYzMjQ=', 'i', 'default-avatar.png'),
(41, 5, 0, NULL, '', 'TOBAR', 'RF', 'LUIS', 'luis@gmail.com', '27659131', 'Mjc2NTkxMzE=', 'a', 'default-avatar.png'),
(42, 5, 0, NULL, '60106738', 'GUTIERREZ', 'CASTILLON', 'ABRAHAM', 'abra@gmail.com', '6738', 'NjczOA==', 'a', 'default-avatar.png'),
(43, 3, 0, NULL, '48419606', 'ROCA', 'CARAHUANCA', 'MIRIAM ELENA', 'Mirian', '48419606', 'NDg0MTk2MDY=', 'a', 'default-avatar.png'),
(44, 5, 0, NULL, '75403771', 'VALENCIA', 'MALLA', 'CRISTIAN MANUEL', 'valencia@gmail.com', '3771', 'Mzc3MQ==', 'a', 'default-avatar.png'),
(45, 5, 0, NULL, '76612043', 'HUAMAN', 'MINA', 'JEAN CARLOS', 'jeancarlo@gmail.com', '76612043', 'NzY2MTIwNDM=', 'i', 'default-avatar.png'),
(46, 5, 0, NULL, '46291257', 'CUSTODIO', 'JESUS', 'WILLY GINO', 'wlly@gmail.com', '1257', 'MTI1Nw==', 'a', 'default-avatar.png'),
(47, 7, 0, NULL, '42433242', 'GONZALEZ', 'OTRILLA', 'EDUARDO', 'randellcode@outlook.es', 'portero', 'cG9ydGVybw==', 'a', 'default-avatar.png'),
(48, 5, 0, NULL, '44843773', 'CAMACHO', 'SALINAS', 'RULLI HENRY', 'previa@gmail.com', '3773', 'Mzc3Mw==', 'a', 'default-avatar.png'),
(49, 5, 0, NULL, '73616324', 'JORGE', 'TICLLACURI', 'SABIO ALDERSON', 'alder@gmail.com', '6324', 'NjMyNA==', 'i', 'default-avatar.png'),
(50, 3, 0, NULL, '20051265', 'FLORES', 'ULLOA', 'MAXIRA DEL PILAR', 'maxira@gmail.com', '1265', 'MTI2NQ==', 'a', 'default-avatar.png'),
(51, 6, 0, NULL, '44342342', 'MUÑOZ', 'REGALADO', 'WILMER', 'wilmer@gmai.com', '1234', 'MTIzNA==', 'a', 'default-avatar.png'),
(52, 2, 0, NULL, '43524323', 'HUATA', 'HINOSTROZA', 'GIL NILTON', 'demo@gmail.com', 'admin', 'YWRtaW4=', 'a', '230311051052.png'),
(53, 7, 0, 8, '42349234', 'MENESES', 'ATAYPOMA', 'RAQUEL', 'mesa1@gmail.com', 'mesa8', 'bWVzYTg=', 'a', 'default-avatar.png'),
(54, 5, 0, NULL, '45453454', 'ALVAREZ', 'SANTOS', 'KARINA JULIANA', 'nadie@example.com', 'nadie', 'bmFkaWU=', 'a', 'default-avatar.png'),
(55, 4, 1, NULL, '18047501', 'PULIDO', 'DE FERNANDEZ', 'EULALIA', 'oscaranaluisa@hotmail.com', 'mesa9', 'bWVzYTk=', 'a', 'default-avatar.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tm_venta`
--

CREATE TABLE `tm_venta` (
  `id_venta` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_tipo_pedido` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_tipo_doc` int(11) NOT NULL,
  `id_tipo_pago` int(11) NOT NULL,
  `id_usu` int(11) NOT NULL,
  `id_apc` int(11) NOT NULL,
  `serie_doc` char(4) NOT NULL,
  `nro_doc` varchar(8) NOT NULL,
  `pago_efe` decimal(10,2) DEFAULT 0.00,
  `pago_efe_none` decimal(10,2) DEFAULT 0.00,
  `pago_tar` decimal(10,2) DEFAULT 0.00,
  `descuento_tipo` char(1) NOT NULL DEFAULT '1',
  `descuento_personal` int(11) DEFAULT NULL,
  `descuento_monto` decimal(10,2) DEFAULT 0.00,
  `descuento_motivo` varchar(200) DEFAULT NULL,
  `comision_tarjeta` decimal(10,2) DEFAULT 0.00,
  `comision_delivery` decimal(10,2) DEFAULT 0.00,
  `igv` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) DEFAULT 0.00,
  `codigo_operacion` varchar(20) DEFAULT NULL,
  `fecha_venta` datetime DEFAULT NULL,
  `estado` varchar(15) DEFAULT 'a',
  `enviado_sunat` char(1) DEFAULT NULL,
  `code_respuesta_sunat` varchar(5) NOT NULL,
  `descripcion_sunat_cdr` varchar(300) NOT NULL,
  `name_file_sunat` varchar(80) NOT NULL,
  `hash_cdr` varchar(200) NOT NULL,
  `hash_cpe` varchar(200) NOT NULL,
  `fecha_vencimiento` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_caja_aper`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_caja_aper` (
`id_apc` int(11)
,`id_usu` int(11)
,`id_caja` int(11)
,`id_turno` int(11)
,`fecha_aper` datetime
,`monto_aper` decimal(10,2)
,`fecha_cierre` datetime
,`monto_cierre` decimal(10,2)
,`monto_sistema` decimal(10,2)
,`stock_pollo` varchar(11)
,`codigo` varchar(10)
,`estado` varchar(5)
,`desc_per` varchar(137)
,`desc_caja` varchar(45)
,`desc_turno` varchar(45)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_clientes`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_clientes` (
`id_cliente` int(11)
,`tipo_cliente` int(11)
,`dni` varchar(10)
,`ruc` varchar(13)
,`nombre` varchar(200)
,`telefono` int(11)
,`fecha_nac` date
,`direccion` varchar(100)
,`referencia` varchar(100)
,`estado` varchar(5)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_cocina_de`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_cocina_de` (
`id_pedido` int(11)
,`id_areap` int(11)
,`id_tipo` int(11)
,`id_pres` int(11)
,`cantidad` int(11)
,`comentario` varchar(100)
,`fecha_pedido` datetime
,`fecha_envio` datetime
,`estado` varchar(5)
,`nro_pedido` varchar(10)
,`id_usu` int(11)
,`nombre_prod` varchar(45)
,`pres_prod` varchar(45)
,`ape_paterno` varchar(45)
,`ape_materno` varchar(45)
,`nombres` varchar(45)
,`estado_pedido` varchar(5)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_cocina_me`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_cocina_me` (
`id_pedido` int(11)
,`id_areap` int(11)
,`id_tipo` int(11)
,`id_pres` int(11)
,`cantidad` int(11)
,`comentario` varchar(100)
,`fecha_pedido` datetime
,`fecha_envio` datetime
,`estado` varchar(5)
,`id_mesa` int(11)
,`id_mozo` int(11)
,`nombre_prod` varchar(45)
,`pres_prod` varchar(45)
,`nro_mesa` varchar(5)
,`desc_salon` varchar(45)
,`ape_paterno` varchar(45)
,`ape_materno` varchar(45)
,`nombres` varchar(45)
,`estado_pedido` varchar(5)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_cocina_mo`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_cocina_mo` (
`id_pedido` int(11)
,`id_areap` int(11)
,`id_tipo` int(11)
,`id_pres` int(11)
,`cantidad` int(11)
,`comentario` varchar(100)
,`fecha_pedido` datetime
,`fecha_envio` datetime
,`estado` varchar(5)
,`nro_pedido` varchar(10)
,`id_usu` int(11)
,`nombre_prod` varchar(45)
,`pres_prod` varchar(45)
,`ape_paterno` varchar(45)
,`ape_materno` varchar(45)
,`nombres` varchar(45)
,`estado_pedido` varchar(5)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_compras`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_compras` (
`id_compra` int(11)
,`id_prov` int(11)
,`id_tipo_compra` int(11)
,`id_tipo_doc` int(11)
,`fecha_c` date
,`fecha_r` datetime
,`hora_c` varchar(45)
,`serie_doc` varchar(45)
,`num_doc` varchar(45)
,`igv` decimal(10,2)
,`total` decimal(10,2)
,`estado` varchar(1)
,`desc_tc` varchar(45)
,`desc_td` varchar(45)
,`desc_prov` varchar(100)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_det_delivery`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_det_delivery` (
`id_pedido` int(11)
,`id_pres` int(11)
,`cantidad` int(11)
,`precio` decimal(10,2)
,`estado` varchar(5)
,`nombre_prod` varchar(45)
,`pres_prod` varchar(45)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_det_llevar`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_det_llevar` (
`id_pedido` int(11)
,`id_pres` int(11)
,`cantidad` int(11)
,`precio` decimal(10,2)
,`estado` varchar(5)
,`nombre_prod` varchar(45)
,`pres_prod` varchar(45)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_gastosadm`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_gastosadm` (
`id_ga` int(11)
,`id_tg` int(11)
,`id_per` int(11)
,`id_usu` int(11)
,`id_apc` int(11)
,`importe` decimal(10,2)
,`responsable` varchar(100)
,`motivo` varchar(100)
,`fecha_re` datetime
,`estado` varchar(5)
,`des_tg` varchar(45)
,`desc_usu` varchar(137)
,`desc_per` varchar(137)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_impresiones`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_impresiones` (
`id_registro` int(11)
,`nombre_impresora` varchar(40)
,`tipo_impresion` varchar(30)
,`encargado` varchar(137)
,`id_usuario` int(11)
,`fecha` datetime
,`id_pedido` int(11)
,`status` varchar(1)
,`url` longtext
,`json` longtext
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_insprod`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_insprod` (
`id_tipo_ins` int(1)
,`id_ins` int(11)
,`id_med` int(11)
,`id_gru` int(11)
,`ins_cod` varchar(10)
,`ins_nom` varchar(45)
,`ins_cat` varchar(45)
,`ins_med` varchar(45)
,`ins_rec` int(1)
,`ins_cos` decimal(10,2)
,`ins_sto` int(11)
,`est_a` varchar(5)
,`est_b` varchar(1)
,`est_c` varchar(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_insumos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_insumos` (
`id_ins` int(11)
,`id_catg` int(11)
,`id_med` int(11)
,`id_gru` int(11)
,`ins_cod` varchar(10)
,`ins_nom` varchar(45)
,`ins_sto` int(11)
,`ins_cos` decimal(10,2)
,`ins_est` varchar(5)
,`ins_cat` varchar(45)
,`ins_med` varchar(45)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_inventario`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_inventario` (
`id_tipo_ins` int(11)
,`id_ins` int(11)
,`ent` double
,`sal` varchar(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_inventario_ent`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_inventario_ent` (
`id_tipo_ins` int(11)
,`id_ins` int(11)
,`total` double
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_inventario_sal`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_inventario_sal` (
`id_tipo_ins` int(11)
,`id_ins` int(11)
,`total` double
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_listar_mesas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_listar_mesas` (
`id_mesa` int(11)
,`id_salon` int(11)
,`nro_mesa` varchar(5)
,`estado` varchar(45)
,`desc_salon` varchar(45)
,`id_pedido` int(11)
,`fecha_pedido` datetime
,`nro_personas` int(11)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_mesas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_mesas` (
`id_mesa` int(11)
,`id_salon` int(11)
,`nro_mesa` varchar(5)
,`estado` varchar(45)
,`desc_salon` varchar(45)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_pedidos_agrupados`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_pedidos_agrupados` (
`tipo_atencion` int(1)
,`id_pedido` int(11)
,`id_areap` int(11)
,`id_tipo` int(11)
,`id_pres` int(11)
,`cantidad` decimal(32,0)
,`comentario` varchar(100)
,`fecha_pedido` datetime
,`fecha_envio` datetime
,`estado` varchar(5)
,`nombre_prod` varchar(45)
,`pres_prod` varchar(45)
,`nro_mesa` varchar(5)
,`desc_salon` varchar(45)
,`ape_paterno` varchar(45)
,`ape_materno` varchar(45)
,`nombres` varchar(45)
,`estado_pedido` varchar(5)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_pedido_delivery`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_pedido_delivery` (
`id_pedido` int(11)
,`id_tipo_pedido` int(11)
,`id_usu` int(11)
,`id_repartidor` int(11)
,`fecha_pedido` datetime
,`estado_pedido` varchar(5)
,`tipo_entrega` int(11)
,`pedido_programado` int(11)
,`hora_entrega` time
,`amortizacion` decimal(10,2)
,`tipo_pago` int(11)
,`paga_con` decimal(10,2)
,`comision_delivery` decimal(10,2)
,`nro_pedido` varchar(10)
,`id_cliente` int(11)
,`tipo_cliente` int(11)
,`dni_cliente` varchar(10)
,`ruc_cliente` varchar(13)
,`nombre_cliente` varchar(100)
,`telefono_cliente` varchar(20)
,`direccion_cliente` varchar(100)
,`referencia_cliente` varchar(100)
,`email_cliente` varchar(200)
,`desc_repartidor` varchar(137)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_pedido_llevar`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_pedido_llevar` (
`id_pedido` int(11)
,`id_tipo_pedido` int(11)
,`id_usu` int(11)
,`fecha_pedido` datetime
,`estado_pedido` varchar(5)
,`nro_pedido` varchar(10)
,`nombre_cliente` varchar(100)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_pedido_mesa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_pedido_mesa` (
`id_pedido` int(11)
,`id_tipo_pedido` int(11)
,`id_usu` int(11)
,`id_mesa` int(11)
,`fecha_pedido` datetime
,`estado_pedido` varchar(5)
,`nombre_cliente` varchar(45)
,`nro_personas` int(11)
,`nro_mesa` varchar(5)
,`desc_salon` varchar(45)
,`estado_mesa` varchar(45)
,`nombre_mozo` varchar(91)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_pedido_portero`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_pedido_portero` (
`id_pedido` int(11)
,`id_tipo_pedido` int(11)
,`id_usu` int(11)
,`fecha_pedido` datetime
,`estado_pedido` varchar(5)
,`personas` int(11)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_productos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_productos` (
`id_pres` int(11)
,`is_combo` varchar(2)
,`id_prod` int(11)
,`id_tipo` int(11)
,`id_catg` int(11)
,`id_areap` int(11)
,`pro_cat` varchar(45)
,`pro_cod` varchar(45)
,`pro_nom` varchar(45)
,`pro_pre` varchar(45)
,`pro_des` varchar(200)
,`pro_cos` decimal(10,2)
,`pro_cos_del` decimal(10,2)
,`pro_rec` int(1)
,`pro_sto` int(11)
,`pro_imp` int(1)
,`pro_mar` int(1)
,`pro_igv` decimal(10,2)
,`pro_img` varchar(200)
,`del_a` int(1)
,`del_b` int(1)
,`del_c` int(1)
,`est_a` varchar(1)
,`est_b` varchar(1)
,`est_c` varchar(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_recetas_producto`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_recetas_producto` (
`id_receta` int(11)
,`id_catg_receta` int(11)
,`nombre` varchar(255)
,`fecha_creacion` datetime
,`estado` varchar(2)
,`producto` varchar(91)
,`nombre_catg` varchar(255)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_repartidores`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_repartidores` (
`id_repartidor` int(11)
,`desc_repartidor` varchar(137)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_stock`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_stock` (
`id_tipo_ins` int(11)
,`id_ins` int(11)
,`ent` double
,`sal` double
,`est_a` varchar(5)
,`est_b` varchar(1)
,`debajo_stock` int(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_usuarios`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_usuarios` (
`id_usu` int(11)
,`id_rol` int(11)
,`id_areap` int(11)
,`dni` varchar(10)
,`ape_paterno` varchar(45)
,`ape_materno` varchar(45)
,`nombres` varchar(45)
,`email` varchar(100)
,`usuario` varchar(45)
,`contrasena` varchar(45)
,`estado` varchar(5)
,`imagen` varchar(45)
,`desc_r` varchar(45)
,`desc_ap` varchar(45)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_ventas_con`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_ventas_con` (
`id_ven` int(11)
,`id_ped` int(11)
,`id_tped` int(11)
,`id_cli` int(11)
,`id_tdoc` int(11)
,`id_tpag` int(11)
,`id_usu` int(11)
,`id_apc` int(11)
,`ser_doc` char(4)
,`nro_doc` varchar(8)
,`pago_efe` decimal(10,2)
,`pago_efe_none` decimal(10,2)
,`pago_tar` decimal(10,2)
,`desc_monto` decimal(10,2)
,`desc_tipo` char(1)
,`desc_personal` int(11)
,`desc_motivo` varchar(200)
,`comis_tar` decimal(10,2)
,`comis_del` decimal(10,2)
,`igv` decimal(10,2)
,`total` decimal(10,2)
,`codigo_operacion` varchar(20)
,`fec_ven` datetime
,`estado` varchar(15)
,`enviado_sunat` char(1)
,`code_respuesta_sunat` varchar(5)
,`descripcion_sunat_cdr` varchar(300)
,`name_file_sunat` varchar(80)
,`hash_cdr` varchar(200)
,`hash_cpe` varchar(200)
,`fecha_vencimiento` date
,`desc_td` varchar(45)
,`desc_tp` varchar(45)
,`desc_usu` varchar(137)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_caja_aper`
--
DROP TABLE IF EXISTS `v_caja_aper`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_caja_aper`  AS SELECT `apc`.`id_apc` AS `id_apc`, `apc`.`id_usu` AS `id_usu`, `apc`.`id_caja` AS `id_caja`, `apc`.`id_turno` AS `id_turno`, `apc`.`fecha_aper` AS `fecha_aper`, `apc`.`monto_aper` AS `monto_aper`, `apc`.`fecha_cierre` AS `fecha_cierre`, `apc`.`monto_cierre` AS `monto_cierre`, `apc`.`monto_sistema` AS `monto_sistema`, `apc`.`stock_pollo` AS `stock_pollo`, `apc`.`cod_reporte` AS `codigo`, `apc`.`estado` AS `estado`, concat(`tp`.`nombres`,' ',`tp`.`ape_paterno`,' ',`tp`.`ape_materno`) AS `desc_per`, `tc`.`descripcion` AS `desc_caja`, `tt`.`descripcion` AS `desc_turno` FROM (((`tm_aper_cierre` `apc` join `tm_usuario` `tp` on(`apc`.`id_usu` = `tp`.`id_usu`)) join `tm_caja` `tc` on(`apc`.`id_caja` = `tc`.`id_caja`)) join `tm_turno` `tt` on(`apc`.`id_turno` = `tt`.`id_turno`)) ORDER BY `apc`.`id_apc` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_clientes`
--
DROP TABLE IF EXISTS `v_clientes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_clientes`  AS SELECT `tm_cliente`.`id_cliente` AS `id_cliente`, `tm_cliente`.`tipo_cliente` AS `tipo_cliente`, `tm_cliente`.`dni` AS `dni`, `tm_cliente`.`ruc` AS `ruc`, concat(ifnull(`tm_cliente`.`razon_social`,''),'',`tm_cliente`.`nombres`) AS `nombre`, `tm_cliente`.`telefono` AS `telefono`, `tm_cliente`.`fecha_nac` AS `fecha_nac`, `tm_cliente`.`direccion` AS `direccion`, `tm_cliente`.`referencia` AS `referencia`, `tm_cliente`.`estado` AS `estado` FROM `tm_cliente` ORDER BY `tm_cliente`.`id_cliente` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_cocina_de`
--
DROP TABLE IF EXISTS `v_cocina_de`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_cocina_de`  AS SELECT `dp`.`id_pedido` AS `id_pedido`, `vp`.`id_areap` AS `id_areap`, `vp`.`id_tipo` AS `id_tipo`, `dp`.`id_pres` AS `id_pres`, if(`dp`.`cantidad` < `dp`.`cant`,`dp`.`cant`,`dp`.`cantidad`) AS `cantidad`, `dp`.`comentario` AS `comentario`, `dp`.`fecha_pedido` AS `fecha_pedido`, `dp`.`fecha_envio` AS `fecha_envio`, `dp`.`estado` AS `estado`, `pd`.`nro_pedido` AS `nro_pedido`, `tp`.`id_usu` AS `id_usu`, `vp`.`pro_nom` AS `nombre_prod`, `vp`.`pro_pre` AS `pres_prod`, `vu`.`ape_paterno` AS `ape_paterno`, `vu`.`ape_materno` AS `ape_materno`, `vu`.`nombres` AS `nombres`, `tp`.`estado` AS `estado_pedido` FROM ((((`tm_detalle_pedido` `dp` join `tm_pedido_delivery` `pd` on(`dp`.`id_pedido` = `pd`.`id_pedido`)) join `tm_pedido` `tp` on(`dp`.`id_pedido` = `tp`.`id_pedido`)) join `v_productos` `vp` on(`dp`.`id_pres` = `vp`.`id_pres`)) join `v_usuarios` `vu` on(`tp`.`id_usu` = `vu`.`id_usu`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_cocina_me`
--
DROP TABLE IF EXISTS `v_cocina_me`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_cocina_me`  AS SELECT `dp`.`id_pedido` AS `id_pedido`, `vp`.`id_areap` AS `id_areap`, `vp`.`id_tipo` AS `id_tipo`, `dp`.`id_pres` AS `id_pres`, `dp`.`cantidad` AS `cantidad`, `dp`.`comentario` AS `comentario`, `dp`.`fecha_pedido` AS `fecha_pedido`, `dp`.`fecha_envio` AS `fecha_envio`, `dp`.`estado` AS `estado`, `pm`.`id_mesa` AS `id_mesa`, `pm`.`id_mozo` AS `id_mozo`, `vp`.`pro_nom` AS `nombre_prod`, `vp`.`pro_pre` AS `pres_prod`, `vm`.`nro_mesa` AS `nro_mesa`, `vm`.`desc_salon` AS `desc_salon`, `vu`.`ape_paterno` AS `ape_paterno`, `vu`.`ape_materno` AS `ape_materno`, `vu`.`nombres` AS `nombres`, `tp`.`estado` AS `estado_pedido` FROM (((((`tm_detalle_pedido` `dp` join `tm_pedido_mesa` `pm` on(`dp`.`id_pedido` = `pm`.`id_pedido`)) join `tm_pedido` `tp` on(`dp`.`id_pedido` = `tp`.`id_pedido`)) join `v_productos` `vp` on(`dp`.`id_pres` = `vp`.`id_pres`)) join `v_mesas` `vm` on(`pm`.`id_mesa` = `vm`.`id_mesa`)) join `v_usuarios` `vu` on(`pm`.`id_mozo` = `vu`.`id_usu`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_cocina_mo`
--
DROP TABLE IF EXISTS `v_cocina_mo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_cocina_mo`  AS SELECT `dp`.`id_pedido` AS `id_pedido`, `vp`.`id_areap` AS `id_areap`, `vp`.`id_tipo` AS `id_tipo`, `dp`.`id_pres` AS `id_pres`, if(`dp`.`cantidad` < `dp`.`cant`,`dp`.`cant`,`dp`.`cantidad`) AS `cantidad`, `dp`.`comentario` AS `comentario`, `dp`.`fecha_pedido` AS `fecha_pedido`, `dp`.`fecha_envio` AS `fecha_envio`, `dp`.`estado` AS `estado`, `pm`.`nro_pedido` AS `nro_pedido`, `tp`.`id_usu` AS `id_usu`, `vp`.`pro_nom` AS `nombre_prod`, `vp`.`pro_pre` AS `pres_prod`, `vu`.`ape_paterno` AS `ape_paterno`, `vu`.`ape_materno` AS `ape_materno`, `vu`.`nombres` AS `nombres`, `tp`.`estado` AS `estado_pedido` FROM ((((`tm_detalle_pedido` `dp` join `tm_pedido_llevar` `pm` on(`dp`.`id_pedido` = `pm`.`id_pedido`)) join `tm_pedido` `tp` on(`dp`.`id_pedido` = `tp`.`id_pedido`)) join `v_productos` `vp` on(`dp`.`id_pres` = `vp`.`id_pres`)) join `v_usuarios` `vu` on(`tp`.`id_usu` = `vu`.`id_usu`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_compras`
--
DROP TABLE IF EXISTS `v_compras`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_compras`  AS SELECT `c`.`id_compra` AS `id_compra`, `c`.`id_prov` AS `id_prov`, `c`.`id_tipo_compra` AS `id_tipo_compra`, `c`.`id_tipo_doc` AS `id_tipo_doc`, `c`.`fecha_c` AS `fecha_c`, `c`.`fecha_reg` AS `fecha_r`, `c`.`hora_c` AS `hora_c`, `c`.`serie_doc` AS `serie_doc`, `c`.`num_doc` AS `num_doc`, `c`.`igv` AS `igv`, `c`.`total` AS `total`, `c`.`estado` AS `estado`, `tc`.`descripcion` AS `desc_tc`, `td`.`descripcion` AS `desc_td`, `tp`.`razon_social` AS `desc_prov` FROM (((`tm_compra` `c` join `tm_tipo_compra` `tc` on(`c`.`id_tipo_compra` = `tc`.`id_tipo_compra`)) join `tm_tipo_doc` `td` on(`c`.`id_tipo_doc` = `td`.`id_tipo_doc`)) join `tm_proveedor` `tp` on(`c`.`id_prov` = `tp`.`id_prov`)) WHERE `c`.`id_compra` <> 0 ORDER BY `c`.`id_compra` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_det_delivery`
--
DROP TABLE IF EXISTS `v_det_delivery`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_det_delivery`  AS SELECT `dp`.`id_pedido` AS `id_pedido`, `dp`.`id_pres` AS `id_pres`, if(`dp`.`cantidad` < `dp`.`cant`,`dp`.`cant`,`dp`.`cantidad`) AS `cantidad`, `dp`.`precio` AS `precio`, `dp`.`estado` AS `estado`, `vp`.`pro_nom` AS `nombre_prod`, `vp`.`pro_pre` AS `pres_prod` FROM (((`tm_detalle_pedido` `dp` join `tm_pedido_delivery` `pd` on(`dp`.`id_pedido` = `pd`.`id_pedido`)) join `tm_pedido` `tp` on(`dp`.`id_pedido` = `tp`.`id_pedido`)) join `v_productos` `vp` on(`dp`.`id_pres` = `vp`.`id_pres`)) WHERE `dp`.`estado` <> 'z' ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_det_llevar`
--
DROP TABLE IF EXISTS `v_det_llevar`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_det_llevar`  AS SELECT `dp`.`id_pedido` AS `id_pedido`, `dp`.`id_pres` AS `id_pres`, if(`dp`.`cantidad` < `dp`.`cant`,`dp`.`cant`,`dp`.`cantidad`) AS `cantidad`, `dp`.`precio` AS `precio`, `dp`.`estado` AS `estado`, `vp`.`pro_nom` AS `nombre_prod`, `vp`.`pro_pre` AS `pres_prod` FROM (((`tm_detalle_pedido` `dp` join `tm_pedido_llevar` `pm` on(`dp`.`id_pedido` = `pm`.`id_pedido`)) join `tm_pedido` `tp` on(`dp`.`id_pedido` = `tp`.`id_pedido`)) join `v_productos` `vp` on(`dp`.`id_pres` = `vp`.`id_pres`)) WHERE `dp`.`estado` <> 'z' ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_gastosadm`
--
DROP TABLE IF EXISTS `v_gastosadm`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_gastosadm`  AS SELECT `ga`.`id_ga` AS `id_ga`, `ga`.`id_tipo_gasto` AS `id_tg`, `ga`.`id_per` AS `id_per`, `ga`.`id_usu` AS `id_usu`, `ga`.`id_apc` AS `id_apc`, `ga`.`importe` AS `importe`, `ga`.`responsable` AS `responsable`, `ga`.`motivo` AS `motivo`, `ga`.`fecha_registro` AS `fecha_re`, `ga`.`estado` AS `estado`, `tg`.`descripcion` AS `des_tg`, concat(`tu`.`nombres`,' ',`tu`.`ape_paterno`,' ',`tu`.`ape_materno`) AS `desc_usu`, if(`ga`.`id_per` = '0','',concat(`tus`.`nombres`,' ',`tus`.`ape_paterno`,' ',`tus`.`ape_materno`)) AS `desc_per` FROM (((`tm_gastos_adm` `ga` join `tm_tipo_gasto` `tg` on(`ga`.`id_tipo_gasto` = `tg`.`id_tipo_gasto`)) join `tm_usuario` `tu` on(`ga`.`id_usu` = `tu`.`id_usu`)) left join `tm_usuario` `tus` on(`ga`.`id_per` = `tus`.`id_usu`)) WHERE `ga`.`id_ga` <> 0 ORDER BY `ga`.`id_ga` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_impresiones`
--
DROP TABLE IF EXISTS `v_impresiones`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_impresiones`  AS SELECT `imp`.`id_registro` AS `id_registro`, `imp`.`nombre_impresora` AS `nombre_impresora`, `imp`.`tipo_impresion` AS `tipo_impresion`, concat(`u`.`nombres`,' ',`u`.`ape_materno`,' ',`u`.`ape_paterno`) AS `encargado`, `imp`.`id_usuario` AS `id_usuario`, `imp`.`fecha` AS `fecha`, `imp`.`id_pedido` AS `id_pedido`, `imp`.`status` AS `status`, `imp`.`url` AS `url`, `imp`.`json` AS `json` FROM (`tm_registro_impresiones` `imp` join `tm_usuario` `u` on(`imp`.`id_usuario` = `u`.`id_usu`)) ORDER BY `imp`.`fecha` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_insprod`
--
DROP TABLE IF EXISTS `v_insprod`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_insprod`  AS SELECT 1 AS `id_tipo_ins`, `i`.`id_ins` AS `id_ins`, `i`.`id_med` AS `id_med`, `i`.`id_gru` AS `id_gru`, `i`.`ins_cod` AS `ins_cod`, `i`.`ins_nom` AS `ins_nom`, `i`.`ins_cat` AS `ins_cat`, `i`.`ins_med` AS `ins_med`, 1 AS `ins_rec`, `i`.`ins_cos` AS `ins_cos`, `i`.`ins_sto` AS `ins_sto`, `i`.`ins_est` AS `est_a`, 'a' AS `est_b`, 'a' AS `est_c` FROM `v_insumos` AS `i` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_insumos`
--
DROP TABLE IF EXISTS `v_insumos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_insumos`  AS SELECT `i`.`id_ins` AS `id_ins`, `i`.`id_catg` AS `id_catg`, `i`.`id_med` AS `id_med`, `m`.`grupo` AS `id_gru`, `i`.`cod_ins` AS `ins_cod`, `i`.`nomb_ins` AS `ins_nom`, `i`.`stock_min` AS `ins_sto`, `i`.`cos_uni` AS `ins_cos`, `i`.`estado` AS `ins_est`, `ic`.`descripcion` AS `ins_cat`, `m`.`descripcion` AS `ins_med` FROM ((`tm_insumo` `i` join `tm_insumo_catg` `ic` on(`i`.`id_catg` = `ic`.`id_catg`)) join `tm_tipo_medida` `m` on(`i`.`id_med` = `m`.`id_med`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_inventario`
--
DROP TABLE IF EXISTS `v_inventario`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_inventario`  AS SELECT `e`.`id_tipo_ins` AS `id_tipo_ins`, `e`.`id_ins` AS `id_ins`, ifnull(`e`.`total`,0) AS `ent`, '0' AS `sal` FROM `v_inventario_ent` AS `e` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_inventario_ent`
--
DROP TABLE IF EXISTS `v_inventario_ent`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_inventario_ent`  AS SELECT `tm_inventario`.`id_tipo_ins` AS `id_tipo_ins`, `tm_inventario`.`id_ins` AS `id_ins`, if(`tm_inventario`.`id_tipo_ope` = 1 or `tm_inventario`.`id_tipo_ope` = 3,sum(`tm_inventario`.`cant`),0) AS `total` FROM `tm_inventario` WHERE `tm_inventario`.`id_tipo_ope` <> 2 AND `tm_inventario`.`id_tipo_ope` <> 4 AND `tm_inventario`.`estado` <> 'i' GROUP BY `tm_inventario`.`id_tipo_ins`, `tm_inventario`.`id_ins` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_inventario_sal`
--
DROP TABLE IF EXISTS `v_inventario_sal`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_inventario_sal`  AS SELECT `tm_inventario`.`id_tipo_ins` AS `id_tipo_ins`, `tm_inventario`.`id_ins` AS `id_ins`, if(`tm_inventario`.`id_tipo_ope` = 2 or `tm_inventario`.`id_tipo_ope` = 4,sum(`tm_inventario`.`cant`),0) AS `total` FROM `tm_inventario` WHERE `tm_inventario`.`id_tipo_ope` <> 1 AND `tm_inventario`.`id_tipo_ope` <> 3 AND `tm_inventario`.`estado` <> 'i' GROUP BY `tm_inventario`.`id_tipo_ins`, `tm_inventario`.`id_ins` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_listar_mesas`
--
DROP TABLE IF EXISTS `v_listar_mesas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_listar_mesas`  AS SELECT `vm`.`id_mesa` AS `id_mesa`, `vm`.`id_salon` AS `id_salon`, `vm`.`nro_mesa` AS `nro_mesa`, `vm`.`estado` AS `estado`, `vm`.`desc_salon` AS `desc_salon`, `vo`.`id_pedido` AS `id_pedido`, `vo`.`fecha_pedido` AS `fecha_pedido`, `vo`.`nro_personas` AS `nro_personas` FROM (`v_mesas` `vm` left join `v_pedido_mesa` `vo` on(`vm`.`id_mesa` = `vo`.`id_mesa`)) ORDER BY `vm`.`nro_mesa` ASC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_mesas`
--
DROP TABLE IF EXISTS `v_mesas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_mesas`  AS SELECT `m`.`id_mesa` AS `id_mesa`, `m`.`id_salon` AS `id_salon`, `m`.`nro_mesa` AS `nro_mesa`, `m`.`estado` AS `estado`, `cm`.`descripcion` AS `desc_salon` FROM (`tm_mesa` `m` join `tm_salon` `cm` on(`m`.`id_salon` = `cm`.`id_salon`)) WHERE `m`.`id_mesa` <> 0 AND `cm`.`estado` <> 'i' ORDER BY `m`.`id_mesa` ASC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_pedidos_agrupados`
--
DROP TABLE IF EXISTS `v_pedidos_agrupados`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_pedidos_agrupados`  AS SELECT 1 AS `tipo_atencion`, `v_cocina_me`.`id_pedido` AS `id_pedido`, `v_cocina_me`.`id_areap` AS `id_areap`, `v_cocina_me`.`id_tipo` AS `id_tipo`, `v_cocina_me`.`id_pres` AS `id_pres`, sum(`v_cocina_me`.`cantidad`) AS `cantidad`, `v_cocina_me`.`comentario` AS `comentario`, `v_cocina_me`.`fecha_pedido` AS `fecha_pedido`, `v_cocina_me`.`fecha_envio` AS `fecha_envio`, `v_cocina_me`.`estado` AS `estado`, `v_cocina_me`.`nombre_prod` AS `nombre_prod`, `v_cocina_me`.`pres_prod` AS `pres_prod`, `v_cocina_me`.`nro_mesa` AS `nro_mesa`, `v_cocina_me`.`desc_salon` AS `desc_salon`, `v_cocina_me`.`ape_paterno` AS `ape_paterno`, `v_cocina_me`.`ape_materno` AS `ape_materno`, `v_cocina_me`.`nombres` AS `nombres`, `v_cocina_me`.`estado_pedido` AS `estado_pedido` FROM `v_cocina_me` GROUP BY `v_cocina_me`.`id_pedido`, `v_cocina_me`.`id_pres`, `v_cocina_me`.`fecha_pedido`, `v_cocina_me`.`comentario` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_pedido_delivery`
--
DROP TABLE IF EXISTS `v_pedido_delivery`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_pedido_delivery`  AS SELECT `p`.`id_pedido` AS `id_pedido`, `p`.`id_tipo_pedido` AS `id_tipo_pedido`, `p`.`id_usu` AS `id_usu`, `pd`.`id_repartidor` AS `id_repartidor`, `p`.`fecha_pedido` AS `fecha_pedido`, `p`.`estado` AS `estado_pedido`, `pd`.`tipo_entrega` AS `tipo_entrega`, `pd`.`pedido_programado` AS `pedido_programado`, `pd`.`hora_entrega` AS `hora_entrega`, `pd`.`amortizacion` AS `amortizacion`, `pd`.`tipo_pago` AS `tipo_pago`, `pd`.`paga_con` AS `paga_con`, `pd`.`comision_delivery` AS `comision_delivery`, `pd`.`nro_pedido` AS `nro_pedido`, `pd`.`id_cliente` AS `id_cliente`, `c`.`tipo_cliente` AS `tipo_cliente`, `c`.`dni` AS `dni_cliente`, `c`.`ruc` AS `ruc_cliente`, `pd`.`nombre_cliente` AS `nombre_cliente`, `pd`.`telefono_cliente` AS `telefono_cliente`, `pd`.`direccion_cliente` AS `direccion_cliente`, `pd`.`referencia_cliente` AS `referencia_cliente`, `pd`.`email_cliente` AS `email_cliente`, `r`.`desc_repartidor` AS `desc_repartidor` FROM (((`tm_pedido` `p` join `tm_pedido_delivery` `pd` on(`p`.`id_pedido` = `pd`.`id_pedido`)) join `v_repartidores` `r` on(`pd`.`id_repartidor` = `r`.`id_repartidor`)) join `tm_cliente` `c` on(`pd`.`id_cliente` = `c`.`id_cliente`)) WHERE `p`.`id_pedido` <> 0 ORDER BY `p`.`id_pedido` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_pedido_llevar`
--
DROP TABLE IF EXISTS `v_pedido_llevar`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_pedido_llevar`  AS SELECT `p`.`id_pedido` AS `id_pedido`, `p`.`id_tipo_pedido` AS `id_tipo_pedido`, `p`.`id_usu` AS `id_usu`, `p`.`fecha_pedido` AS `fecha_pedido`, `p`.`estado` AS `estado_pedido`, `pl`.`nro_pedido` AS `nro_pedido`, `pl`.`nomb_cliente` AS `nombre_cliente` FROM (`tm_pedido` `p` join `tm_pedido_llevar` `pl` on(`p`.`id_pedido` = `pl`.`id_pedido`)) WHERE `p`.`id_pedido` <> 0 ORDER BY `p`.`id_pedido` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_pedido_mesa`
--
DROP TABLE IF EXISTS `v_pedido_mesa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_pedido_mesa`  AS SELECT `p`.`id_pedido` AS `id_pedido`, `p`.`id_tipo_pedido` AS `id_tipo_pedido`, `p`.`id_usu` AS `id_usu`, `pm`.`id_mesa` AS `id_mesa`, `p`.`fecha_pedido` AS `fecha_pedido`, `p`.`estado` AS `estado_pedido`, `pm`.`nomb_cliente` AS `nombre_cliente`, `pm`.`nro_personas` AS `nro_personas`, `vm`.`nro_mesa` AS `nro_mesa`, `vm`.`desc_salon` AS `desc_salon`, `vm`.`estado` AS `estado_mesa`, concat(`u`.`nombres`,' ',`u`.`ape_paterno`) AS `nombre_mozo` FROM (((`tm_pedido` `p` join `tm_pedido_mesa` `pm` on(`p`.`id_pedido` = `pm`.`id_pedido`)) join `v_mesas` `vm` on(`pm`.`id_mesa` = `vm`.`id_mesa`)) join `tm_usuario` `u` on(`pm`.`id_mozo` = `u`.`id_usu`)) WHERE `p`.`id_pedido` <> 0 AND `p`.`estado` = 'a' ORDER BY `p`.`id_pedido` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_pedido_portero`
--
DROP TABLE IF EXISTS `v_pedido_portero`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_pedido_portero`  AS SELECT `p`.`id_pedido` AS `id_pedido`, `p`.`id_tipo_pedido` AS `id_tipo_pedido`, `p`.`id_usu` AS `id_usu`, `p`.`fecha_pedido` AS `fecha_pedido`, `p`.`estado` AS `estado_pedido`, `pl`.`personas` AS `personas` FROM (`tm_pedido` `p` join `tm_pedido_portero` `pl` on(`p`.`id_pedido` = `pl`.`id_pedido`)) WHERE `p`.`id_pedido` <> 0 ORDER BY `p`.`id_pedido` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_productos`
--
DROP TABLE IF EXISTS `v_productos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_productos`  AS SELECT `pp`.`id_pres` AS `id_pres`, `p`.`combo` AS `is_combo`, `pp`.`id_prod` AS `id_prod`, `p`.`id_tipo` AS `id_tipo`, `p`.`id_catg` AS `id_catg`, `p`.`id_areap` AS `id_areap`, `cp`.`descripcion` AS `pro_cat`, `pp`.`cod_prod` AS `pro_cod`, `p`.`nombre` AS `pro_nom`, `pp`.`presentacion` AS `pro_pre`, ifnull(`pp`.`descripcion`,'') AS `pro_des`, `pp`.`precio` AS `pro_cos`, `pp`.`precio_delivery` AS `pro_cos_del`, `pp`.`receta` AS `pro_rec`, `pp`.`stock_min` AS `pro_sto`, `pp`.`impuesto` AS `pro_imp`, `pp`.`margen` AS `pro_mar`, `pp`.`igv` AS `pro_igv`, `pp`.`imagen` AS `pro_img`, `cp`.`delivery` AS `del_a`, `p`.`delivery` AS `del_b`, `pp`.`delivery` AS `del_c`, `cp`.`estado` AS `est_a`, `p`.`estado` AS `est_b`, `pp`.`estado` AS `est_c` FROM ((`tm_producto_pres` `pp` join `tm_producto` `p` on(`pp`.`id_prod` = `p`.`id_prod`)) join `tm_producto_catg` `cp` on(`p`.`id_catg` = `cp`.`id_catg`)) WHERE `pp`.`id_pres` <> 0 ORDER BY `pp`.`id_pres` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_recetas_producto`
--
DROP TABLE IF EXISTS `v_recetas_producto`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_recetas_producto`  AS SELECT `rec`.`id_receta` AS `id_receta`, `rec`.`id_catg_receta` AS `id_catg_receta`, `rec`.`nombre` AS `nombre`, `rec`.`fecha_creacion` AS `fecha_creacion`, `rec`.`estado` AS `estado`, concat(`pr`.`nombre`,' ',`pres`.`presentacion`) AS `producto`, `catg`.`nombre` AS `nombre_catg` FROM (((`tm_recetas` `rec` join `tm_producto_pres` `pres` on(`rec`.`id_pres` = `pres`.`id_pres`)) join `tm_producto` `pr` on(`pres`.`id_prod` = `pr`.`id_prod`)) join `tm_recetas_catg` `catg` on(`rec`.`id_catg_receta` = `catg`.`id_catg`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_repartidores`
--
DROP TABLE IF EXISTS `v_repartidores`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_repartidores`  AS SELECT `tm_usuario`.`id_usu` AS `id_repartidor`, concat(`tm_usuario`.`nombres`,' ',`tm_usuario`.`ape_paterno`,' ',`tm_usuario`.`ape_materno`) AS `desc_repartidor` FROM `tm_usuario` WHERE `tm_usuario`.`id_rol` = 6 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_stock`
--
DROP TABLE IF EXISTS `v_stock`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_stock`  AS   (select `a`.`id_tipo_ins` AS `id_tipo_ins`,`a`.`id_ins` AS `id_ins`,sum(`a`.`ent`) AS `ent`,sum(`a`.`sal`) AS `sal`,`b`.`est_a` AS `est_a`,`b`.`est_b` AS `est_b`,if(`a`.`ent` - `a`.`sal` > `b`.`ins_sto`,1,0) AS `debajo_stock` from (`v_inventario` `a` join `v_insprod` `b` on(`a`.`id_tipo_ins` = `b`.`id_tipo_ins` and `a`.`id_ins` = `b`.`id_ins`)) where `b`.`est_a` = 'a' and `b`.`est_b` = 'a' and `b`.`ins_rec` = 1 group by `a`.`id_tipo_ins`,`a`.`id_ins`)  ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_usuarios`
--
DROP TABLE IF EXISTS `v_usuarios`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_usuarios`  AS SELECT `u`.`id_usu` AS `id_usu`, `u`.`id_rol` AS `id_rol`, `u`.`id_areap` AS `id_areap`, `u`.`dni` AS `dni`, `u`.`ape_paterno` AS `ape_paterno`, `u`.`ape_materno` AS `ape_materno`, `u`.`nombres` AS `nombres`, `u`.`email` AS `email`, `u`.`usuario` AS `usuario`, `u`.`contrasena` AS `contrasena`, `u`.`estado` AS `estado`, `u`.`imagen` AS `imagen`, `r`.`descripcion` AS `desc_r`, `p`.`nombre` AS `desc_ap` FROM ((`tm_usuario` `u` join `tm_rol` `r` on(`u`.`id_rol` = `r`.`id_rol`)) left join `tm_area_prod` `p` on(`u`.`id_areap` = `p`.`id_areap`)) WHERE `u`.`id_usu` <> 0 ORDER BY `u`.`id_usu` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_ventas_con`
--
DROP TABLE IF EXISTS `v_ventas_con`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u407783947_restobar`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_ventas_con`  AS SELECT `v`.`id_venta` AS `id_ven`, `v`.`id_pedido` AS `id_ped`, `v`.`id_tipo_pedido` AS `id_tped`, `v`.`id_cliente` AS `id_cli`, `v`.`id_tipo_doc` AS `id_tdoc`, `v`.`id_tipo_pago` AS `id_tpag`, `v`.`id_usu` AS `id_usu`, `v`.`id_apc` AS `id_apc`, `v`.`serie_doc` AS `ser_doc`, `v`.`nro_doc` AS `nro_doc`, `v`.`pago_efe` AS `pago_efe`, `v`.`pago_efe_none` AS `pago_efe_none`, `v`.`pago_tar` AS `pago_tar`, `v`.`descuento_monto` AS `desc_monto`, `v`.`descuento_tipo` AS `desc_tipo`, `v`.`descuento_personal` AS `desc_personal`, `v`.`descuento_motivo` AS `desc_motivo`, `v`.`comision_tarjeta` AS `comis_tar`, `v`.`comision_delivery` AS `comis_del`, `v`.`igv` AS `igv`, `v`.`total` AS `total`, `v`.`codigo_operacion` AS `codigo_operacion`, `v`.`fecha_venta` AS `fec_ven`, `v`.`estado` AS `estado`, `v`.`enviado_sunat` AS `enviado_sunat`, `v`.`code_respuesta_sunat` AS `code_respuesta_sunat`, `v`.`descripcion_sunat_cdr` AS `descripcion_sunat_cdr`, `v`.`name_file_sunat` AS `name_file_sunat`, `v`.`hash_cdr` AS `hash_cdr`, `v`.`hash_cpe` AS `hash_cpe`, `v`.`fecha_vencimiento` AS `fecha_vencimiento`, `td`.`descripcion` AS `desc_td`, `tp`.`descripcion` AS `desc_tp`, concat(`tu`.`ape_paterno`,' ',`tu`.`ape_materno`,' ',`tu`.`nombres`) AS `desc_usu` FROM (((`tm_venta` `v` join `tm_tipo_doc` `td` on(`v`.`id_tipo_doc` = `td`.`id_tipo_doc`)) join `tm_tipo_pago` `tp` on(`v`.`id_tipo_pago` = `tp`.`id_tipo_pago`)) join `tm_usuario` `tu` on(`v`.`id_usu` = `tu`.`id_usu`)) WHERE `v`.`id_venta` <> 0 ORDER BY `v`.`id_venta` DESC ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comunicacion_baja`
--
ALTER TABLE `comunicacion_baja`
  ADD PRIMARY KEY (`id_comunicacion`);

--
-- Indices de la tabla `resumen_diario`
--
ALTER TABLE `resumen_diario`
  ADD PRIMARY KEY (`id_resumen`);

--
-- Indices de la tabla `resumen_diario_detalle`
--
ALTER TABLE `resumen_diario_detalle`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `FK_RDD_RES` (`id_resumen`),
  ADD KEY `FK_RDD_VEN` (`id_venta`);

--
-- Indices de la tabla `sa_asistencia_registro`
--
ALTER TABLE `sa_asistencia_registro`
  ADD PRIMARY KEY (`id_registro`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `sa_configuracion`
--
ALTER TABLE `sa_configuracion`
  ADD PRIMARY KEY (`id_conf`),
  ADD KEY `id_impresora` (`id_impresora`),
  ADD KEY `id_impresora_2` (`id_impresora`);

--
-- Indices de la tabla `sa_usuario_asistencia`
--
ALTER TABLE `sa_usuario_asistencia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `tm_almacen`
--
ALTER TABLE `tm_almacen`
  ADD PRIMARY KEY (`id_alm`);

--
-- Indices de la tabla `tm_aper_cierre`
--
ALTER TABLE `tm_aper_cierre`
  ADD PRIMARY KEY (`id_apc`),
  ADD KEY `FK_ac_caja` (`id_caja`),
  ADD KEY `FK_ac_turno` (`id_turno`),
  ADD KEY `FK_ac_usu` (`id_usu`);

--
-- Indices de la tabla `tm_areas_rel`
--
ALTER TABLE `tm_areas_rel`
  ADD PRIMARY KEY (`id_rel`),
  ADD KEY `id_usu` (`id_usu`),
  ADD KEY `id_salon` (`id_salon`);

--
-- Indices de la tabla `tm_area_prod`
--
ALTER TABLE `tm_area_prod`
  ADD PRIMARY KEY (`id_areap`),
  ADD KEY `FK_ap_alm` (`id_imp`);

--
-- Indices de la tabla `tm_caja`
--
ALTER TABLE `tm_caja`
  ADD PRIMARY KEY (`id_caja`);

--
-- Indices de la tabla `tm_cliente`
--
ALTER TABLE `tm_cliente`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `tm_compra`
--
ALTER TABLE `tm_compra`
  ADD PRIMARY KEY (`id_compra`),
  ADD KEY `FK_comp_prov` (`id_prov`),
  ADD KEY `FK_comp_tipoc` (`id_tipo_compra`),
  ADD KEY `FK_comp_tipod` (`id_tipo_doc`),
  ADD KEY `FK_comp_usu` (`id_usu`);

--
-- Indices de la tabla `tm_compra_credito`
--
ALTER TABLE `tm_compra_credito`
  ADD PRIMARY KEY (`id_credito`),
  ADD KEY `FK_CC_ID_COMPRA_idx` (`id_compra`);

--
-- Indices de la tabla `tm_compra_detalle`
--
ALTER TABLE `tm_compra_detalle`
  ADD KEY `FK_CDET_COM` (`id_compra`);

--
-- Indices de la tabla `tm_configuracion`
--
ALTER TABLE `tm_configuracion`
  ADD PRIMARY KEY (`id_cfg`);

--
-- Indices de la tabla `tm_credito_detalle`
--
ALTER TABLE `tm_credito_detalle`
  ADD KEY `FK_cred_usu` (`id_usu`),
  ADD KEY `FK_CRED_CRED` (`id_credito`);

--
-- Indices de la tabla `tm_detalle_combo`
--
ALTER TABLE `tm_detalle_combo`
  ADD KEY `id_pres` (`id_pres`),
  ADD KEY `id_pres_2` (`id_pres`),
  ADD KEY `id_ing` (`id_ing`);

--
-- Indices de la tabla `tm_detalle_pedido`
--
ALTER TABLE `tm_detalle_pedido`
  ADD KEY `FK_DPED_PRES` (`id_pres`),
  ADD KEY `FK_DPED_PED` (`id_pedido`),
  ADD KEY `FK_DPED_USU` (`id_usu`);

--
-- Indices de la tabla `tm_detalle_venta`
--
ALTER TABLE `tm_detalle_venta`
  ADD KEY `FK_DVEN_VEN` (`id_venta`),
  ADD KEY `FK_DVEN_PRES` (`id_prod`);

--
-- Indices de la tabla `tm_empresa`
--
ALTER TABLE `tm_empresa`
  ADD PRIMARY KEY (`id_de`);

--
-- Indices de la tabla `tm_gastos_adm`
--
ALTER TABLE `tm_gastos_adm`
  ADD PRIMARY KEY (`id_ga`),
  ADD KEY `FK_gasto_tg` (`id_tipo_gasto`),
  ADD KEY `FK_EADM_APC` (`id_apc`),
  ADD KEY `FK_EADM_USU` (`id_usu`);

--
-- Indices de la tabla `tm_impresora`
--
ALTER TABLE `tm_impresora`
  ADD PRIMARY KEY (`id_imp`);

--
-- Indices de la tabla `tm_ingresos_adm`
--
ALTER TABLE `tm_ingresos_adm`
  ADD PRIMARY KEY (`id_ing`),
  ADD KEY `FK_IADM_USU` (`id_usu`),
  ADD KEY `FK_IADM_APC` (`id_apc`);

--
-- Indices de la tabla `tm_insumo`
--
ALTER TABLE `tm_insumo`
  ADD PRIMARY KEY (`id_ins`),
  ADD KEY `FK_ins_catg` (`id_catg`),
  ADD KEY `FK_ins_med` (`id_med`);

--
-- Indices de la tabla `tm_insumo_catg`
--
ALTER TABLE `tm_insumo_catg`
  ADD PRIMARY KEY (`id_catg`);

--
-- Indices de la tabla `tm_inventario`
--
ALTER TABLE `tm_inventario`
  ADD PRIMARY KEY (`id_inv`);

--
-- Indices de la tabla `tm_inventario_entsal`
--
ALTER TABLE `tm_inventario_entsal`
  ADD PRIMARY KEY (`id_es`),
  ADD KEY `FK_INVES_USU` (`id_usu`),
  ADD KEY `FK_INVES_RESP` (`id_responsable`);

--
-- Indices de la tabla `tm_margen_venta`
--
ALTER TABLE `tm_margen_venta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tm_mesa`
--
ALTER TABLE `tm_mesa`
  ADD PRIMARY KEY (`id_mesa`),
  ADD KEY `FKM_IDCATG_idx` (`id_salon`);

--
-- Indices de la tabla `tm_pago`
--
ALTER TABLE `tm_pago`
  ADD PRIMARY KEY (`id_pago`);

--
-- Indices de la tabla `tm_pedido`
--
ALTER TABLE `tm_pedido`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `FK_ped_tp` (`id_tipo_pedido`),
  ADD KEY `FK_ped_usu` (`id_usu`),
  ADD KEY `FK_ped_apc` (`id_apc`);

--
-- Indices de la tabla `tm_pedido_delivery`
--
ALTER TABLE `tm_pedido_delivery`
  ADD KEY `FK_peddel_ped` (`id_pedido`),
  ADD KEY `FK_peddel_cli` (`id_cliente`);

--
-- Indices de la tabla `tm_pedido_llevar`
--
ALTER TABLE `tm_pedido_llevar`
  ADD KEY `FK_pedlle_ped` (`id_pedido`);

--
-- Indices de la tabla `tm_pedido_mesa`
--
ALTER TABLE `tm_pedido_mesa`
  ADD KEY `FK_pedme_ped` (`id_pedido`),
  ADD KEY `FK_pedme_mesa` (`id_mesa`),
  ADD KEY `FK_pedme_mozo` (`id_mozo`);

--
-- Indices de la tabla `tm_pedido_portero`
--
ALTER TABLE `tm_pedido_portero`
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_cliente` (`id_usuario`);

--
-- Indices de la tabla `tm_precios`
--
ALTER TABLE `tm_precios`
  ADD PRIMARY KEY (`id_precio`),
  ADD KEY `id_pres` (`id_pres`);

--
-- Indices de la tabla `tm_preparados`
--
ALTER TABLE `tm_preparados`
  ADD PRIMARY KEY (`id_preparado`),
  ADD KEY `id_catg` (`id_catg`);

--
-- Indices de la tabla `tm_preparados_catg`
--
ALTER TABLE `tm_preparados_catg`
  ADD PRIMARY KEY (`id_catg`);

--
-- Indices de la tabla `tm_producto`
--
ALTER TABLE `tm_producto`
  ADD PRIMARY KEY (`id_prod`),
  ADD KEY `FK_prod_catg` (`id_catg`),
  ADD KEY `FK_prod_area` (`id_areap`);

--
-- Indices de la tabla `tm_producto_catg`
--
ALTER TABLE `tm_producto_catg`
  ADD PRIMARY KEY (`id_catg`);

--
-- Indices de la tabla `tm_producto_ingr`
--
ALTER TABLE `tm_producto_ingr`
  ADD PRIMARY KEY (`id_pi`),
  ADD KEY `FK_PING_PRES` (`id_pres`),
  ADD KEY `FK_PING_INS` (`id_ins`),
  ADD KEY `FK_PING_MED` (`id_med`);

--
-- Indices de la tabla `tm_producto_pres`
--
ALTER TABLE `tm_producto_pres`
  ADD PRIMARY KEY (`id_pres`),
  ADD KEY `FK_PROP_PROD` (`id_prod`);

--
-- Indices de la tabla `tm_proveedor`
--
ALTER TABLE `tm_proveedor`
  ADD PRIMARY KEY (`id_prov`);

--
-- Indices de la tabla `tm_recetas`
--
ALTER TABLE `tm_recetas`
  ADD PRIMARY KEY (`id_receta`),
  ADD KEY `id_catg_receta` (`id_catg_receta`),
  ADD KEY `id_pres` (`id_pres`);

--
-- Indices de la tabla `tm_recetas_catg`
--
ALTER TABLE `tm_recetas_catg`
  ADD PRIMARY KEY (`id_catg`);

--
-- Indices de la tabla `tm_receta_ingrediente`
--
ALTER TABLE `tm_receta_ingrediente`
  ADD PRIMARY KEY (`id_ingrediente`),
  ADD KEY `id_receta` (`id_receta`);

--
-- Indices de la tabla `tm_registro_impresiones`
--
ALTER TABLE `tm_registro_impresiones`
  ADD PRIMARY KEY (`id_registro`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `tm_repartidor`
--
ALTER TABLE `tm_repartidor`
  ADD PRIMARY KEY (`id_repartidor`);

--
-- Indices de la tabla `tm_rol`
--
ALTER TABLE `tm_rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `tm_salon`
--
ALTER TABLE `tm_salon`
  ADD PRIMARY KEY (`id_salon`);

--
-- Indices de la tabla `tm_tipo_compra`
--
ALTER TABLE `tm_tipo_compra`
  ADD PRIMARY KEY (`id_tipo_compra`);

--
-- Indices de la tabla `tm_tipo_doc`
--
ALTER TABLE `tm_tipo_doc`
  ADD PRIMARY KEY (`id_tipo_doc`);

--
-- Indices de la tabla `tm_tipo_gasto`
--
ALTER TABLE `tm_tipo_gasto`
  ADD PRIMARY KEY (`id_tipo_gasto`);

--
-- Indices de la tabla `tm_tipo_medida`
--
ALTER TABLE `tm_tipo_medida`
  ADD PRIMARY KEY (`id_med`);

--
-- Indices de la tabla `tm_tipo_pago`
--
ALTER TABLE `tm_tipo_pago`
  ADD PRIMARY KEY (`id_tipo_pago`);

--
-- Indices de la tabla `tm_tipo_pedido`
--
ALTER TABLE `tm_tipo_pedido`
  ADD PRIMARY KEY (`id_tipo_pedido`);

--
-- Indices de la tabla `tm_tipo_venta`
--
ALTER TABLE `tm_tipo_venta`
  ADD PRIMARY KEY (`id_tipo_venta`);

--
-- Indices de la tabla `tm_turno`
--
ALTER TABLE `tm_turno`
  ADD PRIMARY KEY (`id_turno`);

--
-- Indices de la tabla `tm_usuario`
--
ALTER TABLE `tm_usuario`
  ADD PRIMARY KEY (`id_usu`),
  ADD KEY `FKU_IDROL_idx` (`id_rol`);

--
-- Indices de la tabla `tm_venta`
--
ALTER TABLE `tm_venta`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `FK_venta_cli` (`id_cliente`),
  ADD KEY `FK_venta_td` (`id_tipo_doc`),
  ADD KEY `FK_venta_tp` (`id_tipo_pago`),
  ADD KEY `FK_venta_usu` (`id_usu`),
  ADD KEY `FK_venta_apc` (`id_apc`),
  ADD KEY `FK_venta_tpe` (`id_tipo_pedido`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comunicacion_baja`
--
ALTER TABLE `comunicacion_baja`
  MODIFY `id_comunicacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `resumen_diario`
--
ALTER TABLE `resumen_diario`
  MODIFY `id_resumen` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `resumen_diario_detalle`
--
ALTER TABLE `resumen_diario_detalle`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sa_asistencia_registro`
--
ALTER TABLE `sa_asistencia_registro`
  MODIFY `id_registro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de la tabla `sa_configuracion`
--
ALTER TABLE `sa_configuracion`
  MODIFY `id_conf` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sa_usuario_asistencia`
--
ALTER TABLE `sa_usuario_asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `tm_almacen`
--
ALTER TABLE `tm_almacen`
  MODIFY `id_alm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tm_aper_cierre`
--
ALTER TABLE `tm_aper_cierre`
  MODIFY `id_apc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tm_areas_rel`
--
ALTER TABLE `tm_areas_rel`
  MODIFY `id_rel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tm_area_prod`
--
ALTER TABLE `tm_area_prod`
  MODIFY `id_areap` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tm_caja`
--
ALTER TABLE `tm_caja`
  MODIFY `id_caja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tm_cliente`
--
ALTER TABLE `tm_cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT de la tabla `tm_compra`
--
ALTER TABLE `tm_compra`
  MODIFY `id_compra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tm_compra_credito`
--
ALTER TABLE `tm_compra_credito`
  MODIFY `id_credito` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tm_configuracion`
--
ALTER TABLE `tm_configuracion`
  MODIFY `id_cfg` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tm_empresa`
--
ALTER TABLE `tm_empresa`
  MODIFY `id_de` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tm_gastos_adm`
--
ALTER TABLE `tm_gastos_adm`
  MODIFY `id_ga` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tm_impresora`
--
ALTER TABLE `tm_impresora`
  MODIFY `id_imp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tm_ingresos_adm`
--
ALTER TABLE `tm_ingresos_adm`
  MODIFY `id_ing` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tm_insumo`
--
ALTER TABLE `tm_insumo`
  MODIFY `id_ins` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tm_insumo_catg`
--
ALTER TABLE `tm_insumo_catg`
  MODIFY `id_catg` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tm_inventario`
--
ALTER TABLE `tm_inventario`
  MODIFY `id_inv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tm_inventario_entsal`
--
ALTER TABLE `tm_inventario_entsal`
  MODIFY `id_es` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tm_margen_venta`
--
ALTER TABLE `tm_margen_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tm_mesa`
--
ALTER TABLE `tm_mesa`
  MODIFY `id_mesa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT de la tabla `tm_pago`
--
ALTER TABLE `tm_pago`
  MODIFY `id_pago` int(2) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tm_pedido`
--
ALTER TABLE `tm_pedido`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tm_precios`
--
ALTER TABLE `tm_precios`
  MODIFY `id_precio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tm_preparados`
--
ALTER TABLE `tm_preparados`
  MODIFY `id_preparado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `tm_preparados_catg`
--
ALTER TABLE `tm_preparados_catg`
  MODIFY `id_catg` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tm_producto`
--
ALTER TABLE `tm_producto`
  MODIFY `id_prod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=292;

--
-- AUTO_INCREMENT de la tabla `tm_producto_catg`
--
ALTER TABLE `tm_producto_catg`
  MODIFY `id_catg` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `tm_producto_ingr`
--
ALTER TABLE `tm_producto_ingr`
  MODIFY `id_pi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tm_producto_pres`
--
ALTER TABLE `tm_producto_pres`
  MODIFY `id_pres` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=318;

--
-- AUTO_INCREMENT de la tabla `tm_proveedor`
--
ALTER TABLE `tm_proveedor`
  MODIFY `id_prov` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `tm_recetas`
--
ALTER TABLE `tm_recetas`
  MODIFY `id_receta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `tm_recetas_catg`
--
ALTER TABLE `tm_recetas_catg`
  MODIFY `id_catg` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tm_receta_ingrediente`
--
ALTER TABLE `tm_receta_ingrediente`
  MODIFY `id_ingrediente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `tm_registro_impresiones`
--
ALTER TABLE `tm_registro_impresiones`
  MODIFY `id_registro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tm_repartidor`
--
ALTER TABLE `tm_repartidor`
  MODIFY `id_repartidor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4446;

--
-- AUTO_INCREMENT de la tabla `tm_rol`
--
ALTER TABLE `tm_rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tm_salon`
--
ALTER TABLE `tm_salon`
  MODIFY `id_salon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tm_tipo_compra`
--
ALTER TABLE `tm_tipo_compra`
  MODIFY `id_tipo_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tm_tipo_doc`
--
ALTER TABLE `tm_tipo_doc`
  MODIFY `id_tipo_doc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tm_tipo_gasto`
--
ALTER TABLE `tm_tipo_gasto`
  MODIFY `id_tipo_gasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tm_tipo_medida`
--
ALTER TABLE `tm_tipo_medida`
  MODIFY `id_med` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tm_tipo_pago`
--
ALTER TABLE `tm_tipo_pago`
  MODIFY `id_tipo_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `tm_tipo_pedido`
--
ALTER TABLE `tm_tipo_pedido`
  MODIFY `id_tipo_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tm_tipo_venta`
--
ALTER TABLE `tm_tipo_venta`
  MODIFY `id_tipo_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tm_turno`
--
ALTER TABLE `tm_turno`
  MODIFY `id_turno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tm_usuario`
--
ALTER TABLE `tm_usuario`
  MODIFY `id_usu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT de la tabla `tm_venta`
--
ALTER TABLE `tm_venta`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5305;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `resumen_diario_detalle`
--
ALTER TABLE `resumen_diario_detalle`
  ADD CONSTRAINT `FK_RDD_RES` FOREIGN KEY (`id_resumen`) REFERENCES `resumen_diario` (`id_resumen`),
  ADD CONSTRAINT `FK_RDD_VEN` FOREIGN KEY (`id_venta`) REFERENCES `tm_venta` (`id_venta`);

--
-- Filtros para la tabla `sa_asistencia_registro`
--
ALTER TABLE `sa_asistencia_registro`
  ADD CONSTRAINT `sa_asistencia_registro_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `tm_usuario` (`id_usu`);

--
-- Filtros para la tabla `sa_configuracion`
--
ALTER TABLE `sa_configuracion`
  ADD CONSTRAINT `sa_configuracion_ibfk_1` FOREIGN KEY (`id_impresora`) REFERENCES `tm_impresora` (`id_imp`);

--
-- Filtros para la tabla `sa_usuario_asistencia`
--
ALTER TABLE `sa_usuario_asistencia`
  ADD CONSTRAINT `sa_usuario_asistencia_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `tm_usuario` (`id_usu`);

--
-- Filtros para la tabla `tm_detalle_combo`
--
ALTER TABLE `tm_detalle_combo`
  ADD CONSTRAINT `tm_detalle_combo_ibfk_1` FOREIGN KEY (`id_pres`) REFERENCES `tm_producto_pres` (`id_pres`),
  ADD CONSTRAINT `tm_detalle_combo_ibfk_2` FOREIGN KEY (`id_ing`) REFERENCES `tm_producto_pres` (`id_pres`);

--
-- Filtros para la tabla `tm_preparados`
--
ALTER TABLE `tm_preparados`
  ADD CONSTRAINT `tm_preparados_ibfk_1` FOREIGN KEY (`id_catg`) REFERENCES `tm_preparados_catg` (`id_catg`);

--
-- Filtros para la tabla `tm_recetas`
--
ALTER TABLE `tm_recetas`
  ADD CONSTRAINT `tm_recetas_ibfk_1` FOREIGN KEY (`id_catg_receta`) REFERENCES `tm_recetas_catg` (`id_catg`),
  ADD CONSTRAINT `tm_recetas_ibfk_2` FOREIGN KEY (`id_pres`) REFERENCES `tm_producto_pres` (`id_pres`);

--
-- Filtros para la tabla `tm_receta_ingrediente`
--
ALTER TABLE `tm_receta_ingrediente`
  ADD CONSTRAINT `tm_receta_ingrediente_ibfk_1` FOREIGN KEY (`id_receta`) REFERENCES `tm_recetas` (`id_receta`);

--
-- Filtros para la tabla `tm_registro_impresiones`
--
ALTER TABLE `tm_registro_impresiones`
  ADD CONSTRAINT `tm_registro_impresiones_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `tm_pedido` (`id_pedido`),
  ADD CONSTRAINT `tm_registro_impresiones_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `tm_usuario` (`id_usu`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
