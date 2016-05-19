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

class InmueblesMonteriaCommand extends Command
{	
	
	public $server = 'http://www.sifinca.net/monteriaServer/web/app.php/';

	public $serverRoot = 'http:/www.sifinca.net/';
	
// 	public $server = 'http://104.239.170.71/monteriaServer/web/app.php/';
	
// 	public $serverRoot = 'http:/104.239.170.71/';
	
	public $localServer = 'http://10.102.1.22/';
	
// 	public $server = 'http://10.102.1.22/sifinca/web/app.php/';
// 	public $serverRoot = 'http:/10.102.1.22/';

	//public $server = 'http://162.242.245.207/sifinca/web/app_dev.php/';
	//public $serverRoot = 'http:/162.242.245.207/';
	
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
	
    public $officeCentro = '439975ab-e7f2-4569-b694-8383ee2cd74b';

    protected function configure()
    {
        $this->setName('inmueblesMonteria')
		             ->setDescription('Comando para obtener datos de inmuebles SF1');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Datos de inmuebles SF1 MONTERIA\n");

        $conn = new data(array(
            'server' =>'192.168.100.1'
            ,'user' =>'sa'
            ,'pass' =>'75080508360'
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
        
         $inmuebles = $inmueblesCtg->getInmueblesMonteria();
  	     $this->buildInmuebles($inmuebles, $inmueblesCtg);
	   
       // $this->updateInmuebles($inmuebles, $inmueblesCtg);
              
        //$this->buildFotos($inmueblesCtg);

        //$this->subirInmueblesConError();

        //$this->mapperTipoEdificio($inmueblesCtg);
        //$this->buildEdificios($inmueblesCtg);
        
    }
    
    function mapperTipoInmueble($inmueblesCtg) {
    	 
    	//$fileJson = file_get_contents($this->serverRoot."upload3/src/upload/data/mapperTipoInmuebleMonteria.json");
    	//echo $fileJson;
    	
    	$fileJson = '[
			  {
			    "name": "propertyTypeCatchmentMON",
			    "idSource": "0",
			    "idTarget": "a1edab94-0edc-44bd-a3be-35954e0af555",
			    "label": "edificio"
			  },
			  {
			    "name": "propertyTypeCatchmentMON",
			    "idSource": "1",
			    "idTarget": "21b26da4-18ea-492f-954f-2562c2e074c7",
			    "label": "apartamento"
			  },
			  {
			    "name": "propertyTypeCatchmentMON",
			    "idSource": "2",
			    "idTarget": "f138ad09-5efc-4bae-9d77-9ee198b99af9",
			    "label": "casa"
			  },
			  {
			    "name": "propertyTypeCatchmentMON",
			    "idSource": "3",
			    "idTarget": "f49c642b-71f2-43c9-bc82-16e1d3b76673",
			    "label": "local"
			  },
			  {
			    "name": "propertyTypeCatchmentMON",
			    "idSource": "4",
			    "idTarget": "5cca94f0-13e7-44c1-b027-ba63837d5906",
			    "label": "bodega"
			  },
			  {
			    "name": "propertyTypeCatchmentMON",
			    "idSource": "5",
			    "idTarget": "493ef167-fb9f-49f7-b09f-9681b590d4c2",
			    "label": "oficina"
			  },
			  {
			    "name": "propertyTypeCatchmentMON",
			    "idSource": "6",
			    "idTarget": "bf48d911-ea9a-4b5c-9e1b-d0da4ce2821b",
			    "label": "consultorio"
			  },
			  {
			    "name": "propertyTypeCatchmentMON",
			    "idSource": "7",
			    "idTarget": "79194b81-910f-45fa-8d54-faae188323fb",
			    "label": "lote"
			  },
			  {
			    "name": "propertyTypeCatchmentMON",
			    "idSource": "8",
			    "idTarget": "5cca94f0-13e7-44c1-b027-ba63837d5906",
			    "label": "bodega"
			  },
			  {
			    "name": "propertyTypeCatchmentMON",
			    "idSource": "9",
			    "idTarget": "57439798-71a8-4786-9838-4e5b75b076d6",
			    "label": "parqueadero"
			  },
			  {
			    "name": "propertyTypeCatchmentMON",
			    "idSource": "10",
			    "idTarget": "751a51c0-04fd-4b5b-b2ab-8b475c63b519",
			    "label": "finca"
			  }
			]';
    	
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
    
    	//$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/mapperTipoInscripcion.json");
    	//echo $fileJson;
    	
    	$fileJson = '[
		  {
		    "name": "propertyInscriptionType",
		    "idSource": "0",
		    "idTarget": "a04bafb4-ce94-466a-90c3-6b055c777739",
		    "label": "avaluo"
		  },
		  {
		    "name": "propertyInscriptionType",
		    "idSource": "1",
		    "idTarget": "bf49243c-b1d9-4e0a-9fb5-0ebe7e1d2d8d",
		    "label": "arriendo"
		  },
		  {
		    "name": "propertyInscriptionType",
		    "idSource": "2",
		    "idTarget": "ae5bb922-df1e-4043-b1c7-4e636a2d666a",
		    "label": "venta"
		  },
		  {
		    "name": "propertyInscriptionType",
		    "idSource": "3",
		    "idTarget": "19196300-d674-4c9f-b290-35534afcdf3d",
		    "label": "arriendoventa"
		  }
		]';
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
        
    	$fileJson = '[
  {
    "name": "propertyTown.MON",
    "idSource": "201",
    "idTarget": "1fc9c785-fc80-4b01-bc83-fa743ead222f",
    "label": "SAHAGUN"
  },
  {
    "name": "propertyTown.MON",
    "idSource": "CER",
    "idTarget": "02194845-d370-494a-9aed-738590fa85ca",
    "label": "CERETE"
  },
  {
    "name": "propertyTown.MON",
    "idSource": "CIE",
    "idTarget": "fc5d1361-b24a-4f41-b2d6-e6df6f2ec94a",
    "label": "CIENAGA DE ORO"
  },
  {
    "name": "propertyTown.MON",
    "idSource": "COV",
    "idTarget": "9f34790e-09bf-499a-8ab6-a518d1a16d6f",
    "label": "COVE„AS"
  },
  {
    "name": "propertyTown.MON",
    "idSource": "LOR",
    "idTarget": "7090c41e-86ec-4032-ac1f-8baf52db104d",
    "label": "LORICA"
  },
  {
    "name": "propertyTown.MON",
    "idSource": "MON",
    "idTarget": "cc036e72-6ee8-4513-a81a-c6b3c9724af7",
    "label": "MONTERIA"
  },
  {
    "name": "propertyTown.MON",
    "idSource": "NEC",
    "idTarget": "cd193b6b-9545-4afb-9124-9d864a54d3f4",
    "label": "NECOCLI"
  },
  {
    "name": "propertyTown.MON",
    "idSource": "PLA",
    "idTarget": "6c6ce851-3147-44ed-8d1e-40b938fd4d5b",
    "label": "PLANETA RICA"
  },
  {
    "name": "propertyTown.MON",
    "idSource": "SAT",
    "idTarget": "bc2c52d6-31c1-4281-a6b8-539e4e482590",
    "label": "SAN ANTERO"
  },
  {
    "name": "propertyTown.MON",
    "idSource": "TOL",
    "idTarget": "ecf33f44-2b07-46b0-a5b4-226ccf91af81",
    "label": "SANTIAGO DE TOLU"
  }
]';
    	$data = json_decode($fileJson, true);
    	
