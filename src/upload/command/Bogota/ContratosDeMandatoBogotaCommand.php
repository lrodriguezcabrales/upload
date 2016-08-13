<?php
namespace upload\command\Bogota;

use upload\model\conveniosCartagena;
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

class ContratosDeMandatoBogotaCommand extends Command
{	
	
	public $server = 'http://www.sifinca.net/bogotaServer/web/app.php/';
	public $serverRoot = 'http://www.sifinca.net';
	
	public $localServer = 'http://10.102.1.22/';
		
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;
		
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
        $this->setName('contratosmandatoBogota')
		             ->setDescription('Comando para pasar contratos de mandato - Bogota');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $conn = new data(array(
            'server' =>'10.102.1.3'
            ,'user' =>'hherrera'
            ,'pass' =>'daniela201'
            ,'database' =>'sifinca_bog' 
            ,'engine'=>'mssql'
        ));

        $conexion = new conveniosCartagena($conn);
     	
        $this->crearContratosDeMandato($conexion);
        
    }
    
    public function crearContratosDeMandato($conexion) {
    
    	 
    	$contratosMandatoSF1 = $conexion->getContratosDeMandatoBogota();
    	$totalContratos = count($contratosMandatoSF1);
    	 
    	$porDonde = 0;
    	$startTime= new \DateTime();
    
    	echo "\nTotal contratos de mandato: ".$totalContratos."\n";
    	
    	for ($i = 0; $i < $totalContratos; $i++) {
    		 
    		$cm = $contratosMandatoSF1[$i];
    		 
    		$mandateContractSF2 = $this->searchMandateContractPorConvenioEinmueble($cm);
    		 
    		$convenioSF2 = $this->searchConvenioSF2($cm);
    		 
    		if(is_null($mandateContractSF2)){
    
    			$property = $this->searchProperty($cm['id_inmueble']);
    
    			if((!is_null($property)) && (!is_null($convenioSF2))){

    				echo "\nCreando contracto: ".$cm['id_inmueble'];
    				
    				$typeContract = $this->searchTipoContratoMandato($cm);
    				$user = $this->searchUsuario($cm);
    					
    				$bContratoDeMandato = array(
    						'consecutive' => $cm['id_inmueble'],
    						'leaseCommission' => $cm['por_cmsi'], //Comision de arriendo
    						'salesCommission' => $cm['por_seguro'], //Comision de garantia
    						'property' => $property,
    						'catcher' => $user,
    						'typesContracts' => $typeContract,
    						'agreement' => array('id'=> $convenioSF2['id']),
    						'convenioSifincaOne' => $this->cleanString($cm['id_convenio'])
    				);
    					
    				$json = json_encode($bContratoDeMandato);
    				//echo "\n\n".$json."\n\n";
    					
    				//print_r($mandateContractSF2);
    				$urlContratoMandato = $this->server.'catchment/main/mandatecontract';
    					
    				//echo "\n".$urlContratoMandato."\n";
    
    				$apiContratoMandato = $this->SetupApi($urlContratoMandato, $this->user, $this->pass);
    					
    				$result = $apiContratoMandato->post($bContratoDeMandato);
    					
    				$result = json_decode($result, true);
    					
    				//print_r($result);
    				if(isset($result['success'])){
    					if($result['success'] == true){
    						echo "\nOk, contrato mandato ".$cm['id_inmueble'];
    						//$total++;
    							
    					}
    				}
    				else{
    
    					$exist = false;
    
    					if(isset($result['message'])){
    						$msj = $result['message'];

    						$exist = strpos($msj, 'duplicate key');
    						
    						if($result['message'] == 'Error el contrato de mandato ya existe'){
    							$exist = true;
    						}
    					}
    
    					if(!$exist){
    
    						echo "\nError al crear contrato mandato ".$cm['id_inmueble']."\n";

    						$urlapiMapper = $this->server.'catchment/main/errormandatecontract';
    						$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    						
    						$error = array(
    						'mandateContract' => $cm['id_inmueble'],
    						'objectJson' => $json
    						);
    						
    						$apiMapper->post($error);
    						
    					}else{
    						echo "\nEl contrato de mandato ya existe: ".$cm['id_inmueble']."\n";
    					}
    
    				}
    				
    			}else{
    				echo "\nDatos insuficientes Inmuebles: ".$cm['id_inmueble']." -- Convenio: ".$cm['id_convenio']."\n";
    			}
    
    
    		}else {
    			echo "\nEl contrato de mandato ya existe: ".$cm['id_inmueble']."\n";
    		}
    		 
    		$porDonde++;
    		 
    		echo "\nVamos por: ".$porDonde."\n";
    
    	}
    	 
    	 
    	$finalTime = new \DateTime();
    
    
    	$diff = $startTime->diff($finalTime);
    
    
    	echo "\n\n Fecha inicial: ".$startTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Fecha final: ".$finalTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
    	
    	echo "\n------------------------------\n";
    	echo "\nBuscando Errores\n";
    	
    	$this->error();
    	
    }
    
     
    function searchTipoContratoMandato($convenio) {
    	 
    	$typeContract = null;
    
    	if($convenio['por_seguro'] > 0){
    		//Con garantia
    		$typeContract = array('id' => '53eee515-a699-498b-a65f-16336660c350');
    	}else{
    		//Sin garantia
    		$typeContract = array('id' => '54530e43-abfc-44e2-a98f-b9fcf51003fa');
    	}
    	 
    	return $typeContract;
    	 
    }
    
    /**
     * Buscar convenio en sifinca2
     * @param unknown $convenio
     * @return NULL
     */
    function searchConvenioSF2($convenio) {
    	 
    	 
    	$filter = array(
    			'value' => $this->cleanString($convenio['id_convenio']),
    			'operator' => '=',
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
    
    function searchMandateContractPorConvenioEinmueble($convenio) {
    
    	$filter = array();
    	 
//     	$filter[] = array(
//     			'value' => $this->cleanString($convenio['id_convenio']),
//     			'operator' => 'equal',
//     			'property' => 'convenioSifincaOne'
//     	);

    	$filter[] = array(
    			'value' => $this->cleanString($convenio['id_convenio']),
    			'operator' => 'equal',
    			'property' => 'consecutive'
    	);
    	 
    	$filter[] = array(
    			'value' => $this->cleanString($convenio['id_inmueble']),
    			'operator' => 'equal',
    			'property' => 'property.consecutive'
    	);
    	 
    	//print_r($filter);
    	 
    	$filter = json_encode($filter);
    
    	$urlMandateContract = $this->server.'catchment/main/mandatecontract?filter='.$filter;
    	//echo "\n".$urlMandateContract."\n";
    	
    	$apiMandateContract = $this->SetupApi($urlMandateContract, $this->user, $this->pass);
    
    	$mandateContract = $apiMandateContract->get();
    	$mandateContract = json_decode($mandateContract, true);
    
    	if($mandateContract['total'] > 0){
    		 
    		return $mandateContract['data'];
    		 
    	}else{
    		
    		return null;
    	}
    
    }
    
    /**
     * Buscar inmueble en Sifinca2
     * @param unknown $propertySF1
     * @return multitype:NULL |NULL
     */
    function searchProperty($propertySF1) {
    	 
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
    	 
    	 
    	if($property['total'] > 0){
    		 
    		$p = array(
    				'id'=> $property['data'][0]['id'],
    				'consecutive' => $property['data'][0]['consecutive']
    		);
    		//return $property['data'][0];
    		return $p;
    		 
    	}else{
    		return null;
    	}
    	 
    }
    
    function searchUsuario($convenio) {
    
    	$usuarioSF1 = $convenio['emailCatcher'];
    	$usuarioSF1 = $this->cleanString($usuarioSF1);
    	 
    	if($usuarioSF1 == 'inmobiliaria@araujoysegovia.com'){
    		$user = array('id' => 1);
    		return $user;
    	}
    	 
    	if($usuarioSF1 == 'hherrera@araujoysegovia.com'){
    		$user = array('id' => 1);
    		return $user;
    	}
    	 
    	 
    	$filter = array(
    			'value' => $this->cleanString($convenio['emailCatcher']),
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
    		return $user = array('id' => 1);
    	}
    	 
    }
     
    function error() {
    
    	$urlError = $this->server.'catchment/main/errormandatecontract';
    
    	$apiError = $this->SetupApi($urlError, $this->user, $this->pass);
    
    	$errores = $apiError->get();
    	$errores = json_decode($errores, true);
    	 
    	$totalErrores = count($errores['data']);
    	 
    	echo "\nTotal errores: ".$totalErrores."\n";
    	 
    	$startTime= new \DateTime(); 
    	$porDonde = 0;
    	
    	if($totalErrores > 0){
    		 
    		$urlTwo = $this->server.'catchment/main/mandatecontract';
    
    		$apiTwo = $this->SetupApi($urlTwo, $this->user, $this->pass);
    		 
    		for ($i = 0; $i < 1; $i++) {
    
    			$error = $errores['data'][$i];
    			 
    			if($error['deleted'] != true){
    				
    				$bError = json_decode($error['objectJson'], true);
    				
    				//print_r($bError);
    				
    				$result = $apiTwo->post($bError);
    				
    				$result = json_decode($result, true);
    				
    				
    				if(isset($result['success'])){
    					if($result['success'] == true){
    						echo "\nDeleted, contrato mandato ".$error['mandateContract'];
    				
    						$apiError->delete($error['id']);
    				
    					}
    				}else{
    					
    					$exist = false;
    					
    					if(isset($result['message'])){
    						$exist = false;
    						
    						$exist = strpos($result['message'], 'duplicate key');
    						
    						
    						if($result['message'] == 'Error el contrato de mandato ya existe'){
    							$exist = true;
    						}
    						
    						if($exist){
    							echo "\nDeleted, contrato mandato ".$error['mandateContract'];
    							$apiError->delete($error['id']);
    						}
    					}else{
    						echo "\nEl error persiste ".$error['mandateContract']."\n";
    					}
    					    					
    				}
    			}else{
    				
    				echo "\nNo\n";
    			}
    			    
    			$porDonde++;
    			echo "\n Vamos por: ".$porDonde."\n";
    
    		}
    
    	}else{
    		echo "\n No hay errores\n";
    	}
    	
    	$finalTime = new \DateTime();
    	
    	
    	$diff = $startTime->diff($finalTime);
    	
    	
    	echo "\n\n Fecha inicial: ".$startTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Fecha final: ".$finalTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
    
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