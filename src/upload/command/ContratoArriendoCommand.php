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

class ContratoArriendoCommand extends Command
{	
	
	public $server = 'http://www.sifinca.net/sifinca/web/app_dev.php/';
	public $serverRoot = 'http://www.sifinca.net/';
	
	public $localServer = 'http://10.102.1.22/';
	
// 	public $server = 'http://10.102.1.22/sifinca/web/app.php/';
// 	public $serverRoot = 'http:/10.102.1.22/';

// 	public $server = 'http://104.130.12.152/sifinca/web/app_dev.php/';
// 	public $serverRoot = 'http:/104.130.12.152/';
	
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
	
	public $buildingTypeEdificio = '345bc0a2-4880-4f13-b06f-80cd7405805c';
	
    protected function configure()
    {
        $this->setName('contratosarriendo')
		             ->setDescription('Comando para pasar contratos arriendo');
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
        
		$contratos = $conexion->getContratosArriendo();
		
		$this->buildContratoArriendo($conexion, $contratos);
		
    }
    
    
    function buildContratoArriendo($conexion, $contratos) {
    	
    	
    	$urlContrato = $this->server.'catchment/main/leasingcontract';
    	 
    	$apiContrato = $this->SetupApi($urlContrato, $this->user, $this->pass);
    	
    	$totalContratos = count($contratos);
    	
    	$totalContratos = 1000;
    	
    	$total = 0;
    	$porDonde = 0;
    	for ($i = 646; $i < $totalContratos; $i++) {
    		
    		$contrato = $contratos[$i];
    		
    		$datacredito = null;
    		if($contrato['datacredito'] == 'S'){
    			$datacredito = true;
    		}else if($contrato['datacredito'] == 'N'){
    			$datacredito = false;
    		}
    		
    		$aseguradora = $this->searchClientSF2($contrato['id_aseguradora']);
    		
    		$inmueble = $this->searchProperty($contrato['id_inmueble']);
    		
    		$estado = $this->statusContract($contrato['estado']);
    		
    		$tipocontrato = $this->contractType($contrato['tipo_contrato']);
    		
    		$tipoGarantia = $this->warrantyType($contrato['act_aseg_canon']);
    		
    		$ejecutivocuenta = $this->searchUsuario($contrato);
    		
    		$sucursal = $this->searchOffice($contrato['id_sucursal']);
    		
    		$arrendatarios = $this->buildArrendatarios($conexion, $contrato);
    		
    		$datetime1 = new \DateTime($contrato['fecha_contrato']);
    		$datetime2 = new \DateTime($contrato['fecha_vencimiento']);
    		$interval = $datetime1->diff($datetime2);
    		
//     		echo $interval->format('%R%a d’as');
    		
//     		echo "\n".$interval->format('%a d’as');
    		
//     		echo "\n".$interval->format('%m mes, %d d’as')."\n";
    		
//     		printf('%d a–os, %d meses, %d d’as, %d horas, %d minutos', $interval->y, $interval->m, $interval->d, $interval->h, $interval->i);
    		
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
    				"nameEstablishment"=> $contrato['denominacion'],
    				"businessActivities"=> $contrato['actividades'],
    				"additionalClauses"=> $contrato['CLAUSULA'],
    				"casesAndRelatedUses"=> null,
    				"suretyCode"=> $contrato['cod_aseguradora'],
    				"eviction"=> $contrato['deshaucio'],
    				"dateEviction"=> $contrato['fecha_deshaucio'],
    				"reportToRiskCentrals"=> $datacredito,
    				"companyWarranty"=> $aseguradora,
    				"application"=> null,
    				"property"=> $inmueble,
    				"lease"=> $arrendatarios,
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
    		 
   			$result = $apiContrato->post($bContrato);
    		
   			$result = json_decode($result, true);
    		
   			$porDonde++;
    		echo "\nPor donde: \n".$porDonde;
   			if($result['success'] == true){
   				echo "\nOk\n";
   				$total++; 			
    		}else{
  				echo "\nError contrato\n";
   				echo "\n\n".$json."\n\n";
   			}
    		
    	}
    	
    	
    	
    }
    
    function buildArrendatarios($conexion, $contrato) {

    	
    	$arrendatarios = $conexion->getArrendatoriosPorContrato($contrato['id_contrato']);
    	
    	$bArrendatario = array();
    	
    	foreach ($arrendatarios as $a) {
    		$clientSF2 = $this->searchClientSF2($a['id_cliente']);
    		
    		
    		if(!is_null($clientSF2)){
    			$bArr = array(
    					'client' => $clientSF2,
    					'typeLease' => $this->leasseType($a['tipo_relacion'])
    			);
    			
    			$bArrendatario[] = $bArr;
    		}
    		
    	}
    	
    	return $bArrendatario;
    	
    	
    }
    
    function leasseType($type) {

    	$rType = null;
    	
    	if($type == 'C'){
    		//Coarrendatario
    		$rType = array('id'=>'33645952-c4f1-47d4-b64b-30cdf60c6d5e');
    	}
    	
    	if($type == 'P'){
    		//Principal
    		$rType = array('id'=>'3b13acd5-41ef-4446-8ddb-de47a85597b6');
    	}
    	
    	return $rType;
    	
    }
    
    function searchClientSF2($identificacion){
    
    	//     	echo "\nidentificacion\n";
    	//     	echo $identificacion."\n";
    	 
    	$identificacion = $this->cleanString($identificacion);
    	 
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
    
    		return $clientSF2['data'][0];
    
    	}else{
    		return null;
    	}
    
    	 
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
    		 
    		$p = array('id' => $property['data'][0]['id']);
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
        
    	echo "\n".$type."\n";
    	
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
    
    function searchUsuario($contrato) {
    
    	$email = $contrato['email'];
    	$email = $this->cleanString($email);
    	
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
    	 
    	$apiUser = $this->SetupApi($urlUser, $this->user, $this->pass);
    
    	$user = $apiUser->get();
    	$user = json_decode($user, true);
    
    
    	if($user['total'] > 0){
    		 
    		return $user['data'][0];
    		 
    	}else{
    		return $user = array('id' => 203);
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
    

    function SetupApi($urlapi,$user,$pass){
    
    	$url= $this->server."login";
    	$headers = array(
    			'Accept: application/json',
    			'Content-Type: application/json',
    	);
    
    	$a = new api($url, $headers);
    
    	//print_r($a);
    
    	$result= $a->post(array("user"=>$user,"password"=>$pass));
    
    	//      	echo "\nAqui result\n";
    	//     	print_r($result);
    
    
    
    	$data=json_decode($result);
    
    	//print_r($data);
    
    	$token = $data->id;
    
    	$headers = array(
    			'Accept: application/json',
    			'Content-Type: application/json',
    			//'x-sifinca:SessionToken SessionID="564f29657315cf4b1afa87e8", Username="lrodriguez@araujoysegovia.net"'
    			'x-sifinca: SessionToken SessionID="'.$token.'", Username="'.$user.'"',
    	);
    
    	//     	print_r($headers);
    
    	$a->set(array('url'=>$urlapi,'headers'=>$headers));
    
    	//print_r($a);
    
    	return $a;
    
    }
    
}