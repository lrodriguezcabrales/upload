<?php

namespace upload\model;
use upload\lib\data;

class conveniosCartagena {

	private $_conn;
    private $_nosource = array('68');
       
    function __construct(data $conn) {
    	$this->_conn = $conn;
    }

    public function getClientesConvenios(){
        	    

    	$query = "SELECT TOP 7 * FROM clientes_convenios AS CV
  				  LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente";

        $r = $this->_conn->_query($query);
        $clients = $this->_conn->_getData($r);
        //print_r($clients);
        echo "Total clientes SF1: ".count($clients);
    
        return $clients;
    
    }
    
    public function getConvenioAll(){
    	 
    
    	$query = "SELECT TOP 22000 CV.id_cliente, IC.id_inmueble, CON.id_convenio, CON.estado ,IC.por_cmsi,  U.email AS emailCatcher, * FROM clientes_convenios AS CV
  LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
  LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
  LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
  LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user 
  LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio 
  WHERE IC.id_inmueble IS NOT NULL
  ORDER BY IC.id_inmueble";
//     	$query = "SELECT TOP 30000 CV.id_cliente, IC.id_inmueble, CON.id_convenio, CON.estado ,IC.por_cmsi,  U.email AS emailCatcher, * FROM clientes_convenios AS CV
//   LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
//   LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
//   LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
//   LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user 
//   LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio 
//   WHERE IC.id_inmueble IS NOT NULL
//   ORDER BY IC.id_inmueble";

//     	$query = "  SELECT CV.id_cliente, IC.id_inmueble, CON.id_convenio, CON.estado ,IC.por_cmsi,  U.email AS emailCatcher, * FROM clientes_convenios AS CV
//   LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
//   LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
//   LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
//   LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user 
//   LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio 
//   WHERE IC.id_inmueble IS NOT NULL
//   AND CON.id_convenio = '8030'
//   ORDER BY IC.id_inmueble";
    
    	$r = $this->_conn->_query($query);
    	$clients = $this->_conn->_getData($r);
    	//print_r($clients);
    	echo "Total convenios SF1: ".count($clients);
    
    	return $clients;
    
    }
    
    public function getCliente($id) {
    
    	$query = "SELECT * FROM clientes
    			 WHERE id_cliente = '".$id."'";
    
    	$r = $this->_conn->_query($query);
    
    	$cliente = $this->_conn->_getData($r);
    
    	//print_r($ciudades);
    
    	return $cliente;
    }
}