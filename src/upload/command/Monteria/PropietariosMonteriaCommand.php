<?php
namespace upload\command\Monteria;

use upload\model\conveniosCartagena;
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

class PropietariosMonteriaCommand extends Command
{	
	
	public $server = 'http://www.sifinca.net/monteriaServer/web/app.php/';
	public $serverRoot = 'http://www.sifinca.net';

	public $localServer = 'http://10.102.1.22/';
		
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
	
    protected function configure()
    {
        $this->setName('propietariosMonteria')
		             ->setDescription('Comando para pasar propietarios - Monteria');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $conn = new data(array(
            'server' =>'192.168.100.1'
            ,'user' =>'sa'
            ,'pass' =>'75080508360'
            ,'database' =>'sifinca' 
            ,'engine'=>'mssql'
        ));

        $conexion = new conveniosCartagena($conn);
             
        $this->crearPropietarios($conexion);
		
    }
     
    public function crearPropietarios($conexion) {
    	    	    	
//     	$urlConvenio = $this->server.'catchment/main/agreement/only/ids';
    	
//     	$apiConvenio = $this->SetupApi($urlConvenio, $this->user, $this->pass);
    	
//     	$convenios = $apiConvenio->get();
//     	$convenios = json_decode($convenios, true);
    	//echo $convenios['total']
    	
    	$convenios = $conexion->getSoloConveniosMonteria();
    	$totalConvenios = count($convenios);
    	
    	$urlapioOwner = $this->server.'catchment/main/owner';
    	
    	$apiOwner = $this->SetupApi($urlapioOwner, $this->user, $this->pass);
    	
    	echo "\nTotal de convenios - propietarios: ".$totalConvenios."\n";
    	
    	//$totalConvenios = count($convenios['data']);
    	
    	$porDonde = 0;
    	$startTime= new \DateTime();
    	
    	//foreach ($convenios['data'] as $convenio) {
    	for ($i = 0; $i < $totalConvenios; $i++) {
    		
    		//$convenio = $convenios['data'][$i];
    		$convenio = $convenios[$i];
    		
    		echo "\n---------------------------\n";
    		echo "\nConvenio: ".$convenio['id_convenio']."\n";
    		//$propietariosSF1 = $conexion->getPropietariosDelConvenio($convenio['consecutive']); 		
    		$propietariosSF1 = $conexion->getPropietariosDelConvenioMonteria($convenio['id_convenio']);
    		
    		$agreement = $this->searchConvenioSF2($convenio);
    		
    		if(!is_null($agreement)){
    			
    			foreach ($propietariosSF1 as $p) {
    			
    				$ownerIdentificacion = $p['id_cliente'];
    				$ownerIdentificacion = $this->cleanString($ownerIdentificacion);
    			
    				$owner = $conexion->getCliente($ownerIdentificacion);
    			
    				$clientSF1 = $owner;
    				 
    				$client = $this->searchClientSF2($ownerIdentificacion);
    				$clientSF2 = $client;
    			
    				if($client){
    			
    			
    					$ownerSF2 = $this->searchPropietarioPorConvenioYcliente($client, $convenio);
    			
    					if(is_null($ownerSF2)){
    			
    						echo "\nYa tenemos el cliente, creando propietario\n";
    							
    						$ownerType = $this->tipoPropietario($p['tipo_relacion']);
    			
    						$payForm = $this->formaDePago($conexion, $clientSF1, $clientSF2);
    			
    						//print_r($payForm);
    			
    						$bOwner = array(
    								'client' => array('id'=>$client['id'], 'name'=>$client['name']),
    								'ownerType' => $ownerType,
    								'percentageIncomeDivision' => $p['participacion'], //% Participacion de renta
    								'taxPercentage' => null, // % tributario
    								'payForm' => $payForm,
    								//'agreement' => array('id'=>$convenio['id'])
    								'agreement' => $agreement
    						);
    			
    			
    						$json = json_encode($bOwner);
    						//     		//echo "\n\n".$json."\n\n";
    							
    			
    						$result = $apiOwner->post($bOwner);
    						$result = json_decode($result, true);
    			
    						if(isset($result['success'])){
    							if($result['success'] == true){
    								echo "\nOk";
    								//$total++;
    									
    							}
    						}
    						else{
    			
    							if($result['message'] == 'Error el cliente ya se ecuentra en este convenio'){
    									
    								echo "\nPropietario ya existe\n";
    							}
    			
    							echo "\nError creando propietario 1\n";
    								
    							//echo "\n\n".$json."\n\n";
    			
    							$urlapiMapper = $this->server.'catchment/main/errorowner';
    							$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    			
    							$error = array(
    									'owner' => $client['id'],
    									'agreement'=> $convenio['consecutive'],
    									'objectJson' => $json
    							);
    			
    							$apiMapper->post($error);
    						}
    			
    			
    					}else{
    			
    						echo "\nEl propietario ya existe en este convenio\n";
    					}
    			
    				}else{
    			
    					echo "\nCreando nuevo cliente\n";
    			
    					//Crear cliente
    					$clienteBus = $this->buildClient($owner[0]);
    			
    					if(!is_null($clienteBus)){
    			
    						//echo "\nentro aqui 1\n";
    						$ownerType = $this->tipoPropietario($p['tipo_relacion']);
    			
    						$payForm = $this->formaDePago($conexion, $clientSF1, $clientSF2);
    			
    			
    						$bOwner = array(
    								'client' => array('id'=>$clienteBus),
    								'ownerType' => $ownerType,
    								'percentageIncomeDivision' => $p['participacion'], //% Participacion de renta
    								'taxPercentage' => null, // % tributario
    								'payForm' => $payForm
    						);
    			
    						//$json = json_encode($bOwner);
    						//     		//echo "\n\n".$json."\n\n";
    						 
    						$result = $apiOwner->post($bOwner);
    						$result = json_decode($result, true);
    							
    						if(isset($result['success'])){
    							if($result['success'] == true){
    								echo "\nOk";
    								//$total++;
    							}
    						}
    						else{
    								
    							echo "\nEl propietario ya existe\n";
    								
    			
    						}
    			
    					}
    				}
    			
    			}
    			
    		}
    		

    		
    		$porDonde++;
    		echo "\nVamos por: ".$porDonde."\n";
    	}
    	
    	$finalTime = new \DateTime();
    	
    	
    	$diff = $startTime->diff($finalTime);
    	
    	
    	echo "\n\n Fecha inicial: ".$startTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Fecha final: ".$finalTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
    	
    }
    
