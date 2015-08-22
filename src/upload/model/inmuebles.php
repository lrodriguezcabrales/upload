<?php

namespace upload\model;
use upload\lib\data;

class inmuebles {
    
    private $_conn;
    
     function __construct(data $conn) {

        $this->_conn = $conn;
    }
 
    
    public function getInmuebles($param){
        
        
        if(array_key_exists('id_inmueble',$param) ) {
            $query="select * from inmuebles where id_inmueble = ".$param['id_inmueble'];
        
        } elseif(array_key_exists('promocion',$param))
        {
            
             $query="select  * from inmuebles where promocion = ".$param['promocion']." order by id_inmueble";
            
        }  else {
             $query="select * from inmuebles order by id_inmueble";
        }
                
                
        
           $r = $this->_conn->_query($query);
         
             

        return $this->_conn->_getData($r);
        
    }
    
     public function getInmueblesWeb($param){
         
            $query="";
         
              $r = $this->_conn->_query($query);
         
        return $this->_conn->_getData($r);
     }
    
    
    
}
