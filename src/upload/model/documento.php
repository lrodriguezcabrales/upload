<?php

namespace upload\model;
use upload\lib\data;
class documento {
    
    private $_conn;
    private $_archivador;
       
    function __construct(data $conn,  $archivador ) {

        $this->_conn = $conn;
        $this->_archivador=$archivador;
    }
    
    public function get($param){
        
        if(array_key_exists('id',$param)){
            
            ## obtener el radicado
            
<<<<<<< HEAD
            $query="select top 2  * from documentos where archivador='".$this->_archivador."' and id ='".$param['id']."'";
=======
            $query="select   * from documentos where archivador='".$this->_archivador."' and id ='".$param['id']."'";
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
            
            
        }else{
            
            ## obtener todos los documentos
<<<<<<< HEAD
            $query="select top 2 "
                    . " * from documentos where archivador='".$this->_archivador."'  order by nkey asc";
=======
            $query="select "
             . " * from documentos where archivador='".$this->_archivador."'  order by nkey asc";
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
            
            
        }
        
 
      
        
        $r = $this->_conn->_query($query);
                        
        $result=$this->_conn->_getData($r);
        
        
<<<<<<< HEAD
        foreach ($result as $key => $value) {
            
        }
=======
       // foreach ($result as $key => $value) {
            
        //}
>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
        
        //$metadata = $this->getMetadata($result['']);
        
        
        
        return $result; 
        
        
        
    }
    
    public function getFile($id,$target) {
       
        ### buscar por nkey
        
         
        
        
         $query="select  image, ext from doc_image where nkey =".$id;
            
         $r = $this->_conn->_query($query);
          
         
         $data = mssql_result($r, 0, 'image');
               
         $ext = mssql_result($r, 0, 'ext');
          
         file_put_contents($target, $data);
        
         
         
         
         $result = $ext;
         
         /*foreach($data[0] as $k => $v){
        
             if($k!='image')   {
                 $result[$k]=$v;
         }
         
         }
          */
         
         
         
     ## devuelve array
        return $result;
        
        
        
        ## retornar string 
        
    }
    
    
    public function getMetadata($id){
        
         $query="select * from ".$this->_archivador." where id ='".$id."'";
            
         $r = $this->_conn->_query($query);
                        
     ## devuelve array
        return $this->_conn->_getData($r);
        
        
    }
    
       
  public function tiff2pdf($tif, $pdf){
      
      ### Ojo debe estar instalado tiff2pdf
      $command= "tiff2pdf  -o $pdf $tif > /dev/null 2>&1 &";
      $salida = shell_exec($command);
    
      return $salida;
      
  }
  
  
  public function getAllTipo_doc(){
      
      $query="select * from tipo_doc where id_archivador ='".$this->_archivador."'";
            
      $r = $this->_conn->_query($query);
                        
     ## devuelve array
       return $this->_conn->_getData($r);
            
  }
  
    
}
