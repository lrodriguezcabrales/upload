<?php

namespace upload\model;
use upload\lib\data;

class comentariosInmuebleCartagena {

	private $_conn;
    private $_nosource = array('68');
       
    function __construct(data $conn) {
    	$this->_conn = $conn;
    }
    
    public function getComentarios(){
    	 
    
    	$query = "SELECT U.email, * FROM COMENTARIOS AS C
  LEFT JOIN usuarios AS U ON C.id_user = U.id_user
  WHERE tipo = 'I'
  ORDER BY fecha ASC";
    
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