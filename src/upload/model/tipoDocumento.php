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
    	 
    
//     	$query = "SELECT * FROM tipo_doc 
// 				WHERE id_archivador = 'ARRIENDOS'
// 				ORDER BY nombre";
    
    	$query = "SELECT * FROM tipo_doc
					where id_archivador = 'ARRIENDOS' OR id_archivador = 'THUMANO'
					ORDER BY id DESC";
    	
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    	//print_r($clients);
    
    
    	return $result;
    	
    	
    
    }
    
    /********************* MONTERIA ********************/
    public function getTiposDeDocumentoMonteria(){
    
    
    	//     	$query = "SELECT * FROM tipo_doc
    	// 				WHERE id_archivador = 'ARRIENDOS'
    	// 				ORDER BY nombre";
    
    	$query = "SELECT * FROM tipo_doc
				WHERE id_archivador = 'RRHH'
				ORDER BY nombre";
    	 
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    	//print_r($clients);
    
    
    	return $result;
    	 
    	 
    
    }
    
}