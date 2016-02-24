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

class ErrorAgreementCommand extends Command
{	
	
 	public $server = 'http://162.242.247.95/sifinca/web/app.php/';
 	public $serverRoot = 'http://162.242.247.95/';
	
	public $localServer = 'http://10.102.1.22/';
	
// 	public $server = 'http://10.102.1.22/sifinca/web/app.php/';
// 	public $serverRoot = 'http:/10.102.1.22/';

	//public $server = 'http://162.242.245.207/sifinca/web/app_dev.php/';
	//public $serverRoot = 'http:/162.242.245.207/';
	
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
		
	
    protected function configure()
    {
        $this->setName('errorconvenios')
		             ->setDescription('Error convenios');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Convenios con error \n");
        
        $this->searchErrorInmueble();
         
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
    
    protected function SetupApi($urlapi,$user,$pass){
    
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
    			//'x-sifinca:SessionToken SessionID="56619d57ed060f8c57c1109d", Username="sifincauno@araujoysegovia.com"'
    			'x-sifinca: SessionToken SessionID="'.$token.'", Username="'.$user.'"',
    	);
    
    	//     	print_r($headers);
    	 
    	$a->set(array('url'=>$urlapi,'headers'=>$headers));
    
    	//print_r($a);
    	 
    	return $a;
    
    }
    
    
}