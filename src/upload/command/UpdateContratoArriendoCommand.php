<?php
namespace upload\command;

use upload\model\contratoArriendoCartagena;
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

class UpdateContratoArriendoCommand extends Command
{	
	
	public $server = 'http://www.sifinca.net/sifinca/web/app.php/';
	public $serverRoot = 'http://www.sifinca.net/';
	public $localServer = 'http://10.102.1.22/';

	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;
		
    protected function configure()
    {
        $this->setName('updatecontratoarriendo')
		             ->setDescription('Comando para actualizar contratos arriendo');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Datos de contrato SF1 \n");

        $conn = new data(array(
            'server' =>'10.102.1.3'
            ,'user' =>'hherrera'
            ,'pass' =>'daniela201'
            ,'database' =>'sifinca' 
            ,'engine'=>'mssql'
        ));

        $conexion = new contratoArriendoCartagena($conn);      
        		
		$this->crearContratoArriendo($conexion);
		
    }
    
    
    function crearContratoArriendo($conexion) {
    	    	
    	$contratos = $conexion->updateContratosArriendo();
    	    	
    	$totalContratos = count($contratos);
    	
    	//$totalContratos = 1000;
    	
    	$total = 0;
    	$porDonde = 0;
    	
    	$startTime= new \DateTime();
    	
    	for ($i = 0; $i < $totalContratos; $i++) {
    		
    		$contrato = $contratos[$i];
    		
    		$contratoSF2 = $this->searchContratoArriendo($contrato);
    		
    		
    		if(!is_null($contratoSF2)){
    			    			
    			//echo "\nActualizar\n";
    			
    			$urlContrato = $this->server.'catchment/main/leasingcontract/'.$contratoSF2['id'];
    			$apiContrato = $this->SetupApi($urlContrato, $this->user, $this->pass);
    			
    			echo "\n".$urlContrato."\n";
    			
    			$datacredito = null;
    			if($contrato['datacredito'] == 'S'){
    				$datacredito = true;
    			}else if($contrato['datacredito'] == 'N'){
    				$datacredito = false;
    			}
    			
    			
    			$aseguradora = $this->searchAseguradora($contrato['id_aseguradora']);
				
    			//echo "\npaso1\n";
    			
    			$inmueble = $this->searchProperty($contrato['id_inmueble']);
    			
    			//echo "\npaso2\n";
    			
    			$estado = $this->statusContract($contrato['estado']);
    			
    			//echo "\npaso3\n";
    			$tipocontrato = $this->contractType($contrato['tipo_contrato']);
    			
    			//echo "\npaso4\n";
    			$tipoGarantia = $this->warrantyType($contrato['act_aseg_canon']);
    			
    			//echo "\npaso5\n";
    			$ejecutivocuenta = $this->searchUsuario($contrato);
    			
    			//echo "\npaso6\n";
    			$sucursal = $this->searchOffice($contrato['id_sucursal']);
    			
    			
    			$datetime1 = new \DateTime($contrato['fecha_contrato']);
    			$datetime2 = new \DateTime($contrato['fecha_vencimiento']);
    			$interval = $datetime1->diff($datetime2);
    			    			    			
    			if(!is_null($inmueble)){
    				 
    				//echo "\nPor aqui\n";
    				
    				$bContrato = array(
    						"consecutive" => $contrato['id_contrato'],
    						"contractDate" => $contrato['fecha_contrato'],
    						"occupationDate" => $contrato['fecha_ocupacion'],
    						"expirationDate" => $contrato['fecha_vencimiento'],
    						"term"=> $contrato['termino'],
    						"canon"=> $contrato['canon'],
    						"expenseValue"=> $contrato['admi'],
    						"warrantyStatus"=> $contrato['aseg_canon'],
    						"warrantyfee"=> $contrato['por_seguro'],  //Comision garantia
    						"nameEstablishment"=> $this->cleanString($contrato['denominacion']),
    						"businessActivities"=> $this->cleanString($contrato['actividades']),
    						"additionalClauses"=> $this->cleanString($contrato['CLAUSULA']),
    						"casesAndRelatedUses"=> null,
    						"suretyCode"=> $this->cleanString($contrato['cod_aseguradora']),
    						"eviction"=> $contrato['deshaucio'],
    						"dateEviction"=> $contrato['fecha_deshaucio'],
    						"reportToRiskCentrals"=> $datacredito,
    						"companyWarranty"=> $aseguradora,
    						"application"=> null,
    						"property"=> $inmueble,
    						//"lease"=> $arrendatarios,
    						//"nameValue"=> null,
    						"renewal"=> null,
    						"inventory"=> null,
    						"statusLeasingContract"=> $estado,
    						"contractTypeLeasingContract" => $tipocontrato,
    						"warrantyType"=> $tipoGarantia,
    						"accoutExecutive"=> $ejecutivocuenta,
    						"office"=> $sucursal
    			
    				);
    				 
    				 
    				$json = json_encode($bContrato);
    			
    				//echo "\n\n".$json."\n\n";
    			
    				$result = $apiContrato->put($bContrato);
    				 
    				$result = json_decode($result, true);
    				 
    				 
    				 
    				if($result['success'] == true){
    					echo "\nOk\n";
    					
    					//echo "\n\n".$json."\n\n";
    					
    					//$total++;
    				}else{
    					echo "\nError contrato\n";
    					//echo "\n\n".$json."\n\n";
    					 
    					$urlapiMapper = $this->server.'catchment/main/errorleasingcontract';
    					$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    					 
    					$error = array(
    							'leasingContract' => $contrato['id_contrato'],
    							'objectJson' => $json
    					);
    						
    					$resultError = $apiMapper->post($error);
    				}
    				 
    			}else{
    				"\n El inmuebles no esta registrado: ".$contrato['id_inmueble'];
    			}
    			
    			
    		}else{
    			
    			echo "\n El contrato ya existe: ".$contrato['id_contrato']."\n";
    		}
    		
    	
    		
   			
   			$porDonde++;
   			echo "\nVamos por: ".$porDonde."\n";
    		
    	}
    	
    	$finalTime = new \DateTime();
    	
    	
    	$diff = $startTime->diff($finalTime);
    	
    	
    	echo "\n\n Fecha inicial: ".$startTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Fecha final: ".$finalTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
    	
//     	echo "\n Total de contrato: ".$totalContratos."\n";
    	
    }

    
    function searchAseguradora($identificacion) {
    	
    	$identificacion = $this->cleanString($identificacion);
    	
    	if(strlen($identificacion) > 1){
    	
    		$filter = array(
    				'value' => $identificacion,
    				'operator' => 'equal',
    				'property' => 'identity.number'
    		);
    		$filter = json_encode(array($filter));
    	
    	
    		$urlClientSF2 = $this->server.'catchment/main/secure?filter='.$filter;
    		//echo "\n".$urlClientSF2."\n";
    	
    		//$urlClientSF2 = $this->server.'crm/main/zero/client';
    	
    		$apiClientSF2 = $this->SetupApi($urlClientSF2, $this->user, $this->pass);
    	
    	
    		$clientSF2 = $apiClientSF2->get();
    		//      	echo "\nbeneficiario\n";
    		//  		echo $clientSF2;
    	
    		$clientSF2 = json_decode($clientSF2, true);
    	
    		//echo $clientSF2['total'];
    	
    		if(isset($clientSF2['total'])){
    			if($clientSF2['total'] > 0){
    				 
    				$c = array(
    						'id' => $clientSF2['data'][0]['id'],
    						'name' => $clientSF2['data'][0]['client']['name']
    				);
    				return $c;
    				 
    			}else{
    				return null;
    			}
    		}else{
    			return null;
    		}
    		
    	}else{
    		return null;
    	}
    	
    }
    
