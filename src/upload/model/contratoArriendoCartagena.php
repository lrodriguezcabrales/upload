<?php

namespace upload\model;
use upload\lib\data;

class contratoArriendoCartagena {

	private $_conn;
    private $_nosource = array('68');
       
    function __construct(data $conn) {
    	$this->_conn = $conn;
    }
    
    public function getContratosArriendo(){
    	 
    
    	$query = "SELECT C.id_contrato, CI.id_inmueble, U.email, * FROM contratos AS C
				LEFT JOIN contratos_inmuebles AS CI ON C.id_contrato = CI.id_contrato
				LEFT JOIN cobradores AS CO ON C.id_cobrador = CO.id_cobrador
				LEFT JOIN usuarios AS U ON CO.usuario = U.id_user
				ORDER BY C.id_contrato";
    
    	$r = $this->_conn->_query($query);
    	$clients = $this->_conn->_getData($r);
    	//print_r($clients);
    	echo "Total contratos SF1: ".count($clients);
    
    	return $clients;
    	
    	
    
    }
    
    
    public function getArrendatoriosPorContrato($idCliente){
    
    
    	$query = "SELECT * FROM clientes_contratos
					WHERE id_contrato = '".$idCliente."'";
    
    	$r = $this->_conn->_query($query);
    	$clients = $this->_conn->_getData($r);
    	//print_r($clients);
    	
    
    	return $clients;
    	 
    	 
    
    }
    
    
    

}