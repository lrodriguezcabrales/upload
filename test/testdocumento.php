<?php

$json_document_type='{
  "name": "Prueba 1",
  "description": "Esto es una prueba",
  "monthsValidaty": "999",
  "container": {
    "id": "37d5ff73-83f2-477a-b65c-d8792993a205"
  },
  "groupSpecialFolder": [],
  "contentType": [
    {
     
     
      "id": "ba977e1b-5292-43de-8c66-601f877c7729",
      "format": "pdf",
      "mimetype": "application/pdf"
    },
    {
      
     
      "id": "db0dab63-70c5-466e-9ca8-60c6753465cd",
      "format": "pdf B",
      "mimetype": "application/octet-stream"
    }
  ]
}';

$type =json_decode($json_document_type,true);






require_once  '../vendor/autoload.php';

use upload\model\documento;
use upload\lib\data;
use upload\lib\api;

$db = new data (array(
 'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'docflow' 
 ,'engine'=>'mssql'
   
));


$archivador ='THUMANO';
$thumano = new documento($db,$archivador);

$data=$thumano->getAllTipo_doc();

$user = "sifinca@araujoysegovia.com";
$pass = "araujo123";

$url = "http://www.sifinca.net/sifinca/web/app.php/admin/sifinca/mapper";
$apimapper = SetupApi($url,$user,$pass);


print_r($apimapper->get());

return 0;

$url = "http://www.sifinca.net/sifinca/web/app.php/archive/main/documenttype";
$apidocumenttype = SetupApi($url,$user,$pass);

## recorrer tipos
foreach ($data as $row) {
 
    
    ## postear en documentype
    
    $documenttype = $type;
    
    $documenttype['name']= $row['nombre'];
    $documenttype['description']= $row['nombre'];


    
  $result = json_decode($apidocumenttype->post($documenttype), true);
   
 
    
    $success=$result['success'];
    
    print_r($success);
        echo "\n";
    
    if($success){
    ### actualiza mapperr
         $mapper = array("name"=> "documentType.".$archivador,
                   "idSource"=> $row['id'], 
                   "idTarget"=> $result['data'][0]);
     
         $result= $apimapper->post($mapper);
         
         print_r($result);
        
    }
    
  
    echo "\n";
    
    
}





function SetupApi($urlapi,$user,$pass){
   
    
    ## Obtener el token de session = SessionID

$url="http://www.sifinca.net/sifinca/web/app.php/login";
$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
   
);

$a = new api($url, $headers);
$result= $a->post(array("user"=>$user,"password"=>$pass));  

$data=json_decode($result);
  
$token = $data->id;

## cargar objetos para inyectar a workers

$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
    'x-sifinca:SessionToken SessionID="'.$token.'", Username="'.$user.'"'  ,  
);


    $a->set(array('url'=>$urlapi,'headers'=>$headers));
    
   return $a; 
    
}


    
    
    
    
