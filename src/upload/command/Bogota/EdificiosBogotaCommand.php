<?php
namespace upload\command\Bogota;

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

class EdificiosBogotaCommand extends Command
{	
 	public $server = 'http://www.sifinca.net/bogotaServer/web/app.php/';
 	public $serverRoot = 'http://www.sifinca.net/';
	
	public $localServer = 'http://10.102.1.22/';
	
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;	
	
	
	
	public $idTypeCedula = '6f80343e-f629-492a-80d1-a7e197c7cf48';
	
	public $contactTypeOther = 'ac76deff-6371-4264-b724-b24b98803b94';
	
	public  $typeAddressHome = '8b8b75ae-6338-461f-8bbd-fc1283621d83';
	public  $typeAddressCorrespondencia = 'e8e9cc62-ec79-4453-8247-a57cc1cf4712';

	
	public  $attributeAlcobas = 'b4b7ea95-ea41-4546-95e0-82b56a947411';
	public  $attributeBanos = 'bb0666a4-e3bf-45eb-8760-d6602a868ed8';
	
	public $buildingTypeEdificio = '345bc0a2-4880-4f13-b06f-80cd7405805c';
	
	public $office = 'fad1de4e-f0f5-4e0c-9e90-e3c717d2ca63'; //Oficina Chico 
	
	public $colombia = '8701307b-d8bd-49f1-8a91-5d0f7b8046b3';
	public $cundinamarca = 'dbb2a916-1093-4de6-b3e2-42757434b4a3';
	public $bogota = '551fccf7-db4e-4992-a8a4-f4f9a9d24c7a';
	
    protected function configure()
    {
        $this->setName('edificiosBogota')
		             ->setDescription('Edificios - Bogota');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Datos de inmuebles SF1 \n");

        $conn = new data(array(
            'server' =>'10.102.1.3'
            ,'user' =>'hherrera'
            ,'pass' =>'daniela201'
            ,'database' =>'sifinca_bog' 
            ,'engine'=>'mssql'
        ));

        $conexion = new inmueblesCartagena($conn);
        
        //$inmuebles = $conexion->getInmueblesBogota();
	    $this->buildEdificios($conexion);
	   
        
    }
    

