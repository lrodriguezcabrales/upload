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

class InmueblesCartagenaCommand extends Command
{	
	
	public $server = 'http://www.sifinca.net/sifinca/web/app.php/';
	public $serverRoot = 'http://www.sifinca.net/';
	
	public $localServer = 'http://10.102.1.22/';
	
	//public $server = 'http://10.102.1.22/sifinca/web/app.php/';
	//public $serverRoot = 'http:/10.102.1.22/';
	
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
	
	
    protected function configure()
    {
        $this->setName('inmueblesCartagena')
		             ->setDescription('Comando para obtener datos de inmuebles SF1');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Datos de inmuebles SF1 \n");

        $conn = new data(array(
            'server' =>'10.102.1.3'
            ,'user' =>'hherrera'
            ,'pass' =>'daniela201'
            ,'database' =>'sifinca' 
            ,'engine'=>'mssql'
        ));

        $inmueblesCtg = new inmueblesCartagena($conn);

        //$this->mapperTipoInmueble($inmueblesCtg);
        //$this->mapperTipoInscripcion($inmueblesCtg);
        //$this->mapperCiudades($inmueblesCtg);  
        //$this->mapperBarrios($inmueblesCtg);
        //$this->mapperDestinacion($inmueblesCtg);
        //$this->mapperEstratos($inmueblesCtg);
        //$this->mapperSucursales($inmueblesCtg);
        //$this->mapperClasificaciones($inmueblesCtg);
        //$this->mapperRazonesDeRetiro($inmueblesCtg);
        //$this->mapperCaracteristicas($inmueblesCtg);
        //$this->mapperEstadosInmueble($inmueblesCtg);
        //$this->mapperTiposServicio($inmueblesCtg);
        
        //$inmuebles = $inmueblesCtg->getInmuebles();
	    //$this->buildInmuebles($inmuebles, $inmueblesCtg);
              
        //$this->buildFotos($inmueblesCtg);

        //$this->subirInmueblesConError();

        //$this->mapperTipoEdificio($inmueblesCtg);
        $this->buildEdificios($inmueblesCtg);
        
    }
    
    function mapperTipoInmueble($inmueblesCtg) {
    	 
    	$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/mapperTipoInmueble.json");
    	//echo $fileJson;
    	$data = json_decode($fileJson, true);
    	 
    	//print_r($data);
    	$total = 0;
    	foreach ($data as $d) {
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		 
    		$apiMapper->post($d);
    		$total++;
    	}
    	
    	echo "tipos de inmuebles mapeados: ".$total."\n";
    }
    
    function mapperTipoInscripcion($inmueblesCtg) {
    
    	$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/mapperTipoInscripcion.json");
    	//echo $fileJson;
    	$data = json_decode($fileJson, true);
    
    	//print_r($data);
    	$total = 0;
    	foreach ($data as $d) {
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		 
    		$apiMapper->post($d);
    		$total++;
    	}
    	 
    	echo "tipos de incripcion mapeados: ".$total."\n";
    }
    
