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
    	$query =$query."'". $param['direccion']."'";
    	$query =$query."'". $param['tipo_avaluo']."'";
    	$query =$query."'". $param['fecha_new']."'";
    	$query =$query."'". $param['solicitante']."'";
    	$query =$query.")";
    			

    			//"VALUES(300001, '73164592', '18/07/2016',
    			//0, 'CTG','','10092',1, 'texto con la direccion', 1, '18/07/2016', 'Nombre solicitante')";
    	

   		echo "\n".$query."\n";
        $r = $this->_conn->_query($query);
        $clients = $this->_conn->_getData($r);
        //print_r($clients);
        echo "Total clientes inmuebles SF1: ".count($clients)."\n";
    
        return $clients;
    
    } 
    public function exitsAvaluo($id_avaluo){

    	$query = " SELECT id_avaluo FROM log_avaluos WHERE id_avaluo = $id_avaluo";
    	$exist = false;

   		echo "\n".$query."\n";
        $r = $this->_conn->_query($query);
        $appraisal = $this->_conn->_getData($r);
        //print_r($clients);
        echo "Total clientes inmuebles SF1: ".count($appraisal)."\n";
           
        if(count($appraisal)>0){
			$exist=true;   	
        }
        return $exist;
    }
    
}