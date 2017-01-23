<?php

namespace upload\model;
use upload\lib\data;

class oportunity {

	private $_conn;
    private $_nosource = array('68');
       
    function __construct(data $conn) {
    	$this->_conn = $conn;
    }
    
    public function getPromotor($email){
    	 
    
    	$query = "SELECT * FROM usuarios 
				  WHERE email = '".$email."'" ;
    
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    	//print_r($clients);
    
    	
    	return $result;
    	
    	
    
    }
    
    
    public function insertOpotunidad($param){
        	
    	$query ="INSERT INTO crmoportunidades (id,fecha_ingreso, estado,id_cliente,
        		 id_promotor, tipo_oportunidad, fecha_cierre, id_sucursal, id_creador) ";
    
    	$query =$query." values ( ";
    	$query =$query.$param['id'].",";
    	$query =$query."'". $param['fecha_ingreso']."',";
    	$query =$query.$param['estado'].",";
    	$query =$query."'".$param['id_cliente']."',";
    	$query =$query."'".$param['id_promotor']."',";
    	$query =$query."'". $param['tipo_oportunidad']."',";
    	$query =$query."'". $param['fecha_cierre']."',";
    	$query =$query."'". $param['id_sucursal']."',";
    	$query =$query."'". $param['id_creador']."'";
    	$query =$query.")";
    	
    	echo "\n\n".$query."\n\n";
        	
    	$r = $this->_conn->_query($query);
    
    	return 0;
    
    }
    
    
    public function insertCliente($param){
    	 
    	$query ="INSERT INTO clientes (id_cliente, id_identificacion, nat_juridica, nombre, apellido, dir_co) ";
    
    	$query =$query." values ( ";
    	$query =$query."'". $param['id_cliente']."',";
    	$query =$query."'".$param['id_identificacion']."',";
    	$query =$query."'".$param['nat_juridica']."',";
    	$query =$query."'".$param['nombre']."',";
    	$query =$query."'". $param['apellido']."',";
    	$query =$query."''";
    	
    	
    	$query =$query.")";
    	 
    	echo "\n".$query."\n";
    	 
    	$r = $this->_conn->_query($query);
    
    	return 0;
    
    }
    
    public function insertClienteJuridico($param){
    
    	$query ="INSERT INTO clientes (id_cliente, id_identificacion, nat_juridica, nombre, nom_empresa) ";
    
    	$query =$query." values ( ";
    	$query =$query."'". $param['id_cliente']."',";
    	$query =$query."'".$param['id_identificacion']."',";
    	$query =$query."'".$param['nat_juridica']."',";
    	$query =$query."'".$param['nom_empresa']."',";
    	$query =$query."'". $param['nom_empresa']."'";
    
    	$query =$query.")";
    
    	//echo "\n".$query."\n";
    
    	$r = $this->_conn->_query($query);
    
    	return 0;
    
    }
    
    
    public function getCliente($idCliente){
    
    	$query = "SELECT * FROM clientes 
				  WHERE id_cliente = '".$idCliente."'" ;
    
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    	//print_r($clients);
    
    
    	return $result;
    }
    
    public function getOportunidad($idOportunidad){
    
    	$query = "SELECT * FROM crmoportunidades
				  WHERE id = '".$idOportunidad."'" ;
    
    	//echo "\n".$query."\n";
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    	//print_r($clients);
    
    
    	return $result;
    }
    
    
}