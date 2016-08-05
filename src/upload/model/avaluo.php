<?php

namespace upload\model;
use upload\lib\data;

class avaluo {

	private $_conn;
    private $_nosource = array('68');
       
    function __construct(data $conn) {
    	$this->_conn = $conn;
    }

    public function insertAvaluo($param){
    
    		$query = " INSERT INTO log_avaluos (id_avaluo, id_cliente, fecha_solicitud,
    			estado, id_ciudad, id_sector, id_barrio, id_tipo_inmueble, direccion, 
    			tipo_avaluo, fecha_new, solicitante)";
    			
	    	$query =$query." values ( ";
	    	$query =$query.$param['id_avaluo'].",";
	    	$query =$query."'". $param['fecha_solicitud']."',";
	    	$query =$query.$param['estado'].",";
	    	$query =$query."'".$param['id_cliente']."',";
	    	$query =$query."'".$param['id_ciudad']."',";
	    	$query =$query."'". $param['id_sector']."',";
	    	$query =$query."'". $param['id_barrio']."',";
	    	$query =$query."'". $param['id_tipo_inmueble']."',";
	    	$query =$query."'". $param['direccion']."',";
	    	$query =$query."'". $param['tipo_avaluo']."',";
	    	$query =$query."'". $param['fecha_new']."',";
	    	$query =$query."'". $param['solicitante']."'";
	    	$query =$query.")";       		    	
    			
    		echo "Consulta Avaluo : ".$query."\n";  
    		
        	$r = $this->_conn->_query($query);
        	$clients = $this->_conn->_getData($r);  
    
    }
    public function insertPuc($param){
    	
    	$query= "INSERT INTO puc (id_cuenta,id_padre,descripcion,nat,nit,tercero,id_cierre)";
    	
    	$query= $query."values ( ";
    	$query= $query."'281095".$param['id_avaluo']."',";
    	$query= $query."'281095"."',";
    	$query= $query."'".$param['solicitante']."',";
    	$query= $query."'C"."',";
    	$query= $query."'".$param['id_cliente']."',";
    	$query= $query."1".",";
    	$query= $query."''";
    	$query =$query.")";
    	
    	echo "\nConsulta Puc : ".$query."\n";
    	
    	$r = $this->_conn->_query($query);
    	$appraisal = $this->_conn->_getData($r);

    }
    public function insertSaldo($param){
    	
    	$query= "INSERT INTO saldos (id_cuenta, año)";
    	
    	$year = date("Y", strtotime($param['fecha_new']));
    	
    	$query= $query."values ( ";
    	$query= $query."'281095".$param['id_avaluo']."',";    	
    	$query= $query."'".$year."'";
    	$query =$query.")";  
    	
    	echo "\nConsulta Saldo : ".$query."\n";    	
  
    	$r = $this->_conn->_query($query);
    	$appraisal = $this->_conn->_getData($r);  
    	 
    	
    }
       
    
    public function exitsAvaluo($id_avaluo){

    	$query = " SELECT id_avaluo FROM log_avaluos WHERE id_avaluo = $id_avaluo";
    	$exist = false;

        $r = $this->_conn->_query($query);
        $appraisal = $this->_conn->_getData($r);       
           
        if(count($appraisal)>0){
			$exist=true;   	
        }
        return $exist;
    }
    
    public function exitsPuc($param){
    
    	$query = " SELECT id_cuenta FROM puc WHERE id_cuenta = '281095".$param['id_avaluo']."'";
    	$exist = false;
    
    	$r = $this->_conn->_query($query);
    	$puc = $this->_conn->_getData($r);
    	 
    	if(count($puc)>0){
    		$exist=true;
    	}
    	return $exist;
    }
    
    public function exitsSaldo($param){
    
    	$query = " SELECT id_cuenta FROM saldos WHERE id_cuenta = '281095".$param['id_avaluo']."'";
    	$exist = false;
    
    	$r = $this->_conn->_query($query);
    	$saldo = $this->_conn->_getData($r);
    
    	if(count($saldo)>0){
    		$exist=true;
    	}
    	return $exist;
    }
    
    public function exitClient($idCliente){
    	
    	$query = "SELECT id_cliente FROM clientes  WHERE id_cliente = '".$idCliente."'" ;
    	$exist = false;    	
    	
    	$r = $this->_conn->_query($query);
    	$client = $this->_conn->_getData($r);
    	//print_r($clients);
    	//echo "Total clientes inmuebles SF1: ".count($client)."\n";
    	 
    	if(count($client)>0){
    		$exist=true;
    	}
    	return $exist;

    }
    
}