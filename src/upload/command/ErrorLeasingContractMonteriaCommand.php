<?php
namespace upload\command;

use upload\model\inmueblesCartagena;
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

class ErrorLeasingContractMonteriaCommand extends Command
{	
	
 	public $server = 'http://www.sifinca.net/sifinca/web/app.php/';
 	public $serverRoot = 'http://www.sifinca.net/';
	
	public $localServer = 'http://10.102.1.22/';
	
// 	public $server = 'http://10.102.1.22/sifinca/web/app.php/';
// 	public $serverRoot = 'http:/10.102.1.22/';

	//public $server = 'http://162.242.245.207/sifinca/web/app_dev.php/';
	//public $serverRoot = 'http:/162.242.245.207/';
	
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;
	
    protected function configure()
    {
        $this->setName('errorconvenios')
		             ->setDescription('Error convenios');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Convenios con error \n");
        
        //$this->searchErrorInmueble();
        $this->searchErrorContratoArriendo();
    }
    
    function searchErrorInmueble() {
    	    	
    	$urlErrorProperty = $this->server.'catchment/main/erroragreement';
    	 
    	echo "\n".$urlErrorProperty."\n";
    	 
    	$apiErrorProperty = $this->SetupApi($urlErrorProperty, $this->user, $this->pass);
    	
    	 
    	$errorProperty = $apiErrorProperty->get();
    	$errorProperty = json_decode($errorProperty, true);
    	
    	$totalErrores = $errorProperty['total'];
    	
    	echo "\nTotal errores: ".$totalErrores."\n";
    	if($totalErrores > 0){
    		 
    		$urlapiInmueble = $this->server.'catchment/main/agreement';
    		
    		$apiInmueble = $this->SetupApi($urlapiInmueble, $this->user, $this->pass);
    		 
    		$officeCentro = array(
    				'id' => 'bee55884-15dc-406f-94f7-547288ebe20d'
    		);
    		
    		
    		$errores = $errorProperty['data'];
    		
    		
    		$startTime= new \DateTime();
    		 
    		$porDonde = 0;
    		
    		for ($i = 0; $i < $totalErrores; $i++) {
    			$error = $errores[$i];
    			
    			$propertySF2 = $this->searchConvenioSF2($error['agreement']);
    			
    			if(!$propertySF2){
    				//echo "\n".$error['objectJson']."\n";
    				 
    				$bError = json_decode($error['objectJson'], true);
    				 
    				//print_r($bError);
    				$result = $apiInmueble->post($bError);
    				 
    				$result = json_decode($result, true);
    				 
    				//print_r($result);
    				 
    				if($result['success'] == true){
    					echo "\nOk\n";
    					
    					$urlapiMapper = $this->server.'catchment/main/erroragreement/';
    					
    					//echo "\n".$urlapiMapper."\n";
    					$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    					
    					$apiMapper->delete($error['id']);
    					 
    				}else{
    					echo "\nError\n";
    					 
    					//     				$urlapiMapper = $this->server.'catchment/main/errorproperty';
    					//     				$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    					 
    					//     				$error = array(
    					//     						'property' => $inmueble['id_inmueble'],
    					//     						'objectJson' => $json
    					//     				);
    					 
    					//     				$apiMapper->post($error);
    				    
    				}
    				
    			}else{
    				
    				
    				echo "\nEl convenio ya existe\n";
    				
    				$urlapiMapper = $this->server.'catchment/main/erroragreement/';
    				
    				//echo "\n".$urlapiMapper."\n";
    				$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    				
    				$apiMapper->delete($error['id']);
    			}
    			
				$porDonde++;
				
    			echo "\n Vamos por: ".$porDonde."\n";
    			
    		}
    		 
    		$finalTime = new \DateTime();
    		
    		
    		$diff = $startTime->diff($finalTime);
    		
    		
    		echo "\n\n Fecha inicial: ".$startTime->format('Y-m-d H:i:s')."\n";
    		echo "\n Fecha final: ".$finalTime->format('Y-m-d H:i:s')."\n";
    		echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
    		
    	}else{
    		echo "No hay errores";
    	}
    }
    
    
    function searchConvenioSF2($convenio) {
    	
    	
    	$filter = array(
    			'value' => $this->cleanString($convenio),
    			'operator' => 'equal',
    			'property' => 'consecutive'
    	);
    	$filter = json_encode(array($filter));
    	
    	$urlAgreement = $this->server.'catchment/main/agreement?filter='.$filter;
    	
    	$apiAgreement = $this->SetupApi($urlAgreement, $this->user, $this->pass);
    	
    	$agreement = $apiAgreement->get();
    	$agreement = json_decode($agreement, true);
    	
    	
    	if($agreement['total'] > 0){
    		 
    		return $agreement['data'][0];
    		 
    	}else{
    		return null;
    	}
    	
    }
    
    
    
    
    
    
    function searchErrorContratoArriendo() {
    
    	$urlErrorProperty = $this->server.'catchment/main/errorleasingcontract';
    
    	echo "\n".$urlErrorProperty."\n";
    
    	$apiErrorProperty = $this->SetupApi($urlErrorProperty, $this->user, $this->pass);
    	 
    
    	$errorProperty = $apiErrorProperty->get();
    	$errorProperty = json_decode($errorProperty, true);
    	 
    	$totalErrores = count($errorProperty['data']);
    	 
    	echo "\nTotal errores: ".$totalErrores."\n";
    	if($totalErrores > 0){
    		 
    		$urlapiInmueble = $this->server.'catchment/main/leasingcontract';
    
    		$apiInmueble = $this->SetupApi($urlapiInmueble, $this->user, $this->pass);
    		 
    		
    
    		$errores = $errorProperty['data'];
    
    
    		$startTime= new \DateTime();
    		 
    		$porDonde = 0;
    
    		for ($i = 0; $i < 1; $i++) {
    			$error = $errores[$i];
    			 
    			print_r($error);
    			$propertySF2 = $this->searchCOASF2($error['agreement']);
    			 
    			if(!$propertySF2){
    				//echo "\n".$error['objectJson']."\n";
    					
    				$bError = json_decode($error['objectJson'], true);
    					
    				//print_r($bError);
    				$result = $apiInmueble->post($bError);
    					
    				$result = json_decode($result, true);
    					
    				//print_r($result);
    					
    				if($result['success'] == true){
    					echo "\nOk\n";
    						
    					$urlapiMapper = $this->server.'catchment/main/leasingcontract/';
    						
    					//echo "\n".$urlapiMapper."\n";
    					$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    						
    					$apiMapper->delete($error['id']);
    
    				}else{
    					echo "\nError\n";
    
    					//     				$urlapiMapper = $this->server.'catchment/main/errorproperty';
    					//     				$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    
    					//     				$error = array(
    					//     						'property' => $inmueble['id_inmueble'],
    					//     						'objectJson' => $json
    					//     				);
    
    					//     				$apiMapper->post($error);
    
    				}
    
    			}else{
    
    
    				echo "\nEl convenio ya existe\n";
    
    				$urlapiMapper = $this->server.'catchment/main/leasingcontract/';
    
    				//echo "\n".$urlapiMapper."\n";
    				$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    
    				$apiMapper->delete($error['id']);
    			}
    			 
    			$porDonde++;
    
    			echo "\n Vamos por: ".$porDonde."\n";
    			 
    		}
    		 
    		$finalTime = new \DateTime();
    
    
    		$diff = $startTime->diff($finalTime);
    
    
    		echo "\n\n Fecha inicial: ".$startTime->format('Y-m-d H:i:s')."\n";
    		echo "\n Fecha final: ".$finalTime->format('Y-m-d H:i:s')."\n";
    		echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
    
    	}else{
    		echo "No hay errores";
    	}
    }
    
    
    function searchCOASF2($convenio) {
    	 
    	 
    	$filter = array(
    			'value' => $this->cleanString($convenio),
    			'operator' => 'equal',
    			'property' => 'consecutive'
    	);
    	$filter = json_encode(array($filter));
    	 
    	$urlAgreement = $this->server.'catchment/main/leasingcontract?filter='.$filter;
    	 
    	$apiAgreement = $this->SetupApi($urlAgreement, $this->user, $this->pass);
    	 
    	$agreement = $apiAgreement->get();
    	$agreement = json_decode($agreement, true);
    	 
    	 
    	if($agreement['total'] > 0){
    		 
    		return $agreement['data'][0];
    		 
    	}else{
    		return null;
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