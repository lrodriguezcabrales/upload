<?php

namespace upload\model;
use upload\lib\data;

class comentariosInmuebleCartagena {

	private $_conn;
    private $_nosource = array('68');
       
    function __construct(data $conn) {
    	$this->_conn = $conn;
    }
    
    /**
     * Obtener los ultimos 10 inmuebles registrados en sifinca1
     * @return unknown
     */
    public function getInmuebles(){
    
//     	$query = "SELECT TOP 50 id_inmueble FROM inmuebles
//     			  ORDER BY id_inmueble DESC";
    	 
    	$query = "SELECT id_inmueble FROM inmuebles
    			  WHERE id_inmueble = 5
    			  ORDER BY id_inmueble DESC";
    	
    	//echo "\n".$query."\n";
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    	//print_r($clients);
    	echo "Total inmuebles inmuebles SF1: ".count($result)."\n";
    
    	return $result;
    
    }
    
    public function getComentariosPorInmueble($inmueble){
    
    
    	$query = "SELECT U.email, * FROM COMENTARIOS AS C
		          INNER JOIN usuarios AS U ON C.id_user = U.id_user
		          WHERE tipo = 'I'
    			  AND clave = $inmueble
		          ORDER BY fecha ASC";
    
    	$r = $this->_conn->_query($query);
    	$clients = $this->_conn->_getData($r);
    	//print_r($clients);
    	echo "Total contratos SF1: ".count($clients);
    
    	return $clients;
    	 
    	 
    
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