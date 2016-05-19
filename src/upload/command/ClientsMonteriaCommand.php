<?php
namespace upload\command;

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

class ClientsMonteriaCommand extends Command
{	
	
	public $server = 'http://104.239.170.71/monteriaServer/web/app.php/';
	public $serverRoot = 'http://104.239.170.71/';

	
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;
		
	public $colombia = '8701307b-d8bd-49f1-8a91-5d0f7b8046b3';
		
	public $idTypeCedula = '6f80343e-f629-492a-80d1-a7e197c7cf48';
	
	public $contactTypeOther = 'ac76deff-6371-4264-b724-b24b98803b94';
	
	public  $typeAddressHome = '8b8b75ae-6338-461f-8bbd-fc1283621d83';
	public  $typeAddressCorrespondencia = 'e8e9cc62-ec79-4453-8247-a57cc1cf4712';
	
	public $typeAddresOficina = '8e62e10e-b34a-4e3e-ad3a-6e288134ac97';
    
    protected function configure()
    {
        $this->setName('clientesMonteria') 
		             ->setDescription('Comando para obtener datos de cliente SF1');
	}
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Datos de cliente SF1 MONTERIA \n");

        $conn = new data(array(
            'server' =>'192.168.100.1'
            ,'user' =>'sa'
            ,'pass' =>'75080508360'
            ,'database' =>'sifinca' 
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
		$clients = $cb->getClientsMonteria();
		$this->buildClients($clients);
        //print_r($clients);

        //$this->banks($cb);
    }
    
    function mapperIdentificaciones($cb) {
    	
    	$identificacionesSF1 = $cb->getIdentificacionMonteria();
    	
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
   				'idTarget' => '6f80343e-f629-492a-80d1-a7e197c7cf48',
   				'label' => 'cedula'
   		);
   		$idsTypeMapper[] = $cedula;
   		
   		$nit = array(
   				'name' => 'idType',
   				'idSource' => '2',
   				'idTarget' => '6c29bc74-a33a-42ed-8d24-1d86e31dce9f',
   				'label' => 'nit'
   		);
   		$idsTypeMapper[] = $nit;
   		
   		$pasaporte = array(
   				'name' => 'idType',
   				'idSource' => '3',
   				'idTarget' => '484cb0cb-29cd-4aa6-91c5-c246332112ff',
   				'label' => 'pasaporte'
   		);
   		$idsTypeMapper[] = $pasaporte;
   		
   		$cedulaExtranjeria = array(
   				'name' => 'idType',
   				'idSource' => '4',
   				'idTarget' => 'd904096d-a740-44a5-a554-44e50bfbca00',
   				'label' => 'cedulaextranjeria'
   		);
   		$idsTypeMapper[] = $cedulaExtranjeria;
   		
   		$nuip = array(
   				'name' => 'idType',
   				'idSource' => '5',
   				'idTarget' => '552311ae-169e-4822-aac7-4f15c162f4e7',
   				'label' => 'nuip'
   		);
   		$idsTypeMapper[] = $nuip;
   		
   		$tarjetaIdentidad = array(
   				'name' => 'idType',
   				'idSource' => '6',
   				'idTarget' => '1513066b-6599-4f4d-acd9-c51012a9c121',
   				'label' => 'tarjetaidentidad'
   		);
   		$idsTypeMapper[] = $tarjetaIdentidad;
   		
   		$idFiscalTributaria = array(
   				'name' => 'idType',
   				'idSource' => '7',
   				'idTarget' => '41e4a290-db12-484d-a7c3-74e466021a82',
   				'label' => 'fiscaltributaria'
   		);
   		$idsTypeMapper[] = $idFiscalTributaria;
   		
   		$tarjetaExtranjeria = array(
   				'name' => 'idType',
   				'idSource' => '8',
   				'idTarget' => '5eeb377b-93d5-4fa8-9217-6ac68871a9ca',
   				'label' => 'tarjetaextranjeria'
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
    					//'name' => 'country.CTG',
    					'name' => 'country.MON',
    					'idSource' => $pais,
    					'idTarget' => $countrySF2['data'][0]['id']
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
    	
    	echo "\nTotal ciudades SF1: ".count($ciudadesSF1)."\n";
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
    					//'name' => 'town.CTG',
    					'name' => 'town.MON',
    					'idSource' => $ciudad,
    					'idTarget' => $townSF2['data'][0]['id']
    			);
    		
    			$urlapiMapper = $this->server.'admin/sifinca/mapper';
    			$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    		
    			$apiMapper->post($mapper);
    			
    			$total++;
    			
    			echo "\nVamos por: ".$total;
    		}else{
    			//echo "No hay coincidencia\n";
    			$sinCoincidencia[] = $ciudad;
    		}

    	}
    
    	
    	echo "Ciudades SF1: ".count($ciudadesSF1)."\n";
    	echo "Ciudades mapeados: ".$total."\n";	
    	echo "Ciudades no mapeados: ".count($sinCoincidencia)."\n";
    	
    	print_r($sinCoincidencia);
    	
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
    
    function mapperTipoDeContribuyente($cb) {
    	
    	/*TIPO_CONTRIBUYENTE	GA	Gran Contribuyente - AutoRetenedor
	    	TIPO_CONTRIBUYENTE	RA	R�gimen Com�n - Autoretenedor
	    	TIPO_CONTRIBUYENTE	GN	Gran Contribuyente - No Autoretenedor
	    	TIPO_CONTRIBUYENTE	RN	R�gimen Com�n - No Autoretenedor
	    	TIPO_CONTRIBUYENTE	RS	R�gimen Simplificado
	    	TIPO_CONTRIBUYENTE	CE	Cliente Extranjero
	    	TIPO_CONTRIBUYENTE	EX	Exento retenci�n (Sin animo de lucro)
    	*/
    	$fileJson = file_get_contents($this->serverRoot."upload/src/upload/data/mapperContribuyente.json");
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
    
    function buildClients($clients) {
    	
    	echo "\nConstruir cliente\n";
    	
    	
    	$clientErrors = array(); 
    	
    	$total = 0;
    	
    	$totalClients = count($clients);
    	//$totalClients = 10;

    	echo "\n".$totalClients."\n";
    	
    	$porDonde = 0;
    	

    	$startTime= new \DateTime();
    	
    	for ($i = 0; $i < $totalClients; $i++) {
    		    		
    		$client = $clients[$i];
			//print_r($client);
    		
    		$clientType = null;
    		
    		//echo "\nTipo de cliente: ".$client['nat_juridica'];
    		
    		//searchClientSF2
    		$clientSF2 = $this->searchClientSF2($client['id_cliente']);
    		
    		
    		
    		if(is_null($clientSF2)){
    			
    			if($client['nat_juridica'] == 'N'){
    				$clientType = 2;
    			
    				 
    				 
    				//$result = $this->buildClintePersona($client);
    				 
    				$urlapiClient = $this->server.'crm/main/clientperson';
    			
    				 
    				$apiClient = $this->SetupApi($urlapiClient, $this->user, $this->pass);
    				 
    				//echo "\nAHORA SI\n";
    				 
    				 
    				$bClient = $this->buildClintePersona($client);
    			
    				$result = $apiClient->post($bClient);
    				 
    				$result = json_decode($result, true);
    				 
    				 
    				 
    				if(isset($result['success'])){
    					if($result['success'] == true){
    						echo "\nOk\n";
    						$total++;
    					}
    				}else{
    					echo "\nError 1\n";

    					//print_r($result['message']);
    					
    					$urlapiMapper = $this->server.'crm/main/errorclient';
    					$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    			
    					$error = array(
    							'client' => $client['id_cliente'],
    							'objectJson' => json_encode($bClient),
    							'error' => $result['message']
    					);
    			
    					$apiMapper->post($error);
    			
    				}
    				 
    				 
    				 
    			}else if($client['nat_juridica'] == 'J'){
    				echo "\nCliente juridico\n";
    				$clientType = 3;
    				 
    				 
    				$urlapiClient = $this->server.'crm/main/clientcompany';
    				//echo "\n".$urlapiClient."\n";
    				 
    				$apiClient = $this->SetupApi($urlapiClient, $this->user, $this->pass);
    				 
    				//print_r($apiClient);
    				 
    				$bClient = $this->buildClintePersonaJuridica($client);
    			
    				$json = json_encode($bClient);
    				//echo "\n".$json."\n";
    				 
    				$result = $apiClient->post($bClient);
    			
    				$result = json_decode($result, true);
    			
    				//print_r($result);
    				 
    				if(isset($result['success'])){
    					if($result['success'] == true){
    						echo "\nOk\n";
    						$total++;
    					}
    				}else{
    					echo "\nError cliente juridico\n";
    					///echo $result;
    			
    					//     				$clientError = array(
    					//     						'id' => $client['id_cliente'],
    					//     						'name' => $client['nombre']
    					//     				);
    			
    					//     				$clientErrors[] = $clientError;
    			
    					//print_r($result);
    			
    					$urlapiMapper = $this->server.'crm/main/errorclient';
    					$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    			
    			
    					$msj = null;
    					if(isset($result['message'])){
    						$msj = $result['message'];
    					}
    			
    					$error = array(
    							'client' => $client['id_cliente'],
    							'type' => 'J',
    							'objectJson' => json_encode($bClient),
    							'error' => $msj
    					);
    						
    					$apiMapper->post($error);
    			
    				}
    			}
    			
    			
    			
    			
    			
    			
    		}else{
    			
    			echo "\nEl cliente ya existe: ".$client['id_cliente']."\n";
    		}
    		
    		$porDonde++;
    		
    		echo "\nVamos por: ".$porDonde."\n";
    	}
    	
    	
    	
    	$finalTime = new \DateTime();
    	 
    	 
    	$diff = $startTime->diff($finalTime);
    	 
    	 
    	echo "\n\n Fecha inicial: ".$startTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Fecha final: ".$finalTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
    	 
    	
    	echo "\nTotal de clintes creados en SF2: ".$total."\n";
//     	echo "\nTotal de clintes con error en SF2: ".count($clientErrors)."\n";
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
    			'number' => $this->cleanString($client['id_cliente']),
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
    	$maritalStatus = null;
    	if($maritalStatusMapper['total'] > 0){
    		$maritalStatus = $maritalStatusMapper['data']['0']['idTarget'];
    		if(!is_null($maritalStatus)){
    		
    			//echo "maritalStatus".$maritalStatus;
    		
    			$maritalStatus = array('id'=>$maritalStatus);
    		
    			if($maritalStatus == 0){
    				$maritalStatus = null;
    			}
    		}
    	}
    		
    	 
    	//echo "maritalStatus".$maritalStatus;

    	//----aad
    	$urlapiLevelStudy = $this->server.'admin/sifinca/mapper/levelStudy/'.$client['nivel_estudios'];
    	$apiLevelStudy = $this->SetupApi($urlapiLevelStudy, $this->user, $this->pass);
    	
    	$levelStudyMapper = $apiLevelStudy->get();
    	$levelStudyMapper = json_decode($levelStudyMapper, true);
    	//print_r($maritalStatusMapper);
    	$levelStudy = null;
    	if(isset($levelStudyMapper['total'])){
    		if($levelStudyMapper['total'] > 0){
    			$levelStudy = $levelStudyMapper['data']['0']['idTarget'];
    			if(!is_null($levelStudy)){
    		
    				$levelStudy = array('id'=>$levelStudy);
    		
    				if(isset($levelStudy['total'])){
    					if($levelStudy['total'] == 0){
    						$levelStudy = null;
    					}
    				}else{
    					$levelStudy = null;
    				}
    				 
    		
    			}
    		}
    	}else{
    		$levelStudy = null;
    	}
    	
    	
    	
    	
    	$email = null;
    	
    	if(!empty($client['e_mail'])){
    		
    		$trimmed = trim($client['e_mail'], " ");
    		
    		$lenght = strlen($trimmed);
    		
    		if($lenght > 0){
    			$email = $client['e_mail'];
    		}else{
    			$email = null;
    		}
    		    		
    	}
    	
    	
    	$contribuyente = $this->buildTipoContribuyente($client);
    	
    	$explodeName = explode(" ", $client['nombre']);
    	$explodeApellido = explode(" ", $client['apellido']);
    	
    	$firstname = $client['nombre'];
    	$lastname = $client['apellido'];
    	
    	$secondname = '';
    	$secondLastname = '';
    	
    	
    	if(count($explodeName) > 0){
    		$firstname = $explodeName[0];
    		if(isset($explodeName[1])){
    			$secondname = $explodeName[1];
    		}
    		
    	}
    	
    	if(count($explodeApellido) > 0){
    		$lastname = $explodeApellido[0];
    		if(isset($explodeApellido[1])){
    			$secondLastname = $explodeApellido[1];
    		}
    		
    	}
    	
    	$bClient = array(
    			'firstname' => $firstname,
    			'secondname' => $secondname,
    			'lastname' => $lastname,
    			"secondLastname" => $secondLastname,
    			//'client_type' => $clientType,
    			'identity' => $identity,
    			'adress' => $direcciones,
    			'phones' => $telefonos,
    			'civilState' => $maritalStatus,
    			'email' => $email,
    			'profession' => $levelStudy,
    			'contributor' => $contribuyente
    	);
    	
    	
//     	//print_r($bClient);
    	
//     	$json = json_encode($bClient);
//     	//echo "\n".$json."\n";
    	
//     	$result = $apiClient->post($bClient);
    	

//     	return $result;
    	
    	return $bClient;
    }
    
    function buildClintePersonaJuridica($client) {
    	 
    	$urlapiClient = $this->server.'crm/main/clientcompany';
    	 
    	//echo "\n".$urlapiClient."\n";
    	
    	$apiClient = $this->SetupApi($urlapiClient, $this->user, $this->pass);
    
    	$urlapiMapper = $this->server.'admin/sifinca/mapper/idType/'.$client['id_identificacion'];
    	$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    
    	$idTypeMapper = $apiMapper->get();
    	$idTypeMapper = json_decode($idTypeMapper, true);
    	//     		print_r($idTypeMapper);
    	 
    	//     		print_r($idTypeMapper["total"]);
    	 
    	$idType = $idTypeMapper['data']['0']['idTarget'];
    
    
    	$identity = array(
    			'number' => $this->cleanString($client['id_cliente']),
    			'idType' => array('id'=>$idType)
    	);
    	 
    	//$company = array('id'=>'19dc5e46-ec2a-4b09-a671-f18c2d90b48c');
    	 
    	$direcciones = $this->buildDirecciones($client);
    	 
    	$telefonos = $this->buildTelefonos($client);

    	$telefonosContacto = $this->buildTelefonosContacto($client);
    	if(count($telefonosContacto > 0)){
    		$contacto = array(
    				'name' => $client['nombre_cto'],
    				'phone' => $telefonosContacto,
    				'position' => 'N/A',
    				'contactType' => array('id'=> $this->contactTypeOther) //Otro
    		);
    	}else{
    		$contacto = null;
    	}
    	
    	
    	$representateLegal = $this->buildRepresentanteLegal($client);
    	
    	$contribuyente = $this->buildTipoContribuyente($client);
    	
    	$bClient = array(
    			'identity' => $identity,
    			'name' => $client['nombre'],
    			'comercialName' => $this->cleanString($client['nombre']),
    			'socialReason' => $this->cleanString($client['nombre']),
    			'contributor' => array(),
    			'adress' => $direcciones,
    			'phones' => $telefonos,
    			'contact' => array($contacto),
    			'legalRepresentative' => $representateLegal,
    			'contributor' => $contribuyente,
    			'client_type' => 3
    	);
    	 
    	//print_r($bClient);
    	 
//     	$json = json_encode($bClient);
//     	//echo "\n".$json."\n";
    	 
//     	$result = $apiClient->post($bClient);
    	
//     	if(isset($result['message'])){
//     		if($result['code'] == 500){
//     			$result = $apiClient->put()
//     		}
//     	}
    	
    	//print($result);
    	
    	//echo "\nCliente creado\n";
    	 
    	//return $result;
    	
    	return $bClient;
    }
     
    function buildDirecciones($client) {
    	 
    	$direccionesSF1 = array();
    	 
    	$dirResidencia = null;
    	$dirTrabajo = null;
    	 
    	
    	
    	if($client['dir_residencia']){
    
    
    		$address = $client['dir_residencia'].", ".$client['barrio_residencia'].", ".$client['edificio_residencia'];
    
    		//$urlapiMapperCountry = $this->server.'admin/sifinca/mapper/country.BOG/'.$client['pais_residencia'];
    		$urlapiMapperCountry = $this->server.'admin/sifinca/mapper/country.MON/'.$client['pais_residencia'];
    		$apiMapper = $this->SetupApi($urlapiMapperCountry, $this->user, $this->pass);
    		 
    		$country = $apiMapper->get();
    		$country = json_decode($country, true);
    
    		//$urlapiMapperTown = $this->server.'admin/sifinca/mapper/town.BOG/'.$client['ciudad_residencia'];
    		$urlapiMapperTown = $this->server.'admin/sifinca/mapper/town.MON/'.$client['ciudad_residencia'];
    		$apiMapper = $this->SetupApi($urlapiMapperTown, $this->user, $this->pass);
    		 
    		$town = $apiMapper->get();
    		$town = json_decode($town, true);
    
    		if($country['total'] == 1){
    			 
    			if($town['total'] == 1){
    				$town = array('id' => $town['data'][0]['idTarget']);
    				
    				$urlTownSF2 = $this->server.'crm/main/town/'.$town['id'];
    				
    				//echo "\n".$urlTownSF2."\n";
    				
    				$apiTownSF2 = $this->SetupApi($urlTownSF2, $this->user, $this->pass);
    				 
    				$townSF2 = $apiTownSF2->get();
    				$townSF2 = json_decode($townSF2, true);
    				
    				$deparment = array('id' => $townSF2['department']['id']);
    				
    			}else{
    				$town = null;
    				$deparment = null;
    			}
    			
    			
    			
    			if(strlen($address) > 7 ){
    				$dirResidencia = array(
    						'address' => $address,
    						'country' => array('id' => $country['data'][0]['idTarget']),
    						'department' => $deparment,
    						'town' => $town,
    						'district' => null,
    						'typeAddress' => array('id'=>$this->typeAddressHome) //CASA
    				);
    			}    			
    		}
    	}
    	 
    	//print_r($dirResidencia);
    	
    	if($client['dir_trabajo']){
    
    
    		$address = $client['dir_trabajo'].", ".$client['barrio_trabajo'].", ".$client['edificio_trabajo'];
    
    		$urlapiMapperCountry = $this->server.'admin/sifinca/mapper/country.MON/'.$client['pais_trabaja'];
    		$apiMapper = $this->SetupApi($urlapiMapperCountry, $this->user, $this->pass);
    		 
    		$country = $apiMapper->get();
    		$country = json_decode($country, true);
    
    		$urlapiMapperTown = $this->server.'admin/sifinca/mapper/town.MON/'.$client['ciudad_trabaja'];
    		$apiMapper = $this->SetupApi($urlapiMapperTown, $this->user, $this->pass);
    		 
    		$town = $apiMapper->get();
    		$town = json_decode($town, true);
    
    		if(isset($country['total'])){
    			
    			if($country['total'] == 1){
    			
    				if(isset($town['total'])){
    					
    					if($town['total'] == 1){
    						$town = array('id' => $town['data'][0]['idTarget']);
    					
    						$urlTownSF2 = $this->server.'crm/main/town/'.$town['id'];
    					
    						//echo "\n".$urlTownSF2."\n";
    					
    						$apiTownSF2 = $this->SetupApi($urlTownSF2, $this->user, $this->pass);
    					
    						$townSF2 = $apiTownSF2->get();
    						$townSF2 = json_decode($townSF2, true);
    					
    						$deparment = array('id' => $townSF2['department']['id']);
    					
    					}else{
    						$town = null;
    						$deparment = null;
    					}
    					
    				}else{
    					$town = null;
    					$deparment = null;
    				}
    					
    						
    				if(strlen($address) > 7){
    					$dirTrabajo = array(
    						'address' => $address,
    						'country' => array('id' => $country['data'][0]['idTarget']),
    						'department' => $deparment,
    						'town' => $town,
    						'district' => null,
    						'typeAddress' => array('id'=>$this->typeAddresOficina) //OFICINA
    					);
    				}
    					
    				
    				 
    			}
    			
    		}else{

    			if(isset($town['total'])){
    				if($town['total'] == 1){
    					$town = array('id' => $town['data'][0]['idTarget']);
    					 
    					$urlTownSF2 = $this->server.'crm/main/town/'.$town['id'];
    					 
    					//echo "\n".$urlTownSF2."\n";
    					 
    					$apiTownSF2 = $this->SetupApi($urlTownSF2, $this->user, $this->pass);
    					 
    					$townSF2 = $apiTownSF2->get();
    					$townSF2 = json_decode($townSF2, true);
    					 
    					$deparment = array('id' => $townSF2['department']['id']);
    					 
    				}else{
    					$town = null;
    					$deparment = null;
    				}
    			}else{
    					$town = null;
    					$deparment = null;
    			}
    			
    				
    			if(strlen($address) > 7){
    				$dirTrabajo = array(
    						'address' => $address,
    						'country' => array('id' => $this->colombia),
    						'department' => null,
    						'town' => $town,
    						'district' => null,
    						'typeAddress' => array('id'=>$this->typeAddresOficina) //OFICINA
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
    	
    	$telefono_celular = $client['TEL_CELULAR'];
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
    
    function buildTelefonosContacto($client) {
    	
    	//echo "Entro aqui";
    	$telefonoContacto1 = $client['TEL_CO'];
    	$telefonoContacto1 = $this->validarTelefono($telefonoContacto1);
    	 
    	$telefonoContacto2 = $client['tel_co2'];
    	$telefonoContacto2 = $this->validarTelefono($telefonoContacto2);
    	 
    	$telefonoContacto3 = $client['tel_cto'];
    	$telefonoContacto3 = $this->validarTelefono($telefonoContacto3);
    	
//     	$telefonoContacto4 = $client['teL_celular_cto'];
//     	$telefonoContacto4 = $this->validarTelefono($telefonoContacto4);
    	    	 
    	$telefonos = array();
    	 
    	if(!is_null($telefonoContacto1)){
    		$telefonos[] = $telefonoContacto1;
    	}
    	 
    	if(!is_null($telefonoContacto2)){
    		$telefonos[] = $telefonoContacto2;
    	}
       
    	if(!is_null($telefonoContacto3)){
    		$telefonos[] = $telefonoContacto3;
    	}
    	
//     	if(!is_null($telefonoContacto4)){
//     		$telefonos[] = $telefonoContacto4;
//     	}
    	
    	//$telefonos = array_unique($telefonos);
    	 
    	return $telefonos;
    }
            
    function validarTelefono($telefono) {
    	
    	$phone = null;
    	
    	//echo "\nTelefono: ".$telefono."\n";
    	
    	if(!is_null($telefono) && (strlen($telefono) > 1) && (strlen($telefono) < 20)){
    		
    		
    		$telefono = preg_replace("[\s+]", "", $telefono); //Quitar espacios en blanco
//     		echo "\nTelefono: ".$telefono."\n";
//     		echo "\n".strlen($telefono);
    		
    		if(strlen($telefono) >= 10){
    			$phone = array(
    					'number' => $telefono,
    					'phoneType' => array('id' => '0b2981d1-f038-4391-9258-015f95b2bf0f'), //Id phoneType SF2, movil
    					'country' => array('id' => $this->colombia)
    					
    			);
    		}
    		
    		if( (strlen($telefono) > 0) && (strlen($telefono) < 10) ){
    			$phone = array(
    					'number' => $telefono,
    					'phoneType' => array('id' => '2f49f417-9db1-4cb6-98c6-7f7f6af21399'), //Id phoneType SF2, fijo
    					'country' => array('id' => $this->colombia)
    					
    			);
    		}
    	}
    	
    	return $phone;
    }
    
    function buildRepresentanteLegal($client) {
    	
    	$representateLegal = null;
    	    	
//     	echo "\nId representante".$client['id_representante'];
//     	echo "\nrepresentante".$client['representante_legal'];
    	
//     	echo "\n".strlen($client['id_representante']);
//     	echo "\n".strlen($client['representante_legal']);
    	
    	//if(($client['representante_legal'] != '') && ($client['id_representante'] != '')){
    	
    	if(($client['representante_legal'] != '') && ($client['id_representante'] != '')){
    		
    		
    		if($client['id_representante'] != 0){
    			
//     			echo "\n".$client['id_representante']."\n";
    			$clientSF2 = $this->searchPersonSF2($client['id_representante']);
    			
    			
    			
    			if(is_null($clientSF2)){
    				//echo "\nentro aqui";
    				$name = explode(" ", $client['representante_legal']);
    				//print_r($name);
    				
    				$representateLegal = array(
    						'firstname' => $client['representante_legal'],
    						'lastname' => $client['representante_legal'],
    						'identity' => array(
    								'number' => $client['id_representante'],
    								'idType' => array(
    										'id' => $this->idTypeCedula
    								)
    						)
    				);
    				 
    				if(count($name) == 2){
    					$representateLegal = array(
    							'firstname' => $name[0],
    							'lastname' => $name[1],
    							'identity' => array(
    									'number' => $client['id_representante'],
    									'idType' => array(
    											'id' => $this->idTypeCedula
    									)
    							)
    					);
    				}
    				 
    				if(count($name) == 4){
    					$representateLegal = array(
    							'firstname' => $name[0],
    							'secondName' => $name[1],
    							'lastname' => $name[2],
    							'secondLastname' => $name[3],
    							'identity' => array(
    									'number' => $client['id_representante'],
    									'idType' => array(
    											'id' => $this->idTypeCedula
    									)
    							)
    					);
    				}
    				
    			}else{
    				$representateLegal = array('id'=> $clientSF2['id']);
    			}
    			
    		}
    	}
    	
    	return $representateLegal;
    }
    
    /*
     TIPO_CONTRIBUYENTE	GA	Gran Contribuyente - AutoRetenedor
     TIPO_CONTRIBUYENTE	RA	R�gimen Com�n - Autoretenedor
     TIPO_CONTRIBUYENTE	GN	Gran Contribuyente - No Autoretenedor
     TIPO_CONTRIBUYENTE	RN	R�gimen Com�n - No Autoretenedor
     TIPO_CONTRIBUYENTE	RS	R�gimen Simplificado
     TIPO_CONTRIBUYENTE	CE	Cliente Extranjero
     TIPO_CONTRIBUYENTE	EX	Exento retenci�n (Sin animo de lucro)
     */  
    // array('gran_contribuyente'=>false, 'regimen_iva'=>'SIMPLIFICADO','retenedor'=>false,'exento_retencion'=>true,'autoretenedor'=>true)
    
    public function  buildTipoContribuyente($cliente){
    
    	## Analizar parametros
    	if($cliente['gran_contribuyente']){
    		if($cliente['autoretenedor']){
    			$type ="GA";
    		}else{
    			$type ="GN";
    		}
    	}else{   
    		if($cliente['regimen_iva']=='COMUN'){
    			if($cliente['autoretenedor']){
    				$type ="RA";
    			}else{
    				$type ="RN";
    			}
    		}else{
    			 $type = 'RS';
    		}
    	}
    
    
    	if($cliente['exento_retencion']){
    		$type = "EX";
    	}
    
    	
    	//echo "\nContribuyente SF1: ".$type."\n";
    
    	$urlapiMapperContributor = $this->server.'admin/sifinca/mapper/contributor/'.$type;
    	$apiMapperContributor = $this->SetupApi($urlapiMapperContributor, $this->user, $this->pass);
    	
    	//echo "\n".$urlapiMapperContributor;
    	
    	$contributorMapper = $apiMapperContributor->get();
    	$contributorMapper = json_decode($contributorMapper, true);
    	//print_r($contributorMapper);
    	
    	//     		print_r($idTypeMapper["total"]);
//     	print_r($contributorMapper);
    	
    	
//     	echo "\ncontribuyente: ".$contributor;
    	$contributor = null;
    	
    	if($contributorMapper['total'] > 0){
    		$contributor = array('id' => $contributorMapper['data']['0']['idTarget']);
    	}
    	
    
    	//echo "\nContribuyente SF2: ".$contributor;
    	
    	return $contributor;
    
    }
    
    
    public function banks($cb){
    	
    	$urlapiBank = $this->server.'admin/accounts/bank';
    	 
    	$apiBank = $this->SetupApi($urlapiBank, $this->user, $this->pass);
    	 
    	$bancos = $cb->getBancos();
    	
    	foreach ($bancos as $banco) {
    		
    		$address = array();
    		$address[] = array(
    			'address' => $banco['direccion'],
    			'country' => array('id' => $this->colombia),
    			'department' => array('id' => $this->bolivar),
    			'town' => array('id' => $this->cartagena),
    			'district' => null,
    			'typeAddress' => array('id'=>$this->typeAddressCorrespondencia)
    		);
    		
    		$phones = array();
    		$phones[] = array(
    			'number' => $banco['tel_enca'],
    			'phoneType' => array('id' => '2f49f417-9db1-4cb6-98c6-7f7f6af21399'), //Id phoneType SF2, fijo
    			'country' => array('id' => $this->colombia)
    		);
    		
    		$bb = array(
    			'code' => $banco['id_banco'],
    			'name' => $banco['nombre'],
    			//'nit' => $banco[]
    			'address' => $address,
    			'phones' =>$phones
    		);
    		
    		
    		//print_r($bb);
    		
    		$json = json_encode($bb);
    		
    		
    		
    		$result = $apiBank->post($bb);
    		 
    		$result = json_decode($result, true);
    		    		 
    		 
    		if(isset($result['success'])){
    			if($result['success'] == true){
    				echo "\nOk\n";
    				$total++;
    			}
    		}else{
    			echo "\nError\n";
    			
    			echo "\n".$json."\n";
    			
    		}
    		
    		}
    }
    
    
    function searchClientSF2($identificacion){
    
    	//     	echo "\nidentificacion\n";
    	//     	echo $identificacion."\n";
    	 
    	$identificacion = $this->cleanString($identificacion);
    	    	    	 
    	$filter = array(
    			'value' => $identificacion,
    			'operator' => 'equal',
    			'property' => 'identity.number'
    	);
    	$filter = json_encode(array($filter));
    
    	 
    	$urlClientSF2 = $this->server.'crm/main/client?filter='.$filter;
    	
    	//echo "\n".$urlClientSF2."\n";
    
    	//$urlClientSF2 = $this->server.'crm/main/zero/client';
    	 
    	$apiClientSF2 = $this->SetupApi($urlClientSF2, $this->user, $this->pass);
    	 
    
    	$clientSF2 = $apiClientSF2->get();
    	//      	echo "\nbeneficiario\n";
    	//  		echo $clientSF2;
    
    	$clientSF2 = json_decode($clientSF2, true);
    	 
    	 
    	if($clientSF2['total'] > 0){
    	
    		return $clientSF2['data'][0];
    
    	}else{
    		return null;
    	}
    
    	 
    }
    
    
    function searchPersonSF2($identificacion){
    
    	//     	echo "\nidentificacion\n";
    	//     	echo $identificacion."\n";
    
    	$identificacion = $this->cleanString($identificacion);
    
    	$filter = array(
    			'value' => $identificacion,
    			'operator' => 'has',
    			'property' => 'identity.number'
    	);
    	$filter = json_encode(array($filter));
    
    
    	$urlClientSF2 = $this->server.'crm/main/person?filter='.$filter;
    	//echo "\n".$urlClientSF2."\n";
    
    	//$urlClientSF2 = $this->server.'crm/main/zero/client';
    
    	$apiClientSF2 = $this->SetupApi($urlClientSF2, $this->user, $this->pass);
    
    
    	$clientSF2 = $apiClientSF2->get();
    	//      	echo "\nbeneficiario\n";
    	//  		echo $clientSF2;
    
    	$clientSF2 = json_decode($clientSF2, true);
    
    
    	if($clientSF2['total'] > 0){
    		 
    		return $clientSF2['data'][0];
    
    	}else{
    		return null;
    	}
    
    
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
    	
    	//$this->token = '571e4ee48863c50c0e8b45a9';
    	
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