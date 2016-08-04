<?php
namespace upload\command;
use upload\model\avaluo;
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

class AvaluosCommand extends Command
{	
	
 	public $server = 'http://www.sifinca.net/sifinca/web/app.php/';
 	public $serverRoot = 'http://www.sifinca.net/';
	
	public $localServer = 'http://10.102.1.22/';
	

	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;	
	
    protected function configure()
    {
        $this->setName('avaluos')
		             ->setDescription('Comando para avaluos');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Datos de inmuebles SF1 \n");

        $conn = new data(array(
            'server' =>'10.102.1.3'
            ,'user' =>'hherrera'
            ,'pass' =>'daniela201'
            ,'database' =>'sifinca' 
            ,'engine'=>'mssql'
        ));


        
        echo "nafer no me cree";
        
        $conexion = new avaluo($conn);
         
        $this->getAvaluosSF2($conexion);
	   
       // $this->updateInmuebles($inmuebles, $inmueblesCtg);
              
        //$this->buildFotos($inmueblesCtg);

        //$this->subirInmueblesConError();

        //$this->mapperTipoEdificio($inmueblesCtg);
        //$this->buildEdificios($inmueblesCtg);
        
    }
    
   
    public  function getAvaluosSF2($avaluo){
    	
    	$relaciones = array("client", "perito","propertyAddress");
    	$relaciones = json_encode($relaciones);
    	
    
    	   	   	 
    	
    	$urlOpArriendos = $this->server.'appraisals/main/zero/appraisals?relations='.$relaciones;
    	
    	
    	echo "\n".$urlOpArriendos."\n";
    	
    	
    	$apiOp = $this->SetupApi($urlOpArriendos, $this->user, $this->pass);
    	$oportunityArriendos = $apiOp->get();
    	$oportunityArriendos = json_decode($oportunityArriendos, true);
    	
    	//print_r($oportunityArriendos);

    	foreach ($oportunityArriendos['data'] as $kAppra => $vAppra){
    		if(!$avaluo->exitsAvaluo($vAppra['appraisalNumber'])){
	    		$oneAppraisal = array();
	    	
	    		$oneAppraisal['id_avaluo'] =  $vAppra['appraisalNumber'];
	    		$oneAppraisal['fecha_solicitud'] = $vAppra['entrydate'];
	    		$oneAppraisal['estado'] = 0;
	    		$oneAppraisal['address'] = $vAppra['propertyAddress'];
	    		$oneAppraisal['id_cliente'] = $vAppra['id_avaluo'];
	    		$oneAppraisal['id_ciudad'] = $vAppra['id_avaluo'];
	    		$oneAppraisal['id_sector'] = "";
	    		$oneAppraisal['id_barrio'] = $vAppra['id_avaluo'];
	    		$oneAppraisal['id_tipo_inmueble'] = 1;
	    		$oneAppraisal['direccion'] = $vAppra['id_avaluo'];
	    		$oneAppraisal['tipo_avaluo'] = 1;
	    		$oneAppraisal['fecha_new'] = $vAppra['id_avaluo'];
    			$oneAppraisal['solicitante'] = $vAppra['id_avaluo'];
	    		print_r($oneAppraisal);
    			
    		}else{
    			echo "\n\nEl avaluo ".$vAppra['appraisalNumber']." ya existe.\n\n";
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