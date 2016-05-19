<?php
namespace upload\command;

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

class TiposDeDocumentosCommand extends Command
{	
	
 	public $server = 'http://www.sifinca.net/sifinca/web/app.php/';
 	public $serverRoot = 'http://www.sifinca.net/';
	
	public $localServer = 'http://10.102.1.22/';
	
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;
	
	
    protected function configure()
    {
        $this->setName('tipodocumentos')
		             ->setDescription('Comando para tipos de documents');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Datos de inmuebles SF1 \n");

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
    	
    	$tipoDeDocumentoSF1 = $conexion->getTiposDeDocumento();

    	$tipoDeDocumentoSF2 = $this->searchDocumentType();
    	
    	
    	foreach ($tipoDeDocumentoSF2 as $tipo2) {
    		foreach ($tipoDeDocumentoSF1 as $tipo1) {
    			
    			if($tipo2['name'] == $tipo1['nombre']){
    				$urlDocumentType = $this->server.'archive/main/documenttype/'.$tipo2['id'];
    				
    				//echo "\n".$urlBank."\n";
    				
    				$apiDocumentType = $this->SetupApi($urlDocumentType, $this->user, $this->pass);
    				 
    				$tipo2['code'] = $tipo1['id'];
    				
    				$result = $apiDocumentType->put($tipo2);
    				    				
    				$result = json_decode($result, true);
    				 
    				if($result['success'] == true){
    					echo "\nActualizado ".$tipo2['name']."\n";
    					   					 
    				}
    				 
    				if(isset($result['error'])){
    					echo "\nerror\n";
    				}
    			}
    		}
    	}
    }
    
    function searchDocumentType() {
    	
    	$filter = array(
    			'value' => 'Arriendos',
    			'operator' => 'equal',
    			'property' => 'container.name'
    	);
    	$filter = json_encode(array($filter));
    	
    	$urlDocumentType = $this->server.'archive/main/documenttype?filter='.$filter;
    	 
    	//echo "\n".$urlBank."\n";
    	 
    	$apiDocumentType = $this->SetupApi($urlDocumentType, $this->user, $this->pass);
    	
    	 
    	$documentType = $apiDocumentType->get();
    	$documentType = json_decode($documentType, true);
    	
    	if($documentType['total'] > 0){
    		 
    		return $documentType['data'];
    		 
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