    	//print_r($data);
    	$total = 0;
    	foreach ($data as $d) {
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		 
    		$apiMapper->post($d);
    		$total++;
    	}
    	
    	echo "Ciudades  mapeados: ".$total."\n";
    	
//     	$ciudadesSF1 = $cb->getCiudades();
    	
//     	$sinCoincidencia = array();
//     	$total = 0;
//     	foreach ($ciudadesSF1 as $ciudad) {
    		
//     		//$ciudad = utf8_encode($ciudad);
//     		echo "\n".utf8_encode($ciudad['nombre'])."\n"; 		
//     		$filter = array(
//     				'value' => utf8_encode($ciudad['nombre']),
//     				'operator' => 'start_with',
//     				'property' => 'name'
//     		);
    		
//     		//print_r($filter);
//     		$filter = json_encode(array($filter));
    		 
    		
//     		$urlapiTownTT = $this->server.'crm/main/zero/town?filter='.$filter;
//     	    echo "\n".$urlapiTownTT."\n";
//     		$apiTownTT = $this->SetupApi($urlapiTownTT, $this->user, $this->pass);
//     		//print_r($apiTownTT->get());
//     		$townSF2 = $apiTownTT->get();
    		
//     		//echo $townSF2;
    		
//     		//print_r($townSF2);
//     		//echo $townSF2['total'];
    		
//     		$townSF2 = json_decode($townSF2, true);
    		
//     		echo "Total: ".$townSF2['total'];
//     		//print_r($townSF2);
    		
//     		if($townSF2['total'] == 1){
//     			$mapper = array(
//     				'name' => 'propertyTown.MON',
// 					'idSource' => $ciudad['id_ciudad'],
//     				'idTarget' => $townSF2['data'][0]['id']
//     			);
    		
//     			$urlapiMapper = $this->server.'admin/sifinca/mapper';
//     			$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		
//     			print_r($mapper);
    			
//     			$apiMapper->post($mapper);
//     			$total++;
//     		}else{
//     			//echo "No hay coincidencia\n";
//     			$sinCoincidencia[] = $ciudad;
//     		}

//     	}
//     	echo "\n--------------\n";
//     	print_r($sinCoincidencia);
//     	echo "Ciudades SF1: ".count($ciudadesSF1)."\n";
//     	echo "Ciudades mapeados: ".$total."\n";	
//     	echo "Ciudades no mapeados: ".count($sinCoincidencia)."\n";
//     	//print_r($sinCoincidencia);
    	
//     	return ;
    }
    
    function mapperBarrios($cb) {
    
    		
    		$filter = null;
    		
//     		$filter[] = array(
//     				'value' => 'cc036e72-6ee8-4513-a81a-c6b3c9724af7', //Monteria
//     				'operator' => '=',
//     				'property' => 'town.id'
//     		);
    		
//     		$filter[] = array(
//     				'value' => '02194845-d370-494a-9aed-738590fa85ca', //Cerete
//     				'operator' => '=',
//     				'property' => 'town.id'
//     		);
    		
//     		$filter[] = array(
//     				'value' => 'fc5d1361-b24a-4f41-b2d6-e6df6f2ec94a', //Cienaga
//     				'operator' => '=',
//     				'property' => 'town.id'
//     		);

//     		$filter[] = array(
//     				'value' => 'ae074201-8bbe-472e-9093-eb1e91659edb', //Cove–as
//     				'operator' => '=',
//     				'property' => 'town.id'
//     		);

//     		$filter[] = array(
//     				'value' => '7090c41e-86ec-4032-ac1f-8baf52db104d', //Lorica
//     				'operator' => '=',
//     				'property' => 'town.id'
//     		);
    		
    		$filter[] = array(
    				'value' => 'cd193b6b-9545-4afb-9124-9d864a54d3f4', //Necocli
    				'operator' => '=',
    				'property' => 'town.id'
    		);
    		
    		$filterDecode = json_encode($filter);
    		 
    		//echo "filtro decode ".utf8_encode($filterDecode);
    		
    		
    		
    		$urlapiTownTT = $this->server.'crm/main/zero/district?filter='.$filterDecode;
    		//$urlapiTownTT = $this->server.'crm/main/zero/district';
    		//echo "\n".$urlapiTownTT."\n";
    		$apiTownTT = $this->SetupApi($urlapiTownTT, $this->user, $this->pass);
    		//print_r($apiTownTT->get());
    		$townSF2 = $apiTownTT->get();
    		
    		
    		$barriosSF2 = json_decode($townSF2, true);
    		
    		foreach ($barriosSF2['data'] as $b) {
    			
    			$mapper = array(
    				'name' => 'propertyDistrict.MON',
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
    
    	//$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/mapperDestinacion.json");
    	//echo $fileJson;
    	$fileJson = '[
  {
    "name": "propertyDestiny.MON",
    "idSource": "0",
    "idTarget": "169c9685-b58a-47e7-badc-20627583ca13",
    "label": "n/a"
  },
  {
    "name": "propertyDestiny.MON",
    "idSource": "1",
    "idTarget": "f537d6c8-764d-47c8-82c1-38bb8c29e443",
    "label": "vivienda"
  },
  {
    "name": "propertyDestiny.MON",
    "idSource": "2",
    "idTarget": "be9b75a2-3d73-42e1-a553-998e40994f35",
    "label": "comercial"
  },
  {
    "name": "propertyDestiny.MON",
    "idSource": "3",
    "idTarget": "15c724fe-c766-4dff-abc8-b9aaeb5c57f6",
    "label": "mixto"
  }
]';
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
    
    	//$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/mapperEstratos.json");
    	//echo $fileJson;
    	$fileJson = '[
			  {
			    "name": "stratum.MON",
			    "idSource": "1",
			    "idTarget": "44b9370e-0ad9-4904-88b6-e7264180d4bf"
			  },
			  {
			    "name": "stratum.MON",
			    "idSource": "2",
			    "idTarget": "988831b9-723b-4396-ba80-4b4e46acf12f"
			  },
			  {
			    "name": "stratum.MON",
			    "idSource": "3",
			    "idTarget": "26a81fc4-8d36-4397-81c3-ffedb2c47ccb"
			  },
			  {
			    "name": "stratum.MON",
			    "idSource": "4",
			    "idTarget": "97c62c9b-9271-49ff-a110-70736b5f7a65"
			  },
			  {
			    "name": "stratum.MON",
			    "idSource": "5",
			    "idTarget": "acceb6be-aba4-4998-b0d4-02a786fbfa6f"
			  },
			  {
			    "name": "stratum.MON",
			    "idSource": "6",
			    "idTarget": "2c4fecfa-5eeb-4f6b-8d2c-ea6c4cd85bda"
			  }
			]';
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
    
    	//$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/mapperSucursales.json");
    	//echo $fileJson;
    	$fileJson = '[
			  {
			    "name": "propertyOffice.MON",
			    "idSource": "1",
			    "idTarget": "a989afd0-f14f-4e20-9c75-5e084070129c",
			    "label": "monteria"
			  },
			  {
			    "name": "propertyOffice.MON",
			    "idSource": "2",
			    "idTarget": "439975ab-e7f2-4569-b694-8383ee2cd74b",
			    "label": "cerete"
			  },
			  {
			    "name": "propertyOffice.MON",
			    "idSource": "3",
			    "idTarget": "f0196680-ba6c-49f0-821a-da90eb5b6752",
			    "label": "norte"
			  }
			]';
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
    	 
    	//$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/caracteristicasInmueble.json");
    	
    	$fileJson = '[
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 1,
	    "idTarget": "79cb1f8c-2842-4456-86fe-ea5d397650f8",
	    "label": "SALA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 2,
	    "idTarget": "3ad1482a-2fe4-4c93-9fc3-7ca8640ba2fa",
	    "label": "COMEDOR"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 3,
	    "idTarget": "9a3b47e6-7130-4ffb-82bf-85d91a7df004",
	    "label": "SALA COMEDOR"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 4,
	    "idTarget": "38237c6c-b8f3-410c-8ecc-a8aca09b0732",
	    "label": "COCINA INTEGRAL"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 5,
	    "idTarget": "eddf57bf-5f85-4c83-8e70-6750b1203533",
	    "label": "COCINA SEMINTEGRAL"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 6,
	    "idTarget": "5c7aefca-063d-4664-a83a-aeb17294bd1c",
	    "label": "COCINA NORMAL"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 7,
	    "idTarget": "44190306-d590-4c52-926a-d8183b36ba52",
	    "label": "ALCOBA DEL SERVICIO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 8,
	    "idTarget": "2b5d9fee-e8fb-4e1c-87e1-5d9174645a27",
	    "label": "BA„O DEL SERVICIO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 9,
	    "idTarget": "c24d7f35-21cb-46a4-9d52-886cdfda115a",
	    "label": "AREA DE LABORES"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 10,
	    "idTarget": "826674ad-b83f-4568-ab3f-478ab2ae870c",
	    "label": "PATIO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 11,
	    "idTarget": "36c8ac25-d168-49a3-9219-d81422005132",
	    "label": "GARAJE INTERNO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 12,
	    "idTarget": "4d37970f-ee91-4b6e-a93f-ccd933d22157",
	    "label": "ZONA PARQUEO CUBIERTA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 13,
	    "idTarget": "93b3a728-0798-45b6-a9b5-366c358f8a28",
	    "label": "ZONA PARQUEO DESCUBIERTA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 14,
	    "idTarget": "7a3d3106-e2be-496c-82af-ae70b0f4bf32",
	    "label": "BALCîN"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 15,
	    "idTarget": "9ed6a6f8-138d-49a1-9483-a4e05750ce28",
	    "label": "AIRE ACONDICIONADO CENTRAL"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 16,
	    "idTarget": "3b99b677-aa8d-4e2b-ba8b-574c7491e1ab",
	    "label": "AIRE ACONDICIONADO DE VENTANA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 17,
	    "idTarget": "732ac0e0-2bfd-4507-baa7-090fc161dbb3",
	    "label": "CALENTADOR DE AGUA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 18,
	    "idTarget": "5b5afe92-8e45-491a-a4e4-afb9d542cafe",
	    "label": "ESTUDIO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 19,
	    "idTarget": "aa533d39-9b02-4ca7-ade2-375169bb4834",
	    "label": "UNA PLANTA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 20,
	    "idTarget": "84645d39-d6a7-4646-9eb0-06d9fc3f2705",
	    "label": "DOS PLANTAS"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 21,
	    "idTarget": "f0a1680f-d4da-4b19-b3c9-00d5c19da077",
	    "label": "TRES PLANTAS"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 22,
	    "idTarget": "3e323b34-92fb-478f-8569-6f96f5862798",
	    "label": "SALON"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 23,
	    "idTarget": "6ad32d1c-7afd-440f-bc05-59176c3883f4",
	    "label": "DIVISIONES O PANELES"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 24,
	    "idTarget": "f9405770-10d2-4eaa-97ce-999652121671",
	    "label": "CAFETINES"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 25,
	    "idTarget": "f5942f23-dc9a-409a-85d9-eb6d8f5c4dcc",
	    "label": "MEZANINE"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 26,
	    "idTarget": "8cc4987a-7e9e-4a8c-87a7-de38c9aa965e",
	    "label": "RESISTENCIA PISO NORMAL (1-20 TON)"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 27,
	    "idTarget": "76a15472-fbf5-4e7c-b7d5-9cc69febf9fc",
	    "label": "RESISTENCIA PISO MEDIA (21-50 TON)"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 28,
	    "idTarget": "2e22fabe-4f85-488b-8e95-6e97adbc5c2c",
	    "label": "RESISTENCIA PISO ALTA (50 O MçS)"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 35,
	    "idTarget": "f7d2bcf0-5960-4fb0-9222-0a486fcf6d2f",
	    "label": "OFICINAS"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 40,
	    "idTarget": "70c69c02-0853-4bd6-b6a8-a3c1d3216ae3",
	    "label": "TANQUE RESERVA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 41,
	    "idTarget": "16799086-413b-4b17-977a-1aa8073be06a",
	    "label": "INSTALACIîN ELECTRICA TRIFASICA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 42,
	    "idTarget": "384077ba-ce67-4fd8-8331-dc9806430bdc",
	    "label": "INSTALACIîN ELECTRICA MONOFASICA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 50,
	    "idTarget": "5ca9b96c-c163-4e6e-b163-f1738cb51462",
	    "label": "PORTERIA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 51,
	    "idTarget": "ef254d6e-a286-4269-aff5-4f0c1e6f070a",
	    "label": "CITîFONO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 52,
	    "idTarget": "65fb3f49-0b11-4bb3-8ee3-e6c59b80428c",
	    "label": "ASCENSOR"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 53,
	    "idTarget": "289edb08-03b1-479e-b46b-d3de59d072c2",
	    "label": "ZONAS VERDES"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 54,
	    "idTarget": "28fd08a7-ba5f-467f-b054-de052e09c853",
	    "label": "ZONA JUEGOS INFANTILES"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 55,
	    "idTarget": "1bdeb612-37db-450b-bf7b-10182e15cb28",
	    "label": "PISCINA ADULTOS"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 56,
	    "idTarget": "55090592-e903-45d0-bb60-46071693f46d",
	    "label": "PISCINA NI„OS"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 57,
	    "idTarget": "c28b9d65-d8bb-46ca-86de-58ddf873c4e9",
	    "label": "GIMNASIO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 58,
	    "idTarget": "f749eb45-236e-4ed8-820b-d099937b4a70",
	    "label": "TELEVISION POR CABLE"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 59,
	    "idTarget": "658ccf9f-d013-40e0-b319-4c74dc49be4d",
	    "label": "ANTENA PARABOLICA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 60,
	    "idTarget": "4702a366-f5c6-4ad4-abd7-851c6684db99",
	    "label": "JACUZZI"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 61,
	    "idTarget": "ae97f865-937b-4a22-9069-d09a6485abc6",
	    "label": "SAUNA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 62,
	    "idTarget": "7d6b87a6-cd10-4ffd-817c-f866d3984ba0",
	    "label": "BA„OS TURCOS"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 63,
	    "idTarget": "93bce932-0477-43e0-8175-310495724fbe",
	    "label": "CONJUNTO CERRADO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 64,
	    "idTarget": "827cb0bc-f8b0-40dc-9113-d8c2019ec67e",
	    "label": "SALON COMUNAL"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 65,
	    "idTarget": "dfb781e9-647f-4c85-964a-fc8c0345c9b8",
	    "label": "PLANTA ELƒCTRICA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 66,
	    "idTarget": "c5f44c7a-a676-428a-b74d-3e2ade633ba3",
	    "label": "PLANTA TELƒFONICA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 67,
	    "idTarget": "71850fc7-cf77-4536-b987-e2a3ee759c0a",
	    "label": "ACCESO PARA CARGA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 70,
	    "idTarget": "2fcd2c7d-9046-4419-b26b-d6cfd8dea6a5",
	    "label": "ESQUINERO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 71,
	    "idTarget": "a65c42e7-4d01-4b98-9fab-2675f38c245c",
	    "label": "POLO A TIERRA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 72,
	    "idTarget": "ef7041e1-fbb0-4d6c-bb97-06ea013ebf49",
	    "label": "LêNEA TELEFîNICA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 73,
	    "idTarget": "1ee1ae05-275c-4058-93b7-21b825cd711b",
	    "label": "COCINETA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 74,
	    "idTarget": "1b4629d6-60f0-4aa8-830b-c3321933ba85",
	    "label": "VISTA AL MAR"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 75,
	    "idTarget": "a03509c9-0da4-4ceb-9395-d8ffb1c51b6a",
	    "label": "SEMIAMOBLADO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 76,
	    "idTarget": "77184144-7052-4f87-bb0d-c88778ff3c52",
	    "label": "HALL"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 77,
	    "idTarget": "5b5afe92-8e45-491a-a4e4-afb9d542cafe",
	    "label": "ESTUDIO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 78,
	    "idTarget": "c68f772b-ea85-44e1-a3d3-8c499bb7bc19",
	    "label": "BAR"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 79,
	    "idTarget": "c30f2b8d-058e-415d-8e39-90aaea256af2",
	    "label": "ACUEDUCTO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 80,
	    "idTarget": "e342a919-1cb3-4637-8c0e-5dd13c6dd750",
	    "label": "ALCANTARILLADO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 81,
	    "idTarget": "981f0ec4-17f8-4eb8-b5f1-b1964969d2e3",
	    "label": "ENERGIA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 82,
	    "idTarget": "6215f62b-ff83-480a-96e4-9270cd0139b1",
	    "label": "RED TELEFONêCA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 83,
	    "idTarget": "a343d248-e4b5-4d50-8fd1-a38c09ca0bd3",
	    "label": "GAS NATURAL"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 84,
	    "idTarget": "67df77cb-28db-416b-8c15-f9da2bf40cd5",
	    "label": "TERRENO ONDULADO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 85,
	    "idTarget": "77e01d98-aa74-4cfa-be2a-4e2c80aa51b7",
	    "label": "TERRENO PLANO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 86,
	    "idTarget": "3f68dc6a-3e4a-4c04-ac8f-45fe9d5c88dc",
	    "label": "ARBOLES FRUTALES"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 87,
	    "idTarget": "3e0f128d-364b-4b06-b11d-cd2276db10cf",
	    "label": "CERCAS"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 88,
	    "idTarget": "fef90c02-63bd-42f6-acc5-1026b37e2c59",
	    "label": "CASA MAYORDOMO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 89,
	    "idTarget": "0a6c2fbc-c436-4c55-b35c-1ee336622979",
	    "label": "ALJIBES"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 90,
	    "idTarget": "f433b097-e5bf-4a8e-8e3c-b1db7b2298a6",
	    "label": "POZOS"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 91,
	    "idTarget": "32d7a99f-c09d-4d37-a021-680985ff92e7",
	    "label": "KIOSKOS"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 92,
	    "idTarget": "3565c2e6-ec14-4dda-a29b-808d976bb319",
	    "label": "POTREROS"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 93,
	    "idTarget": "ecf04744-a4d4-4228-bedb-c83406508662",
	    "label": "AGUA VIVA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 94,
	    "idTarget": "6e985357-2bbc-4329-a12a-014eae50b02c",
	    "label": "PISCINAS"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 95,
	    "idTarget": "826674ad-b83f-4568-ab3f-478ab2ae870c",
	    "label": "PATIO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 96,
	    "idTarget": "1c5ab653-39dc-4b89-bb15-84e68d73bc08",
	    "label": "LAVADERO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 97,
	    "idTarget": "1a50af9f-0ce4-47db-a7f3-613173932b8a",
	    "label": "ALBERCA SUBTERRANEA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 98,
	    "idTarget": "32d7a99f-c09d-4d37-a021-680985ff92e7",
	    "label": "KIOSCO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 99,
	    "idTarget": "dd0bc17e-5c58-4cfd-980d-5e5082aef6ac",
	    "label": "TANQUE ELEVADO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 100,
	    "idTarget": "cb655b6e-22c3-4b50-8d96-379ef1ca8204",
	    "label": "ALBERCA EXTERNA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 101,
	    "idTarget": "0a6c2fbc-c436-4c55-b35c-1ee336622979",
	    "label": "ALJIBE"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 102,
	    "idTarget": "e12238e5-4d80-450e-b39c-392f194c5aa7",
	    "label": "MOTOBOMBA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 103,
	    "idTarget": "6e985357-2bbc-4329-a12a-014eae50b02c",
	    "label": "PISCINA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 104,
	    "idTarget": "c3cde66d-ad3d-43e8-8108-387fdd74b367",
	    "label": "CUARTO DE SAN ALEJO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 105,
	    "idTarget": "063c1a1c-07ad-44de-b9fe-f9b8a18acfd6",
	    "label": "GUARDILLA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 106,
	    "idTarget": "dfb781e9-647f-4c85-964a-fc8c0345c9b8",
	    "label": "PLANTA ELECTRICA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 107,
	    "idTarget": "78bdebe1-1ef5-4a23-a915-f74e6703d1de",
	    "label": "JARDIN INTERNO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 108,
	    "idTarget": "e4252afc-79ad-4e65-bee8-1b3cf89137be",
	    "label": "ANTEJARDIN"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 109,
	    "idTarget": "32bb6963-f27a-4176-bf1e-c0524a690d54",
	    "label": "JARDIN EXTERNO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 110,
	    "idTarget": "c10d4f07-f8c2-47c4-b7af-dc28919286c2",
	    "label": "TERRAZA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 111,
	    "idTarget": "98015aa3-bb68-4c13-bdd3-24f94a8d3c43",
	    "label": "DESPENSA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 112,
	    "idTarget": "826674ad-b83f-4568-ab3f-478ab2ae870c",
	    "label": "PATIO INTERNO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 113,
	    "idTarget": "6d03d685-0571-4897-8a8e-a2c97932b480",
	    "label": "SALON DE JUEGOS"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 114,
	    "idTarget": "c2fec77c-9d91-4cee-aab0-7a7a0fd84653",
	    "label": "BIBLIOTECA"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 115,
	    "idTarget": "bb0666a4-e3bf-45eb-8760-d6602a868ed8",
	    "label": "BANO"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 116,
	    "idTarget": "3e323b34-92fb-478f-8569-6f96f5862798",
	    "label": "SALONES"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 117,
	    "idTarget": "c2f7b1ad-609e-4735-819c-44d247bb9617",
	    "label": "CLOSETS"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 118,
	    "idTarget": "3e323b34-92fb-478f-8569-6f96f5862798",
	    "label": "SALON"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 119,
	    "idTarget": "4e715919-c910-4f89-b1a1-e01834cb5710",
	    "label": "MINISPLIT SOLO PARA ALCOBA PRINCIPAL, AIRE DE VENTANA PARA ALCOBAS AUXILIARES"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 120,
	    "idTarget": "4e715919-c910-4f89-b1a1-e01834cb5710",
	    "label": "MINISPLIT"
	  },
	  {
	    "name": "propertyAttribute.MON",
	    "idSource": 121,
	    "idTarget": "21d69bfe-74c4-4025-8c91-35cd3e39f730",
	    "label": "VENTILADORES"
	  }
	]';
    	
    	$data = json_decode($fileJson, true);
    	 
