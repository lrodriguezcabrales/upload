<?php

namespace upload\model;
use upload\lib\data;

class comentariosContratosArriendos {

	private $_conn;
    private $_nosource = array('68');
       
    function __construct(data $conn) {
    	$this->_conn = $conn;
    }
    
    public function getContratosArriendo(){
    
    
    	$query = "SELECT TOP 5000 C.id_contrato, CI.id_inmueble, U.email, * FROM contratos AS C
				LEFT JOIN contratos_inmuebles AS CI ON C.id_contrato = CI.id_contrato
				LEFT JOIN cobradores AS CO ON C.id_cobrador = CO.id_cobrador
				LEFT JOIN usuarios AS U ON CO.usuario = U.id_user
				ORDER BY C.id_contrato ASC";
    		
//     	$query = "SELECT C.id_contrato, CI.id_inmueble, U.email, * FROM contratos AS C
// 				LEFT JOIN contratos_inmuebles AS CI ON C.id_contrato = CI.id_contrato
// 				LEFT JOIN cobradores AS CO ON C.id_cobrador = CO.id_cobrador
// 				LEFT JOIN usuarios AS U ON CO.usuario = U.id_user
// 				where CI.id_contrato = '21'
// 				ORDER BY C.id_contrato DESC";
    	 
    	//echo "\n".$query."\n";
    	    	    	
    	$r = $this->_conn->_query($query);
    	$clients = $this->_conn->_getData($r);
    	//print_r($clients);
    	echo "Total contratos SF1: ".count($clients);
    
    	return $clients;
    	 
    	 
    
    }
    
    public function getComentariosContratosArriendo($idContrato){
    
    
    	$query = "SELECT U.email, CAST(comentario AS TEXT) AS comentarioAll, * FROM COMENTARIOS AS C
				  LEFT JOIN usuarios AS U ON C.id_user = U.id_user
				  WHERE tipo = 'C'
				  AND clave = ".$idContrato."
				  ORDER BY fecha ASC";
    
    	//echo "\n".$query."\n";
    	 
    	$r = $this->_conn->_query($query);
    	$clients = $this->_conn->_getData($r);
    	//print_r($clients);
    	//echo "Total comentarios: ".count($clients);
    
    	return $clients;
    
    
    
    }
    
}