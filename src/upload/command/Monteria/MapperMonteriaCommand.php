<?php
namespace upload\command\Monteria;

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

class MapperMonteriaCommand extends Command
{	
	
 	public $server = 'http://www.sifinca.net/monteriaServer/web/app.php/';
 	public $serverRoot = 'http://www.sifinca.net/';
	
	public $localServer = 'http://10.102.1.22/';
	
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;
	
	
    protected function configure()
    {
        $this->setName('mapperMonteria2')
		             ->setDescription('Mapper monteria');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Datos de inmuebles SF1 \n");

         $conn = new data(array(
            'server' =>'192.168.100.1'
            ,'user' =>'sa'
            ,'pass' =>'75080508360'
            ,'database' =>'docflow' 
            ,'engine'=>'mssql'
        ));

      
        $conexion = new tipoDocumento($conn);
        
	    //$this->mapperTipoDocumento($conexion);
	   
        //$this->createDocumentType($conexion);
        
        $this->mapperDocumentType($conexion);
    }
    
    function createDocumentType($conexion) {
    	
    	$tipoDeDocumentoSF1 = $conexion->getTiposDeDocumentoAllMonteria();
    	
    	
    	
    	foreach ($tipoDeDocumentoSF1 as $td) {
    		
    		$tipoDeDocumentoSF2 = $this->searchDocumentType($td);
    		    		
    		if(is_null($tipoDeDocumentoSF2)){
    			
    			$contentType = array();
    			$contentType[] = array('id'=> 'ba977e1b-5292-43de-8c66-601f877c7729');
    			$contentType[] = array('id'=> 'db0dab63-70c5-466e-9ca8-60c6753465cd');
    			
    			$bTipoDocumento = array(
    					'code' => $td['id'],
    					'codeName'=> $td['id']." - ".$td['nombre'],
    					'name' => $td['nombre'],
    					'container'=> array('id'=> '2e062689-3070-4758-9af5-4ee676b1fd58'), //arriendos
    					'contentType' => $contentType
    			);
    			
    			$json = json_encode($bTipoDocumento);
    			
    			echo "\n".$json."\n";
    			
    			
    			$url = $this->server.'archive/main/documenttype';
    			  
    			echo "\n".$url."\n";
    			
    			$api = $this->SetupApi($url, $this->user, $this->pass);
    			
    			$result = $api->post($bTipoDocumento);
    			    			
    			$result = json_decode($result, true);
    			
    			
    			
    			
    			if(isset($result['success'])){
    				 
    				if($result['success'] == true){
    			
    					echo "\nTipo de documento creado - ".$td['nombre']."\n";
    					
    				}
    			
    			}else{
    				echo "\nError\n";
    			
    		}
    		
    	}
    	
    }
    }
    
    function mapperDocumentType($conexion) {
    	
    	$tipoDeDocumentoSF1 = $conexion->getTiposDeDocumentoAllMonteria();

    	foreach ($tipoDeDocumentoSF1 as $tipo1) {
    		
    		$tipo2  = $this->searchDocumentType($tipo1);
    		
    		if(!is_null($tipo2)){
    			
    			$bMapper = array(
    					"name"=> "documentType",
    					"idSource"=> $tipo1['id'],
    					"idTarget"=> $tipo2['id'],
    					"label"=> $tipo1['nombre']
    			);
    			
    	
    			
    			$url = $this->server.'admin/sifinca/mapper';
    				

    			 
    			$api = $this->SetupApi($url, $this->user, $this->pass);
    			 
    			$result = $api->post($bMapper);
    			
    			$result = json_decode($result, true);
    			
    			if(isset($result['success'])){
    					
    				if($result['success'] == true){
    					 
    					echo "\nTipo de documento mapeado - ".$td['nombre']."\n";
    						
    				}
    				 
    			}else{
    				echo "\nError\n";
    				 
    			}
    			
    		}
    		
    	}
    }
    
    function searchDocumentType($tipoDocumento) {
    	
    	$code = $tipoDocumento['id'];
    	
    	$filter1 = array(
    			'value' => $code,
    			'operator' => 'equal',
    			'property' => 'code'
    	);
    	
    	$filter2 = array(
    			'value' => 'rrhh',
    			'operator' => 'has',
    			'property' => 'container.name'
    	);
    	
    	
    	$filter = array();
    	$filter[] = $filter1;
    	$filter[] = $filter2;
    	
    	$filter = json_encode($filter);
    	
    	$urlDocumentType = $this->server.'archive/main/documenttype?filter='.$filter;
    	 
    	echo "\n".$urlDocumentType."\n";
    	 
    	$apiDocumentType = $this->SetupApi($urlDocumentType, $this->user, $this->pass);
    	
    	 
    	$documentType = $apiDocumentType->get();
    	$documentType = json_decode($documentType, true);
    	
    	if($documentType['total'] > 0){
    		 
    		return $documentType['data'][0];
    		 
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