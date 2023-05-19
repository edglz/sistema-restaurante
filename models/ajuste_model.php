<?php Session::init(); ?>
<?php

class Ajuste_Model extends Model
{
	public function __construct()
	{
		parent::__construct();
	}

    public function AreaProduccion()
    {
        try
        {      
            return $this->db->selectAll('SELECT * FROM tm_area_prod WHERE estado = "a"');
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }
    public function Mesas(){
        try
        {      
            return $this->db->selectAll('SELECT * FROM v_mesas WHERE estado = "a"');
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }
    public function Rol()
    {
        try
        {      
            return $this->db->selectAll('SELECT * FROM tm_rol WHERE id_rol <> 1');
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function UnidadMedida()
    {
        try
        {      
            return $this->db->selectAll('SELECT * FROM tm_tipo_medida');
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function Impresora()
    {
        try
        {      
            return $this->db->selectAll('SELECT * FROM tm_impresora WHERE estado = "a"');
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    /* INICIO MODULO EMPRESA */

	public function datosempresa_data()
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

    public function datosempresa_crud($data)
    {
        try 
        { 
            if( !empty( $_FILES['imagen']['name'] ) ){
            switch ($_FILES['imagen']['type']) 
            { 
                case 'image/jpeg': 
                $ext = "jpg"; 
                break;
                case 'image/gif': 
                $ext = "gif"; 
                break; 
                case 'image/png': 
                $ext = "png"; 
                break;
            }
            $imagen = 'logoprint.'.$ext;
            move_uploaded_file ($_FILES['imagen']['tmp_name'], 'public/images/'.$imagen);
            $data['logo'] =  $imagen;
            } else {
                $imagen = $data['imagen'];

            }
            // // subir certifico pfx 
            // if( !empty( $_FILES['subir_archivo']['name'] ) ){
            //     // api_fact/UBL21/archivos_xml_sunat/certificados/
            //     // beta
            //     // produccion
            //     $directoriobeta         = 'api_fact/UBL21/archivos_xml_sunat/certificados/beta/'.$data['ruc'];
            //     $directorioproduccion   = 'api_fact/UBL21/archivos_xml_sunat/certificados/produccion/'.$data['ruc'];
            //     if (!file_exists($directorioproduccion)) {
            //         mkdir($directorioproduccion, 0777, true);
            //         $urlsproduccion        = 'api_fact/UBL21/archivos_xml_sunat/certificados/produccion/'.$data['ruc'].'/'.$data['ruc'].'.pfx';
            //         move_uploaded_file($_FILES['subir_archivo']['tmp_name'], $urlsproduccion);
                    
            //         $cpeproduccion ='api_fact/UBL21/archivos_xml_sunat/cpe_xml/produccion/'.$data['ruc'];
            //         mkdir($cpeproduccion, 0777, true);
            //     } 
            //     if (!file_exists($directoriobeta)) {
            //         mkdir($directoriobeta, 0777, true);
            //         $urlsbeta         = 'api_fact/UBL21/archivos_xml_sunat/certificados/beta/'.$data['ruc'].'/'.$data['ruc'].'.pfx';
            //         move_uploaded_file($_FILES['subir_archivo']['tmp_name'], $urlsbeta);
            //         // \cpe_xml
            //         $cpebeta ='api_fact/UBL21/archivos_xml_sunat/cpe_xml/beta/'.$data['ruc'];
            //         mkdir($cpebeta, 0777, true);
            //     }            

            // } else {

            // }
                
            if($data['usuid'] == 1){
                $sql = "UPDATE tm_empresa SET ruc = ?,razon_social  = ?, nombre_comercial = ?, direccion_comercial = ?, direccion_fiscal = ?, celular = ?, ubigeo = ?, departamento = ?, provincia = ?, distrito = ?, usuariosol = ?, clavesol = ?, clavecertificado = ?, logo = ?, sunat = ?, modo = ?";
                $this->db->prepare($sql)->execute(array(
                    $data['ruc'],
                    $data['razon_social'],
                    $data['nombre_comercial'],
                    $data['direccion_comercial'],
                    $data['direccion_fiscal'],
                    $data['celular'],
                    $data['ubigeo'],
                    $data['departamento'],
                    $data['provincia'],
                    $data['distrito'],
                    $data['usuariosol'],
                    $data['clavesol'],
                    $data['clavecertificado'],
                    $imagen,
                    $data['sunat'],
                    $data['modo']
                ));
                Session::set('sunat', $data['sunat']);
                Session::set('modo', $data['modo']);
            } else {
                $sql = "UPDATE tm_empresa SET ruc = ?,razon_social  = ?, nombre_comercial = ?, direccion_comercial = ?, celular = ?";
                $this->db->prepare($sql)->execute(array(
                    $data['ruc'],
                    $data['razon_social'],
                    $data['nombre_comercial'],
                    $data['direccion_comercial'],
                    $data['celular']
                ));
            }

        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function tipodoc_list()
    {
        try
        {      
            $stm = $this->db->prepare("SELECT * FROM tm_tipo_doc");
            $stm->execute();            
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

    public function tipodoc_crud($data)
    {
        try 
        {
            $sql = "UPDATE tm_tipo_doc SET serie = ?, numero = ?, estado = ? WHERE id_tipo_doc = ?";
            $this->db->prepare($sql)->execute(array($data['serie'],$data['numero'],$data['estado'],$data['id_tipo_doc']));
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function usuario_list()
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM v_usuarios WHERE id_rol <> 1 AND id_usu <> 54");
            $stm->execute();
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

    public function usuario_data($id)
    {
        return $this->db->selectOne('SELECT * FROM v_usuarios WHERE id_usu = :id_usu', 
            array('id_usu' => $id));
    }

    public function usuario_crud_create($data)
    {
        try 
        {
            if( !empty( $_FILES['imagen']['name'] ) ){
                switch ($_FILES['imagen']['type']) 
                { 
                    case 'image/jpeg': 
                    $ext = "jpg"; 
                    break;
                    case 'image/gif': 
                    $ext = "gif"; 
                    break; 
                    case 'image/png': 
                    $ext = "png"; 
                    break;
                    case 'application/pdf':
                    $ext = "pdf";
                    break;
                }
                $imagen = date('ymdhis').'.'.$ext;
                move_uploaded_file ($_FILES['imagen']['tmp_name'], 'public/images/
                
                s/'.$imagen);
            } else {
                $imagen = $data['imagen'];
            }

            $area = (isset($data['id_areap'])) ? $data['id_areap'] : 0;

            $consulta = "call usp_configUsuario( :flag, @a, :id_rol, :id_areap, :id_mesa, :dni, :ape_paterno, :ape_materno, :nombres, :email, :usuario, :contrasena, :imagen);";
            $arrayParam =  array(
                ':flag' => 1,
                ':id_rol' => $data['id_rol'],
                ':id_areap' => $area,
                ':id_mesa' => $data['id_mesa'] ? $data['id_mesa'] : null,
                ':dni' => $data['dni'],
                ':ape_paterno' => $data['ape_paterno'],
                ':ape_materno' => $data['ape_materno'],
                ':nombres' => $data['nombres'],
                ':email' => $data['email'],
                ':usuario' => $data['usuario'],
                ':contrasena' => base64_encode($data['contrasena']),
                ':imagen' => $imagen
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

    public function usuario_crud_update($data)
    {
        try 
        {
            if( !empty( $_FILES['imagen']['name'] ) ){
                switch ($_FILES['imagen']['type']) 
                { 
                    case 'image/jpeg': 
                    $ext = "jpg"; 
                    break;
                    case 'image/gif': 
                    $ext = "gif"; 
                    break; 
                    case 'image/png': 
                    $ext = "png"; 
                    break;
                    case 'application/pdf':
                    $ext = "pdf";
                    break;
                }
                $imagen = date('ymdhis').'.'.$ext;
                move_uploaded_file ($_FILES['imagen']['tmp_name'], 'public/images/users/'.$imagen);
            } else {
                $imagen = $data['imagen'];
            }

            $area = (isset($data['id_areap'])) ? $data['id_areap'] : 0;

            $consulta = "call usp_configUsuario( :flag, :id_usu, :id_rol, :id_areap, :id_mesa, :dni, :ape_paterno, :ape_materno, :nombres, :email, :usuario, :contrasena, :imagen);";
            $arrayParam =  array(
                ':flag' => 2,
                ':id_usu' => $data['id_usu'],
                ':id_mesa' => $data['id_mesa'] ? $data['id_mesa'] : null,
                ':id_rol' => $data['id_rol'],
                ':id_areap' => $area,
                ':dni' => $data['dni'],
                ':ape_paterno' => $data['ape_paterno'],
                ':ape_materno' => $data['ape_materno'],
                ':nombres' => $data['nombres'],
                ':email' => $data['email'],
                ':usuario' => $data['usuario'],
                ':contrasena' => base64_encode($data['contrasena']),
                ':imagen' => $imagen
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function usuario_estado($data)
    {
        try 
        {
            $sql = "UPDATE tm_usuario SET estado = ? WHERE id_usu = ?";
            $this->db->prepare($sql)
                ->execute(array($data['estado'],$data['id_usu']));
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function usuario_delete($data)
    {
        try 
        {
        if($data['id_rol'] == 1 OR $data['id_rol'] == 2){
            $consulta = "SELECT count(*) AS total FROM tm_pedido WHERE id_usu = ?";
        } else {
            $consulta = "SELECT count(*) AS total FROM tm_pedido_mesa WHERE id_mozo = ?";
        }
        $result = $this->db->prepare($consulta);
        $result->execute(array($data['id_usu']));
        $result->execute();
            if($result->fetchColumn()==0){
                $stm = $this->db->prepare("DELETE FROM tm_usuario WHERE id_usu = ?");          
                $stm->execute(array($data['id_usu']));
                return 1;
            }else{
                return 0;
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    /* FIN MODULO EMPRESA */

    /* INICIO MODULO RESTAURANTE */

    public function caja_list()
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_caja");
            $stm->execute();
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

    public function caja_crud_create($data)
    {
        try
        {
            $consulta = "call usp_configCajas( :flag, @a, :descripcion, :estado);";
            $arrayParam =  array(
                ':flag' => 1,
                ':descripcion' => $data['descripcion'],
                ':estado' => $data['estado']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function caja_crud_update($data)
    {
        try 
        {
            $consulta = "call usp_configCajas( :flag, :id_caja, :descripcion, :estado);";
            $arrayParam =  array(
                ':flag' => 2,
                ':id_caja' => $data['id_caja'],
                ':descripcion' => $data['descripcion'],
                ':estado' => $data['estado']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function areaprod_list($data)
    {
        try
        {
           
            $stm = $this->db->prepare("SELECT * FROM tm_area_prod WHERE id_areap like ?");
            $stm->execute(array($data['id_areap']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Impresora'} = $this->db->query("SELECT nombre FROM tm_impresora WHERE id_imp = ".$d->id_imp)
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

    public function areaprod_crud_create($data)
    {
        try
        {
            $consulta = "call usp_configAreasProd( :flag, @a, :id_imp, :nombre, :estado);";
            $arrayParam =  array(
                ':flag' => 1,                
                ':id_imp' => $data['id_imp'],
                ':nombre' => $data['nombre'],
                ':estado' => $data['estado']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function areaprod_crud_update($data)
    {
        try 
        {
            $consulta = "call usp_configAreasProd( :flag, :id_areap, :id_imp, :nombre, :estado);";
            $arrayParam =  array(
                ':flag' => 2,
                ':id_areap' => $data['id_areap'],
                ':id_imp' => $data['id_imp'],
                ':nombre' => $data['nombre'],
                ':estado' => $data['estado']                
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function salon_list()
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_salon ORDER BY id_salon ASC");
            $stm->execute();
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Mesas'} = $this->db->query("SELECT COUNT(id_mesa) AS total FROM tm_mesa WHERE id_salon = ".$d->id_salon)
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

    public function mesa_list()
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_mesa WHERE id_salon like ? ORDER BY nro_mesa ASC");
            $stm->execute(array($_POST['id_salon']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Salon'} = $this->db->query("SELECT descripcion FROM tm_salon WHERE id_salon = ".$d->id_salon)
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

    public function salon_crud_create($data)
    {
        try 
        {
            $consulta = "call usp_configSalones( :flag, @a, :descripcion, :estado);";
            $arrayParam =  array(
                ':flag' => 1,
                ':descripcion' => $data['descripcion'],
                ':estado' => $data['estado']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        }
        catch (Exception $e) 
        {
            return false;
        }
    }

    public function salon_crud_update($data)
    {
        try 
        {
            $consulta = "call usp_configSalones( :flag, :id_salon, :descripcion, :estado);";
            $arrayParam =  array(
                ':flag' => 2,
                ':id_salon' => $data['id_salon'],
                ':descripcion' => $data['descripcion'],
                ':estado' => $data['estado']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        }
        catch (Exception $e) 
        {
            return false;
        }
    }

    public function salon_crud_delete($data)
    {
        try 
        {
            $consulta = "call usp_configSalones( :flag, :id_salon, @a, @b);";
            $arrayParam =  array(
                ':flag' => 3,
                ':id_salon' => $data['id_salon']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function mesa_crud_create($data)
    {
        try 
        {
            $consulta = "call usp_configMesas( :flag, @a, :id_salon, :nro_mesa, @b);";
            $arrayParam =  array(
                ':flag' => 1,
                ':id_salon' => $data['id_salon'],
                ':nro_mesa' => $data['nro_mesa']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        }
        catch (Exception $e) 
        {
            return false;
        }
    }

    public function mesa_crud_update($data)
    {
        try 
        {
            $consulta = "call usp_configMesas( :flag, :id_mesa, :id_salon, :nro_mesa, :estado);";
            $arrayParam =  array(
                ':flag' => 2,
                ':id_mesa' => $data['id_mesa'],
                ':id_salon' => $data['id_salon'],
                ':nro_mesa' => $data['nro_mesa'],
                ':estado' => $data['estado']                        
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        }
        catch (Exception $e) 
        {
            return false;
        }
    }

    public function mesa_crud_delete($data)
    {
        try 
        {
            $consulta = "call usp_configMesas( :flag, :id_mesa, @a, @b, @c);";
            $arrayParam =  array(
                ':flag' => 3,
                ':id_mesa' => $data['id_mesa']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    /*
    public function mesa_estado($data)
    {
        try 
        {
            $sql = "UPDATE tm_mesa SET estado = ? WHERE id_mesa = ?";
            $this->db->prepare($sql)->execute(array($data['est_mesa'],$data['codi_mesa']));    
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }
    */

    /* ===================================== PRODUCTO*/
    public function producto_list()
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_producto WHERE id_prod like ? AND id_catg like ? AND id_catg <> 1 ORDER BY id_prod DESC");
            $stm->execute(array($_POST['id_prod'],$_POST['id_catg']));
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
    public function producto_pres_list()
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_producto_pres WHERE id_prod LIKE ? AND id_pres LIKE ?");
            $stm->execute(array($_POST['id_prod'],$_POST['id_pres']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'TipoProd'} = $this->db->query("SELECT id_tipo FROM tm_producto WHERE id_prod = ".$d->id_prod)
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

    public function producto_cat_list()
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_producto_catg WHERE id_catg <> 1 ORDER BY orden ASC");
            $stm->execute();
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

    public function producto_pres_ing($data)
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_producto_ingr WHERE id_pres = ?");
            $stm->execute(array($data['id_pres']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Insumo'} = $this->db->query("SELECT ins_med,ins_nom,ins_cat FROM v_insprod WHERE id_tipo_ins = ".$d->id_tipo_ins." AND id_ins = ".$d->id_ins)
                ->fetch(PDO::FETCH_OBJ);
            }
            foreach($c as $k => $d)
            {
                $c[$k]->{'Medida'} = $this->db->query("SELECT descripcion FROM tm_tipo_medida WHERE id_med = ".$d->id_med)
                ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function producto_buscar_ins($data)
    {
        try
        {        
            $cadena = $data['cadena'];
            $tipo = $data['tipo'];
            $stm = $this->db->prepare("SELECT * FROM v_insprod WHERE (ins_nom LIKE '%$cadena%' OR ins_cod LIKE '%$cadena%') AND est_b = 'a' AND est_c = 'a' AND id_tipo_ins <> ".$tipo." ORDER BY ins_nom LIMIT 5");
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function producto_cat_crud_create($data)
    {
        try 
        {
            if( !empty( $_FILES['imagen']['name'] ) ){
                switch ($_FILES['imagen']['type']) 
                { 
                    case 'image/jpeg': 
                    $ext = "jpg"; 
                    break;
                    case 'image/gif': 
                    $ext = "gif"; 
                    break; 
                    case 'image/png': 
                    $ext = "png"; 
                    break;
                    case 'application/pdf':
                    $ext = "pdf";
                    break;
                }
                $imagen = date('ymdhis').'.'.$ext;
                move_uploaded_file ($_FILES['imagen']['tmp_name'], 'public/images/productos/'.$imagen);
            } else {
                $imagen = $data['imagen'];
            }
            $consulta = "call usp_configProductoCatgs( :flag, @a, :descripcion, :delivery, :orden, :imagen, :estado);";
            $arrayParam =  array(
                ':flag' => 1,
                ':descripcion' => $data['descripcion_categoria'],
                ':delivery' => $data['hidden_delivery_categoria'],
                ':orden' => 100,
                ':imagen' => $imagen,
                ':estado' => $data['hidden_estado_categoria']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        }
        catch (Exception $e) 
        {
            return false;
        }
    }

    public function producto_cat_crud_update($data)
    {
        try 
        {
            if( !empty( $_FILES['imagen']['name'] ) ){
                switch ($_FILES['imagen']['type']) 
                { 
                    case 'image/jpeg': 
                    $ext = "jpg"; 
                    break;
                    case 'image/gif': 
                    $ext = "gif"; 
                    break; 
                    case 'image/png': 
                    $ext = "png"; 
                    break;
                    case 'application/pdf':
                    $ext = "pdf";
                    break;
                }
                $imagen = date('ymdhis').'.'.$ext;
                move_uploaded_file ($_FILES['imagen']['tmp_name'], 'public/images/productos/'.$imagen);
            } else {
                $imagen = $data['imagen'];
            }
            $consulta = "call usp_configProductoCatgs( :flag, :id_catg, :descripcion, :delivery, :orden, :imagen, :estado);";
            $arrayParam =  array(
                ':flag' => 2,
                ':id_catg' => $data['id_catg_categoria'],
                ':descripcion' => $data['descripcion_categoria'],
                ':delivery' => $data['hidden_delivery_categoria'],
                ':orden' => $data['orden_categoria'],
                ':imagen' => $imagen,
                ':estado' => $data['hidden_estado_categoria']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        }
        catch (Exception $e) 
        {
            return false;
        }
    }

    public function producto_crud_create($data)
    {
        try
        {
            $consulta = "call usp_configProducto( :flag, @a, :id_tipo, :id_catg, :id_areap, :nombre, :notas, :delivery, @b, :combo);";
            $arrayParam =  array(
                ':flag' => 1,
                ':id_tipo' => $data['id_tipo'],
                ':id_catg' => $data['id_catg'],
                ':id_areap' => $data['id_areap'],
                ':nombre' => $data['nombre'],
                ':notas' => $data['notas'],
                ':delivery' => $data['delivery'],
                ':combo'=> $data['combo']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function producto_crud_update($data)
    {
        try 
        {
            $consulta = "call usp_configProducto( :flag, :id_prod, :id_tipo, :id_catg, :id_areap, :nombre, :notas, :delivery, :estado);";
            $arrayParam =  array(
                ':flag' => 2,
                ':id_prod' => $data['id_prod'],
                ':id_tipo' => $data['id_tipo'],
                ':id_catg' => $data['id_catg'],
                ':id_areap' => $data['id_areap'],
                ':nombre' => $data['nombre'],
                ':notas' => $data['notas'],
                ':delivery' => $data['delivery'],
                ':estado' => $data['estado']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function producto_pres_crud_create($data)
    {
        try 
        {
            if( !empty( $_FILES['imagen']['name'] ) ){
                switch ($_FILES['imagen']['type']) 
                { 
                    case 'image/jpeg': 
                    $ext = "jpg"; 
                    break;
                    case 'image/gif': 
                    $ext = "gif"; 
                    break; 
                    case 'image/png': 
                    $ext = "png"; 
                    break;
                    case 'application/pdf':
                    $ext = "pdf";
                    break;
                }
                $imagen = date('ymdhis').'.'.$ext;
                move_uploaded_file ($_FILES['imagen']['tmp_name'], 'public/images/productos/'.$imagen);
            } else {
                $imagen = $data['imagen'];
            }
            $consulta = "call usp_configProductoPres( :flag, @a, :id_prod, :cod_prod, :presentacion, :descripcion, :precio, :precio_delivery, :receta, :stock_min, :impuesto, :delivery, :margen, :igv, :imagen, :estado);";
            $arrayParam =  array(
                ':flag' => 1,
                ':id_prod' => $data['id_prod_presentacion'],
                ':cod_prod' => $data['cod_prod_presentacion'],
                ':presentacion' => $data['presentacion_presentacion'],
                ':descripcion' => $data['descripcion_presentacion'],
                ':precio' => $data['precio_presentacion'],
                ':precio_delivery' => $data['precio_delivery'],
                ':receta' => $data['hidden_receta_presentacion'],
                ':stock_min' => $data['stock_min_presentacion'],
                ':impuesto' => $data['hidden_impuesto_presentacion'],
                ':delivery' => $data['hidden_delivery_presentacion'],
                ':margen' => $data['hidden_insumo_principal_presentacion'],
                ':igv' => Session::get('igv'),
                ':imagen' => $imagen,
                ':estado' => $data['hidden_estado_presentacion']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        }
        catch (Exception $e) 
        {
            return false;
        }
    }

    public function producto_pres_crud_update($data)
    {
        try 
        {
            if( !empty( $_FILES['imagen']['name'] ) ){
                switch ($_FILES['imagen']['type']) 
                { 
                    case 'image/jpeg': 
                    $ext = "jpg"; 
                    break;
                    case 'image/gif': 
                    $ext = "gif"; 
                    break; 
                    case 'image/png': 
                    $ext = "png"; 
                    break;
                    case 'application/pdf':
                    $ext = "pdf";
                    break;
                }
                $imagen = date('ymdhis').'.'.$ext;
                move_uploaded_file ($_FILES['imagen']['tmp_name'],'public/images/productos/'.$imagen);
            } else {
                $imagen = $data['imagen'];
            }
            $consulta = "call usp_configProductoPres( :flag, :id_pres, :id_prod, :cod_prod, :presentacion, :descripcion, :precio, :precio_delivery, :receta, :stock_min, :impuesto, :delivery, :margen, :igv, :imagen, :estado);";
            $arrayParam =  array(
                ':flag' => 2,
                ':id_pres' => $data['id_pres_presentacion'],
                ':id_prod' => $data['id_prod_presentacion'],
                ':cod_prod' => $data['cod_prod_presentacion'],
                ':presentacion' => $data['presentacion_presentacion'],
                ':descripcion' => $data['descripcion_presentacion'],
                ':precio' => $data['precio_presentacion'],
                ':precio_delivery' => $data['precio_delivery'],
                ':receta' => $data['hidden_receta_presentacion'],
                ':stock_min' => $data['stock_min_presentacion'],
                ':impuesto' => $data['hidden_impuesto_presentacion'],
                ':delivery' => $data['hidden_delivery_presentacion'],
                ':margen' => $data['hidden_insumo_principal_presentacion'],
                ':igv' => Session::get('igv'),
                ':imagen' => $imagen,
                ':estado' => $data['hidden_estado_presentacion']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        }
        catch (Exception $e) 
        {
            return false;
        }
    }

    public function producto_combo_cat()
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_producto_catg WHERE id_catg <> 1");
            $stm->execute();
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function producto_combo_unimed($data)
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

    public function producto_ingrediente_create($data)
    {
        try 
        {          
            $consulta = "call usp_configProductoIngrs( :flag, @a, :id_pres, :id_tipo_ins, :id_ins, :id_med, :cant);";
            $arrayParam =  array(
                ':flag' => 1,
                ':id_pres' => $data['id_pres'],
                ':id_tipo_ins' => $data['id_tipo_ins'],
                ':id_ins' => $data['id_ins'],
                ':id_med' => $data['id_med'],
                ':cant' => $data['cant']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
        }
        catch (Exception $e) 
        {
            return false;
        }
    }

    /*
    public function producto_ingrediente_update($data)
    {
        try 
        {
            $consulta = "call usp_configProductoIngrs( :flag, :idPres, :idIns, :cant, :idPi);";
            $arrayParam =  array(
                ':flag' => 2,
                ':idPres' => 1,
                ':idIns' => 1,
                ':cant' => $data['cant'],
                ':idPi' => $data['cod'],
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
        }
        catch (Exception $e) 
        {
            return false;
        }
    }
    */

    public function producto_ingrediente_delete($data)
    {
        try 
        {
            $consulta = "call usp_configProductoIngrs( :flag, :id_pi, @a, @b, @c, @d, @e);";
            $arrayParam =  array(
                ':flag' => 3,
                ':id_pi' => $data['id_pi']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
        }
        catch (Exception $e) 
        {
            return false;
        }
    }

    public function producto_cat_delete($data)
    {
        try 
        {
            $consulta = "call usp_configEliminarCategoriaProd(:id_catg);";
            $arrayParam =  array(
                ':id_catg' => $data['id_catg']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    /* ======================= FIN PRODUCTO */

    /* ======================= INCIO COMBO */
    public function combo_list()
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_producto WHERE id_prod like ? AND id_catg = 1 ORDER BY id_prod DESC");
            $stm->execute(array($_POST['id_prod']));
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

    /* ======================= FIN COMBO */

    /* ======================= INICIO INSUMO */

    public function insumo_cat_list()
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_insumo_catg");
            $stm->execute();
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

    public function insumo_list()
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM v_insumos WHERE id_ins like ? AND id_catg like ? ORDER BY id_ins DESC");
            $stm->execute(array($_POST['id_ins'],$_POST['id_catg']));
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

    public function insumo_combo_cat()
    {
        try
        {
            $stm = $this->db->prepare("SELECT * FROM tm_insumo_catg");
            $stm->execute();
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function insumo_cat_crud_create($data)
    {
        try 
        {
            $consulta = "call usp_configInsumoCatgs( :flag, :descC, @a);";
            $arrayParam =  array(
                ':flag' => 1,
                ':descC' => $data['descripcion']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        }
        catch (Exception $e) 
        {
            return false;
        }
    }

    public function insumo_cat_crud_update($data)
    {
        try 
        {
            $consulta = "call usp_configInsumoCatgs( :flag, :descC, :idCatg);";
            $arrayParam =  array(
                ':flag' => 2,
                ':descC' => $data['descripcion'],
                ':idCatg' => $data['id_catg']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        }
        catch (Exception $e) 
        {
            return false;
        }
    }

    public function insumo_crud_create($data)
    {
        try
        {
            $consulta = "call usp_configInsumo( :flag, :idCatg, :idMed, :cod, :nombre, :stock, :costo, @a, @b);";
            $arrayParam =  array(
                ':flag' => 1,
                ':idCatg' => $data['id_catg'],
                ':idMed' => $data['id_med'],
                ':cod' => $data['cod_ins'],
                ':nombre' => $data['nomb_ins'],
                ':stock' => $data['stock_min'],
                ':costo' => $data['cos_uni']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function insumo_crud_update($data)
    {
        try 
        {
            $consulta = "call usp_configInsumo( :flag, :idCatg, :idMed, :cod, :nombre, :stock, :costo, :estado, :idIns);";
            $arrayParam =  array(
                ':flag' => 2,
                ':idCatg' => $data['id_catg'],
                ':idMed' => $data['id_med'],
                ':cod' => $data['cod_ins'],
                ':nombre' => $data['nomb_ins'],
                ':stock' => $data['stock_min'],
                ':costo' => $data['cos_uni'],
                ':estado' => $data['estado'],
                ':idIns' => $data['id_ins']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function insumo_cat_delete($data)
    {
        try 
        {
            $consulta = "call usp_configEliminarCategoriaIns(:id_catg);";
            $arrayParam =  array(
                ':id_catg' => $data['id_catg']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function print_list($data)
    {
        try
        {
           
            $stm = $this->db->prepare("SELECT * FROM tm_impresora WHERE id_imp <> 1 AND id_imp LIKE ?");
            $stm->execute(array($data['id_imp']));
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

    public function print_crud_create($data)
    {
        try
        {
            $consulta = "call usp_configImpresoras( :flag, @a, :nombre, :estado);";
            $arrayParam =  array(
                ':flag' => 1,
                ':nombre' => $data['nombre'],
                ':estado' => $data['estado']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function print_crud_update($data)
    {
        try 
        {
            $consulta = "call usp_configImpresoras( :flag, :id_imp, :nombre, :estado);";
            $arrayParam =  array(
                ':flag' => 2,
                ':id_imp' => $data['id_imp'],
                ':nombre' => $data['nombre'],
                ':estado' => $data['estado']
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    /* ======================= FIN INSUMO */

    /* FIN MODULO RESTAURANTE */

    public function optimizar_pedidos()
    {
        try
        {
            $consulta = "call usp_optPedidos(:flag);";
            $arrayParam =  array(
                ':flag' => 1
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function optimizar_ventas()
    {
        try
        {
            $consulta = "call usp_optPedidos(:flag);";
            $arrayParam =  array(
                ':flag' => 2
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function optimizar_productos()
    {
        try
        {
            $consulta = "call usp_optPedidos(:flag);";
            $arrayParam =  array(
                ':flag' => 3
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function optimizar_insumos()
    {
        try
        {
            $consulta = "call usp_optPedidos(:flag);";
            $arrayParam =  array(
                ':flag' => 4
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function optimizar_clientes()
    {
        try
        {
            $consulta = "call usp_optPedidos(:flag);";
            $arrayParam =  array(
                ':flag' => 5
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function optimizar_proveedores()
    {
        try
        {
            $consulta = "call usp_optPedidos(:flag);";
            $arrayParam =  array(
                ':flag' => 6
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function optimizar_mesas()
    {
        try
        {
            $consulta = "call usp_optPedidos(:flag);";
            $arrayParam =  array(
                ':flag' => 7
            );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                return $row['cod'];
            }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function datosistema_data()
    {
        try
        {    
            $stm = $this->db->prepare("SELECT * FROM tm_configuracion");
            $stm->execute();
            $c = $stm->fetch(PDO::FETCH_OBJ);
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function datosistema_crud($data)
    {
        try 
        {
            $sql = "UPDATE tm_configuracion SET zona_hora = ?,trib_acr = ?,trib_car = ?,di_acr = ?,di_car = ?,imp_acr = ?,imp_val = ?,mon_acr = ?,mon_val = ?,pc_name = ?,pc_ip = ?,print_com = ?,print_pre = ?,print_cpe = ?";
            $this->db->prepare($sql)->execute(array($data['zona_hora'],$data['trib_acr'],$data['trib_car'],$data['di_acr'],$data['di_car'],$data['imp_acr'],$data['imp_val'],$data['mon_acr'],$data['mon_val'],$data['pc_name'],$data['pc_ip'],$data['print_com'],$data['print_pre'],$data['print_cpe']));

            /* ACTUALIZAR DATOS */
            Session::set('moneda', $data['mon_val']);
            Session::set('igv', $data['imp_val']);
            Session::set('tribAcr', $data['trib_acr']);
            Session::set('tribCar', $data['trib_car']);
            Session::set('diAcr', $data['di_acr']);
            Session::set('diCar', $data['di_car']);
            Session::set('impAcr', $data['imp_acr']);
            Session::set('monAcr', $data['mon_acr']);
            Session::set('zona_hor', $data['zona_hora']);
            Session::set('pc_name', $data['pc_name']);
            Session::set('pc_ip', $data['pc_ip']);
            Session::set('print_com', $data['print_com']);
            Session::set('print_pre', $data['print_pre']);
            Session::set('print_cpe', $data['print_cpe']); //funcion impresion directa 

        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }
    //NUEVAS FUNCIONES 
    public function select_Products_byCategory($data){
      try{
        $sql = "SELECT *
        FROM tm_producto_pres
        INNER JOIN tm_producto 
        
        ON tm_producto_pres.id_prod = tm_producto.id_prod  WHERE id_catg LIKE :id_categoria AND tm_producto.estado LIKE 'a'";
        $arrayParam =  array(
            ':id_categoria' => $data["id_catg"]
        );
        $st = $this->db->prepare($sql);
        $st->execute($arrayParam);
        $c = $st->fetchAll(PDO::FETCH_OBJ);
        $data = array("data" => $c);
        $json = json_encode($data);
        echo $json;  

      }catch (Exception $e)
      {
          echo $e->getMessage();
      }
    }
    public function seleccionar_precios_por_presentacion($data){
        try {
            $sql = "SELECT *
            FROM tm_precios
            WHERE id_pres LIKE :id_pres";
            $arrayParam =  array(
                ':id_pres' => $data["id_pres"]
            );
            $st = $this->db->prepare($sql);
            $st->execute($arrayParam);
            $c = $st->fetchAll(PDO::FETCH_OBJ);
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;  
        } catch (Exception $th) {
            //throw $th;
            echo $th->getMessage();
        }
    }
    public function editar_precio($data){
        try {
            $sql = "UPDATE tm_precios SET precio = :precio WHERE id_precio = :id_pres AND dia LIKE :dia";
            $arrayParam = array(
                ':precio' => $data['precio'],
                ':id_pres' => $data['id_pres'],
                ':dia' => $data['dia']
            );
            $st = $this->db->prepare($sql);
            echo $st->execute($arrayParam);
        } catch (PDOException $th) {
            echo $th->getMessage();
        }
    }
   public function agregar_precio($data){
       try {
           //code...
           $sql = "INSERT INTO tm_precios VALUES(null, :id_pres, :precio, :dia)";
           $arrayParam =  array(
             'id_pres' => $data['id_pres'],
             'precio' => $data['precio'],
             'dia' => $data['dia']
           );
           $st = $this->db->prepare($sql);
           echo  $st->execute($arrayParam);
       } catch (Exception $th) {
           echo $th->getMessage();
       }        
   }
   public function eliminar_precio($data){
       try {
           $sql = "DELETE FROM tm_precios WHERE id_precio = :id_precio";
           $arrayParam = array(
            'id_precio' => $data['id_precio']
           );
           $st = $this->db->prepare($sql);
           echo $st->execute($arrayParam);
       } catch (Exception $th) {
        echo $th->getMessage();
    }
   }
   public function verificar_existencia_de_precio($data){
       $sql = "SELECT * FROM
       tm_precios
       WHERE dia LIKE :dia AND id_pres = :id_pres";
       $arrayParam = array(
           ':dia'=>$data['dia'],
           ':id_pres'=> $data['id_pres']
       );
       $st = $this->db->prepare($sql);
       $st->execute($arrayParam);
       echo $st->rowCount();
   }
   public function extraer_dias($parametros){
       $sql = "SELECT dia FROM
       tm_precios WHERE id_pres = :id_pres";
       $arrayParam = array(
           ':id_pres'=> $parametros['id']
       );
       $st = $this->db->prepare($sql);
       $st->execute($arrayParam);
       $c = $st->fetchAll(PDO::FETCH_OBJ);
       $data = array("data" => $c);
       $json = json_encode($data, true);
       echo $json;
   }
   public function contar_precios($parametros){
       $sql = "SELECT *
       FROM tm_precios WHERE id_pres = :id_pres";
       $arrayParam = array(
        ':id_pres'=> $parametros['id_pres']
       );
       $st = $this->db->prepare($sql);
       $st->execute($arrayParam);
       echo $st->rowCount();
   }
   public function cambiar_precio_por_id($data){
       try {
           //code...
           $sql = "UPDATE tm_producto_pres SET precio = :precio WHERE id_pres = :id_pres";
           $arrayParam = array(
            ':precio'=> $data['precio'],
            ':id_pres'=> $data['id_pres']
           );
           $st = $this->db->prepare($sql);
           $i = $st->execute($arrayParam);
           $sql1 = "UPDATE tm_detalle_pedido SET precio = :precio WHERE id_pres = :id_pres AND estado LIKE 'a'";
           $stm = $this->db->prepare($sql1);
           echo $stm->execute($arrayParam);
          
       } catch (PDOException $th) {
           //throw $th;
           echo $th->getMessage();
       }
   }
   public function cambiar_precio_por_categoria($p){
       //CONSULTA PARA TRAER TODOS LOS ID_PRESENTACION PARA VERIFICAR EN TM_PRECIOS SI EXISTE UNA IRREGULARIDAD
       $sql = "SELECT id_pres FROM 
       tm_producto_pres INNER JOIN tm_producto ON tm_producto.id_prod = tm_producto_pres.id_prod WHERE id_catg = :id_cat";
       $p1 = array(
           ':id_cat'=>$p['id_cat']
       );
       $st = $this->db->prepare($sql);
       $st->execute($p1);
       $c = $st->fetchAll();
       $data = array("data" => $c);
       $c_array =  count($data["data"]);

       // HACEMOS EL CICLO DONDE SELECCIONAREMOS TODOS LOS PRECIOS REFERERENTE AL DÍA
       $a = 0;
       $id_pres = 0;
       $dia = $p["dia"];
       for ($x = 0; $x < $c_array ; $x++) {     
           $dayQuery = "SELECT * FROM tm_precios WHERE id_pres = :id_pres AND dia LIKE :dia";
           $p_array = array(
               ":id_pres" => $data["data"][$x][0],
               ":dia" => $dia
           );
           $e = $this->db->prepare($dayQuery);
           $e->execute($p_array);
           $d = $e->fetchAll();
          if(count($d)>0){
            $sql = "UPDATE tm_producto_pres SET precio = :precio WHERE id_pres = :id_pres";
            $arrayParam = array(
             ':precio'=> $d[0]['precio'],
             ':id_pres'=> $d[0]['id_pres']
            );
            $e = $this->db->prepare($sql);
            if($e->execute($arrayParam)){
             $sql1 = "UPDATE tm_detalle_pedido SET precio = :precio WHERE id_pres = :id_pres AND estado LIKE 'a'";
             $stm = $this->db->prepare($sql1);
                 if($stm->execute($arrayParam)) {$a = $c_array;}else{$a = 0;} 
            }
          }
       }  
       if($a == $c_array){
           echo true;
       }else{
           echo "Al parecer hubo un problema al cambiar algunos precios, esto se debe a que no todos tienen un precio registrado para el dia " . $dia . "Pero en realidad se han cambiado precios";
       }
   }
   public function cambiar_precio_general($data){
       try{
        $a = 0;
        $sqlSelectId = "SELECT id_pres FROM tm_producto_pres";
        $e = $this->db->prepare($sqlSelectId);
        $e->execute();
        $d = $e->fetchAll();
        $rowCount = count($d);
        $dia = $data["dia"];
    
        for($x = 0 ; $x  < $rowCount ; $x++){
            $dayQuery = "SELECT precio, id_pres FROM tm_precios WHERE id_pres = :id_pres AND dia LIKE :dia LIMIT 1";
            $p_array = array(
               ":id_pres" => $d[$x]["id_pres"],
               ":dia" => $dia
           );
           $e = $this->db->prepare($dayQuery);
           $e->execute($p_array);
           $a = $e->fetchAll();
            if(count($a) > 0){
                    $precio = $a[0]["precio"];
                    $id_pres = $a[0]["id_pres"];
                    $sql = "UPDATE tm_producto_pres SET precio = :precio WHERE id_pres = :id_pres";
                    $arrayParam = array(
                    ':precio'=> $precio,
                    ':id_pres'=> $id_pres
                    );
                    $ex = $this->db->prepare($sql);
                    if($ex->execute($arrayParam)){
                        $cm = "UPDATE tm_detalle_pedido SET precio = :precio WHERE id_pres = :id_pres AND estado LIKE 'a' ";
                        $stm = $this->db->prepare($cm);
                            if($stm->execute($arrayParam))
                            {$a = $rowCount;}else{$a = true;} 
                    }
            }
        }
        if($a == $rowCount){
            echo true;
        }else{
            echo "Al parecer, no tienes ningun precio registrado, favor de registrarlo";
        }
       }catch(PDOException $e){
           echo "Error: ".$e->getMessage();
       }
   }
   public function Mozo()
    {
        try
        {   
            return $this->db->selectAll('SELECT id_usu,nombres,ape_paterno,ape_materno FROM v_usuarios WHERE id_rol = 5 AND estado = "a"');
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }
    public function add_rol_salon($data){
        $cs = $this->db->prepare("SELECT * FROM tm_areas_rel WHERE id_usu = {$data['id_usu']} AND id_salon = {$data['id_salon']}");
        $cs->execute();
        if($cs->rowCount() > 0){
            echo "USUARIO YA REGISTRADO A ESTE SALON";
        }else{
            $stm = $this->db->prepare("INSERT INTO tm_areas_rel (id_rel, id_usu, id_salon) VALUES (null, ?,?)");
            if($stm->execute(array($data['id_usu'], $data['id_salon']))){
                echo true;
            }else{
                echo "ERROR AL AGREGAR";
            }   
        }
      
    }
    public function Usuarios_rol(){
        $c = $this->db->query("SELECT *FROM tm_areas_rel")->fetchAll(PDO::FETCH_OBJ);
        return $c;
    }
    public function listar_personal($data){
       

        $x = $this->db->query("SELECT id_usu FROM tm_areas_rel as v WHERE v.id_salon = {$data['id_salon']}")->fetchAll(PDO::FETCH_OBJ);
        for ($i=0; $i < count($x); $i++) { 
            $c[$i] = $this->db->query("SELECT id_usu, CONCAT(ape_paterno,' ',ape_materno, ' ', nombres) AS nombre,
            IF(id_usu = {$x[$i]->id_usu}, 1, 0) as ST, {$x[$i]->id_usu} AS comparacion
            FROM v_usuarios WHERE id_rol = 5 AND estado = 'a'")->fetchAll(PDO::FETCH_OBJ);
        }
        $cx = $this->db->query("SELECT id_usu, CONCAT(ape_paterno,' ',ape_materno, ' ', nombres) AS nombre FROM v_usuarios WHERE id_rol = 5 AND estado = 'a' ")->fetchAll(PDO::FETCH_OBJ);
        $data = array(
            "data"=>$x,
            "mozos" => $cx
        );
        $json = json_encode($data);
        echo $json;
    }
    public function Mozo1()
    {
        try
        {   
            echo $this->db->selectAll('SELECT id_usu,nombres,ape_paterno,ape_materno FROM v_usuarios WHERE id_rol = 5 AND estado = "a"');
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }
    public function listar_usuarios_en_salon($data){
        $c = $this->db->query("SELECT r.id_usu, CONCAT(u.ape_paterno,' ',u.ape_materno, ' ', u.nombres) AS nombre FROM tm_areas_rel AS r 
        INNER JOIN v_usuarios AS u ON r.id_usu = u.id_usu WHERE r.id_salon = {$data['id_salon']}")->fetchAll(PDO::FETCH_OBJ);
        $data = array("data" => $c);
        $json = json_encode($data);
        echo $json;
    }
    public function borrar_acceso($data){
        $stm = $this->db->prepare("DELETE FROM tm_areas_rel WHERE id_usu = {$data['id_usu']} AND id_salon = {$data['id_salon']}");
        if($stm->execute()){
            echo 1;
        }else{
            echo "Hubo un error al eliminar el acceso";
        }
    }

}