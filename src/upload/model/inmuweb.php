<?php
namespace upload\model;
use upload\lib\api;


class inmuweb {
    
    private $_user ;
    private $_pass ;
    private $_api;
    private $_headers;
    const URL = 'http://www.araujoysegovia.com/api.php/api/inmueble/';
    
 function __construct()
  {
     $user='jplozgom@chipichipistudio.com';
    $pass='jaja123456jeje';
     $this->_user= $user;
     $this->_pass=$pass;
     
    $this->_headers = array(
	'Accept: application/json',
	'Content-Type: application/json',
	'X_REST_USERNAME: '.$user,
	'X_REST_PASSWORD: '.$pass
	);
     
  
   
     $this->_api = new api(self::URL, $this->_headers);
     
 }
    
  public function delete($id){
           
    $result = $this->_api->delete($id);
                    
     return $result;
      
  }
  
  
   public function get($id){
           
    $result = $this->_api->get($id);
                    
     return $result;
      
  }
  
   public function post($data){
           
    $result = json_encode($data);
                    
     return $result;
      
  }
    
  public function put($data){
           
    $result = json_encode($data);
                    
     return $result;
      
  }
  
}