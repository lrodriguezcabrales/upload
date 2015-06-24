<?php

require_once  '../vendor/autoload.php';

$db = new data (array(
 'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'mca' 
 ,'engine'=>'mssql'
   
));

$b= new contable($db);

$p = array('NUMEDCTO'=>'0000273509','FNTEDCTO'=>'70' );


$data ='{"voucher":"0000412505","voucherSource":"16-0000412505","source":"CHEQUE","sourceCode":16,"description":"VR.IVA MES DE ENERO\/14 PROP.GUSTAVO CHAPARRO A.","thirdIdentity":"7445981    ","third":"GUSTAVO CHAPARRO ACEVEDO","date":"Jan 22 2014 12:00:00:000AM"}';
