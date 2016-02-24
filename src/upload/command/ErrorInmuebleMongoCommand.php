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

class ErrorInmuebleMongoCommand extends Command
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
		
	public $colombia = '8701307b-d8bd-49f1-8a91-5d0f7b8046b3';
	public $bolivar = 'a7ff9a96-5a2f-4bba-94aa-d45a15f67f66';
	public $cartagena = '994b009d-50a5-44dd-8060-878a10f4dd00';
	
	public $idTypeCedula = '6f80343e-f629-492a-80d1-a7e197c7cf48';
	
	public $contactTypeOther = 'ac76deff-6371-4264-b724-b24b98803b94';
	
	public  $typeAddressHome = '8b8b75ae-6338-461f-8bbd-fc1283621d83';
	public  $typeAddressCorrespondencia = 'e8e9cc62-ec79-4453-8247-a57cc1cf4712';

	
	public  $attributeAlcobas = 'b4b7ea95-ea41-4546-95e0-82b56a947411';
	public  $attributeBanos = 'bb0666a4-e3bf-45eb-8760-d6602a868ed8';
	
	public $buildingTypeEdificio = '345bc0a2-4880-4f13-b06f-80cd7405805c';
	
    protected function configure()
    {
        $this->setName('errorinmuebles')
		             ->setDescription('Error inmuebles');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Inmuebles con error \n");
        
        $this->searchErrorInmueble();
         
    }
    
    function searchErrorInmueble() {
    	    	
    	$urlErrorProperty = $this->server.'catchment/main/errorproperty';
    	 
    	echo "\n".$urlErrorProperty."\n";
    	 
    	$apiErrorProperty = $this->SetupApi($urlErrorProperty, $this->user, $this->pass);
    	
    	 
    	$errorProperty = $apiErrorProperty->get();
    	$errorProperty = json_decode($errorProperty, true);
    	
    	$totalErrores = $errorProperty['total'];
    	
    	echo "\nTotal errores: ".$totalErrores."\n";
    	if($totalErrores > 0){
    		 
    		$urlapiInmueble = $this->server.'catchment/main/property';
    		
    		$apiInmueble = $this->SetupApi($urlapiInmueble, $this->user, $this->pass);
    		 
    		$officeCentro = array(
    				'id' => 'bee55884-15dc-406f-94f7-547288ebe20d'
    		);
    		
    		
    		$errores = $errorProperty['data'];
    		
    		
    		$startTime= new \DateTime();
    		 
    		$porDonde = 0;
    		
    		for ($i = 0; $i < $totalErrores; $i++) {
    			$error = $errores[$i];
    			
    			$propertySF2 = $this->searchPropertySF2($error['property']);
    			
    			if(!$propertySF2){
    				//echo "\n".$error['objectJson']."\n";
    				 
    				$bError = json_decode($error['objectJson'], true);
    				 
    				//print_r($bError);
    				$result = $apiInmueble->post($bError);
    				 
    				$result = json_decode($result, true);
    				 
    				//print_r($result);
    				 
    				if($result['success'] == true){
    					echo "\nOk\n";
    					
    					$urlapiMapper = $this->server.'catchment/main/errorproperty/';
    					
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
    				
    				
    				echo "\nEl inmueble ya existe\n";
    				
    				$urlapiMapper = $this->server.'catchment/main/errorproperty/';
    				
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
    
    
    function searchPropertySF2($propertySF1) {
    
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
    		 
    		return true;
    		 
    	}else{
    		return false;
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