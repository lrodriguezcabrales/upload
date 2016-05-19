<?php

namespace upload\command;

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

class OportunityMonteriaCommand extends Command
{	
 
    private $conn;
    
    public $server = 'http://www.sifinca.net/monteriaServer/web/app.php/';
    public $serverRoot = 'http://www.sifinca.net/';
    
    public $localServer = 'http://10.102.1.22/';
    
    public $user= "sifincauno@araujoysegovia.com";
    public $pass="araujo123";
    
    public $token = null;
    
    /*
     array("id"=>"#oportunidad",
     "fecha_ingreso"=>"dd/mm/aaaa",
     "estado"=>"1", // cierre de negocio
     "id_cliente"="CC solicitante"
     "id_promotor"=>"mapper",
     "tipo_oportunidad"=>"COA", // de arriendos
     "fecha_cierre"=>"dd/mm/aaaa hh:mm:ss",
           "id_sucursal"=> "mapper"
          )
     */
    
    protected function configure()
    {
    	$this->setName('oportunidadesMonteria')
    	->setDescription('Comando para pasar oportunidades de SF2 a SF1');
    }
    
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input,
    		\Symfony\Component\Console\Output\OutputInterface $output)
    {
    
    	$output->writeln("Conectando SF1 \n");
    
        $conn = new data(array(
            'server' =>'192.168.100.1'
            ,'user' =>'sa'
            ,'pass' =>'75080508360'
            ,'database' =>'sifinca' 
            ,'engine'=>'mssql'
        ));
       
    	
    	$conexion = new oportunity($conn);
    	
    	$this->getOportunitySF2($conexion);
    
    	$this->clientesCaptacion($conexion);
    	
    }
    
    
    public  function getOportunitySF2($conexion){
    	
    	$relaciones = array("responsable", "client", "quote", "state", "oportunityType", "office", "lead", "creator", "closeReason", "unit", "property");
    	$relaciones = json_encode($relaciones);
    	
    	//[{"value":"C","operator":" equal","property":"state.value"}]
    	$filter = array();
    	
    	$filter[] = array(
    			'value' => 'C',
    			'operator' => 'equal',
    			'property' => 'state.value'
    	);
    	//{"value":"Arriendos","operator":" start_with","field":"oportunityType.name","property":"oportunityType.name"}
    	$filter[] = array(
    			'value' => 'Arriendos',
    			'operator' => 'equal',
    			'field' => 'oportunityType.name',
    			'property' => 'oportunityType.name'
    	);
    	
    	$filter = json_encode($filter);
    	

    	$filterTwo = array();
    	 
    	$filterTwo[] = array(
    			'value' => 'C',
    			'operator' => 'equal',
    			'property' => 'state.value'
    	);
    	//{"value":"Arriendos","operator":" start_with","field":"oportunityType.name","property":"oportunityType.name"}
    	$filterTwo[] = array(
    			'value' => 'Ventas/Usados',
    			'operator' => 'equal',
    			'field' => 'oportunityType.name',
    			'property' => 'oportunityType.name'
    	);
    	 
    	$filterTwo = json_encode($filterTwo);
    	
    	
    	$urlOpArriendos = $this->server.'crm/main/oportunity?filter='.$filter;
    	$urlOpVentas = $this->server.'crm/main/oportunity?filter='.$filterTwo;
    	
//     	$urlOpArriendos = $this->server.'crm/main/zero/oportunity?relations='.$relaciones.'&filter='.$filter; 	
//     	$urlOpVentas = $this->server.'crm/main/zero/oportunity?relations='.$relaciones.'&filter='.$filterTwo;
    	
    	$apiOp = $this->SetupApi($urlOpArriendos, $this->user, $this->pass);
    	$oportunityArriendos = $apiOp->get();
    	$oportunityArriendos = json_decode($oportunityArriendos, true);
    	
    	echo "\nOportunidades de arriendo: ".$oportunityArriendos['total'];
    	
    	$apiOp = $this->SetupApi($urlOpVentas, $this->user, $this->pass);
    	$oportunityVentas = $apiOp->get();
    	$oportunityVentas = json_decode($oportunityVentas, true);
    	
    	echo "\nOportunidades de ventas: ".$oportunityVentas['total'];
    	
    	$oportunity = array_merge($oportunityArriendos['data'], $oportunityVentas['data']);
    	
    	//$oportunity = $oportunityArriendos['data'];
    	
    	//$oportunity = $oportunityVentas['data'];
    	//print_r($oportunity);
    	
    	echo "\nTotal de oportunidades cerradas: ".count($oportunity)."\n";
    	
    	$startTime= new \DateTime();
    	
    	if(count($oportunity) > 0){
    		
    		for ($i = 0; $i < count($oportunity); $i++) {
    			
    			$op = $oportunity[$i];
    			//echo "\naqui 2\n";
    			
    			$oportunidadSF1 = $this->searchOportunidadSF1($conexion, $op['oportunityNumber']);
    			
    			if(is_null($oportunidadSF1)){
    				
    				if(($op['oportunityType']['value'] == 0) || ($op['oportunityType']['value'] == 1)){
    						
    					//$fechaIngreso = $op['entrydate'];
    					//print_r($fechaIngreso);
    						
    					//$fechaIngreso = date_format($fechaIngreso, 'd-m-Y');
    						
    					$fechaIngreso = new \DateTime($op['entrydate']['date']);
    					$fechaIngreso = $fechaIngreso->format('Y-m-d H:i:s');
    				
    					$emailResponsable = $op['responsable']['email'];
    					$promotor = $this->searchPromotor($conexion, $emailResponsable);
    						
    					
    					$emailCreador = $op['creator']['email'];
    					//echo $emailCreador;
    					$creador = $this->searchPromotor($conexion, $emailCreador);
    					//print_r($promotor);
    				
    					$tipoOportunidad = $this->searchTipoOportunidad($op);
    						
    					//$fechaCierre = date_format($fechaCierre, 'd-m-Y H:i:s');
    					$fechaCierre = new \DateTime($op['closeDate']['date']);
    					$fechaCierre = $fechaCierre->format('Y-m-d H:i:s');
    				
    					$sucursal = $this->searchSucursal($op);
    				
    					$param = array("id"=> $op['oportunityNumber'],
    							"fecha_ingreso"=> $fechaIngreso,
    							"estado"=>"1", // cierre de negocio
    							"id_cliente"=> $op['client']['identity']['number'],
    							"id_promotor"=> $promotor['id_user'],
    							"tipo_oportunidad"=> $tipoOportunidad, // de arriendos
    							"fecha_cierre"=> $fechaCierre,
    							"id_sucursal"=> $sucursal,
    							"id_creador" => $creador['id_user']
    					);
    				
    					//print_r($param);
    					echo "\n".$op['client']['identity']['number']."\n";
    					$clienteSF1 = $this->searchUsuarioSF1($conexion, $op['client']['identity']['number']);
    					
    					//print_r($clienteSF1);
    					if(is_null($clienteSF1)){
    						echo "\n Creando cliente en Sifinca 1\n";
    							
    						$this->insertCliente($conexion, $op['client']);
    							
    					}
    				
    					$this->insertOpotunidad($conexion, $param);
    				
    				}
    				
     			}else{
     				echo "\n La oportunidad ya esta creada ".$op['oportunityNumber']."\n";
    				
    				
     			}
    			    			
    		}
    		
    		 
    	}else{
    		return ;
    	}
    	

    	$finalTime = new \DateTime();    	 
    	$diff = $startTime->diff($finalTime);
    	     	 
    	echo "\n\n Fecha inicial: ".$startTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Fecha final: ".$finalTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
    	
    }
    
    public  function clientesCaptacion($conexion){
    
    	$relaciones = array("responsable", "client", "quote", "state", "oportunityType", "office", "lead", "creator", "closeReason", "unit", "property");
    	$relaciones = json_encode($relaciones);
    
    	//[{"value":"C","operator":" equal","property":"state.value"}]
    	$filter = array();
    
    	$filter[] = array(
    			'value' => 'C',
    			'operator' => 'equal',
    			'property' => 'state.value'
    	);
    	//{"value":"Arriendos","operator":" start_with","field":"oportunityType.name","property":"oportunityType.name"}
    	$filter[] = array(
    			'value' => 5,
    			'operator' => 'equal',
    			'field' => 'oportunityType.value',
    			'property' => 'oportunityType.value'
    	);
    
    	$filter = json_encode($filter);
    
    
    
    
    	$urlOpCaptacion = $this->server.'crm/main/oportunity?filter='.$filter;
    
    	 
    	$apiOp = $this->SetupApi($urlOpCaptacion, $this->user, $this->pass);
    	$oportunityCaptacion = $apiOp->get();
    	$oportunityCaptacion = json_decode($oportunityCaptacion, true);
    
    	echo "\nOportunidades de captacion: ".$oportunityCaptacion['total'];
    
    	 
    
    	$oportunity = $oportunityCaptacion['data'];
    
    	//$oportunity = $oportunityVentas['data'];
    	//print_r($oportunity);
    
    	//echo "\nTotal de oportunidades cerradas: ".count($oportunity)."\n";
    	 
    	$startTime= new \DateTime();
    
    	if(count($oportunity) > 0){
    
    		for ($i = 0; $i < count($oportunity); $i++) {
    			 
    			$op = $oportunity[$i];
    			//echo "\naqui 2\n";
    
    			$oportunidadSF1 = $this->searchOportunidadSF1($conexion, $op['oportunityNumber']);
    
    			if(is_null($oportunidadSF1)){
    
    				if(($op['oportunityType']['value'] == 5)){
    
    					//echo "\n".$op['client']['identity']['number']."\n";
    					$clienteSF1 = $this->searchUsuarioSF1($conexion, $op['client']['identity']['number']);
    
    					//print_r($clienteSF1);
    					if(is_null($clienteSF1)){
    						echo "\n Creando cliente en Sifinca1 ".$op['client']['identity']['number']."\n";
    							
    
    
    						$this->insertCliente($conexion, $op['client']);
    						 
    					}
    					 
    				}
    			}
    			 
    		}
    
    
    	}else{
    		return ;
    	}
    
    
    	$finalTime = new \DateTime();
    	$diff = $startTime->diff($finalTime);
    
    	echo "\n\n Fecha inicial: ".$startTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Fecha final: ".$finalTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
    
    }
    
    
    
    public function insertOpotunidad($conexion, $param){
        
        $conexion->insertOpotunidad($param);
                
    }
    
    public function insertCliente($conexion, $cliente){
    
    	$tipoIdentificacion = null;
    	
    	//print_r($cliente);
    	$urlapiMapper = $this->server.'admin/sifinca/mapper/target/idType/'.$cliente['identity']['idType']['id'];
    	
    	//echo "\n".$urlapiMapper."\n";
    	$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    	
    	$idTypeMapper = $apiMapper->get();
    	$idTypeMapper = json_decode($idTypeMapper, true);
    	 
    	
    	///print_r($idTypeMapper);
    	$idType = $idTypeMapper['data']['0']['idSource'];
    	
    	
    	$naturaleza = 'N';
    	if($cliente['identity']['idType']['id'] == '6c29bc74-a33a-42ed-8d24-1d86e31dce9f'){
    		$naturaleza = 'J';
    		
    		$param = array(
    				'id_cliente' => $cliente['identity']['number'],
    				'id_identificacion' => $idType,
    				'nat_juridica' => $naturaleza,
    				'nom_empresa' => $cliente['comercialName']
    				 
    		);
    		
    	}else{
    		
    		$param = array(
    				'id_cliente' => $cliente['identity']['number'],
    				'id_identificacion' => $idType,
    				'nat_juridica' => $naturaleza,
    				'nombre' => $cliente['firstname']." ".$cliente['secondname'],
    				'apellido'=> $cliente['lastname']." ".$cliente['secondLastname']
    				
    				 
    		);
    		
    	}
    	
    	$conexion->insertCliente($param);
    
    }
    
    public function searchOportunidadSF1($conexion, $idOportunidad){
        
        $op = $conexion->getOportunidad($idOportunidad);
        
        if(isset($op[0])){
        	return $op[0];
        }else{
        	return null;
        }
       
        
        
    }
        
    public function searchPromotor($conexion, $email) {
    	
    	
    	
    	$promotor = $conexion->getPromotor($email);
    	
        	
    	return $promotor[0];
    	
    }
    
    public function searchTipoOportunidad($op) {
    	
    	
    	//Arriendo
    	if($op['oportunityType']['value'] == 0){
    		$tipoOportunidad = 'COA';
    	}
    	//Venta
    	if($op['oportunityType']['value'] == 1){
    		$tipoOportunidad = 'COV';
    	}
    	
    	return $tipoOportunidad;
    	
    }
    
    /**	
     * Buscar sucursal equivalente en sifinca1
     * @param unknown $op
     */
    public function  searchSucursal($op){
    	
    	//echo "\nentro aqui sucursal\n";
    	$urlapiMapper = $this->server.'admin/sifinca/mapper/target/propertyOffice.MON/'.$op['office']['id'];
    	
    	//echo "\n".$urlapiMapper."\n";
    	$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    	
    	$sucursalMapper = $apiMapper->get();
    	$sucursalMapper = json_decode($sucursalMapper, true);
    	    		
    	 
    	$sucursal = $sucursalMapper['data']['0']['idSource'];
    	
    	return $sucursal;
    }
    
    public function searchUsuarioSF1($conexion, $idCliente){
    	
    	$usuario = $conexion->getCliente($idCliente);
    	 
    	if(isset($usuario[0])){
    		return $usuario[0];
    	}else{
    		return null;
    	}
    	     	
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
  		 
    		//echo "\n".$url."\n";
    		
     		$result = $a->post(array("user"=>$this->user,"password"=>$this->pass));
     		
     		//print_r($result);
     		
     		$result = json_decode($result, true);
     		
     		

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
    
//     	$url= $this->server."login";
    	$headers = array(
    			'Accept: application/json',
    			'Content-Type: application/json',
    	);
    
     	$a = new api($urlapi, $headers);
    
//     	//print_r($a);
    
//     	$result= $a->post(array("user"=>$user,"password"=>$pass));
    
//     	//      	echo "\nAqui result\n";
//     	//     	print_r($result);
    
    
//     	$data=json_decode($result);
    
//     	//print_r($data);
    
//     	$token = $data->id;
		
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