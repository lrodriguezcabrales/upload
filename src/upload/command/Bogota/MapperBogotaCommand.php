<?php
namespace upload\command\Bogota;

use upload\model\clientsCartagena;
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

class MapperBogotaCommand extends Command
{	
	
	public $server = 'http://162.242.218.66/bogotaServer/web/app.php/';
	public $serverRoot = 'http://162.242.218.66/';
	
	public $serverLocal = 'http://10.102.1.22/';
	
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
    
    protected function configure()
    {
        $this->setName('mapperBogota')
		             ->setDescription('Comando para mapear datos de SF1');
	}
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Datos de cliente SF1 \n");

        $conn = new data(array(
            'server' =>'10.102.1.3'
            ,'user' =>'hherrera'
            ,'pass' =>'daniela201'
            ,'database' =>'sifinca_bog' 
            ,'engine'=>'mssql'
        ));

        $cb = new clientsCartagena($conn);

        //$this->mapperIdentificaciones($cb);
        //$this->mapperPaises($cb);
        //$this->mapperCiudades($cb);
        //$this->mapperCiudadesNotFound($cb);
        //$this->mapperEstadosCiviles($cb);
        //$this->mapperNivelDeEstudio($cb);
        //$this->mapperTipoDeContribuyente($cb);
       
        //$this->banks($cb);
        
        
        //$this->mapperTipoInmueble($cb);
        //$this->mapperTipoInscripcion($cb);
        //$this->mapperCiudades($inmueblesCtg);
        //$this->mapperBarrios($inmueblesCtg);
        //$this->mapperDestinacion($inmueblesCtg);
        //$this->mapperEstratos($inmueblesCtg);
        //$this->mapperSucursales($inmueblesCtg);
        //$this->mapperClasificaciones($inmueblesCtg);
        //$this->mapperRazonesDeRetiro($inmueblesCtg);
        //$this->mapperCaracteristicas($inmueblesCtg);
        