    /**
     * Guardar equivalencia de ciudades/municipios de SF1 con SF2
     * @param unknown $cb
     */
    function mapperCiudades($cb) {
        
    	$ciudadesSF1 = $cb->getCiudades();
    	
    	$sinCoincidencia = array();
    	$total = 0;
    	foreach ($ciudadesSF1 as $ciudad) {
    		
    		//$ciudad = utf8_encode($ciudad);
    		echo "\n".utf8_encode($ciudad['nombre'])."\n"; 		
    		$filter = array(
    				'value' => utf8_encode($ciudad['nombre']),
    				'operator' => 'start_with',
    				'property' => 'name'
    		);
    		
    		//print_r($filter);
    		$filter = json_encode(array($filter));
    		 
    		
    		$urlapiTownTT = $this->server.'crm/main/zero/town?filter='.$filter;
    	    echo "\n".$urlapiTownTT."\n";
    		$apiTownTT = $this->SetupApi($urlapiTownTT, $this->user, $this->pass);
    		//print_r($apiTownTT->get());
    		$townSF2 = $apiTownTT->get();
    		
    		//echo $townSF2;
    		
    		//print_r($townSF2);
    		//echo $townSF2['total'];
    		
    		$townSF2 = json_decode($townSF2, true);
    		
    		echo "Total: ".$townSF2['total'];
    		//print_r($townSF2);
    		
    		if($townSF2['total'] == 1){
    			$mapper = array(
    				'name' => 'propertyTown.CTG',
					'idSource' => $ciudad['id_ciudad'],
    				'idTarget' => $townSF2['data'][0]['id']
    			);
    		
    			$urlapiMapper = $this->server.'admin/sifinca/mapper';
    			$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		
    			print_r($mapper);
    			
    			$apiMapper->post($mapper);
    			$total++;
    		}else{
    			//echo "No hay coincidencia\n";
    			$sinCoincidencia[] = $ciudad;
    		}

    	}
    	echo "\n--------------\n";
    	print_r($sinCoincidencia);
    	echo "Ciudades SF1: ".count($ciudadesSF1)."\n";
    	echo "Ciudades mapeados: ".$total."\n";	
    	echo "Ciudades no mapeados: ".count($sinCoincidencia)."\n";
    	//print_r($sinCoincidencia);
    	
    	return ;
    }
    
    function mapperBarrios($cb) {
    
    		
    		$filter = null;
    		
    		$filter[] = array(
    				'value' => '994b009d-50a5-44dd-8060-878a10f4dd00',
    				'operator' => '=',
    				'property' => 'town.id'
    		);
    		
    		$filterDecode = json_encode($filter);
    		 
    		//echo "filtro decode ".utf8_encode($filterDecode);
    		
    		
    		
    		$urlapiTownTT = $this->server.'crm/main/zero/district?filter='.$filterDecode;
    		//echo "\n".$urlapiTownTT."\n";
    		$apiTownTT = $this->SetupApi($urlapiTownTT, $this->user, $this->pass);
    		//print_r($apiTownTT->get());
    		$townSF2 = $apiTownTT->get();
    		
    		
    		$barriosSF2 = json_decode($townSF2, true);
    		
    		foreach ($barriosSF2['data'] as $b) {
    			
    			$mapper = array(
    				'name' => 'propertyDistrict.CTG',
    				'idSource' => $b['code'],
    				'idTarget' => $b['id']
    			);
    		
    			$urlapiMapper = $this->server.'admin/sifinca/mapper';
    			$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		
    			//print_r($mapper);
    			$apiMapper->post($mapper);
    			$total = $total + 1;
    			echo "\nOk\n";
    		}
    		
    		
    	
    	echo "Barrios mapeados: ".$total."\n";
    	
    	 
    	return ;
    }
    
    function mapperDestinacion($inmueblesCtg) {
    
    	$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/mapperDestinacion.json");
    	//echo $fileJson;
    	$data = json_decode($fileJson, true);
    
    	//print_r($data);
    	$total = 0;
    	foreach ($data as $d) {
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		 
    		$apiMapper->post($d);
    		$total++;
    	}
    
    	echo "destinos mapeados: ".$total."\n";
    }
        
    function mapperEstratos($inmueblesCtg) {
    
    	$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/mapperEstratos.json");
    	//echo $fileJson;
    	$data = json_decode($fileJson, true);
    
    	//print_r($data);
    	$total = 0;
    	foreach ($data as $d) {
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		 
    		$apiMapper->post($d);
    		$total++;
    	}
    
    	echo "destinos mapeados: ".$total."\n";
    }
    
    function mapperSucursales($inmueblesCtg) {
    
    	$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/mapperSucursales.json");
    	//echo $fileJson;
    	$data = json_decode($fileJson, true);
    
    	//print_r($data);
    	$total = 0;
    	foreach ($data as $d) {
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		 
    		$apiMapper->post($d);
    		$total++;
    	}
    
    	echo "sucursales mapeados: ".$total."\n";
    }
    
    function mapperClasificaciones($inmueblesCtg) {
    
    	$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/mapperClasificacion.json");
    	//echo $fileJson;
    	$data = json_decode($fileJson, true);
    
    	//print_r($data);
    	$total = 0;
    	foreach ($data as $d) {
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		 
    		$apiMapper->post($d);
    		$total++;
    	}
    
    	echo "clasificaciones mapeados: ".$total."\n";
    }
    
