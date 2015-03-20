<?php

class data {

private $_server = "";
private $_user = "";
private $_pass = "";
private $_database = ""; 
private $_dbhandle = null;
private $_numrows=0;
private $_engine="mysql";

function __construct ($param )
{
	
	$this->_server= $param['server'] ;
	$this->_user= $param['user'] ;
	$this->_pass= $param['pass'] ;
	$this->_database= $param['database'] ;
	$this->_engine= $param['engine'];
        
	$this->_connect();
	

	
}

function __destruct(){

	//close the connection
    
    switch ($this->_engine) 
    {
       
       case "mssql":
        
            mssql_close($this->_dbhandle);
            break;
          
       case "mysql":
                   
              mysql_close($this->_dbhandle);
              break;
        
        
    }
    
	$this->_client = null;
}


private function _connect(){
	
    
     switch ($this->_engine) 
    {
       
       case "mssql":
        
 
                   
           $this->_dbhandle = mssql_connect($this->_server, $this->_user, $this->_pass);
           if (!$this->_dbhandle) {
                die('No pudo conectarse: ' . mssql_error());
            }
            
            
            
           $selected = $this->_selectdb();
            break;
          
       case "mysql":
                   
              $this->_dbhandle = mysql_connect($this->_server, $this->_user, $this->_pass);
          
           if (!$this->_dbhandle) {
                die('No pudo conectarse: ' . mysql_error());
            }
            
            mysql_set_charset('utf8',$this->_dbhandle);
           
            $selected = $this->_selectdb();
            
           break;
        
        
    }
    
    
    
    

}


public function _getGUID(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
            .substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12)
            .chr(125);// "}"
        return $uuid;
    }
}

public function parseUtf8ToIso88591(&$string){
     if(!is_null($string)){
         
            $iso88591_1 = utf8_decode($string);
            $iso88591_2 = iconv('UTF-8', 'ISO-8859-1', $string);
            $string = mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');       
     }
}



private function _selectdb(){
        
    
         switch ($this->_engine) 
    {
       
       case "mysql":
        
            $result=mysql_select_db($this->_database, $this->_dbhandle);
            break;
          
       case "mssql":
             
              ini_set('mssql.charset', 'UTF-8');
           
             $result=mssql_select_db($this->_database, $this->_dbhandle);
              break;
        
        
    }
    
    
	return $result;

}


public function _getData($r){
    
     $list = array();

        while ($row = mssql_fetch_array($r)) {
   
            foreach ($row as $key => $value) {
             
                if(is_string($value)){
                    $row[$key]= utf8_encode($value);
                }

            }
            
            $list[] = $row;
        }

    return $list;
}


public function _query($query){
    

    
    switch ($this->_engine) 
    {
       
       case "mssql":
         
         $this->parseUtf8ToIso88591($query);
           
           $result = mssql_query($query);
           
          
           $this->_numrows =0;
          break;
       case "mysql":
              $result = mysql_query($query,$this->_dbhandle);
           if (!$result) {
                    $mensaje  = 'Consulta no válida: ' . mysql_error() . "\n";
                    $mensaje .= 'Consulta completa: ' . $consulta;
                    die($mensaje);
                }

           
           
           $this->_numrows =  0;
              break;
    }
    
    

	
	return $result;
}


public function fechArray($q){
    
     $r=$this->_query($q);
     $list = array();
        
         while( $row = mssql_fetch_array($r)){
            
            $list[]=$row;   
             
         }
                 
  return $list;
}


}