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
	public $user= "sifinca@araujoysegovia.com";
	public $pass="araujo123";
		
	public $colombia = '8701307b-d8bd-49f1-8a91-5d0f7b8046b3';
	public $idTypeCedula = '6f80343e-f629-492a-80d1-a7e197c7cf48';
	
	public $contactTypeOther = 'ac76deff-6371-4264-b724-b24b98803b94';
	public  $typeAddressHome = '8b8b75ae-6338-461f-8bbd-fc1283621d83';
	
	
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
        $this->mapperRazonesDeCierre($inmueblesCtg);
        
//        	$inmuebles = $inmueblesCtg->getInmuebles();
// 	    $this->buildInmuebles($inmuebles);
      
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
    
    	$barriosSF1 = $cb->getBarrios();
    	 
    	$sinCoincidencia = array();
    	$total = 0;
    	
    	$totalBarriosSF1 = count($barriosSF1);
    	
    	for ($i = 200; $i < 300; $i++) {
    		
    		$filter = null;
    		
    		$filter[] = array(
    				'value' => utf8_encode($barriosSF1[$i]['nombre']),
    				'operator' => 'has',
    				'property' => 'name'
    		);
    		
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
    		
    		
    		$townSF2 = json_decode($townSF2, true);
    		
    		
    		if($townSF2['total'] == 1){
    			$mapper = array(
    					'name' => 'propertyDistrict.CTG',
    					'idSource' => $barriosSF1[$i]['id_barrio'],
    					'idTarget' => $townSF2['data'][0]['id']
    			);
    		
    			$urlapiMapper = $this->server.'admin/sifinca/mapper';
    			$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		
    			//print_r($mapper);
    		
    			$apiMapper->post($mapper);
    			$total = $total + 1;
    			
    		}else{
    			//echo "No hay coincidencia\n";
    			$sc = [
    				'id_barrio' => $barriosSF1[$i]['id_barrio'],
    				'nombre' => $barriosSF1[$i]['nombre']
    			];
    			
    			$sinCoincidencia[] = $sc;
    		}
    		
    		
    		
    	}
    	
    	echo "\n--------------\n";
    	//print_r($sinCoincidencia);
    	echo "JSON sin coincidencia";
    	echo json_encode($sinCoincidencia);
    	 
    	echo "Barrios SF1: ".count($barriosSF1)."\n";
    	echo "Barrios mapeados: ".$total."\n";
    	echo "Barrios no mapeados: ".count($sinCoincidencia)."\n";
    	
    	 
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
    
    function mapperRazonesDeCierre($inmueblesCtg) {
    	
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
				    "name" => "closingReason",
				    "idSource" => $cont,
				    "idTarget" => $result['data'][0]
    		);
    		
    		$apiMapper->post($m);
    		
    		$total++;
    		$cont++;
    	}
    	
    }
    
    
    function buildInmuebles($inmuebles) {
    	
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
    		
//     		if($inmueble['id_edificio']){
//     			$this->buildEdificio($inmueble);
//     		}
    		
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
    		
    		$urlStratum = $this->server.'admin/sifinca/mapper/stratum/'.$inmueble['estrato'];
    		//echo $urlStratum;
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
    		
    		$urlOffice = $this->server.'admin/sifinca/mapper/propertyOffice.CTG/'.$inmueble['estrato'];
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
    		
    		$urlClassification = $this->server.'admin/sifinca/mapper/propertyClassification/'.$inmueble['clasificacion'];
    		//echo $urlStratum;
    		$apiClassification = $this->SetupApi($urlClassification, $this->user, $this->pass);
    		 
    		$classificationMapper = $apiClassification->get();
    		$classificationMapper = json_decode($classificationMapper, true);
    		//print_r($stratumMapper);
    		$classification = array('id' => 'cfd4fe35-5ced-4df1-a361-37457244223b');
    		if($classificationMapper['total'] > 0){
    			$classification = $classificationMapper['data']['0']['idTarget'];
    			if(!is_null($classification)){
    		
    				$classification = array('id'=>$classification);
    		
    				if($classificationMapper['total'] == 0){
    					$classification = null;
    				}
    		
    			}
    		}
    		
    		$address = $this->buidDireccion($inmueble);
    		
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
    				"publicService" => null,
    				"propertyTypeCatchment" => $propertyType,
    				"inscriptionType" => $inscriptionType,
    				"destiny" => $destiny,
    				"office" => $office,
    				"stratum" => $stratum,
    				//"propertyCondition"  //Estado
    				"classificationOfProperty" => $classification,
    				//"propertyAttribute" 
    				"address" => $address,
    				"retirementDate" => $retirementDate
    		);
    		
    		
    		$json = json_encode($bInmueble);
    		echo "\n\n".$json."\n\n";
    		 
    		$result = $apiInmueble->post($bInmueble);
    		
    		$result = json_decode($result, true);
    		
    		
    		if($result['success'] == true){
    			echo "\nOk";
    			$total++;
    		}else{
    			echo "\nError\n";
    			print_r($result);
    		}
    		
    	}
    	
    	
    	echo "\n\nTotal inmuebles pasados ".$total."\n";
    }
    
    
    function buildEdificio($inmueble) {
    	
    	$bEdificio = array(
    		   			
    	);
    	
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
    
    
    protected function SetupApi($urlapi,$user,$pass){
    
    	$url= $this->server."login";
    	$headers = array(
    			'Accept: application/json',
    			'Content-Type: application/json',
    	);
    
    	$a = new api($url, $headers);
    
    	$result= $a->post(array("user"=>$user,"password"=>$pass));
    	 
    	//     	echo "aqui";
    	//     	print_r($result);
    
    	 
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