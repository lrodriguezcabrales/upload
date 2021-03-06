<?php
namespace upload\command;
use upload\model\avaluo;
use upload\model\oportunity;
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

        $output->writeln("Datos de Avaluos SF1 \n");

        $conn = new data(array(
            'server' =>'10.102.1.3'
            ,'user' =>'hherrera'
            ,'pass' =>'daniela201'
            ,'database' =>'sifinca' 
            ,'engine'=>'mssql'
        ));

    
        // $oportunity = new oportunity($conn);
        $conexion = new avaluo($conn);
         
        $this->getAvaluosSF2($conexion);
	   
        // $this->updateInmuebles($inmuebles, $inmueblesCtg);         
        // $this->buildFotos($inmueblesCtg);
        // $this->subirInmueblesConError();
        // $this->mapperTipoEdificio($inmueblesCtg);
        // $this->buildEdificios($inmueblesCtg);        
    }
       
    public  function getAvaluosSF2($avaluo)
    {    	
    	$relaciones = array("client", "perito","propertyAddress");
    	$filter = array();
    	
    	$fecha = date('d-m-Y');
    	$nuevafecha = strtotime ( '-2 day' , strtotime ( $fecha ) ) ;
    	$nuevafecha = date ( 'd-m-Y' , $nuevafecha );
    	
    	$filter[] = array(
    		'value' => $nuevafecha,
    		'operator' => '+>=',
    		'property' => 'entrydate'
    	);    	
    	
    	$filter = json_encode($filter);
    	$relaciones = json_encode($relaciones);    	   	   
    	print_r($filter);
    	
    	$urlOpArriendos = $this->server.'appraisals/main/zero/appraisals?relations='.$relaciones."&filter=".$filter;
    	
    	echo "\n -----".$nuevafecha."----- \n";
    	echo "\n".$urlOpArriendos."\n";
    	    	
    	$apiOp = $this->SetupApi($urlOpArriendos, $this->user, $this->pass);
    	$oportunityArriendos = $apiOp->get();
    	$oportunityArriendos = json_decode($oportunityArriendos, true);
    	
    	//print_r($oportunityArriendos);
    	foreach ($oportunityArriendos['data'] as $kAppra => $vAppra)
    	{    		
	    		$oneAppraisal = array();	    	
	    		$oneAppraisal['id_avaluo'] =  $vAppra['appraisalNumber'];
	    		
	    		if ($vAppra['entrydate'])
	    		{
	    			$date= $vAppra['entrydate']['date'];
	    			$oneAppraisal['fecha_solicitud'] = date("Y-m-d H:i:s", strtotime($date));
	    			
	    			echo "\n------------------------------\n";
	    			echo "Fecha soli ".$oneAppraisal['fecha_solicitud']."\n";
	    		}
	    			    		
	    		$oneAppraisal['estado'] = 0;
	    		//$oneAppraisal['address'] = $vAppra['propertyAddress'];
	    		
	    		if ($vAppra['client'])
	    		{
	    			if ($vAppra['client']['identity'])
	    			{	   
	    				$identificacionClient = $vAppra['client']['identity']['number'];
	    				
	    				if ($avaluo->exitClient($identificacionClient))
	    				{	    				
	    					//echo "\n\nEl cliente ".$vAppra['client']['name']." SI existe.\n\n";
	    				}
	    				else
	    				{
	    					$this->insertCliente($avaluo, $vAppra['client']);
	    					//echo "\n\nEl cliente ".$vAppra['client']['name']." No existe.\n\n";
	    				}
	    				
	    				if ($avaluo->exitClient($identificacionClient))
	    				{
	    					echo "\n\nEl cliente ".$vAppra['client']['name']." SI existe.";
	    					
		    				$identificationClient = $identificacionClient;
				    		$oneAppraisal['id_cliente'] = $identificationClient;
				    		$oneAppraisal['id_ciudad'] = "CTG";
				    		$oneAppraisal['id_tipo_inmueble'] = 1;
				    		$oneAppraisal['tipo_avaluo'] = 1;
				    		
				    		$oneAppraisal['id_sector'] = "";
				    		$oneAppraisal['id_barrio'] = "";
				    		$oneAppraisal['direccion'] = "";
				    		
				    		if ($vAppra['propertyAddress'])
				    		{
				    			if ($vAppra['propertyAddress']['district'])
				    			{
				    				$oneAppraisal['id_barrio'] = $vAppra['propertyAddress']['district']['code'];
				    			}
				    			$oneAppraisal['direccion'] = $vAppra['propertyAddress']['address'];
				    		}
				    		
				    		if ($vAppra['entrydate'])
				    		{
				    			$date= $vAppra['entrydate']['date'];
				    			$oneAppraisal['fecha_new'] = date("d/m/Y", strtotime($date));
				    		}
				    		
			    			$oneAppraisal['solicitante'] = $vAppra['client']['name'];
			    			//print_r($oneAppraisal);		    				
		    				
		    				if (!$avaluo->exitsAvaluo($vAppra['appraisalNumber']))
		    				{
		    					$avaluo->insertAvaluo($oneAppraisal);
		    					echo "\n\nEl avaluo ".$vAppra['appraisalNumber']." NO existe.";
		    				}
		    				else
		    				{
		    					echo "\n\nEl avaluo ".$vAppra['appraisalNumber']." ya existe.";
		    				}
		    				
		    				if (!$avaluo->exitsPuc($oneAppraisal))
		    				{
		    					$avaluo->insertPuc($oneAppraisal);
		    					echo "\n\nEl puc ".$vAppra['appraisalNumber']." NO existe.";
		    				}
		    				else
		    				{
		    					echo "\n\nEl puc ".$vAppra['appraisalNumber']." ya existe.";
		    				}
		    				
		    				if (!$avaluo->exitsSaldo($oneAppraisal))
		    				{
		    					echo "\n\nEl saldo ".$vAppra['appraisalNumber']." No existe.\n";
		    					$avaluo->insertSaldo($oneAppraisal);
		    				}
		    				else
		    				{
		    					echo "\n\nEl saldo ".$vAppra['appraisalNumber']." ya existe.\n";
		    				}		    							    				
	    				}
	    				else
	    				{
	    					echo "\n\nEl cliente ".$vAppra['client']['name']." No existe.\n";
	    				}
			    			
	    			}
	    		}    		
    	}    
    }    
    
    /**
     * Eliminar espacios en blanco seguidos
     * @param unknown $string
     * @return unknown
     */
    function  cleanString($string)
    {
    	$string = trim($string);
    	$string = str_replace('&nbsp;', ' ', $string);
    	$string = preg_replace('/\s\s+/', ' ', $string);
    	return $string;
    }    
    
    function login()
    {    
    	if (is_null($this->token))
    	{    
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
        
    		if (isset($result['code']))
    		{
    			if ($result['code'] == 401)
    			{    
    				$this->login();
    			}
    		}
    		else
    		{    
    			if (isset($result['id']))
    			{    
    				$this->token = $result['id'];
    			}
    			else
    			{
    				echo "\nError en el login\n";
    				$this->token = null;
    			}    
    		}
    	}    	     	
    }
    
    function SetupApi($urlapi,$user,$pass)
    {    
    	$headers = array(
    		'Accept: application/json',
    		'Content-Type: application/json',
    	);
    
    	$a = new api($urlapi, $headers);
    
    	$this->login();
    	 
    	if (!is_null($this->token))
    	{    
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
    	}
    	else
    	{
    		echo "\nToken no valido\n";
    	}    
    }
    
    public function insertCliente($avaluo, $cliente)
    {    
    	$tipoIdentificacion = null;
    	 
    	//print_r($cliente);
    	$urlapiMapper = $this->server.'admin/sifinca/mapper/target/idType/'.$cliente['identity']['idType']['id'];
    	 
    	//echo "\n".$urlapiMapper."\n";
    	$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    	 
    	$idTypeMapper = $apiMapper->get();
    	$idTypeMapper = json_decode($idTypeMapper, true);
        	
    	///print_r($idTypeMapper);
    	$idType = $idTypeMapper['data']['0']['idSource'];
    	    	
    	if ($cliente['client_type'] == 3)
    	{
    		$param = array(
    			'id_cliente' => $cliente['identity']['number'],
    			'id_identificacion' => $idType,
    			'nat_juridica' => 'J',
    			'nom_empresa' => $cliente['comercialName']    					
    		);
    
    		$avaluo->insertClienteJuridico($param);    
    	}
    	else if ($cliente['client_type'] == 2)
    	{    
    		$param = array(
    			'id_cliente' => $cliente['identity']['number'],
    			'id_identificacion' => $idType,
    			'nat_juridica' => 'N',
    			'nombre' => $cliente['firstName']." ".$cliente['secondName'],
    			'apellido'=> $cliente['lastName']." ".$cliente['secondLastname']    					
    		);
    
    		$avaluo->insertCliente($param);
    
    	}    
    }        
}