<?php
namespace upload\command\Monteria;

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

class InmueblesMonteriaCommand extends Command
{	
 	public $server = 'http://www.sifinca.net/monteriaServer/web/app.php/';
 	public $serverRoot = 'http://www.sifinca.net/';
	
	public $localServer = 'http://10.102.1.22/';
	
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;	
	
	public $colombia = '8701307b-d8bd-49f1-8a91-5d0f7b8046b3';
	
	public $idTypeCedula = '6f80343e-f629-492a-80d1-a7e197c7cf48';
	
	public $contactTypeOther = 'ac76deff-6371-4264-b724-b24b98803b94';
	
	public  $typeAddressHome = '8b8b75ae-6338-461f-8bbd-fc1283621d83';
	public  $typeAddressCorrespondencia = 'e8e9cc62-ec79-4453-8247-a57cc1cf4712';

	
	public  $attributeAlcobas = 'b4b7ea95-ea41-4546-95e0-82b56a947411';
	public  $attributeBanos = 'bb0666a4-e3bf-45eb-8760-d6602a868ed8';
	
	public $buildingTypeEdificio = '345bc0a2-4880-4f13-b06f-80cd7405805c';
	
	public $office = 'a989afd0-f14f-4e20-9c75-5e084070129c'; //Oficina Chico 

	public $bogota = 'e296f778-e194-495a-a07d-98a6cbba974a';
	public $cundinamarca = 'cc036e72-6ee8-4513-a81a-c6b3c9724af7';
	
    protected function configure()
    {
        $this->setName('inmueblesMonteria')
		             ->setDescription('Comando para obtener datos de inmuebles SF1 - Bogota');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Datos de inmuebles SF1 \n");

        $conn = new data(array(
            'server' =>'192.168.100.1'
            ,'user' =>'sa'
            ,'pass' =>'75080508360'
            ,'database' =>'sifinca' 
            ,'engine'=>'mssql'
        ));

        $conexion = new inmueblesCartagena($conn);
        
        $inmuebles = $conexion->getInmueblesBogota();
	    $this->buildInmuebles($inmuebles, $conexion);
	   
        
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
    
    	if($property['total'] > 0){
    		 
    		return true;
    		 
    	}else{
    		return false;
    	}
    
    }
     
    
    
