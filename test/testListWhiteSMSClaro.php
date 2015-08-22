<?php
require_once  '../vendor/autoload.php';

use upload\lib\smsclaro;
use upload\model\cartera;
use upload\lib\data;


$db = new data (array(
 'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));

$c= new cartera($db);

$result=$c->getAllphonenumbers();

$text ="";
$n=0;
 $cont=0;
foreach($result as $row){
   
    foreach( $row as $k => $v){
    $tel= rtrim($row[$k]);
    if(strlen($tel)==10){
        
        if($tel[0]=='3'){
          $text .= $tel."\n";  
       
           $cont++;
        }
        
        
        }
   }
   
    
   if($cont==5000){
           $n++; 
       $cont=0;
       
       $file ="phones$n.txt";

        file_put_contents($file, $text);  
        
        $text="";
       
   } 
    
    
}


$file ="phones.txt";

file_put_contents($file, $text); 
 





