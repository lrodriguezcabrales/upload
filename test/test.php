<?php


http://web_user:web_pass@192.168.200.10/cgi-bin/exec?
cmd=api_queue_sms&username=lyric_api&password=lyric_api&content=SMS+Content&de
stination=55555555&api_version=0.08&channel=2

        
        
require_once  '../vendor/autoload.php';
use upload\lib\api;
$user="sifinca@araujoysegovia.com";
$pass="araujo123";

$url="http://www.sifinca.net/sifinca/web/app.php/admin/sifinca/company?take=10&skip=0&page=1&pageSize=10";
$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
   'x-sifinca:SessionToken SessionID="554947fccd7959680f2fcf11", Username="sifinca@araujoysegovia.com"'
);

$a = new api($url, $headers);
$result= $a->get();  

$data=json_decode($result);

echo $result;
echo $data;

  




//$token = $data->id;