    function buildEdificios($conexion) {
    	 
    	$edificiosSF1 = $conexion->getEdificios();
    	 
    	$urlEdificio = $this->server.'catchment/main/building';
    	$apiEdificio = $this->SetupApi($urlEdificio, $this->user, $this->pass);
    	 
    	$totalEdificios = count($edificiosSF1);
    	 
    	$total = 0;
    	 
    	
    	
    	
    	for ($i = 0; $i < $totalEdificios; $i++) {
    		
    		$edificio = $edificiosSF1[$i];
    
    		
    		$exist = $this->searchEdificio($edificio);
    		
    		if(!$exist){
    			
    			$urlBuildingType = $this->server.'admin/sifinca/mapper/buildingType/'.$edificio['id_tipo_edif'];
    			//echo "\n".$urlPropertyType."\n";
    			$apiBuildingType = $this->SetupApi($urlBuildingType, $this->user, $this->pass);
    			 
    			$buildingTypeMapper = $apiBuildingType->get();
    			$buildingTypeMapper = json_decode($buildingTypeMapper, true);
    			//print_r($propertyTypeMapper);
    			//$buildingType =  array('id'=>$this->buildingTypeEdificio);
    			$buildingType = null;
    			
    			if($buildingTypeMapper['total'] > 0){
    			
    				$buildingType = $buildingTypeMapper['data']['0']['idTarget'];
    				;
    				if(!is_null($buildingType)){
    			
    					$buildingType = array('id'=>$buildingType);
    			
    					if($buildingTypeMapper['total'] == 0){
    						$buildingType = $this->buildingTypeEdificio;
    					}
    			
    				}
    			}else{
    				$buildingType =  array('id'=>$this->buildingTypeEdificio);
    			}
    			
    			$telefonos = array();
    			$numeroTelefono = $this->cleanString($edificio['telefono']);
    			if(strlen($numeroTelefono) > 0){
    				$telefono = array(
    						'number' => $this->cleanString($edificio['telefono']),
    						'phoneType' => array('id' => '2f49f417-9db1-4cb6-98c6-7f7f6af21399'), //Fijo
    						'country' => array('id' => $this->colombia)
    			
    				);
    				$telefonos[] = $telefono;
    			}
    			
    			
    			
    			//print_r($edificio);
    			
    			//return ;
    			
    			$direccion = array(
    					'address' => $this->cleanString($edificio['direccion']),
    					'country' => array('id' => $this->colombia),
    					'department' => array('id' => $this->cundinamarca),
    					'town' => array('id' => $this->bogota),
    					'district' => null,
    					'typeAddress' => array('id'=>$this->typeAddressCorrespondencia), //Correspondecia
    			);
    			
    			
    			$trimEncargado = trim($edificio['encargado']);
    			
    			if(strlen($trimEncargado) > 0){
    				$contacto = array(
    						'name' => $this->cleanString($edificio['encargado']),
    						'phone' => $this->cleanString($edificio['telefono']),
    						'position' => 'N/A',
    						'contactType' => array('id'=> $this->contactTypeOther) //Otro
    				);
    			}else{
    				$contacto = null;
    			}
    			
    			
    			 
    			 
    			$beneficiary = $this->searchClientSF2($edificio['ID_ADMIN']);
    			 
    			//echo $beneficiary['id'];
    			//     		return;
    			$clienteSF1 = $this->searchClienteSF1($conexion, $edificio['ID_ADMIN']);
    			$clienteSF1 = $clienteSF1[0];
    			if(!is_null($beneficiary) && !is_null($clienteSF1)){
    			
    			}
    			 
    			//print_r($clienteSF1);
    			 
    			$bank = $this->searchBank($clienteSF1['id_banco']);
    			 
    			$payType = $this->searchPayType($clienteSF1['pagos']);
    			 
    			$accountType = $this->searchAccountType($clienteSF1['tipo_cuenta']);
    			 
    			$payForm = array(
    					'payType' => $payType,
    					'bank' => $bank,
    					'accountType' => $accountType,
    					'accountNumber' => $this->cleanString($clienteSF1['no_cuenta']),
    					'beneficiary' => $beneficiary
    			);
    			
    			$idEdificio = $edificio['id_edificio'];
    			$idEdificio = trim($idEdificio);
    			
    			$nombreEdificio = $this->cleanString($edificio['Nombre']);
    			
    			$bEdificio = array(
    					'idSifincaOne' => $idEdificio,
    					'name' => $nombreEdificio,
    					'buildingType' => $buildingType,
    					'address' => $direccion,
    					//'description' => $edificio[''],
    					'contact' => $contacto,
    					'payForm' => $payForm,
    					'phones' => $telefonos
    			);
    			
    			
    			$json = json_encode($bEdificio);
    			 
    			//     		echo "\njson\n";
    			//     		echo $json;
    			
    			$result = $apiEdificio->post($bEdificio);
    			
    			//echo "\nresult\n";
    			//print_r($result);
    			
    			$result = json_decode($result, true);
    			
    			
    			
    			
    			if(isset($result['success'])){
    				if($result['success'] == true){
    					echo "\nOk\n";
    					$total++;
    				}
    			}else{
    				echo "\nError\n";
    			
    			
    			
    			
    				//echo $json;
    				//echo "\nclient\n";
    				//print_r($bClient);
    			
    				$urlapiMapper = $this->server.'catchment/main/errorbuilding';
    				$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    			
    				$error = array(
    						'building' => $edificio['id_edificio'],
    						'objectJson' => $json,
    						'error' => $result['message']
    				);
    			
    				$apiMapper->post($error);
    			
    			}
    			
    			
    		}else{
    			
    			echo "\nEl edificio ya existe: ".$nombreEdificio."\n";
    		}
    		
    		
    		 
    
    	}
    	 
    	echo "\n\nTotal edificios pasados ".$total."\n";
    	 
    }
    
