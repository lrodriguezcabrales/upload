<?php

namespace upload\model;
use upload\lib\data;

class conveniosCartagena {

	private $_conn;
    private $_nosource = array('68');
       
    function __construct(data $conn) {
    	$this->_conn = $conn;
    }

	/**	
	 * Obtner los ultimos 10 convenios creados en sifinca1
	 * @return unknown
	 */
    public function getSoloConvenios() {
    
    	$currentDate = new \DateTime();
    	$currentDate = $currentDate->format('Ymd H:i:s');
    	
    	$query = "SELECT TOP 20 CON.id_convenio, CON.estado, CON.fecha_convenio, CON.fecha_inicio,
    			  CON.fecha_final, CON.fecha_retiro, U.email,
			   	  CON.FECHA_LOG FROM clientes_convenios AS CV
			  	  INNER JOIN clientes AS C ON CV.id_cliente = C.id_cliente
				  INNER JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
    			  LEFT JOIN usuarios AS U ON CON.id_eje_cuenta = U.id_user
				  ORDER BY id_convenio DESC";
    	    
    	
    	$r = $this->_conn->_query($query);
    
    	$result = $this->_conn->_getData($r);
    
    	return $result;
    }
    
	/**
	 * Buscar contratos de mandato en sifinca1
	 */
    public function getContratosDeMandato() {
    
    	$currentDate = new \DateTime();
    	 
    	$earlier = new \DateTime();
    	$earlier = $earlier->modify('-10 minutes');
    	 
    	$currentDate = $currentDate->format('Ymd H:i:s');
    	
    	$earlier = $earlier->format('Ymd H:i:s');
    	
    	$query = "SELECT I.id_inmueble, CON.id_convenio,IC.por_cmsi, IC.por_seguro,
					U.email AS emailCatcher, CV.tstamp, CON.FECHA_LOG
					FROM clientes_convenios AS CV
					INNER JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
					INNER JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
					INNER JOIN usuarios AS U ON I.id_promotor = U.id_user
					INNER JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
					WHERE CON.FECHA_LOG BETWEEN '$earlier' AND '$currentDate'
					ORDER BY I.id_inmueble DESC";
    	
//     	$query = "SELECT I.id_inmueble, CON.id_convenio,IC.por_cmsi, IC.por_seguro,
// 					U.email AS emailCatcher, CV.tstamp, CON.FECHA_LOG
// 					FROM clientes_convenios AS CV
// 					INNER JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
// 					INNER JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
// 					INNER JOIN usuarios AS U ON I.id_promotor = U.id_user
// 					INNER JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
// 					WHERE CON.FECHA_LOG > '20161001 00:00:00'
// 					ORDER BY I.id_inmueble DESC";
    	
//     	    	$query = "SELECT I.id_inmueble, CON.id_convenio,IC.por_cmsi, IC.por_seguro,
//     						U.email AS emailCatcher, CV.tstamp, CON.FECHA_LOG
//     						FROM clientes_convenios AS CV
//     						INNER JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
//     						INNER JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
//     						INNER JOIN usuarios AS U ON I.id_promotor = U.id_user
//     						INNER JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
//     						WHERE I.id_inmueble = '35486'
//     						ORDER BY I.id_inmueble DESC";
    	
    	 
    	echo "\n".$query."\n";
    	
    	$r = $this->_conn->_query($query);
    
    	$result = $this->_conn->_getData($r);
    
    	return $result;
    }
    
    
    /**
     * Obtner los convenios para crear sus propieatarios
     * @return unknown
     */
    public function getConveniosForPropietario() {
    
    	$currentDate = new \DateTime();
    	$currentDate = $currentDate->format('Ymd H:i:s');
    	 
    	$query = "SELECT TOP 50 CON.id_convenio, CON.estado, CON.fecha_convenio, CON.fecha_inicio,
    			  CON.fecha_final, CON.fecha_retiro, U.email,
			   	  CON.FECHA_LOG FROM clientes_convenios AS CV
			  	  INNER JOIN clientes AS C ON CV.id_cliente = C.id_cliente
				  INNER JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
    			  LEFT JOIN usuarios AS U ON CON.id_eje_cuenta = U.id_user
				  ORDER BY id_convenio DESC";
    
    	$r = $this->_conn->_query($query);
    
    	$result = $this->_conn->_getData($r);
    
    	return $result;
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
    	
    	//echo "\n".$query."\n";
    	
    	$r = $this->_conn->_query($query);
    	$propietarios = $this->_conn->_getData($r);
    	//print_r($clients);
    	//echo "Total convenios SF1: ".count($propietarios);
    	
    	return $propietarios;
    	
    	
    }
    
    
    public function getCliente($id) {
    
    	$query = "SELECT * FROM clientes
    			 WHERE id_cliente = '".$id."'";
    
    	//echo "\n".$query."\n";
    	
    	$r = $this->_conn->_query($query);
    
    	$cliente = $this->_conn->_getData($r);
    
    	//print_r($ciudades);
    
    	return $cliente;
    }
    
    
	/**
	 * Obtener los convenios recientemente modificados intervalo de 10 min
	 */
    public function getConveniosRecientes() {
    	    	
    	$currentDate = new \DateTime();
    	 
    	$earlier = new \DateTime();
    	$earlier = $earlier->modify('-10 minutes');
    	 
    	$currentDate = $currentDate->format('Ymd H:i:s');
    	
    	$earlier = $earlier->format('Ymd H:i:s');
    	
		$query = "SELECT CON.id_convenio, CON.estado, CON.fecha_convenio, CON.fecha_inicio,
		          CON.fecha_final, CON.fecha_retiro, U.email,
		          CON.FECHA_LOG FROM clientes_convenios AS CV
		          INNER JOIN clientes AS C ON CV.id_cliente = C.id_cliente
		          INNER JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
		          INNER JOIN usuarios AS U ON CON.id_eje_cuenta = U.id_user
		          WHERE CON.FECHA_LOG BETWEEN '$earlier' AND '$currentDate'
		          ORDER BY id_convenio DESC";
    	
		echo "\n".$query."\n";
		
    	$r = $this->_conn->_query($query);
    	
    	$result = $this->_conn->_getData($r);
    	
    	return $result;
    	
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
    
//     	$query = "SELECT CON.id_convenio, CON.estado, CON.fecha_convenio, CON.fecha_inicio,
// 					CON.fecha_final, CON.fecha_retiro, U.email,
// 					CON.FECHA_LOG FROM clientes_convenios AS CV
// 					LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
// 					LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
// 					INNER JOIN usuarios AS U ON CON.id_eje_cuenta = U.id_user
// 					ORDER BY id_convenio DESC";
    	 
    	$query = "SELECT CON.id_convenio, CON.estado, CON.fecha_convenio, CON.fecha_inicio,
					CON.fecha_final, CON.fecha_retiro, U.email,
					CON.FECHA_LOG FROM clientes_convenios AS CV
					LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
					LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
					INNER JOIN usuarios AS U ON CON.id_eje_cuenta = U.id_user
					WHERE CON.id_convenio = 10212";
    	
    	$r = $this->_conn->_query($query);
    
    	$cliente = $this->_conn->_getData($r);
        
    	return $cliente;
    }
    
    
    
