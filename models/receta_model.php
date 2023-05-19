<?php

class Receta_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function Preparados()
    {
        return $this->db->query("SELECT pr.id_preparado, cat.nombre, pr.nombre, pr.preparacion, pr.estado FROM tm_preparados as pr INNER JOIN tm_preparados_catg AS cat ON pr.id_catg = cat.id_catg")->fetchAll(PDO::FETCH_OBJ);
    }
    public function CategoriasAll()
    {
        return $this->db->query("SELECT  * FROM tm_recetas_catg")->fetchAll(PDO::FETCH_OBJ);
    }
    public function Categorias()
    {
        $stm = $this->db->query("SELECT  * FROM tm_recetas_catg")->fetchAll(PDO::FETCH_OBJ);
        $data = array("data" => $stm);
        echo json_encode($data);
    }
    public function ProductosAll()
    {
        return $this->db->query("SELECT  * FROM tm_producto")->fetchAll(PDO::FETCH_OBJ);
    }
    public function PresentacionesAll($id)
    {
        $c =  $this->db->query("SELECT  * FROM tm_producto_pres WHERE id_prod LIKE {$id}")->fetchAll(PDO::FETCH_OBJ);
        $data = array("data" => $c);
        echo json_encode($data);
    }
    public function eliminaCategoria($data)
    {
        $stm = $this->db->query("DELETE FROM tm_recetas_catg WHERE id_catg = {$data['id_catg']}");
        if ($stm) {
            $return = array("msj" => 1);
        } else {
            $return = array("msj" => -1);
        }
        echo json_encode($return);
    }
    public function eliminaReceta($id)
    {
        $stm = $this->db->query("DELETE FROM tm_receta_ingrediente WHERE id_receta = {$id}");
        $stm = $this->db->query("DELETE FROM tm_recetas WHERE id_receta = {$id}");
        if ($stm) {
            $return = array("msj" => 1);
        } else {
            $return = array("msj" => -1);
        }
        echo json_encode($return);
    }
    public function editaCategoria($data)
    {

        $id_catg = $data['id_catg_categoria'];
        $imagen = $data['imagen'];
        $nombre = $data['descripcion_categoria'];
        $estado = "";
        if (isset($data['estado_categoria']) && $data['estado_categoria'] == 'on') {
            $estado = "a";
        } else {
            $estado = "i";
        }
        $stm = $this->db->query("UPDATE tm_recetas_catg SET nombre = '{$nombre}', imagen = '{$imagen}', estado = '{$estado}' WHERE id_catg = {$id_catg}");
        if ($stm) {
            $return = array("msj" => 2);
        } else {
            $return = array("msj" => -1);
        }
        echo json_encode($return);
    }
    public function registraCategoria($data)
    {
        $imagen = $data['imagen'];
        $nombre = $data['descripcion_categoria'];
        $estado = "";
        if (isset($data['estado_categoria']) && $data['estado_categoria'] == 'on') {
            $estado = "a";
        } else {
            $estado = "i";
        }
        $stm = $this->db->query("INSERT INTO tm_recetas_catg (id_catg, nombre, imagen, estado) VALUES(null, '{$nombre}', '{$imagen}','{$estado}')");
        if ($stm) {
            $return = array("msj" => 1);
        } else {
            $return = array("msj" => -1);
        }
        echo json_encode($return);
    }
    public function receta_list($data)
    {
        $stm = $this->db->prepare("SELECT * FROM v_recetas_producto WHERE id_catg_receta LIKE ?");
        $stm->execute(array($data['id_catg']));
        $c = $stm->fetchAll(PDO::FETCH_OBJ);
        $data = array("data" => $c);
        $json = json_encode($data);
        echo $json;
    }
    public function list_preparados()
    {
        $c = $this->db->query("SELECT * FROM v_preparados")->fetchAll(PDO::FETCH_OBJ);
        $data = array("data" => $c);
        $json = json_encode($data);
        echo $json;
    }
    public function registra_receta($data)
    {
        date_default_timezone_set('America/Lima');
        $stm = $this->db->prepare("INSERT INTO tm_recetas (id_receta, id_catg_receta, id_pres, nombre, receta, fecha_creacion, estado)
        VALUES(null, :id_catg, :id_pres, :nombre, :receta, :fecha, 'a')");
        $stm->bindParam(':id_catg', $data['catg_id'], PDO::PARAM_INT);
        $stm->bindParam(':id_pres', $data['pres_id'], PDO::PARAM_INT);
        $stm->bindParam(':nombre', $data['nombre_rec'], PDO::PARAM_STR);
        $stm->bindParam(':receta', $data['content_prep'], PDO::PARAM_STR);
        $stm->bindParam(':fecha', date('d-m-y H:i:s'), PDO::PARAM_STR);
        $stm->execute();
        $id =  $this->db->lastInsertId();
        //REGISTRA INGREDIENTES
        $ci = count($data['ingredientes']);
        for ($x = 0; $x < $ci; $x++) {
            $xx = $this->db->query("INSERT INTO tm_receta_ingrediente (id_ingrediente, id_receta, cantidad, unidad, nombre, oracion)
            VALUES(null, {$id}, {$data['ingredientes'][$x]['cantidad']}, '{$data['ingredientes'][$x]['unidad']}', '{$data['ingredientes'][$x]['nombre_ing']}', '{$data['ingredientes'][$x]['complete']}')");
        }
        if ($stm) {
            $return = array("msj" => 1);
        } else {
            $return = array("msj" => -1);
        }
        echo json_encode($return);
    }
    public function receta($id)
    {
        $c = $this->db->query("SELECT * FROM tm_recetas WHERE id_receta = {$id}")->fetch(PDO::FETCH_OBJ);
        $c->{'ingredientes'} = $this->db->query("SELECT * FROM tm_receta_ingrediente WHERE id_receta = {$id}")->fetchAll(PDO::FETCH_OBJ);
        $c->{'Producto'} = $this->db->query("SELECT CONCAT(p.nombre, ' ', pres.presentacion) as nombre FROM tm_producto as p INNER JOIN tm_producto_pres as pres ON p.id_prod = pres.id_prod WHERE pres.id_pres = $c->id_pres")->fetch(PDO::FETCH_OBJ);
        $c->{'catg'} = $this->db->query("SELECT * FROM tm_recetas_catg WHERE id_catg = {$c->id_catg_receta}")->fetch(PDO::FETCH_OBJ);
        return $c;
    }

    public function Empresa()
    {
        try {
            return $this->db->selectOne("SELECT * FROM tm_empresa");
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function receta_edit_fetch($id)
    {
        $c = $this->db->query("SELECT * FROM tm_recetas WHERE id_receta = {$id}")->fetch(PDO::FETCH_OBJ);
        $c->{'ingredientes'} = $this->db->query("SELECT cantidad, nombre, unidad, oracion as complete FROM tm_receta_ingrediente WHERE id_receta = {$id}")->fetchAll(PDO::FETCH_OBJ);
        $c->{'Producto'} = $this->db->query("SELECT CONCAT(p.nombre, ' ', pres.presentacion) as nombre FROM tm_producto as p INNER JOIN tm_producto_pres as pres ON p.id_prod = pres.id_prod WHERE pres.id_pres = $c->id_pres")->fetch(PDO::FETCH_OBJ);
        $c->{'catg'} = $this->db->query("SELECT * FROM tm_recetas_catg WHERE id_catg = {$c->id_catg_receta}")->fetch(PDO::FETCH_OBJ);
        return $c;
    }
    public function get_catg_preparados()
    {
        $c = $this->db->query("SELECT * FROM tm_preparados_catg WHERE estado = 'a'")->fetchAll(PDO::FETCH_OBJ);
        $data = array("data" => $c);
        $json = json_encode($data);
        echo $json;
    }
    public function get_preparados($id_catg = '%')
    {
        $c = $this->db->query("SELECT * FROM tm_preparados WHERE id_catg LIKE '{$id_catg}'")->fetchAll(PDO::FETCH_OBJ);
        $data = array("data" => $c);
        $json = json_encode($data);
        echo $json;
    }
    public function crud_catg_preparados($req)
    {
        $nombre = $req['nombre'];
        $id_catg = $req['id_catg'];
        $estado = $req['estado'];
        $add = $req['id_catg'] == '' ? true : false;
        if ($add == false) {
            $c = $this->db->query("UPDATE tm_preparados_catg SET nombre = '{$nombre}', 
            estado = '{$estado}' WHERE id_catg = '{$id_catg}'");
            if ($c) {
                $return = array("msj" => 1);
            } else {
                $return = array("msj" => -1);
            }
        } else {
            $c = $this->db->query("INSERT INTO tm_preparados_catg (id_catg, nombre, estado)
            VALUES(null,'{$nombre}', '{$estado}')");
            if ($c) {
                $return = array("msj" => 1);
            } else {
                $return = array("msj" => -1);
            }
        }
        echo json_encode($return);
    }
    public function delete_catg_prep($id){
        $c = $this->db->query("DELETE FROM tm_preparados_catg WHERE id_catg = {$id}");
        if ($c) {
            $return = array("msj" => 1);
        } else {
            $return = array("msj" => -1);
        }
        echo json_encode($return);
    }
}
