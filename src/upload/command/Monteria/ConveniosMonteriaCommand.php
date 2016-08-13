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

class ConveniosMonteriaCommand extends Command
{	
	
	public $server = 'http://www.sifinca.net/monteriaServer/web/app.php/';
	public $serverRoot = 'http:/www.sifinca.net/';
	public $localServer = 'http://10.102.1.22/';
	
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;	
	
	public $colombia = '8701307b-d8bd-49f1-8a91-5d0f7b8046b3';
	
	public $idTypeCedula = '6f80343e-f629-492a-80d1-a7e197c7cf48';
	
	public $contactTypeOther = 'ac76deff-6371-4264-b724-b24b98803b94';
	
	public  $typeAddressHome = '8b8b75ae-6338-461f-8bbd-fc1283621d83';
	public  $typeAddressCorrespondencia = 'e8e9cc62-ec79-4453-8247-a57cc1cf4712';

	
	public  $attributeAlcobas = 'b4b7ea95-ea41-4546-95e0-82b56a947411';
	public  $attributeBanos = 'bb0666a4-e3bf-45eb-8760-d6602a868ed8';
	
	
    protected function configure()
    {
        $this->setName('conveniosMonteria')
		             ->setDescription('Comando para pasar convenios');
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
     	
       	$this->crearConvenios($conexion);
        
		
    }
    

    /**
     * Crear convenios en sifinca2
     */
    public function crearConvenios($conexion) {
    	 
    	$convenios = $conexion->getSoloConveniosMonteria();
    	 
    	$urlConvenio = $this->server.'catchment/main/agreement';
    	$apiConvenio = $this->SetupApi($urlConvenio, $this->user, $this->pass);
    
    	$porDonde = 0;
    	 
    	$startTime= new \DateTime();
    	 
    	echo "\nTotal de convenios: ".count($convenios)."\n";
    	 
    	foreach ($convenios as $convenio) {
    
    		$convenioSF2 = $this->searchConvenioSF2($convenio);
    
    		if(is_null($convenioSF2)){
    			 
    			//Convenio Activo
    			if($convenio['estado'] == 'V'){
    				//echo "\nConvenio Activo\n";
    				$statusAgreement = array('id' => 'e905639b-8e6f-4df0-a079-f54869132763');
    			}
    			 
    			//Convenio Inactivo
    			if($convenio['estado'] == 'R'){
    				//echo "\nConvenio Inactivo\n";
    				$statusAgreement = array('id' => '83e2fcf9-4ea9-46d2-9068-7960df757cb4');
    			}
    			 
    			 
    			$agreementDate = new \DateTime($convenio['fecha_convenio']);
    			$agreementDate = $agreementDate->format('Y-m-d');
    			 
    			$initialDate = new \DateTime($convenio['fecha_inicio']);
    			$initialDate = $initialDate->format('Y-m-d');
    			 
    			$endDate = new \DateTime($convenio['fecha_final']);
    			$endDate = $endDate->format('Y-m-d');
    			 
    			$retirementDate = new \DateTime($convenio['fecha_retiro']);
    			$retirementDate = $retirementDate->format('Y-m-d');
    			 
    			//Asesor integreal
    			$responsable = $this->searchUsuarioByEmail($convenio['email']);
    
    			$bConvenio = array(
    					'consecutive' => $convenio['id_convenio'],
    					'statusAgreement' => $statusAgreement,
    					'agreementDate' => $agreementDate,
    					'initialDate' => $initialDate,
    					'endDate' => $endDate,
    					'retirementDate' => $retirementDate,
    					'responsable' => $responsable
    			);
    
    			$json = json_encode($bConvenio);
    
    			//echo "\n\n".$json."\n\n";
    
    			$result = $apiConvenio->post($bConvenio);
    
    			$result = json_decode($result, true);
    
    
    			//print_r($result);
    			if(isset($result['success'])){
    				 
    				if($result['success'] == true){
    					echo "\nOk convenio\n";
    
    				}
    
    			}else{
    				echo "\nError convenio: ".$convenio['id_convenio']."\n";
    				//echo "\n\n".$json."\n\n";
    				 
    				 
    				$urlapiMapper = $this->server.'catchment/main/erroragreement';
    				$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    				 
    				$error = array(
    						'agreement' => $convenio['id_convenio'],
    						'objectJson' => $json
    				);
    				 
    				$apiMapper->post($error);
    				 
    			}
    			 
    			 
    			 
    		}else{
    			 
    			echo "\nEl convenio ya existe ".$convenio['id_convenio']."\n";
    			 
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
     * Buscar convenio en sifinca2
     */
    function searchConvenioSF2($convenio) {
    	 
    	 
    	$filter = array(
    			'value' => $this->cleanString($convenio['id_convenio']),
    			'operator' => 'equal',
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
    
    /**
     * Buscar un usuario en sifinca2 por medio del email de sifinca1
     * @param unknown
     * @return multitype:number
     */
    function searchUsuarioByEmail($email) {
    
    	$email = $this->cleanString($email);
    
    	if($email == 'inmobiliaria@araujoysegovia.com'){
    		$user = array('id' => 203);
    		return $user;
    	}
    
    	if($email == 'hherrera@araujoysegovia.com'){
    		$user = array('id' => 203);
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