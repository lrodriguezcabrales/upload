<?php

namespace upload\model;
use upload\lib\data;

class conveniosCartagena {

	private $_conn;
    private $_nosource = array('68');
       
    function __construct(data $conn) {
    	$this->_conn = $conn;
    }

    public function getClientesConvenios(){
        	    

    	$query = "SELECT TOP 7 * FROM clientes_convenios AS CV
  				  LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente";

        $r = $this->_conn->_query($query);
        $clients = $this->_conn->_getData($r);
        //print_r($clients);
        echo "Total clientes SF1: ".count($clients);
    
        return $clients;
    
    }
    
    public function getConvenioAll(){
    	 
    
    	$query = "SELECT CV.id_cliente, IC.id_inmueble, CON.id_convenio, CON.estado ,IC.por_cmsi,  U.email AS emailCatcher, * FROM clientes_convenios AS CV
  LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
  LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
  LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
  LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user 
  LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio 
  WHERE IC.id_inmueble IS NOT NULL
  ORDER BY IC.id_inmueble";
//     	$query = "SELECT TOP 30000 CV.id_cliente, IC.id_inmueble, CON.id_convenio, CON.estado ,IC.por_cmsi,  U.email AS emailCatcher, * FROM clientes_convenios AS CV
//   LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
//   LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
//   LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
//   LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user 
//   LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio 
//   WHERE IC.id_inmueble IS NOT NULL
//   ORDER BY IC.id_inmueble";

//     	$query = "  SELECT CV.id_cliente, IC.id_inmueble, CON.id_convenio, CON.estado ,IC.por_cmsi,  U.email AS emailCatcher, * FROM clientes_convenios AS CV
//   LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
//   LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
//   LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
//   LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user 
//   LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio 
//   WHERE IC.id_inmueble IS NOT NULL
//   AND CON.id_convenio = '8030'
//   ORDER BY IC.id_inmueble";
    
    	$r = $this->_conn->_query($query);
    	$clients = $this->_conn->_getData($r);
    	//print_r($clients);
    	echo "Total convenios SF1: ".count($clients);
    
    	return $clients;
    
    }
    
    public function getConvenioDisponibles(){
    
    	$query = "SELECT CV.id_cliente, IC.id_inmueble, CON.id_convenio, CON.estado ,IC.por_cmsi, 
  U.email AS emailCatcher, * FROM clientes_convenios AS CV
  LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
  LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
  LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
  LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user 
  LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio 
  AND I.promocion = 1
  ORDER BY CON.FECHA_LOG DESC";
    	
    	
//     	$query = "SELECT CV.id_cliente, IC.id_inmueble, CON.id_convenio, CON.estado ,IC.por_cmsi,  U.email AS emailCatcher, * FROM clientes_convenios AS CV
//   LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
//   LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
//   LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
//   LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user
//   LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
//   WHERE IC.id_inmueble IS NOT NULL
//   AND I.promocion = 1
//   ORDER BY IC.id_inmueble";

    	
    
    	$r = $this->_conn->_query($query);
    	$clients = $this->_conn->_getData($r);
    	//print_r($clients);
    	//echo "Total convenios SF1: ".count($clients);
    
    	return $clients;
    
    }
    
    public function getPropietariosDelConvenio($idConvenio){
    	
    	$query = " SELECT CON.id_convenio, I.id_inmueble,CV.id_cliente, tipo_relacion, participacion FROM clientes_convenios AS CV
  LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
  LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
  LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
  LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user 
  LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio 
  WHERE CON.id_convenio = '".$idConvenio."'
  AND CON.id_convenio IS NOT NULL
  AND I.id_inmueble IS NOT NULL";
    	
    	
    	$r = $this->_conn->_query($query);
    	$propietarios = $this->_conn->_getData($r);
    	//print_r($clients);
    	//echo "Total convenios SF1: ".count($propietarios);
    	
    	return $propietarios;
    	
    	
    }
    
    
    public function getCliente($id) {
    
    	$query = "SELECT * FROM clientes
    			 WHERE id_cliente = '".$id."'";
    
    	$r = $this->_conn->_query($query);
    
    	$cliente = $this->_conn->_getData($r);
    
    	//print_r($ciudades);
    
    	return $cliente;
    }
    
    
    public function getSoloConvenios() {
    
    	$query = "SELECT TOP 20 CON.id_convenio, CON.estado, CON.fecha_convenio, CON.fecha_inicio,
    				CON.fecha_final, CON.fecha_retiro, 
			   		CON.FECHA_LOG FROM clientes_convenios AS CV
			  		LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
					LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
					ORDER BY id_convenio DESC";
    	
//     	$query = "SELECT TOP 50 CV.id_cliente, IC.id_inmueble, CON.id_convenio, CON.estado ,IC.por_cmsi, 
//    U.email AS emailCatcher, CON.fecha_convenio, CON.fecha_inicio, CON.fecha_final, CON.fecha_retiro, CON.FECHA_LOG FROM clientes_convenios AS CV
//   LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
//   LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
//   LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
//   LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user
//   LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
//   --WHERE I.promocion = 1
//   ORDER BY id_convenio DESC";
    
    	$r = $this->_conn->_query($query);
    
    	$cliente = $this->_conn->_getData($r);
    
    	//print_r($ciudades);¼
    
    	return $cliente;
    }
    
