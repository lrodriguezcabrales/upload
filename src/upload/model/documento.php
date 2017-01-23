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
            echo "\ndoc 1 \n";
            $query="select * from documentos where archivador='".$this->_archivador."' and id ='".$param['id']."'";

            
        }else{
            
            ## obtener todos los documentos
        	echo "\ndoc 2 \n";
            $query="select top 34000 * from documentos where archivador='".$this->_archivador."'  order by nkey asc";

        	
        }
        
        //echo "\n".$query."\n";
 
        
        $r = $this->_conn->_query($query);
                        
        $result=$this->_conn->_getData($r);
        
        
       return $result; 
        
        
        
    }
    
    
    public function getOneDocument(){

    	$query="select * from documentos where archivador='".$this->_archivador."' and radicacion = '12731'";
    
    	echo "\n".$query."\n";
    	
    	$r = $this->_conn->_query($query);
    
    	$result=$this->_conn->_getData($r);
    
    
    
    	return $result;
    
    
    
    }
    
    public function getFile($id,$target) {
       
        ### buscar por nkey
         $find = false;    
    		
    	
         $query="select  image, ext from doc_image where nkey =".$id;
            
         //$query = "select  image, ext from doc_image where nkey =12775";
         
         echo "\n".$query."\n";
         
         $r = $this->_conn->_query($query);
          
         if (!mssql_num_rows($r)) {
         	
         	echo "\nNo se encontraron registros\n";
         	
         }else{
         	
         	$data = mssql_result($r, 0, 'image');
         	 
         	$ext = mssql_result($r, 0, 'ext');
         	
         	file_put_contents($target, $data);
         	
         	$result = $ext;
         	 
         	$find = true;
         	//return $result;
         }
         
         return $find;
        
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
    
      echo $command;
      
      echo $salida;
      
      return $salida;
      
  }
  
  
  public function getAllTipo_doc(){
      
      $query="select * from tipo_doc where id_archivador ='".$this->_archivador."'";
            
      $r = $this->_conn->_query($query);
                        
     ## devuelve array
       return $this->_conn->_getData($r);
            
  }
  
    
}
