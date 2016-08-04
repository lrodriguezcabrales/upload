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
        $this->mapperEstadosInmueble($inmueblesCtg);
        //$this->mapperCaracteristicas($inmueblesCtg);
        
        //$this->mapperTiposServicio($inmueblesCtg);
        
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