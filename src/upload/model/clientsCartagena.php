<?php

namespace upload\model;
use upload\lib\data;

class clientsCartagena {

	private $_conn;
    private $_nosource = array('68');
       
    function __construct(data $conn) {
    	$this->_conn = $conn;
    }
    

    public function getClients(){

    	$query = "SELECT * FROM clientes
    	WHERE id_cliente IS NOT NULL
   
    	AND fecha_ingreso > '2016-01-18 00:00:00.00'";
    	
//     	$query = "SELECT * FROM clientes
//     	WHERE id_cliente IS NOT NULL
    	
//     	AND log_modi > '2016-01-18 5:20:00.00'";
    	
//     	$query = "SELECT log_modi, * FROM clientes
// WHERE id_cliente IS NOT NULL
// AND log_modi > '2016-01-14 00:00:00.00'";
//         $query = "SELECT * FROM clientes
// WHERE id_cliente = '32938206'";   
         
//     	$query = "SELECT * FROM clientes
// 		WHERE id_cliente IS NOT NULL
// 		AND id_cliente != ''
// 		AND id_cliente != '0'
// 		AND id_cliente != '000000'
//     	AND nat_juridica = 'J'";
    	
//     	$query = "SELECT TOP 64607 * FROM clientes
//     			WHERE id_cliente IS NOT NULL
//     			AND id_cliente != ''
//     			AND id_cliente != '0'
//     			AND id_cliente != '000000'
//     	    	AND nat_juridica = 'N'";
    	
        $r = $this->_conn->_query($query);
        
        $clients = $this->_conn->_getData($r); 
		//print_r($clients);
        echo "Total clientes SF1: ".count($clients);         
                
        return $clients;

    } 

    public function getIdentificacion(){
    
    	$query = "SELECT * FROM identificaciones";
    	 
    	$r = $this->_conn->_query($query);
    
    	$identificaciones = $this->_conn->_getData($r);
    
    	return $identificaciones;
    
    }
    
    public function getPaises(){
    
    	$query1 = "SELECT pais_residencia FROM clientes
				  GROUP BY pais_residencia";
    
    	$query2 = "SELECT pais_trabaja FROM clientes
				   GROUP BY pais_trabaja";
    	
    	$query3 = "SELECT pais_co FROM clientes
				   GROUP BY pais_co";
    	    	    	
    	$r1 = $this->_conn->_query($query1);
    	$r2 = $this->_conn->_query($query2);
    	$r3 = $this->_conn->_query($query3);
    	
    	$paisesResidencia = $this->_conn->_getData($r1);
    	$paisesTrabaja = $this->_conn->_getData($r2);
    	$paisesCo = $this->_conn->_getData($r3);
    
    	$mergePaises = array_merge($paisesResidencia, $paisesTrabaja, $paisesCo);
    	$paises = array();
    	foreach ($mergePaises as $pais) {
    		$paises[] = $pais[0];
    	}
    
    	$paises = array_unique($paises);
    	
    	return $paises;
    }
    
    public function getCiudades(){
    
    	$query1 = "SELECT ciudad_residencia FROM clientes
				  GROUP BY ciudad_residencia";
    
    	$query2 = "SELECT ciudad_trabaja FROM clientes
				   GROUP BY ciudad_trabaja";
    	 
    	$query3 = "SELECT ciudad_co FROM clientes
				   GROUP BY ciudad_co";
    	 
    	$r1 = $this->_conn->_query($query1);
    	$r2 = $this->_conn->_query($query2);
    	$r3 = $this->_conn->_query($query3);
    	 
    	$ciudadesResidencia = $this->_conn->_getData($r1);
    	$ciudadesTrabaja = $this->_conn->_getData($r2);
    	$ciudadesCo = $this->_conn->_getData($r3);
    
    	$mergeCiudades = array_merge($ciudadesResidencia, $ciudadesTrabaja, $ciudadesCo);
    	$ciudades = array();
    	foreach ($mergeCiudades as $ciudad) {
    		$ciudades[] = $ciudad[0];
    	}
    
    	$ciudades = array_unique($ciudades);
    	 
    	//print_r($ciudades);
    	
    	return $ciudades;
    }

    public function getEstadoCivil(){
    
    	$query = "SELECT * FROM codes
				  WHERE id = 'estado_civil'";
    
    	$r = $this->_conn->_query($query);
    
    	$estadosCiviles = $this->_conn->_getData($r);
    
    	return $estadosCiviles;
    
    }
    
    public function getBancos(){
    
    	$query = "SELECT * FROM bancos
  				  ORDER BY id_banco";
    
    	$r = $this->_conn->_query($query);
    
    	$bancos = $this->_conn->_getData($r);
    
    	return $bancos;
    
    }
    
}