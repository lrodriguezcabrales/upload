<?php
namespace upload\command;

use upload\model\clientsBogota;
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

class ClientsBogotaCommand extends Command
{	
	
	public $server = 'http://10.102.1.22/sifinca/web/app_dev.php/';
	public $user= "sifinca@araujoysegovia.com";
	public $pass="araujo123";
	public $colombia = '9ced8f51-6caf-4d6f-bdaf-e72d9a246bc7';
	public $serverRoot = 'http://10.102.1.22/';
	
	
    protected function configure()
    {
        $this->setName('clientsBogota')
		             ->setDescription('Comando para obtener datos de cliente SF1');
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

        $cb = new clientsBogota($conn);

        //$this->mapperIdentificaciones($cb);
        //$this->mapperPaises($cb);
        //$this->mapperCiudades($cb);
        //$this->mapperCiudadesNotFound($cb);
        //$this->mapperEstadosCiviles($cb);
        //$this->mapperNivelDeEstudio($cb);
       	$clients = $cb->getClients();
		$this->buildClients($clients);
        //print_r($clients);

    }
    
    function mapperIdentificaciones($cb) {
    	
    	$identificacionesSF1 = $cb->getIdentificacion();
    	
    	//print_r($identificacionesSF1);
    	
    	$urlapi = 'http://10.102.1.22/sifinca/web/app_dev.php/admin/sifinca/idtype';
    	$user= "sifinca@araujoysegovia.com";
    	$pass="araujo123";
    	
    	$api = $this->SetupApi($urlapi, $user, $pass);
    	
    	$identificacionesSF2 = $api->get();
    	//print_r($identificacionesSF2);
    	
    	$urlapi2 = 'http://10.102.1.22/sifinca/web/app_dev.php/admin/sifinca/mapper';
    	$api2 = $this->SetupApi($urlapi2, $user, $pass);
    	
    	$idsTypeMapper = array();
    	
   		$cedula = array(
   				'name' => 'idType',
   				'idSource' => '1',
   				'idTarget' => '3bf118f6-643a-4c54-83d4-b0b331e7594d'
   		);
   		$idsTypeMapper[] = $cedula;
   		
   		$nit = array(
   				'name' => 'idType',
   				'idSource' => '2',
   				'idTarget' => '3bf118f6-643a-4c54-83d4-b0b331e7594d'
   		);
   		$idsTypeMapper[] = $nit;
   		
   		$pasaporte = array(
   				'name' => 'idType',
   				'idSource' => '3',
   				'idTarget' => '3bf118f6-643a-4c54-83d4-b0b331e7594d'
   		);
   		$idsTypeMapper[] = $pasaporte;
   		
   		$cedulaExtranjeria = array(
   				'name' => 'idType',
   				'idSource' => '4',
   				'idTarget' => '3bf118f6-643a-4c54-83d4-b0b331e7594d'
   		);
   		$idsTypeMapper[] = $cedulaExtranjeria;
   		
   		$tarjetaIdentidad = array(
   				'name' => 'idType',
   				'idSource' => '6',
   				'idTarget' => '3bf118f6-643a-4c54-83d4-b0b331e7594d'
   		);
   		$idsTypeMapper[] = $tarjetaIdentidad;
   		
   		$total = 0;
		foreach ($idsTypeMapper as $value) {
			$api2->post($value);
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
    	
    	$user= "sifinca@araujoysegovia.com";
    	$pass="araujo123";
    	 
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

    		$apiCountry = $this->SetupApi($urlapiConuntry, $user, $pass);
    		
    		$countrySF2 = $apiCountry->get();
    		
    		$countrySF2 = json_decode($countrySF2, true);
    		
    		if(count($countrySF2['data']) > 0){
    			$mapper = array(
    					'name' => 'country.BOG',
    					'idSource' => $pais,
    					'idTarget' => $countrySF2['data'][0]['id']
    			); 
    			
    			$urlapiMapper = $this->server.'admin/sifinca/mapper';
    			$apiMapper = $this->SetupApi($urlapiMapper, $user, $pass);
    			
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
    
    	$user= "sifinca@araujoysegovia.com";
    	$pass="araujo123";
    
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
    		$apiTown = $this->SetupApi($urlapiTown, $user, $pass);
    		
    		$townSF2 = $apiTown->get();
    		
    		//echo $townSF2;
    		
    		//print_r($townSF2);
    		
    		$townSF2 = json_decode($townSF2, true);
    		
    		//print_r($townSF2);
    		
    		if($townSF2['total'] == 1){
    			$mapper = array(
    					'name' => 'town.BOG',
    					'idSource' => $ciudad,
    					'idTarget' => $townSF2['data'][0]['id']
    			);
    		
    			$urlapiMapper = $this->server.'admin/sifinca/mapper';
    			$apiMapper = $this->SetupApi($urlapiMapper, $user, $pass);
    		
    			$apiMapper->post($mapper);
    			$total++;
    		}else{
    			//echo "No hay coincidencia\n";
    			$sinCoincidencia[] = $ciudad;
    		}

    	}
    
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
    	
    	$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/mapperNivelDeEstudio.json");
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
    
    function buildClients($clients) {
    	
    	//echo count($clients);
    	
    	$total = 0;
    	foreach ($clients as $key => $client) {
    		
			//print_r($client);
    		
    		$clientType = null;
    		if($client['nat_juridica'] == 'N'){
    			$clientType = 2;
    			      			   			
    			$this->buildClintePersona($client);
    			
    			$total++;
    			
    		}else if($client['nat_juridica'] == 'J'){
    			$clientType = 3;
    		}
    		
    		
    		//print_r($result);
    		
    	}
    	
    	echo "Total de clintes creados en SF2: ".$total."\n";
    	
    }
    
    function buildClintePersona($client) {
    	
    	$urlapiClient = $this->server.'crm/main/clientperson';
    	
    	$apiClient = $this->SetupApi($urlapiClient, $this->user, $this->pass);
    	 
    	$urlapiMapper = $this->server.'admin/sifinca/mapper/idType/'.$client['id_identificacion'];
    	$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    	 
    	$idTypeMapper = $apiMapper->get();
    	$idTypeMapper = json_decode($idTypeMapper, true);
    	//     		print_r($idTypeMapper);
    	
    	//     		print_r($idTypeMapper["total"]);
    	
    	$idType = $idTypeMapper['data']['0']['idTarget'];
    	 
    	 
    	$identity = array(
    			'number' => $client['id_cliente'],
    			'idType' => array('id'=>$idType)
    	);
    	
    	//$company = array('id'=>'19dc5e46-ec2a-4b09-a671-f18c2d90b48c');
    	
    	$direcciones = $this->buildDirecciones($client);
    	
    	$telefonos = $this->buildTelefonos($client);
    	
    	
    	//echo "\n Estado civil: ".$client['estado_civil']."\n";
    	$urlapiMaritalStatus = $this->server.'admin/sifinca/mapper/maritalStatus/'.$client['estado_civil'];
    	$apiMapperMaritalStatus = $this->SetupApi($urlapiMaritalStatus, $this->user, $this->pass);
    	 
    	$maritalStatusMapper = $apiMapperMaritalStatus->get();
    	$maritalStatusMapper = json_decode($maritalStatusMapper, true);
    	//print_r($maritalStatusMapper);
    	$maritalStatus = $maritalStatusMapper['data']['0']['idTarget'];
    	if(!is_null($maritalStatus)){
    		
    		//echo "maritalStatus".$maritalStatus;
    		
    		$maritalStatus = array('id'=>$maritalStatus);
    		
    		if($maritalStatus == 0){
    			$maritalStatus = null;
    		}
    	} 	
    	 
    	//echo "maritalStatus".$maritalStatus;

    	//----aad
    	$urlapiLevelStudy = $this->server.'admin/sifinca/mapper/levelStudy/'.$client['nivel_estudios'];
    	$apiLevelStudy = $this->SetupApi($urlapiLevelStudy, $this->user, $this->pass);
    	
    	$levelStudyMapper = $apiLevelStudy->get();
    	$levelStudyMapper = json_decode($levelStudyMapper, true);
    	//print_r($maritalStatusMapper);
    	$levelStudy = $levelStudyMapper['data']['0']['idTarget'];
    	if(!is_null($levelStudy)){
    	
    		$levelStudy = array('id'=>$levelStudy);
    		
    		if($levelStudy['total'] == 0){
    			$levelStudy = null;
    		}
    		
    	}
    	
    	
    	$bClient = array(
    			'firstname' => $client['nombre'],
    			'secondname' => '',
    			'lastname' => $client['apellido'],
    			"secondLastname" => '',
    			//'client_type' => $clientType,
    			'identity' => $identity,
    			'adress' => $direcciones,
    			'phones' => $telefonos,
    			'civilState' => $maritalStatus,
    			'email' => $client['e_mail'],
    			'profession' => $levelStudy,
    	);
    	
    	
    	//print_r($bClient);
    	
    	$json = json_encode($bClient);
    	//echo "\n".$json."\n";
    	
    	$result = $apiClient->post($bClient);
    	
    	echo "\nCliente creado\n";
    	
    }
    
    function buildClintePersonaJuridica($client) {
    	 
    	$urlapiClient = $this->server.'crm/main/clientcompany';
    	 
    	$apiClient = $this->SetupApi($urlapiClient, $this->user, $this->pass);
    
    	$urlapiMapper = $this->server.'admin/sifinca/mapper/idType/'.$client['id_identificacion'];
    	$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    
    	$idTypeMapper = $apiMapper->get();
    	$idTypeMapper = json_decode($idTypeMapper, true);
    	//     		print_r($idTypeMapper);
    	 
    	//     		print_r($idTypeMapper["total"]);
    	 
    	$idType = $idTypeMapper['data']['0']['idTarget'];
    
    
    	$identity = array(
    			'number' => $client['id_cliente'],
    			'idType' => array('id'=>$idType)
    	);
    	 
    	//$company = array('id'=>'19dc5e46-ec2a-4b09-a671-f18c2d90b48c');
    	 
    	$direcciones = $this->buildDirecciones($client);
    	 
    	$telefonos = $this->buildTelefonos($client);
    	    	   	 
    	$bClient = array(
    			'identity' => $identity,
    			'comercialName' => $client['nombre'],
    			'contributor' => array(), 
    			'lastname' => $client['apellido'],
    			"secondLastname" => '',
    			//'client_type' => $clientType,
    			
    			'adress' => $direcciones,
    			'phones' => $telefonos,
    			'civilState' => $maritalStatus,
    	);
    	 
    	 
    	//print_r($bClient);
    	 
    	$json = json_encode($bClient);
    	//echo "\n".$json."\n";
    	 
    	$result = $apiClient->post($bClient);
    	 
    	print($result);
    	
    	echo "\nCliente creado\n";
    	 
    }
    
    
    function buildDirecciones($client) {
    	 
    	$direccionesSF1 = array();
    	 
    	$dirResidencia = null;
    	$dirTrabajo = null;
    	 
    	if($client['dir_residencia']){
    
    
    		$address = $client['dir_residencia'].", ".$client['barrio_residencia'].", ".$client['edificio_residencia'];
    
    		$urlapiMapperCountry = $this->server.'admin/sifinca/mapper/country.BOG/'.$client['pais_residencia'];
    		$apiMapper = $this->SetupApi($urlapiMapperCountry, $this->user, $this->pass);
    		 
    		$country = $apiMapper->get();
    		$country = json_decode($country, true);
    
    		$urlapiMapperTown = $this->server.'admin/sifinca/mapper/town.BOG/'.$client['ciudad_residencia'];
    		$apiMapper = $this->SetupApi($urlapiMapperTown, $this->user, $this->pass);
    		 
    		$town = $apiMapper->get();
    		$town = json_decode($town, true);
    
    		if($country['total'] == 1){
    			 
    			if($town['total'] == 1){
    				$town = array('id' => $town['data'][0]['idTarget']);
    			}else{
    				$town = null;
    			}
    			
    			if(strlen($address) > 7 ){
    				$dirResidencia = array(
    						'address' => $address,
    						'country' => array('id' => $country['data'][0]['idTarget']),
    						'department' => null,
    						'town' => $town,
    						'district' => null,
    						'typeAddress' => array('id'=>'c86fbba2-4643-4caa-9549-75f301627fdb') //CASA
    				);
    			}    			
    		}
    	}
    	 
    	//print_r($dirResidencia);
    	
    	if($client['dir_trabajo']){
    
    
    		$address = $client['dir_trabajo'].", ".$client['barrio_trabajo'].", ".$client['edificio_trabajo'];
    
    		$urlapiMapperCountry = $this->server.'admin/sifinca/mapper/country.BOG/'.$client['pais_trabaja'];
    		$apiMapper = $this->SetupApi($urlapiMapperCountry, $this->user, $this->pass);
    		 
    		$country = $apiMapper->get();
    		$country = json_decode($country, true);
    
    		$urlapiMapperTown = $this->server.'admin/sifinca/mapper/town.BOG/'.$client['ciudad_trabaja'];
    		$apiMapper = $this->SetupApi($urlapiMapperTown, $this->user, $this->pass);
    		 
    		$town = $apiMapper->get();
    		$town = json_decode($town, true);
    
    		if($country['total'] == 1){
    			 
    			if($town['total'] == 1){
    				$town = $town['data'][0]['idTarget'];
    			}else{
    				$town = null;
    			}
    			if(strlen($address) > 7){
    				$dirTrabajo = array(
    						'address' => $address,
    						'country' => $country['data'][0]['idTarget'],
    						'department' => null,
    						'town' => $town,
    						'district' => null,
    						'typeAddress' => array('id'=>'26f76dc7-4204-4972-9d47-30360735514b') //OFICINA
    				);
    			}
    			
    		}
    	}
    
    	//print_r($dirTrabajo);
    	
    	if(!is_null($dirResidencia)){
    		$direccionesSF1[] = $dirResidencia;
    	}
    	
    	if(!is_null($dirTrabajo)){
    		$direccionesSF1[] = $dirTrabajo;
    	}
    	
    	return $direccionesSF1;
    	 
    }

    function buildTelefonos($client) {
    	
    	//print_r($client);
    	
    	//echo "Entro aqui";
    	$telefono_residencia = $client['teL_residencia'];
    	$telefono_residencia = $this->validarTelefono($telefono_residencia);
    	
    	$telefono_trabajo = $client['tel_trabajo'];
    	$telefono_trabajo = $this->validarTelefono($telefono_trabajo);
    	
    	$telefono_celular = $client['tel_celular'];
    	$telefono_celular = $this->validarTelefono($telefono_celular);
    	    	
    	$telefono_residencia2 = $client['tel_residencia2'];
    	$telefono_residencia2 = $this->validarTelefono($telefono_residencia2);
    	  	
    	
    	$telefonos = array();
    	
    	if(!is_null($telefono_residencia)){
    		$telefonos[] = $telefono_residencia;
    	}
    	
    	if(!is_null($telefono_trabajo)){
    		$telefonos[] = $telefono_trabajo;
    	}
    	 
    	if(!is_null($telefono_celular)){
    		$telefonos[] = $telefono_celular;
    	}

    	if(!is_null($telefono_residencia2)){
    		$telefonos[] = $telefono_residencia2;
    	}
    	    	   	
    	//$telefonos = array_unique($telefonos);
    	
    	return $telefonos;
    }
    
    function validarTelefono($telefono) {
    	
    	$phone = null;
    	
    	//echo "\nTelefono: ".$telefono."\n";
    	
    	if(!is_null($telefono) && (strlen($telefono) > 1)){
    		
//     		echo "\nTelefono: ".$telefono."\n";
//     		echo "\n".strlen($telefono);
    		
    		if(strlen($telefono) >= 10){
    			$phone = array(
    					'number' => $telefono,
    					'phoneType' => array('id' => '8c7c99e4-7cad-4b10-9181-543c5221ec9a'), //Id phoneType SF2, celular
    					'country' => array('id' => $this->colombia)
    					
    			);
    		}
    		
    		if( (strlen($telefono) > 0) && (strlen($telefono) < 10) ){
    			$phone = array(
    					'number' => $telefono,
    					'phoneType' => array('id' => '0fb57090-7f0c-4b95-9494-353717fc53a8'), //Id phoneType SF2, fijo
    					'country' => array('id' => $this->colombia)
    					
    			);
    		}
    	}
    	
    	return $phone;
    }
    
    
    protected function SetupApi($urlapi,$user,$pass){
    
    	$url= $this->server."login";
    	$headers = array(
    			'Accept: application/json',
    			'Content-Type: application/json',		 
    	);
    
    	$a = new api($url, $headers);
    
    	$result= $a->post(array("user"=>$user,"password"=>$pass));
    	
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