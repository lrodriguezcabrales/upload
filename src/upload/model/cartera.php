<?php

namespace upload\model;
use upload\lib\data;
class cartera {
    
    private $_conn;
    
     function __construct(data $conn) {

        $this->_conn = $conn;
    }
    
    
    
    
    public function  getListforLabel(array $param){
    
        ## Analizar parametros
        ## si viene codigo
        if(array_key_exists('label',$param) and array_key_exists('year',$param)  and array_key_exists('month',$param))
                {
               $query = "select c.diasmora_a,c.saldo_a,c.label,c.canon_a,a.* from vwarr_activos a , lstcobro c
                    where a.id_contrato = c.id_contrato
             and c.mes = ".$param['month']." and ano=".$param['year']." and c.label ='".$param['label']."'  and len(rtrim(a.tel_celular))=10";
              
        

        
        }
        
        $r = $this->_conn->_query($query);
         
             
        return $this->_conn->_getData($r);
        
    }

     
     
}

