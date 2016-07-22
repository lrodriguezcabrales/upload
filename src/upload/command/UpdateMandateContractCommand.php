<?php
namespace upload\command;

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

class UpdateMandateContractCommand extends Command
{	
	
	public $server = 'http://www.sifinca.net/sifinca/web/app.php/';
	public $serverRoot = 'http://www.sifinca.net';
	
	public $localServer = 'http://10.102.1.22/';
		
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;
			
    protected function configure()
    {
        $this->setName('updateContratoMandato')
		             ->setDescription('Comando para pasar convenios');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $conn = new data(array(
            'server' =>'10.102.1.3'
            ,'user' =>'hherrera'
            ,'pass' =>'daniela201'
            ,'database' =>'sifinca' 
            ,'engine'=>'mssql'
        ));

        $conexion = new conveniosCartagena($conn);
     	

        
        $this->crearContratosDeMandato($conexion);
    }
        
    public function crearContratosDeMandato($conexion) {

    	
//     	$urlConvenio = $this->server.'catchment/main/agreement/only/ids';
//     	echo "\n".$urlConvenio."\n";
    	    	
//     	$apiConvenio = $this->SetupApi($urlConvenio, $this->user, $this->pass);
    	 
//     	$convenios = $apiConvenio->get();
    	
//     	$convenios = json_decode($convenios, true);
//     	//echo $convenios['total']

//     	$urlapioOwner = $this->server.'catchment/main/owner';
    	 
//     	$apiOwner = $this->SetupApi($urlapioOwner, $this->user, $this->pass);
    	 
//     	echo "\nTotal de convenios - contratos mandato: ".count($convenios['data'])."\n";
    	 
//     	$totalConvenios = count($convenios['data']);

    	$contratosMandatoSF1 = $conexion->getContratosDeMandato();
    	$totalContratos = count($contratosMandatoSF1);
    	
    	$porDonde = 0;
    	$startTime= new \DateTime();
    	 
    	//foreach ($convenios['data'] as $convenio) {
    	for ($i = 0; $i < $totalContratos; $i++) {
    	    		
    		$cm = $contratosMandatoSF1[$i];
    		    		    		    			
    		$mandateContractSF2 = $this->searchMandateContractPorConvenioEinmueble($cm);
    		 
    		$convenioSF2 = $this->searchConvenioSF2($cm);
    		 
    		if(!is_null($mandateContractSF2)){
    				
    			$property = $this->searchProperty($cm['id_inmueble']);
    				
    			if((!is_null($property)) && (!is_null($convenioSF2))){
    				 
    				$typeContract = $this->searchTipoContratoMandato($cm);
    				$user = $this->searchUsuario($cm);
    				 
    				$bContratoDeMandato = array(
    						'consecutive' => $cm['id_inmueble'],
    						'leaseCommission' => $cm['por_cmsi'], //Comision de arriendo
    						'salesCommission' => $cm['por_seguro'], //Comision de garantia
    						'property' => $property,
    						'catcher' => $user,
    						'typesContracts' => $typeContract,
    						'agreement' => array('id'=> $convenioSF2['id']),
    						'convenioSifincaOne' => $this->cleanString($cm['id_convenio'])
    				);
    				 
    				$json = json_encode($bContratoDeMandato);
    				//echo "\n\n".$json."\n\n";
    				 
    				//print_r($mandateContractSF2);
    				$urlContratoMandato = $this->server.'catchment/main/mandatecontract/'.$mandateContractSF2[0]['id'];
    					
    				//echo "\n".$urlContratoMandato."\n";
    					    					
    				$apiContratoMandato = $this->SetupApi($urlContratoMandato, $this->user, $this->pass);
    				 
    				$result = $apiContratoMandato->put($bContratoDeMandato);
    				 
    				$result = json_decode($result, true);
    				 
    				//print_r($result);
    				if(isset($result['success'])){
    					if($result['success'] == true){
    						echo "\nOk, contrato mandato ".$cm['id_inmueble'];
    						//$total++;
    						 
    					}
    				}
    				else{
    					 
    					$exist = false;
    					 
    					if(isset($result['message'])){
    						$msj = $result['message'];
    		    		
    						$exist = strpos($msj, 'duplicate key');
    		    		
    					}
    					     					 
    					if(!$exist){
    		
    						echo "\nError al actualizar contrato mandato ".$cm['id_inmueble']."\n";   							
    							
    					}else{
    						echo "\nEl contrato de mandato ya existe: ".$cm['id_inmueble']."\n";
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
        
         
    function searchTipoContratoMandato($convenio) {
    	
    	$typeContract = null;

    	if($convenio['por_seguro'] > 0){
    		//Con garantia
    		$typeContract = array('id' => '53eee515-a699-498b-a65f-16336660c350');
    	}else{
    		//Sin garantia
    		$typeContract = array('id' => '54530e43-abfc-44e2-a98f-b9fcf51003fa');
    	}
      	
    	return $typeContract;
    	
    }

    /**	
     * Buscar convenio en sifinca2
     * @param unknown $convenio
     * @return NULL
     */
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
            
    function searchMandateContractPorConvenioEinmueble($convenio) {
    
    	$filter = array();
    	
    	$filter[] = array(
    			'value' => $this->cleanString($convenio['id_convenio']),
    			'operator' => 'equal',
    			'property' => 'convenioSifincaOne'
    	);
    	
    	$filter[] = array(
    			'value' => $this->cleanString($convenio['id_inmueble']),
    			'operator' => 'equal',
    			'property' => 'property.consecutive'
    	);
    	
    	//print_r($filter);
    	
    	$filter = json_encode($filter);
    
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
        
    /**	
     * Buscar inmueble en Sifinca2
     * @param unknown $propertySF1
     * @return multitype:NULL |NULL
     */
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
    		 
    		$p = array(
    			'id'=> $property['data'][0]['id'],
    			'consecutive' => $property['data'][0]['consecutive']
    		);
    		//return $property['data'][0];
    		return $p;
    		 
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