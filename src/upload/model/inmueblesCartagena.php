<?php

namespace upload\model;
use upload\lib\data;

class inmueblesCartagena {

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

    	$query = "SELECT TOP 10 *, CAST(linderos AS TEXT) AS linderosAll, CAST(texto_inmu AS TEXT) AS descripcionAll 
    			  FROM inmuebles
    			  ORDER BY id_inmueble DESC";

    	 
    	
   		echo "\n".$query."\n";
        $r = $this->_conn->_query($query);
        $result = $this->_conn->_getData($r);
        //print_r($clients);
        echo "Total clientes inmuebles SF1: ".count($result)."\n";
    
        return $result;
    
    } 
    
    /**	
     * Inmuebles actaulizados en el dia
     */
    public function getInmueblesUpdatedDay(){
    	 
    	$currentDate = new \DateTime();
    	//echo "\n Fecha final: ".$currentDate->format('Y-m-d H:i:s')."\n";
    	$currentDate = $currentDate->format('Ymd');
    
    	$currentDate = $currentDate." 00:00:00";
		
      	$currentDate = "20161101 00:00:00";
    	
    	$query = "SELECT tstamp as fecha_actualizacion, *, CAST(linderos AS TEXT) AS linderosAll, 
				  CAST(texto_inmu AS TEXT) AS descripcionAll
    			  FROM inmuebles
    			  WHERE tstamp > '$currentDate'
    			  ORDER BY tstamp DESC";
    	
//     	$query = "SELECT tstamp as fecha_actualizacion, *, CAST(linderos AS TEXT) AS linderosAll,
//     	CAST(texto_inmu AS TEXT) AS descripcionAll
//     	FROM inmuebles
//     			WHERE id_inmueble = '35954'
//     	ORDER BY id_inmueble DESC";
    
//     	$query = "SELECT tstamp as fecha_actualizacion, *, CAST(linderos AS TEXT) AS linderosAll,
//      	CAST(texto_inmu AS TEXT) AS descripcionAll
//      	FROM inmuebles
//      	WHERE id_edificio = '900864910'
//      	ORDER BY tstamp DESC";
    	
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    
    	return $result;
    
    }
    
    
    /**
     * Inmuebles recientemente actualizados 5 min
     */
    public function getInmueblesUpdateReciente(){
    	 
    	$currentDate = new \DateTime();
    	
    	$earlier = new \DateTime();
    	$earlier = $earlier->modify('-10 minutes');
    	
    	$currentDate = $currentDate->format('Ymd H:i:s');
    
    	$earlier = $earlier->format('Ymd H:i:s');
    	    	
    	$query = "SELECT tstamp as  t, *, CAST(linderos AS TEXT) AS linderosAll, 
				  CAST(texto_inmu AS TEXT) AS descripcionAll 
                  FROM inmuebles
                  WHERE tstamp BETWEEN '$earlier' AND '$currentDate'
                  ORDER BY tstamp ASC";

    	echo "\n".$query."\n";
    	
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);    	
    
    	return $result;
    
    }
    
    /**
     * Obtnener todos los inmuebles a los que se van a cargar fotos
     */
    public function getInmueblesFotos(){
    
    	$query = "SELECT TOP 100 tstamp as  t, *, CAST(linderos AS TEXT) AS linderosAll, CAST(texto_inmu AS TEXT) AS descripcionAll
    	FROM inmuebles
    	ORDER BY id_inmueble DESC";
    
    
    	//echo "\n".$query."\n";
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    	//print_r($clients);
    	echo "Total inmuebles: ".count($result)."\n";
    
    	return $result;
    
    }
    
    public function getInmueblesFotosRecientes(){
    
    	
    	$currentDate = new \DateTime();
    	 
    	$earlier = new \DateTime();
    	$earlier = $earlier->modify('-30 minutes');
    	 
    	$currentDate = $currentDate->format('Ymd H:i:s');
    	
    	$earlier = $earlier->format('Ymd H:i:s');
    	
    	$query = "SELECT tstamp as  t, *, CAST(linderos AS TEXT) AS linderosAll, CAST(texto_inmu AS TEXT) AS descripcionAll
    			  FROM inmuebles
    			  WHERE tstamp BETWEEN '$earlier' AND '$currentDate'
    			  ORDER BY tstamp ASC";
        	
    	//echo "\n".$query."\n";
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    	//print_r($clients);
    	echo "Total inmuebles: ".count($result)."\n";
    
    	return $result;
    
    }
    
    
    /**
     * Fotos de un inmueble
     */ 
    public function getFotosDeInmueble($inmueble) {
        
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
    
    

    public function getEdificio($id) {
    	 
    	$query = "SELECT * FROM edificios
    			  WHERE id_edificio = '".$id."'";
    	 
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

    
    
    /****************** Monteria *********************/
    
    public function getInmueblesMonteria(){
    
    	$query = "SELECT TOP 10 *, CAST(linderos AS TEXT) AS linderosAll, CAST(texto_inmu AS TEXT) AS descripcionAll 
    			  FROM inmuebles
    			  ORDER BY id_inmueble DESC";
    	
    	
   		echo "\n".$query."\n";
        $r = $this->_conn->_query($query);
        $result = $this->_conn->_getData($r);
        //print_r($clients);
        echo "Total clientes inmuebles SF1: ".count($result)."\n";
    
        return $result;
    
    }
    
    public function getInmueblesUpdateRecienteMonteria(){
    
    	$currentDate = new \DateTime();
    	 
    	$earlier = new \DateTime();
    	$earlier = $earlier->modify('-10 minutes');
    	 
    	$currentDate = $currentDate->format('Ymd H:i:s');
    	
    	$earlier = $earlier->format('Ymd H:i:s');
    	
    	$query = "SELECT tstamp as  t, *, CAST(linderos AS TEXT) AS linderosAll,
    	CAST(texto_inmu AS TEXT) AS descripcionAll
    	FROM inmuebles
    	WHERE tstamp BETWEEN '$earlier' AND '$currentDate'
    	ORDER BY tstamp ASC";
    	
    	echo "\n".$query."\n";
    	 
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    	
    	return $result;
    
    }
    
    /**
     * Inmuebles actaulizados en el dia
     */
    public function getInmueblesUpdatedDayMonteria(){
    
    	$currentDate = new \DateTime();
    	//echo "\n Fecha final: ".$currentDate->format('Y-m-d H:i:s')."\n";
    	$currentDate = $currentDate->format('Ymd');
    	//echo "\n".$currentDate;
    	$currentDate = $currentDate." 00:00:00";
    	
    	//$currentDate = "20161101 00:00:00";
    	
    	$query = "SELECT tstamp as fecha_actualizacion, *, CAST(linderos AS TEXT) AS linderosAll,
    	CAST(texto_inmu AS TEXT) AS descripcionAll
    	FROM inmuebles
    	WHERE tstamp > '$currentDate'
    	ORDER BY id_inmueble DESC";
    	 
    	
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    
    	return $result;
    
    }
    

    /**
     * Obtnener todos los inmuebles a los que se van a cargar fotos
     */
    public function getInmueblesFotosMonteria(){
    
    	$query = "SELECT TOP 100 tstamp as  t, *, CAST(linderos AS TEXT) AS linderosAll, CAST(texto_inmu AS TEXT) AS descripcionAll
    	FROM inmuebles
    	ORDER BY id_inmueble DESC";
    
    
    	//echo "\n".$query."\n";
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    	//print_r($clients);
    	echo "Total inmuebles: ".count($result)."\n";
    
    	return $result;
    
    }
    
    public function getInmueblesFotosRecientesMonteria(){
    
    	 
    	$currentDate = new \DateTime();
    
    	$earlier = new \DateTime();
    	$earlier = $earlier->modify('-30 minutes');
    
    	$currentDate = $currentDate->format('Ymd H:i:s');
    	 
    	$earlier = $earlier->format('Ymd H:i:s');
    	 
    	$query = "SELECT tstamp as  t, *, CAST(linderos AS TEXT) AS linderosAll, CAST(texto_inmu AS TEXT) AS descripcionAll
    	FROM inmuebles
    	WHERE tstamp BETWEEN '$earlier' AND '$currentDate'
    	ORDER BY tstamp ASC";
    	 
    	//echo "\n".$query."\n";
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    	//print_r($clients);
    	echo "Total inmuebles: ".count($result)."\n";
    
    	return $result;
    
    }
    
    
    
    
    /************************* BOGOTA ****************************/
    
    
    public function getInmueblesBogota(){
    
    	$currentDate = new \DateTime();

    	$currentDate = $currentDate->format('Ymd H:i:s');

    	$query = "SELECT TOP 10 *, CAST(linderos AS TEXT) AS linderosAll, CAST(texto_inmu AS TEXT) AS descripcionAll
    	    	  FROM inmuebles
    			  ORDER BY id_inmueble DESC";
    	
    	echo "\n".$query."\n";

		$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    		//print_r($clients);
    	echo "Total inmuebles inmuebles SF1: ".count($result)."\n";
    
    	return $result;
    
    }
    
    /**
     * Inmuebles recientemente actualizados 5 min
     */
    public function getInmueblesUpdateRecienteBogota(){
    
    	$currentDate = new \DateTime();
    	 
    	$earlier = new \DateTime();
    	$earlier = $earlier->modify('-10 minutes');
    	 
    	$currentDate = $currentDate->format('Ymd H:i:s');
    
    	$earlier = $earlier->format('Ymd H:i:s');
    
    	$query = "SELECT tstamp as  t, *, CAST(linderos AS TEXT) AS linderosAll,
			      CAST(texto_inmu AS TEXT) AS descripcionAll
			      FROM inmuebles
			      WHERE tstamp BETWEEN '$earlier' AND '$currentDate'
			      ORDER BY tstamp ASC";
    
    	echo "\n".$query."\n";
    	 
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    
    	return $result;
    
    }
    
    

    /**
     * Inmuebles actaulizados en el dia
     */
    public function getInmueblesUpdatedDayBogota(){
    
    	$currentDate = new \DateTime();
    	//echo "\n Fecha final: ".$currentDate->format('Y-m-d H:i:s')."\n";
    	$currentDate = $currentDate->format('Ymd'); 
    	//echo "\n".$currentDate;   	
    	
    	$currentDate = $currentDate." 00:00:00";
    	
    	//$currentDate = "20161101 00:00:00";
    	
    	$query = "SELECT tstamp as fecha_actualizacion, *, CAST(linderos AS TEXT) AS linderosAll,
    	CAST(texto_inmu AS TEXT) AS descripcionAll
    	FROM inmuebles
    	WHERE tstamp > '$currentDate'
    	ORDER BY tstamp ASC";
    	 
//     	$query = "SELECT tstamp as fecha_actualizacion, *, CAST(linderos AS TEXT) AS linderosAll,
//     	CAST(texto_inmu AS TEXT) AS descripcionAll
//     	FROM inmuebles
//     	ORDER BY id_inmueble DESC";
    
    	
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    
    	return $result;
    
    }
    
    /**
     * Obtnener todos los inmuebles a los que se van a cargar fotos
     */
    public function getInmueblesFotosBogota(){
    
//     	$query = "SELECT tstamp as  t, *, CAST(linderos AS TEXT) AS linderosAll, CAST(texto_inmu AS TEXT) AS descripcionAll
//     	FROM inmuebles
//     	WHERE tstamp > '20160801 08:00:00.00'
//     	AND promocion = 1
//     	ORDER BY id_inmueble DESC";
    
    	$query = "SELECT TOP 100 tstamp as  t, *, CAST(linderos AS TEXT) AS linderosAll, CAST(texto_inmu AS TEXT) AS descripcionAll
    	FROM inmuebles
    	ORDER BY id_inmueble DESC";
    
    	//echo "\n".$query."\n";
    	$r = $this->_conn->_query($query);
    	$result = $this->_conn->_getData($r);
    	//print_r($clients);
    	echo "Total inmuebles: ".count($result)."\n";
    
    	return $result;
    
    }
    
    
    /**************************** FIN BOGOTA ***********************/
    
    
    /****************** GENERAL PARA TODOS ************************/
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
    
    /****************** FIN GENERAL PARA TODOS ************************/
    
}