    public function getContratosMandatoConvenios() {
    
    	$query = "SELECT TOP 1000 CON.id_convenio,IC.por_cmsi, IC.por_seguro, I.id_inmueble, U.email AS emailCatcher FROM clientes_convenios AS CV
  LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
  LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
  LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
  LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user
  LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
  ORDER BY CON.id_convenio DESC";
    
//     	$query = "SELECT CON.id_convenio,IC.por_cmsi, IC.por_seguro, I.id_inmueble, U.email AS emailCatcher FROM clientes_convenios AS CV
//   LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
//   LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
//   LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
//   LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user
//   LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
//   --WHERE I.promocion = 1
//   ORDER BY CON.id_convenio";
    	
    	$r = $this->_conn->_query($query);
    
    	$cliente = $this->_conn->_getData($r);
    
    	//print_r($ciudades);
    
    	return $cliente;
    }
    
    public function getContratoMandatoDelConvenio($idConvenio){
    	 
    	$query = "SELECT CON.id_convenio,IC.por_cmsi, IC.por_seguro, I.id_inmueble, U.email AS emailCatcher FROM clientes_convenios AS CV
  LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
  LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
  LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
  LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user
  LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
  WHERE CON.id_convenio = '".$idConvenio."'
  AND I.id_inmueble IS NOT NULL";
    	 
    	//echo "\n".$query."\n";
    	$r = $this->_conn->_query($query);
    	$propietarios = $this->_conn->_getData($r);
    	//print_r($clients);
    	//echo "Total convenios SF1: ".count($propietarios);
    	 
    	return $propietarios;
    	 
    	 
    }
    
    
    public function updateConvenios() {
    
    	$query = "SELECT TOP 5 CON.id_convenio, CON.estado, CON.fecha_convenio, CON.fecha_inicio,
CON.fecha_final, CON.fecha_retiro, U.email,
CON.FECHA_LOG FROM clientes_convenios AS CV
LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
LEFT JOIN usuarios AS U ON CON.id_eje_cuenta = U.id_user
ORDER BY id_convenio DESC";
    	 
    
    	$r = $this->_conn->_query($query);
    
    	$cliente = $this->_conn->_getData($r);
        
    	return $cliente;
    }
    
    
    
    /******* MONTERIA *********/
    
    public function getSoloConveniosMonteria() {
    
    	$query = "SELECT CON.id_convenio, CON.estado, CON.fecha_convenio, CON.fecha_inicio,
    				CON.fecha_final, CON.fecha_retiro,
			   		CON.FECHA_LOG FROM clientes_convenios AS CV
			  		LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
					LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
					ORDER BY id_convenio ASC";
    	 
    
    	$r = $this->_conn->_query($query);
    
    	$cliente = $this->_conn->_getData($r);
    
    	return $cliente;
    }
    
    public function getContratoMandatoDelConvenioMonteria($idConvenio){
    
    	$query = "SELECT CON.id_convenio,IC.por_cmsi, IC.por_seguro, I.id_inmueble, U.email AS emailCatcher FROM clientes_convenios AS CV
  LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
  LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
  LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
  LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user
  LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
  WHERE CON.id_convenio = '".$idConvenio."'
  AND I.id_inmueble IS NOT NULL";
    
    	//echo "\n".$query."\n";
    	$r = $this->_conn->_query($query);
    	$propietarios = $this->_conn->_getData($r);
    	//print_r($clients);
    	//echo "Total convenios SF1: ".count($propietarios);
    
    	return $propietarios;
    
    
    }
    
    public function getPropietariosDelConvenioMonteria($idConvenio){
    	 
    	$query = " SELECT CON.id_convenio, I.id_inmueble,CV.id_cliente, tipo_relacion, participacion FROM clientes_convenios AS CV
  LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
  LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
  LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
  LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user
  LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
  WHERE CON.id_convenio = '".$idConvenio."'
  AND CON.id_convenio IS NOT NULL
  AND I.id_inmueble IS NOT NULL";
    	 
    	
//     	$query = " SELECT CON.id_convenio, I.id_inmueble,CV.id_cliente, tipo_relacion, participacion FROM clientes_convenios AS CV
//   LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
//   LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
//   LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
//   LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user
//   LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
//   WHERE CON.id_convenio = '5319'
//   AND CON.id_convenio IS NOT NULL
//   AND I.id_inmueble IS NOT NULL";
    	
    	 
    	$r = $this->_conn->_query($query);
    	$propietarios = $this->_conn->_getData($r);
    	//print_r($clients);
    	//echo "Total convenios SF1: ".count($propietarios);
    	 
    	return $propietarios;
    	 
    	 
    }
}