    function searchClientSF2($identificacion){
    
    	//     	echo "\nidentificacion\n";
    	//     	echo $identificacion."\n";
    
    	$identificacion = $this->cleanString($identificacion);
    
    	if(strlen($identificacion) > 1){
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
    		//echo "\n".$urlClientSF2."\n";
    		
    		//$urlClientSF2 = $this->server.'crm/main/zero/client';
    		
    		$apiClientSF2 = $this->SetupApi($urlClientSF2, $this->user, $this->pass);
    		
    		
    		$clientSF2 = $apiClientSF2->get();
    		//      	echo "\nbeneficiario\n";
    		//  		echo $clientSF2;
    		
    		$clientSF2 = json_decode($clientSF2, true);
    		
    		//echo $clientSF2['total'];
    		
    		if($clientSF2['total'] > 0){
    		
    			$c = array(
    				'id' => $clientSF2['data'][0]['id'],
    				'name' => $clientSF2['data'][0]['name']
    			);
    			return $c;
    		
    		}else{
    			return null;
    		}
    	}else{
    		return null;
    	} 
    
    }
      
    function searchProperty($propertySF1) {
    	 
    	$propertySF1 = $this->cleanString($propertySF1);
    	    	
    	$relations = array(
    			"address",
    			"propertyTypeCatchment", 
    			"inscriptionType", 
    			"propertyStatus"
    	);
    	$relations = json_encode($relations);
    	
    	$filter = array(
    			'value' => $propertySF1,
    			'operator' => 'equal',
    			'property' => 'consecutive'
    	);
    	$filter = json_encode(array($filter));
    	 
    	$urlProperty = $this->server.'catchment/main/zero/property?relations='.$relations.'&filter='.$filter;
    
    	//echo "\n".$urlProperty."\n";
    	
    	$apiProperty = $this->SetupApi($urlProperty, $this->user, $this->pass);
    	 
    	$property = $apiProperty->get();
    	$property = json_decode($property, true);
    	 
//     	echo "\n\nInmueble\n";
//     	print_r($property['data'][0]); 
    	
    	if($property['total'] > 0){
    		 
    		$p = array(
    				'id' => $property['data'][0]['id'],
    				'consecutive' => $property['data'][0]['consecutive']
    		);
    		return $p;
    		 
    	}else{
    		return null;
    	}
    	 
    }
        