        $this->mapperTiposServicio($inmueblesCtg);
        
    }
    
    function mapperIdentificaciones($cb) {
    	
    	$identificacionesSF1 = $cb->getIdentificacion();
    	
    	//print_r($identificacionesSF1);
    	
    	$urlapi = $this->server.'admin/sifinca/idtype';
    	    	
    	$api = $this->SetupApi($urlapi, $this->user, $this->pass);
    	    	
    	$identificacionesSF2 = $api->get();
    	//print_r($identificacionesSF2);
    	
    	$urlapi2 = $this->server.'admin/sifinca/mapper';
    	$api2 = $this->SetupApi($urlapi2, $this->user, $this->pass);
    	
    	$idsTypeMapper = array();
    	
   		$cedula = array(
   				'name' => 'idType',
   				'idSource' => '1',
   				'idTarget' => '6f80343e-f629-492a-80d1-a7e197c7cf48'
   		);
   		$idsTypeMapper[] = $cedula;
   		
   		$nit = array(
   				'name' => 'idType',
   				'idSource' => '2',
   				'idTarget' => '6c29bc74-a33a-42ed-8d24-1d86e31dce9f'
   		);
   		$idsTypeMapper[] = $nit;
   		
   		$pasaporte = array(
   				'name' => 'idType',
   				'idSource' => '3',
   				'idTarget' => '484cb0cb-29cd-4aa6-91c5-c246332112ff'
   		);
   		$idsTypeMapper[] = $pasaporte;
   		
   		$cedulaExtranjeria = array(
   				'name' => 'idType',
   				'idSource' => '4',
   				'idTarget' => 'd904096d-a740-44a5-a554-44e50bfbca00'
   		);
   		$idsTypeMapper[] = $cedulaExtranjeria;
   		
   		$nuip = array(
   				'name' => 'idType',
   				'idSource' => '5',
   				'idTarget' => '552311ae-169e-4822-aac7-4f15c162f4e7'
   		);
   		$idsTypeMapper[] = $nuip;
   		
   		$tarjetaIdentidad = array(
   				'name' => 'idType',
   				'idSource' => '6',
   				'idTarget' => '1513066b-6599-4f4d-acd9-c51012a9c121'
   		);
   		$idsTypeMapper[] = $tarjetaIdentidad;
   		
   		$idFiscalTributaria = array(
   				'name' => 'idType',
   				'idSource' => '7',
   				'idTarget' => '41e4a290-db12-484d-a7c3-74e466021a82'
   		);
   		$idsTypeMapper[] = $idFiscalTributaria;
   		
   		$tarjetaExtranjeria = array(
   				'name' => 'idType',
   				'idSource' => '8',
   				'idTarget' => '5eeb377b-93d5-4fa8-9217-6ac68871a9ca'
   		);
   		$idsTypeMapper[] = $tarjetaExtranjeria;
   		
   		
   		$total = 0;
		foreach ($idsTypeMapper as $value) {
			$r = $api2->post($value);
			//print_r($r);
			$total++;
		} 	
   		   		
		echo "Tipos de identificaciones SF1: ".count($identificacionesSF1)."\n";
		echo "Tipos de identificaciones mapeados: ".$total."\n";
		
    }

    /**
     * Guardar equivalencia de paises de SF1 con SF2
     * @param unknown $cb
     */
    function mapperPaises($cb) {
    	    	    	 
    	$paisesSF1 = $cb->getPaises();

    	$total = 0;
    	foreach ($paisesSF1 as $pais) {
    		//[{"value":"COLOMBIA","operator":" has","property":"name"}]
    		$pais = utf8_encode($pais);

    		$filter = array(
    			'value' => $pais,
    			'operator' => 'has',
    			'property' => 'name'
    		);
    		$filter = json_encode(array($filter));
    	
    		$urlapiConuntry = $this->server.'crm/main/country?filter='.$filter;

    		$apiCountry = $this->SetupApi($urlapiConuntry, $this->user, $this->pass);
    		
    		$countrySF2 = $apiCountry->get();
    		
    		$countrySF2 = json_decode($countrySF2, true);
    		
    		if(count($countrySF2['data']) > 0){
    			$mapper = array(
    					'name' => 'country.BOG',
    					'idSource' => $pais,
    					'idTarget' => $countrySF2['data'][0]['id'],
    					'label' => $pais
    			); 
    			
    			$urlapiMapper = $this->server.'admin/sifinca/mapper';
    			$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    			
    			$apiMapper->post($mapper);
    			$total++;
    		}
    		
    	}
    	
    	echo "Paises SF1: ".count($paisesSF1)."\n";
    	echo "Paises mapeados: ".$total."\n";
    	
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
    		
    		$ciudad = utf8_encode($ciudad);
    		    		
    		$filter = array(
    				'value' => $ciudad,
    				'operator' => 'start_with',
    				'property' => 'name'
    		);
    		$filter = json_encode(array($filter));
    		 
    		
    		$urlapiTown = $this->server.'crm/main/town?filter='.$filter;
    		//echo $urlapiTown."\n";
    		$apiTown = $this->SetupApi($urlapiTown, $this->user, $this->pass);
    		
    		$townSF2 = $apiTown->get();
    		
    		//echo $townSF2;
    		
    		//print_r($townSF2);
    		
    		$townSF2 = json_decode($townSF2, true);
    		
    		//print_r($townSF2);
    		
    		if($townSF2['total'] == 1){
    			$mapper = array(
    					'name' => 'town.BOG',
    					'idSource' => $ciudad,
    					'idTarget' => $townSF2['data'][0]['id'],
    					"label" => $ciudad
    			);
    		
    			$urlapiMapper = $this->server.'admin/sifinca/mapper';
    			$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		
    			$apiMapper->post($mapper);
    			$total++;
    		}else{
    			//echo "No hay coincidencia\n";
    			$sinCoincidencia[] = $ciudad;
    		}

    	}
    
    	//print_r($sinCoincidencia);
    	echo "Ciudades SF1: ".count($ciudadesSF1)."\n";
    	echo "Ciudades mapeados: ".$total."\n";	
    	echo "Ciudades no mapeados: ".count($sinCoincidencia)."\n";
    	//print_r($sinCoincidencia);
    	
    }
    
    function mapperCiudadesNotFound($cb) {
    	
    	$fileJson = file_get_contents($this->$serverRoot."upload/src/upload/data/mapperCiudades.json");
    	//echo $fileJson;
    	$data = json_decode($fileJson, true);
    	
    	//print_r($data);
    	$total = 0;
    	foreach ($data as $ciudad) {
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		 
    		$apiMapper->post($ciudad);
    		$total++;
    	}
    	echo "Ciudades mapeados: ".$total."\n";
    }

    function mapperEstadosCiviles($cb) {
    	
    	$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/mapperMaritalStatus.json");
    	//echo $fileJson;
    	$data = json_decode($fileJson, true);
    	 
    	//print_r($data);
    	$total = 0;
    	foreach ($data as $ciudad) {
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		 
    		$apiMapper->post($ciudad);
    		$total++;
    	}
    	echo "maritalStatus mapeados: ".$total."\n";
    	
    }

    function mapperNivelDeEstudio($cb) {
    	
    	$fileJson = file_get_contents($this->serverLocal."upload3/src/upload/data/mapperNivelDeEstudio.json");
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
    	echo "levelStudy mapeados: ".$total."\n";
    }
    
    function mapperTipoDeContribuyente($cb) {
    	
    	/*TIPO_CONTRIBUYENTE	GA	Gran Contribuyente - AutoRetenedor
	    	TIPO_CONTRIBUYENTE	RA	RŽgimen Comœn - Autoretenedor
	    	TIPO_CONTRIBUYENTE	GN	Gran Contribuyente - No Autoretenedor
	    	TIPO_CONTRIBUYENTE	RN	RŽgimen Comœn - No Autoretenedor
	    	TIPO_CONTRIBUYENTE	RS	RŽgimen Simplificado
	    	TIPO_CONTRIBUYENTE	CE	Cliente Extranjero
	    	TIPO_CONTRIBUYENTE	EX	Exento retenci—n (Sin animo de lucro)
    	*/
    	$fileJson = file_get_contents($this->serverLocal."upload3/src/upload/data/mapperContribuyente.json");
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
    	echo "tipos de contribuyentes mapeados: ".$total."\n";
    }
    
    function mapperTipoInmueble($inmueblesCtg) {
    
    	$fileJson = '[{"name":"propertyTypeCatchment.BOG","idSource":"0","idTarget":"a1edab94-0edc-44bd-a3be-35954e0af555"},{"name":"propertyTypeCatchment.BOG","idSource":"1","idTarget":"21b26da4-18ea-492f-954f-2562c2e074c7"},{"name":"propertyTypeCatchment.BOG","idSource":"2","idTarget":"f138ad09-5efc-4bae-9d77-9ee198b99af9"},{"name":"propertyTypeCatchment.BOG","idSource":"3","idTarget":"f49c642b-71f2-43c9-bc82-16e1d3b76673"},{"name":"propertyTypeCatchment.BOG","idSource":"4","idTarget":"5cca94f0-13e7-44c1-b027-ba63837d5906"},{"name":"propertyTypeCatchment.BOG","idSource":"5","idTarget":"493ef167-fb9f-49f7-b09f-9681b590d4c2"},{"name":"propertyTypeCatchment.BOG","idSource":"6","idTarget":"bf48d911-ea9a-4b5c-9e1b-d0da4ce2821b"},{"name":"propertyTypeCatchment.BOG","idSource":"7","idTarget":"79194b81-910f-45fa-8d54-faae188323fb"},{"name":"propertyTypeCatchment.BOG","idSource":"8","idTarget":"57439798-71a8-4786-9838-4e5b75b076d6"},{"name":"propertyTypeCatchment.BOG","idSource":"9","idTarget":"751a51c0-04fd-4b5b-b2ab-8b475c63b519"},{"name":"propertyTypeCatchment.BOG","idSource":"10","idTarget":"b1532926-45a7-4f50-9d29-8b54c50f2334"},{"name":"propertyTypeCatchment.BOG","idSource":"11","idTarget":"9476e82b-490f-4e82-b52a-14f921efeed6"},{"name":"propertyTypeCatchment.BOG","idSource":"12","idTarget":"bd1ef030-4769-47fc-9438-ca67aaea0fdf"},{"name":"propertyTypeCatchment.BOG","idSource":"13","idTarget":"064bc34b-3451-4881-b96d-ec5010d4e9ef"},{"name":"propertyTypeCatchment.BOG","idSource":"14","idTarget":"33e96b14-2dd4-4e8f-9ec1-a482bd81dc80"},{"name":"propertyTypeCatchment.BOG","idSource":"15","idTarget":"fbf0b8f6-face-4116-87a7-c3110b43d3bc"}]';
    	
    	$data = json_decode($fileJson, true);
    	//print_r($data);
    	$total = 0;
    	foreach ($data as $d) {
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		 
    		$apiMapper->post($d);
    		$total++;
    	}
    	 
    	echo "\nTipos de inmuebles mapeados: ".$total."\n";
    }
    
    function mapperTipoInscripcion($inmueblesCtg) {
    
    	$fileJson = '[{"name":"propertyInscriptionType","idSource":"1","idTarget":"bf49243c-b1d9-4e0a-9fb5-0ebe7e1d2d8d","label":"Arriendo"},{"name":"propertyInscriptionType","idSource":"2","idTarget":"ae5bb922-df1e-4043-b1c7-4e636a2d666a","label":"Venta"},{"name":"propertyInscriptionType","idSource":"3","idTarget":"19196300-d674-4c9f-b290-35534afcdf3d","label":"Arriendo/Venta"}]';
    	$data = json_decode($fileJson, true);
    
    	//print_r($data);
    	$total = 0;
    	foreach ($data as $d) {
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		 
    		$apiMapper->post($d);
    		$total++;
    	}
    
    	echo "\nTipos de incripcion mapeados: ".$total."\n";
    }
    
    /**
     * Guardar equivalencia de ciudades/municipios de SF1 con SF2
     * @param unknown $cb
     */
    function mapperCiudades2($cb) {
    
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
    					'name' => 'propertyTown.BOG',
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
    			'value' => '551fccf7-db4e-4992-a8a4-f4f9a9d24c7a', //BOGOTA
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
    				'name' => 'propertyDistrict.BOG',
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
    
    	$fileJson = '[{"name":"propertyDestiny.BOG","idSource":"1","idTarget":"f537d6c8-764d-47c8-82c1-38bb8c29e443"},{"name":"propertyDestiny.BOG","idSource":"2","idTarget":"be9b75a2-3d73-42e1-a553-998e40994f35"},{"name":"propertyDestiny.BOG","idSource":"3","idTarget":"15c724fe-c766-4dff-abc8-b9aaeb5c57f6"}]';
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
    
    	$fileJson = '[{"name":"stratum.BOG","idSource":"1","idTarget":"44b9370e-0ad9-4904-88b6-e7264180d4bf"},{"name":"stratum.BOG","idSource":"2","idTarget":"988831b9-723b-4396-ba80-4b4e46acf12f"},{"name":"stratum.BOG","idSource":"3","idTarget":"26a81fc4-8d36-4397-81c3-ffedb2c47ccb"},{"name":"stratum.BOG","idSource":"4","idTarget":"97c62c9b-9271-49ff-a110-70736b5f7a65"},{"name":"stratum.BOG","idSource":"5","idTarget":"acceb6be-aba4-4998-b0d4-02a786fbfa6f"},{"name":"stratum.BOG","idSource":"6","idTarget":"2c4fecfa-5eeb-4f6b-8d2c-ea6c4cd85bda"}]';
   	
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
    
    	$fileJson = '[{"name":"propertyOffice.BOG","idSource":"1","idTarget":"fad1de4e-f0f5-4e0c-9e90-e3c717d2ca63","label":"Chico"}]';
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
    
    	$fileJson = '[{"name":"propertyClassification","idSource":"A","idTarget":"037afd0f-938c-4d4c-96de-24f1b2d63baf"},{"name":"propertyClassification","idSource":"B","idTarget":"5b4e7ca3-5b1e-4822-878a-83dcc6f06979"},{"name":"propertyClassification","idSource":"C","idTarget":"cc725d6d-d56b-46ac-b1ac-8b5c7adf1eab"}]';
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
    	 
    	$fileJson = '[{"name":"retirementReason","idSource":"12","idTarget":"53bf0f08-b14c-466c-881b-959b20155f0f"},{"name":"retirementReason","idSource":"11","idTarget":"6ec33c86-3b84-4e37-aca8-74d8b5da1ad8"},{"name":"retirementReason","idSource":"10","idTarget":"86908d71-5f77-4b04-baf8-dc32194c6f5a"},{"name":"retirementReason","idSource":"9","idTarget":"abc8d559-31b2-489d-b4ed-0a957e6579f9"},{"name":"retirementReason","idSource":"8","idTarget":"7643f7e1-cb12-4b8f-ab4f-6e4fa3edce4d"},{"name":"retirementReason","idSource":"7","idTarget":"ace2d6d4-050a-4c4e-ac7d-b65e6a2d87f8"},{"name":"retirementReason","idSource":"6","idTarget":"623fe8d7-02e0-4f22-9318-a571a4132fb6"},{"name":"retirementReason","idSource":"5","idTarget":"4c672ee0-7ee3-4fa2-a020-b29a30222c46"},{"name":"retirementReason","idSource":"4","idTarget":"9bb15c41-aaa4-4920-9567-dc1e19679d91"},{"name":"retirementReason","idSource":"3","idTarget":"c25a2512-7bad-4ad8-b920-cabffa9b9b5f"},{"name":"retirementReason","idSource":"2","idTarget":"4fedf93d-f603-406a-b3a6-9121a1e284c6"},{"name":"retirementReason","idSource":"1","idTarget":"84aabc2e-ef48-4eb4-8070-302658ec1880"}]';
    	$data = json_decode($fileJson, true);
    	 
    	$total = 0;
    	foreach ($data as $d) {
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		 
    		$apiMapper->post($d);
    		$total++;
    	}
    	
    	echo "\nRazones de cierre mapeados: ".$total."\n";
    }

    
    function mapperCaracteristicas($inmueblesCtg) {
    
    	$fileJson = '[{"name":"propertyAttribute","idSource":"1","idTarget":"79cb1f8c-2842-4456-86fe-ea5d397650f8"},{"name":"propertyAttribute","idSource":"2","idTarget":"3ad1482a-2fe4-4c93-9fc3-7ca8640ba2fa"},{"name":"propertyAttribute","idSource":"3","idTarget":"9a3b47e6-7130-4ffb-82bf-85d91a7df004"},{"name":"propertyAttribute","idSource":"4","idTarget":"38237c6c-b8f3-410c-8ecc-a8aca09b0732"},{"name":"propertyAttribute","idSource":"5","idTarget":"eddf57bf-5f85-4c83-8e70-6750b1203533"},{"name":"propertyAttribute","idSource":"6","idTarget":"5c7aefca-063d-4664-a83a-aeb17294bd1c"},{"name":"propertyAttribute","idSource":"7","idTarget":"44190306-d590-4c52-926a-d8183b36ba52"},{"name":"propertyAttribute","idSource":"8","idTarget":"2b5d9fee-e8fb-4e1c-87e1-5d9174645a27"},{"name":"propertyAttribute","idSource":"9","idTarget":"c24d7f35-21cb-46a4-9d52-886cdfda115a"},{"name":"propertyAttribute","idSource":"10","idTarget":"826674ad-b83f-4568-ab3f-478ab2ae870c"},{"name":"propertyAttribute","idSource":"11","idTarget":"36c8ac25-d168-49a3-9219-d81422005132"},{"name":"propertyAttribute","idSource":"12","idTarget":"4d37970f-ee91-4b6e-a93f-ccd933d22157"},{"name":"propertyAttribute","idSource":"13","idTarget":"93b3a728-0798-45b6-a9b5-366c358f8a28"},{"name":"propertyAttribute","idSource":"14","idTarget":"7a3d3106-e2be-496c-82af-ae70b0f4bf32"},{"name":"propertyAttribute","idSource":"15","idTarget":"807894f7-77ca-448c-80a1-400761a6b0d2"},{"name":"propertyAttribute","idSource":"16","idTarget":"3b99b677-aa8d-4e2b-ba8b-574c7491e1ab"},{"name":"propertyAttribute","idSource":"17","idTarget":"732ac0e0-2bfd-4507-baa7-090fc161dbb3"},{"name":"propertyAttribute","idSource":"18","idTarget":"5b5afe92-8e45-491a-a4e4-afb9d542cafe"},{"name":"propertyAttribute","idSource":"19","idTarget":"aa533d39-9b02-4ca7-ade2-375169bb4834"},{"name":"propertyAttribute","idSource":"20","idTarget":"84645d39-d6a7-4646-9eb0-06d9fc3f2705"},{"name":"propertyAttribute","idSource":"21","idTarget":"f0a1680f-d4da-4b19-b3c9-00d5c19da077"},{"name":"propertyAttribute","idSource":"22","idTarget":"3e323b34-92fb-478f-8569-6f96f5862798"},{"name":"propertyAttribute","idSource":"23","idTarget":"6ad32d1c-7afd-440f-bc05-59176c3883f4"},{"name":"propertyAttribute","idSource":"24","idTarget":"f9405770-10d2-4eaa-97ce-999652121671"},{"name":"propertyAttribute","idSource":"25","idTarget":"f5942f23-dc9a-409a-85d9-eb6d8f5c4dcc"},{"name":"propertyAttribute","idSource":"26","idTarget":"8c7607b2-fd46-44e2-99d3-68b1aa58a0f0"},{"name":"propertyAttribute","idSource":"27","idTarget":"886b983b-d89a-48d8-8ffe-663055705d7c"},{"name":"propertyAttribute","idSource":"28","idTarget":"b6f8765f-b94a-4180-a8a8-d6ba5179f1b8"},{"name":"propertyAttribute","idSource":"35","idTarget":"f7d2bcf0-5960-4fb0-9222-0a486fcf6d2f"},{"name":"propertyAttribute","idSource":"40","idTarget":"70c69c02-0853-4bd6-b6a8-a3c1d3216ae3"},{"name":"propertyAttribute","idSource":"41","idTarget":"16799086-413b-4b17-977a-1aa8073be06a"},{"name":"propertyAttribute","idSource":"42","idTarget":"384077ba-ce67-4fd8-8331-dc9806430bdc"},{"name":"propertyAttribute","idSource":"50","idTarget":"5ca9b96c-c163-4e6e-b163-f1738cb51462"},{"name":"propertyAttribute","idSource":"51","idTarget":"ef254d6e-a286-4269-aff5-4f0c1e6f070a"},{"name":"propertyAttribute","idSource":"52","idTarget":"65fb3f49-0b11-4bb3-8ee3-e6c59b80428c"},{"name":"propertyAttribute","idSource":"53","idTarget":"289edb08-03b1-479e-b46b-d3de59d072c2"},{"name":"propertyAttribute","idSource":"54","idTarget":"28fd08a7-ba5f-467f-b054-de052e09c853"},{"name":"propertyAttribute","idSource":"55","idTarget":"1bdeb612-37db-450b-bf7b-10182e15cb28"},{"name":"propertyAttribute","idSource":"56","idTarget":"55090592-e903-45d0-bb60-46071693f46d"},{"name":"propertyAttribute","idSource":"57","idTarget":"c28b9d65-d8bb-46ca-86de-58ddf873c4e9"},{"name":"propertyAttribute","idSource":"58","idTarget":"32261efa-5c6f-4551-a46e-3f9c16b334e5"},{"name":"propertyAttribute","idSource":"59","idTarget":"85d3bdc6-d381-41b9-b21b-34f930d63b9a"},{"name":"propertyAttribute","idSource":"60","idTarget":"4702a366-f5c6-4ad4-abd7-851c6684db99"},{"name":"propertyAttribute","idSource":"61","idTarget":"ae97f865-937b-4a22-9069-d09a6485abc6"},{"name":"propertyAttribute","idSource":"62","idTarget":"7d6b87a6-cd10-4ffd-817c-f866d3984ba0"},{"name":"propertyAttribute","idSource":"63","idTarget":"93bce932-0477-43e0-8175-310495724fbe"},{"name":"propertyAttribute","idSource":"64","idTarget":"827cb0bc-f8b0-40dc-9113-d8c2019ec67e"},{"name":"propertyAttribute","idSource":"65","idTarget":"dfb781e9-647f-4c85-964a-fc8c0345c9b8"},{"name":"propertyAttribute","idSource":"66","idTarget":"c5f44c7a-a676-428a-b74d-3e2ade633ba3"},{"name":"propertyAttribute","idSource":"67","idTarget":"71850fc7-cf77-4536-b987-e2a3ee759c0a"},{"name":"propertyAttribute","idSource":"70","idTarget":"2fcd2c7d-9046-4419-b26b-d6cfd8dea6a5"},{"name":"propertyAttribute","idSource":"71","idTarget":"3178b1d3-b5a9-4a95-adcd-4ab45a1b478f"},{"name":"propertyAttribute","idSource":"72","idTarget":"ef7041e1-fbb0-4d6c-bb97-06ea013ebf49"},{"name":"propertyAttribute","idSource":"73","idTarget":"1ee1ae05-275c-4058-93b7-21b825cd711b"},{"name":"propertyAttribute","idSource":"74","idTarget":"1b4629d6-60f0-4aa8-830b-c3321933ba85"},{"name":"propertyAttribute","idSource":"75","idTarget":"a03509c9-0da4-4ceb-9395-d8ffb1c51b6a"},{"name":"propertyAttribute","idSource":"76","idTarget":"77184144-7052-4f87-bb0d-c88778ff3c52"},{"name":"propertyAttribute","idSource":"77","idTarget":"33653182-030d-43a9-ac29-a6541580366d"},{"name":"propertyAttribute","idSource":"78","idTarget":"c68f772b-ea85-44e1-a3d3-8c499bb7bc19"},{"name":"propertyAttribute","idSource":"79","idTarget":"c30f2b8d-058e-415d-8e39-90aaea256af2"},{"name":"propertyAttribute","idSource":"80","idTarget":"e342a919-1cb3-4637-8c0e-5dd13c6dd750"},{"name":"propertyAttribute","idSource":"81","idTarget":"981f0ec4-17f8-4eb8-b5f1-b1964969d2e3"},{"name":"propertyAttribute","idSource":"82","idTarget":"6215f62b-ff83-480a-96e4-9270cd0139b1"},{"name":"propertyAttribute","idSource":"83","idTarget":"a343d248-e4b5-4d50-8fd1-a38c09ca0bd3"},{"name":"propertyAttribute","idSource":"84","idTarget":"67df77cb-28db-416b-8c15-f9da2bf40cd5"},{"name":"propertyAttribute","idSource":"85","idTarget":"77e01d98-aa74-4cfa-be2a-4e2c80aa51b7"},{"name":"propertyAttribute","idSource":"86","idTarget":"3f68dc6a-3e4a-4c04-ac8f-45fe9d5c88dc"},{"name":"propertyAttribute","idSource":"87","idTarget":"3e0f128d-364b-4b06-b11d-cd2276db10cf"},{"name":"propertyAttribute","idSource":"88","idTarget":"fef90c02-63bd-42f6-acc5-1026b37e2c59"},{"name":"propertyAttribute","idSource":"89","idTarget":"0a6c2fbc-c436-4c55-b35c-1ee336622979"},{"name":"propertyAttribute","idSource":"90","idTarget":"f433b097-e5bf-4a8e-8e3c-b1db7b2298a6"},{"name":"propertyAttribute","idSource":"91","idTarget":"32d7a99f-c09d-4d37-a021-680985ff92e7"},{"name":"propertyAttribute","idSource":"92","idTarget":"3565c2e6-ec14-4dda-a29b-808d976bb319"},{"name":"propertyAttribute","idSource":"93","idTarget":"ecf04744-a4d4-4228-bedb-c83406508662"},{"name":"propertyAttribute","idSource":"94","idTarget":"6e985357-2bbc-4329-a12a-014eae50b02c"},{"name":"propertyAttribute","idSource":"95","idTarget":"0d940647-fa49-44f1-a832-ccee12041d5f"},{"name":"propertyAttribute","idSource":"96","idTarget":"4e715919-c910-4f89-b1a1-e01834cb5710"},{"name":"propertyAttribute","idSource":"97","idTarget":"c10d4f07-f8c2-47c4-b7af-dc28919286c2"},{"name":"propertyAttribute","idSource":"98","idTarget":"452830e4-5ce3-406e-872c-4926d882e0a4"},{"name":"propertyAttribute","idSource":"99","idTarget":"277c4e15-1b40-4ef6-a9db-939d79094eb1"},{"name":"propertyAttribute","idSource":"100","idTarget":"58abb72a-065d-4434-8d84-bb95e882af0e"},{"name":"propertyAttribute","idSource":"101","idTarget":"c11266ba-560b-4a2f-bb49-850b1d92caca"},{"name":"propertyAttribute","idSource":"102","idTarget":"a56f313f-d245-4705-8713-ecb73b4fe923"},{"name":"propertyAttribute","idSource":"104","idTarget":"dd0b3a77-b1d4-4623-8bbc-1a555a1dd160"},{"name":"propertyAttribute","idSource":"105","idTarget":"43ca43ac-abd3-41ee-b046-1a9580ca9b3e"},{"name":"propertyAttribute","idSource":"106","idTarget":"21893ac5-9bc7-413d-8102-39671686941a"},{"name":"propertyAttribute","idSource":"107","idTarget":"465ef5fe-6ab4-4cb2-acf7-b6fcadd69e2f"},{"name":"propertyAttribute","idSource":"108","idTarget":"be3ea2f6-05ab-4137-9819-750db4961aad"},{"name":"propertyAttribute","idSource":"109","idTarget":"25337b1c-f6fa-4978-9733-11728712bf72"},{"name":"propertyAttribute","idSource":"110","idTarget":"dd8e9975-f38b-4817-8091-1461455a04f4"},{"name":"propertyAttribute","idSource":"111","idTarget":"3e5e6997-e2b2-4242-8ea5-b0e087d3d5ba"},{"name":"propertyAttribute","idSource":"112","idTarget":"e6844503-ed2e-4e05-9f6c-4b67a24b2295"},{"name":"propertyAttribute","idSource":"114","idTarget":"978945e6-844a-4765-971d-bd1a53a339eb"},{"name":"propertyAttribute","idSource":"116","idTarget":"02d4b437-16ad-49ab-9858-f2aa64aedbd7"},{"name":"propertyAttribute","idSource":"117","idTarget":"42aee7b4-27f1-4253-9a51-3fbeaaf1f900"},{"name":"propertyAttribute","idSource":"118","idTarget":"c2fec77c-9d91-4cee-aab0-7a7a0fd84653"},{"name":"propertyAttribute","idSource":"119","idTarget":"79e8acb8-1ced-4531-9e2f-08f9b9059736"},{"name":"propertyAttribute","idSource":"120","idTarget":"6df804d1-9b53-4701-9a75-3b0afe447fb8"},{"name":"propertyAttribute","idSource":"121","idTarget":"9eb912a4-ffeb-40d9-8749-ad17fcacfee2"},{"name":"propertyAttribute","idSource":"123","idTarget":"c2f7b1ad-609e-4735-819c-44d247bb9617"},{"name":"propertyAttribute","idSource":"124","idTarget":"50c24264-f444-4665-986a-d035e62f5e3b"},{"name":"propertyAttribute","idSource":"125","idTarget":"b960773a-d4fe-4861-89cc-288f856467ea"},{"name":"propertyAttribute","idSource":"126","idTarget":"fde6faaf-d555-4a73-b30a-8ba0b5b1f88a"},{"name":"propertyAttribute","idSource":"127","idTarget":"a441b8fc-f531-4bd9-8464-be6b69b4deb9"},{"name":"propertyAttribute","idSource":"128","idTarget":"56163701-b781-4cae-8f64-1327d91c0a45"},{"name":"propertyAttribute","idSource":"129","idTarget":"5e4320ad-5bdb-4653-8c96-5566d127cef8"},{"name":"propertyAttribute","idSource":"130","idTarget":"87617a91-6451-472d-b24c-42ab9c40180b"},{"name":"propertyAttribute","idSource":"131","idTarget":"21c8eaf5-35db-44cb-9aed-cd9bea18f07f"},{"name":"propertyAttribute","idSource":"132","idTarget":"a256bd5e-5d3d-4729-bb26-4d1afdd302b2"},{"name":"propertyAttribute","idSource":"133","idTarget":"cf88c84a-8b74-46d5-ba34-ec8e786b2022"},{"name":"propertyAttribute","idSource":"134","idTarget":"eeddcc71-e656-4390-8428-0a7015de849b"},{"name":"propertyAttribute","idSource":"135","idTarget":"deff57be-81b3-41d1-91b8-24b2e7ea665c"},{"name":"propertyAttribute","idSource":"136","idTarget":"73d566d5-bf37-426c-8a8d-e6a5c7ba99ab"},{"name":"propertyAttribute","idSource":"137","idTarget":"da819f56-d3f0-4c07-ac94-3d999cecc9a0"},{"name":"propertyAttribute","idSource":"138","idTarget":"7985e43c-847d-480b-9f36-22fd7d0f7138"},{"name":"propertyAttribute","idSource":"139","idTarget":"14c6d87c-4279-4b8f-a0b4-019812874a3d"},{"name":"propertyAttribute","idSource":"140","idTarget":"d1b16cd4-5800-4098-89d6-a0fc1b05da86"},{"name":"propertyAttribute","idSource":"141","idTarget":"8f6f77f1-33ae-4467-8cff-fdb8688543aa"},{"name":"propertyAttribute","idSource":"142","idTarget":"7a98ddca-1ec7-4611-bfee-001e6a3aa2a7"},{"name":"propertyAttribute","idSource":"143","idTarget":"69e65c15-d829-4fd8-b9b6-df7d277e36e5"},{"name":"propertyAttribute","idSource":"144","idTarget":"480addae-6734-45f8-a30a-3785dc884b6f"},{"name":"propertyAttribute","idSource":"145","idTarget":"50f57fae-0d70-4f1e-9362-71459223e023"},{"name":"propertyAttribute","idSource":"146","idTarget":"d4474638-723e-42b2-b051-3fc7edf3a7c4"},{"name":"propertyAttribute","idSource":"147","idTarget":"6f870aa2-0566-4476-91aa-435131be4438"},{"name":"propertyAttribute","idSource":"148","idTarget":"0b3b99bb-72c8-467d-bc51-592420dc0c59"},{"name":"propertyAttribute","idSource":"149","idTarget":"e900d076-5e92-4257-a7d8-7ed16a8002a1"},{"name":"propertyAttribute","idSource":"150","idTarget":"127dd148-4e4f-4ed6-8b9b-47961016bf7e"},{"name":"propertyAttribute","idSource":"151","idTarget":"83eb1fb2-5f1c-45be-92eb-28c3c52f7d85"},{"name":"propertyAttribute","idSource":"152","idTarget":"b113888f-e4a8-44a6-ba84-7eb780a7eab7"},{"name":"propertyAttribute","idSource":"153","idTarget":"033c3746-f53e-4229-9357-58b23c2777af"},{"name":"propertyAttribute","idSource":"154","idTarget":"48affafc-d7df-4d6f-b434-e70463c030f1"},{"name":"propertyAttribute","idSource":"155","idTarget":"53747216-9363-4bd6-ad04-682cf385d888"},{"name":"propertyAttribute","idSource":"156","idTarget":"872e06f7-e400-4c68-af52-1c3fceec2062"},{"name":"propertyAttribute","idSource":"157","idTarget":"4f1aa680-58bf-4aa0-bdec-0b8d39ebc382"},{"name":"propertyAttribute","idSource":"158","idTarget":"b1305e4b-7517-4dfc-b792-a33c4e77d463"},{"name":"propertyAttribute","idSource":"159","idTarget":"8dbb5f08-091a-4714-8e38-417bca1f5fa2"},{"name":"propertyAttribute","idSource":"160","idTarget":"5a7ae949-ad0e-46b9-b054-ee5fcedeb881"},{"name":"propertyAttribute","idSource":"161","idTarget":"fa61c2de-0da9-463e-bf68-e5668db3f9d3"},{"name":"propertyAttribute","idSource":"162","idTarget":"ef7acd56-d3e8-4f10-8fed-97de72208462"},{"name":"propertyAttribute","idSource":"163","idTarget":"9a176a6c-8d6c-488a-ad90-a50c9d77e6c6"},{"name":"propertyAttribute","idSource":"164","idTarget":"1a066f49-3b9f-49c0-b9c9-a0bddb36b105"},{"name":"propertyAttribute","idSource":"165","idTarget":"4a73a262-d028-431e-a7c0-53a5234e7db9"},{"name":"propertyAttribute","idSource":"166","idTarget":"8bcf1375-d5f6-4b0e-9fe5-0f7f714dcd5c"},{"name":"propertyAttribute","idSource":"167","idTarget":"369418e7-3ebe-48b7-986d-f6c80c40a402"},{"name":"propertyAttribute","idSource":"168","idTarget":"ecc759b7-832b-4c8d-989d-2a389878892a"},{"name":"propertyAttribute","idSource":"169","idTarget":"56ee5056-7c4b-4685-a93a-dd7789e20cbf"},{"name":"propertyAttribute","idSource":"170","idTarget":"3daa9bc4-a775-4701-9700-082bdc1b197d"},{"name":"propertyAttribute","idSource":"171","idTarget":"17af8b76-4807-4a83-9bc1-b12d8a000a0a"},{"name":"propertyAttribute","idSource":"172","idTarget":"e0c0e973-f967-4ad3-91bf-62c441418079"},{"name":"propertyAttribute","idSource":"173","idTarget":"b5a1c03b-5b41-4899-971a-a28aeba83b02"},{"name":"propertyAttribute","idSource":"174","idTarget":"34f3a009-c6e8-421d-b9fb-094ae94dbc93"}]';
    	$data = json_decode($fileJson, true);
    
    	$total = 0;
    	foreach ($data as $d) {
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		 
    		$apiMapper->post($d);
    		$total++;
    	}
    	
    	echo "\nCaracteristicas mapeados: ".$total."\n";
    
    }
    
    function mapperTiposServicio($inmueblesCtg) {
    
    	$fileJson = '[{"name":"serviceType","idSource":"1","idTarget":"3be801fb-2913-48da-a48f-250b650ac9ce"},{"name":"serviceType","idSource":"2","idTarget":"dbe20aa6-0d7b-498c-8667-9b671b1bc52a"},{"name":"serviceType","idSource":"3","idTarget":"1bb920cc-303b-4ad6-89c6-f9d26d699d47"},{"name":"serviceType","idSource":"4","idTarget":"0a608cb5-e249-469d-8b4a-4061e14abd79"},{"name":"serviceType","idSource":"5","idTarget":"8054508d-faa2-4d1a-98e1-130b790528ea"}]';
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
    
    	echo "\nTipos de servicios mapeados: ".$total."\n";
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
    		
    		//echo "\n".$url."\n";
    		
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