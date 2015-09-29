<?php

namespace upload\model;
use upload\lib\data;

class inmueblesCartagena {

	private $_conn;
    private $_nosource = array('68');
       
    function __construct(data $conn) {
    	$this->_conn = $conn;
    }

    public function getInmuebles(){
        	 
    	$query = "SELECT TOP 1 * FROM inmuebles
					WHERE id_barrio = '0004'";
   
        $r = $this->_conn->_query($query);
        $clients = $this->_conn->_getData($r);
        //print_r($clients);
        echo "Total clientes SF1: ".count($clients);
    
        return $clients;
    
    }
    
    public function getTiposInmuebles(){
    
    	$query = "SELECT * FROM identificaciones";
    
    	$r = $this->_conn->_query($query);
    
    	$identificaciones = $this->_conn->_getData($r);
    
    	return $identificaciones;
    
    }
    
    public function getCiudades(){
    
    	$query = "SELECT * FROM ciudades";
    
    	$r = $this->_conn->_query($query);
    
    	$ciudades = $this->_conn->_getData($r);
    
    	print_r($ciudades);
    	 
    	return $ciudades;
    }
    
    public function getBarrios(){
    
    	$query = "SELECT * FROM barrios";
    
    	$r = $this->_conn->_query($query);
    
    	$ciudades = $this->_conn->_getData($r);
    
    	//print_r($ciudades);
    
    	return $ciudades;
    }
}