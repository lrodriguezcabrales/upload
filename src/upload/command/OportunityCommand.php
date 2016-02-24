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

class OportunityCommand extends Command
{	
 
    private $conn;
    
    public $server = 'http://162.242.247.95/sifinca/web/app_dev.php/';
    public $serverRoot = 'http://162.242.247.95/';
    
    public $localServer = 'http://10.102.1.22/';
    
    public $user= "sifincauno@araujoysegovia.com";
    public $pass="araujo123";
    
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
    	$this->setName('oportunidades')
    	->setDescription('Comando para pasar oportunidades de SF2 a SF1');
    }
    
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input,
    		\Symfony\Component\Console\Output\OutputInterface $output)
    {
    
    	$output->writeln("Conectando SF1 \n");
    
    	$conn = new data(array(
    			'server' =>'10.102.1.3'
    			,'user' =>'hherrera'
    			,'pass' =>'daniela201'
    			,'database' =>'sifinca'
    			,'engine'=>'mssql'
    	));
       
    	
    	$conexion = new oportunity($conn);
    	
    	$this->getOportunitySF2($conexion);
    
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
    	
    	//print_r($oportunity);
    	
    	echo "\nTotal de oportunidades cerradas: ".count($oportunity)."\n";
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
    				echo "\n La oportunidad ya esta creada en Sifinca 1\n";
    			}
    			    			
    		}
    		
    		 
    	}else{
    		return ;
    	}
    	
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
    	$urlapiMapper = $this->server.'admin/sifinca/mapper/target/propertyOffice.CTG/'.$op['office']['id'];
    	
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