    /**
     * Buscar que el edificio a que pertene el inmueble
     */
    function searchEdificio($edificio) {
    
    
    	if($edificio['id_edificio']){
    
    		$filter = array(
    				'value' => $this->cleanString($edificio['id_edificio']),
    				'operator' => 'equal',
    				'property' => 'idSifincaOne'
    		);
    		$filter = json_encode(array($filter));
    
    		$urlBuildingSF2 = $this->server.'catchment/main/building?filter='.$filter;
    
    		$apiBuildingSF2 = $this->SetupApi($urlBuildingSF2, $this->user, $this->pass);
    		 
    		$buildingSF2 = $apiBuildingSF2->get();
    		 
    		$buildingSF2 = json_decode($buildingSF2, true);
    		 
    		if($buildingSF2['total'] > 0){
    			    
    			return true;
    			 
    		}else{
    			return false;
    		}
    
    	}else{
    		return false;
    	}
    
    
    
    }
    
    
    function searchClientSF2($identificacion){
        	 
    	$identificacion = $this->cleanString($identificacion);
    	 
    	$relations = array(
    			'identity',
    			'phones'
    	);
    	$relations = json_encode($relations);
    	 
    	$filter = array(
    			'value' => $identificacion,
    			'operator' => 'equal',
    			'property' => 'identity.number'
    	);
    	$filter = json_encode(array($filter));
    
    	 
    	$urlClientSF2 = $this->server.'crm/main/zero/client?relations='.$relations.'&filter='.$filter;
    		 
    	$apiClientSF2 = $this->SetupApi($urlClientSF2, $this->user, $this->pass);
    	    
    	$clientSF2 = $apiClientSF2->get();
    	
    	$clientSF2 = json_decode($clientSF2, true);
    	 
    	if($clientSF2['total'] > 0){
    
    		return $clientSF2['data'][0];
    
    	}else{
    		return null;
    	}
    
    	 
    }
     
    function searchClienteSF1($inmueblesCtg, $id){
    	 
    	$clienteSF1 = $inmueblesCtg->getCliente($id);
    	 
    	return $clienteSF1;
    }
    
    function searchBank($code) {
    	$filter = array(
    			'value' => $code,
    			'operator' => '=',
    			'property' => 'code'
    	);
    	$filter = json_encode(array($filter));
    
    	$urlBank = $this->server.'admin/accounts/zero/bank?filter='.$filter;
    	 
    	//echo "\n".$urlBank."\n";
    	 
    	$apiBank = $this->SetupApi($urlBank, $this->user, $this->pass);
    
    	 
    	$bankSF2 = $apiBank->get();
    	$bankSF2 = json_decode($bankSF2, true);
    
    	if($bankSF2['total'] > 0){
    		 
    		return $bankSF2['data'][0];
    		 
    	}else{
    		return null;
    	}
    }
    
    function searchPayType($pt) {
    	 
    	$pt = trim($pt);
    	 
    	if($pt == 'CHEQUE'){
    		$pt = 'cheque';
    	}
    	 
    	if($pt == 'CONSIGNACION'){
    		$pt = 'transfer';
    	}
    	 
    	if($pt == 'NO GIRAR'){
    		$pt = 'ng';
    	}
    
    	if($pt == 'PAGOS'){
    		$pt = 'p';
    	}
    	 
    	 
    	$filter = array(
    			'value' => $pt,
    			'operator' => '=',
    			'property' => 'value'
    	);
    	$filter = json_encode(array($filter));
    
    	$urlPayType = $this->server.'admin/sifinca/attributelist?filter='.$filter;
    
    	//     	echo "\n".$urlPayType."\n";
    
    	$apiPayType = $this->SetupApi($urlPayType, $this->user, $this->pass);
    
    	$payType = $apiPayType->get();
    	$payType = json_decode($payType, true);
    	 
    
    	if($payType['total'] > 0){
    		 
    		return $payType['data'][0];
    		 
    	}else{
    		return null;
    	}
    	 
    	//return $payType;
    }
    
    function searchAccountType($act) {
    	 
    	$accountType = null;
    	 
    	if($act == 0){
    		$accountType = array(
    				'id' => '2a997b5d-b284-4ee2-9064-df0d7d508fee'         //Ahorro
    		);
    	}
    
    	 
    	if($act == 1){
    		$accountType = array(
    				'id' => 'dfe58d8c-57e0-4790-bc14-c02dd1f9ba2d'         //Corriente
    		);
    	}
    
    	 
    	 
    	return $accountType;
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