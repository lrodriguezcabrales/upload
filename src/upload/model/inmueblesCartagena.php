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
        	    
//     	$query = "SELECT TOP 5 * FROM inmuebles
// 					WHERE id_ciudad = 'CTG'";
//     	$query = "SELECT TOP 21661 * FROM inmuebles
//  				  WHERE id_ciudad = 'CTG'
//     			  AND (id_edificio IS NULL OR id_edificio = '')";


    	$query = "SELECT TOP 1 * FROM inmuebles
  			      WHERE id_ciudad = 'CTG'
     			  AND (id_edificio IS NOT NULL)";

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
    
    
    public function getCaracteristasDeInmueble($inmueble) {
    	
    	//print_r($inmueble);
    	
    	$query = "SELECT * FROM caracteristicas_inmueble
				  WHERE id_inmueble = ".$inmueble['id_inmueble'];
    	
    	$r = $this->_conn->_query($query);
    	
    	$caracteristicas = $this->_conn->_getData($r);
    	    	
    	return $caracteristicas;
    }
    
    
    public function getServiciosDeInmueble($inmueble) {
    	 
    	 
    	$query = "SELECT * FROM servicios
				  WHERE id_inmueble = ".$inmueble['id_inmueble'];
    	 
    	$r = $this->_conn->_query($query);
    	 
    	$servicios = $this->_conn->_getData($r);
    
    	return $servicios;
    }
    
    public function getFotosDeInmueble($inmueble) {
        	
    	//echo "\naqui\n";
    	
    	$query = "SELECT id, tipo, descripcion, nkey, ext, fecha, publicar, publicada, 
    			  fecha_publicada, orden, carpeta, slot, id_user
    			  FROM fotos
    			  WHERE(id = ".$inmueble['consecutive'].") AND (tipo = 'I')";
    
    	//echo "\n".$query."\n";
    	
    	$r = $this->_conn->_query($query);
    
    	$fotos = $this->_conn->_getData($r);
    
    	return $fotos;
    }

    public function getEdificios() {
    	
    	$query = "SELECT * FROM edificios";
    	
    	$r = $this->_conn->_query($query);
    	
    	$edificios = $this->_conn->_getData($r);
    	
    	//print_r($ciudades);
    	
    	return $edificios;
    }
    
    public function getCliente($id) {
    	 
    	$query = "SELECT * FROM clientes
    			 WHERE id_cliente = '".$id."'";
    	 
    	$r = $this->_conn->_query($query);
    	 
    	$cliente = $this->_conn->_getData($r);
    	 
    	//print_r($ciudades);
    	 
    	return $cliente;
    }

}