<?php
namespace upload\command\Cartagena;

use upload\model\tipoDocumento;
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

class MapperTipoDocumentosCommand extends Command
{	
	
 	public $server = 'http://www.sifinca.net/sifinca/web/app.php/';
 	public $serverRoot = 'http://www.sifinca.net/';
	
	public $localServer = 'http://10.102.1.22/';
	
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;
	
	
    protected function configure()
    {
        $this->setName('mapperTipoDocumentos')
		             ->setDescription('Comando para tipos de documents');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Tipos de documentos SF1 \n");

        $conn = new data(array(
            'server' =>'10.102.1.3'
            ,'user' =>'hherrera'
            ,'pass' =>'daniela201'
            ,'database' =>'docflow' 
            ,'engine'=>'mssql'
        ));

      
        $conexion = new tipoDocumento($conn);
        
	    $this->updateDocumentType($conexion);
	   
        
    }
    
    function updateDocumentType($conexion) {
    	
    	$tiposDeDocumentoSF1 = $conexion->getTiposDeDocumento();

    
    	$porDonde = 0;
    	for ($i = 0; $i < count($tiposDeDocumentoSF1); $i++) {
    		
    		$tipoDocumentSF1 = $tiposDeDocumentoSF1[$i];
    		
    		//print_r($tipoDocumentSF1);
    		
    		$documentTypeSF2 = $this->searchDocumentType($tipoDocumentSF1['id']);
    		
    		if(!is_null($documentTypeSF2)){
    			
    			$this->crearMapper($tipoDocumentSF1, $documentTypeSF2);		
    			
    		}else{
    			
    			echo "\nTipo de documento no encontrado: ".$tipoDocumentSF1['nombre']."\n";
    			
    			//$this->crearDocumentType($tipoDocumentSF1);
    			
    			
    			
    		}
    		
    		$porDonde++;
    		
    		echo "\nVamos por: ".$porDonde."\n";
    	}
    	
    	
    }
    
    function crearMapper($tipoDocumentSF1, $documentTypeSF2) {
    	
    	$bmapper = array(
    			"name"=> "documentType",
    			"idSource"=> $tipoDocumentSF1['id'],
    			"idTarget"=> $documentTypeSF2['id'],
    			"label"=> $tipoDocumentSF1['nombre']
    	);
    	 
    	$urlapiMapper = $this->server.'admin/sifinca/mapper';
    	 
    	$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    	 
    	$result = $apiMapper->post($bmapper);
    	
    	//     			if(isset($result['success'])){
    	
    	//     				if($result['success'] == true){
    	//     					echo "Ok mapper: ".$tipoDocumentSF1['id']." - ".$tipoDocumentSF1['nombre'];
    	//     				}else{
    	//     					echo "Error mapper: ".$tipoDocumentSF1['id']." - ".$tipoDocumentSF1['nombre'];
    	//     				}
    	//     			}else{
    	//     				echo "Error mapper: ".$tipoDocumentSF1['id']." - ".$tipoDocumentSF1['nombre'];
    	//     			}
    	
    }
    
    function searchDocumentType($code) {
    	
    	$documentType = null;
    	
    	$filter = array(
    			'value' => $code,
    			'operator' => 'equal',
    			'property' => 'code'
    	);
    	$filter = json_encode(array($filter));
    	 
    	$urlDocumentType = $this->server.'archive/main/documenttype?filter='.$filter;
    	
    	//echo "\n".$urlDocumentType."\n";
    	
    	$apiDocumentType = $this->SetupApi($urlDocumentType, $this->user, $this->pass);
    	 
    	
    	$docType = $apiDocumentType->get();
    	$docType = json_decode($docType, true);
    	 
    	if($docType['total'] > 0){
    		 
    		//echo "\n totla:  ".$docType['total']."\n";
    		//print_r($docType);
    		if($docType['total'] == 1){
    			
    			$documentType = $docType['data'][0];
    			
    			//print_r($documentType);
    		}
    		 
    	}
    	
    	return $documentType;
    	
    }
    
    function crearDocumentType($tipoDocumentSF1) {
    	
    	$container = null;
    	
    	if($tipoDocumentSF1['id_archivador'] == 'ARRIENDOS'){
    		$container = array('id' => '2e062689-3070-4758-9af5-4ee676b1fd58');
    	}
    	
    	if($tipoDocumentSF1['id_archivador'] == 'THUMANO'){
    		$container = array('id' => '37d5ff73-83f2-477a-b65c-d8792993a205');
    	}
    	
    	echo "\nid_archivador: ".$tipoDocumentSF1['id_archivador']."\n";
    	
    	
    	if(!is_null($container)){
    		
    		$bDocumentType = array(
    				'code' => $tipoDocumentSF1['id'],
    				'name' => $tipoDocumentSF1['nombre'],
    				'container' => $container,
    				'monthsValidaty' => $tipoDocumentSF1['rete_fisico']
    		);
    		
    		$json = json_encode($bDocumentType);
    		echo "\n".$json."\n";
    		
    		$urlDocumentType = $this->server.'archive/main/documenttype';
    		
    		$apiDocumentType = $this->SetupApi($urlDocumentType, $this->user, $this->pass);
    		
    		$result = $apiDocumentType->post($bDocumentType);
    		 
    		$result = json_decode($result, true);
    		 
    		if(isset($result['success'])){
    			if($result['success'] == true){
    				echo "\nOk - tipo de documento creado: ".$tipoDocumentSF1['nombre']."\n";
    				 
    				//return $result;
    			}else{
    				echo "\nError - tipo de documento creado: ".$tipoDocumentSF1['nombre']."\n";
    				throw new Exception('Error');
    			}
    		}else{
    			echo "\nError - tipo de documento creado: ".$tipoDocumentSF1['nombre']."\n";
    			throw new Exception('Error');
    		}
    		
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