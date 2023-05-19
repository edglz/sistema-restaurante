<?php

class Cliente_Model extends Model {

	public function __construct() {
		parent::__construct();
	}
	
    public function cliente_list($data)
    {
        try
        {      
            $stm = $this->db->prepare("SELECT * FROM v_clientes WHERE id_cliente <> 1 AND tipo_cliente = ?");
            $stm->execute(array($data['tipo_cliente']));            
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

    public function cliente_datos($data)
    {
        try 
        {
            $stm = $this->db->prepare("SELECT * FROM tm_cliente WHERE id_cliente = ?");
            $stm->execute(array($data['id_cliente']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function cliente_crud_create($data)
    {
        try 
        {
            $consulta = "call usp_restRegCliente( :flag, @a, :tipo_cliente, :dni, :ruc, :nombres, :razon_social, :telefono, :fecha_nac, :correo, :direccion, :referencia);";
                $arrayParam =  array(
                    ':flag' => 1,
                    ':tipo_cliente' => $data['tipo_cliente'],
                    ':dni' => $data['dni'],
                    ':ruc' => $data['ruc'],
                    ':nombres' => $data['nombres'],
                    ':razon_social' => $data['razon_social'],
                    ':telefono' => $data['telefono'],
                    ':fecha_nac' => date('Y-m-d',strtotime($data['fecha_nac'])),
                    ':correo' => $data['correo'],
                    ':direccion' => $data['direccion'],
                    ':referencia' => $data['referencia']
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

    public function cliente_crud_update($data)
    {
        try 
        {
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
                    ':fecha_nac' => date('Y-m-d',strtotime($data['fecha_nac'])),
                    ':correo' => $data['correo'],
                    ':direccion' => $data['direccion'],
                    ':referencia' => $data['referencia']
                );
            $st = $this->db->prepare($consulta);
            $st->execute($arrayParam);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function cliente_ventas($data)
    {
        try
        {   
            $stm = $this->db->prepare("SELECT *,IFNULL((pago_efe+pago_tar),0) AS monto_total FROM v_ventas_con WHERE id_cli = ? AND estado = 'a'");
            $stm->execute(array($data['id_cliente']));
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

    public function cliente_estado($data)
    {
        try 
        {
            $sql = "UPDATE tm_cliente SET estado = ? WHERE id_cliente = ?";
            $this->db->prepare($sql)
                ->execute(array($data['estado'],$data['id_cliente']));
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function cliente_delete($data)
    {
        try 
        {
            $consulta = "SELECT count(*) AS total FROM tm_venta WHERE id_cliente = :id_cliente";
            $result = $this->db->prepare($consulta);
            $result->bindParam(':id_cliente',$data['id_cliente'],PDO::PARAM_INT);
            $result->execute();
                if($result->fetchColumn()==0){
                    $stm = $this->db->prepare("DELETE FROM tm_cliente WHERE id_cliente = ?");          
                    $stm->execute(array($data['id_cliente']));
                    return 1;
                }else{
                    return 0;
                }
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }
  

}