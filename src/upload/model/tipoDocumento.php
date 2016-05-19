<?php

namespace upload\model;
use upload\lib\data;

class tipoDocumento {

	private $_conn;
    private $_nosource = array('68');
       
    function __construct(data $conn) {
    	$this->_conn = $conn;
    }
    
    public function getTiposDeDocumento(){
    	 
    
    	$query = "SELECT * FROM tipo_doc 
				WHERE id_archivador = 'ARRIENDOS'
				ORDER BY nombre";
    
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    	//print_r($clients);
    
    
    	return $result;
    	
    	
    
    }
    
}