    function buildInmuebles($inmuebles, $conexion) {
    	
    	$urlapiInmueble = $this->server.'catchment/main/property';
    	 
    	$apiInmueble = $this->SetupApi($urlapiInmueble, $this->user, $this->pass);
    	
    	$officeCentro = array(
    		'id' => $this->office
    	);
    	
    	$total = 0;
    	
    	//$totalInmueblesSF1 = count($inmuebles);
    	$totalInmueblesSF1 = count($inmuebles);
    	
    	$startTime= new \DateTime();
    	
    	$porDonde = 0;
    	
    	for ($i = 0; $i < $totalInmueblesSF1; $i++) {
    		
    		$inmueble = $inmuebles[$i];
    		    		    		    		
    		$existe = $this->searchProperty($inmueble['id_inmueble']);
    		
    		if(!$existe){
    			
    			$edificio = $this->searchEdificio($inmueble);
    			
    			$propertyType = $this->searchPropertyType($inmueble);
    			
    			$inscriptionType = $this->searchInscriptionType($inmueble);
    			
    			$destiny = $this->searchDestiny($inmueble);
    			
    			$stratum = $this->searchStratum($inmueble);
    			
    			$office = $this->searchOffice($inmueble);
    			
    			$classification = $this->searchClassification($inmueble);
    			
    			$retirementReason = $this->searchRetirementReason($inmueble);
    			
    			$propertyStatus = $this->searchStatus($inmueble);
    			
    			$address = $this->buidDireccion($inmueble);
    			
    			//Caracteristicas del inmueble
    			$caracteristicas = $this->buildCaracteristicasInmueble($inmueble, $conexion);
    			
    			//Servicios publicos del inmueble
    			$servicios = $this->buildServicios($inmueble, $conexion);
    			
    			$consignmentdate = new \DateTime($inmueble['fecha_consignacion']);
    			$consignmentdate = $consignmentdate->format('Y-m-d');
    			
    			$dateAvailable = new \DateTime($inmueble['fecha_disponible']);
    			$dateAvailable = $dateAvailable->format('Y-m-d');
    			
    			$retirementDate= new \DateTime($inmueble['fecha_retiro']);
    			$retirementDate = $retirementDate->format('Y-m-d');
    			
    			$bInmueble = array(
    					"consecutive" => $inmueble['id_inmueble'],
    					"cadastralReference" => $this->cleanString($inmueble['referencia_catastral']),
    					"folioRegistration" => $this->cleanString($inmueble['folio_matricula']),
    					"priceSale" => $inmueble['valor_venta'],
    					"priceLease"=> $inmueble['canon'],
    					"priceAdministration" => $inmueble['admin'],
    					"includeAdministration" => $inmueble['adm_incl'],
    					"exclusive" => $inmueble['exclusivo'],
    					"constructArea" => $inmueble['area_construida'],
    					"totalArea"=> $inmueble['area_lote'],
    					"outstanding" => $inmueble['destacado'],
    					"boundaries" => $this->cleanString($inmueble['linderosAll']),
    					//"published": "false",
    					"numberKeys" => $inmueble['Llaves'],
    					"locationKeys"=> $inmueble['ubillave'],
    					"keyName" => $inmueble['NoLLaves'],
    					"furnished" => $inmueble['amoblado'],
    					"propertyDescription" => $this->cleanString($inmueble['descripcionAll']),
    					"consignmentdate" => $consignmentdate,
    					"dateAvailable" => $dateAvailable,
    					"dateUpdated" => $dateAvailable,
    					"building" => $edificio,
    					"publicService" => $servicios,
    					"propertyTypeCatchment" => $propertyType,
    					"inscriptionType" => $inscriptionType,
    					"destiny" => $destiny,
    					"office" => $office,
    					"stratum" => $stratum,
    					"propertyStatus" => $propertyStatus, //Estado del inmueble
    					"classificationOfProperty" => $classification,
    					"propertyAttribute" => $caracteristicas,
    					"address" => $address,
    					"retirementDate" => $retirementDate,
    					"retirementReason" => $retirementReason,
    			);
    		
    			$json = json_encode($bInmueble);

                 
                $result = $apiInmueble->post($bInmueble);
                
                $result = json_decode($result, true);
                
                
                if(isset($result['success'])){
                	
                	if($result['success'] == true){
                
	                    echo "\nOk";
	                    $total++;
	                    
	                    $idInmuebleSF2 = $result['data'][0];
                	}             
                    
                }else{
                    echo "\nError\n";
                    //print_r($result);
                    
                    $urlapiMapper = $this->server.'catchment/main/errorproperty';
                    $apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);

                    $error = array(
                        'property' => $inmueble['id_inmueble'],
                        'objectJson' => $json
                    );
                    
                    $apiMapper->post($error);
                    
                }
                
              


    		}else{

                echo "\nInmueble - ".$inmueble['id_inmueble']." ya existe\n";

            }
    		
            $porDonde++;
            echo "\n\nVamos por: ".$porDonde."\n";
    		
    	}
    	
    	
    	$finalTime = new \DateTime();
    	 
    	 
    	$diff = $startTime->diff($finalTime);
    	 
    	 
    	echo "\n\n Fecha inicial: ".$startTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Fecha final: ".$finalTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
    	 
    	
    	echo "\n\nTotal inmuebles pasados ".$total."\n";
    }
    
    /**
     * Buscar edificio a que pertene el inmueble
     */
    function searchEdificio($inmueble) {
    	 

    	if($inmueble['id_edificio']){
    		
    		$filter = array(
    				'value' => $this->cleanString($inmueble['id_edificio']),
    				'operator' => 'equal',
    				'property' => 'idSifincaOne'
    		);
    		$filter = json_encode(array($filter));
    
    		$urlBuildingSF2 = $this->server.'catchment/main/building?filter='.$filter;
    
    		$apiBuildingSF2 = $this->SetupApi($urlBuildingSF2, $this->user, $this->pass);
    		 
    		$buildingSF2 = $apiBuildingSF2->get();
    		 
    		$buildingSF2 = json_decode($buildingSF2, true);
    		 
    		if($buildingSF2['total'] > 0){
    			$edificio = array(
    					'id'=>$buildingSF2['data'][0]['id'],
    					'name' => $buildingSF2['data'][0]['name']
    			);
        			 
    			return $edificio;
    			
    		}else{
    			
    			$this->buildEdificios();
    			
    		}
    
    	}else{
    		return null;
    	}
    	 
    	 
    
    }
    
    public function searchPropertyType($inmueble) {
    	 
    	$urlPropertyType = $this->server.'admin/sifinca/mapper/propertyTypeCatchmentMON/'.$inmueble['id_tipo_inmueble'];
    	//echo "\n".$urlPropertyType."\n";
    	if($inmueble['id_tipo_inmueble'] == 0){
    		$propertyType = array('id'=>'a1edab94-0edc-44bd-a3be-35954e0af555'); //Edificio
    	}else{
    		$apiPropertyType = $this->SetupApi($urlPropertyType, $this->user, $this->pass);
    		
    		$propertyTypeMapper = $apiPropertyType->get();
    		$propertyTypeMapper = json_decode($propertyTypeMapper, true);
    		//print_r($propertyTypeMapper);
    		$propertyType = null;
    		if($propertyTypeMapper['total'] > 0){
    			$propertyType = $propertyTypeMapper['data']['0']['idTarget'];
    			if(!is_null($propertyType)){
    				 
    				$propertyType = array('id'=>$propertyType);
    				 
    				if($propertyTypeMapper['total'] == 0){
    					$propertyType = null;
    				}
    				 
    			}
    		}
    	}
    	 
    	return $propertyType;
    }
    
    public function searchInscriptionType($inmueble) {
    	 
    	$urlInscriptionType = $this->server.'admin/sifinca/mapper/propertyInscriptionType/'.$inmueble['tipo_inscripcion'];
    	$apiInscriptionType = $this->SetupApi($urlInscriptionType, $this->user, $this->pass);
    
    	$inscriptionTypeMapper = $apiInscriptionType->get();
    	$inscriptionTypeMapper = json_decode($inscriptionTypeMapper, true);
    	//print_r($inscriptionTypeMapper);
    	$inscriptionType = null;
    	if($inscriptionTypeMapper['total'] > 0){
    		$inscriptionType = $inscriptionTypeMapper['data']['0']['idTarget'];
    		if(!is_null($inscriptionType)){
    			 
    			$inscriptionType = array('id'=>$inscriptionType);
    			 
    			if($inscriptionTypeMapper['total'] == 0){
    				$inscriptionType = null;
    			}
    			 
    		}
    	}
    	 
    	return $inscriptionType;
    	 
    }
    
    public function searchDestiny($inmueble) {
    	 
    	if($inmueble['id_destinacion'] == '0'){
    		$destiny = array('id'=>'169c9685-b58a-47e7-badc-20627583ca13');
    	}else{
    		$urlDestiny = $this->server.'admin/sifinca/mapper/propertyDestiny.MON/'.$inmueble['id_destinacion'];
    		$apiDestiny = $this->SetupApi($urlDestiny, $this->user, $this->pass);
    		 
    		$destinyMapper = $apiDestiny->get();
    		$destinyMapper = json_decode($destinyMapper, true);
    		//print_r($maritalStatusMapper);
    		$destiny = null;
    		if($destinyMapper['total'] > 0){
    			$destiny = $destinyMapper['data']['0']['idTarget'];
    			if(!is_null($destiny)){
    				 
    				$destiny = array('id'=>$destiny);
    				 
    				if($destinyMapper['total'] == 0){
    					$destiny = null;
    				}
    				 
    			}
    		}
    	}
    	 
    	return $destiny;
    }
    
    public function searchStratum($inmueble) {
    	 
    	if(!empty($inmueble['estrato'])){
    		$urlStratum = $this->server.'admin/sifinca/mapper/stratum/'.$inmueble['estrato'];
    		//echo "\n".$urlStratum;
    		$apiStratum = $this->SetupApi($urlStratum, $this->user, $this->pass);
    		 
    		$stratumMapper = $apiStratum->get();
    		$stratumMapper = json_decode($stratumMapper, true);
    		//print_r($stratumMapper);
    		$stratum = null;
    
    		if(isset($stratumMapper['total'])){
    			if($stratumMapper['total'] > 0){
    				$stratum = $stratumMapper['data']['0']['idTarget'];
    				if(!is_null($stratum)){
    
    					$stratum = array('id'=>$stratum);
    
    					if($stratumMapper['total'] == 0){
    						$stratum = null;
    					}
    
    				}
    			}
    		}
    
    	}else{
    		$stratum = null;
    	}
    	 
    	return $stratum;
    }
    
    public function searchOffice($inmueble) {
    	 
    	$officeCentro = array(
    			'id' => $this->office
    	);
    	 
    	$urlOffice = $this->server.'admin/sifinca/mapper/propertyOffice.MON/'.$inmueble['id_sucursal'];
    	//echo $urlStratum;
    	$apiOffice = $this->SetupApi($urlOffice, $this->user, $this->pass);
    
    	$officeMapper = $apiOffice->get();
    	$officeMapper = json_decode($officeMapper, true);
    	//print_r($stratumMapper);
    	$office = null;
    	if($officeMapper['total'] > 0){
    		$office = $officeMapper['data']['0']['idTarget'];
    		if(!is_null($office)){
    			 
    			$office = array('id'=>$office);
    			 
    			if($officeMapper['total'] == 0){
    				$office = null;
    			}
    			 
    		}
    	}
    	 
    	if(is_null($office)){
    		$office = $officeCentro;
    	}
    	 
    	return $office;
    }
    
    function searchClassification($inmueble) {
    	 
    	if(!empty($inmueble['clasificacion'])){
    		 
    		$urlClassification = $this->server.'admin/sifinca/mapper/propertyClassification/'.$inmueble['clasificacion'];
    		//echo "\n".$urlClassification;
    		$apiClassification = $this->SetupApi($urlClassification, $this->user, $this->pass);
    		 
    		$classificationMapper = $apiClassification->get();
    		$classificationMapper = json_decode($classificationMapper, true);
    		//print_r($classificationMapper);
    		$classification = array('id' => 'd39ffd77-d68f-4ab0-8de4-0ae345c27b08'); // N/A
    		if($classificationMapper['total'] > 0){
    			$classification = $classificationMapper['data']['0']['idTarget'];
    			if(!is_null($classification)){
    				 
    				$classification = array('id'=>$classification);
    				 
    				if($classificationMapper['total'] == 0){
    					$classification = null;
    				}
    				 
    			}
    		}
    	}else{
    		$classification = array('id' => 'd39ffd77-d68f-4ab0-8de4-0ae345c27b08'); // N/A
    	}
    	 
    	return $classification;
    }
    
    function searchRetirementReason($inmueble) {
    	 
    	if(!empty($inmueble['id_retiro'])){
    		 
    		$urlRetirementReason = $this->server.'admin/sifinca/mapper/retirementReason/'.$inmueble['id_retiro'];
    		//echo $urlStratum;
    		$apiRetirementReason = $this->SetupApi($urlRetirementReason, $this->user, $this->pass);
    		 
    		$retirementReasonMapper = $apiRetirementReason->get();
    		$retirementReasonMapper = json_decode($retirementReasonMapper, true);
    		//              echo "\n--------------------\n";
    		//              print_r($retirementReasonMapper);
    		//              echo "\n--------------------\n";
    		$retirementReason = null;
    		 
    		if($retirementReasonMapper['total'] > 0){
    			$retirementReason = $retirementReasonMapper['data']['0']['idTarget'];
    			if(!is_null($retirementReason)){
    				 
    				$retirementReason = array('id'=>$retirementReason);
    				 
    				if($retirementReasonMapper['total'] == 0){
    					$retirementReason = null;
    				}
    				 
    			}
    		}
    	}else{
    		$retirementReason = null;
    	}
    	 
    	return $retirementReason;
    }
    
    function searchStatus($inmueble) {
    	 
    	//Retirado
    	if($inmueble['promocion'] == '0'){
    		$propertyStatus = array('id'=>'e3a87433-0439-47a8-a59f-62e511eb87d7');
    	}
    	 
    	if($inmueble['promocion'] == '1'){
    		$propertyStatus = array('id'=>'49147d17-fc80-4eb1-ad34-90622938138e');
    	}
    	 
    	return $propertyStatus;
    }
    
    
    function buildEdificios($inmueblesCtg) {
    	
    	$edificiosSF1 = $inmueblesCtg->getEdificios();
    	
    	$urlEdificio = $this->server.'catchment/main/building';
    	$apiEdificio = $this->SetupApi($urlEdificio, $this->user, $this->pass);
    	
    	$totalEdificios = count($edificiosSF1);
    	
    	$total = 0;
    	
    	for ($i = 0; $i < 1148; $i++) {
    		$edificio = $edificiosSF1[$i];
    		
    		
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
    				'department' => array('id' => $this->bogota),
    				'town' => array('id' => $this->cundinamarca),
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
     		$clienteSF1 = $this->searchClienteSF1($inmueblesCtg, $edificio['ID_ADMIN']);
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
    		 
    		
    	}
    	
    	echo "\n\nTotal edificios pasados ".$total."\n";
    	
	}
    

	function buidDireccion($inmueble){
		 
		$deparment = null;
	
		$urlTown = $this->server.'admin/sifinca/mapper/propertyTown.MON/'.$inmueble['id_ciudad'];
		$apiTown= $this->SetupApi($urlTown, $this->user, $this->pass);
	
		$townMapper = $apiTown->get();
		$townMapper = json_decode($townMapper, true);
		//print_r($maritalStatusMapper);
		$town = array('id'=>$this->bogota);
		$deparment = array('id' => $this->cundinamarca);
		 
		if(isset($townMapper['total'])){
			if($townMapper['total'] > 0){
				$town = $townMapper['data']['0']['idTarget'];
				if(!is_null($town)){
	
					$town = array('id'=>$town);
	
					$urlTownSF2 = $this->server.'crm/main/town/'.$town['id'];
					//echo "\n".$urlTownSF2."\n";
					$apiTownSF2 = $this->SetupApi($urlTownSF2, $this->user, $this->pass);
	
					$townSF2 = $apiTownSF2->get();
					$townSF2 = json_decode($townSF2, true);
	
					$deparment = $townSF2['department'];
	
					//print_r($deparment);
	
	
					if($townMapper['total'] == 0){
						$town = array('id'=>$this->bogota);
						$deparment = array('id' => $this->cundinamarca);
					}
	
				}
			}
		}
		 
		$urlDistrict = $this->server.'admin/sifinca/mapper/propertyDistrict.MON/'.$inmueble['id_barrio'];
		//echo "\n".$urlDistrict."\n";
		$apiDistrict= $this->SetupApi($urlDistrict, $this->user, $this->pass);
		 
		$districtMapper = $apiDistrict->get();
		$districtMapper = json_decode($districtMapper, true);
		 
		//print_r($districtMapper);
		 
		$district = null;
		if(isset($districtMapper['total'])){
			if($districtMapper['total'] > 0){
				$district = $districtMapper['data']['0']['idTarget'];
				if(!is_null($district)){
	
					$district = array('id'=>$district);
	
					if($districtMapper['total'] == 0){
						$district = null;
					}
	
				}
			}
		}
		 
		 
		 
	
		$bAddress = array(
				'address' => $inmueble['direccion'],
				'country' => array('id' => $this->colombia),
				'department' => $deparment,
				'town' => $town,
				'district' => $district,
				'typeAddress' => array('id'=>$this->typeAddressHome), //CASA
				'latitude' => $inmueble['latitud'],
				'longitude' => $inmueble['longitud']
		);
	
	
		return $bAddress;
	}
	 
    
    function buildCaracteristicasInmueble($inmueble, $inmueblesCtg) {
    	 
    	$caracteristicasSF1 = $inmueblesCtg->getCaracteristasDeInmueble($inmueble);
    	
    	$caracteristicas = null;
    	
    	if($inmueble['alcobas'] > 0){
    		$bcarateristica = array(
    				"amount" => $inmueble['alcobas'],
    				"name" => 'Alcobas',
    				"propertyAttribute" => array(
    						'id' => $this->attributeAlcobas
    				)
    		);
    		$caracteristicas[] = $bcarateristica;
    	}
    	 
    	//Baños
    	if($inmueble['43'] > 0){
    		$bcarateristica = array(
    				"amount" => $inmueble['43'],
    				'name' => 'Banos',
    				"propertyAttribute" => array(
    						'id' => $this->attributeBanos
    				)
    		);
    		$caracteristicas[] = $bcarateristica;
    	}
    	
    	foreach ($caracteristicasSF1 as $c) {
    		
    		$urlPropertyAttribute = $this->server.'admin/sifinca/mapper/propertyAttribute/'.$c['id_caracteristica'];
    		//echo $urlStratum;
    		$apiPropertyAttribute = $this->SetupApi($urlPropertyAttribute, $this->user, $this->pass);
    		 
    		$propertyAttributeMapper = $apiPropertyAttribute->get();
    		$propertyAttributeMapper = json_decode($propertyAttributeMapper, true);
    		//print_r($stratumMapper);
    		$propertyAttribute = null;
    		
    		if($propertyAttributeMapper['total'] > 0){
    			$propertyAttribute = $propertyAttributeMapper['data']['0']['idTarget'];
    			if(!is_null($propertyAttribute)){
    		
    				$propertyAttribute = array('id'=>$propertyAttribute);
    		
    				if($propertyAttributeMapper['total'] == 0){
    					$propertyAttribute = null;
    				}
    		
    			}
    		}
    		
    		$bcarateristica = array(
    			"amount" => "1",
    			"propertyAttribute" => $propertyAttribute
    		);
    		
    		$caracteristicas[] = $bcarateristica;
    		
    	}
    	
    	return $caracteristicas;
    }    
    
    function buildServicios($inmueble, $inmueblesCtg) {
    	
    	$serviciosSF1 = $inmueblesCtg->getServiciosDeInmueble($inmueble);
    	 
    	$servicios = null;
    	
    	foreach ($serviciosSF1 as $s) {
    		
    		$urlServiceType = $this->server.'admin/sifinca/mapper/serviceType/'.$s['id_tipo_servicio'];
    		//echo $urlStratum;
    		$apiServiceType = $this->SetupApi($urlServiceType, $this->user, $this->pass);
    		
    		$serviceTypeMapper = $apiServiceType->get();
    		$serviceTypeMapper = json_decode($serviceTypeMapper, true);
    		//print_r($stratumMapper);
    		$serviceType = null;
    		 
    		if($serviceTypeMapper['total'] > 0){
    			$serviceType = $serviceTypeMapper['data']['0']['idTarget'];
    			if(!is_null($serviceType)){
    				 
    				$serviceType = array('id'=>$serviceType);
    				 
    				if($serviceTypeMapper['total'] == 0){
    					$serviceType = null;
    				}
    				 
    			}
    		}
    		
    		
    		
    		if($s['compartido'] == 0){
    			$compartido = false;
    		}else{
    			$compartido = true;
    		}
    		
    		$bservicio = array(
    			'serviceType' => $serviceType,
    			'shared' => $compartido,
    			'subscriptionNumber' => $s['Suscripcion']
    		);
    		
    		$servicios[] = $bservicio;
    		
    		
    	}
    	
    	if(count($servicios) == 0){
    		
    		$agua = array(
    			   'serviceType' => array('id'=>'3be801fb-2913-48da-a48f-250b650ac9ce'),
    			   'shared' => 'false',
    			   'subscriptionNumber' => 0
    		);
    		
    		$energia = array(
    				'serviceType' => array('id'=>'dbe20aa6-0d7b-498c-8667-9b671b1bc52a'),
    				'shared' => 'false',
    				'subscriptionNumber' => 0
    		);
    		
    		$gas = array(
    				'serviceType' => array('id'=>'1bb920cc-303b-4ad6-89c6-f9d26d699d47'),
    				'shared' => 'false',
    				'subscriptionNumber' => 0
    		);
    		
    		$telefono = array(
    				'serviceType' => array('id'=>'0a608cb5-e249-469d-8b4a-4061e14abd79'),
    				'shared' => 'false',
    				'subscriptionNumber' => 0
    		);
    		
    		$cable = array(
    				'serviceType' => array('id'=>'8054508d-faa2-4d1a-98e1-130b790528ea'),
    				'shared' => 'false',
    				'subscriptionNumber' => 0
    		);
    		
    		$servicios[] = $agua;
    		$servicios[] = $energia;
    		$servicios[] = $gas;
    		$servicios[] = $telefono;
    		$servicios[] = $cable;
    	}
    	
    	return $servicios;
    	
    	
    }

    function searchClientSF2($identificacion){
    	    	
//     	echo "\nidentificacion\n";
//     	echo $identificacion."\n";
    	
    	$identificacion = trim($identificacion);
    	
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
//      	echo "\n".$urlClientSF2."\n";
    	 
    	//$urlClientSF2 = $this->server.'crm/main/zero/client';
    	
    	$apiClientSF2 = $this->SetupApi($urlClientSF2, $this->user, $this->pass);
    	
    	 
    	$clientSF2 = $apiClientSF2->get();
//      	echo "\nbeneficiario\n";
//  		echo $clientSF2;
		
    	$clientSF2 = json_decode($clientSF2, true);
    	
    	//echo $clientSF2['total'];
    	
    	if($clientSF2['total'] > 0){
    		
    		return $clientSF2['data'][0];
    		
    	}else{
    		return null;
    	}

    	
    }
     
    function searchClienteSF1($inmueblesCtg, $id){
    	
    	$clienteSF1 = $inmueblesCtg->getCliente($id);
    	
//     	echo "\nCliente\n";
//     	print_r($clienteSF1);
    	
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