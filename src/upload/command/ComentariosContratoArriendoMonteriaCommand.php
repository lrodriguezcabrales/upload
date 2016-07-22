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

class ComentariosContratoArriendoMonteriaCommand extends Command
{	
	
	public $server = 'http://www.sifinca.net/sifinca/web/app.php/';
	public $serverRoot = 'http://www.sifinca.net/';
	
	public $localServer = 'http://10.102.1.22/';
	
// 	public $server = 'http://10.102.1.22/sifinca/web/app.php/';
// 	public $serverRoot = 'http:/10.102.1.22/';

// 	public $server = 'http://104.130.12.152/sifinca/web/app_dev.php/';
// 	public $serverRoot = 'http:/104.130.12.152/';
	
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;	
	
	
    protected function configure()
    {
        $this->setName('comentarioscontratosMonteria')
		             ->setDescription('Comando para pasar comentarios de contratos de arriendo');
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
        
		
		
		$this->crearComentarios($conexion);
		
    }
    
    function crearComentarios($conexion) {
    	
    	
    	
    	$urlConArriendo = $this->server.'catchment/main/leasingcontract/only/ids';
    	 
    	$apiConArriendo = $this->SetupApi($urlConArriendo, $this->user, $this->pass);
    	 
    	$conArriendos = $apiConArriendo->get();
    	$conArriendos = json_decode($conArriendos, true);
    	//echo $conArriendos['total']

    	 
    	echo "\nTotal de contratos: ".count($conArriendos['data'])."\n";
    	 
    	$totalConArriendos = count($conArriendos['data']);
    	
    	$porDonde = 0;
    	$total = 0;
    	
    	$startTime= new \DateTime();
    	
    	for ($i = 5550; $i < 6000; $i++) {
    		
    		
    		$conArriendo = $conArriendos['data'][$i];
    		
    		//print_r($conArriendo);
    		echo "\nContrato ".$conArriendo['consecutive']."\n";
    		
    		$comentarios = $conexion->getComentariosContratosArriendo($this->cleanString($conArriendo['consecutive']));
    		 
    		
    		 
    		$totalComentarios = count($comentarios);
    		 
    		
    		echo "\nTotal comentarios: ".$totalComentarios."\n";
    		
    		 
    		
    		
    		for ($j = 0; $j < $totalComentarios; $j++) {
    			
    			
    			$comentario = $comentarios[$j];
    		
    			$commentS = $this->searchComment($comentario);
    			
    			if(!$commentS){
    				
    				//echo "\nEntro aqui\n";
    				//print_r($comentario);
    				
    				 
    				$usuario = $this->searchUsuario($comentario['email']);
    				
    				//     		echo "\ninmueble\n";
    				
    				
    					$ccomment = $this->cleanString($comentario['comentarioAll']);
    						
    					//echo "\n".$ccomment."\n";
    					
    					$bComentario = array(
    							'comment' => $ccomment,
    							'idEntity' => $conArriendo['id'],
    							'user' => array('id'=>$usuario['id']),
    							'date' => $comentario['fecha'],
    							'nkey' => $comentario['NKEY'],
    							'sifincaOne' => true
    					);
    						
    					//$json = json_encode($bComentario);
    				
    					//echo "\n".$json."\n";
    						
    					$urlComentario = $this->server.'catchment/main/leasingcontract/comment/'.$conArriendo['id'];
    				
    					//echo "\n".$urlComentario."\n";
    						
    					$apiComentario = $this->SetupApi($urlComentario, $this->user, $this->pass);
    						
    					$result = $apiComentario->post($bComentario);
    						
    					$result = json_decode($result, true);
    						
    						
    				
    					$json = json_encode($bComentario);
    				
    						
    					//echo $json;
    						
    						
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
    						
    				
    						
    				
    				
    			}else{
    				
    				
    				echo "\nEl comentario ya existe\n";
    			}
    			
    			
    			
    			
    		
    		}
    		
    		$porDonde++;
    		echo "\n Por donde vamos: ".$porDonde."\n";
    		
    	}
    	
    	
    	
    	$finalTime = new \DateTime();
    	 
    	 
    	$diff = $startTime->diff($finalTime);
    	 
    	 
    	echo "\n\n Fecha inicial: ".$startTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Fecha final: ".$finalTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
   
    	echo "\nTotal de comentarios pasados: ".$total."\n";
    }
    
    
 
    
    function searchComment($comentario) {
    
    	 
    	$filter = array(
    			'value' => $this->cleanString($comentario['NKEY']),
    			'operator' => 'equal',
    			'property' => 'nkey'
    	);
    	
    	$filter = json_encode(array($filter));
    
    	$urlComment = $this->server.'comment/comment?filter='.$filter;
    
    	//echo "\n".$urlComment."\n";
    	
    	$apiComment = $this->SetupApi($urlComment, $this->user, $this->pass);
    
    	$comment = $apiComment->get();
    	$comment = json_decode($comment, true);
    
    	//     	echo "\n\nInmueble\n";
    	//     	print_r($property['data'][0]);
    	 
    	$totalComment = count($comment['data']);
    	
    	if($totalComment > 0){
    		 
    		//echo "\nlo encontro\n";
    		return true;
    		 
    	}else{
    		
    		//echo "\nes nuevo \n";
    		return false;
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
    	
    	if(!isset($user['total'])){
    		return $user = array('id' => 203);
    	}
    
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
    
    function login() {
    
    	if(is_null($this->token)){
    
    		echo "\nEntro a login\n";
    
    		$url= $this->server."login";
    		$headers = array(
    				'Accept: application/json',
    				'Content-Type: application/json',
    		);
    		 
    		$a = new api($url, $headers);
    		 
    
    		$result = $a->post(array("user"=>$this->user,"password"=>$this->pass));
    		$result = json_decode($result, true);
    		 
    		//print_r($result);
    
    		//echo "\n".$result['id']."\n";
    
    
    		if(isset($result['code'])){
    			if($result['code'] == 401){
    
    				$this->login();
    			}
    		}else{
    
    			if(isset($result['id'])){
    
    				$this->token = $result['id'];
    			}else{
    				echo "\nError en el login\n";
    				$this->token = null;
    			}
    
    		}
    	}
    
    
    }
    
    function SetupApi($urlapi,$user,$pass){
    
    	$headers = array(
    			'Accept: application/json',
    			'Content-Type: application/json',
    	);
    
    	$a = new api($urlapi, $headers);
    
    	$this->login();
    
    	if(!is_null($this->token)){
    
    		$headers = array(
    				'Accept: application/json',
    				'Content-Type: application/json',
    				//'x-sifinca: SessionToken SessionID="56cf041b296351db058b456e", Username="lrodriguez@araujoysegovia.net"'
    				'x-sifinca: SessionToken SessionID="'.$this->token.'", Username="'.$this->user.'"',
    		);
    
    		//     	print_r($headers);
    
    		$a->set(array('url'=>$urlapi,'headers'=>$headers));
    
    		//print_r($a);
    
    		return $a;
    
    	}else{
    		echo "\nToken no valido\n";
    	}
    
    
    }
}