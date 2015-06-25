<?php

namespace upload\model;
use upload\lib\data;
class identificacion {
	private $_conn;
    private $_nosource = array('68');
       
    function __construct(data $conn) {
    	$this->_conn = $conn;
    }
    
    public function getIds(){
    	$query="SELECT * FROM TipoIdentificacion";
    	$r = $this->_conn->_query($query);
        $data = $this->_conn->_getData($r); 
        return $data;
    }    
}