<?php
namespace upload\command\Cartagena;

use upload\model\comentariosContratosArriendos;
use upload\lib\data;
use upload\lib\api;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use GearmanClient;

class ComentariosContratoArriendoCommand extends Command
{	
	
	public $server = 'http://www.sifinca.net/sifinca/web/app.php/';
	public $serverRoot = 'http://www.sifinca.net/';
	
	public $localServer = 'http://10.102.1.22/';
		
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;	
	
	
    protected function configure()
    {
        $this->setName('comentarioscontratos')
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

        $conexion = new comentariosContratosArriendos($conn);      
        
		
		
		$this->crearComentarios($conexion);
		//$this->crearComentarioPorDemanda($conexion);
		
		
    }
    
    function crearComentarios($conexion) {
    	
    	
    	$startTime= new \DateTime();
    	
    	$conArriendos = $conexion->getContratosArriendo();
    	
    	$total = 0;
    	
    	$porDonde = 0;
    	
    	for ($i = 0; $i < count($conArriendos); $i++) {
    		
    		$conArriendo = $conArriendos[$i];
    		
    		echo "\nContrato ".$conArriendo['id_contrato']."\n";
    		
    		$contratoSF2 = $this->searchContratoArriendo($conArriendo);
    		
    		
    		
    		if(!is_null($contratoSF2)){
    			
    			$comentarios = $conexion->getComentariosContratosArriendo($this->cleanString($conArriendo['id_contrato']));
    			 
    			$totalComentarios = count($comentarios);
    			     			
    			echo "\nTotal comentarios: ".$totalComentarios."\n";
    			
    			for ($j = 0; $j < $totalComentarios; $j++) {
    				 
    				 
    				$comentario = $comentarios[$j];
    			
    				 
    				$commentS = $this->searchComment($comentario, $contratoSF2);
    				 
    				if(!$commentS){
    						
    					$usuario = $this->searchUsuario($comentario['email']);
    			
    					$ccomment = $this->cleanString($comentario['comentarioAll']);
    						
    					$bComentario = array(
    							'comment' => $ccomment,
    							'idEntity' => $contratoSF2['id'],
    							'user' => array('id'=>$usuario['id']),
    							'date' => $comentario['fecha'],
    							'nkey' => $comentario['NKEY'],
    							'sifincaOne' => true
    					);
    			
    					//$json = json_encode($bComentario);
    			
    					//echo "\n".$json."\n";
    			
    					$urlComentario = $this->server.'catchment/main/leasingcontract/comment/'.$contratoSF2['id'];
    			
    					//echo "\n".$urlComentario."\n";
    			
    					$apiComentario = $this->SetupApi($urlComentario, $this->user, $this->pass);
    			
    					$result = $apiComentario->post($bComentario);
    			
    					$result = json_decode($result, true);
    			
    			
    			
    					$json = json_encode($bComentario);
    			
    			
    					if($result[0]['success'] == true){
    						echo "\nOk contrato - ".$conArriendo['id_contrato']."\n";
    						$total++;
    					}else{
    						echo "\nError cometario - contrato de arriendo: ".$conArriendo['id_contrato']."\n";
    			
    					}
    			
    				}else{
    			
    					echo "\nEl comentario ya existe\n";
    				}
    				 
    				 
    				 
    				 
    			
    			}
    			
    			
    			
    		}else{
    			echo "\nContrato no encontrado - ".$conArriendo['id_contrato']."\n";
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
     
    
    function searchComment($comentario, $contrato) {
    	
    	$nkey = $this->cleanString($comentario['NKEY']);
    	 
    	$find = false;
    	
    	$url = $this->server.'catchment/main/property/comment/for/nkey/'.$nkey."/".$contrato['id'];
    	 
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
    
    function searchContratoArriendo($contrato) {
    	 
    	$codigoContrato = $this->cleanString($contrato['id_contrato']);
    
    	 
    	 
    	$filter = array(
    			'value' => $codigoContrato,
    			'operator' => 'equal',
    			'property' => 'consecutive'
    	);
    	$filter = json_encode(array($filter));
    	 
    	$urlContract = $this->server.'catchment/main/leasingcontract?filter='.$filter;
    	 
    	echo "\n".$urlContract."\n";
    	 
    	$apiContract = $this->SetupApi($urlContract, $this->user, $this->pass);
    	 
    	$contract = $apiContract->get();
    	$contract = json_decode($contract, true);
    	 
    
    	if($contract['total'] > 0){
    		 
    		$c = array('id' => $contract['data'][0]['id']);
    		return $c;
    		 
    	}else{
    		return null;
    	}
    
    }
    
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