    function mapperRazonesDeRetiro($inmueblesCtg) {
    	
    	$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/closingReason.json");
    	$data = json_decode($fileJson, true);
    	
    	$url = $this->server.'admin/sifinca/attributelist';
    	
    	$apiClosingReason = $this->SetupApi($url, $this->user, $this->pass);
    	    	
    	
    	$total = 0;
    	$cont = 1;
    	foreach ($data as $d) {
    		
    		$result = $apiClosingReason->post($d);
    		$result = json_decode($result, true);

    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		
    		$m = array(
				    "name" => "retirementReason",
				    "idSource" => $cont,
				    "idTarget" => $result['data'][0]
    		);
    		
    		$apiMapper->post($m);
    		
    		$total++;
    		$cont++;
    	}
    	
    }
    
    function mapperEstadosInmueble($inmueblesCtg) {
    
    	$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/mapperEstadosInmueble.json");
    	//echo $fileJson;
    	$data = json_decode($fileJson, true);
    
    	//print_r($data);
    	$total = 0;
    	foreach ($data as $d) {
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		 
    		$apiMapper->post($d);
    		$total++;
    	}
    
    	echo "estados mapeados: ".$total."\n";
    }
    
    function mapperCaracteristicas($inmueblesCtg) {
    	 
    	$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/caracteristicasInmueble.json");
    	$data = json_decode($fileJson, true);
    	 
    	$url = $this->server.'admin/sifinca/attributelist';
    	 
    	$apiClosingReason = $this->SetupApi($url, $this->user, $this->pass);
    	 
    	$total = 0;
    	$cont = 167;
    	//foreach ($data as $d) {
    	for ($i = 0; $i < 8; $i++) {
    		
    		$d = $data[$i];
    		
    		$result = $apiClosingReason->post($d);
    		$result = json_decode($result, true);
    
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    
    		$m = array(
    				"name" => "propertyAttribute",
    				"idSource" => $cont,
    				"idTarget" => $result['data'][0]
    		);
    
    		$apiMapper->post($m);
    
    		$total++;
    		$cont++;
    	}
    	 
    }
    
    function mapperTiposServicio($inmueblesCtg) {
    
    	$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/mapperTiposServicio.json");
    	//echo $fileJson;
    	$data = json_decode($fileJson, true);
    	//print_r($data);
    	$total = 0;
    	foreach ($data as $d) {
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		 
    		$apiMapper->post($d);
    		$total++;
    	}
    
    	echo "tipos de servicios mapeados: ".$total."\n";
    }
    

    function mapperTipoEdificio($inmueblesCtg) {
    
    	$fileJson = file_get_contents($this->localServer."upload/src/upload/data/mapperTipoEdificio.json");
    	//echo $fileJson;
    	$data = json_decode($fileJson, true);
    	//print_r($data);
    	$total = 0;
    	foreach ($data as $d) {
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		 
    		$apiMapper->post($d);
    		$total++;
    	}
    
    	echo "tipos de edificios mapeados: ".$total."\n";
    }
    
    

