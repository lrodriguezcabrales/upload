<?php


class api {
    
    private $_url ;
    private $_headers;
    
    function __construct ($url,$headers )
    {
        
        $this->_url=$url;
        $this->_headers= $headers;
        
    }
    
        
    
    public function get($id=null){
        
        
        
        if(is_null($id)){
           $result=  $this->_callapi('GET', $this->_url, $this->_headers,'');
        }else
            {
             
           $result= $this->_callapi('GET', $this->_url.$id, $this->_headers,'');
        }
        
         
          return $result ;
        
    }
    
    public function post($data){
        
        
        return $this->_callapi('POST', $this->_url, $this->_headers, json_encode($data));
        
        
    }
    
    public function delete($id=null){
        
        if(is_null($id)){
            
            // traer todos
            $response= $this->get();
         
            $a = json_decode($response,true);
      
           
            foreach($a['data'] as $value){
                            
          
             // print_r( $value['id']);
               $response= $this->_callapi('DELETE', $this->_url.$value['id'], $this->_headers,null);
                
            }
           
                //borrar
            
            
        }else{
               $response= $this->_callapi('DELETE', $this->_url.$id, $this->_headers,null);
     
        }
        
        return $response;
    }
    
    
    
    public function _callapi($method, $url,$headers,$data)
   {
       


    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

    switch($method) {
        case 'GET':
            break;
        case 'POST':
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
            break;
        case 'PUT': 
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
            break;
        case 'DELETE':
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
            break;
    }

        $response = curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);




           return $response;
   }

    
    
    
    
}