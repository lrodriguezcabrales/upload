<?php

require_once  '../vendor/autoload.php';
use upload\model\empleados;
use upload\lib\data;

$db = new data (array(
 'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'nomina_AYS' 
 ,'engine'=>'mssql'
   
));
/*
$db = new data (array(
 'server' =>'192.168.100.5'
 ,'user' =>'sa'
 ,'pass' =>'75080508360'
 ,'database' =>'nomina' 
 ,'engine'=>'mssql'
   
));

*/
$idcompany="890400048";
$filename='users-ctg.json';
$e= new empleados($db);
$data = $e->getEmpleados(array());

/*
ays cartagena 890400048
ays monteria 880325873

*/
$n=0;
foreach ($data as $row) {
  
   
    
    if(IsMail($row['Email'])){
       if($row['Inactivo']==false){
        $users[]=array("id"=>$row['Identificacion'],
        "email"=>$row['Email'] ,
        "n1"=>$row['Nombre1'],
        "n2"=>$row['Nombre2'],
        "a1"=>$row['Apellido1'],
        "a2"=>$row['Apellido2'],
        "company"=>$idcompany
        );  
        $n++;
       }
        echo $row['Identificacion']."\n";
        
        
    }
    
}

file_put_contents($filename, json_encode($users));
echo $n;



function IsMail($direccion)
{
   $Sintaxis='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
   if(preg_match($Sintaxis,$direccion))
      return true;
   else
     return false;
}