    function buildInmuebles($inmuebles, $inmueblesCtg) {
    	
    	$urlapiInmueble = $this->server.'catchment/main/property';
    	 
    	$apiInmueble = $this->SetupApi($urlapiInmueble, $this->user, $this->pass);
    	
    	$officeCentro = array(
    		'id' => 'bee55884-15dc-406f-94f7-547288ebe20d'
    	);
    	
    	$total = 0;
    	
    	//$totalInmueblesSF1 = count($inmuebles);
    	$totalInmueblesSF1 = 1;
    	    	
    	
    	for ($i = 0; $i < $totalInmueblesSF1; $i++) {
    		
    		$inmueble = $inmuebles[$i];
    		//foreach ($inmuebles as $inmueble) {
    		
    		$edificio = null;
    		
    		if($inmueble['id_edificio']){
    			$this->buildEdificio($inmueble);
    		}
    		
    		$urlPropertyType = $this->server.'admin/sifinca/mapper/propertyTypeCatchment/'.$inmueble['id_tipo_inmueble'];
    		//echo "\n".$urlPropertyType."\n";
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
    		
    		
    		if($inmueble['id_destinacion'] == '0'){
    			$destiny = array('id'=>'169c9685-b58a-47e7-badc-20627583ca13');
    		}else{
    			$urlDestiny = $this->server.'admin/sifinca/mapper/propertyDestiny/'.$inmueble['id_destinacion'];
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
    		
    		
    		if(!empty($inmueble['estrato'])){
    			$urlStratum = $this->server.'admin/sifinca/mapper/stratum/'.$inmueble['estrato'];
    			//echo "\n".$urlStratum;
    			$apiStratum = $this->SetupApi($urlStratum, $this->user, $this->pass);
    			 
    			$stratumMapper = $apiStratum->get();
    			$stratumMapper = json_decode($stratumMapper, true);
    			//print_r($stratumMapper);
    			$stratum = null;
    			if($stratumMapper['total'] > 0){
    				$stratum = $stratumMapper['data']['0']['idTarget'];
    				if(!is_null($stratum)){
    			
    					$stratum = array('id'=>$stratum);
    			
    					if($stratumMapper['total'] == 0){
    						$stratum = null;
    					}
    			
    				}
    			}
    		}else{
    			$stratum = null;
    		}
    	
    		
    		$urlOffice = $this->server.'admin/sifinca/mapper/propertyOffice.CTG/'.$inmueble['id_sucursal'];
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
    		
    		
    		if(!empty($inmueble['id_retiro'])){
    		
	    		$urlRetirementReason = $this->server.'admin/sifinca/mapper/retirementReason/'.$inmueble['id_retiro'];
	    		//echo $urlStratum;
	    		$apiRetirementReason = $this->SetupApi($urlRetirementReason, $this->user, $this->pass);
	    		 
	    		$retirementReasonMapper = $apiRetirementReason->get();
	    		$retirementReasonMapper = json_decode($retirementReasonMapper, true);
// 	    		echo "\n--------------------\n";
// 	    		print_r($retirementReasonMapper);
// 	    		echo "\n--------------------\n";
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
    		
    		$urlStatus = $this->server.'admin/sifinca/mapper/propertyStatus/'.$inmueble['estado_operativo'];
    		//echo $urlStratum;
    		$apiStatus = $this->SetupApi($urlStatus, $this->user, $this->pass);
    		 
    		$statusMapper = $apiStatus->get();
    		$statusMapper = json_decode($statusMapper, true);
    		//print_r($stratumMapper);
    		$propertyStatus = null;
    		if($statusMapper['total'] > 0){
    			$propertyStatus = $statusMapper['data']['0']['idTarget'];
    			if(!is_null($propertyStatus)){
    		
    				$propertyStatus = array('id'=>$propertyStatus);
    		
    				if($statusMapper['total'] == 0){
    					$propertyStatus = null;
    				}
    		
    			}
    		}
    		if($inmueble['estado_operativo'] == '0'){
    			$propertyStatus = array('id'=>'49147d17-fc80-4eb1-ad34-90622938138e');
    		}
    		
    		
    		$address = $this->buidDireccion($inmueble);
    		
    		//Caracteristicas del inmueble
    		$caracteristicas = $this->buildCaracteristicasInmueble($inmueble, $inmueblesCtg);
    		
    		//Servicios publicos del inmueble
    		$servicios = $this->buildServicios($inmueble, $inmueblesCtg);
    		
    		$consignmentdate = new \DateTime($inmueble['fecha_consignacion']);
    		$consignmentdate = $consignmentdate->format('Y-m-d');
    		
    		$dateAvailable = new \DateTime($inmueble['fecha_disponible']);
    		$dateAvailable = $dateAvailable->format('Y-m-d');
    		
    		$retirementDate= new \DateTime($inmueble['fecha_retiro']);
    		$retirementDate = $retirementDate->format('Y-m-d');
    		
    		$bInmueble = array(
    				"consecutive" => $inmueble['id_inmueble'],
    				"cadastralReference" => $inmueble['referencia_catastral'],
    				"folioRegistration" => $inmueble['folio_matricula'],
    				"priceSale" => $inmueble['valor_venta'],
    				"priceLease"=> $inmueble['canon'],
    				"priceAdministration" => $inmueble['admin'],
    				"includeAdministration" => $inmueble['adm_incl'],
    				"exclusive" => $inmueble['exclusivo'],
    				"constructArea" => $inmueble['area_construida'],
    				"totalArea"=> $inmueble['area_lote'],
    				"outstanding" => $inmueble['destacado'],
    				"boundaries" => $inmueble['linderos'],
    				//"published": "false",
    				"numberKeys" => $inmueble['Llaves'],
    				"locationKeys"=> $inmueble['ubillave'],
    				"keyName" => $inmueble['NoLLaves'],
    				"furnished" => $inmueble['amoblado'],
    				"propertyDescription" => $inmueble['texto_inmu'],
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
    				"propertyStatus" => $propertyStatus,
    				"classificationOfProperty" => $classification,
    				"propertyAttribute" => $caracteristicas, 
    				"address" => $address,
    				"retirementDate" => $retirementDate,
    				"retirementReason" => $retirementReason,
    		);
    		
    		
    		$json = json_encode($bInmueble);
    		//echo "\n\n".$json."\n\n";
    		 
    		$result = $apiInmueble->post($bInmueble);
    		
    		$result = json_decode($result, true);
    		
    		
    		if($result['success'] == true){
    			echo "\nOk";
    			$total++;
    			
    			$idInmuebleSF2 = $result['data'][0];
    			
    			
    			if($inmueble['estado_operativo'] == '0'){
    				$this->buildPublicacion($idInmuebleSF2, $inmueble);
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
    		
    	}
    	
    	
    	echo "\n\nTotal inmuebles pasados ".$total."\n";
    }
    
    function buildEdificios($inmueblesCtg) {
    	
    	$edificiosSF1 = $inmueblesCtg->getEdificios();
    	
    	$urlEdificio = $this->server.'catchment/main/building';
    	$apiEdificio = $this->SetupApi($urlEdificio, $this->user, $this->pass);
    	
    	$total = 0;
    	
    	for ($i = 0; $i < 1; $i++) {
    		$edificio = $edificiosSF1[$i];
    		
    		
    		$urlBuildingType = $this->server.'admin/sifinca/mapper/buildingType/'.$edificio['id_tipo_edif'];
    		//echo "\n".$urlPropertyType."\n";
    		$apiBuildingType = $this->SetupApi($urlBuildingType, $this->user, $this->pass);
    		 
    		$buildingTypeMapper = $apiBuildingType->get();
    		$buildingTypeMapper = json_decode($buildingTypeMapper, true);
    		//print_r($propertyTypeMapper);
    		$buildingType = null;
    		if($buildingTypeMapper['total'] > 0){
    			$propertyType = $buildingTypeMapper['data']['0']['idTarget'];
    			if(!is_null($buildingType)){
    		
    				$buildingType = array('id'=>$buildingType);
    		
    				if($buildingTypeMapper['total'] == 0){
    					$buildingType = null;
    				}
    		
    			}
    		}
    		
    		//print_r($edificio);
    		
    		//return ;
    		
    		$direccion = array(
    				'address' => $edificio['direccion'],
    				'country' => array('id' => $this->colombia),
    				'department' => array('id' => $this->bolivar),
    				'town' => array('id' => $this->cartagena),
    				'district' => null,
    				'typeAddress' => array('id'=>$this->typeAddressCorrespondencia), //Correspondecia
    		);
    		 
    		$contacto = array(
    				'name' => $edificio['encargado'],
    				'phone' => $edificio['telefono'],
    				'position' => 'N/A',
    				'contactType' => array('id'=> $this->contactTypeOther) //Otro
    		);
    		 
    		 
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
    				'accountNumber' => $clienteSF1['no_cuenta'],
    				'beneficiary' => $beneficiary
    		);
    		
    		$idEdificio = $edificio['id_edificio'];
    		$idEdificio = trim($idEdificio);
    		
    		$bEdificio = array(
    				'idSifincaOne' => $idEdificio,
    				'name' => $edificio['Nombre'],
    				'buildingType' => $buildingType,
    				'address' => $direccion,
    				//'description' => $edificio[''],
    				'contact' => $contacto,
    				'payForm' => $payForm
    		);
    		
    		
    		$json = json_encode($bEdificio);
    	
    		
    		
    		$result = $apiEdificio->post($bEdificio);
    		
    		$result = json_decode($result, true);
    		
    		
    		
    		if(isset($result['success'])){
    			if($result['success'] == true){
    				echo "\nOk\n";
    				$total++;
    			}
    		}else{
    			echo "\nError\n";
    				
    			print_r($result);
    				
    			
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
    	    	
    	$urlTown = $this->server.'admin/sifinca/mapper/propertyTown.CTG/'.$inmueble['id_ciudad'];
    	$apiTown= $this->SetupApi($urlTown, $this->user, $this->pass);
    	 
    	$townMapper = $apiTown->get();
    	$townMapper = json_decode($townMapper, true);
    	//print_r($maritalStatusMapper);
    	$town = null;
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
    				$town = null;
    			}
    	
    		}
    	}
    	
    	$urlDistrict = $this->server.'admin/sifinca/mapper/propertyDistrict.CTG/'.$inmueble['id_barrio'];
    	//echo "\n".$urlDistrict."\n";
    	$apiDistrict= $this->SetupApi($urlDistrict, $this->user, $this->pass);
    	
    	$districtMapper = $apiDistrict->get();
    	$districtMapper = json_decode($districtMapper, true);
    	
    	//print_r($districtMapper);
    	
    	$district = null;
    	if($districtMapper['total'] > 0){
    		$district = $districtMapper['data']['0']['idTarget'];
    		if(!is_null($district)){
    			 
    			$district = array('id'=>$district);
    			 
    			if($districtMapper['total'] == 0){
    				$district = null;
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
    
    function buildPublicacion($idInmuebleSF2, $inmuebleSF1) {
    	
    	$urlPublication = $this->server.'catchment/main/publication';
    	$apiMapper = $this->SetupApi($urlPublication, $this->user, $this->pass);
    	
    	$datePublication = new \DateTime($inmuebleSF1['fecha_consignacion']);
    	//$datePublication = new \DateTime($inmuebleSF1['consignmentdate']);
    	$datePublication = $datePublication->format('Y-m-d');
    	
    	
//     	if(!empty($inmuebleSF1['dateAvailable'])){
//     		//echo "\nentro";
//     		if(!is_null($inmuebleSF1['dateAvailable'])){
//     			$datePublication = new \DateTime($inmuebleSF1['dateAvailable']);
//     			$datePublication = $datePublication->format('Y-m-d');
//     		}
//     	}
    	
    	if(!empty($inmuebleSF1['fecha_promocion'])){
    		//echo "\nentro";
    		if(!is_null($inmuebleSF1['fecha_promocion'])){
    			$datePublication = new \DateTime($inmuebleSF1['fecha_promocion']);
    			$datePublication = $datePublication->format('Y-m-d');
    		}
    	}
    	
    	//echo "\n".$datePublication."\n";
    	
    	
    	$bpublicacion = array(
    			'active' => true,
    			'datePublication' => $datePublication,
    			'property' => array('id' => $idInmuebleSF2)
    	);
    	 
    	
    	$apiMapper->post($bpublicacion);
    	
    }
    
	function buildFotos($inmueblesCtg) {
		
		
// 		$urlInmueblesSF2 = $this->server.'admin/sifinca/mapper/serviceType/'.$s['id_tipo_servicio'];
// 		//echo $urlStratum;
// 		$apiServiceType = $this->SetupApi($urlServiceType, $this->user, $this->pass);
		
// 		$serviceTypeMapper = $apiServiceType->get();
// 		$serviceTypeMapper = json_decode($serviceTypeMapper, true);
		
		
		$inmueble = array(
			'id_inmueble' => '5'
		);
		
		$fotos = $inmueblesCtg->getFotosDeInmueble($inmueble);
		
		$urlFotoSF1 = 'http://10.102.1.3:81/publiweb/foto.php?key=';
		
				
		$urlapiFile = $this->server."archive/main/file";
		
		foreach ($fotos as $foto) {
			
			$path = "/var/www/html/upload/fotosinmueble/".$foto['id']."/";
			
						
			if (!file_exists($path)) {
				//Crearlo
				mkdir($path);
			}
			
			///print_r($foto);
			$nkey = $foto['nkey'];
			$url = $urlFotoSF1.$nkey;
			
			echo "url";
			echo "\n".$url."\n";
						
			
			$pathFilename= $path.$foto['nkey'].".".$foto['ext'];
			
			echo "path";
			echo "\n".$pathFilename."\n";
			
			file_put_contents($pathFilename, file_get_contents($url));
						
// 			$ch = curl_init();
// 			$timeout = 300;
// 			curl_setopt($ch, CURLOPT_URL, $url);
// 			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
// 			$data = curl_exec($ch);
// 			curl_close($ch);

			
// 			set_time_limit(0);
// 			$fp = fopen ($pathFilename, 'w+');//This is the file where we save the    information
// 			$ch = curl_init(str_replace(" ","%20",$url));//Here is the file we are downloading, replace spaces with %20
// 			curl_setopt($ch, CURLOPT_TIMEOUT, 50);
// 			curl_setopt($ch, CURLOPT_FILE, $fp); // write curl response to file
// 			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// 			curl_exec($ch); // get curl response
// 			curl_close($ch);
// 			fclose($fp);
			
			
// 			echo "\ndata\n";
// 			echo $data;
			
// 			$fp = fopen($pathFilename,"wb");
//             fwrite($fp,$data);
//             fclose($fp);
            
            //
			
		}
		
	}    

    function subirInmueblesConError() {
    	
    	$urlInmueblesSF2 = $this->server.'catchment/main/errorproperty';
    	
    	$apiInmueblesSF2 = $this->SetupApi($urlInmueblesSF2, $this->user, $this->pass);
    	    	 
    	$inmueblesSF2 = $apiInmueblesSF2->get();
    	$inmueblesSF2 = json_decode($inmueblesSF2, true);
    	
    	$total = 0;
    	
    	$totalInmueblesLog = $inmueblesSF2['total'];
    	
    	//$totalInmueblesLog = 1;
    	
    	for ($i = 0; $i < $totalInmueblesLog; $i++) {
    		
    		$logInmueble = $inmueblesSF2['data'][$i];
    		
    		
    		$urlapiInmueble = $this->server.'catchment/main/property';
    		
    		$apiInmueble = $this->SetupApi($urlapiInmueble, $this->user, $this->pass);

    		
    		//echo $logInmueble['objectJson'];
    		
    		$inm = json_decode($logInmueble['objectJson'], true);
    		
    		$result = $apiInmueble->post($inm);
    		
    		$result = json_decode($result, true);
    		
    		
    		if($result['success'] == true){
    			echo "\nOk";
    			$total++;
    			 
    			$idInmuebleSF2 = $result['data'][0];
    			 
    			//print_r($inm);
    			 
    			if($inm['propertyStatus']['id'] == '49147d17-fc80-4eb1-ad34-90622938138e'){
    				$this->buildPublicacion($idInmuebleSF2, $inm);
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
    		
    	}
    	
    	echo "\n\nTotal inmuebles pasados ".$total."\n";
    	
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
    	
    	echo "\n".$urlBank."\n";
    	
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

    protected function SetupApi($urlapi,$user,$pass){
    
    	$url= $this->server."login";
    	$headers = array(
    			'Accept: application/json',
    			'Content-Type: application/json',
    	);
    
    	$a = new api($url, $headers);
    
    	$result= $a->post(array("user"=>$user,"password"=>$pass));

    	     	//echo "aqui";
    	     	//print_r($result);

    
    	 
    	$data=json_decode($result);
    
    
    	$token = $data->id;
    
    	$headers = array(
    			'Accept: application/json',
    			'Content-Type: application/json',
    			'x-sifinca:SessionToken SessionID="'.$token.'", Username="'.$user.'"'  ,
    	);
    
    	$a->set(array('url'=>$urlapi,'headers'=>$headers));
    
    	return $a;
    
    }
    
}