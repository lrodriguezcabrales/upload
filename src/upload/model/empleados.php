<?php

namespace upload\model;
use upload\lib\data;
class empleados {
    
    private $_conn;
    
     function __construct(data $conn) {

        $this->_conn = $conn;
    }
    
    
    
    
    public function  getEmpleados(array $param){
    
        ## Analizar parametros
        ## si viene codigo
        if(array_key_exists('codigo',$param) ) {
<<<<<<< HEAD
               $query = "﻿SELECT     c.Codigo, e.IDEN, e.Identificacion, e.Nombre1, e.Nombre2, e.Apellido1, e.Apellido2, e.Nombres, e.Direccion, e.Urbanizacion, e.NoCasa, e.Barrio, 
=======
               $query = "SELECT     c.Codigo, e.IDEN, e.Identificacion, e.Nombre1, e.Nombre2, e.Apellido1, e.Apellido2, e.Nombres, e.Direccion, e.Urbanizacion, e.NoCasa, e.Barrio, 
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
                      e.IDEN_Departamento, e.IDEN_Ciudad, e.Telefono1, e.Telefono2, e.FechaNacimiento, e.IDEN_LugarNacimiento, e.Sexo, e.EstadoCivil, e.NivelEstudio, 
                      e.NoHijos, e.PersonasACargo, e.Email, e.IDEN_TipoIdentificacion, e.IDEN_Profesion, e.Deshabilitado, e.Pasaporte, e.PasaporteFecha, 
                      e.PasaporteVencimiento, e.CertificadoJudicial, e.CertificadoJudicialFecha, e.CertificadoJudicialVecimiento, e.IndEnvioReciboEmail, e.Localizacion, 
                      e.FechaCreacion, e.Extranjero, e.TipoSangre,c.Inactivo
                    FROM         Nm_Empleado AS e INNER JOIN
                      Nm_Contrato AS c ON e.IDEN = c.IDEN_Empleado  where c.Codigo='".$param['codigo']."' "
                      ."ORDER BY c.Codigo";
        
        
        
        }elseif(array_key_exists('identificacion',$param) ) {
        
<<<<<<< HEAD
             $query = "﻿SELECT     c.Codigo, e.IDEN, e.Identificacion, e.Nombre1, e.Nombre2, e.Apellido1, e.Apellido2, e.Nombres, e.Direccion, e.Urbanizacion, e.NoCasa, e.Barrio, 
=======
             $query = "SELECT    c.Codigo, e.IDEN, e.Identificacion, e.Nombre1, e.Nombre2, e.Apellido1, e.Apellido2, e.Nombres, e.Direccion, e.Urbanizacion, e.NoCasa, e.Barrio, 
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
                      e.IDEN_Departamento, e.IDEN_Ciudad, e.Telefono1, e.Telefono2, e.FechaNacimiento, e.IDEN_LugarNacimiento, e.Sexo, e.EstadoCivil, e.NivelEstudio, 
                      e.NoHijos, e.PersonasACargo, e.Email, e.IDEN_TipoIdentificacion, e.IDEN_Profesion, e.Deshabilitado, e.Pasaporte, e.PasaporteFecha, 
                      e.PasaporteVencimiento, e.CertificadoJudicial, e.CertificadoJudicialFecha, e.CertificadoJudicialVecimiento, e.IndEnvioReciboEmail, e.Localizacion, 
                      e.FechaCreacion, e.Extranjero, e.TipoSangre, c.Inactivo
                    FROM         Nm_Empleado AS e INNER JOIN
                      Nm_Contrato AS c ON e.IDEN = c.IDEN_Empleado  where e.Identificacion='".$param['identificacion']."' "
                      ." ORDER BY c.Codigo";
        
        
        
        }else {
        
<<<<<<< HEAD
             $query = "SELECT    c.Codigo, e.IDEN, e.Identificacion, e.Nombre1, e.Nombre2, e.Apellido1, e.Apellido2, e.Nombres, e.Direccion, e.Urbanizacion, e.NoCasa, e.Barrio, 
=======
             $query = "SELECT c.Codigo, e.IDEN, e.Identificacion, e.Nombre1, e.Nombre2, e.Apellido1, e.Apellido2, e.Nombres, e.Direccion, e.Urbanizacion, e.NoCasa, e.Barrio, 
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
                      e.IDEN_Departamento, e.IDEN_Ciudad, e.Telefono1, e.Telefono2, e.FechaNacimiento, e.IDEN_LugarNacimiento, e.Sexo, e.EstadoCivil, e.NivelEstudio, 
                      e.NoHijos, e.PersonasACargo, e.Email, e.IDEN_TipoIdentificacion, e.IDEN_Profesion, e.Deshabilitado, e.Pasaporte, e.PasaporteFecha, 
                      e.PasaporteVencimiento, e.CertificadoJudicial, e.CertificadoJudicialFecha, e.CertificadoJudicialVecimiento, e.IndEnvioReciboEmail, e.Localizacion, 
                      e.FechaCreacion, e.Extranjero, e.TipoSangre,c.Inactivo
                    FROM    Nm_Empleado AS e INNER JOIN
                      Nm_Contrato AS c ON e.IDEN = c.IDEN_Empleado ORDER BY c.Codigo";
        

        
        }
        
        $r = $this->_conn->_query($query);
         
<<<<<<< HEAD
                

=======
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
        return $this->_conn->_getData($r);
        
    }

     public function  getTerceros(){
             
         ### select entidades
         
         
<<<<<<< HEAD
         $query = "select Codigo , Nombre from Nm_FondoCesantias "
                 . " Union select Codigo , Nombre from Nm_FondoRiesgos"
                 . " Union  select Codigo , Nombre from Nm_FondoSalud "
                 . " Union select Codigo , Nombre from ﻿Nm_EntidadFinanciera";
=======
         $query = "SELECT Codigo , Nombre from Nm_FondoCesantias 
                  Union select Codigo , Nombre from Nm_FondoRiesgos
                 Union  select Codigo , Nombre from Nm_FondoSalud
                Union select Codigo , Nombre from ﻿Nm_EntidadFinanciera ";
         
         echo $query;
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
         
         $r = $this->_conn->_query($query);
        $result = $this->_conn->_getData($r);
         
         return $result;
        
     }
     
     
}

