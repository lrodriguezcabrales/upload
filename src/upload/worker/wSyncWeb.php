<?php
require_once __DIR__.'/../../../vendor/autoload.php'; 

use upload\model\inmuweb;
## Workers para syncronizar pagina web




$w = new GearmanWorker();
$count=0;
$w->addServer();
### estsblecer funciones disponibles
$w->addFunction("delete","delete");


//$w->addFunction("actContractLst","actContractLst",$c);

while($w->work()){

if ($w->returnCode() != GEARMAN_SUCCESS)
  {
    echo "return_code: " . $w->returnCode() . "\n";
    break;
  }

} 
  

function delete($job){
    
   $inm = new inmuweb();
  
  $data= json_decode($job->workload());
    
 $result = $inm->delete($data->id);
  
  echo "Inmueble :".$data->id." -> $result \n";
  return 0;
    
}