//     	$url = $this->server.'admin/sifinca/attributelist';
    	 
//     	$apiClosingReason = $this->SetupApi($url, $this->user, $this->pass);
    	 
    	$total = 0;
    	$cont = 167;
    	foreach ($data as $d) {
    	    		
    		//$d = $data[$i];
    		
//     		$result = $apiClosingReason->post($d);
//     		$result = json_decode($result, true);
    
    		$urlapiMapper = $this->server.'admin/sifinca/mapper';
    		$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    
//     		$m = array(
//     				"name" => "propertyAttribute",
//     				"idSource" => $cont,
//     				"idTarget" => $result['data'][0]
//     		);
    
    		$apiMapper->post($d);
    
    		$total++;
    		
    	}
    	
    	echo "\n Atributos mapeados: ".$total."\n";
    	 
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
    
    	//     	echo "\n\nInmueble\n";
    	//     	print_r($property['data'][0]);
    	 
    	if($property['total'] > 0){
    		 
    		return true;
    		 
    	}else{
    		return false;
    	}
    
    }
     
    
    
    function buildInmuebles($inmuebles, $inmueblesCtg) {
    	
    	$urlapiInmueble = $this->server.'catchment/main/property';
    	 
    	$apiInmueble = $this->SetupApi($urlapiInmueble, $this->user, $this->pass);
    	
    	$officeCentro = array(
    		'id' => $this->officeCentro
    	);
    	
    	$total = 0;
    	
    	$totalInmueblesSF1 = count($inmuebles);
    	//$totalInmueblesSF1 = 1000;
    	
    	$startTime= new \DateTime();
    	
    	$porDonde = 0;
    	
    	for ($i = 0; $i < $totalInmueblesSF1; $i++) {
    		
    		$inmueble = $inmuebles[$i];
    		
    		//foreach ($inmuebles as $inmueble) {
    		
    		$edificio = null;
    		
    		
    		$existe = $this->searchProperty($inmueble['id_inmueble']);
    		
    		if(!$existe){
    			

                if($inmueble['id_edificio']){
                    //Buscar edificio
                    
                    $filter = array(
                            'value' => $inmueble['id_edificio'],
                            'operator' => 'equal',
                            'property' => 'idSifincaOne'
                    );
                    $filter = json_encode(array($filter));
                             
                    $urlBuildingSF2 = $this->server.'catchment/main/building?filter='.$filter;
                     
                    $apiBuildingSF2 = $this->SetupApi($urlBuildingSF2, $this->user, $this->pass);
                    
                    $buildingSF2 = $apiBuildingSF2->get();
                    
                    $buildingSF2 = json_decode($buildingSF2, true);
                    
                    if($buildingSF2['total'] > 0){
                        $edificio = array('id'=>$buildingSF2['data'][0]['id']);
                    }
                    
                    
                }
                
                $urlPropertyType = $this->server.'admin/sifinca/mapper/propertyTypeCatchmentMON/'.$inmueble['id_tipo_inmueble'];
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
                    //Destino no aplica
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
                
                
                if(!empty($inmueble['estrato'])){
                    $urlStratum = $this->server.'admin/sifinca/mapper/stratum.MON/'.$inmueble['estrato'];
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
            
                
                $urlOffice = $this->server.'admin/sifinca/mapper/propertyOffice.MON/'.$inmueble['id_sucursal'];
                //echo "\n".$urlOffice."\n";
                
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
                
                
                //Retirado
                if($inmueble['promocion'] == '0'){
                     $propertyStatus = array('id'=>'e3a87433-0439-47a8-a59f-62e511eb87d7');
                }
                
                //Disponible
                if($inmueble['promocion'] == '1'){
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
                        "retirementReason" => $retirementReason
                );
                
//                 echo "\nCaracteristicas\n";
//                 print_r($caracteristicas);
                
                $json = json_encode($bInmueble);
                
//                 echo "\nJSON\n";
//                 echo "\n\n".$json."\n\n";
                 
                $result = $apiInmueble->post($bInmueble);
                
                $result = json_decode($result, true);
                
                
                if($result['success'] == true){
                    echo "\nOk";
                    $total++;
                    
                    $idInmuebleSF2 = $result['data'][0];
                    
                    
    //              if($inmueble['estado_operativo'] == '0'){
    //                  $this->buildPublicacion($idInmuebleSF2, $inmueble);
    //              }
                            
                    
                }else{
                    echo "\nError\n";
                    //print_r($result);
                    
                    echo $json;
                    
                    $urlapiMapper = $this->server.'catchment/main/errorproperty';
                    $apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);

                    $error = array(
                        'property' => $inmueble['id_inmueble'],
                        'objectJson' => $json
                    );
                    
                    $apiMapper->post($error);
                    
                }
                
              


    		}else{

                echo "\nInmueble ya existe\n";

            }
    		
            $porDonde++;
            echo "\n\nvamos por: ".$porDonde."\n";
    		
    	}
    	
    	
    	$finalTime = new \DateTime();
    	 
    	 
    	$diff = $startTime->diff($finalTime);
    	 
    	 
    	echo "\n\n Fecha inicial: ".$startTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Fecha final: ".$finalTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
    	 
    	
    	echo "\n\nTotal inmuebles pasados ".$total."\n";
    }
    
    function buildEdificios($inmueblesCtg) {
    	
    	$edificiosSF1 = $inmueblesCtg->getEdificios();
    	
    	$urlEdificio = $this->server.'catchment/main/building';
    	$apiEdificio = $this->SetupApi($urlEdificio, $this->user, $this->pass);
    	
    	$total = 0;
    	
    	for ($i = 100; $i < 1148; $i++) {
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
    				'department' => array('id' => $this->bolivar),
    				'town' => array('id' => $this->cartagena),
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
    	
    	$urlDistrict = $this->server.'admin/sifinca/mapper/propertyDistrict.MON/'.$inmueble['id_barrio'];
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
    				"name" => 'Alcobas',
    				"propertyAttribute" => array(
    						'id' => $this->attributeAlcobas
    				)
    		);
    		$caracteristicas[] = $bcarateristica;
    	}
    	 
    	//BaÃ±os
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
    		
    		$urlPropertyAttribute = $this->server.'admin/sifinca/mapper/propertyAttribute.MON/'.$c['id_caracteristica'];
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

    //Actualizacion de estado y comentarios 
    function updateInmuebles($inmuebles, $conexion) {
    	    	    	 
    	$total = 0;
    	 
    	//$totalInmueblesSF1 = count($inmuebles);
    	$totalInmueblesSF1 = 30000;
    	
    	$porDonde = 0;
    	 
		$startTime= new \DateTime();
		

    	for ($i = 27000; $i < $totalInmueblesSF1; $i++) {
    	
    		$inmueble = $inmuebles[$i];
    		
    		$urlapiInmueble = $this->server.'catchment/main/property/'.$inmueble['id_inmueble'].'/status/'.$inmueble['promocion'];
    		
    		$apiInmueble = $this->SetupApi($urlapiInmueble, $this->user, $this->pass);
    		
     		//echo  "\n".$urlapiInmueble;
    		
    		$bupdate = array(
    				'linderos' => $this->cleanString($inmueble['linderos']),
    				
    		);
    		
    		$json = json_encode($bupdate);
    		
    		//echo "\n".$json."\n";
    		
    		//$result = $apiInmueble->get();
    		$result = $apiInmueble->post($bupdate);
    		
    		$result = json_decode($result, true);
    		
//     		echo "\nresult\n";
//     		print_r($result);
    		
      		//return ;
    		
    		if (array_key_exists("error",$result)){
    			echo "\nerror\n";
    		}else{
    			
    			$porDonde++;
    			
    			
    			$total++;
    			
    			echo "\nvamos por : ".$porDonde."\n";
    		}
    		
    	}
    	
    	$finalTime = new \DateTime();
    	
    	
    	$diff = $startTime->diff($finalTime);
    	
    	
    	echo "\n\n Fecha inicial: ".$startTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Fecha final: ".$finalTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
    	
    	echo "\ntotal de inmuebles actualizados: ".$total."\n";
    }
    
    
    /**
     * Actaulizacion general del inmuebles de sifinca1 a sifinca2
     * @param unknown $param
     */
    function updateProperty($inmuebles, $conexion) {
    	
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