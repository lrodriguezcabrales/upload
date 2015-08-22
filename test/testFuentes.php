<?php

$json_document_type='{
		"code": "002",
		"description": "PRUEBA 1",
}';

$type =json_decode($json_document_type,true);






require_once  '../vendor/autoload.php';

use upload\model\contable;
use upload\lib\data;
use upload\lib\api;

$db = new data (array(
 'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'mca' 
 ,'engine'=>'mssql'
   
));


$contable = new contable($db);

$data=$contable->getFuentes();
$user = "sifinca@araujoysegovia.com";
$pass = "araujo123";

// $url = "http://www.sifinca.net/sifinca/web/app.php/admin/sifinca/mapper";
$url = "http://10.102.1.22/sifinca/web/app.php/admin/sifinca/mapper";
$apimapper = SetupApi($url,$user,$pass);

$url = "http://10.102.1.22/sifinca/web/app.php/admin/accounting/documenttype";
$apidocumenttype = SetupApi($url,$user,$pass);


## recorrer tipos
foreach ($data as $row) {
    ## postear en documentype
    $documenttype = $type;
    $documenttype['code'] = $row['IDFUENTE'];
    $documenttype['description'] = $row['DESFUENTE'];
    
    echo "fuente\n";
	print_r($documenttype);
	$result = json_decode($apidocumenttype->post($documenttype), true);
	
	print_r($result);
    $success=$result['success'];
    echo "\npase\n";
    
    print_r($success);
        echo "\n";
    
    if($success){
    ### actualiza mapperr
         $mapper = array("name"=> "documentType",
                   "idSource"=> $row['IDFUENTE'], 
                   "idTarget"=> $result['data'][0]);
         $result= $apimapper->post($mapper);
         
         print_r($result);
    }
    
  
    echo "\n";
    
    
}





function SetupApi($urlapi,$user,$pass){
   
    
    ## Obtener el token de session = SessionID

$url="http://10.102.1.22/sifinca/web/app.php/login";
$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
   
);

$a = new api($url, $headers);
echo "login\n";
print_r($a->post(array("user"=>$user,"password"=>$pass)));
$result= $a->post(array("user"=>$user,"password"=>$pass));  

$data=json_decode($result,true);

$token = $data['id'];

## cargar objetos para inyectar a workers

$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
    'x-sifinca:SessionToken SessionID="'.$token.'", Username="'.$user.'"'  ,  
);


    $a->set(array('url'=>$urlapi,'headers'=>$headers));
    
   return $a;
}
    
    
    
