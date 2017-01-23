<?php
namespace upload\command\Monteria;

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

class ComentariosInmuebleMonteriaCommand extends Command
{	
	
	public $server = 'http://www.sifinca.net/monteriaServer/web/app.php/';
	public $serverRoot = 'http://www.sifinca.net';
	
	public $localServer = 'http://10.102.1.22/';
		
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;
		
	
    protected function configure()
    {
        $this->setName('comentariosInmuebleMonteria')
		             ->setDescription('Comando para pasar comentarios de inmuebles - Cartagena');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Comentarios inmuebles ---- SF1 \n");

        $conn = new data(array(
        		'server' =>'192.168.100.1'
        		,'user' =>'sa'
        		,'pass' =>'75080508360'
        		,'database' =>'sifinca'
        		,'engine'=>'mssql'
        ));

        $conexion = new comentariosInmuebleCartagena($conn);      
        
		
		$this->buildCometarios($conexion);
		
    }
    
    function buildCometarios($conexion) {
    	
    	
    	$inmuebles = $conexion->getInmuebles();
    	$totalInmuebles = count($inmuebles);
    	
    	$startTime= new \DateTime();
    	
    	$porDonde = 0;
    	
    	$total = 0;
    	
    	if($totalInmuebles > 0){
    		for ($i = 20000; $i < $totalInmuebles; $i++) {
    			
	    		$inmueble = $inmuebles[$i];
	    		
	    		echo "\nInmueble: ".$inmueble['id_inmueble']."\n";
	    		
	    		$comentarios = $conexion->getComentariosPorInmueble($inmueble['id_inmueble']);
	    		$total = 0;
	    		$totalComentarios = count($comentarios);
	    		    		 
	    		
	    		
	    		if($totalComentarios > 0){
	    		
		    		for ($j = 0; $j < $totalComentarios; $j++) {
		    			
		    			
		    			$comentario = $comentarios[$j];
		    			$property = $this->searchProperty($comentario['clave']);
		    			$usuario = $this->searchUsuario($comentario['email']);
		    		
		    			if(!is_null($property)){
		    				 
		    				
		    				$existComment = $this->searchComentario($comentario, $property);
		    				
		    				if(!$existComment){
		    					$bComentario = array(
		    							'comment' => $this->cleanString($comentario['comentario']),
		    							'idEntity' => $property['id'],
		    							'user' => array('id'=>$usuario['id']),
		    							'date' => $comentario['fecha'],
		    							'nkey' => $comentario['NKEY'],
		    							'sifincaOne' => true
		    					);
		    					 
		    					 
		    					$urlComentario = $this->server.'catchment/main/property/comment/'.$property['id'];
		    					
		    					 
		    					$apiComentario = $this->SetupApi($urlComentario, $this->user, $this->pass);
		    					 
		    					$result = $apiComentario->post($bComentario);
		    					 
		    					$result = json_decode($result, true);
		    					
		    					$json = json_encode($bComentario);
		    					
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
		    					
		    					}
		    				}else{
		    					echo "\nEl comentario ".$comentario['NKEY']." ya existe\n";
		    				}
		    				

		    			}
		    		
		    		}
	    		}
	    		
	    		$porDonde++;
	    		echo "\nPor donde vamos: ".$porDonde."\n";
	    		
	    	}
    	
    	}
    	
    	$finalTime = new \DateTime();
    	 
    	 
    	$diff = $startTime->diff($finalTime);
    	 
    	 
    	echo "\n\n Fecha inicial: ".$startTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Fecha final: ".$finalTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
   
    	echo "\n Total de comentarios pasados: ".$total."\n";
    	
    	
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
    
    function searchComentario($comentario, $property) {
    	
    	$nkey = $this->cleanString($comentario['NKEY']);
    	
    	$find = false;
    	    	
    	$url = $this->server.'catchment/main/property/comment/for/nkey/'.$nkey."/".$property['id'];
    	
        //echo "\n".$url."\n";

    	$api = $this->SetupApi($url, $this->user, $this->pass);
    	
    	$comment = $api->get();
    	$comment = json_decode($comment, true);
    	
    	if(isset($comment['total'])){
    		if($comment['total'] >= 1){
    			$find = true;
    		}
    	}
    	
    	return $find;
    }
    
    /**	
     * Buscar usuario sifinca2
     */
    function searchUsuario($email) {

    	$email = $this->cleanString($email);
		$email = strtolower($email);
    	
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