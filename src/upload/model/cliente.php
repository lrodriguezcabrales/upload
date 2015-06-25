<?php

namespace upload\model;
use upload\lib\data;
class cliente {
	private $_conn;
    private $_nosource = array('68');
       
    function __construct(data $conn) {
    	$this->_conn = $conn;
    }
    
    public function getClientes(array $param  ){
    	if(array_key_exists('IDTERCERO',$param) ) {
    		$query="SELECT     IDTERCERO, NOMBRETER, DIGIVERIF, TIPOTERCE, SEGMENTO, TIPOEMPRESA, DIRECCION, CIUDAD, DIVPOLITICA, CODIGODANE, Telefono, 
                      TipoIdentificacion, Nombre1, Nombre2, Apellido1, Apellido2, TipoRazonSocial, Prefijo_NCF, Sexo, Profesion, FechaNacimiento, Hobbies, 
                      NombreConyugue, FechaNacimientoConyugue, TipoClienteFrecuenciaCompra, EstratoSocial, Barrio, Telefono2, Celular, Fechagrabacion, Pais, Tipo, 
                      CentroCosto, Item, Deshabilitado, CodigoOcupacion
					  FROM         TERCEROS
					  WHERE     (IDTERCERO = '".$param['IDTERCERO']."')";
             
         }
         else {
         	return array("Message"=>"Parametro invalido");
         }
         $r = $this->_conn->_query($query);
         $data = $this->_conn->_getData($r); 
         return $data;
    }    
}