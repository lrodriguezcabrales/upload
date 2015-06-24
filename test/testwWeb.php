<?php

require_once  '../vendor/autoload.php';
use upload\lib\data;
use upload\model\inmuebles;

$tt1 = microtime(true);

$ctg = new data (array(
    'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));

$inmueble = new inmuebles($ctg);
echo " Cargando inmuebles ->";

$inmuebles = $inmueble->getInmuebles(array('promocion'=>0));


echo count($inmuebles)."\n";


$client = new GearmanClient();
//por defecto el localhost
$client->addServer();
$client->setCompleteCallback("complete"); 

$map = array("total"=>0, "data"=>null);

  $json = json_encode($map);
// crear task
  $i=0;
foreach ($inmuebles as $row ){
     
    $i++;
     $json = json_encode(array("id"=>'C'.$row['id_inmueble']));
    
     $job_handle = $client->addTask("delete",$json,null,$i);  
  
    echo "Enviando Tasks -> $i \n";
}
  


if(!$client->runTasks())
{
    echo "ERROR " . $client->error() . "\n";
    exit;
}


$tt2 = microtime(true);
$r= $tt2-$tt1;
echo "\n\nTiempo de ". $r." para $i registros\n";



function complete($task) 
{ 
  print "COMPLETE: " . $task->unique()  . "\n"; 
}