    /**	
     * Crear cliente
     * @param unknown $clients
     */
    function buildClient($client) {
    	 
    	//echo count($clients);
    	 
    	$clientErrors = array();
    	 
    
    	$clientType = null;
    
    		//echo "\nTipo de cliente: ".$client['nat_juridica'];
    
    	//print_r($client);
    		if($client['nat_juridica'] == 'N'){
    			$clientType = 2;
    
    			 
    			$urlapiClient = $this->server.'crm/main/clientperson';
    
    			 
    			$apiClient = $this->SetupApi($urlapiClient, $this->user, $this->pass);
    			 
    			 
    			$bClient = $this->buildClintePersona($client);
    
    			$result = $apiClient->post($bClient);
    			 
    			$result = json_decode($result, true);
    			 
    			 
    			 
    			if(isset($result['success'])){
    				if($result['success'] == true){
    					
    					return $client['id_cliente'];
    				}
    			}else{
    				echo "\nError creando cliente\n";
    					
    					
    				$urlapiMapper = $this->server.'crm/main/errorclient';
    				$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    
    				$msj = null;
    				if(isset($result['message'])){
    					$msj = $result['message'];
    				}
    				
    				$error = array(
    						'client' => $client['id_cliente'],
    						'objectJson' => json_encode($bClient),
    						'error' => $msj
    				);
    
    				$apiMapper->post($error);
    				
    				return null;
    
    			}
    			 
    			 
    			 
    		}else if($client['nat_juridica'] == 'J'){
    			//echo "\nCliente juridico\n";
    			$clientType = 3;
    			 
    			$urlapiClient = $this->server.'crm/main/clientcompany';
    			
    			
    			$apiClient = $this->SetupApi($urlapiClient, $this->user, $this->pass);
    			
    			
    			$bClient = $this->buildClintePersonaJuridica($client);
    			
    			$result = $apiClient->post($bClient);
    			
    			$result = json_decode($result, true);
    			 
    			//print_r($result);
    			 
    			if(isset($result['success'])){
    				if($result['success'] == true){
    					
    					return $client['id_cliente'];
    				}
    			}else{
    				echo "\nError cliente juridico\n";

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
    				
    				return null;
    
    			}
    		}
    

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
    	if($levelStudyMapper['total'] > 0){
    		$levelStudy = $levelStudyMapper['data']['0']['idTarget'];
    		if(!is_null($levelStudy)){
    
    			$levelStudy = array('id'=>$levelStudy);
    
    			if($levelStudy['total'] == 0){
    				$levelStudy = null;
    			}
    
    		}
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
    
    	echo "\n".$urlapiMapper."\n";
    	
    	$idTypeMapper = $apiMapper->get();
    	$idTypeMapper = json_decode($idTypeMapper, true);
    	//     		print_r($idTypeMapper);
    
    	//     		print_r($idTypeMapper["total"]);
    
    	print_r($idTypeMapper['data']['0']);
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
    			'comercialName' => $client['nombre'],
    			'socialReason' => $client['nombre'],
    			'contributor' => array(),
    			'adress' => $direcciones,
    			'phones' => $telefonos,
    			'contact' => array($contacto),
    			'legalRepresentativeClient' => $representateLegal,
    			'contributor' => $contribuyente
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
    
    		$urlapiMapperCountry = $this->server.'admin/sifinca/mapper/country.BOG/'.$client['pais_trabaja'];
    		$apiMapper = $this->SetupApi($urlapiMapperCountry, $this->user, $this->pass);
    		 
    		$country = $apiMapper->get();
    		$country = json_decode($country, true);
    
    		$urlapiMapperTown = $this->server.'admin/sifinca/mapper/town.BOG/'.$client['ciudad_trabaja'];
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
    							'typeAddress' => array('id'=>'26f76dc7-4204-4972-9d47-30360735514b') //OFICINA
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
    	 
    	$telefonoContacto4 = $client['tel_celular_cto'];
    	$telefonoContacto4 = $this->validarTelefono($telefonoContacto4);
    	 
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
    	 
    	if(!is_null($telefonoContacto4)){
    		$telefonos[] = $telefonoContacto4;
    	}
    	 
    	//$telefonos = array_unique($telefonos);
    
    	return $telefonos;
    }
    
    function validarTelefono($telefono) {
    	 
    	$phone = null;
    	 
    	//echo "\nTelefono: ".$telefono."\n";
    	 
    	if(!is_null($telefono) && (strlen($telefono) > 1)){
    
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
    			$clientSF2 = $this->searchClientSF2($client['id_representante']);
    			 
    			 
    			 
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
    
    // array('gran_contribuyente'=>false, 'regimen_iva'=>'SIMPLIFICADO','retenedor'=>false,'exento_retencion'=>true,'autoretenedor'=>true)
    /**	
     *  TIPO_CONTRIBUYENTE	GA	Gran Contribuyente - AutoRetenedor
     *	TIPO_CONTRIBUYENTE	RA	RŽgimen Comœn - Autoretenedor
     *	TIPO_CONTRIBUYENTE	GN	Gran Contribuyente - No Autoretenedor
     *	TIPO_CONTRIBUYENTE	RN	RŽgimen Comœn - No Autoretenedor
     *	TIPO_CONTRIBUYENTE	RS	RŽgimen Simplificado
     *	TIPO_CONTRIBUYENTE	CE	Cliente Extranjero
     *	TIPO_CONTRIBUYENTE	EX	Exento retenci—n (Sin animo de lucro)
     * @param unknown $cliente
     * @return Ambigous <NULL, multitype:NULL >
     */
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
         
    function tipoPropietario($tipo) {
    	
    	$ownerType = null;
    	
    	//Copropietario
    	if($tipo == 'C'){
    		$ownerType = array('id'=>'6f73328c-244a-4d17-a875-018aad7dfc72');
    	}
    	//Principal
    	if($tipo == 'P'){
    		$ownerType = array('id'=>'c21a7d71-de51-4fa4-91e4-2940444466fc');
    	}
    	
    	return $ownerType;
    }
    
    function formaDePago($conexion, $clientSF1, $clientSF2) {
    	
//     	$beneficiary = $this->searchClientSF2($owner['id_cliente']);
    	 
//     	//echo $beneficiary['id'];
//     	//     		return;
//     	$clienteSF1 = $this->searchClienteSF1($conexion, $owner['id_cliente']);
     	$clientSF1 = $clientSF1[0];

     	$beneficiary = null;
     	
     	
     	
     	if($clientSF1['doc_tercero'] == 0){
     		$beneficiary = $clientSF2;
     	}else{
     		
     		$beneficiary = $this->searchClientSF2($clientSF1['doc_tercero']);
     		
     		if(is_null($beneficiary)){
     			$beneficiary = $clientSF2;
     		}
     		
     	}
     
    	$bank = $this->searchBank($clientSF1['id_banco']);
    	 
//     	echo "banco";
//     	print_r($bank);
    	$payType = $this->searchPayType($clientSF1['pagos']);
    	 
    	$accountType = $this->searchAccountType($clientSF1['tipo_cuenta']);
    	 
    	if(!is_null($beneficiary)){
    		$beneficiary = array('id'=>$beneficiary['id']);
    	}
    	if(is_null($bank)){
    		$payForm = array(
    				'payType' => array('id'=>$payType['id']),
    				'bank' => $bank,
    				'accountType' => $accountType,
    				'accountNumber' => $this->cleanString($clientSF1['no_cuenta']),
    				'beneficiary' => $beneficiary
    		);
    	}else{
    		$payForm = array(
    				'payType' => array('id'=>$payType['id']),
    				'bank' => array('id'=>$bank['id']),
    				'accountType' => $accountType,
    				'accountNumber' => $this->cleanString($clientSF1['no_cuenta']),
    				'beneficiary' => $beneficiary
    		);
    	}
    	
    	
    	return $payForm;
    	
    }       

    function searchClienteSF1($conexion, $id){
    	 
    	$clienteSF1 = $conexion->getCliente($id);
    	 
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
        
    function searchTipoContratoMandato($convenio) {
    	
    	$typeContract = null;

    	if($convenio['por_seguro'] > 0){
    		//Con garantia
    		$typeContract = array('id' => '53eee515-a699-498b-a65f-16336660c350');
    	}else{
    		//Sin garantia
    		$typeContract = array('id' => '54530e43-abfc-44e2-a98f-b9fcf51003fa');
    	}
    	
//     	//Arriendo sin garantia
//     	if($convenio['aseg'] = 0){
//     		$typeContract = array('id' => '54530e43-abfc-44e2-a98f-b9fcf51003fa');
//     	}
    	 
//     	//Arriendo con garantia
//     	if($convenio['aseg'] = 1){
//     		$typeContract = array('id' => '53eee515-a699-498b-a65f-16336660c350');
//     	}
    	
    	return $typeContract;
    	
    }
    
    function searchConvenioSF2($convenio) {
    	
    	
    	$filter = array(
    			'value' => $this->cleanString($convenio['id_convenio']),
    			'operator' => '=',
    			'property' => 'consecutive'
    	);
    	$filter = json_encode(array($filter));
    	
    	$urlAgreement = $this->server.'catchment/main/agreement?filter='.$filter;
    	
    	$apiAgreement = $this->SetupApi($urlAgreement, $this->user, $this->pass);
    	
    	$agreement = $apiAgreement->get();
    	$agreement = json_decode($agreement, true);
    	
    	
    	if($agreement['total'] > 0){
    		 
    		return $agreement['data'][0];
    		 
    	}else{
    		return null;
    	}
    	
    }
        
    function searchMandateContract($convenio) {
    	 
    	$filter = array(
    			'value' => $this->cleanString($convenio['id_convenio']),
    			'operator' => '=',
    			'property' => 'convenioSifincaOne'
    	);
    	$filter = json_encode(array($filter));
    	 
    	$urlMandateContract = $this->server.'catchment/main/mandatecontract?filter='.$filter;
    	//echo "\n".$urlMandateContract."\n";
    	$apiMandateContract = $this->SetupApi($urlMandateContract, $this->user, $this->pass);
    	 
    	$mandateContract = $apiMandateContract->get();
    	$mandateContract = json_decode($mandateContract, true);
    	 
    	if($mandateContract['total'] > 0){
    		 
    		return $mandateContract['data'];
    		 
    	}else{
    		return null;
    	}
    	 
    }
    
    function searchMandateContractPorConvenioEinmueble($convenio) {
    
    	$filter = array();
    	
    	$filter[] = array(
    			'value' => $this->cleanString($convenio['id_convenio']),
    			'operator' => '=',
    			'property' => 'convenioSifincaOne'
    	);
    	
    	$filter[] = array(
    			'value' => $this->cleanString($convenio['id_inmueble']),
    			'operator' => '=',
    			'property' => 'property.consecutive'
    	);
    	
    	//print_r($filter);
    	
    	$filter = json_encode(array($filter));
    
    	$urlMandateContract = $this->server.'catchment/main/mandatecontract?filter='.$filter;
    	//echo "\n".$urlMandateContract."\n";
    	$apiMandateContract = $this->SetupApi($urlMandateContract, $this->user, $this->pass);
    
    	$mandateContract = $apiMandateContract->get();
    	$mandateContract = json_decode($mandateContract, true);
    
    	if($mandateContract['total'] > 0){
    		 
    		return $mandateContract['data'];
    		 
    	}else{
    		return null;
    	}
    
    }
        
    function searchMandateContractByProperty($convenio) {
    
    	$filter = array(
    			'value' => $this->cleanString($convenio['id_inmueble']),
    			'operator' => '=',
    			'property' => 'property.consecutive'
    	);
    	$filter = json_encode(array($filter));
    
    	$urlMandateContract = $this->server.'catchment/main/mandatecontract?filter='.$filter;
    	echo "\n".$urlMandateContract."\n";
    	
    	$apiMandateContract = $this->SetupApi($urlMandateContract, $this->user, $this->pass);
    
    	$mandateContract = $apiMandateContract->get();
    	$mandateContract = json_decode($mandateContract, true);
    
    	if($mandateContract['total'] > 0){
    		 
    		return $mandateContract['data'];
    		 
    	}else{
    		return null;
    	}
    
    }
        
    function searchProperty($propertySF1) {
    	
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
    		 
    		return $property['data'][0];
    		 
    	}else{
    		return null;
    	}
    	
    }
    
    function searchUsuario($convenio) {
    
    	$usuarioSF1 = $convenio['emailCatcher'];
    	$usuarioSF1 = $this->cleanString($usuarioSF1);
    	
    	if($usuarioSF1 == 'inmobiliaria@araujoysegovia.com'){
    		$user = array('id' => 203);
    		return $user;
    	}
    	
    	if($usuarioSF1 == 'hherrera@araujoysegovia.com'){
    		$user = array('id' => 203);
    		return $user;
    	}
    	
    	
    	$filter = array(
    			'value' => $this->cleanString($convenio['emailCatcher']),
    			'operator' => '=',
    			'property' => 'email'
    	);
    	$filter = json_encode(array($filter));
    	 
    	$urlUser = $this->server.'admin/security/user?filter='.$filter;
    	
    	$apiUser = $this->SetupApi($urlUser, $this->user, $this->pass);
    	 
    	$user = $apiUser->get();
    	$user = json_decode($user, true);
    	 
    	 
    	if($user['total'] > 0){
    		 
    		return $user['data'][0];
    		 
    	}else{
    		return $user = array('id' => 203);
    	}
    	
    }
    
    function searchClientSF2($identificacion){
    
    	//     	echo "\nidentificacion\n";
    	//     	echo $identificacion."\n";
    
    	$identificacion = $this->cleanString($identificacion);
    
    	$filter = array(
    			'value' => $identificacion,
    			'operator' => 'has',
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
         
    function searchOwner($convenio) {
    	
    	$codigoConvenio = $this->cleanString($convenio['id_convenio']);
    	
    	$filter = array(
    			'value' => $codigoConvenio,
    			'operator' => 'equal',
    			'property' => 'agreement.consecutive'
    	);
    	$filter = json_encode(array($filter));
    	
    	
    	$urlClientSF2 = $this->server.'catchment/main/owner?filter='.$filter;
    	//echo "\n".$urlClientSF2."\n";
    	
    	//$urlClientSF2 = $this->server.'crm/main/zero/client';
    	
    	$apiClientSF2 = $this->SetupApi($urlClientSF2, $this->user, $this->pass);
    	
    	
    	$clientSF2 = $apiClientSF2->get();
    	//      	echo "\nbeneficiario\n";
    	//  		echo $clientSF2;
    	
    	$clientSF2 = json_decode($clientSF2, true);
    	
    	
    	if($clientSF2['total'] > 0){
    		 
    		return $clientSF2['data'];
    	
    	}else{
    		return null;
    	}
    	
    }
      
    function searchPropietarioPorConvenioYcliente($client, $convenio) {
    	
    	//print_r($convenio);
    	//$codigoConvenio = $this->cleanString($convenio['consecutive']);
    	$codigoConvenio = $this->cleanString($convenio['id_convenio']);
    	 
    	$filter = array();
    	
    	$filter[] = array(
    			'value' => $codigoConvenio,
    			'operator' => 'equal',
    			'property' => 'agreement.consecutive'
    	);
    	
    	$filter[] = array(
    			'value' => $client['id'],
    			'operator' => 'equal',
    			'property' => 'client.id'
    	);
    	$filter = json_encode($filter);
    	 
    	
    	 
    	$urlClientSF2 = $this->server.'catchment/main/owner?filter='.$filter;
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