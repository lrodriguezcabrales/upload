<?php

namespace upload\model;
use upload\lib\data;

class ventas {

	private $_conn;
    private $_nosource = array('68');
       
    function __construct(data $conn) {
    	$this->_conn = $conn;
    }
    
    public function getVentas(){
    	     
    	$query = "SELECT V.id_log_venta, V.id_sucursal, V.fecha_venta, V.valor_venta, 
				  V.id_inmueble, V.id_comprador, V.comision, V.estado, U.email
				  FROM log_ventas AS V
				  INNER JOIN usuarios AS U ON V.id_promotor = U.id_user
				  ORDER BY id_log_venta ASC";
    
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    	//print_r($clients);
        
    	return $result;
    	    
    }
}