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
    	 
    
    	$query = "SELECT TOP 100 C.id_contrato, CI.id_inmueble, U.email, * FROM contratos AS C
				LEFT JOIN contratos_inmuebles AS CI ON C.id_contrato = CI.id_contrato
				LEFT JOIN cobradores AS CO ON C.id_cobrador = CO.id_cobrador
				LEFT JOIN usuarios AS U ON CO.usuario = U.id_user
				ORDER BY C.id_contrato DESC";
    
    	$r = $this->_conn->_query($query);
    	$clients = $this->_conn->_getData($r);
    	//print_r($clients);
    	echo "Total contratos SF1: ".count($clients);
    
    	return $clients;
    	
    	
    
    }
    
    
    public function getArrendatoriosPorContrato($idContrato){
    
    
    	$query = "SELECT * FROM clientes_contratos
					WHERE id_contrato = '".$idContrato."'";
    
    	$r = $this->_conn->_query($query);
    	$clients = $this->_conn->_getData($r);
    	//print_r($clients);
    	
    	//echo "\n".$query."\n";
    
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
    
    public function updateContratosArriendo(){
    
    	$currentDate = new \DateTime();
    	$currentDate = $currentDate->format('Y-m-d H:i:s');
    
    	$query = "SELECT C.id_contrato, CI.id_inmueble, U.email, * FROM contratos AS C
				LEFT JOIN contratos_inmuebles AS CI ON C.id_contrato = CI.id_contrato
				LEFT JOIN cobradores AS CO ON C.id_cobrador = CO.id_cobrador
				LEFT JOIN usuarios AS U ON CO.usuario = U.id_user
				WHERE tstamp > '".$currentDate."'";
    
    	$r = $this->_conn->_query($query);
    	$clients = $this->_conn->_getData($r);
    	//print_r($clients);
    	echo "Total contratos SF1: ".count($clients);
    
    	return $clients;
    	 
    	 
    
    }
    
    
    //*********** MONTERIA ***************/
    
    
    public function getContratosArriendoMonteria(){
    
    
    	$query = "SELECT C.id_contrato, CI.id_inmueble, U.email, * FROM contratos AS C
				LEFT JOIN contratos_inmuebles AS CI ON C.id_contrato = CI.id_contrato
				LEFT JOIN cobradores AS CO ON C.id_cobrador = CO.id_cobrador
				LEFT JOIN usuarios AS U ON CO.usuario = U.id_user
				ORDER BY C.id_contrato ASC";
    
    	$r = $this->_conn->_query($query);
    	$clients = $this->_conn->_getData($r);
    	//print_r($clients);
    	echo "Total contratos SF1: ".count($clients);
    
    	return $clients;
    	 
    	 
    
    }
    
    

}