    function statusContract($status) {
    	
    	$rStatus = null;
    	
    	if($status == '0'){
    		//En proceso
    		$rStatus = array('id'=>'cdfdc596-612a-410f-ac77-799e237832d1');
    	}
    	
    	if($status == '1'){
    		//Legalizado
    		$rStatus = array('id'=>'99923a28-7a3b-4612-bac1-282e6ba3a317');
    	}
    	
    	if($status == '2'){
    		//Activo
    		$rStatus = array('id'=>'f071f312-2ed0-4ffd-be35-0b6da1ac8e71');
    	}
    	
    	if($status == '3'){
    		//Desocupado
    		$rStatus = array('id'=>'08bcff74-a85b-4d7d-9f4c-7008080d35d2');
    	}
    	
    	if($status == '4'){
    		//Desocupado
    		$rStatus = array('id'=>'08bcff74-a85b-4d7d-9f4c-7008080d35d2');
    	}
    	
    	return $rStatus;
    }
    
    function contractType($type) {
    	 
    	$rType = null;
    	 
    	if($type == 'C'){
    		//Comercial
    		$rType = array('id'=>'8d49e38a-fef0-4d1c-b0d5-3cad19fff78c');
    	}
    	 
    	if($type == 'O'){
    		//Oficina
    		$rType = array('id'=>'77012956-53f2-45e2-ba2c-d548b01aa8ff');
    	}
    	 
    	if($type == 'P'){
    		//
    		$rType = array('id'=>'420938d6-f006-4ff7-b4d7-d034b1bb582a');
    	}
    	 
    	if($type == 'V'){
    		//Vivienda
    		$rType = array('id'=>'2a765a1d-51cf-4348-b716-581b5fdf0154');
    	}
    	 

    	return $rType;
    }
    
    function warrantyType($type) {
    
    	$rType = null;
        
    	//echo "\n".$type."\n";
    	
    	if($type == 'O'){
    		//No garantizado
    		$rType = array('id'=>'6a35e35d-64ff-4ed3-99d3-94ca1553b0d5');
    	}
    
    	if($type == '1'){
    		//Garantizado
    		$rType = array('id'=>'c5d05d62-fa22-45b6-8ee5-4ae80cedfcc9');
    	}
    
    	return $rType;
    }
    
    /**	
     * Buscar ejecutivo de cuenta del contrato
     * @param unknown $contrato
     * @return multitype:number
     */
    function searchUsuario($contrato) {
    
    	$email = $contrato['email'];
    	$email = $this->cleanString($email);
    	//Convertir a minusculas
    	$email = strtolower($email); 
    	
    	$cobrador = $contrato['id_cobrador'];
    	$cobrador = $this->cleanString($cobrador);
    	 
    	if($cobrador == '000'){
    		//Cartera oficial asignada a Hernando Madrid
    		$user = array('id' => 116);
    		return $user;
    	}
    	 
    	if($cobrador == '009'){
    		//Cartera especial asignada a Dolores Arrieta
    		$user = array('id' => 68);
    		return $user;
    	}
    	     	 
    	$filter = array(
    			'value' => $email,
    			'operator' => '=',
    			'property' => 'email'
    	);
    	$filter = json_encode(array($filter));
    
    	$urlUser = $this->server.'admin/security/user?filter='.$filter;
    	 
    	//echo "\n".$urlUser."\n";
    	$apiUser = $this->SetupApi($urlUser, $this->user, $this->pass);
    
    	$user = $apiUser->get();
    	$user = json_decode($user, true);
    
    
    	if($user['total'] > 0){
    		 
    		$u = array(
    				'id' => $user['data'][0]['id'],
    				'email' => $user['data'][0]['email']
    		);
    		
    		return $u;
    		 
    	}else{
    		return $user = array('id' => 203); //User araujo y segovia
    	}
    	 
    }
    
    function searchOffice($oficina){
    	
    	$urlOffice = $this->server.'admin/sifinca/mapper/propertyOffice.CTG/'.$oficina;
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
    	
//     	if(is_null($office)){
//     		$office = $officeCentro;
//     	}

    	return $office;
    	
    }
    
    
    function searchContratoArriendo($contrato) {
    	
    	$codigoContrato = $this->cleanString($contrato['id_contrato']);
    	 
    	
    	
    	$filter = array(
    			'value' => $codigoContrato,
    			'operator' => 'equal',
    			'property' => 'consecutive'
    	);
    	$filter = json_encode(array($filter));
    	
    	$urlContract = $this->server.'catchment/main/leasingcontract?filter='.$filter;
    	//echo "\n".$urlContract."\n";
    	
    	$apiContract = $this->SetupApi($urlContract, $this->user, $this->pass);
    	
    	$contract = $apiContract->get();
    	$contract = json_decode($contract, true);
    	
    	 
    	if($contract['total'] > 0){
    		 
    		$c = array('id' => $contract['data'][0]['id']);
    		return $c;
    		 
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
    				
    				echo "\nError 401\n";
    				$this->login();
    			}
    		}else{
    
    			if(isset($result['id'])){
        				
    				$this->token = $result['id'];
    				
    				echo "\nToken: ".$this->token."\n";
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