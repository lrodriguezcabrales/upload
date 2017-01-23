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
    			  ORDER BY id_inmueble DESC";
    	
//     	$query = "SELECT TOP 50 id_inmueble FROM inmuebles
//     			  where id_inmueble = '28438'
//     	          ORDER BY id_inmueble DESC";
    	
    	//echo "\n".$query."\n";
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    	//print_r($clients);
    	//echo "Total inmuebles inmuebles SF1: ".count($result)."\n";
    
    	return $result;
    
    }
    
    public function getInmueblesRecientes(){
    

    	$currentDate = new \DateTime();
    	
    	$earlier = new \DateTime();
    	$earlier = $earlier->modify('-15 minutes');
    	
    	$currentDate = $currentDate->format('Ymd H:i:s');
    	
    	$earlier = $earlier->format('Ymd H:i:s');
    	
    	$query = "SELECT id_inmueble FROM inmuebles
    	          WHERE tstamp BETWEEN '$earlier' AND '$currentDate'
    	          ORDER BY tstamp ASC";
    	
    	
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    	
    	return $result;
       
    }
    
    
    public function getComentariosPorInmueble($inmueble){
        
    	$query = "SELECT U.email, * FROM COMENTARIOS AS C
		          INNER JOIN usuarios AS U ON C.id_user = U.id_user
		          WHERE tipo = 'I'
    			  AND clave = $inmueble
		          ORDER BY fecha ASC";
    
//     	$query = "SELECT TOP 1 U.email, * FROM COMENTARIOS AS C
//     	INNER JOIN usuarios AS U ON C.id_user = U.id_user
//     	WHERE tipo = 'I'
//     	AND clave = 5
//     	ORDER BY fecha ASC";
    	
    	$r = $this->_conn->_query($query);
    	$clients = $this->_conn->_getData($r);
    	//print_r($clients);
    
    	return $clients;
    	 
    	 
    
    }
    
    /**
     * Comentarios de Inmueble
     * @return unknown
     */
    public function getComentarios(){
    	 
    
    	$query = "SELECT TOP 70000 U.email, * FROM COMENTARIOS AS C
		  LEFT JOIN usuarios AS U ON C.id_user = U.id_user
		  WHERE tipo = 'I'
		  ORDER BY fecha ASC";
    
    	$r = $this->_conn->_query($query);
    	$clients = $this->_conn->_getData($r);
    	//print_r($clients);
    	echo "Total: ".count($clients);
    
    	return $clients;
    	
    	
    
    }
       
    ///***************************** MONTERIA ***********************************//
    
    /**
     * Obtener los ultimos 10 inmuebles registrados en sifinca1
     * @return unknown
     */
    public function getInmueblesMonteria(){
    
    	//73400
    	//     	$query = "SELECT TOP 50 id_inmueble FROM inmuebles
    	//     			  ORDER BY id_inmueble DESC";
    
    	$query = "SELECT TOP 30000 id_inmueble FROM inmuebles
    			  ORDER BY id_inmueble DESC";
    	 
    	//     	$query = "SELECT TOP 50 id_inmueble FROM inmuebles
    	//     			  where id_inmueble = '28438'
    	//     	          ORDER BY id_inmueble DESC";
    	 
    	//echo "\n".$query."\n";
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    	//print_r($clients);
    	//echo "Total inmuebles inmuebles SF1: ".count($result)."\n";
    
    	return $result;
    
    }
    
}