    /******* MONTERIA *********/
    
    public function getSoloConveniosMonteria() {
    
    	$query = "SELECT TOP 50 CON.id_convenio, CON.estado, CON.fecha_convenio, CON.fecha_inicio,
    			  CON.fecha_final, CON.fecha_retiro, U.email,
			   	  CON.FECHA_LOG FROM clientes_convenios AS CV
			  	  INNER JOIN clientes AS C ON CV.id_cliente = C.id_cliente
				  INNER JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
    			  LEFT JOIN usuarios AS U ON CON.id_eje_cuenta = U.id_user
				  ORDER BY id_convenio DESC";

    
    	$r = $this->_conn->_query($query);
    
    	$cliente = $this->_conn->_getData($r);
    
    	return $cliente;
    }
    
    /**
     * Buscar contratos de mandato en sifinca1
     */
    public function getContratosDeMandatoMonteria() {
    
    	$currentDate = new \DateTime();
    
    	$earlier = new \DateTime();
    	$earlier = $earlier->modify('-10 minutes');
    
    	$currentDate = $currentDate->format('Ymd H:i:s');
    	 
    	$earlier = $earlier->format('Ymd H:i:s');
    	 
    	$query = "SELECT I.id_inmueble, CON.id_convenio,IC.por_cmsi, IC.por_seguro,
			    	U.email AS emailCatcher, CV.tstamp, CON.FECHA_LOG
			    	FROM clientes_convenios AS CV
			    	INNER JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
			    	INNER JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
			    	INNER JOIN usuarios AS U ON I.id_promotor = U.id_user
			    	INNER JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
			    	WHERE CON.FECHA_LOG BETWEEN '$earlier' AND '$currentDate'
			    	ORDER BY I.id_inmueble DESC";
    	 
//     	    	$query = "SELECT I.id_inmueble, CON.id_convenio,IC.por_cmsi, IC.por_seguro,
//     						U.email AS emailCatcher, CON.FECHA_LOG
//     						FROM clientes_convenios AS CV
//     						INNER JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
//     						INNER JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
//     						INNER JOIN usuarios AS U ON I.id_promotor = U.id_user
//     						INNER JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
//     						WHERE CON.FECHA_LOG > '20161101 00:00:00'
//     						ORDER BY I.id_inmueble DESC";
    	 

    
    	echo "\n".$query."\n";
    	 
    	$r = $this->_conn->_query($query);
    
    	$result = $this->_conn->_getData($r);
    
    	return $result;
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
    
    /**
     * Obtner los convenios para crear sus propieatarios
     * @return unknown
     */
    public function getConveniosForPropietarioMonteria() {
    
    	$currentDate = new \DateTime();
    	$currentDate = $currentDate->format('Ymd H:i:s');
    
    	$query = "SELECT top 50  CON.id_convenio, CON.estado, CON.fecha_convenio, CON.fecha_inicio,
    			  CON.fecha_final, CON.fecha_retiro, U.email,
			   	  CON.FECHA_LOG FROM clientes_convenios AS CV
			  	  INNER JOIN clientes AS C ON CV.id_cliente = C.id_cliente
				  INNER JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
    			  LEFT JOIN usuarios AS U ON CON.id_eje_cuenta = U.id_user
				  ORDER BY id_convenio DESC";
    
    	$r = $this->_conn->_query($query);
    
    	$result = $this->_conn->_getData($r);
    
    	return $result;
    }
    
    public function getPropietariosDelConvenioMonteria($idConvenio){
    	 
    	$query = "SELECT CON.id_convenio, I.id_inmueble,CV.id_cliente, tipo_relacion, participacion FROM clientes_convenios AS CV
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

    
    
    /********************* BOGOTA *****+*********/
    
    public function getSoloConveniosBogota() {
    
    	$query = "SELECT TOP 10 CON.id_convenio, CON.estado, CON.fecha_convenio, CON.fecha_inicio,
    			  CON.fecha_final, CON.fecha_retiro, U.email,
			   	  CON.FECHA_LOG FROM clientes_convenios AS CV
			  	  INNER JOIN clientes AS C ON CV.id_cliente = C.id_cliente
				  INNER JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
    			  LEFT JOIN usuarios AS U ON CON.id_eje_cuenta = U.id_user
				  ORDER BY CON.id_convenio DESC";


//     	    	$query = "SELECT CON.id_convenio, CON.estado, CON.fecha_convenio, CON.fecha_inicio,
//     	    			  CON.fecha_final, CON.fecha_retiro, U.email,
//     				   	  CON.FECHA_LOG FROM clientes_convenios AS CV
//     				  	  INNER JOIN clientes AS C ON CV.id_cliente = C.id_cliente
//     					  INNER JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
//     	    			  LEFT JOIN usuarios AS U ON CON.id_eje_cuenta = U.id_user
//     	    			  WHERE CON.id_convenio = 211
//     					  ORDER BY CON.id_convenio DESC";
    
    	$r = $this->_conn->_query($query);
    
    	$cliente = $this->_conn->_getData($r);
    
    	return $cliente;
    }
    
    /**
     * Buscar contratos de mandato en sifinca1
     */
    public function getContratosDeMandatoBogota() {
    
    	$currentDate = new \DateTime();
    
    	$earlier = new \DateTime();
    	$earlier = $earlier->modify('-5 minutes');
    
    	$currentDate = $currentDate->format('Ymd H:i:s');
    
    	$earlier = $earlier->format('Ymd H:i:s');
    
    	
    	$query = "SELECT I.id_inmueble, CON.id_convenio,IC.por_cmsi, IC.por_seguro,
    	U.email AS emailCatcher, CV.tstamp, CON.FECHA_LOG
    	FROM clientes_convenios AS CV
    	INNER JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
    	INNER JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
    	INNER JOIN usuarios AS U ON I.id_promotor = U.id_user
    	INNER JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
    	WHERE CON.FECHA_LOG BETWEEN '$earlier' AND '$currentDate'
    	ORDER BY I.id_inmueble DESC";
    
//     	    	    	$query = "SELECT I.id_inmueble, CON.id_convenio,IC.por_cmsi, IC.por_seguro,
//     	    						U.email AS emailCatcher, CON.FECHA_LOG
//     	    						FROM clientes_convenios AS CV
//     	    						INNER JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
//     	    						INNER JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
//     	    						INNER JOIN usuarios AS U ON I.id_promotor = U.id_user
//     	    						INNER JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
//     	    						WHERE CON.FECHA_LOG > '20161001 00:00:00'
//     	    						ORDER BY I.id_inmueble DESC";
    
    
    
    	echo "\n".$query."\n";
    
    	$r = $this->_conn->_query($query);
    
    	$result = $this->_conn->_getData($r);
    
    	return $result;
    }
        
    public function getContratoMandatoDelConvenioBogota($idConvenio){
    
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
    
    public function getPropietariosDelConvenioBogota($idConvenio){
    
//     	$query = "SELECT CON.id_convenio, I.id_inmueble,CV.id_cliente, C.nombre, tipo_relacion, participacion 
//     			  FROM clientes_convenios AS CV
// 				  LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
// 				  LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
// 				  LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
// 				  LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user
// 				  LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
// 				  WHERE CON.id_convenio = '".$idConvenio."'
// 				  AND CON.id_convenio IS NOT NULL
// 				  AND I.id_inmueble IS NOT NULL";
    	
    	$query = "SELECT CON.id_convenio, I.id_inmueble,CV.id_cliente, C.nombre, tipo_relacion, participacion 
    			  FROM clientes_convenios AS CV
				  LEFT JOIN clientes AS C ON CV.id_cliente = C.id_cliente
				  LEFT JOIN inmuebles_convenios AS IC ON CV.id_convenio = IC.id_convenio
				  LEFT JOIN inmuebles AS I ON IC.id_inmueble = I.id_inmueble
				  LEFT JOIN usuarios AS U ON I.id_promotor = U.id_user
				  LEFT JOIN convenios AS CON ON CV.id_convenio = CON.id_convenio
				  WHERE CON.id_convenio = '211'
				  AND CON.id_convenio IS NOT NULL
				  AND I.id_inmueble IS NOT NULL";
    
    	echo "\n".$query."\n";
    
    	$r = $this->_conn->_query($query);
    	$propietarios = $this->_conn->_getData($r);
    	//print_r($clients);
    	//echo "Total convenios SF1: ".count($propietarios);
    
    	return $propietarios;
    
    
    }
    
}