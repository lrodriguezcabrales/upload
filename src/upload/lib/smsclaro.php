<?php


namespace upload\lib;

class smsclaro {
    
     private $_resultCode ;
     private $_operationId;
    
     function __construct() {

       
    }
    
    
    public function send($phone,$message)
    {
        
    $cmd = 'curl -v -k -u "PMEappClCo:PMEappClCo2012" -X POST -d "domain=araujoysegovia.com&method=sendSMS&subscriber={phone}&message={message}&date=" http://mic.claro.com.co/servicesPME/app2mobile.jws';
    $cmd = str_replace('{message}', $message, $cmd);
    $cmd = str_replace('{phone}', $phone, $cmd);
    
    $result=shell_exec($cmd);
    parse_str($result,$codes);
    
    
    $this->_resultCode= $codes['resultCode'];
    $this->_operationId= $codes['operationId'];
    
    
    return $codes;
       
        
        
    }
    
    
    public function getresultCode(){
        
        return $this->_resultCode;
    }
    
    public function getoperationId(){
        
        return $this->_operationId;
    }

    
    public function errormsg(){
        
        $msg = array( 200=>"Usuario se encuentra Deshabilitado"
                        ,201=>"El número de teléfono móvil no es válido"
                        ,202=>"Número de teléfono no ha sido aprovisionado"
                        ,203=>"Texto del mensaje es nulo"
                        ,204=>"Compañía se encuentra en estado INACTIVA"
                        ,206=>"Fecha de despacho agendada ha expirado"
                        ,208=>"Se ha excedido el número máximo de mensajes y no le está permitido EXCEDERSE"
                        ,250=>"La fecha de despacho tiene como límite 30 días"
              );
        
        
        return $msg[$this->_resultCode];
        
    }
    
   
    
    
    }        







        

