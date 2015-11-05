<?php
require_once  '../vendor/autoload.php';

use upload\lib\smsclaro;
use upload\model\cartera;
use upload\lib\data;


$sms= new smsclaro();




$sms->send("3013248324", "Mensaje de prueba ...");

if($sms->getresultCode()==0){
    
    echo "Mensaje enviado exitosamente -> Id :".$sms->getoperationId();
    
}  else {
    echo "Mensaje no enviado : ".$sms->errormsg();
}



