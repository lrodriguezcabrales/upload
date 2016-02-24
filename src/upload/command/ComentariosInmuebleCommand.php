<?php
namespace upload\command;

use upload\model\comentariosInmuebleCartagena;
use upload\lib\data;
use upload\lib\api;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use GearmanClient;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ComentariosInmuebleCommand extends Command
{	
	
	public $server = 'http://104.130.11.91/sifinca/web/app_dev.php/';
	public $serverRoot = 'http://104.130.11.91/';
	
	public $localServer = 'http://10.102.1.22/';
	
// 	public $server = 'http://10.102.1.22/sifinca/web/app.php/';
// 	public $serverRoot = 'http:/10.102.1.22/';

// 	public $server = 'http://104.130.12.152/sifinca/web/app_dev.php/';
// 	public $serverRoot = 'http:/104.130.12.152/';
	
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
		
	
    protected function configure()
    {
        $this->setName('comentariosinmueble')
		             ->setDescription('Comando para pasar comentarios de inmuebles');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Datos de contrato SF1 \n");

        $conn = new data(array(
            'server' =>'10.102.1.3'
            ,'user' =>'hherrera'
            ,'pass' =>'daniela201'
            ,'database' =>'sifinca' 
            ,'engine'=>'mssql'
        ));

        $conexion = new comentariosInmuebleCartagena($conn);      
        
		$comentarios = $conexion->getComentarios();
		
		$this->buildCometarios($conexion, $comentarios);
		
    }
    
    function buildCometarios($conexion, $comentarios) {
    	
    	
    	$total = 0;
    	
    	$totalComentarios = count($comentarios);
    	
    	//$totalComentarios = 135000;
    	$startTime= new \DateTime();
    	
    	$porDonde = 0;
    	for ($i = 166050; $i < $totalComentarios; $i++) {
    		$comentario = $comentarios[$i];

    		//print_r($comentario);
    		
    		$inmueble = $this->searchProperty($comentario['clave']);
    		$usuario = $this->searchUsuario($comentario['email']);
    		
//     		echo "\ninmueble\n";
     		
    		
    		if(!is_null($inmueble)){
    			
    			$bComentario = array(
    					'comment' => $this->cleanString($comentario['comentario']),
    					'idEntity' => $inmueble['id'],
    					'user' => array('id'=>$usuario['id']),
    					'date' => $comentario['fecha'],
    					'nkey' => $comentario['NKEY'],
    					'sifincaOne' => true
    			);
    			
//     			$json = json_encode($bComentario);
    			 
    			//echo "\n".$json."\n";
    			
    			$urlComentario = $this->server.'catchment/main/property/comment/'.$inmueble['id'];
    			 
//     			echo "\n".$urlComentario."\n";
    			
    			$apiComentario = $this->SetupApi($urlComentario, $this->user, $this->pass);
    			
    			$result = $apiComentario->post($bComentario);
    			
    			$result = json_decode($result, true);
    			
    			

    			$json = json_encode($bComentario);
    			 
// 	    		echo "\njson\n";
// 	    		echo $json;
    			
    			
//     			$porDonde++;
//     			echo "\nPor donde: \n".$porDonde;
    			if($result[0]['success'] == true){
    				echo "\nOk\n";
    				$total++;
    			}else{
    				echo "\nError cometario\n";
    				//echo "\n\n".$json."\n\n";
    				
    				$urlapiMapper = $this->server.'admin/sifinca/errorcomment';
    				$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    				
    				$error = array(
    						'comment' => $comentario['NKEY'], 
    						'objectJson' => $json
    				);
    				 
    				$resultError = $apiMapper->post($error);
    				
    				//print_r($resultError);
    			}
    			
    			$porDonde++;
    			echo "\n Por donde vamos: ".$porDonde."\n";
    			
    		}
    		
    	}
    	
    	$finalTime = new \DateTime();
    	 
    	 
    	$diff = $startTime->diff($finalTime);
    	 
    	 
    	echo "\n\n Fecha inicial: ".$startTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Fecha final: ".$finalTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
   
    	echo "\n Total de comentarios pasados: ".$total."\n";
    	
    	echo "\n Indice final: ".$totalComentarios."\n";
    }
    
    
    function searchProperty($propertySF1) {
    
    	$propertySF1 = $this->cleanString($propertySF1);
    	 
    	$filter = array(
    			'value' => $propertySF1,
    			'operator' => '=',
    			'property' => 'consecutive'
    	);
    	$filter = json_encode(array($filter));
    
    	$urlProperty = $this->server.'catchment/main/property?filter='.$filter;
    
    	$apiProperty = $this->SetupApi($urlProperty, $this->user, $this->pass);
    
    	$property = $apiProperty->get();
    	$property = json_decode($property, true);
    
    	//     	echo "\n\nInmueble\n";
    	//     	print_r($property['data'][0]);
    	 
    	if($property['total'] > 0){
    		 
    		$p = array('id' => $property['data'][0]['id']);
    		return $p;
    		 
    	}else{
    		return null;
    	}
    
    }
    
    function searchUsuario($email) {

    	$email = $this->cleanString($email);

    	if($email == 'hherrera@araujoysegovia.com'){
    		
    		return $user = array('id' => 203);
    	}
    	
    	$filter = array(
    			'value' => $email,
    			'operator' => '=',
    			'property' => 'email'
    	);
    	$filter = json_encode(array($filter));
    
    	$urlUser = $this->server.'admin/security/user?filter='.$filter;
    
    	$apiUser = $this->SetupApi($urlUser, $this->user, $this->pass);
    
    	$user = $apiUser->get();
    	$user = json_decode($user, true);
    
    
    	if($user['total'] > 0){
    		 
    		
    		
    		return $user['data'][0];
    		 
    	}else{
    		return $user = array('id' => 203);
    	}
    
    }
    
    /**
     * Eliminar espacios en blanco seguidos
     * @param unknown $string
     * @return unknown
     */
    function  cleanString($string){
    	$string = trim($string);
    	$string = str_replace('&nbsp;', ' ', $string);
    	$string = preg_replace('/\s\s+/', ' ', $string);
    	return $string;
    }
    
    
    function SetupApi($urlapi,$user,$pass){
    
    	$url= $this->server."login";
    	$headers = array(
    			'Accept: application/json',
    			'Content-Type: application/json',
    	);
    
    	$a = new api($url, $headers);
    
    	//print_r($a);
    
    	$result= $a->post(array("user"=>$user,"password"=>$pass));
    
    	//      	echo "\nAqui result\n";
    	//     	print_r($result);
    
    
    	$data=json_decode($result);
    
    	//print_r($data);
    
    	$token = $data->id;
    
    	$headers = array(
    			'Accept: application/json',
    			'Content-Type: application/json',
    			//'x-sifinca:SessionToken SessionID="564f29657315cf4b1afa87e8", Username="lrodriguez@araujoysegovia.net"'
    			'x-sifinca: SessionToken SessionID="'.$token.'", Username="'.$user.'"',
    	);
    
    	//     	print_r($headers);
    
    	$a->set(array('url'=>$urlapi,'headers'=>$headers));
    
    	//print_r($a);
    
    	return $a;
    
    }
}