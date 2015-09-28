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
       // $this->mapperCiudades($inmueblesCtg);
        
        //$this->mapperBarrios($inmueblesCtg);
        
        $this->mapperDestinacion($inmueblesCtg);
        
        //$this->mapperEstadosCiviles($cb);
        //$this->mapperNivelDeEstudio($cb);
        //$this->mapperTipoDeContribuyente($cb);
       	//$clients = $cb->getClients();
	    //$this->buildClients($clients);
        //print_r($clients);

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