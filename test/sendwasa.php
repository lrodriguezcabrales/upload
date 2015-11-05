<?php
require_once  '../vendor/autoload.php';
require '../src/whatsapi/whatsprot.class.php';
use upload\model\cartera;
use upload\lib\data;

$nickname = "Araujo y Segovia SA";

// #### DO NOT ADD YOUR INFO AND THEN COMMIT THIS FILE! ####
$sender = 	"573145892664"; // Mobile number with country code (but without + or 00)
$password =     "ZnxJsoVDxYjOlj858MLBrZ8Ko0U="; // Password you received from WhatsApp
$status="Líderes Inmobiliarios";
$seconds=15;
## cargar datos a enviar

$db = new data (array(
 'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));


$c= new cartera($db);
<<<<<<< HEAD

$param=array('label'=>'30-60','year'=>2015,'month'=>6);

$list=$c->getListforLabel($param);


=======
$param=array('label'=>'30-60','year'=>2015,'month'=>7);

$list=$c->getListforLabel($param);

>>>>>>> c4ca7ef1998f7d27d3aa2057ee37bc1da48e629a
echo "[] Logging in as '$nickname' ($sender)\n";
$wa = new WhatsProt($sender, $nickname, TRUE);

$wa->connect();
$wa->loginWithPassword($password);

echo "\n[] Setting status:\n";
$wa->sendStatusUpdate($status);



$tpl="Estimado cliente, 
 ARAUJO Y SEGOVIA SA, le recuerda el pago de su obligación, la cual a la fecha presenta mora por valor de $ %saldo%. 

La mora en el pago de sus obligaciones acarrea reportes negativos en centrales de riesgos, así como el cobro de cargos adicionales.
Si ya ha efectuado su pago, envíenos el comprobante al correo cartera@araujoysegovia.com , cualquier información adicional en el teléfono 0356501190.

Para realizar su pago en linea : http://www.araujoysegovia.com/pagos

No responda este mensaje, fue generado automáticamente.";


### obtener

//$list = array(array('tel_celular'=>'3175102281','saldo_a'=>2050030),
 //   array('tel_celular'=>'3013248324','saldo_a'=>2050030),
  //  array('tel_celular'=>'3164539256','saldo_a'=>2050030),
   //     );

foreach ($list as $row){
    $dst = '57'.$row['tel_celular'];
         
     
    $msg=str_replace('%saldo%',number_format($row['saldo_a']),$tpl);

    echo "\n[] Send message to $dst: $msg\n";
    $wa->sendMessage($dst , $msg);
    $wa->sendMessageImage($dst, "pse.jpg");

//$wa->sendGetRequestLastSeen($dst);
   
    sleep($seconds);
}

/*
echo "\n[] Request last seen $dst: ";

$wa->sendGetRequestLastSeen($dst);



echo "\n[] Send message to $dst: $msg\n";
$wa->sendMessage($dst , $msg);
echo "\n";
 * */